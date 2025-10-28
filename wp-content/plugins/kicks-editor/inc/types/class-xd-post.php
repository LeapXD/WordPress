<?php
/**
 * XD_Post type
 *
 * @package Kicks.
 */

namespace XD\Types;

use Timber\Post;
use Timber\Timber;
use WP_Post;

/**
 * XD_Post type definition.
 */
#[\AllowDynamicProperties]
class XD_Post extends Post {

	/**
	 * Global compatibility switch.
	 * Set by the autoloader: true only for Timber v2 with older themes.
	 *
	 * @var bool
	 */
	public static $compat_mode = false;

	/**
	 * Selected Timber major version for this request (1 or 2), set by the autoloader.
	 *
	 * @var int
	 */
	public static $timber_version = 1;
	/**
	 * The current post.
	 *
	 * @var XD_Post[] current post.
	 */
	public static $stack = array();

	/**
	 * Guard so the constructor knows when it’s being called by build().
	 *
	 * @var int
	 */
	protected static $build_depth = 0;

	/**
	 * Gets the current post.
	 */
	public static function get_current() {
		return ! empty( self::$stack ) ? end( self::$stack ) : null;
	}

	/**
	 * Gets the previous post.
	 */
	public static function get_previous() {
		return ! empty( self::$stack ) ? prev( self::$stack ) : null;
	}

	/**
	 * Pop the stack only if the top matches the given post or ID.
	 *
	 * @param int|XD_Post $post_or_id The post or post ID to match.
	 * @return bool True if popped, false otherwise.
	 */
	public static function pop_if( $post_or_id ): bool {
		if ( ! self::$stack ) {
			return false;
		}
		$id  = $post_or_id instanceof self ? (int) $post_or_id->ID : (int) $post_or_id;
		$top = end( self::$stack );
		if ( $top && (int) $top->ID === $id ) {
			array_pop( self::$stack );
			return true;
		}
		return false;
	}

	/**
	 * Pop the top of the stack, regardless of which post it is.
	 *
	 * @return XD_Post|null The popped post or null.
	 */
	public static function pop(): ?self {
		return self::$stack ? array_pop( self::$stack ) : null;
	}

	/**
	 * Back-compat constructor: allow `new XD_Post( $id|WP_Post|null )`.
	 *
	 * @param int|\WP_Post|null $pid The post id or WP_Post instance.
	 * @throws \Exception If direct instantiation is attempted in Timber v2 strict mode.
	 */
	public function __construct( $pid = null ) {
		// 1) Timber v1: pure v1 behavior (parent ctor + push)
		if ( 1 === (int) static::$timber_version ) {
			parent::__construct( $pid );
			if ( isset( $this->ID ) && $this->ID ) {
				self::$stack[] = $this;
			}
			return;
		}

		// 2) If Timber’s factory (build) is constructing us, allow it in ALL modes.
		// parent::build() does `new static()`, and our build() override sets $build_depth>0 beforehand.
		if ( self::$build_depth > 0 ) {
			return; // skip manual hydration / checks; build() will finish setup + push.
		}

		// 3) Timber v2 STRICT (compat OFF): block *direct* `new XD_Post(...)`
		if ( ! static::$compat_mode ) {
			throw new \Exception(
				'Direct instantiation of XD_Post is not allowed in Timber v2 strict mode; use Timber::get_post().'
			);
		}

		// 4) Timber v2 COMPAT: allow legacy `new` (warn in dev) and hydrate manually
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			@trigger_error(
				'Instantiating XD_Post with `new` is deprecated; use Timber::get_post().',
				E_USER_DEPRECATED
			);
		}

