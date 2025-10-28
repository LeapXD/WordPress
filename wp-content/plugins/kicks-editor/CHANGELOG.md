# Changelog  
## cheatsheet:
```bash
git log --pretty=format:"- %s"
```

# Changes
# 2.2.0
- update hooks for Timber v2 compatibility
- Add Timber Loader settings page and version selection
- remove timber v1 from old location
- add timber v1 and v2

## 2.1.10
- Refactor block settings merging and enhance attribute handling for features
- Bump version to 2.1.10 in plugin files and package metadata
- Add size control component and supporting functions
- Add block settings merging and enhance inspector controls with new features
- Enhance block settings handling by adding support for view and editor styles, scripts, and context properties

## 2.1.05 
- Refactor xd_merge_meta_value_defaults to improve handling of associative arrays in meta values
  
## 2.1.04
- Improve server side block template loading
  
## 2.1.03
- Refactor block registration logic to improve compatibility and consistency in block settings
- 
## 2.1.02
- Fix block registration not working for acf blocks
  
## 2.1.01
- Fix for wp 8.1 compatibility issues

## 2.0.26
- Fix editor plugins not loading on post types other than pages

## 2.0.25
- build assets
- ensure plugins not loaded for wp_font_family and wp_font_face in custom post meta fields
- refactor: update template context file handling and ensure early content rendering for block dependencies
- feat: add multi-select control component
- refactor: update post selection logic to use post ID and include template data
## 2.0.24
- widget editor fixes
- update composer deps
  
## 2.0.23
- Build
- Fix meta field default
- pass block state through link toolbar button
- Update deprecated imports
- Pass source block when fetching block context
- Add aria attribute to template
- Add class to toolbar control
- Pass id through field group control
- Add embed control

## 2.0.22
- Remove default meta schema props for all but string types
- Add missing include
  
## 2.0.21
- Add clear gallery button to image gallery.

## 2.0.20
- add child theme option to version compare function
- add edit links to link popups
- add version option to block contexts
- require names on block modules on new sites

## 2.0.19
- expose original render_callback to  theme
- ensure is_plugin_active is available
- add custom eventHandler functionality to field group and repeater components
- fix attributes getting overwritten by block settings on acf blocks

## 2.0.18
- fix block context not being merged with block settings
- fix attributes getting overwritten by block settings on acf blocks
- add custom eventHandler functionality to field group and repeater components

## 2.0.16
- expose block clientId in handler callback

## 2.0.15
- add custom eventHandler functionality to components
- add custom eventHandler registration to block settings

## 2.0.14
- fix link component not saving data

## 2.0.12
- replace all button attributes on save to ensure flyout is removed
 
## 2.0.11
- fix for number / integer attributes
- Prevent block titles from inheriting from parents
  
## 2.0.09
- fix a paragraph block issue
  
## 2.0.08
- update repeater component
  
## 2.0.07
- fix link toolbar button and block splitting
- fix block variations in wp 6.5

## 2.0.06
- improve class_names functionality in blocks
- back-end image fix

## 2.0.05
- Fix number / integer value
  
## 2.0.04
- add built assets
- add additional attributes to link controls for entity type and id
- add static field to xd_post class to track current post
- add disabled option to checkbox group control
- fix meta field registration happening too early
  
## 2.0.03
- fix number type attribute not working
- deactivate Timber plugin if installed
  
## 2.0.02
- build assets
- Fix media upload styles
- add and merge default meta fields
- fix template includes
- remove snake cased data attributes
- add video error notices
- add gallery preview control
## 2.0.01
- Fix field group field id

## 2.0.0
- Bake in Timber
## 1.6.63
- build assets
- make plugin json overrideable
- editor image style fixes
- update version
- fixes to template includes
## 1.6.61
- fix `is_empty` method
## 1.6.6
- only emit json block settings in xd/ namespace
- fix for associative array detection 
## 1.6.5
- fix for child theme / plugin block overrides
- fix for image in repeater field

## 1.6.4
- allow multiple block context files
- use template dir not stylesheet
- allow child theme block json override
- add search control

## 1.6.3
- fix property importing on type classes
## 1.6.2
- fix snake_case function
- add 'inputType' prop to plugin fields
- improve empty type filtering
- reduce redundant meta field registration

## 1.6.1
- fix snake_case function
  
## 1.6.0
- fix number / integer input handling

## 1.5.59
- fix template load order
- fix handling of anchor id
- allow dynamic properties on xd post class
- fix repeater field label position

## 1.5.58
 - fix twig folder locations

## 1.5.57
- fix issue with acf and dynamic blocks with same name

## 1.5.56
- Add taxonomy dropdown filter

## 1.5.55
- template include fix

## 1.5.54
- new hooks and Relavanssi fix

## 1.5.53
- fix attempting to close dir on non resource

## 1.5.52
- Prevent using XD_Post class on older themes

## 1.5.51
- fix blocks not splitting properly on carriage return

## 1.5.5
- improvements to paragraph editing

## 1.4.43
- fix undefined block body controls

## 1.4.42
- make buttons more prominent and improve link control title field
- add width control, message panel, video preview, pass props to radio button group

## 1.4.41
- refactor modules and remove slot fills
  
## 1.4.4
- fix acf block rendering
## 1.4.1
- added self update capability
- fix template include bug
- ensure block config is initialized in older themes
- ensure block config is initialized in older themes
- remove timber contect override on older themes
- prevent extra wrapping div on older themes on editor blocks and ensure core/list-item is available
- fix legacy css not enqueued
- restore blocks auto margins in legacy css
- fix incorrect include for acf blocks
- fix error when no screen id

## 1.4.0
   TODO - add changelog for 1.4.0

## 1.3.1
- fix for potential issues with block attributes
## 1.3.0
- Added function for checking theme version
- Don't load editor blocks in new themes (> 2.4.3)
- Update eslint and eslint babel parser
- Added patches for block render functions to fix broken functions in theme in WordPress >= 6.1.0
- moved all block and editor settings to global 'xd_settings' object

## 1.2.0
- update build deps
- update plugin  version, script deps, and add production build script
- update imports
- improve handling of  block override and fix bug when block has no settings on block-settings.json
- move innerblocks filter to filters - since it's a filter, not a module
- add xd/image and xd/richtext to block title ignore list
- update block styles to allow contextual style options
- update block-settings todo note
- allow innerblocks wrapper setting and auto registering blocks with same name as existing blocks (thus overwriting them)
- added schema for block-settings.json
- remove requirement for "context":"all" on innerblocks
- prepare admin metbox area
- make all block templates overridable
- added richtext component
- Added todo note for block styles
- fix script order
## 1.1.0
- Add link module
- Fixes blocks settings override for blocks with no settings
- Allows override of blocks registered with custom register setting 
## 1.0.02
- Remove typo from two-tile-inner block
- Create this changelog




