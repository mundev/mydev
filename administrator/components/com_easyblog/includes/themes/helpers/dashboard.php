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

class EasyBlogThemesHelperDashboard
{
	/**
	 * Renders the heading on the dashboard
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function heading($title, $icon, $action = false)
	{
		$title = JText::_($title);
		$info = EB::info();

		if (is_array($action)) {
			$action = (object) $action;

			if (isset($action->text)) {
				$action->text = JText::_($action->text);
			}

			if (!isset($action->icon)) {
				$action->icon = false;
			}
		}

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('icon', $icon);
		$theme->set('info', $info);
		$theme->set('action', $action);

		$output = $theme->output('site/dashboard/helpers/heading');

		return $output;
	}

	/**
	 * Render mini heading on the dashboard
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function miniHeading($title, $icon, $desc = null)
	{
		$title = EBString::strtoupper($title);

		if (!$desc) {
			$desc = JText::_($title . '_DESC');
		}

		$title = JText::_($title);

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('description', $desc);
		$theme->set('icon', $icon);

		$output = $theme->output('site/dashboard/helpers/miniheading');

		return $output;
	}

	/**
	 * Generates a checkbox in a table
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function checkbox($element, $value, $options = array())
	{
		$theme = EB::template();

		$disabled = false;

		if (isset($options['disabled']) && $options['disabled']) {
			$disabled = true;
		}

		$theme->set('element', $element);
		$theme->set('value', $value);
		$theme->set('disabled', $disabled);


		$output = $theme->output('site/dashboard/helpers/checkbox');

		return $output;
	}

	public static function action($title, $action, $type = 'dialog')
	{
		$title  = JText::_($title);
		$theme  = EB::template();

		$theme->set('type', $type);
		$theme->set('title', $title);
		$theme->set('action', $action);

		$output = $theme->output('site/dashboard/html/item.action');

		return $output;
	}

	/**
	 * Renders a filter dropdown
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function filters($state)
	{
		$theme = EB::themes();
		$theme->set('state', $state);
		$output = $theme->output('site/dashboard/helpers/filters');

		return $output;
	}

	/**
	 * Renders a filter dropdown
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function sort($label, $column, $ordering, $currentSort = '', $default = 'desc')
	{

		$default = ($default) ? $default : 'desc';

		$theme = EB::themes();
		$theme->set('label', $label);
		$theme->set('column', $column);
		$theme->set('ordering', $ordering);
		$theme->set('default', $default);
		$theme->set('currentSort', $currentSort);
		$output = $theme->output('site/dashboard/helpers/sort');

		return $output;
	}


	/**
	 * Renders a check all button
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function checkall($disabled = false)
	{
		$theme = EB::themes();
		$theme->set('disabled', $disabled);
		$output = $theme->output('site/dashboard/helpers/checkall');

		return $output;
	}

	/**
	 * Renders label form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function label($text, $id = null)
	{
		$key = EBString::strtoupper($text);
		$text = JText::_($key);

		$theme = EB::themes();
		$theme->set('id', $id);
		$theme->set('text', $text);

		$output = $theme->output('site/dashboard/helpers/label');

		return $output;
	}

	/**
	 * Render text form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function text($name, $value = '', $id = null, $options = array())
	{
		$class = 'form-control';
		$placeholder = '';
		$attributes = '';

		if (isset($options['attr']) && $options['attr']) {
			$attributes = $options['attr'];
		}

		if (isset($options['class']) && $options['class']) {
			$class = $options['class'];
		}

		if (isset($options['placeholder']) && $options['placeholder']) {
			$placeholder = JText::_($options['placeholder']);
		}

		$theme = EB::themes();
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('class', $class);
		$theme->set('placeholder', $placeholder);

		return $theme->output('site/dashboard/helpers/text');
	}
}