		// Manual hydration for legacy new XD_Post(...).
		$wp_post = $pid instanceof \WP_Post ? $pid : ( is_numeric( $pid ) ? get_post( (int) $pid ) : null );
		if ( $wp_post ) {
			$this->id        = $wp_post->ID;
			$this->ID        = $wp_post->ID;
			$this->wp_object = $wp_post;

			$data = \get_object_vars( $wp_post );
			$data = $this->get_info( $data );
			$data = \apply_filters( 'timber/post/import_data', $data, $this );
			$this->import( $data );
		}
	}

	/**
	 * Override Timber’s factory to automatically push
	 * onto the XD_Post stack whenever a post is built.
	 *
	 * @param \WP_Post $wp_post A WordPress post object.
	 * @return static
	 */
	public static function build( \WP_Post $wp_post ): self {
		self::$build_depth++;
		try {
			/**
			 *  Built post.
			 *
			 *  @var static $post the built post. */
			$post = parent::build( $wp_post ); // triggers new static() → ctor sees build_depth>0.
		} finally {
			self::$build_depth--;
		}
		// v2: push here (v1 pushes in ctor).
		if ( 2 === (int) static::$timber_version ) {
			self::$stack[] = $post;
		}
		return $post;
	}




	/**
	 * Resolve mixed input to a WP_Post (instance method helper).
	 *
	 * @param int|WP_Post|null $pid The post id or WP_Post instance.
	 * @return WP_Post|null
	 */
	protected function resolve_wp_post( $pid ): ?\WP_Post {

		if ( $pid instanceof \WP_Post ) {
			return $pid;
		}
		if ( \is_numeric( $pid ) ) {
			return \get_post( (int) $pid ) ? \get_post( (int) $pid ) : null;
		}
		global $post;
		return ( $post instanceof \WP_Post ) ? $post : null;
	}

	/**
	 * Back-compat for `preview` with conditional fallback to Timber's deprecation.
	 *
	 * - Timber v1: call excerpt($len, $readmore).
	 * - Timber v2 + compat ON: map to excerpt([ 'words' => $len, 'end' => $readmore ]).
	 * - Timber v2 + compat OFF: call parent::preview() so Timber shows deprecation.
	 *
	 * @param int    $len      Number of words (v1 signature).
	 * @param string $readmore String to append (v1 signature).
	 * @return mixed
	 */
	public function preview( $len = 50, $readmore = '…' ) {
		// Timber v1: pure parent behavior.
		if ( 1 === (int) static::$timber_version ) {
			return parent::preview( $len, $readmore );
		}

		// Timber v2: mask warning only in compat mode.
		if ( static::$compat_mode ) {
			$options = array(
				'words' => (int) $len,
				'end'   => (string) $readmore,
			);
			return $this->excerpt( $options );
		}

		// Timber v2 strict: let parent throw the deprecation notice.
		return parent::preview( $len, $readmore );
	}

	/**
	 * The purpose of this override is to restore the previous post reference after the content is rendered.
	 *
	 * @param int $page the page number to get.
	 * @param int $len the length of the content to get.
	 */
	public function content( $page = 0, $len = -1 ) {
		$content = '';
		if ( ! empty( $this->post_content ) ) {
			$content = parent::content( $page, $len );
		}
		if ( ! empty( self::$stack ) ) {
			array_pop( self::$stack );
		}
		return $content;
	}


	/**
	 * Checks the provided fields against already existing in self.
	 *
	 * @param array|object $info the fields to check.
	 */
	private function check_fields( $info ) {
		$methods = get_class_methods( $this );
		$methods = array_flip( $methods );
		$vars    = get_class_vars( __CLASS__ );
		$message = "%s already has a %s %s. Use %s::import(\$value, true) to override \n %s";
		foreach ( $info as $field => $value ) {
			if ( array_key_exists( $field, $methods ) ) {
				$ex = new \Exception();
				///phpcs:ignore
				trigger_error( sprintf( $message, __CLASS__, $field, 'method', __CLASS__, $ex->getTraceAsString() ) );
			}
			if ( array_key_exists( $field, $vars ) ) {
				$ex = new \Exception();
				///phpcs:ignore
				trigger_error( sprintf( $message, __CLASS__, $field, 'property', __CLASS__, $ex->getTraceAsString() ) );
			}
		}
	}

	/**
	 * Magic method to get properties.
	 * In Timber V2. __call is used to invoke meta()
	 * for back compat, we need to ensure properties are loaded before meta()
	 *
	 * @param string $name Property name.
	 * @param array  $arguments Arguments.
	 */
	public function __call( $name, $arguments ) {
		return $this->__get( $name );
	}

	/**
	 * Allows fields to be read by their snake_cased or camelCased name.
	 *
	 * @param string $field field name.
	 */
	public function __get( $field ) {
		$snake_cased_field = xd_snake_case( $field );
		if ( property_exists( $this, $field ) ) {
			return $this->$field;
		}
		if ( property_exists( $this, $snake_cased_field ) ) {
			return $this->$snake_cased_field;
		}
		if ( method_exists( $this, 'meta' ) ) {
			$meta_value = $this->meta( $field );
			if ( $meta_value ) {
				return $this->$field = $meta_value;
			}
			$meta_value = $this->meta( $snake_cased_field );
			if ( $meta_value ) {
				return $this->$field = $meta_value;
			}
		}
		if ( method_exists( $this, $field ) ) {
			return $this->$field = $this->$field();
		}
		if ( method_exists( $this, $snake_cased_field ) ) {
			return $this->$field = $this->$snake_cased_field();
		}
		$this->$field             = null;
		$this->$snake_cased_field = null;
		return null;
	}

	/**
	 * Ensure empty()/isset() work with magic properties like featured_image_card.
	 * PHP calls __isset($field) for `$obj->field` inside empty()/isset(), and will
	 * only proceed to evaluate `$obj->field['id']` if this returns true.
	 *
	 * @param string $field
	 * @return bool
	 */
	public function __isset( $field ): bool {
		$snake_cased_field = xd_snake_case( $field );

		// If the property was already materialized on the object, respect it.
		if ( property_exists( $this, $field ) ) {
			return isset( $this->$field );
		}
		if ( $snake_cased_field !== $field && property_exists( $this, $snake_cased_field ) ) {
			return isset( $this->$snake_cased_field );
		}

		// If Timber v1 exposes a real meta() method, try it.
		if ( method_exists( $this, 'meta' ) ) {
			$val = $this->meta( $field );
			if ( null !== $val && '' !== $val && array() !== $val ) {
				return true;
			}
			if ( $snake_cased_field !== $field ) {
				$val = $this->meta( $snake_cased_field );
				if ( null !== $val && '' !== $val && array() !== $val ) {
					return true;
				}
			}
		}

		// If a method would lazily compute it, consider it "isset".
		if ( method_exists( $this, $field ) ) {
			return true;
		}
		if ( $snake_cased_field !== $field && method_exists( $this, $snake_cased_field ) ) {
			return true;
		}

		// Otherwise, not set.
		return false;
	}

	/**
	 * Sets property.
	 *
	 * @param string $property the property name.
	 * @param mixed  $value the property value.
	 */
	public function __set( $property, $value ) {
		$snake_cased_property        = xd_snake_case( $property );
		$this->$snake_cased_property = $value;
	}

	/**
	 * Import field data.
	 *
	 * @param mixed   $info Field data.
	 * @param boolean $force Overwrite existing fields or methods.
	 * @param boolean $only_declared_properties Only import declared properties.
	 */
	public function import( $info, $force = false, $only_declared_properties = false ) {
		if ( ( is_array( $info ) || is_object( $info ) ) ) {
			if ( $info instanceof XD_Type_Base ) {
				$info = $info->get_data();
			}
			if ( is_array( $info ) ) {
				foreach ( $info as $key => $field ) {
					if ( $field instanceof XD_Type_Base ) {
						$info[ $key ] = $field->get_data();
					}
				}
			}
			$callee = ( new \Exception() )->getTrace()[1];
			if ( ! $force &&
				(
			defined( 'WP_DEBUG' ) &&
			WP_DEBUG &&
			isset( $callee['class'] ) && 'Timber\Post' !== $callee['class'] || ! isset( $callee['class'] ) )
			) {
				$this->check_fields( $info );
			}
			parent::import( $info, $force );
		}
	}

}
