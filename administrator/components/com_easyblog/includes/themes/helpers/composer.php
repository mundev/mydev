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

class EasyBlogThemesHelperComposer extends EasyBlogThemesHelperAbstract
{
	/**
	 * Renders a field in the composer panel
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function checkbox($name, $title, $value = null, $attributes = array())
	{
		$title = JText::_($title);

		if (is_array($attributes) && !empty($attributes)) {
			$attributes = implode(' ', $attributes);
		} else {
			$attributes = '';
		}

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/composer/checkbox');

		return $output;
	}

	/**
	 * Renders a textbox in the composer panel
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function textbox($name, $value = null, $id = null, $attributes = array())
	{
		// We may allow user to provide us with a string as attribute
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		
		if (is_array($attributes) && !empty($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		// Default to use name as the id
		if (!$id) {
			$id = $name;
		}

		$theme = EB::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/composer/textbox');

		return $output;
	}


	/**
	 * Renders a field in the composer panel
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function field($type, $name, $title, $value = null, $attributes = array())
	{
		$title = JText::_($title);

		// We may allow user to provide us with a string as attribute
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}

		if (is_array($attributes) && !empty($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		$theme = EB::themes();
		$theme->set('type', $type);
		$theme->set('title', $title);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/composer/field');

		return $output;
	}

	/**
	 * Renders a fieldset in the composer panel
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function fieldset($type, $name, $title, $value = null, $attributes = array())
	{
		$title = JText::_($title);

		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		
		if (is_array($attributes) && !empty($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		$theme = EB::themes();
		$theme->set('type', $type);
		$theme->set('title', $title);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/composer/fieldset');

		return $output;
	}
}
