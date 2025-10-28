# Changelog  
## cheatsheet:
```bash
git log --pretty=format:"- %s"
```


# Changes

## 2.7.11
- fix: Correct slug assignment for user popups in template props
- Enhance grid and content block styles and functionality

## 2.7.10
- Bump version to 2.7.10 in package.json and style.css
- fix: Add check to prevent modification of custom logo HTML if class is not found
- fix: Update set_quality method to accept dimensions parameter and improve compression handling
- feat: Add support for flyouts and modals in navigation menus
- fix: Ensure caption is set conditionally to avoid potential errors
- fix: Usqe modern block splitting in button
- fix: Ensure hero_video is cast to array for consistent data handling
- fix: Adjust image icon dimensions and options handling for better responsiveness
- feat: Add grid layout and grid cell blocks with support for alignment and height modules
- Move screen reader text for improved accessibility in single.twig
- Remove parent reference from core/embed block configuration
- update modal allowed blocks

## 2.7.1
- Refactor flyout ID assignment and dataset properties for thumbnail card
- Add permalink assignment for button entity subtype when entity ID is present
- Add unique slug generation for flyouts and modals on save
- Fix default parameter for xd_register_ui_page function and register team_member UI page
- Remove core quote block configuration file
- Refactor password form styles for improved layout and consistency
- change padding bottom on teo tile mobile to static large padding
- Prevent anchor lins inside the flyout from preventing the flyout to close
- Update changelog for version 2.7.0 to include recent change

## 2.7.0
- Allow unsafe HTML in ACF fields and suppress escaped HTML notice
- Remove container parent requirement for basic content blocks
- Fix parent block reference for column block in block.json
- Hide video poster image when video is playing
- Add build script for generating blocks manifest
- Fix post type archive args handling
- Refactor caption margins in image slider

## 2.6.02
- Fix z-index issue in background image styles by adding !important to ensure proper layering
- Enhance gravity forms styles by adding hidden and honeypot field adjustments
- Enhance flyout and modal styles with responsive padding and display adjustments
- Refactor UIkit component loading to improve dynamic imports and dependency management
- Improve page template selection by adding ID check for better handling of non-existent pages
- Refactor template conditionals to improve readability and maintainability
- Add dynamic body class support in base template
- Add support for multiple open items in accordion component
- Refactor UI component initialization and toggle handling for improved functionality and event management
- Enhance button and flyout functionality with version checks and improved UI link handling
- Add Gravity Forms block support and fix iframe rendering
- Update media query breakpoint for post card styling
- Refactor SVG icons for improved styling and transitions
- Add CountUp component and EasingMixin for animated value display
- Add documentation for xd_register_archive_page, xd_register_ui_page, and xd_register_static_page functions
- Refactor image handling in xd_get_picture function for improved SVG support
- Add project cards block context file
- fix bugs after updates
- ui bug fix
- remove header template part
- Add UI components initialization and routing logic

## 2.6.01
- Modals and flyouts now open based on the url hash
- Add post relationship component
- gravity form style fix
- migrate leap features from plugins to theme for ease of use
- Unsure gravity fom page data is only loaded for main query
- Add postinstall script to build production assets after installation

## 2.5.70
- Fixed gravity form attachment not downloading

## 2.5.69
- Bump version to 2.5.69 in package.json and style.css
- Implement workaround to stop videos from playing on Apple devices and Safari when modal is closed
- Add slug to button dataset for improved context handling
- Refactor image size attributes and update media query breakpoints for improved responsiveness

## 2.5.68
- fix issues when menu button removed
- allow video auto play to be disabled

## 2.5.67
- fix handling of template loading in nested components
- adjust gravityform iframe confirmation message height
- hide gravity forms hidden inputs

## 2.5.66
- Form styling improvements
- Use flyout background color variable in menu
- Add invert button option
- Accessibility improvements

## 2.5.65
- Lazyload gravity forms
- improve preloads
- Prevent erorr when displaying cards linked to deleted post
- autoplay videos with uikit inview
- Accessibilty improvements
- prevent error on empty images
- gravity forms json name change
- image style fixes
- remove iframe borders
- reduce flyout padding
- set link text decoration to none
- wait for video to play before testing for error

## 2.5.64
- add modal 'flyout' variant
- side stickybutton ff fix
- image width fix
- Scroll spy selector fix

## 2.5.63
- Prevent instantiation of empty blocks in template parts
- Fix image attribute concatenation in xd_get_picture function
- Update layout.scss to fix admin bar styling
- Add password form styles and update password-protected template

## 2.5.62
- Image/video fix

## 2.5.61
- Fix for hero button still displaying when empty
- 2 column heading tweak
- select container css tweak
- menu + button css tweak
- Slider nav dot fix
- Add lazy loading gravity forms in flyouts
- fix modal video not playing on open
- update changelog and version
- image fixes

