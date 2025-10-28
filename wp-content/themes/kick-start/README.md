# Kicks
## Release: 2019.5

## About

The Leap WordPress framework is a complete WordPress installation, custom Leap theme "Kicks", and curated selection of community and custom plugins.  



-----

## Getting Started

### Step 1: Create Pantheon

### Step 2: Import Starter Database

### Step 3: Setup Local

-----

## Updating WordPress / Pantheon Upstream

The Leap Framework supports acting as a Pantheon custom upstream (see https://pantheon.io/docs/create-custom-upstream).  This means it supports pulling from the Pantheon WordPress upstream.

To pull from the Pantheon WordPress Upstream ensure that you have the Pantheon remote listed in your git settings.  If it is not there, add it using the following command:

`$ git remote add pantheon-wordpress git://github.com/pantheon-systems/WordPress.git`

Ensure you are on the branch you want to update (for this example it is __master__).

`$ git checkout master`

Fetch updates from the pantheon-wordpress remote.

`$ git fetch pantheon-wordpress`

Merge updates from the pantheon-wordpress remote into your branch (here the example is __master__).

`$ git merge pantheon-wordpress/master`

Push changes to your branch (here __master__).

`$ git push origin master`

-----

## Updating Plugins




-----
## SASS/CSS

Kicks is built on UIKit.  
Unused UIKit components are disabled in the source/scss/core/\_uikit.scss.  
Use UIKit's !default properties and hook mixins to overwrite or add properties to UIKit components prior to compilation.

See the example of hooks in \_uikit.scss and available variables defaults that can be set in \_uikit_variables.scss.


-----
## Background Images (srcset and lazy loading)
The background-image property provides better control and backwards compatibility.
Setting images in blocks requires the use of inline styles.
Inline background images are a problem because they can only have one source so you're stuck choosing between loading an image that is too large on mobile (performance hit both for the user and in PageSpeed), or an image that is too small for desktop (low quality).

Kicks offers a solution that mirrors the functionality of srcset and lazy loading.

Before:
`<div style="background-image: url('my-background-image-high-res.jpg'); background-size: cover;"></div>`  

After:
`<div
  style="background-image: url('my-bg-img-low-res.jpg'); background-size: cover;"
  class="bg-srcset"
  bg-srcset="my-bg-img-sm-res.jpg 600w, my-bg-img-md-res.jpg 900w, my-bg-img-lg.jpg 1200w, my-bg-img-xl.jpg 1600w"
  bg-size="300"
 ></div>`


__class__ `bg-srcset` Required class to trigger JavaScript.

__data-bg-srcset__ A comma-separated list of image resources formatted the same as a standard srcset list.

__data-bg-size__ The current size of the asset.  A default value is required for the loading media.  This value is used to determine if a new source should be loaded and set.  A new source will be loaded if the current screen sizes is larger than the current image AND a larger image asset is available.  This property is required and should be set to the value of the size at which you want the replacement to take place.

__NOTE:__ *You are not required to have an original source image.*


-----
## Preloading
Preloading allows the browser to begin loading assets immediately and in parallel. It is a critical part of performance optimization on modern websites.

See MDN article for details.


-----
## The Waterfall
Kicks attempts to optimize browser performance in a number of ways.
First it divides all theme code into critical assets and deferred.

Critical assets are those that are required to render the top of the page as quickly as possible. This will result in the user seeing a pager rendered as quickly as possible, while also representing everything that will be measured PageSpeed.

Full functionality of the page and site comes from the assets that are deferred.  These _main_ files are almost certainly where new features should be added and the bulk of the website styling and javascript functionality should be loaded.

You should go through and manually add preloads for everything required on your site, including plugins no already defined in Kicks.


-----

## Namespacing

CSS/SCSS: .xd-
SCSS variables: $xd-

 
