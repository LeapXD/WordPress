# Framework 2.5.0 updates


- [Framework 2.5.0 updates](#framework-250-updates)
- [Glossary](#glossary)
	- [Blocks](#blocks)
	- [Block context](#block-context)
	- [Gutenberg Block context](#gutenberg-block-context)
	- [Components](#components)
	- [Icons](#icons)
	- [Plugins](#plugins)
	- [Modules](#modules)
	- [Twig](#twig)
	- [Timber](#timber)
	- [Timber Context](#timber-context)
	- [Template Context](#template-context)
	- [xd context](#xd-context)
- [Scope](#scope)
- [Block json files](#block-json-files)
	- [Attributes](#attributes)
		- [`boolean` type attributes:](#boolean-type-attributes)
		- [`string` type attributes:](#string-type-attributes)
		- [`object` type attributes:](#object-type-attributes)
	- [Variations](#variations)
	- [Supports](#supports)
	- [Custom](#custom)
		- [Register](#register)
		- [innerBlocks](#innerblocks)
		- [wrap](#wrap)
			- [Examples of wrap usage:](#examples-of-wrap-usage)
				- [With no wrap](#with-no-wrap)
				- [With block wrap string only](#with-block-wrap-string-only)
				- [With block wrap only](#with-block-wrap-only)
				- [With innerBlocks wrap only](#with-innerblocks-wrap-only)
				- [With outer wrap only](#with-outer-wrap-only)
				- [With all wrap levels](#with-all-wrap-levels)
				- [With pre, and post innerBlocks inside](#with-pre-and-post-innerblocks-inside)
				- [With editorInnerBlocks only](#with-editorinnerblocks-only)
			- [context (block context)](#context-block-context)
				- [context compare operators](#context-compare-operators)
		- [isDefault](#isdefault)
		- [inspectorControls](#inspectorcontrols)
		- [context](#context)
		- [panels](#panels)
			- [title](#title)
			- [initialOpen](#initialopen)
			- [controls](#controls)
				- [example](#example)
		- [preInnerBlocksControls](#preinnerblockscontrols)
			- [context](#context-1)
			- [controls](#controls-1)
				- [example](#example-1)
		- [postInnerBlocksControls](#postinnerblockscontrols)
		- [blockControls](#blockcontrols)
		- [modules](#modules-1)
			- [context](#context-2)
			- [allowedModules](#allowedmodules)
- [Components](#components-1)
	- [usage](#usage)
		- [examples](#examples)
		- [properties](#properties)
			- [activeState](#activestate)
			- [advanced](#advanced)
			- [help](#help)
			- [inputType](#inputtype)
			- [label](#label)
			- [location](#location)
			- [method](#method)
			- [name / attribute](#name--attribute)
			- [type](#type)
			- [schema](#schema)
			- [value](#value)
		- [Additional advanced properties](#additional-advanced-properties)
			- [support](#support)
			- [id](#id)
			- [data](#data)
			- [propertyName](#propertyname)
			- [propertyValue](#propertyvalue)
			- [setData](#setdata)
			- [setProperty](#setproperty)
		- [Component specific properties](#component-specific-properties)
			- [XDLinkToolBarButton](#xdlinktoolbarbutton)
				- [properties](#properties-1)
			- [XDToolBarButton](#xdtoolbarbutton)
				- [properties](#properties-2)
			- [XDToolbarDropdownMenu](#xdtoolbardropdownmenu)
				- [properties](#properties-3)
			- [XDButton](#xdbutton)
				- [properties](#properties-4)
			- [XDLinkControl](#xdlinkcontrol)
				- [properties](#properties-5)
			- [XDButtonGroup](#xdbuttongroup)
				- [properties](#properties-6)
			- [XDRadioButtonGroup](#xdradiobuttongroup)
				- [properties](#properties-7)
			- [XDCheckboxGroup](#xdcheckboxgroup)
				- [properties](#properties-8)
			- [XDImage](#xdimage)
				- [properties](#properties-9)
			- [XDImageControl](#xdimagecontrol)
			- [XDFeaturedImageControl](#xdfeaturedimagecontrol)
			- [XDVideo](#xdvideo)
				- [properties](#properties-10)
			- [XDVideoControl](#xdvideocontrol)
			- [XDFileUpload](#xdfileupload)
				- [properties](#properties-11)
			- [XDFileUploadControl](#xdfileuploadcontrol)
			- [XDFieldGroup](#xdfieldgroup)
				- [properties](#properties-12)
			- [XDControlGroup](#xdcontrolgroup)
				- [properties](#properties-13)
- [Helper functions](#helper-functions)
	- [php](#php)
		- [xd\_render\_block](#xd_render_block)
			- [parameters](#parameters)
		- [xd\_create\_parsed\_block](#xd_create_parsed_block)
			- [parameters](#parameters-1)
		- [xd\_get\_block\_variation](#xd_get_block_variation)
			- [parameters](#parameters-2)
		- [xd\_classnames](#xd_classnames)
			- [parameters](#parameters-3)
		- [xd\_attribute](#xd_attribute)
			- [parameters](#parameters-4)
		- [xd\_context](#xd_context)
			- [parameters](#parameters-5)
	- [javascript](#javascript)
		- [useXdBlockContext](#usexdblockcontext)
		- [registerComponent](#registercomponent)
			- [parameters](#parameters-6)
		- [example](#example-2)
		- [example](#example-3)
- [Icons](#icons-1)
- [Plugins](#plugins-1)
	- [name](#name)
	- [title](#title-1)
	- [panels](#panels-1)
	- [fieldGroups](#fieldgroups)
		- [properties](#properties-14)
	- [locations](#locations)
		- [properties](#properties-15)
		- [pseudo example](#pseudo-example)
		- [example](#example-4)
		- [Schema](#schema-1)
- [Modules](#modules-2)
		- [properties](#properties-16)
- [Template Context](#template-context-1)
	- [Type classes](#type-classes)
		- [properties](#properties-17)
			- [class\_name](#class_name)
			- [css](#css)
			- [dataset](#dataset)
			- [aria](#aria)
			- [id](#id-1)
		- [Methods](#methods)
			- [import](#import)
			- [is\_empty](#is_empty)
			- [get\_data](#get_data)




# Glossary

## Blocks
Gutenberg sections comprising of client-side editing code and server-side or client-side code for front-end rendering
## Block context
A custom addition to blocks which allows aspects of a block to be changed in various situations
## Gutenberg Block context
A context api provided by Gutenberg which allows data to be shared between posts, blocks, and child blocks both server side, and client side
## Components
React-based components used for adding additional fields to blocks or plugins
## Icons 
Block or block variation icon
## Plugins
Panels containing components that can be registered to post types which support the Gutenberg editor (not to be confused with WordPress plugins)
## Modules
A custom addition to the gutenberg editor which allows features to be shared between blocks
## Twig
A template engine used for displaying blocks and post type content
## Timber
A WordPress plugin which implements Twig and provides tools to load Wordpress data into templates
## Timber Context
The data model used by timber to build templates
## Template Context
PHP files which are loaded immediately before rendering a Twig template allowing for modifications to be made to the Timber context
## xd context
A custom addition to the client-side block editor with similar features to block context, but less restrictive

# Scope

This document will cover the following features which are available to the Gutenberg editor when the Kicks Editor plugin is installed and enabled:

- The `className` property within the [attributes](#attributes), and [variations](#variations) properties which allow a block's css classes to be modified depending on selected attributes without the need for writing dedicated React code
- The [supports](#supports)	property of block json files which allow core and custom features to be added to blocks
- The [custom](#custom) property of the [supports](#supports) property, which is where the majority of the Kicks editor plugin adds block features
- The [block context](#context) properties within sections of the [custom](#custom) property which allows contextual customizations

- The [modules](#modules) feature which allows attributes, and controlling components to be shared between blocks through json configuration files
- Standard [blocks](#blocks)
- Using custom block [icons](#icons)
- The [plugins](#plugins) feature which allows meta fields, and controlling components to be added to post types through json configuration files
- [Component](#components) registration api and component authoring
- The [template](context) context
- custom xd_ prefixed helper functions

The following items will not be covered by this document:
- The Twig template engine
- Standard features of the Timber plugin
- The Gutenberg block api
- The Gutenberg plugin api 
- Gutenberg data stores
- The WordPress rest api and meta field schema
- The Wordpress React component library
- React Javascript framework
- React context providers and consumers

 Readers of this document should study the resources below before proceeding any further.


https://twig.symfony.com/doc/3.x/

https://developer.wordpress.org/block-editor/reference-guides/block-api/

https://developer.wordpress.org/block-editor/how-to-guides/plugin-sidebar-0/

https://developer.wordpress.org/block-editor/reference-guides/data/

https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/

https://developer.wordpress.org/block-editor/reference-guides/components/

https://reactjs.org/docs/getting-started.html

https://reactjs.org/docs/context.html

# Block json files
## Attributes
Expanding on the core [attributes](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#attributes) property, the plugin adds the `className` property. The property allows custom classes to be added to a block, depending on the attribute value

### `boolean` type attributes:

Setting the `className` to attribute to a string literal will result in that string being applied to the class only whn  that attributes's value is true
```json
"attributes":{
	...
	"myAttribute":{
		"type":"boolean",
		"className" : "my-class"
	}
	...
}
```
### `string` type attributes:

Setting the `className` to `true` will result in the attribute's current value being applied to the block class.
in this example, if "myAttribute" is equal to "foo" then "foo" will be applied to the class
```json
"attributes":{
	...
	"myAttribute":{
		"type":"string"
		"className" : true
	}
	...
}
```
Setting the `className` to an object will result in the object's value being applied to the block's class when the object key matches the attribute's value.
In this example, if the attribute value is "left", the the class "align--left" will be applied
```json
"attributes":{
	...
	"align":{
		"type":"string",
		"className" : {
			"left":"align--left",
			"right":"align--right",
		}
	}
	...
}
```
### `object` type attributes:

 `className` must me an object whose keys match those of the attribute schema.

 When the object values are set to `true` the the corresponding property of the attribute value will be applied to the class. 
 The class is "left top" in the following example when the attributes are default.
```json
"attributes":{
	...
	"position":{
		"type":"object",
		"default" :{
			"horizontal" : "left",
			"vertical" : "top"
		},
		"className" : {
			"horizontal" : true,
			"vertical" : true
		}
	}
	...
}
```

 When the object values are set to string literals, that string will be applied to the class when the corresponding attribute property has a non-empty value. 
 The class is "align--left" when the "left" property has a non-empty value. Likewise for "align--top"
```json
"attributes":{
	...
	"position":{
		"type":"object",
		"default" :{
			"left" : "",
			"top" : ""
		},
		"className" : {
			"left" : "align--left",
			"top" : "align--top"
		}
	}
	...
}
```
In addition to the className property, the above rules also apply to:

- className
- postInnerBlocksClassName
- preInnerBlocksClassName
- innerBlocksClassName
- innerClassName
- outerClassName

Which are additional wrappers that can be applied to inner blocks wrapping elements, which will be covered in the [inner blocks](#innerblocks) section

## Variations

In addition to the attributes property, the wrapping element classes can also be customized in the same manner as above for specific block [variations](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-variations/) by adding the `classNames` property to the variation. Eg:

```json
  "variations": [
    {
      "name": "xd/four-column",
      "title": "Four Column",
      "icon": "gm/view-week",
      "attributes": { "columnsType": "four-column" },
      "isActive": ["columnsType"],
      "classNames:": {
        "columnsType": {
          "className": {
            "four-column": true,
          }
        }
      }
    }
  ]
```

## Supports

In addition to the core [supports](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/), the plugin allows for custom [modules](#modules) to be added to the block. The Kicks theme provides support for the following modules by default:

- xd/image-background
- xd/image-desktop
- xd/image-mobile
- xd/background-overlay
- xd/background-overlay-controls (allows overlay to be disabled when xd/background-overlay is enabled)
- xd/background-opacity-controls  (not yet implemented on front end)
- xd/background-blend-mode-controls - (not yet implemented on front end)
- xd/id (adds an automatically generated id to the block, a manual id can be added by enabling the [anchor](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/#anchor) support)
- xd/color-theme
- xd/link (adds a control to the block toolbar for selecting a link)
- xd/margin
- xd/margin-controls
- xd/padding
- xd/padding-controls
- xd/title
- xd/video
- xd/video-modal
- xd/container-width (used by container block, but modularized for re-use if needed)

## Custom

The custom property is a sub-property of the supports property. This section will cover the properties of the custom property.

### Register
When set to true, this will invoke the [registerBlockType](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/#registerblocktype) function, passing in the block json file into the block [metadata](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/). The Kicks Editor plugin, contains a generic [edit](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit) function which renders the block in the editor using the [useBlockProps](https://github.com/WordPress/gutenberg/blob/trunk/packages/block-editor/src/components/block-list/use-block-props/index.js) hook and the provided [innerblocks]() using the [useInnerBlocksProps
](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit) hook. 

The built in edit function is also exposed to `window.xd_settings.editor_settings` and can be used or extended when the register setting is unset or `false` allowing for support of the custom property settings when authoring a custom edit function.

```javascript
const { xd_settings: settings } = window;
const { editor_settings: editorSettings } = settings;
const { edit: Edit } = editorSettings;

function ExtendedEdit = (props) {
  const { clientId, attributes, innerBlocksSettings } = props;
	const additionalSettings = {templateLock:"all"}
	return <Edit {...innerBlocksSettings,...additionalSettings}/>
}

wp.blocks.registerBlockType({
  /* ... */
  edit: compose([
    withFilters('xd.innerBlocksClassName'),
    withFilters('xd.innerBlocksSettings')])(ExtendedEdit),
  /* ... */
});

```
See the xd/post-cards block for example usage

### innerBlocks
innerBlocks property should contain an array of [inner blocks configurations](https://github.com/WordPress/gutenberg/blob/4ef110ca0f1177a720b0d233454bde837ae455a4/packages/block-editor/src/components/inner-blocks/README.md). If the array contains more than one configuration, the configuration with the best matching [block context](#context-block-context) to the edit function. 

### wrap

In addition to the core inner blocks settings, the Kicks Editor adds the `wrap` property. The wrap property allows for different levels of wrapping elements, each with custom classes to be applied.
each wrap level, can either be a string of classes to add, or an object with classes to add, and classes to remove

Here is an example of what a complete wrapper might look like:

```json
{
...
"supports": {
	"custom":{
		"innerBlocks": {
			"wrap": {
				"block": {
				"add": "block-class",
				"remove": "xd-container"
				},
				"outer": "outer-class",
				"inner": "inner-class",
				"preInnerBlocks": "pre-inner-blocks-class",
				"innerBlocks": {
					"add": "inner-blocks-class",
					"remove": "xd-container__inner"
				},
				"postInnerBlocks": "post-inner-blocks-class",
				"editorBlock": "editor-block-class",
				"editorOuter": "editor-outer-class",
				"editorInner": "editor-inner-class",
				"editorPreInnerBlocks": "editor-preInner-blocks-class",
				"editorInnerBlocks": "editor-inner-blocks-class",
				"editorPostInnerBlocks": "editor-post-inner-blocks-class",
				"serverSideRenderBlock": "server-side-render-block-class",
				"preInnerBlocksInside": true,
				"postInnerBlocksInside": true
			}
		}
	}
}
...
}
```

#### Examples of wrap usage:

##### With no wrap

```json
{
...
	"supports": {
		"custom":{
			"innerBlocks": [{
				...
			}]
		}
	}
...
}
```

Output on front and back end:

```html
<div class="xd-block-name">
	<!-- pre inner blocks -->
	<!-- inner blocks -->
	<!-- post inner blocks -->
</div>
```

##### With block wrap string only

```json
{
...
	"supports": {
		"custom":{
			"innerBlocks": [{
				"wrap": {
					"block": "block-class"
				}
			}]
		}
	}
	...
}
```


Output on front and back end:

```html
<div class="xd-block-name block-class">
	<!-- pre inner blocks -->
	<!-- inner blocks -->
	<!-- post inner blocks -->
</div>
```
##### With block wrap only
```json
{
...
	"supports": {
		"custom":{
			"innerBlocks": [{
				"wrap": {
					"block": {
						"add": "block-class",
						"remove": "xd-block-name"
					}
				}
			}]
		}
	}
...
}
```

Output on front and back end:

Note that with no wrap, or block wrap settings only, `preInnerBlocksInside` and `postInnerBlocksInside` have no effect
```html
<div class="block-class">
	<!-- pre inner blocks -->
	<!-- inner blocks -->
	<!-- post inner blocks -->
</div>
```

##### With innerBlocks wrap only
```json
{
...
	"supports": {
		"custom":{
			"innerBlocks": [{
				"wrap": {
					"innerBlocks": "block-class__inner"
				}
			}]
		}
	}
...
}
```
Output on front and back end:

```html
<div class="block-class">
	<!-- pre inner blocks -->
		<div class="block-class__inner">
			<!-- inner blocks -->
		</div>
	<!-- post inner blocks -->
</div>
```

##### With outer wrap only
```json
{
...
	"supports": {
		"custom":{
			"innerBlocks": [{
				"wrap": {
					"outer": "block-class__outer"
				}
			}]
		}
	}
...
}
```
Output on front and back end:

Note that when an `outer`, or `inner` wrap is applied, the `block` and `innerBlocks` will be separated whether or not an `innerBlocks` class is applied.


When outer level classes are applied, and no `innerBlocks` classes are applied, levels are pushed down to the `innerBlocks`. Classes should be applied to `innerBlocks` first, working outward to the `inner` and `outer` wrappers as needed

```html
<div class="block-class">
	<!-- pre inner blocks -->
		<div class="block-class__outer">
			<!-- inner blocks -->
		</div>
	<!-- post inner blocks -->
</div>
```

##### With all wrap levels
```json
{
...
	"supports": {
		"custom":{
			"innerBlocks": [{
				"wrap": {
					"outer": "container",
					"inner": "row",
					"innerBlocks": "col-10 offset-1"
				}
			}]
		}
	}
...
}
```
Output on front and back end:


```html
<div class="container">
	<div class="row">
		<!-- pre inner blocks -->
			<div class="col-10 offset-1">
				<!-- inner blocks -->
			</div>
		<!-- post inner blocks -->
	</div>
</div>
```


##### With pre, and post innerBlocks inside

```json
{
...
	"supports": {
		"custom":{
			"innerBlocks": [{
				"wrap": {
					"preInnerBlocksInside": true,
					"postInnerBlocksInside": true,
					"outer": "container",
					"inner": "row",
					"innerBlocks": "col-10 offset-1"
				}
			}]
		}
	}
...
}
```
Output on front and back end:

```html
<div class="container">
	<div class="row">
		<div class="col-10 offset-1">
				<!-- pre inner blocks -->
				<!-- inner blocks -->
				<!-- post inner blocks -->
			</div>
	</div>
</div>
```
##### With editorInnerBlocks only

```json
{
...
"supports": {
	"custom":{
			"innerBlocks": [{
				"wrap": {
					"editorInnerBlocks": "block-name__inner"
				}
			}]
		}
	}
...
}
```
Output on back end:

```html
<div class="block-name">
	<!-- pre inner blocks -->
		<div class="block-name__inner">
			<!-- inner blocks -->
		</div>
	<!-- post inner blocks -->
</div>
```
Output on front end:

```html
<div class="block-name">
	<!-- pre inner blocks -->
	<!-- inner blocks -->
	<!-- post inner blocks -->
</div>
```

#### context (block context)

The context property is used when multiple inner blocks configurations are provided in the array. 
when a context object is provided, the properties will be compared to the current context, and the best match will be selected. The configurations are processed from right to left (or bottom to top), in a tie situation, the configuration nearer the top will be selected. At least one configuration will always be matched. In cases where none of the configurations should apply by default, a configuration with a context property only should be provided. 

##### context compare operators
The following operators can be provided to the context property to match an inner blocks configuration in a given situation:

 - postType (the the type of post currently being edited)
 - parent (the name of the parent of the block currently being edited)
 - variation (the variation name of the block currently being edited)
 - parentVariation (the variation name of the parent of the block currently being edited)
 - attributes (an object with property names that match the block attribute names, and values that contain qualifying values for the give context)

note that operators should be provided as an array. eg.

```json
...
 "context" : {
	...
	"postType" : [ "post", "page" ]
	...
	}
...
```

note that when attributes are provided, and a matching key is found with a mismatching value, the configuration associated with that context is explicitly rejected.

In the following example, the order of the inserted image and heading blocks are reversed only when the post being edited is a page or project, the parent block is a two-column-with-title block, the alignment attribute is set to "right", the parent variation is two-column-with-title-wide, and the variation of the block is a column-large-heading.

```json
...
innerBlocks :[
			{
				"context": {
					"postType": ["page","project"],
					"parent": ["xd/two-column-with-title"],
					"attributes": {
						"alignment":"right"
					},
					"parentVariation": ["xd/two-column-with-title-wide"],
					"variation": ["xd/column-large-heading"],
					},
					"allowedBlocks": ["core/heading", "xd/image"],
					"template": [["xd/image"],["core/heading"]],
				},
				{
				"context": {"postType": ["post"]},
				"templateLock": "all",
				"allowedBlocks": ["core/heading", "xd/image"],
				"template": [["core/heading"],["xd/image"]],
				},
				{
				"allowedBlocks": ["core/heading", "xd/image"],
				"template": [["core/heading"],["xd/image"]],
				}
]
...
```

### isDefault

This property is used when multiple inner blocks configurations are provided. In cases where some fields in the inner blocks configurations are the same, the `isDefault` field can be set to true, which will result in the default configuration being used regardless of the matching context. The fields provided in the matching context will then override those provided in the default context.

In the following example, all configurations have their `allowedBlocks` set to `["xd/column"]` and their `templateLock` set to `insert`

```json
...
innerBlocks: [
	{
		"isDefault" : true,
		"allowedBlocks": ["xd/column"],
		"templateLock" : "insert"
	},
	{
		"context" : {"parentVariation" : ["xd/two-column"]},
		"template": [["xd/column"],["xd/column"]],
	}
	{
		"context" : {"parentVariation" : ["xd/three-column"]},
		"template": [["xd/column"],["xd/column"],["xd/column"]],
	}
	{
		"context" : {"parentVariation" : ["xd/four-column"]},
		"template": [["xd/column"],["xd/column"],["xd/column"],["xd/column"]],
	}
]
...
```

### inspectorControls

The [inspector controls side bar](https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/block-controls-toolbar-and-sidebar/) allows for controls to be added to a block which then display when the block is active.

The `inspectorControls` property allows for [WordPress Components]() or [Components](#components-1) to be registered to a block.

### context 
See [block context](#context-block-context)
### panels
An array of panels to which the controls belong. Each panel supports:
#### title
The title of the panel
#### initialOpen
Whether or not the panel should be expanded by default
#### controls
an array of [WordPress Components]() or [Components](#components-1)
##### example 

```json
...
{
	"inspectorControls": [
		{
			"context": {"postType": ["page"]},
			"panels": [
				{
					"title": "My Panel",
					"initialOpen": true,
					"controls": [{
						"type":"TextControl",
						"attribute":"myAttribute"
					}]
				},
				{
					"title": "My Other Panel",
					"initialOpen": true,
					"controls": [{
						"type":"TextControl",
						"attribute":"myOtherAttribute"
					}]
				}
			]
		}
	]
}
...
```

### preInnerBlocksControls
`preInnerBlocksControls` function similarly to [inspectorControls](#inspectorcontrols), but rather than rendering in the inspector controls panel, the controls appear before the block body. `preInnerBlocksControls` do not have a panels array, instead, the controls are registered directly

The following properties are supported: 
#### context 
See [block context](#context-block-context)
#### controls
See [controls](#controls)
##### example 

```json
...
{
	"preInnerBlocksControls": [
		{
			"context": {"postType": ["page"]},
			"controls": [
				{
					"type":"TextControl",
					"attribute":"myAttribute"
				},
				{
					"type":"TextControl",
					"attribute":"myOtherAttribute"
				}
			]
		}
	]
}
...
```

### postInnerBlocksControls
`postInnerBlocksControls` performs the same function as [preInnerBlocksControls](#preinnerblockscontrols) except that the controls are rendered after the block body.
### blockControls
`blockControls` perform a similar function  [preInnerBlocksControls](#preinnerblockscontrols) except that the controls are rendered in the block toolbar. Fewer [Componets](#components-1) are suitable for use in the toolbar currently.


### modules
`modules` is used to restrict permitted [modules](#modules-2) based on context

The following properties are supported: 
#### context 
See [block context](#context-block-context)
#### allowedModules
`allowedModules` is an array of permitted modules

# Components
The Leap editor plugin provides support for the following components

  - XDToolBarButton
  - XDButton
  - XDLinkToolBarButton
  - XDLinkControl
  - XDButtonGroup
  - XDRadioButtonGroup
  - XDCheckboxGroup
  - XDImage
  - XDImageControl
  - XDVideo
  - XDVideoControl
  - XDFeaturedImageControl
  - XDFileUpload
  - XDFileUploadControl
  - XDFieldGroup
  - XDControlGroup
  - XDToolbarDropdownMenu

## usage

Components can be added to [preInnerBlocksControls](#preinnerblockscontrols); [inspectorControls](#inspectorcontrols); and [postInnerBlocksControls](#postinnerblockscontrols) in the [custom](#custom) section of block json files or in [module](#modules-2) json files, or [plugin](#plugins-1) json files.  

In addition to the above custom components, [core components](https://developer.wordpress.org/block-editor/reference-guides/components/) can also be added

### examples

`plugin-name.json`
```json
...
{
	"fields": [
		"label": "My text input",
		"name": "field_name",
		"type": "TextControl"
		"placeholder": "Enter some text",
	]
}
...
```

`module-name.json / block.json`
```json
...
{
	"controls": [
		"label": "My text input",
		"attribute": "fieldName",
		"type": "TextControl"
		"placeholder": "Enter some text",
	]
}
...
```
### properties 

Note that when registering a control via json files or [registerComponent](#registercomponent), the following properties will be *captured* and used to configure the control:

  - activeState
  - advanced
  - exposeProps
  - help
  - inputType
  - label
  - location (plugins only)
  - method
  - name/attribute
  - schema (plugins only)
  - type
  - value

#### activeState
The attribute of the underlying control that changes state. eg. `checked` for checkboxes, `value` for [TextControl](https://developer.wordpress.org/block-editor/reference-guides/components/text-control/), or `currentDate` for [DateTimePicker](https://developer.wordpress.org/block-editor/reference-guides/components/date-time/). Defaults to `value`

#### advanced
Applies to inspectorControls only. Control will show in Advanced section

#### help
displays a caption under the component to help the user
#### inputType
Passed to the input property of the underlying component. Eg `number` could be passed to a `TextControl` resulting in `<input type="number" />`

#### label
The Component label

#### location
See [Locations](#locations)

#### method
The name of the event handler method that the control uses to update the control state. Eg. `onChange` for [TextControl](https://developer.wordpress.org/block-editor/reference-guides/components/text-control/), or `onClick` for XDButton. Defaults to `onChange`

#### name / attribute
The name of the field the control value will be saved in
`name` when used in plugin.json. Plugin meta fields are typically snake_cased
`attribute` when used in blocks or modules. Block attribute fields are typically camelCased

#### type
The control type. Eg. XDRadioButtonGroup

#### schema 
See [schema](#schema-1)

#### value
Used rarely as value is typically pulled from the saved state, but can be used to set button text.

### Additional advanced properties 


When the attribute property is omitted, or when the exposeProps property is provided, all of the properties will be passed through to the component. This can be useful when registering an advanced component with [registerComponent](#registercomponent). This is rarely necessary

- support
- id
- data			
- propertyName
- propertyValue
- setData
-	setProperty

#### support
The name of the module/support feature that invoked the component

#### id
The id of the component
#### data
Raw data of all block attributes / post meta fields
#### propertyName
The name of the attribute / meta field
#### propertyValue
The current attribute / meta field value
#### setData
Method to set the raw attribute/meta data
alias of setAttributes / saveMeta function used to update all block attributes / post meta data
####	setProperty
update just the specific attribute  / meta field associated with the component

### Component specific properties

In addition to the above properties, each component has its own properties. Unless otherwise mentioned, the default properties are sufficient, and do not need to be provided with any special implementation or specific requirements

#### XDLinkToolBarButton
##### properties

 - group: the toolbar group to add the control to
 - attribute: omit. This component uses predetermined attributes (url, rel, target). all of type `string`. The user must ensure they exist on the block

#### XDToolBarButton

For use in block controls. Useful for toggling an attribute value, or value in array of attribute value.
##### properties
 - value: The provided value will be toggled, or its enumeration in an array will be toggled when clicked
 - group: the toolbar group to add the control to
 - method: should be set to `onClick`
 - icon: See [icons](#icons-1)
 - text: the button text
   - type: `string`
 - attribute/name:
   - attribute or schema type should be `scalar|array`

#### XDToolbarDropdownMenu
for use in blockControls, creates a list of submenu options in the toolbar
##### properties
 - group: the toolbar group to display the button in. if multiple toolbar buttons are provided with the same group, they will appear in the same section as each other
 - icon: See [icons](#icons-1)
   - (optional) if not provided, the icon form the active option will be displayed
 - options: an array of objects each containing:
   - label: the button label
   - value: the button value
   - icon: (optional) see [icons](#icons-1)
#### XDButton
For use in inspector / pre/postInnerblocksControls. Useful for toggling an attribute value, or value in array of attribute value
##### properties
See [XDToolBarButton properties](#properties-1)

#### XDLinkControl
Provides a UI for picking a link to a page, post, or custom type
##### properties
 - attribute/name:
   - attribute or schema type should be `scalar|array`
#### XDButtonGroup
Provides a group of buttons, of which one or multiple can be selected
##### properties
- attribute/name:
   - attribute or schema type should be `array` for multiple, or `scalar` for singular value
 - gridColumns: sets the width of the buttons by adjusting the grid size
 - options: an array of objects each containing:
   - label: the button label
   - value: the button value
   - icon: (optional) see [icons](#icons-1)
#### XDRadioButtonGroup
Similar to `XDButtonGroup` but only a single value may be selected. Has a stylized UI
##### properties
- attribute/name:
   - attribute or schema type should be `scaler`
 - options: an array of objects each containing:
   - label: the radio button label
   - value: the radio button value
#### XDCheckboxGroup
Similar to `XDButtonGroup` but should be used when there are a larger number of items to choose from
##### properties
- attribute/name:
   - attribute or schema type should be `array` for multiple, or `scalar` for singular value
 - gridColumns: sets the width of the buttons by adjusting the grid size
 - options: an array of objects each containing:
   - label: the button label
   - value: the button value
#### XDImage
Typically used in pre/postinnerBlocksControls - this is the foundational image component, and does not render any buttons below the image, or label, or caption
##### properties
- gallery: if true, image will accept an array of images
  - type:`boolean`
- attribute/name:
   - attribute or schema type should be `array` when `gallery` property is `true`, or `object` for single image
- Fill: if provided, image component will render its buttons here
  - type: JSX SlotFill component (javascript only)
- alwaysDisplayButton: Will always show the upload / select button, rather than when block is selected
  - type: boolean
-	accept: 
	- type:string Allowed mime type
	- default:`image/*`
- allowedTypes:
  - type: `array`
  - default:`['image']`,
#### XDImageControl
Extension of `XDImage` but, renders additional buttons below. Typically used in inspectorControls 
#### XDFeaturedImageControl
Extension of `XDImageControl` but, writes its data to the current post's featured image meta field
#### XDVideo
Typically used in pre/postinnerBlocksControls - this is the foundational video component, and does not render a block label / help caption
##### properties
- attribute/name:
   - attribute or schema type should be `object`
- allowedTypes:
  - type: `array`
  - default:`['video/mp4', 'video/webm']`,
#### XDVideoControl
Extension of `XDVideo` but, renders a label and help caption if provided. Typically used in inspectorControls 
#### XDFileUpload
Typically used in pre/postinnerBlocksControls - this is the foundational file upload component, and does not render a block label / help caption
##### properties
- attribute/name:
   - attribute or schema type should be `object`
- allowedTypes:
  - type: `array`
  - default:`'application/pdf'`,
#### XDFileUploadControl
Extension of `XDFileUploa` but, renders a label and help caption if provided. Typically used in inspectorControls 
#### XDFieldGroup
This is a special field, used in plugins, that allows a group pf fields to save their data to a single meta field
##### properties
- name:
   - schema type should be `object`
- fields: an array of objects each containing:
  - name: `string` the name of the subfield
  - type: `string` the name of the subfield type (can be any [Component](#components-1))
  - ...props: where props are the properties required by the field type
#### XDControlGroup
This is a special field, used in inspectorControls, that allows a group pf fields to save their data to a single attribute
##### properties
- attribute:
   - attribute or type should be `object`
- fields: an array of objects each containing:
  - name: `string` the name of the subfield
  - type: `string` the name of the subfield type (can be any [Component](#components-1))
  - ...props: where props are the properties required by the field type

  

# Helper functions
## php
### xd_render_block
#### parameters

 - $block
   - type: `WP_Block|array|string`
 - $attributes
   - type: `array`
 - $context 
   - type: `array`

When `$block` is a `WP_Block` the object will be rendered and returned as a string
When `$block` is a string, it must be the name of a registered block. A block with that name will be rendered and returned as a string
When `$block` is an array, the keys can be that matching a the output of [parse_blocks](https://developer.wordpress.org/reference/functions/parse_blocks/), or can match the following custom structure:

```php
array(
	'name' => 'core/group',
	'attributes' => array(), // array of attributes
	'context' => array() // array of xd context fields to be sent to thr block render function 
	'inner_blocks' => array(
		'<div class="wrapper">'
		array(
			'name' => 'xd/container',
			'attributes' => array(), // array of attributes
			'context' => array() // array of xd context fields to be sent to thr block render function 
			'inner_blocks' => array(
				// etc
			)
		),
		'</div>'
	)
);
```
Any attributes passed into the attributes parameter, will override / merge with the original blocks attributes
Any context fields passed into the context parameter, will override / merge with the original blocks context fields

### xd_create_parsed_block
Similar to [xd_render_block](#xd_render_block), but only accepts the first block parameter, and returns an array structure matching that returned by [parse_blocks](https://developer.wordpress.org/reference/functions/parse_blocks/). This out put can be used by [xd_render_block](#xd_render_block), or [render_block](https://developer.wordpress.org/reference/functions/render_block/)
#### parameters

 - $block
   - type: `WP_Block|array|string`

### xd_get_block_variation
Attempts to get the [variation](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-variations/) object of the provided block, or the block currently being rendered if none is provided
#### parameters

 - $block
   - type: `WP_Block|array|void`

### xd_classnames
This function is a Php port of [classnames](https://github.com/JedWatson/classnames)
#### parameters

 - $...classnames

### xd_attribute
gets an attribute from a block, used inside a template context file. It will attempt to get the attribute from the current block being rendered, but this can fail when using `xd_render_block`, to guarantee the correct block is selected, pass the block into $block
#### parameters

 - $attribute: the attribute name
   - type: `string`
 - $block the block to which the attribute belongs
   - type: `WP_Block`
  
### xd_context
gets a block context field from a block, used inside a template context file. It will attempt to get the field from the current block being rendered, but this can fail when using `xd_render_block`, to guarantee the correct block is selected, pass the block into $block
#### parameters

 - $field: the context field name
   - type: `string`
 - $block the block to which the field belongs
   - type: `WP_Block`

## javascript

### useXdBlockContext
Custom React hook used for retrieving the current block's context, when used outside of the blocks [Edit](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit) function
### registerComponent
Used for installing additional custom [components](#components-1)

#### parameters

 - componentName:
   - type: `string`
 - component:
   - type: `JSXElement`
### example

```javascript

import {TextControl} from "@wordpress/components";
const registerComponent = window.xd_settings.registerComponent;
const useXdBlockContext = window.xd_settings.context.useXdBlockContext;

function XDBackgroundColourControl(props) {
  const { value } = props;
  const { contextRef } = useXdBlockContext();
  const { current } = contextRef;
  const { ref } = current;
  ref.current.style.background = value;

  return (<TextControl {...props} />);
}

registerComponent('XDBackgroundColourControl', XDBackgroundColourControl);

```

Note that the component is receiving its value, onChange method and, label properties in the `props` parameter and forwarding them to the `TextControl` as described in [Component properties](#properties). The on change method will save its data in the provided attribute / meta field

### example

```json
"preInnerBlocksControls": [
	{
		"controls": [
			{
				"label":"Background Colour",
				"attribute": "backgroundColour",
				"type": "XDBackgroundColourControl",
				"help": "Enter a hex code or colour to change the background colour of this component"
			}
		]
	}
]
```

# Icons
Where a Component, block, variation or plugin accepts an icon property, the provided property may be a string like:

 - 'admin-post'
   - With No prefix, an icon with a matching name from the [WordPress DashIcons](https://developer.wordpress.org/resource/dashicons/) will be used
 - 'xd/checkmark'
   - With a 'xd/' prefix, an icon from the theme's svg sprite will be used
 - 'gm/photo-album'
   - With a 'gm/' prefix, an icon from [Google Material Symbols](https://fonts.google.com/icons) will be used
 - 'wp/button'
   - With a 'wp/' prefix, an icon from [WordPress' Gutenberg icon library](https://wordpress.github.io/gutenberg/?path=/story/icons-icon--library) will be used




# Plugins
[Plugins](https://developer.wordpress.org/block-editor/reference-guides/slotfills/) are Gutenberg's answer to WordPress' classic meta boxes, which are used by Advanced Custom Fields. Since they're rendered by React, this means that data is accessed through the rest api, and can be updated / refreshed without a full page reload.

The Leap Editor plugin provides a way via Json files to register plugins, and fields

A json file can contain the following fields

## name
The name of the plugin, which should be a hyphenated field
## title
The name of the plugin sidebar title
## panels
An array of strings which determine in which admin area the plugin should render. See [Plugins](https://developer.wordpress.org/block-editor/reference-guides/slotfills/)
## fieldGroups
An array of objects which describe each group of fields.
### properties
 - title: The group title
   - type: `string`
 - fields: the field properties
   - type: `object`
     - properties: See [Components](#components-1)
       - schema:
         - type: `object` see [Shema](#schema-1) 
## locations
An array describing the permitted locations for the plugin
the array contains sub-arrays where each array describes an `||` condition and each sub-array describes and `&&` condition

### properties
Each each condition consists of an object, which will have the following properties

 - param: the property to match (`post_state` = 'page_for_posts' or 'page_for_custom_post_type_archive')
   - type: `enum` ['post_type', 'post_state', 'page_template', 'slug']
 - operator whether the property should or should not for the location to match
   - type: `enum` ['==','!=']
 - value: the value to compare
   - type: `string`

### pseudo example
In this example,(`a` AND `b` must be true), OR (`c` AND `d` must be true) for the location to match
```json
"locations": [
	["a","b"],
	["c","d"]
]
```
###  example
In this example, the field will appear on any page that is not the contact page, or on any team member
```json
"locations": [
	[
		{ "param": "post_type", "operator": "==", "value": "page" },
		{ "param": "slug", "operator": "!=", "value": "contact" }
	],
	[
		{ "param": "post_type", "operator": "==", "value": "team_member" }
	],
]
```

### Schema
The schema property is used in plugin fields, and is used for two purposes:
 - To describe the field data shape,
 - To register the meta field in the WordPress Rest API

 For details, see [Rest API Schema](https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/)

 Note that, when the rest fields are registered, the `additionalProperties` field is to `true` for objects when no `properties` field is provided. 
 
 When `type` is set to array, `items.type` is set to object, and no `items.properties` field is provided, `items.additionalProperties` is set to true. 

 This means that fields can be registered without being as particular with the specification as would otherwise be required. 

 The default behavior is restored, if `properties`, or `items.properties`, for objects, or arrays containing objects  respectively.

To prevent unexpected behavior, it is recommended to provide a default value. 

An important consideration with the Rest API, is that a `null` value is seen as a value to **delete**. 

If fields are failing to save, this is likely due to a mis-configured schema, or an inappropriate default value for the type. This can lead to a null value being received from the server, which is subsequently returned to the server resulting in the field deletion. 
  
# Modules

Modules provide in interface that allow custom block [supports](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/) to be registered.

Each json file in the modules folder maps to an inspectorControls panel on blocks that support the module.

### properties
 - title: the panel title
   - type: `string`
 - initialOpen: if the panel should be open when the block is active
   - type: `boolean`
 - supports: the module features
   - type: `array`
     - items: the supported features
       - type: `object`
       - properties:
         - name: the supported feature name
           - type: `string`
         - attributes: see [Attributes](#attributes)
         - inspectorControls: see [inspectorControls](#controls)
         - blockControls: see [blockControls](#blockcontrols)
         - preInnerblocksControls: see [preInnerblocksControls](#preinnerblockscontrols)
         - postInnerblocksControls: see [postInnerblocksControls](#postinnerblockscontrols)

# Template Context

The template context, is a php file which is loaded immediately before the Timber plugin renders a block, or a template.
Each block will attempt to load its corresponding template file from the template-context folder if it exists, while all templates load the template-context/templates/templates.php. 

It is up to the developer to include separate files based on [wordpress conditionals](https://codex.wordpress.org/Conditional_Tags).

The variables received by the context file are documented in the doc block at the header of each file.

All variables are [passed by reference](https://www.php.net/manual/en/language.references.pass.php) as of theme version 2.5.0. This means that variables can be mutated in the context file, before they're subsequently applied to the twig template in the block / template render function.

## Type classes 
Type classes also live in the template-context folder. 

The type classes perform two main functions: 
 - To assist the developer in passing properties into timber context before templates are included. Each type class has [php docblock](https://docs.phpdoc.org/guide/getting-started/what-is-a-docblock.html) style comments, which provides ide intellisense describing properties that templates expect to receive
 - To modify the the data, making it suitable for template consumption, and removing empty properties before interpolating the data into the template.

### properties

The type classes take advantage of php's [magic methods](https://www.php.net/manual/en/language.oop5.magic.php), which store properties in snake_cased format, and allow them to be retrieved in camelCased format. This allows for consistency between meta fields which are typically snaked_case, and block attributes which are typically camelCased

Eg 
```php
$template_props->sub_title = 'Sub title';
```

In a twig template, the field wil be available in camelCases format.

```twig
{{ templateProps.subTitle }}
```

#### class_name
the class_name property is an array, which allows individual classes to be add / removed more easily. 
The `template_props` variable in each block context specifically, has array keys that match its array values. 
This means that classes can be added with `$template_props['class_name'][] = 'my-class';`, or `$template_props['class_name']['my-class'] = 'my-class';`
and removed with `unset($template_props['class_name']['my-class'];`


#### css

Field reserved for future use

#### dataset

an array of key value pars that will be returned as `data-key="value"`
values can be primitive types, or be a an `XD_Type_Base` class extension themselves, whose [get_data](#get_data) method will be invoked when rendering the property.
if the property value is null, an empty property data key will be rendered. See [get_data](#get_data)

eg:
```php
$template_props->import(
	array ( 
		'dataset' => array(
			'foo'     => 'bar', 
			'baz'     => 'qux',
			'options' => new XD_Type( array( 'cls_page'=> "modal-open") ),
			'button'  => null
		) 
	) 
);
```

#### aria
The aria property functions in the same manner as the dataset property with the exception that it does not accept properties that contain an `XD_Type_Base` class . See See [get_data](#get_data)

eg:
```php
$template_props->import(
	array ( 
		'aria' => array(
			'pressed'  => true, 
			'checked'  => true,
			'autocomplete'  => 'inline'
		) 
	) 
);
```
#### id
When an id is passed to the type class, its value will be be replaced with `xd-<type>-<id>`. This allows for the same id, or block id to be passed to several types, each with independent id's 

### Methods

Each type class has the following methods available

#### import

import allows fields to be added to the type class in bulk via an array. These fields will also be stored internally in snake_cased format

Eg.
```php
$template_props->import = array( 'title' => 'Title', 'sub_title' => 'Sub title' );
```

The import method is also invoked when a type class is instantiated
Eg.
```php
$props = new XD_Type(array( 'title' => 'Title', 'sub_title' => 'Sub title' ));
```

#### is_empty
This method can be modified as needed and should return `true` when the type does not contain enough data to render a template or potion of a template.

For example, the `is_empty` method in an `XD_Image` class looks like this:

```php
public function is_empty() {
		$primary   = (array) $this->primary;
		$secondary = (array) $this->secondary;
		$video     = (array) $this->video;
		return $this->empty( $primary ) && $this->empty( $secondary ) && empty( $video );
}
```
which makes it possible to test the presence of an image in a twig template without needing to check each sub property individually. Eg.

```twig
{% if image %}
	{% include 'includes/image.twig' %}
{% endif %}
```
as opposed to:
```twig
{% if image.primary.id or image.secondary.id %}
	{% include 'includes/image.twig' %}
{% endif %}
```

#### get_data

get_data, is rarely needed to be used in context files; however, it is important to note, that **all** data fed into a twig template via template context is filtered through get_data before it is rendered

by default, get_data makes the following modifications to the class data before it is fed into a template:

 - empty properties are stripped out.
   -  properties are considered empty if:
      -  it contains an empty string
      -  it contains an empty array
      -  it contains a null value
      -  it contains an extension of an `XD_Type_Base` class whose `is_empty` method returns `true`
   -  unlike php's `empty` language construct,  properties are **not** considered empty if they're `false`:
-  The `id` property is returned as `id="value"`
-  The original `id` is available through the `ui` property
-  The `class_name` property (which should be an array, is joined and cast to a string with `" "` delimiters)
-  The `dataset` property is returned as a datset string `data-foo="bar" data-baz="qux" data-options='{clsPage:"modal-open"}'`  See [dataset](#dataset)
-  The `aria` property is returned as a string `aria-pressed="true" aria-checked="true" aria-autocomplete="inline"`  See [aria](#aria)
