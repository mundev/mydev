<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogBlockHandlerGallery extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fa fa-image';
	public $element = 'gallery';

	public function meta()
	{
		static $meta;
		if (isset($meta)) return $meta;

		$meta = parent::meta();

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;

		$template = EB::template();
		$meta->galleryContainer = $template->output('site/composer/blocks/handlers/gallery/container');
		$meta->galleryItem = $template->output('site/composer/blocks/handlers/gallery/item');
		$meta->galleryPlaceholder = $template->output('site/composer/blocks/handlers/gallery/placeholder');
		$meta->galleryMenuItem = $template->output('site/composer/blocks/handlers/gallery/menu_item');
		$meta->galleryListItem = $template->output('site/composer/blocks/handlers/gallery/list_item');

		return $meta;
	}

	public function data() 
	{
		$config = EB::config();
		$data = (object) array();
		$data->strategy = "fill";
		$data->ratio = '16:9';
		$data->items = array();

		// Workaround to store arrays
		$data->itemsKeyArray = array();
		$data->itemsArray = array();
		$data->primary = null;
		$data->imageVariation = $config->get('main_media_variation');

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
	 * Retrieve Instant article html
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

		// if the gallery has no images, return false
		if (empty($imageUri)) {
			return false;
		}

		return $html;
	}

}

