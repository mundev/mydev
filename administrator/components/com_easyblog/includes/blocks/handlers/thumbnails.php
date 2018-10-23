<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/abstract.php');

class EasyBlogBlockHandlerThumbnails extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fa fa-th';
    public $element = 'gallery';

    public function meta()
    {
        static $meta;

        if (isset($meta)) {
            return $meta;
        }

        $meta = parent::meta();

        // Disable dimensions fieldset
        $meta->properties['fonts'] = false;

        $template = EB::template();
        $meta->thumbContainer   = $template->output('site/composer/blocks/handlers/thumbnails/container');
        $meta->thumbColumn      = $template->output('site/composer/blocks/handlers/thumbnails/column');
        $meta->thumbItem        = $template->output('site/composer/blocks/handlers/thumbnails/item');
        $meta->thumbPlaceholder = $template->output('site/composer/blocks/handlers/thumbnails/placeholder');

        return $meta;
    }

    public function data()
    {
        $data = (object) array();
        $data->layout = 'stack'; // grid, stack
        $data->column_count = 4; // 1-6
        $data->strategy = "fit"; // fit, fill
        $data->ratio = 4 / 3;

        return $data;
    }

    /**
     * Validates if the block contains any contents
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function validate($block)
    {
        // Gallery block do not need to do validation. js return true.
        return true;
    }

    /**
     * determine if current user can use this block or not in composer.
     *
     * @since   5.0
     * @access  public
     * @param
     * @return boolean
     */
    public function canUse()
    {
        $acl = EB::acl();
        return $acl->get('upload_image');
    }

    /**
     * Retrieve AMP html
     *
     * @since   5.1
     * @access  public
     */
    public function getAMPHtml($block)
    {
        $html = '<amp-carousel width="1280" height="970" layout="responsive" type="slides">';

        // Get image blocks in this gallery 
        $imageBlocks = $block->blocks;

        $imageUri = array();

        foreach ($imageBlocks as $imageBlock) {

            if (in_array($imageBlock->uid, $imageUri)) {
                continue;
            }

            $media = EB::mediamanager();
            $imagePath = $media->getPath($imageBlock->data->uri);

            $imageData = @getimagesize($imagePath);

            if (!$imageData || empty($imageData[1])) {
                continue;
            }

            $info = 'width="' . $imageData[0] . '" height="' . $imageData[1] . '"';

            $html .= '<figure><amp-img ' . $info . ' src="'. $imageBlock->data->url .'" layout="responsive" ></amp-img></figure>';

            $imageUri[] = $imageBlock->uid; 
        }

        $html .= '</amp-carousel>';

        // if the thumbnail has no images, return false
        if (empty($imageUri)) {
            return false;
        }

        return $html;
    }
}
