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

class EasyBlogThemesHelperMedia extends EasyBlogThemesHelperAbstract
{
	/**
	 * Renders a form row in media manager panel
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function field($type, $name, $title, $value = null, $attributes = array(), $placeholder = '')
	{
		$title = JText::_($title);

		// We may allow user to provide us with a string as attribute
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}

		if (is_array($attributes) && !empty($attributes)) {
			$attributes = implode(' ', $attributes);
		} else {
			$attributes = '';
		}

		if ($placeholder) {
			$placeholder = JText::_($placeholder);
		}

		$theme = EB::themes();
		$theme->set('placeholder', $placeholder);
		$theme->set('type', $type);
		$theme->set('title', $title);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/media/field');

		return $output;
	}

	/**
	 * Renders a textbox for media manager's panel
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function textbox($name, $value, $id = '', $attributes = array(), $placeholder = '')
	{
		return $this->renderCommonOutput($name, $value, $id, $attributes, $placeholder, 'textbox');
	}

	/**
	 * Renders a textbox for media manager's panel
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function textarea($name, $value, $id = '', $attributes = array(), $placeholder = '')
	{
		return $this->renderCommonOutput($name, $value, $id, $attributes, $placeholder, 'textarea');
	}

	/**
	 * Renders common input fields
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function renderCommonOutput($name, $value, $id, $attributes, $placeholder, $themeFile)
	{
		if ($placeholder) {
			$placeholder = JText::_($placeholder);
		}

		if (!$id) {
			$id = $name;
		}

		// We may allow user to provide us with a string as attribute
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}

		if (is_array($attributes) && !empty($attributes)) {
			$attributes = implode(' ', $attributes);
		} else {
			$attributes = '';
		}

		$theme = EB::themes();
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('placeholder', $placeholder);

		$output = $theme->output('site/helpers/media/' . $themeFile);

		return $output;
	}
}
