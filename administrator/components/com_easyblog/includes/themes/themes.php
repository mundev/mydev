<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

// Include our abstract class
require_once(__DIR__ . '/helpers/abstract.php');

class EasyBlogThemes extends EasyBlog
{
	/**
	 * Stores the template variables
	 * @var	Array
	 */
	public $vars = array();

	/**
	 * Determines if this is for the dashboard
	 *
	 * @deprecated 4.0
	 * @var	bool
	 */
	public $dashboard = false;

	/**
	 * Determines the user's selected theme.
	 *
	 * @deprecated 4.0
	 * @var	bool
	 */
	public $user_theme = '';

	/**
	 * Determines the category theme
	 * @var string
	 */
	public $categoryTheme = '';

	/**
	 * Theme params
	 * @var	bool
	 */
	public $params = null;

	/**
	 * Theme params
	 * @var	bool
	 */
	public $entryParams = null;

	/**
	 * Determines if this view is for the adminv iew
	 *
	 * @param	object
	 */
	public $admin = false;

	/**
	 * Holds the current view object
	 *
	 * @param	object
	 */
	public $view = null;

	public function __construct($overrideTheme = null, $options = array())
	{
		parent::__construct();

		// Determine if this is an admin location
		if (isset($options['admin']) && $options['admin']) {
			$this->admin = true;
		}

		// Determine the configured theme
		$theme = $this->config->get('layout_theme', $overrideTheme);

		// If a view is provided into the theme, the theme files could call methods from a view
		if (isset($options['view']) && is_object($options['view'])) {
			$this->view = $options['view'];
		}

		$this->theme = $theme;

		// var_dump($this->theme);

		$obj = new stdClass();
		$obj->config = EB::config();
		$obj->my = JFactory::getUser();
		$obj->admin = EB::isSiteAdmin();
		$obj->profile = EB::user();

		// lets check if current page is a blogger standalone page or not. if yes, get the blogger's theme
		$bloggerTheme = EB::getBloggerTheme();

		if ($bloggerTheme) {
			$this->theme = $bloggerTheme;
		}

		// If it's development mode, allow user to invoke in the url to change theme.
		$environment = $obj->config->get('main_environment');

		if ($environment == 'development') {
			$invokeTheme = $this->input->get('theme', '', 'word');

			if ($invokeTheme) {
				$this->theme = $invokeTheme;
			}
		}

		// If this is entry view, or category view, we need to respect the theme's category
		$this->params = new JRegistry();

		$this->menu = $this->app->getMenu()->getActive();
		$segments = array('view' => '');

		if ($this->menu) {
			$segments = $this->menu->query;
		}

		// Check the view
		$view = $this->app->input->get('view');
		$layout = $this->input->get('layout', '', 'cmd');

		// Check the id
		$id = $this->input->get('id', 0, 'int');

		if ($view == 'categories' && $layout != 'listings') {

			if (!$id && !$layout) {
				// check if current active menu item also a categories page.
				if ($segments['view'] == 'categories' && !isset($segments['layout'])) {
					// this is all categories page. lets just it the active menu params
					$this->params = self::formatMenuParams('categories',$this->menu->params);
				} else {
					$this->params = EB::getMenuParams($id, 'categories');
				}

			} else {
				// could be category edit form
				$this->params = EB::getMenuParams($id, 'categories');
			}
		}

		if ($view == 'categories' && $layout == 'listings') {
			$this->params = EB::getMenuParams($id, 'category');
		}

		if ($view == 'tags' && $layout == 'tag') {
			$this->params = EB::getMenuParams($id, 'tag');
		}

		if ($view == 'tags' && !$layout) {
			// $this->params = EB::getMenuParams($id);
		}

		if ($view == 'blogger' && $layout == 'listings') {
			$this->params = EB::getMenuParams($id, 'blogger', true);
		}

		if ($view == 'blogger' && $layout != 'listings') {

			if (!$id && !$layout) {
				// check if current active menu item also a blogger page.
				if ($segments['view'] == 'blogger' && !isset($segments['layout'])) {
					// this is all blogger page. lets just it the active menu params
					$this->params = self::formatMenuParams('bloggers', $this->menu->params);
				} else {
					$this->params = EB::getMenuParams($id, 'bloggers', true);
				}

			} else {
				$this->params = EB::getMenuParams($id, 'bloggers', true);
			}
		}

		// If there is an active menu, try to get the menu parameters.
		if ($this->menu && !in_array($view, array('categories', 'tags', 'blogger'))) {

			// Get the params prefix
			$prefix = isset($options['paramsPrefix']) ? $options['paramsPrefix'] : '';

			// Set the current parameters.
			if ($prefix) {
				$model = EB::model('Menu');
				$this->params = $model->getCustomMenuParams($this->menu->id, $this->menu->params, $prefix);
			} else {
				$this->params = self::formatMenuParams($view, $this->menu->params);
			}
		}

		// For all tags menu
		if ($this->menu && $view == 'tags' && !$layout) {

			// Get the params prefix
			$prefix = isset($options['paramsPrefix']) ? $options['paramsPrefix'] : '';

			$model = EB::model('Menu');
			$this->params = $model->getCustomMenuParams($this->menu->id, $this->menu->params, $prefix);
		}

		if ($this->params->get('post_image', null) == null) {
			// if this happen, we know the whatever menu item is created prior to 5.0. Lets just get the default listing options from config.
			$defaultListingParams = EB::getMenuParams('0', 'listing');

			$defaultListingParams = $defaultListingParams->toArray();

			if ($defaultListingParams) {
				foreach($defaultListingParams as $key => $val) {
					$this->params->set($key, $val);
				}
			}
		}

		// We will just set it here from the menu when this class first get instantiate.
		// The corresponding view will have to do their own assignment if the view's templates need to access this entryParams
		$this->entryParams = $this->params;

		//is blogger mode flag
		$obj->isBloggerMode	= EBR::isBloggerMode();

		$this->my = $obj->my;
		$this->profile = $obj->profile;

		// Assign the acl
		$this->acl = EB::acl();
	}