## 2.5.60
- Image fixes
  
## 2.5.59
- Navbar updates
- Uikit component import fixes
- Hide alertbar when menu opened
- Remove old alert bar field
- remove unused context file
- make outer and inner content overrideable in block template
- fix for when image is undefined in image template
- image style fixes
- css class fix on old testimonial
- Error fixes with image and video classes
- Allow child theme to use icons in parent theme and override
- Remove old testimonial slider from allowed blocks
- Side sticky button firefox / safari fix
- Limit slider dots to 5
- Added new icons
- fix options page links

## 2.5.58
- fix options page links
- fix image caption
- vertical rhythm fixes
- gravity form style fixes
- fix buttons margin
- fix slider caption position
- restore old testimonial slider for sites that need it
- fixed margin classes applying padding
- Fix regression - default search modal to disabled
- Fix timeline image
- image caption image fix
- back-end image fix

## 2.5.57
- add background image type class
- improve image captions and template props filters
- Gravity form style fixes
- Make Icons accept svg only
- background image fix on iphone
- fix editor style wrapper class specifity
- icon fixes
- fix alert bar
- disable search by default
- add external link attributes to list items
- scroll spy fix
- return dynamic uikit component in loader promise
- gravity forms fixes
- add flyout feature to buttons

## 2.5.56
- preload blocks assets without rendering blocks
- fix video size in large gallery
- add flyout feature to buttons
- fix post card display on tablet
- add missing footer block attribute
## 2.5.55
- update changelog and version
- video fixes
- image fixes
- add hero video size suggestions
- add gallery preview control
- thank you page fix
- add delay animation class
- script enqueue fixes
- Style tweaks
- Template tweaks
- hero tweaks
- Image tweaks
- Button tweaks
- slider tweaks
- back end gallery fix
- vertically align popup center
- update sass for sass version change
- quick facts d2 colour
- hero video size suggestions
- portrait video option
- slow slider velocity
- add natural image width option
- fix logo grid image widths
- decrease column gap on tablet
- testimonial slider fixes slider tweaks card widths
- split assets, update uikit and sass version

## 2.5.54
- add custom image edit class
- scroll spy fix
- two column style fix
- fix post card image width
- preload block scripts
- two tile animation
- defer parallax js loading
- Style fixes
- Update sass
## 2.5.53
- editor image style fixes
- fix missing post type on post card insertion
- make link in linked list contain all text
- fix contact page form not showing
- fix footer fields not showing
- Fix cta align control causing dirty editor state
- page card style fixes
- add spacing tiny option on galleries / post-cards
- fix card button icon

## 2.5.52
- update version
- column fixes
- add builtin page template options
- add block frontend js feature
- scrollspy fix
- various block fixes
- various template fixes
- various style fixes
## 2.5.51
- fix hero small padding
- company detials template part fix
- fix various styles

## 2.5.50
- add missing example block template
- fix xss vulnerability
- fix cta default setting
- fix cta classes
- fix image gallery classes
- add footer options
## 2.5.40
- changes needed for child theme compatibility
- sass color variable improvements
- add right and left stacked gallery options
- add poster option to cta
- Make uikit external
## 2.5.31
- move footer logo to company details options
- only display modal when there is one
- change blog filter alignment
- improve hero vertical alignment
- reduce xl spacing for hero
- fix side sticky button position
- fix icon sizes
- only load image gallery modal when there is one
- fix two tile padding on mobile / tablet
- added color theme classes
- hero vertical alignment improvements
- make logo grid align with block title
- timeline block qt
- add image-gallery 11 col options
- remove margin from columns that contain a heading only
- fix for when footer logo only partially selected
- update version in style.css

## 2.5.3
- replace footer logo widget with company details logo block
- fix for navbar when no menu button

## 2.5.21
- add cta alignment option
- add homepage hero button

## 2.5.2
- remove debug line
- fix slider nav option
- improve empty video and video modal detection
- add 'enabled' property to search modal and menu flyout
- add anchor to columns block
- fix padding on card slider
- add timeline block
- handle form submit for single select


## 2.5.1
- Merge branch 'master' of bitbucket.org:leapxd-dev/kicks-theme
- change to slider arrows and add contact form anchor
- QT - image breakpoint default and two tile image width fix
- Hero tweak
- QT of sticky button
- fixed homepage hero vertical alignment
- Merge branch 'master' of bitbucket.org:leapxd-dev/kicks-theme
- add full width card slider variant
- some adjustments and uikit component additions for js for the plugins
- Making additions, tweaks, and changes as per 3 Leap Products outline
- Fixed footer bottom margin
- hide slider arrows on sliders with only one slide
- update archive template to allow page content block
- add support for theme extension plugins
- editor scss fixes
- fix card label z-index
- fix block title classes
- update archive page functionality and add taxonomy helper function
- updated header class locations
- re-arranging editor stylesheets order
- fix options.php
- 2.5.1 updates


