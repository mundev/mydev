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

class EasyBlogBlockHandlerPdf extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fa fa-file-pdf-o';
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
	 * Validates if the block contains any contents
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function validate($block)
	{
		// if no url specified, return false.
		if (!isset($block->data->url) || !$block->data->url) {
			return false;
		}

		return true;
	}

	/**
	 * Displays the html output for a file preview block
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false, $useRelative = false)
	{
		if ($textOnly) {
			return;
		}

		if (!isset($block->data->url) || !$block->data->url) {
			return;
		}

		if ($useRelative) {
			$block->data->url = EB::string()->abs2rel($block->data->url);
		}

		// Set a default height value
		if (!isset($block->data->height)) {
			$block->data->height = 600;
		}

		$template = EB::template();
		$template->set('block', $block);
		$contents = $template->output('site/blogs/blocks/pdf');

		return $contents;
	}
}
