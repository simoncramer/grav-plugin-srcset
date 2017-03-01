# Grav srcset Helper

`srcset Helper` is a simple [Grav](https://getgrav.org/) Plugin that makes the images (.jpg) in your pages responsive.

# Installation

Installing the srcset Helper can be done in one of two ways. Using GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

## GPM Installation (Preferred) (Coming soon!)
<!---
The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's Terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install srcset-helper

This will install the srcset Helper into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/srcset-helper`.-->

## Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `srcset-helper`. You can find these files either on [GitHub](https://github.com/simoncramer/grav-plugin-srcset) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/srcset-helper
    
# Config Defaults

The default config is only an example and has to be changed in accordance with the theme. Otherwise the correct image sizes are not delivered to the browser.

# Configuration

This plugin only affects images in your page content. Not in your theme!

## Set Up

The options 'Maximum Width', 'Minimum Width' and 'Step Size' govern which image sizes are generated. The aspect ratio of the image is always preserved and it is not enlarged in case that the original is small.

For example: In your responsive theme the smallest space the image has to fill is 300px, set 'Minimum Width': 300. Assume your maximum page width is 1000px, set 'Maximum Width': 1000. The 'Step Size' should be as small as possible to let the browser pick the best fitting image. However setting the 'Step Size' to small will result in failure because your php installation runs out of memory or time. As a compromise set 'Step Size': 140. This will generate 5 images with the widths 300px, 440px, 580px, 720px, 860px, 1000px.

The 'Image Sizes' attributes tell the browser when to load which image. Before rendering the page it is not known what space the image has to fill. Therefore you as a developer/theme designer have to tell the browser when to load which image only depending on the view port of the visitor. 

In most cases the different 'Screen Sizes' correspont to the breakpoints of your theme. So for each breakpoint add and 'Image Size' and put in the 'Screen Size' field '(max-width: BREAKPOINTWIDTH)'. BREAKPOINTWIDHT can either be in em or px. For example: '(max-width: 30em)' for mobile phones. The corresponding 'Image Size' can be set in percentage of the viewport (e.g. 60vw) or in pixel (e.g. 100px). For example: On a screen that is at most 30em wide (phone), your image takes the width of the entire view port. Set 'Image Size': 100vw. On a screen that is at most 48em wide (tablet), your image takes half of the width of the view port (screen). Set 'Image Size': 50vw.

The size attribute in HTML is very powerful. A good introduction can be found at [EricPortis](https://ericportis.com/posts/2014/srcset-sizes/).


## Page Config

You can override the plugin options by adding overrides in the page header frontmatter:

```
srcset-helper:
    active: false
```
