<?php
/**
 * srcset-helper
 *
 * This plugin sets the srcset and size attribute of images using Gravs internal methods.
 *
 * Licensed under MIT, see LICENSE.
 */

namespace Grav\Plugin;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Page\Page;
use Grav\Common\Uri;
use RocketTheme\Toolbox\Event\Event;

class SrcsetHelperPlugin extends Plugin
{
    /**
     * Return a list of subscribed events.
     *
     * @return array    The list of events of the plugin of the form
     *                      'name' => ['method_name', priority].
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
        ];
    }

    /**
     * Initialize configuration.
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->active = false;
            return;
        }

        $this->enable([
            'onPageContentRaw' => ['onPageContentRaw', 0],
            'onOutputGenerated' => ['onOutputGenerated', 0]
        ]);
    }
    
    /**
     * Unfortunatelly the 'derivatives' function in markdown does not set the size attribute to the right value. This is done here after all the page is fully rendered to html.
     */
    public function onOutputGenerated()
    {

      $config = $this->grav['config']->get('plugins.srcset-helper');

        if ($config['enabled']) {
            // Get raw content
            $raw = $this->grav->output;
            
            // Assemble the size attribute from config
            $sizeArray = (array)$this->config->get('plugins.srcset-helper.size');
            $size = "";
            foreach ($sizeArray as $sz){
              $size = $size.$sz['screenSize'].' '.$sz['imageSize'].',';
            }
            $size = $size.' '.$this->config->get('plugins.srcset-helper.defaultImageSize');
            $srcset_string = ' sizes="'.$size.'"' ;
            
            // Replace all the size attributes of all images touched by this plugin.
            $raw = preg_replace('~sizes="srcsethelper"~', $srcset_string, $raw);
            
            // Write back the changed html.
            $this->grav->output = $raw;
        }
    }

    /**
     * Add derivatives function in the page's markdown to all images and let Grav handel the image creation.
     *
     * @param  Event  $event An event object, when `onPageContentRaw` is fired.
     */
    public function onPageContentRaw(Event $event)
    {
        /** @var Page $page */
        $page = $event['page'];

        $config = $this->mergeConfig($page);

        $twig = $this->grav['twig'];

        if ($config->get('enabled') && $config->get('active')) {
            // Get raw markdown content
            $raw = $this->grav['page']->rawMarkdown();
            
            // Assemble markdown injection from configuration
            $minWidth = $this->config->get('plugins.srcset-helper.minWidth');
            $maxWidth = $this->config->get('plugins.srcset-helper.maxWidth');
            $stepSize = $this->config->get('plugins.srcset-helper.stepSize');
            $srcset_string = 'derivatives='.$minWidth.','.$maxWidth.','.$stepSize.'&sizes=srcsethelper';
            
            // There are 4 cases of image markup. They are matched one by one.
            //Case 1 ![Test Image 3](../test-post-3/test_image_3.jpg)
            $raw = preg_replace('~(\!\[.*\].*)(=?\.jpg)\)~', '$1.jpg?'.$srcset_string.')', $raw);
            
            //Case 2 ![Test Image 3](../test-post-3/test_image_3.jpg 'alt')
            $raw = preg_replace('~(\!\[.*\].*)(=?\.jpg[^\?](.*))\)~', '$1.jpg?'.$srcset_string.' $3)', $raw);
            
            //Case 3 ![Test Image 3](../test-post-3/test_image_3.jpg?truc)
            $raw = preg_replace('~(\!\[.*\].*)(=?\.jpg\?((.*)))~', '$1.jpg?'.$srcset_string.'&$3', $raw);
            
            //Case 4 ![Test Image 3](../test-post-3/test_image_3.jpg?truc '#alt')
            $raw = preg_replace('~(\!\[.*\].*)(=?\.jpg\?(.*(=?\s.*)))\)~', '$1.jpg?'.$srcset_string.'&$3)', $raw);
            
            // set the parsed content back into as raw content
            $this->grav['page']->setRawContent($raw);
        }
    }
}