	/**
	 * Utility function to format params from menu on 'inherit' setting.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function formatMenuParams($type, $params)
	{
		$config = EB::config();
		$arrParams = $params->toArray();

		foreach ($arrParams as $key => $value) {
			if ($value == '-1') {
				$params->set($key, $config->get($type . '_' . $key));
			}
		}

		return $params;
	}


	/**
	 * Allows caller to set a custom theme
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setCategoryTheme($theme)
	{
		$this->categoryTheme = $theme;
	}

	/**
	 * Resolves a given namespace to the appropriate path
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function resolve($namespace='', $extension='php', $checkOverridden = true)
	{
		$parts     = explode('/', $namespace);
		$location  = $parts[0];
		$path      = '';
		$extension = '.' . $extension;

		unset($parts[0]);

		// For admin theme files
		if ($location=='admin') {
			$defaultPath = JPATH_ADMINISTRATOR . '/components/com_easyblog/themes/default/' . implode('/', $parts);

			// If there is a template override on the default joomla template, we should use that instead.
			$defaultJoomlaTemplate = $this->app->getTemplate();
			$path = JPATH_ADMINISTRATOR . '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/' . implode('/', $parts);
			$exists = JFile::exists($path . $extension);

			if ($exists) {
				return $path;
			}

			return $defaultPath;
		}

		// For site theme files
		if ($location=='site') {

			$defaultJoomlaTemplate = EB::getCurrentTemplate();

			// Implode the parts back to form the namespace
			$namespace = implode('/', $parts);

			if ($checkOverridden) {

				// Category Theme
				if (!empty($this->categoryTheme)) {

					$path   = JPATH_ROOT . '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/themes/' . $this->categoryTheme . '/' . $namespace;
					$exists = JFile::exists($path . $extension);

					if ($exists) {
						return $path;
					}
				}

				// If there is a template override on the default joomla template, we should use that instead.
				$path = JPATH_ROOT . '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/' . $namespace;
				$exists = JFile::exists($path . $extension);

				if ($exists) {
					return $path;
				}
			}

			// If there are no overrides, we should just use the default theme in EasyBlog
			$path = EBLOG_THEMES . '/' . $this->theme . '/' . $namespace;
			$exists = JFile::exists($path . $extension);

			if ($exists) {
				return $path;
			}

			// Base Theme
			// We no longer inherit from other themes. All themes will fallback to the wireframe theme by default.
			$path = EBLOG_THEMES . '/wireframe/' . $namespace;
		}

		return $path;
	}

	/**
	 * Retrieves the path to the current theme.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPath()
	{
		$theme 	= (string) trim(strtolower($this->theme));

		return EBLOG_THEMES . '/' . $theme;
	}


	/**
	 * Renders module in a template
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderModule($position, $attributes = array(), $content = null)
	{
		$doc = JFactory::getDocument();
		$renderer = $doc->loadRenderer('module');

		$buffer = '';
		$modules = JModuleHelper::getModules($position);

		// Use a standard module style if no style is provided
		if (!isset($attributes['style'])) {
			$attributes['style'] = 'xhtml';
		}

		foreach ($modules as $module) {
			$theme = EB::template();

			$theme->set('position', $position);
			$theme->set('output', $renderer->render($module, $attributes, $content));

			$buffer .= $theme->output('site/modules/item');
		}

		return $buffer;
	}

	/**
	 * Determines if this is from an iphone
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isIphone()
	{
		static $iphone = null;

		if (is_null($iphone)) {
			$iphone = EB::responsive()->isIphone();
		}

		return $iphone;
	}

	/**
	 * Determines if this is a mobile layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isMobile()
	{
		static $responsive = null;

		if (is_null($responsive)) {
			$responsive = EB::responsive()->isMobile();
		}

		return $responsive;
	}

	/**
	 * Determines if this is a ipad layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isIpad()
	{
		static $responsive = null;

		if (is_null($responsive)) {
			$responsive = EB::responsive()->isIpad();
		}

		return $responsive;
	}

	/**
	 * Determines if this is a tablet layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isTablet()
	{
		static $responsive = null;

		if (is_null($responsive)) {
			$responsive = EB::responsive()->isTablet();
		}

		return $responsive;
	}

	/**
	 * Retrieves the document direction. Whether this is rtl or ltr
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getDirection()
	{
		$document	= JFactory::getDocument();
		return $document->getDirection();
	}

	public function getNouns($text , $count , $includeCount = false )
	{
		return EB::string()->getNoun( $text , $count , $includeCount );
	}

	public function getParam($key, $default = null)
	{
		return $this->params->get( $key , $default );
	}

	/**
	 * Retrieves the themes parameters
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getThemeParams()
	{
		static $params = array();

		if (!isset($params[$this->theme])) {
			$model = EB::model('Themes');
			$params[$this->theme] = $model->getThemeParams($this->theme);
		}

		return $params[$this->theme];
	}

	/**
	 * Formats a date.
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function formatDate($format, $dateString)
	{
		$date 	= EB::call('Date', 'dateWithOffSet', array($dateString));

		return $date->format($format);
	}

	/**
	 * Template helper
	 *
	 * @since	5.1.0
	 * @access	public
	 */
	public function html($namespace)
	{
		static $language = false;

		// Load language strings from back end.
		if (!$language) {
			EB::loadLanguages();

			$language = true;
		}

		$helper = explode('.', $namespace);
		$helperName = $helper[0];
		$methodName	= $helper[1];

		$file = __DIR__ . '/helpers/' . strtolower($helperName) . '.php';

		// Remove the first 2 arguments from the args.
		$args = func_get_args();
		$args = array_splice($args, 1);

		include_once($file);

		$class = 'EasyBlogThemesHelper' . ucfirst($helperName);
		$obj = new $class();

		if (!method_exists($obj, $methodName)) {
			return false;
		}

		return call_user_func_array(array($obj, $methodName), $args);
	}

