
## What is Twig ?

Twig is a template engine for php. 

See https://en.wikipedia.org/wiki/Twig_(template_engine) for basic implementation

See https://twig.symfony.com/doc/2.x/index.html for full details

----------
## What is Timber ?

Timber is a Twig implementation for WordPress

Timber extends twig - providing a method to run custom functions in templates, iterate over Wordpress query objects, inject context into templates, and more

See: https://timber.github.io/docs/

----------


## PHP / Twig comparison

### rendering

#### PHP
```PHP
echo $variable;
```

#### Twig
```twig
{{ variable }}
```

### foreach loop

#### PHP
```php
foreach ($items as $item) {
	print_r($item);
}
```

#### Twig
```twig
{% for item in items %}
	{{ item | debug }}
{% endfor %}
```

### foreach loop with keys

#### PHP
```php
foreach ($items as $key => $item) {
	echo $key;
	print_r($item);
}
```

#### Twig
```twig
{% for key, item in items %}
	{{ key }}
	{{ item | debug }}
{% endfor %}
```

### Setting variables in templates. Use sparingly. Use Timber context where possible (see Timber context below) 

#### PHP
```php
$message = 'Hello World';
echo $message
```

#### Twig
```twig
{% set message = 'Hello World' %}
{{ message }}
```

### Running PHP functions in templates. Use sparingly. Use Timber context where possible (see Timber context below) 

#### PHP
```php
<?php $terms = get_the_terms(get_the_ID(),'my_taxonomy'); ?>
<ul>
	<?php foreach($terms as $term) : ?>
		<li><?php echo $term->term_name ?></li>
	<?php endforeach ?>
</ul>
```

#### Twig
```twig
{% set terms = function('get_the_terms',post.id,'my_taxonomy') %}
{# shorthand version #}
{% set terms = fn('get_the_terms',post.id,'my_taxonomy') %}
<ul>
	{% for term in terms %}
		<li>
			{{term.term_name}}
		</li>
	{% endfor %}
</ul>
```
----------

## Twig template blocks
### Twig blocks

#### Portions of templates can be overriden using twig blocks: 
The [include](https://twig.symfony.com/doc/3.x/tags/include.html) tag allows templates to be included, and the [extends](https://twig.symfony.com/doc/3.x/tags/extends.html) tag allows templates to be extended.
#### button.twig
```twig
<a href="#" class="xd-button {{type}}">{% block button_text %}Read More{% endblock%}</a>
```

#### contact-button.twig
```twig
{% extends 'button.twig' %}
{% block button_text %}Contact{% endblock %}
```
 
The [embed](https://twig.symfony.com/doc/3.x/tags/embed.html) tag combines the behavior of [include](https://twig.symfony.com/doc/3.x/tags/include.html) and [extends](https://twig.symfony.com/doc/3.x/tags/extends.html), but also allows variable overrides 

#### footer-buttons.twig
```twig
{% embed 'button.twig' with {type:'xd-button--secondary'} %}
	{% block button_text %}Secondary Button{% endblock %}
{% endembed %}

{% embed 'button.twig' with {type:'xd-button--primary'} %}
	{% block button_text %}Primary Button{% endblock %}
{% endembed %}
```
----------

## Timber custom functions

`xd_get_picture`,

`get_icon`,

`get_theme_mod`,

`has_custom_logo`,

`get_social_icons`,

`has_background_video`,

`get_background_video_srcset`,

`get_modal_video_src` 

are available in twig templates. This can be customised in `inc/template-functions.php`

----------


## Timber context
Adding items to the context array makes them available within the template.

### Examples:

#### PHP
```php
$context['subtitle'] = get_field('sub_title');
$context['terms'] = get_the_terms(get_the_ID(),'my_taxonomy'); 
```
#### Twig
```twig
{{ subtitle }}
<ul>
	{% for term in terms %}
		<li>
			{{term.term_name}}
		</li>
	{% endfor %}
</ul>
```

`$post`, `get_fields()`, and `$props` from Gutenberg block data, are loaded into the template context **automatically**:

### In page templates 

#### Twig
```twig
{# Global acf phone 1 field #}
{{ options.option_company_contact_phone_1 }} 

{# A post custom ACF field  #}
{{ post.fields.subtitle }} 

```
### In Gutenberg blocks 

#### Twig
```twig
{# className from block props #}
<div> class="{{ props.className }}"></div> 

{# A block custom ACF field  (ACF blocks only) #}
{{ fields.block_heading }} 

{# The block innerblock content (Dynamic blocks only) #}
{{ content }}
```

----------

## Timber post classes
Working with `Timber\Post`, `Timber\PostCollection`, and `Timber\PostQuery`	

To summarize:

A `Timber\Post` is an abstraction of a `WP_Post` object or the current `$post` variable which provides some useful tools for use in Twig templates. 

A `Timber\PostCollection` is an array of `Timber\Post`'s but also offers similar functionality to the WordPress `while( have_posts() ): the_post()` loop. 

A `Timber\PostQuery` is an abstraction of a `WP_query` which returns a `Timber\PostCollection`

The post properties are available by default in a twig **single** template through the post context. It is similar to `$post` but offers some shorthand properties. See the Timber [docs](https://timber.github.io/docs/reference/timber-post/) for more info

#### Twig
```twig
{# single.twig #}

{# instead of $post->post_title #}
{{ post.title }}

{# instead of get_post_thumbnail_id() #}
{{ post.thumbnail.id }}
```

Post ACF fields are also available under `post.fields.field_name`. Note: the official implementation of this is to use `post.meta('field_name')`. See https://timber.github.io/docs/guides/acf-cookbook/. The kicks theme adds the additional functionality adding the ACF fields to `post.fields`


#### Twig
```twig
{% for button in post.fields.buttons %}
	<a href="{{ button.url }}" class="xd-button xd-button--primary">{{ button.title }}</a>
{% endfor %}
```

To iterate over a set of `WP_posts` we use a `Timber\PostCollection`

#### PHP
```php
// Note: there are no parameters passed to PostQuery(). It will inherit from the current global WP_query object
// A PostQuery() will return a PostCollection
$context['posts'] = new PostQuery();
```


#### Twig
```twig
{# archive.twig #}

{% for post in posts %}
	{{ post.title }}
	{{ post.fields.buttons | debug }}
{% endfor %}
```

Under the hood, each time the post variable is accessed in `{% for post in posts %}` WordPress is calling the same `the_post()` function as in a  `while( have_posts() ):` loop. This ensures that `post.title` contains the correct title, and `post.fields` contains the correct ACF fields etc.

To run a custom query, we use a `Timber\PostQuery`

#### PHP
```php
$context['floorplans'] = new PostQuery(
	array(
		'post_type' => 'floorplan',
		'posts_per_page' => -1,
	)
);
// Note: the following is functionally equivalent to above.
$floorplans = get_posts(
	array(
		'post_type' => 'floorplan',
		'posts_per_page' => -1,
	)
);
$context['floorplans']  = new PostCollection($floorplans);
```
We can also insert an array of posts (from a relationship field, for example) into a `Timber\PostCollection`
#### PHP
```php
// get_field('floor_plans') is an ACF relationship field containing floorplans from a custom post type
$context['floorplans'] = get_field('floor_plans');
// Note, since the context already contains all of the ACF fields automatically in $context['fields'], we can just copy it instead of using get_field()
$context['floorplans'] = $context['fields']['floorplans'];
```
#### Twig
```twig
{% for floorplan in floorplans %}
	{{ floorplan.title }}
	{{ floorplan.fields.squarefeet }}
{% endfor %}
```
