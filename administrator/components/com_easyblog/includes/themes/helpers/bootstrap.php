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

class EasyBlogThemesHelperBootstrap extends EasyBlogThemesHelperAbstract
{
	/**
	 * Renders publish / unpublish icon.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function popover($title = '', $content = '', $placement = '' , $placeholder = '' , $html = false )
	{
		$theme = EB::themes();

		if (!$content) {
			$content = $title . '_TOOLTIP';
		}

		if (!$placeholder) {
			$placeholder = $title .'_PLACEHOLDER';
		}

		$title = JText::_($title);
		$content = JText::_($content);
		$placeholder = JText::_($placeholder);
		
		$theme->set('title', $title);
		$theme->set('content', $content);
		$theme->set('placement', $placement);
		$theme->set('placeholder', $placeholder);
		$theme->set('html', $html);

		return $theme->output('admin/html/bootstrap/popover');
	}
}
