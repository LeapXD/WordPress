<?php
/**
 * Block Type Registry
 *
 * @package Leap Editor
 */

namespace Leap\Editor\Block_Setup;

/**
 * Block Type Registry Class
 *
 * @since 2.1.0
 */
class XD_Block_Metadata_Registry {

	/**
	 * Container for storing block metadata collections.
	 *
	 * Each entry maps a base path to its corresponding metadata and callback.
	 *
	 * @var array<string,array<string,mixed>>
	 */
	private static $collections = array();

	/**
	 * Register collection of block metadata.
	 *
	 * @param string $path     The base path for the collection.
	 * @param string $manifest The path to the manifest file for the collection.
	 */
	public static function register( $path, $manifest ) {
		if ( is_array( $manifest ) ) {
			$block_types = $manifest;
		} elseif ( file_exists( $manifest ) ) {
			$block_types = require $manifest;
		} else {
			return;
		}
		self::$collections[ basename( $path ) ] = array(
			'block_types' => array_column( $block_types, null, 'name' ),
			'path'        => $path,
			'manifest'    => $manifest,
		);
	}

	/**
	 * Get the registered block metadata collections.
	 *
	 * @return array<string,array<string,mixed>> The registered block metadata collections.
	 */
	public static function get_collections() {
		return self::$collections;
	}

	/**
	 * Get the registered block metadata for a specific collection.
	 *
	 * @param string $collection The name of the collection to retrieve.
	 * @return array<string,mixed>|null The block metadata for the specified collection, or null if not found.
	 */
	public static function get_collection( $collection ) {
		if ( isset( self::$collections[ $collection ] ) ) {
			return self::$collections[ $collection ];
		}

		return null;
	}

	/**
	 * Get the registered block metadata for a specific collection and block.
	 *
	 * @return array<string,mixed>|null The registered block_types metadata for the specified collection and block, or null if not found.
	 */
	public static function get_all_block_types() {
		$all_block_types = array();
		foreach ( self::$collections as $collection ) {
			$all_block_types = array_merge( $all_block_types, $collection['block_types'] );
		}

		return $all_block_types;
	}

	/**
	 * Get the registered block_types metadata for a specific collection.
	 *
	 * @param string $collection The name of the collection to retrieve.
	 * @return array<string,mixed>|null The block metadata for the specified collection and block, or null if not found.
	 */
	public static function get_blocks_types( $collection ) {
		$collection = self::get_collection( $collection );
		if ( isset( $collection['block_types'] ) ) {
			return $collection['block_types'];
		}

		return null;
	}

	/**
	 * Get the registered block_type metadata for a specific block.
	 *
	 * @param string $block The name of the block to retrieve.
	 * @return array<string,mixed>|null The block metadata for the specified block, or null if not found.
	 */
	public static function get_block_type( $block ) {
		$name_space  = dirname( $block );
		$block_types = self::get_blocks_types( $name_space );
		if ( isset( $block_types[ $block ] ) ) {
			return $block_types[ $block ];
		}

		return null;
	}


	/**
	 * Set the block type metadata for a specific block.
	 *
	 * @param string $block The name of the block to set.
	 * @param array  $type  The block metadata to set.
	 * @return void
	 */
	public static function set_block_type( $block, $type ) {
		$collection = dirname( $block );
		$block      = basename( $block );
		if ( ! isset( self::$collections[ $collection ] ) ) {
			self::$collections[ $collection ] = array();
		}
		self::$collections[ $collection ][ $block ] = $type;
	}
}