## 2.5.0
- TODO - add changelog for 2.5.0 see 2.5.0.md

## 2.4.3
- restore babel options, list.scss type fix and bump version
- rollback uikit version
- minor fixes to list component
- remove duplicate entry for core list in block settings
- fix menu colours
- add types to two tile context file
- improve colour replacement in svg sprite generator
- add a twig file for richtext block
- fix for wordpress external camelCase imports and image path in css output
- add page for custom post archive feature
## 2.4.2
- fix array partition bug when no array passed in
- remove theme support for block templates
- remove two tile experiment
- improve block context variables
- fix js error when amenities map not enabled
- add dynamic list block and disable acf list block
- add link module attributes
- fix bug with new block templates not saving
- fix for install script breaking when mu-plugins folder not present
- added delay to install script after writing constants 
  to ensure constants are updated before page reload
- cleanup unused sass
## 2.4.1
- updated package deps and scripts
## 2.4.0
- Converted block-settings to json format
## 2.3.0
- Amenities Map Component rebuilt in twig as a new ACF block, old amenities map code removed. 

## 2.2.0
- added and modified theme.json
- revamping how we use colors and fonts by implementing the css variable sheets in the new theme folder. 
- tidying various stylesheets up in general to reduce repetition or unused css.

## 2.1.16
- fix mu-plugins folder not being created on init
- remove unused node scripts

## 2.1.15
- Fix class names in `/inc/class-xd-scrollspy.php` and `/views/search.twig`, and some styles for search too.
- iPad/iPhone style fixes for dropdowns and submit button on forms. 
- Change `/views/uikit/offcanvas.twig` from `div` to `aside` tag.
- Replace hex colours and RGBA overlays with variables set in `/scss/variables/_colours.scss`.

## 2.1.14
- xd-image margins on img now instead of wrapper
- remove defunct row_direction_class from two-column text view
- fix hero-blog and hero-full views titles and subtitles
- work on hero partials naming and classes: 
  - each hero now has a class that specifies: 
    - where it belongs (`hero-inner-page`, `hero-front-page` `hero-blog`)
    - weather it's a full page hero or a small hero (`hero-full`, `hero-small`)
  - `hero-inner.twig` renamed to `hero-small.twig`
    - references to this file updated in templates.
- Fix `inc/install/styleguide-content.html` page so it shows all buttons
- Replace xd-colour__quinary with appropriate order number (tertiary) everywhere so it doesn't skip numbers.
- Replace hex code colors with variable names (footer, two-column)

## 2.1.13
- Correct sticky button colour when only one button
- Add missing pagination

## 2.1.12
- remove old unused colour variable
- fix media grid plus on tablet issue

## 2.1.11
- Video updates: Lazy load modal videos, Fix modal video field name, add allow="autoplay" attribute to iframe
- fix theme installer not working when mu-plugins folder not exists
- Image updates: Remove mobile crop, disable lazyload for hero images
- Accordion fixes: Icon gettng squished, Unordered list in accordion have outline not solid bullet
- Search fixes: Fix links on search results, Search icon not clickable
- Allow open in new tab option for sticky buttons
- Fix Cta inner width

## 2.1.1
- Fix pantheon.yml file location
- remove git pull and add set tracking branch from installer
- theme init fixes
- add acf license key
- add template block example
- add parent block props to block render params
- add paramter for site name and improve checks for terminus login status
- improve support for editor plugin theme overrides
- ignore build folder to reduce conflicts

## 2.1.0
- Editor moved to plugin
- incorrect field name for large hero image inner template 
- missing password protected page functionality (twig)
- Clean up fonts scss file, shorten class names
- Two tile changes - split the text and image into two blocks
- Contact field page options cleaned up
- Buttons changed to dynamic block - download icon uses sprite
- Standardise grid / carousel image index
- missing thank-you template (twig) 
- Popup modal re-styled
- Initialization scripts added

## 2.0.1 
- Remove block widgets block editor
- Requires WordPress v5.8
- Add caption to image block
- Refresh image block when image updated in media gallery
- Add wordpress libraries to package json for improved code completion
- Remove redundant image css
- Fixed + button in editor (was 100% width in WordPress v5.8)
- Added Gutenberg plugin - useful for testing theme for upcoming changes - not recommended for production use
## 2.0.0 
- Introduce twig templates
## Release 2019.3
- Introduction of Leap "Standard" blocks:
  - Standard Text
  - Standard Accordion
  - Standard List
  - Standard Carousel
  - Standard Two Tile
- Created "XD Standard" and "XD Advanced" and moved previous blocks into XD Advanced section of block picker.
  

## Release 2019.2
- Merged Foundation Blocks into Foundation Theme
- Added Lazyload support and refactored components to support lazyloading images.
- Added GSAP support.
