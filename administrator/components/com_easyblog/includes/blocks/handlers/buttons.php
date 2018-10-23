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

class EasyBlogBlockHandlerButtons extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fa fa-square-o';
	public $element = 'none';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();
		$meta->dimensions->respectMinContentSize = true;

		return $meta;
	}

	public function data()
	{
		$data = (object) array(
			'caption' => JText::_('COM_EASYBLOG_BLOCKS_BUTTON_CONTENT'),
			'style' => 'btn-default',
			'size' => '',
			'link' => '',
			'nofollow' => 0,
			'target' => ''
		);

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
		// button not consider content. just return false.
		return false;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		$pattern = '/<span(?:[^>]+class=\"(.*?)\"[^>]*)?>(.*?)<\/span>/';
		preg_match($pattern, $block->html, $match);

		$content = $match[0];

		$html = '<p><button class="btn-eb"><a href="' . $block->data->link . '">' . $content . '</a></button></p>';
		return $html;
	}

}