	/**
	 * Sets a variable on the template
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	/**
	 * Retrieves the theme's name.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getName()
	{
		return $this->theme;
	}

	/**
	 * New method to display contents from template files
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function output($namespace, $vars=array(), $extension='php')
	{
		$path = $this->resolve($namespace, $extension);
		$extension = '.' . $extension;

		// Extract template variables
		if (!empty($vars)) {
			extract($vars);
		}

		if (isset($this->vars)) {
			extract($this->vars);
		}

		$templateFile = $path . $extension;
		$templateContent = '';

		ob_start();
			include($templateFile);
			$templateContent = ob_get_contents();
		ob_end_clean();

		// Embed script within template
		$scriptFile = $path . '.js';

		$scriptFileExists = JFile::exists($scriptFile);

		if (!$scriptFileExists) {
			$tmpPath = $this->resolve($namespace, 'php', false);
			$scriptFile = $tmpPath . '.js';
		}

		$scriptFileExists = JFile::exists($scriptFile);

		if ($scriptFileExists) {

			if ($namespace == 'site/blogs/code') {
				return;
			}

			ob_start();
				echo '<script type="text/javascript">';
				include($scriptFile);
				echo '</script>';
				$scriptContent = ob_get_contents();
			ob_end_clean();

			// Add to collection of scripts
			if ($this->doc->getType() == 'html') {
				EB::scripts()->add($scriptContent);
			} else {

				// Append script to template content
				// if we're not on html document (ajax).
				$templateContent .= $scriptContent;
			}
		}

		return $templateContent;
	}

	/**
	 * Retrieves the images path for the current template
	 *
	 * @since	4.0
	 * @access	public
	 * @return	string	The absolute URI to the images path
	 */
	public function getPathUri($location)
	{
		if ($this->admin) {

			$path = rtrim(JURI::root(), '/') . '/administrator/components/com_easyblog/themes/default/' . ltrim($location, '/');

			return $path;
		}
	}

	public function __call($method, $args)
	{
		if (is_null($this->view)) {
			return false;
		}

		if (!method_exists($this->view, $method)) {
			return false;
		}

		return call_user_func_array(array($this->view, $method), $args);
	}

	/**
	 * Escapes a string
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function escape($val)
	{
		return EB::string()->escape($val);
	}
}
