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

class EasyBlogThemesHelperSettings extends EasyBlogThemesHelperAbstract
{
	/**
	 * Renders a dropdown settings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function dropdown($name, $title, $options = array(), $desc = '', $attributes = '', $note = '')
	{
		$theme = EB::themes();

		if (empty($desc)) {
			$desc = $title . '_DESC';
		}
		
		if ($note) {
			$note = JText::_($note);
		}
			
		$theme->set('options', $options);
		$theme->set('note', $note);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('attributes', $attributes);

		$contents = $theme->output('admin/html/settings/dropdown');

		return $contents;
	}

	/**
	 * Displays a list of menu forms
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function menus($name, $title, $desc = '', $attributes = '', $note = '')
	{
		$theme = EB::themes();

		if (empty($desc)) {
			$desc = $title . '_DESC';
		}
		
		if ($note) {
			$note = JText::_($note);
		}

		require_once realpath(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

		$items = MenusHelper::getMenuLinks();

		$options = array();

		foreach ($items as $menu) {
			$options[$menu->menutype] = $menu->title;
		}

		$theme->set('options', $options);
		$theme->set('note', $note);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('attributes', $attributes);
		$output = $theme->output('admin/html/settings/dropdown');

		return $output;
	}

	/**
	 * Renders a toggle button
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function toggle($name, $title, $desc = '', $attributes = '', $note = '', $wrapperAttributes = '')
	{
		$theme = EB::themes();

		if (empty($desc)) {
			$desc = $title . '_DESC';
		}
		
		if ($note) {
			$note = JText::_($note);
		}

		if (is_array($wrapperAttributes)) {
			$wrapperAttributes = implode(' ', $wrapperAttributes);
		}

		$theme->set('note', $note);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('attributes', $attributes);
		$theme->set('wrapperAttributes', $wrapperAttributes);

		$contents = $theme->output('admin/html/settings/toggle');

		return $contents;
	}

	/**
	 * Renders a small inputbox
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function smalltext($name, $title, $desc = '', $prefix = '')
	{
		$theme 	= EB::getTemplate();

		if (empty($desc)) {
			$desc 	= $title . '_DESC';
		}

		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);

		$contents = $theme->output('admin/html/settings.text.small');

		return $contents;
	}

	/**
	 * Renders a textbox for settings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function text($name, $title, $desc = '', $options = array(), $instructions = '', $class = '')
	{
		$theme = EB::themes();
		
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		$size = '';
		$postfix = '';
		$prefix = '';
		$attributes = '';
		$type = 'text';

		if (isset($options['type'])) {
			$type = $options['type'];
		}

		if (isset($options['attributes'])) {
			$attributes = $options['attributes'];
		}

		if (isset($options['postfix'])) {
			$postfix = $options['postfix'];
		}

		if (isset($options['prefix'])) {
			$prefix = $options['prefix'];
		}

		if (isset($options['size'])) {
			$size = $options['size'];
		}


		$theme->set('attributes', $attributes);
		$theme->set('type', $type);
		$theme->set('size', $size);
		$theme->set('class', $class);
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);
		$theme->set('postfix', $postfix);

		$contents = $theme->output('admin/html/settings/text');

		return $contents;
	}

	/**
	 * Renders a password input for settings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function password($name, $title, $desc = '', $options = array(), $instructions = '', $class = '')
	{
		$options['type'] = 'password';

		return $this->text($name, $title, $desc, $options, $instructions, $class);
	}

	/**
	 * Renders a textarea input
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function textarea($name, $title, $desc = '', $prefix = '', $instructions = '')
	{
		$theme 	= EB::getTemplate();

		if (empty($desc)) {
			$desc 	= $title . '_DESC';
		}
		
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);

		$contents 	= $theme->output('admin/html/settings.textarea');

		return $contents;
	}

	/**
	 * Renders a small inputbox
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function categories($name, $title, $desc = '', $prefix = '', $instructions = '')
	{
		$theme 	= EB::getTemplate();

		if (empty($desc)) {
			$desc 	= $title . '_DESC';
		}
		
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);

		$contents 	= $theme->output('admin/html/settings.categories');

		return $contents;
	}
}
