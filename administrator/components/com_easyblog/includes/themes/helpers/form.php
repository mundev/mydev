<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogThemesHelperForm extends EasyBlogThemesHelperAbstract
{
	/**
	 * Renders the label for generic forms
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function label($text, $id, $helpTitle = '', $helpContent = '', $tooltip = true)
	{
		$key = EBString::strtoupper($text);
		$text = JText::_($key);

		if (!$helpTitle) {
			$helpTitle = $text;
		}

		if (!$helpContent) {
			$helpContent = JText::_($key . '_DESC');
		}

		$theme = EB::themes();
		$theme->set('id', $id);
		$theme->set('text', $text);
		$theme->set('helpTitle', $helpTitle);
		$theme->set('helpContent', $helpContent);
		$theme->set('tooltip', $tooltip);

		$output = $theme->output('admin/html/form/label');

		return $output;
	}

	/**
	 * Floating label with input form
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function floatinglabel($label, $name, $type = 'text', $value = '')
	{
		// This currently only supports textbox and password
		$supported = array('text', 'password');

		if (!in_array($type, $supported)) {
			return "Field type not supported";
		}

		$label = JText::_($label);
		$id = 'eb-' . str_ireplace(array('.'), '', $name);

		$theme = EB::themes();
		$theme->set('type', $type);
		$theme->set('value', $value);
		$theme->set('label', $label);
		$theme->set('name', $name);
		$theme->set('id', $id);

		$output = $theme->output('site/helpers/form/' . __FUNCTION__);

		return $output;
	}

	/**
	 * Renders a simple password input
	 *
	 * @since   5.2.0
	 * @access  public
	 */
	public function password($name, $value = '', $id = null, $options = array())
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

		return $theme->output('site/helpers/form/password');
	}

	/**
	 * Renders a simple text input
	 *
	 * @since   5.1
	 * @access  public
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

		return $theme->output('admin/html/form/text');
	}

	/**
	 * Renders a author browser form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function author($name, $value, $id = null, $attributes = array())
	{
		if (is_null($id)) {
			$id = $name;
		}

		$authorName = '';

		if ($value) {
			$user = EB::user($value);
			$authorName = $user->getName();
		}

		$attributes = implode(' ', $attributes);

		$theme = EB::themes();
		$theme->set('authorName', $authorName);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		return $theme->output('admin/html/form/author');
	}

	/**
	 * Renders a custom field group browser form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function fieldGroups($name, $value, $id = null, $attributes = array())
	{
		if (is_null($id)) {
			$id = $name;
		}

		$groupTitle = '';

		if ($value) {
			// Load the custom field group
			$group = EB::table('FieldGroup');
			$group->load($value);

			$groupTitle = $group->getTitle();
		}

		$attributes = implode(' ', $attributes);

		$theme = EB::themes();
		$theme->set('groupTitle', $groupTitle);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		return $theme->output('admin/html/form/fieldgroups');
	}

	/**
	 * Renders a team browser form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function team($name, $value, $id = null)
	{
		if (is_null($id)) {
			$id = $name;
		}

		$teamTitle = '';

		if ($value) {
			$team = EB::table('Teamblog');
			$team->load($value);
			$teamTitle = $team->title;
		}

		$theme = EB::themes();
		$theme->set('teamTitle', $teamTitle);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);

		return $theme->output('admin/html/form/team');
	}

	/**
	 * Renders a tag browser form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function tag($name, $value, $id = null)
	{
		if (is_null($id)) {
			$id = $name;
		}

		$title = '';

		if ($value) {
			$tag = EB::table('Tag');
			$tag->load($value);

			$title = JText::_($tag->title);
		}

		$theme = EB::themes();
		$theme->set('title', $title);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);

		return $theme->output('admin/html/form/tag');
	}

	/**
	 * Renders a category browser form
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function browseCategory($name, $value, $id = null)
	{
		if (is_null($id)) {
			$id = $name;
		}

		$categoryTitle = '';

		if ($value) {
			$category = EB::table('Category');
			$category->load($value);

			$categoryTitle = JText::_($category->title);
		}

		$theme = EB::themes();
		$theme->set('categoryTitle', $categoryTitle);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);

		return $theme->output('admin/html/form/category');
	}

	/**
	 * Renders a dropdown
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function dropdown($name, $selected ='', $values = array(), $options = array())
	{
		$class = 'form-control';
		$attributes = '';

		if (isset($options['attr']) && $options['attr']) {
			$attributes = $options['attr'];
		}

		if (isset($options['class']) && $options['class']) {
			$class = $options['class'];
		}

		$theme = EB::themes();
		$theme->set('values', $values);
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('class', $class);
		$theme->set('selected', $selected);

		return $theme->output('admin/html/form/dropdown');
	}

	/**
	 * Renders a colour picker input
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function colorpicker($name, $value = '', $revert = '')
	{
		static $script = null;

		$loadScript = false;

		if (is_null($script)) {
			$loadScript = true;
			$script = true;
		}

		JHTML::_('behavior.colorpicker');

		$theme = EB::themes();
		$theme->set('loadScript', $loadScript);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('revert', $revert);

		$output = $theme->output('admin/html/form/colorpicker');

		return $output;
	}

	/**
	 * Renders a calendar for settings
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function calendar($name, $value = '', $format = '%Y-%m-%d %H:%M:%S')
	{
		$theme = EB::template();

		$date  = EB::date($value);
		$value = $date->format($format);

		// Generate a uniqid
		$hash = strtolower($name);

		$theme->set('hash', $hash);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('format', $format);

		$output = $theme->output('admin/html/form.calendar');

		return $output;
	}

	/**
	 * Generates a on / off switch
	 *
	 * @since   5.1
	 * @access  public
	 */
	public static function toggler($name, $enabled = false, $id = '', $attributes = '')
	{
		if (is_array($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		if (!$id) {
			$id = $name;
		}

		$theme = EB::template();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('enabled', $enabled);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/html/form/toggler');

		return $output;
	}

	/**
	 * Retrieves the dropdown list for editors on the site
	 *
	 * @since   5.0
	 * @access  public
	 */
	public static function editors($element, $selected = null, $composer = false)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('element') . ' AS ' . $db->qn('value') . ',' . $db->qn('name') . ' AS ' . $db->qn('text');
		$query[] = 'FROM ' . $db->qn('#__extensions');
		$query[] = 'WHERE ' . $db->qn('folder') . '=' . $db->Quote('editors');
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('plugin');
		$query[] = 'AND ' . $db->qn('enabled') . '=' . $db->Quote(1);
		$query[] = 'ORDER BY ' . $db->qn('ordering') . ',' . $db->qn('name');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$editors = $db->loadObjectList();

		$lang = JFactory::getLanguage();

		// There are some cases where the editor selected is not exists in the database
		$selectedExists = false;

		for ($i = 0; $i < count($editors); $i++) {
			$editor = $editors[$i];
			$lang->load($editor->text . '.sys', JPATH_ADMINISTRATOR, null, false, false);
			$editor->text = JText::_($editor->text);

			if ($selected == $editor->value) {
				$selectedExists = true;
			}
		}

		// If editor not exist, we automatically select composer as default editor.
		if (!$selectedExists) {
			$selected = 'composer';
		}

		$theme = EB::template();
		$theme->set('composer', $composer);
		$theme->set('element', $element);
		$theme->set('selected', $selected);
		$theme->set('editors', $editors);
		$output = $theme->output('admin/html/form.editors');

		return $output;
	}

	/**
	 * Generates a html code for category selection.
	 *
	 * @access  public
	 * @param   int     $parentId   if this option spcified, it will list the parent and all its childs categories.
	 * @param   int     $userId     if this option specified, it only return categories created by this userId
	 * @param   string  $outType    The output type. Currently supported links and drop down selection
	 * @param   string  $eleName    The element name of this populated categeries provided the outType os dropdown selection.
	 * @param   string  $default    The default selected value. If given, it used at dropdown selection (auto select)
	 * @param   boolean $isWrite    Determine whether the categories list used in write new page or not.
	 * @param   boolean $isPublishedOnly    If this option is true, only published categories will fetched.
	 * @param   array   $exclusion  A list of excluded categories that it should not be including
	 */

	public function category($element, $id = '', $selected = '', $attributes = '', $parentId = null, $exclusion = array())
	{
		// Get the model
		$model = EB::model('Category');

		// Default to filter all categories
		$filter = 'all';

		if (!is_null($parentId)) {
			$filter = 'category';
		}

		// Get list of parent categories
		$categories = $model->getParentCategories($parentId, $filter, true, true, $exclusion);

		// Perform recursive operation to get the child items
		if (!empty($categories)) {

			foreach ($categories as $category) {

				$category->childs = null;

				self::buildNestedCategories($category);
			}
		}

		// Get the selected category
		$selected = $selected ? $selected : $model->getDefaultCategoryId();

		// Build the nested output for the category items
		$output = '';

		foreach ($categories as $category) {

			$selectedOutput = $selected == $category->id ? ' selected="selected"' : '';
			$output .= '<option value="' . $category->id . '"' . $selectedOutput . '>' . JText::_($category->title) . '</option>';

			self::generateNestedCategoriesOutput($category, $output, $selected);
		}

		// Now we need to build the select output
		$theme = EB::template();
		$theme->set('element', $element);
		$theme->set('attributes', $attributes);
		$theme->set('id', '');
		$theme->set('output', $output);

		$html = $theme->output('admin/html/form.category.select');

		return $html;
	}


	public static function buildNestedCategories($category, $exclusion = array(), $writeOnly = true, $publishedOnly = true)
	{
		$model = EB::model('Category');
		$categories = $model->getChildCategories($category->id, $publishedOnly, $writeOnly, $exclusion);

		// Get accessible categories
		// $accessibleCategories = EB::getAccessibleCategories($category->id);

		if (!empty($categories)) {

			foreach ($categories as &$row) {

				$row->childs = null;

				if (!self::buildNestedCategories($row)) {
					$category->childs[] = $row;
				}
			}
		}

		return false;
	}

	/**
	 * Generates a list of category options in a select list
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public static function generateNestedCategoriesOutput($category, &$html, $selected = 0, $depth = 0)
	{
		if (!is_array($category->childs)) {
			return false;
		}

		// Increment the depth
		$depth++;

		$prefix = '';

		for ($i = 0; $i < $depth; $i++) {
			$prefix .= '&nbsp;&nbsp;&nbsp;';
		}

		foreach ($category->childs as $child) {
			$selectedOutput = $selected == $child->id ? ' selected="selected"' : '';
			$html .= '<option value="' . $child->id . '"' . $selectedOutput . '>' . $prefix . '<sup>|_</sup>' . JText::_($child->title) . '</option>';

			// Try to build the nested items
			self::generateNestedCategoriesOutput($child, $html, $selected, $depth);
		}
	}

	/**
	 * Generates hidden input for form submission
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function action($task = '')
	{
		$theme = EB::themes();
		$token = JFactory::getSession()->getFormToken();

		$theme->set('token', $token);
		$theme->set('task', $task);
		$output	= $theme->output('admin/html/form/action');

		return $output;
	}

	public static function hidden($name = '', $value = '')
	{
		$theme = EB::template();

		$theme->set('name', $name);
		$theme->set('value', $value);
		$output = $theme->output('admin/html/form.hidden');

		return $output;
	}

	public static function token()
	{
		$theme = EB::template();
		$token = JFactory::getSession()->getFormToken();

		$theme->set('token', $token);
		$output  = $theme->output('admin/html/form.token');

		return $output;
	}
}
