<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogBlockHandlerHtml extends EasyBlogBlockHandlerAbstract 
{
	public $icon = 'fa fa-code';
	public $element = 'none';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;
		
		
		return $meta;
	}

	public function data()
	{
		$data = (object) array();

		return $data;
	}

	/**
	 * We need to alter the behavior of the HTML block
	 * to address issues with dynamic scripts that tries to alter the output
	 *
	 * @since 	5.1.4
	 * @access 	public
	 */
	public function getHtml($block, $textOnly = false)
	{
		// Since version 5.1.14, we now render the original codes instead.
		// The reason is because some codes dynamically alters the html codes (se.g: adsense codes)
		if (isset($block->data->original)) {

			// Ensure that the html content is in the correct format
			$block->data->original = EB::string()->fixUnclosedTags($block->data->original);

			return $block->data->original;
		}

		return $block->html;
	}

	/**
	 * Retrieves the output for the block when it is being edited
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getEditableHtml($block)
	{
		// Since version 5.1.14, we now store the original entered in the texteditor instead of the html output of the DOM
		if (isset($block->data->original)) {

			// Ensure that the html content is in the correct format
			$block->data->original = EB::string()->fixUnclosedTags($block->data->original);

			$content = $block->data->original;
		} else {
			$content = isset($block->editableHtml) ? $block->editableHtml : '';
		}

		// Strip any <form> tag to avoid nasty issue with form submitting. #1590
		$content = preg_replace('/<\/?form(.|\s)*?>/', '', $content);

		return $content;
	}
}