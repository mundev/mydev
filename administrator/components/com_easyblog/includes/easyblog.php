<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/constants.php');
require_once(__DIR__ . '/utils.php');

if (!defined('EASYBLOG_COMPONENT_CLI')) {
	require_once(__DIR__ . '/compatibility.php');
	require_once(__DIR__ . '/router.php');
	require_once(EBLOG_ROOT . '/router.php');
}

class EB
{
	public static $headersLoaded = array();

	/**
	 * Initializes EasyBlog's javascript framework
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function init($section = 'site')
	{
		// Determines if we should compile the javascripts on the site
		$app = JFactory::getApplication();
		$input = $app->input;
		$compile = $input->get('compile', false, 'bool');

		if (EB::isSiteAdmin() && $compile) {

			// Determines if we need to minify the js
			$minify = $input->get('minify', false, 'bool');

			// Get section if not default one
			$section = $input->get('section', $section, 'cmd');

			// Get the compiler
			$compiler = EB::compiler();
			$result = array();

			// Compile with jquery.easyblog.js
			$result['standard'] = $compiler->compile($section, $minify);

			// Compile with jquery.js
			$result['basic'] = $compiler->compile($section, $minify, false);

			// Since excluded files are running on their own, we would need to minify them so that it
			// runs on the compressed version rather than the uncompressed version
			$compiler->compileExcludedFiles();

			if ($result !== false) {
				header('Content-type: text/x-json; UTF-8');
				echo json_encode($result);
				exit;
			}
		}

		// If this is a non html view, skip this altogether
		$doc = JFactory::getDocument();

		if ($doc->getType() !=='html') {
			return;
		}

		if (!isset(self::$headersLoaded[$section])) {

			// Attach scripts on the page
			$scripts = EB::scripts($section);
			$scripts->attach();

			self::$headersLoaded[$section] = true;
		}

		return self::$headersLoaded[$section];
	}

	/**
	 * Custom implementation of EasySocial integrations
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function easysocial()
	{
		static $easysocial = null;

		if (is_null($easysocial)) {
			self::load(__FUNCTION__);

			$easysocial = new EasyBlogEasySocial();
		}

		return $easysocial;
	}

	/**
	 * Custom implementation of EasyDiscuss integrations
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function easydiscuss()
	{
		static $easydiscuss = null;

		if (is_null($easydiscuss)) {
			self::load(__FUNCTION__);

			$easydiscuss = new EasyBlogEasyDiscuss();
		}

		return $easydiscuss;
	}

	/**
	 * If the current user is a super admin, allow them to change the environment via the query string
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function checkEnvironment()
	{
		if (!EB::isSiteAdmin()) {
			return;
		}

		$app = JFactory::getApplication();
		$environment = $app->input->get('eb_env', '', 'word');
		$allowed = array('production', 'development');

		// Nothing has changed
		if (!$environment || !in_array($environment, $allowed)) {
			return;
		}

		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/defaults/configuration.ini';
		$contents = JFile::read($file);
		$contents = preg_replace('/main_environment=(.*)/', 'main_environment=' . $environment, $contents);

		JFile::write($file, $contents);

		// We also need to update the database value
		$config = EB::table('Configs');
		$config->load(array('name' => 'config'));

		$params = $config->getParams();
		$params->set('main_environment', $environment);

		$config->params = $params->toString();
		$config->store();

		EB::info()->set('Updated system environment to <b>' . $environment . '</b> mode', 'success');
		return $app->redirect('index.php?option=com_easyblog');
	}

	/**
	 * Retrieves the cdn url for the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getCdnUrl()
	{
		$config = EB::config();
		$url = $config->get('cdn_url');

		if (!defined('EASYBLOG_COMPONENT_CLI')) {
			$app = JFactory::getApplication();

			// We do not want to render cdn urls for admins
			if ($app->isAdmin()) {
				return false;
			}
		}

		if (!$url) {
			return false;
		}

		// Ensure that the url contains http:// or https://
		if (stristr($url, 'http://') === false && stristr($url, 'https://') === false) {
			$url = '//' . $url;
		}

		return $url;
	}

	/**
	 * Load EasyBlog's ACL
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function acl($userId = '')
	{
		static $acl = array();

		if (!$userId) {
			$userId = JFactory::getuser()->id;
		}

		if (!isset($acl[$userId])) {

			require_once(dirname(__FILE__) . '/acl/acl.php');

			$acl[$userId] = EasyBlogAcl::getRuleSet($userId);
		}

		return $acl[$userId];
	}

	/**
	 * Simple way to minify css codes
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function minifyCSS($css)
	{
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		$css = str_replace(': ', ':', $css);
		$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);

		return $css;
	}

	/**
	 * Proxy for media manager
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function mediamanager($source = EBLOG_MEDIA_SOURCE_LOCAL)
	{
		require_once(dirname(__FILE__) . '/mediamanager/mediamanager.php');

		$media = new EBMM($source);

		return $media;
	}

	/**
	 * Creates a new stylesheet instance
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function stylesheet($location, $name = null, $useOverride = false)
	{
		require_once(__DIR__ . '/stylesheet/stylesheet.php');

		if ($location == 'site' && !defined('EASYBLOG_COMPONENT_CLI')) {

			// Lets overrite the theme based on blogger theme
			$bloggerTheme = EB::getBloggerTheme();

			if ($bloggerTheme) {
				$name = $bloggerTheme;
			}

		}

		$stylesheet = new EasyBlogStylesheet($location, $name, $useOverride);

		return $stylesheet;
	}

	/**
	 * Creates a new instance of the exception library.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function exception($message='', $type = EASYBLOG_MSG_ERROR, $silent = false, $customErrorCode = null)
	{
		require_once(dirname(__FILE__) . '/exception/exception.php');

		$exception = new EasyBlogException($message, $type, $silent, $customErrorCode);

		return $exception;
	}

	/**
	 * Determines if the current URL is on blogger mode
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function isBloggerMode()
	{
		static $itemIds	= null;

		$app = JFactory::getApplication();

		$itemId = $app->input->get('Itemid', 0, 'int');

		if (empty($itemId)) {
			return false;
		}

		if (!isset($itemIds[$itemId])) {
			$id = false;

			$menu = $app->getMenu();
			$params = $menu->getParams($itemId);

			if ($params->get('standalone_blog', false)) {
				$id = EBR::getBloggerIdFromMenu($itemId);
			}

			$itemIds[$itemId]   = $id;
		}

		return $itemIds[$itemId];
	}

	/**
	 * Retrieve the current language tag set
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getLanguageTag()
	{
		static $tag = false;

		if (!$tag) {
			$tag = JFactory::getLanguage()->getTag();
		}

		return $tag;
	}

	/**
	 * Retrieve the current language tag set
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getMomentLanguage()
	{
		static $tag = false;

		if (!$tag) {
			$tag = JFactory::getLanguage()->getTag();

			if ($tag == 'zh-TW') {
				$tag = 'zh-tw';
			} else {
				$tag = explode('-', $tag);

				$tag = $tag[0];

				if ($tag == 'en') {
					$tag = 'en-gb';
				}
			}

		}

		return $tag;
	}

	/**
	 * Determines if the site has multi lingual capability
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function isMultiLingual()
	{
		$enabled = JFactory::getApplication()->getLanguageFilter();

		return $enabled;
	}


	/**
	 * Proxy for post library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function reactions(EasyBlogPost $post)
	{
		require_once(__DIR__ . '/reactions/reactions.php');

		$lib = new EasyBlogReactions($post);

		return $lib;
	}

	/**
	 * Render the modules library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function modules($module)
	{
		require_once(__DIR__ . '/modules/modules.php');

		$lib = new EasyBlogModules($module);
		return $lib;
	}

	/**
	 * Render the scripts library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function scripts($location = 'site')
	{
		require_once(__DIR__ . '/scripts/scripts.php');

		$lib = new EasyBlogScripts($location);
		return $lib;
	}

	/**
	 * Get's the database object.
	 *
	 * @since	3.7
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function db()
	{
		static $db = null;

		if (!$db) {
			$db = new EasyBlogDbJoomla();
		}

		return $db;
	}

	/**
	 * Loads the date helper object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function date($current = '', $offset = null)
	{
		require_once(dirname(__FILE__) . '/date/date.php');

		$date = new EasyBlogDate($current, $offset);

		return $date;
	}

	/**
	 * Loads the colors helper object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function colors($color)
	{
		self::load('colors');

		$colors = new EasyBlogColors($color);

		return $colors;
	}


	/**
	 * Load's EasyBlog's settings object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function config()
	{
		static $config	= null;

		if (is_null($config)) {

			// Load up default configuration file
			$file = EBLOG_ADMIN_ROOT . '/defaults/configuration.ini';
			$raw = JFile::read($file);

			$registry = new JRegistry($raw);

			if (!defined('EASYBLOG_COMPONENT_CLI')) {
				// Get config stored in db
				$table = EB::table('Configs');
				$table->load('config');

				// Load the stored config as a registry
				$stored	= new JRegistry($table->params);

				$registry->merge($stored);

				if (!$stored->get('main_blocked_words')) {
					$registry->set('main_blocked_words', '');
				}

				// Get any config from current active menu item and override it
				$menu = JFactory::getApplication()->getMenu()->getActive();

				if ($menu) {
					$params = $menu->params;
					$properties = $params->toArray();

					foreach ($properties as $key => $val) {

						// Get the prefix of the params
						$subKey = explode('_', $key);

						// anything start with ebconfig_ is consider as global config.
						// eg : ebconfig_main_title
						if ($subKey[0] == 'ebconfig') {

							// Value is not inherited from settings
							if ($val != '-1') {

								// Remove the prefix
								array_shift($subKey);

								// Glue back the param name
								$name = implode('_', $subKey);

								// Re-assign the override value to the global config
								$registry->set($name, $val);
							}
						}
					}
				}
			}

			$config = $registry;
		}

		return $config;
	}

	/**
	 * Renders the info object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function info()
	{
		static $instance = null;

		require_once(dirname(__FILE__) . '/info/info.php');

		if (is_null($instance)) {

			$info = new EasyBlogInfo();

			$instance = $info;
		}

		return $instance;
	}

	/**
	 * Renders Joomla's configuration object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function jconfig()
	{
		static $config = null;

		if (is_null($config)) {
			$config = JFactory::getConfig();
		}

		return $config;
	}

	/**
	 * Use @jconfig instead
	 *
	 * @deprecated	5.1
	 */
	public static function getJConfig()
	{
		$config = JFactory::getConfig();

		return $config;
	}

	/**
	 * Returns a new registry object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function registry($contents = '', $isFile = false)
	{
		$registry = new JRegistry($contents, $isFile);

		return $registry;
	}

	/**
	 * Method to retrieve a registry object
	 *
	 * @deprecated	4.0
	 * @access	public
	 */
	public static function getRegistry($contents = '', $isFile = false)
	{
		return EB::registry($contents, $isFile);
	}

	/**
	 * Retrieves the token
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function token()
	{
		$session = JFactory::getSession();
		$token = $session->getFormToken();

		return $token;
	}

	/**
	 * Deprecated. Use EB::token() instead.
	 *
	 * @deprecated	5.1
	 */
	public static function getToken()
	{
		return EB::token();
	}

	/**
	 * Loads a library dynamically
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function __callStatic($method, $arguments)
	{
		self::load($method);

		$class = 'EasyBlog' . ucfirst($method);

		$obj = new $class($arguments);

		return $obj;
	}

	/**
	 * Single point of entry for static calls.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function call($className , $method , $args = array() )
	{
		// We always want lowercased items.
		$item			= strtolower($className);
		$obj			= false;

		$path			= dirname(__FILE__) . '/' . $item . '/' . $item . '.php';

		require_once( $path );

		$class 	= 'EasyBlog' . ucfirst( $className );

		if (!class_exists($class)) {
			return false;
		}

		if (!method_exists($class, $method)) {
			return false;
		}

		return call_user_func_array(array($class, $method), $args);
	}

	/**
	 * Retrieves the blog image object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function blogimage($path, $uri)
	{
		require_once(dirname(__FILE__) . '/blogimage/blogimage.php');

		$image = new EasyBlogBlogImage($path, $uri);

		return $image;
	}

	/**
	 * Retrieves the category library
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function category()
	{
		require_once(dirname(__FILE__) . '/category/category.php');

		$category 	= new EasyBlogCategory();

		return $category;
	}

	/**
	 * Retrieve specific helper objects.
	 *
	 * @since	4.0
	 * @access	public
	 **/
	public static function helper($helper)
	{
		static $obj	= array();

		if (!isset($obj[$helper])) {

			$file = dirname(__FILE__) . '/' . strtolower($helper) . '/' . strtolower($helper) . '.php';

			require_once($file);
			$class = 'EasyBlog' . ucfirst($helper);

			$obj[$helper] = new $class();
		}

		return $obj[$helper];
	}

	/**
	 * Retrieve the limit defined for each views settings given from specific key
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function getViewLimit($name = 'limit', $view = 'listing')
	{
		$app = JFactory::getApplication();
		$config = EB::config();

		$active = $app->getMenu()->getActive();
		$limit = -1;

		// Check if menu item is exists
		if (is_object($active)) {
			$limit = $active->params->get($name, '-1');
		}

		// Inherit from default view settings
		if ($limit == '-1') {
			$key = $view . '_' . $name;
			$limit = $config->get($key, '-2');
		}

		// Inherit from EB global settings
		if ($limit == '-2') {
			$key = 'layout_listlength';
			$limit = $config->get($key);
		}

		// Inherit from Joomla itself
		if ($limit == '-3' || !$limit) {
			$limit = EB::jconfig()->get('list_limit');
		}

		return $limit;
	}

	/**
	 * Retrieve the limit defined in the settings given the specific key
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getLimit($key = 'listlength')
	{
		$app = JFactory::getApplication();

		// Get the default limit
		$default = EB::jconfig()->get('list_limit');

		if ($app->isAdmin()) {
			return $default;
		}

		// Get the current active menu
		$active = $app->getMenu()->getActive();
		$limit  = -2;

		if (is_object($active)) {
			$limit = $active->params->get('limit', '-2');
		}

		// if menu did not specify the limit, then we use easyblog setting.
		if ($limit == '-2') {

			// Use default configurations.
			$config = EB::config();

			// @rule: For compatibility between older versions
			if ($key == 'listlength') {
				$key = 'layout_listlength';
			} else {
				$key = 'layout_pagination_' . $key;
			}

			$limit = $config->get($key);
		}

		// Revert to joomla's pagination if configured to inherit from Joomla
		if( $limit == '0' || $limit == '-1' || $limit == '-2') {

			return $default;
		}

		return $limit;
	}

	/**
	 * Retrieve specific helper objects. Use @helper instead
	 *
	 * @deprecated 	4.0
	 **/
	public static function getHelper($helper)
	{
		return self::helper($helper);
	}

	/**
	 * Pagination for EasyBlog
	 *
	 * @since	1.2
	 * @access	public
	 */
	public static function pagination($total, $limitstart, $limit, $prefix = '')
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		$signature = serialize(array($total, $limitstart, $limit, $prefix));

		if (empty($instances[$signature])) {

			require_once(dirname(__FILE__) . '/pagination/pagination.php');

			$pagination	= new EasyBlogPagination($total, $limitstart, $limit, $prefix);

			$instances[$signature] = &$pagination;
		}

		return $instances[$signature];
	}

	/**
	 * Retrieves the default placeholder image
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getPlaceholderImage($protocol = false)
	{
		static $image = null;

		if (is_null($image)) {

			$defaultJoomlaTemplate = EB::getCurrentTemplate();

			$file = JPATH_ROOT . '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/images/placeholder.png';

			// Default placeholder image.
			$default = rtrim(JURI::root(), '/') . '/components/com_easyblog/themes/wireframe/images/placeholder-image.png';

			if (JFile::exists($file)) {
				$default = rtrim(JURI::root(), '/') . '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/images/placeholder.png';
			}

			$image = $default;
		}

		return $image;
	}

	/**
	 * Retrieves local version
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getLocalParser()
	{
		static $parser = null;

		if (is_null($parser)) {
			$contents = JFile::read(JPATH_ADMINISTRATOR . '/components/com_easyblog/easyblog.xml');
			$parser = EB::getXml($contents, false);
		}

		return $parser;
	}

	/**
	 * Retrieves the installed version of EasyBlog
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getLocalVersion()
	{
		static $version = null;

		if (is_null($version)) {
			$parser	= EB::getLocalParser();

			if (!$parser) {
				$version = false;
				return $version;
			}

			$version = (string) $parser->version;
		}

		return $version;
	}

	/**
	 * Method to retrieve a table object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function table($name)
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/tables/table.php');

		$table = EasyBlogTable::getInstance($name);

		return $table;
	}


	/**
	 * Method to retrieve a EasyBlogUser object
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function user( $ids = null , $debug = false )
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/user/user.php';
		include_once($path);

		return EasyBlogUser::factory($ids, $debug);
	}


	/**
	 * Load the request library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function request()
	{
		require_once(dirname(__FILE__) . '/request/request.php');

		$request 	= new EasyBlogRequest();

		return $request;
	}

	/**
	 * Method to retrieve a model
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function model($name)
	{
		static $models = array();

		$index = $name;

		if (!isset($models[$index])) {

			$file = strtolower($name);
			$path = JPATH_ROOT . '/administrator/components/com_easyblog/models/' . $file . '.php';

			require_once($path);

			$class = 'EasyBlogModel' . ucfirst($name);

			$obj = new $class();
			$models[$index] = $obj;
		}

		return $models[$index];
	}

	/**
	 * Deprecated. Use EB::model
	 *
	 * @deprecated	4.0
	 */
	public static function getModel($name)
	{
		return EB::model($name);
	}

	/**
	 * Deprecated. Use EB::config
	 *
	 * @deprecated 	4.0
	 */
	public static function getConfig()
	{
		return EB::config();
	}

	/**
	 * Determines if the user is logged into the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function isLoggedIn()
	{
		$my = JFactory::getUser();

		return !$my->guest;
	}

	/**
	 * Determines if want to display the site name
	 *
	 * @since	5.0.36
	 * @access	public
	 */
	public static function showSiteName()
	{
		$jConfig = EB::jConfig();

		// Get site name
		$websiteName = $jConfig->get('sitename');

		return $websiteName;
	}

	/**
	 * Determines if the user is a super admin on the site.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isSiteAdmin($id = null)
	{
		static $items = array();

		$user = JFactory::getUser($id);

		if (!isset($items[$user->id])) {
			$items[$user->id] = $user->authorise('core.admin');
		}

		return $items[$user->id] ? true : false;
	}

	/**
	 * Determines if the user is a team admin
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isTeamAdmin()
	{
		static $admins = array();

		$my	= JFactory::getUser();

		if ($my->guest) {
			return false;
		}

		if (EB::isSiteAdmin()) {
			return true;
		}

		if (!isset($admins[$my->id])) {

			$model = EB::model('TeamBlogs');
			$admins[$my->id] = $model->checkIsTeamAdmin($my->id);
		}

		return $admins[$my->id];
	}

	/**
	 * Use EB::gallery()->removeGalleryCodes($text) instead
	 *
	 * @deprecated	5.1
	 */
	public static function removeGallery($text)
	{
		return EB::gallery()->removeGalleryCodes($text);
	}

	/**
	 * Verifies the password for a post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function verifyBlogPassword($crypt, $id)
	{
		if (!empty($crypt) && !empty($id)) {
			$session = JFactory::getSession();
			$password = $session->get('PROTECTEDBLOG_'.$id, '', 'EASYBLOG');

			if ($crypt == $password) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Formats microblog posts. Use EB::quickpost()->getAdapter('source')->format($blog);
	 *
	 * @deprecated	4.0
	 */
	public static function formatMicroblog(&$row)
	{
		$adapter = EB::quickpost()->getAdapter($row->posttype);

		if ($adapter === false) {
			return;
		}

		$adapter->format($row);
	}

	/**
	 * This method searches for built in tags and strips them off. This should only be used
	 * when you are trying to output some data that doesn't contain html tags.
	 */
	public static function stripEmbedTags( $content )
	{
		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$content	= str_ireplace( '&quot;' , '"' , $content );

		$pattern	= array('/\{video:.*?\}/',
							'/\{"video":.*?\}/',
							'/\[embed=.*?\].*?\[\/embed\]/'
							);

		$replace    = array('','','');


		return preg_replace( $pattern , $replace , $content );
	}

	/**
	 * Requires the user to be logged in
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function requireLogin()
	{
		$my = JFactory::getUser();
		$app = JFactory::getApplication();

		if ($my->guest || $my->block) {

			$config = EB::config();
			$provider = $config->get('main_login_provider');
			
			// Get the current URI which you trying to access
			$currentUri = EBR::getCurrentURI();
			$returnURL = '?return=' . base64_encode($currentUri);

			$url = EB::_('index.php?option=com_easyblog&view=login', false) . $returnURL;

			if ($provider == 'easysocial' && EB::easysocial()->exists()) {
				$url = ESR::login(array(), false) . $returnURL;
			}

			return $app->redirect($url);
		}
	}

	/**
	 * Trigger Joomla events
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function triggerEvent( $event , &$row , &$params , $limitstart )
	{
		$dispatcher = EB::dispatcher();
		$events = array(
							'easyblog.prepareContent'	=> 'onEasyBlogPrepareContent',
							'easyblog.beforeSave'		=> 'onBeforeEasyBlogSave',
							'easyblog.commentCount'		=> 'onGetCommentCount',
							'prepareContent'			=> 'onContentPrepare',
							'afterDisplayTitle'			=> 'onContentAfterTitle',
							'beforeDisplayContent'		=> 'onContentBeforeDisplay',
							'afterDisplayContent'		=> 'onContentAfterDisplay',
							'beforeSave'				=> 'onContentBeforeSave'
					);

		// Need to make this behave like how Joomla category behaves.
		if (!isset($row->catid)) {
			$row->catid	= $row->category_id;
		}

		// In the event that there is no such triggers, skip this
		if (!isset($events[$event])) {
			return false;
		}

		$result = $dispatcher->trigger($events[$event] , array('easyblog.blog', &$row, &$params, $limitstart));

		// Remove unwanted fields.
		unset($row->catid);

		return $result;
	}

	/**
	 * Retrieves plain links (Non SEF)
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getExternalLink($link, $xhtml = false)
	{
		$uri	= JURI::getInstance();
		$domain	= $uri->toString( array('scheme', 'host', 'port'));

		return $domain . '/' . ltrim(EBR::_( $link, $xhtml , null, true ), '/');
	}

	/**
	 *
	 *
	 * @deprecated	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isFeatured($type, $id)
	{
		$model 	= EB::model('Featured');
		return $model->isFeatured($type, $id);
	}

	/**
	 * Allows caller to upload a user's avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function uploadAvatar($profile, $isFromBackend = false, $file = false)
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my			= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config		= EB::config();

		$acl		= EB::acl();

		if (!$isFromBackend) {

			if (! $acl->get('upload_avatar')) {
				$url	= 'index.php?option=com_easyblog&view=dashboard&layout=profile';
				EB::info()->set(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'error');
				$mainframe->redirect(EBR::_($url, false));
			}
		}
		$avatar_config_path	= $config->get('main_avatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');
		$avatar_config_path	= str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$upload_path		= JPATH_ROOT.DIRECTORY_SEPARATOR.$avatar_config_path;
		$rel_upload_path	= $avatar_config_path;

		$err				= null;

		if (!$file) {
			$file				= $this->input->get('Filedata', '', 'files', 'array' );
		}

		//check whether the upload folder exist or not. if not create it.
		if(! JFolder::exists($upload_path))
		{
			if(! JFolder::create( $upload_path ))
			{
				// Redirect
				if(! $isFromBackend)
				{
					EB::info()->set( JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER') , 'error');
					$mainframe->redirect( EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false) );
				}
				else
				{
					//from backend
					$mainframe->redirect( EBR::_('index.php?option=com_easyblog&view=users', false), JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error' );
				}
				return;
			}
			else
			{
				// folder created. now copy index.html into this folder.
				if(! JFile::exists( $upload_path . DIRECTORY_SEPARATOR . 'index.html' ) )
				{
					$targetFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'index.html';
					$destFile	= $upload_path . DIRECTORY_SEPARATOR .'index.html';

					if( JFile::exists( $targetFile ) )
						JFile::copy( $targetFile, $destFile );
				}
			}
		}

		//makesafe on the file
		$file['name']	= $my->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			$target_file_path		= $upload_path;
			$relative_target_file	= $rel_upload_path.DIRECTORY_SEPARATOR.$file['name'];
			$target_file			= JPath::clean($target_file_path . DIRECTORY_SEPARATOR. JFile::makeSafe($file['name']));
			$isNew					= false;

			if (EB::image()->canUpload($file, $err)) {

				if (!$isFromBackend) {
					EB::info()->set( JText::_( $err ) , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				} else {
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=users', false), JText::_( $err ), 'error');
				}
				return;
			}

			if (0 != (int)$file['error'])
			{
				if(! $isFromBackend)
				{
					EB::info()->set( $file['error'] , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=users', false), $file['error'], 'error');
				}
				return;
			}

			//rename the file 1st.
			$oldAvatar	= $profile->avatar;
			$tempAvatar	= '';

			if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
			{
				$session	= JFactory::getSession();
				$sessionId	= $session->getToken();

				$fileExt	= JFile::getExt(JPath::clean($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . DIRECTORY_SEPARATOR . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar, $tempAvatar);
			}
			else
			{
				$isNew  = true;
			}

			if (JFile::exists($target_file))
			{
				if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DIRECTORY_SEPARATOR.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					EB::info()->set( JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=users', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			if (JFolder::exists($target_file))
			{

				if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DIRECTORY_SEPARATOR.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					//JError::raiseNotice(100, JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS',$relative_target_file));
					EB::info()->set( JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=users', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			$configImageWidth	= EBLOG_AVATAR_LARGE_WIDTH;
			$configImageHeight	= EBLOG_AVATAR_LARGE_HEIGHT;

			$image 	= EB::simpleimage();
			$image->load($file['tmp_name']);
			$image->resizeToFill($configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if( $oldAvatar != 'default.png' && $oldAvatar != 'default_blogger.png' )
			{
				//if(JFile::exists( JPATH_ROOT.DIRECTORY_SEPARATOR.$oldAvatar ))
				if(JFile::exists( $tempAvatar ))
				{
					//JFile::delete( JPATH_ROOT.DIRECTORY_SEPARATOR.$oldAvatar );
					JFile::delete( $tempAvatar );
				}
			}

			if($isNew && !$isFromBackend)
			{
				if( $my->id != 0 && $config->get('main_jomsocial_userpoint') )
				{
					$jsUserPoint	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
					if( JFile::exists( $jsUserPoint ) )
					{
						require_once( $jsUserPoint );
						CUserPoints::assignPoint( 'com_easyblog.avatar.upload' , $my->id );
					}
				}
			}

			return JFile::makeSafe( $file['name'] );
		}
		else
		{
			return 'default_blogger.png';
		}

	}

	public static function uploadCategoryAvatar($category, $isFromBackend = false)
	{
		return EB::uploadMediaAvatar('category', $category, $isFromBackend);
	}

	public static function uploadTeamAvatar( $team, $isFromBackend = false )
	{
		return EB::uploadMediaAvatar('team', $team, $isFromBackend);
	}

	/**
	 * Standard method to process uploaded avatars
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function uploadMediaAvatar($mediaType, $mediaTable, $isFromBackend = false)
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my = JFactory::getUser();
		$mainframe = JFactory::getApplication();
		$config = EB::config();
		$acl = EB::acl();


		// required params
		$layout_type = ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$view_type = ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$default_avatar_type = ($mediaType == 'category') ? 'default_category.png' : 'default_team.png';

		if (!$isFromBackend && $mediaType == 'category' && !$acl->get('upload_cavatar')) {
			$url = 'index.php?option=com_easyblog&view=dashboard&layout='.$layout_type;
			EB::info()->set( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'warning');
			return $mainframe->redirect(EBR::_($url, false));
		}

		$avatar_config_path	= ($mediaType == 'category') ? $config->get('main_categoryavatarpath') : $config->get('main_teamavatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');

		$upload_path = JPATH_ROOT . '/' . $avatar_config_path;
		$rel_upload_path = $avatar_config_path;

		$err = null;
		$file = EB::request()->files->get('Filedata', '');

		// Check whether the upload folder exist or not. if not create it.
		if (!JFolder::exists($upload_path) && !JFolder::create($upload_path)) {

			// Redirect
			if(! $isFromBackend) {
				EB::info()->set('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER', 'error');
				return $mainframe->redirect( EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false) );
			}

			return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$layout_type, false), JText::_('COM_EASYBLOG_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error' );
		}

		//makesafe on the file
		$file['name'] = $mediaTable->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name'])) {
			$target_file_path = $upload_path;
			$relative_target_file = $rel_upload_path.DIRECTORY_SEPARATOR.$file['name'];
			$target_file = JPath::clean($target_file_path . DIRECTORY_SEPARATOR. JFile::makeSafe($file['name']));

			if (!EB::image()->canUpload($file, $error)) {
				if (!$isFromBackend) {
					EB::info()->set($err, 'error');
					return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}

				return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), JText::_($err), 'error');
			}

			if (0 != (int)$file['error']) {
				if (!$isFromBackend) {
					EB::info()->set($file['error'], 'error');
					return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}

				return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), $file['error'], 'error');
			}

			// Rename the file 1st.
			$oldAvatar	= (empty($mediaTable->avatar)) ? $default_avatar_type : $mediaTable->avatar;
			$tempAvatar	= '';

			if ($oldAvatar != $default_avatar_type) {
				$session = JFactory::getSession();
				$sessionId = $session->getToken();

				$fileExt = JFile::getExt(JPath::clean($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . DIRECTORY_SEPARATOR . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path.DIRECTORY_SEPARATOR.$oldAvatar, $tempAvatar);
			}

			if (JFile::exists($target_file)) {

				//rename back to the previous one.
				if ($oldAvatar != $default_avatar_type) {
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if (!$isFromBackend) {
					EB::info()->set(JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
			}

			if (JFolder::exists($target_file)) {

				//rename back to the previous one.
				if ($oldAvatar != $default_avatar_type) {
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if (!$isFromBackend) {
					EB::info()->set( JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file) , 'error');
					return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view=dashboard&layout='.$layout_type, false));
				}
				return $mainframe->redirect(EBR::_('index.php?option=com_easyblog&view='.$view_type, false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
			}

			$image 	= EB::simpleimage();
			$image->load($file['tmp_name']);

			// Based on this 9511b4c commit, we keep the original size of category avatar
			// But we also need to resize it so that the PNG's transparency is kept. #1288
			if ($mediaType == 'category') {
				$image->resize($image->getWidth(), $image->getHeight());
			} else {
				$image->resizeToFill(EBLOG_AVATAR_LARGE_WIDTH, EBLOG_AVATAR_LARGE_HEIGHT);
			}

			$image->save($target_file, $image->image_type);

			// Now we update the user avatar. If needed, we remove the old avatar.
			if ($oldAvatar != $default_avatar_type && JFile::exists($tempAvatar)) {
				JFile::delete($tempAvatar);
			}

			return JFile::makeSafe($file['name']);
		}

		return $default_avatar_type;
	}

	/**
	 * Renders module in a template
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function renderModule($position, $attributes = array(), $content = null)
	{
		$doc = JFactory::getDocument();
		$renderer = $doc->loadRenderer('module');

		$buffer = '';
		$modules = JModuleHelper::getModules($position);

		foreach ($modules as $module) {
			$theme = EB::template();

			$theme->set('position', $position);
			$theme->set('output', $renderer->render($module, $attributes, $content));

			$buffer .= $theme->output('site/modules/item');
		}

		return $buffer;
	}

	/**
	 * Loads a library file
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function load($file)
	{
		$file = __DIR__ . '/' . strtolower($file) . '/' . strtolower($file) . '.php';

		require_once($file);

		return true;
	}

	/**
	 * Loads the default languages for EasyBlog
	 *
	 * @since	1.2
	 * @access	public
	 */
	public static function loadLanguages($path = JPATH_ROOT)
	{
		static $loaded = array();

		if (!isset($loaded[$path])) {
			$lang = JFactory::getLanguage();

			// Load site's default language file.
			$lang->load('com_easyblog', $path);

			$loaded[$path] = true;
		}

		return $loaded[$path];
	}

	/**
	 * Deprecated
	 *
	 * @deprecated	5.1
	 */
	public static function loadModuleCss()
	{
	}

	/**
	 * Given a username, retrieve the user's id.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getUserId($username)
	{
		$db = EB::db();

		$query	= 'SELECT `id` FROM `#__easyblog_users` WHERE `permalink`=' . $db->Quote($username);
		$db->setQuery( $query );
		$result	= $db->loadResult();

		if (empty($result)) {
			$query	= 'SELECT `id` FROM `#__users` WHERE `username`=' . $db->Quote( $username );
			$db->setQuery( $query );
			$result = $db->loadResult();
		}

		return $result;
	}

	/**
	 * Alternative to setMeta
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function setMetaData($keywords, $description)
	{

		if (!$keywords || !$description) {
			$menu = JFactory::getApplication()->getMenu()->getActive();
			$params = new JRegistry($menu->params);

			if (!$keywords) {
				$keywords = $params->get('menu-meta_keywords', '');
			}

			if (!$description) {
				$description = $params->get('menu-meta_description', '');
			}
		}

		if ($keywords) {
			$doc->setMetadata('keywords', $keywords);
		}

		if ($description) {
			$doc->setMetadata('description', $description);
		}
	}

	/**
	 * Allows caller to set the meta
	 *
	 * @since	5.1.9
	 * @access	public
	 */
	public static function setMeta($id, $type, $defaultViewDesc = '')
	{
		$doc = JFactory::getDocument();
		$config = EB::config();
		$app = JFactory::getApplication();

		// Try to load the meta for the content
		$meta = EB::table('Meta');
		$meta->load(array('content_id' => $id, 'type' => $type));

		// If the category was created without any meta, we need to automatically fill in the description
		if ($type == META_TYPE_CATEGORY && !$meta->id) {

			$category = '';
			if (EB::cache()->exists($id, 'category')) {
				$category = EB::cache()->get($id, 'category');
			} else {
				$category = EB::table('Category');
				$category->load($id);
			}

			$doc->setMetadata('description', strip_tags($category->description));
		}

		// If the blogger was created, try to get meta from blogger biography/title
		if ($type == META_TYPE_BLOGGER) {

			$author = '';
			if (EB::cache()->exists($id, 'author')) {
				$author = EB::cache()->get($id, 'author');
			} else {
				$author = EB::table('Profile');
				$author->load($id);
			}

			$biography = EB::string()->escape(strip_tags($author->biography));

			$doc->setMetadata('description', $biography);

			if (!empty($author->biography)) {
				$meta->description = $biography;
			}

			if (!empty($author->title)) {
				$meta->keywords = $author->title;
			}
		}

		// Automatically fill meta keywords
		if ($type == META_TYPE_POST && (($config->get('main_meta_autofillkeywords') && empty($meta->keywords) )|| ($config->get( 'main_meta_autofilldescription')))) {

			// Retrieve data from cache
			$post = EB::post();
			$post->load($id);

			$category = $post->getPrimaryCategory();
			$keywords = array($category->getTitle());

			if ($config->get('main_meta_autofillkeywords') && empty($meta->keywords)) {

				$tags = $post->getTags();

				foreach ($tags as $tag) {
					$keywords[] = $tag->getTitle();
				}

				$meta->keywords = implode(',', $keywords);
			}

			// Automatically fill meta description
			if ($config->get( 'main_meta_autofilldescription' ) && empty($meta->description)) {

				$content = $post->getIntro(EASYBLOG_STRIP_TAGS);
				$content = strip_tags($content);

				// Remove newlines
				$content = str_ireplace("\n", "", $content);
				$content = trim($content);

				// Set description into meta headers
				$meta->description	= EBString::substr($content , 0 , $config->get( 'main_meta_autofilldescription_length'));
				$meta->description	= EB::string()->escape($meta->description);
			}

			// Remove JFBConnect codes.
			if ($meta->description) {
				$pattern = '/\{JFBCLike(.*)\}/i';
				$meta->description = preg_replace($pattern , '' , $meta->description);
			}
		}


		// Check if the descriptin or keysword still empty or not. if yes, try to get from joomla menu.
		if (empty($meta->description) && empty($meta->keywords)) {
			$active = JFactory::getApplication()->getMenu()->getActive();

			if ($active) {
				$params = $active->params;

				$description = $params->get('menu-meta_description', '');
				$keywords = $params->get('menu-meta_keywords', '');

				if (!empty($description) || !empty($keywords)) {
					$meta = new stdClass();
					$meta->description = EB::string()->escape($description);
					$meta->keywords = $keywords;
				}
			}
		}

		// Only inject this in frontend and html type doc
		if (!$app->isAdmin() && $doc->getType() == 'html') {
			$key = $config->get('facebook_instant_article_id', '');

			if ($key != '') {
				$doc->addCustomTag('<meta property="fb:pages" content="' . $key . '" />');
			}
		}

		if (!$meta) {
			return;
		}

		// If there's no meta description, try to get it from Joomla's settings
		if (!$meta->description && $defaultViewDesc) {
			$meta->description = $defaultViewDesc . ' - ' . EB::jconfig()->get('MetaDesc');
		}

		if ($meta->keywords) {
			$doc->setMetadata('keywords', $meta->keywords);
		}

		if ($meta->description) {
			$doc->setMetadata('description', $meta->description);
		}

		// Admin probably disabled indexing
		if (isset($meta->indexing) && !$meta->indexing) {
			$doc->setMetadata('robots', 'noindex,follow');
		}

	}

	/**
	 * Deprecated. Use EB::comment()->getLikesAuthors
	 *
	 * @deprecated	5.1
	 */
	public static function getLikesAuthors($contentId, $type, $userId)
	{
		return EB::comment()->getLikesAuthors($contentId, $type, $userId);
	}

	/**
	 * We cannot rely on $app->getTemplate() because we need to explicitly get the current default front end template.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getCurrentTemplate()
	{
		static $template = null;

		if (is_null($template)) {
			$model = EB::model('Themes');
			$template = $model->getDefaultJoomlaTemplate();
		}

		return $template;
	}


	/**
	 * Retrieve easyblog custom themes
	 *
	 * @since 5.1.0
	 * @access public
	 */
	public static function getCustomThemes($template)
	{
		$path = JPATH_ROOT . '/templates/' . $template . '/html/com_easyblog/themes';

		if (!JFolder::exists($path)) {
			return false;
		}

		$folders = JFolder::folders($path);

		return $folders;
	}



	/**
	 * Given a page title, this method would try to find any existing menu items that are tied to the current page view.
	 * * If a page title is tied, it will then use the page title defined in the menu.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getPageTitle($default = '', $useMenuForTitle = true)
	{
		$config = EB::config();
		$app = JFactory::getApplication();
		$itemid = $app->input->get('Itemid', '');
		$originalTitle = $default;

		// @task: If we can't detect the item id, just return the default page title that was passed in.
		if (!$itemid) {
			return $default;
		}

		// Prepare Joomla's site title if necessary.
		$jConfig = EB::jConfig();
		$addTitle = $jConfig->get('sitename_pagetitles');
		$siteTitle = $jConfig->get('sitename');

		// Only add Joomla's site title if it was configured to.
		if ($addTitle) {
			// If the user does not want to append anything
			if (!$config->get('main_pagetitle_autoappend')) {
				$default = $siteTitle;
			} else {
				if ($addTitle == 1) {
					// There is a case where the site title is same as the $default
					// Entry 1 - My Easyblog - My Easyblog
					if ($siteTitle != $default) {
						$default = $siteTitle . ' - ' . $default;
					} else {
						$default = $siteTitle;
					}
				}

				if ($addTitle == 2) {
					$default = $default . ' - ' . $siteTitle;
				}
			}
		}

		// @task: Let's find the menu item.
		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getItem($itemid);

		// @task: If configured to not append the blog title on the page, do not set any page title.
		if (!$config->get('main_pagetitle_autoappend') && $default == $config->get('main_title')) {
			return $siteTitle;
		}

		// If menu item cannot be found anywhere, just use the default
		if (!$item) {
			return $default;
		}

		// @task: Let's get the page title from the menu.
		$title = $item->params->get('page_title', '');

		// If a title is found, just use the configured title.
		if ($title && $useMenuForTitle) {
			return $title;
		}

		return $default;
	}

	/**
	 * Generate page title for post entry page
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public static function getPagePostTitle($postTitle)
	{
		$config = EB::config();
		$jConfig = EB::jConfig();
		$pageTitle = $postTitle;

		// Get the page title
		$title = EB::getPageTitle(JText::_($config->get('main_title')));

		// Prepare Joomla's site title if necessary.
		$jPageTitleFormat = $jConfig->get('sitename_pagetitles');

		if ($config->get('main_pagetitle_autoappend_entry') && $title) {

			$pageTitle = $postTitle . ' - ' . $title;

			// after post title render site title
			if ($jPageTitleFormat == 2) {
				$pageTitle = $postTitle . ' - ' . $title;
			}

			// before post title render site title
			if ($jPageTitleFormat == 1) {
				$pageTitle = $title . ' - ' . $postTitle;
			}
		}

		return $pageTitle;
	}

	// this function used to show the login form
	public static function showLogin($return='')
	{
		$my = JFactory::getUser();

		if ($my->id == 0) {
			$comUserOption	= 'com_users';
			$tasklogin		= 'user.login';
			$tasklogout		= 'user.logout';
			$viewRegister	= 'registration';
			$inputPassword	= 'password';

			if (empty($return)) {
				$currentUri = EBFactory::getURI(true);
				$uri = base64_encode($currentUri);
			} else {
				$uri = $return;
			}

			$theme = EB::template();
			$theme->set('return', $uri);
			$theme->set('comUserOption', $comUserOption);
			$theme->set('tasklogin', $tasklogin);
			$theme->set('tasklogout', $tasklogout);
			$theme->set('viewRegister', $viewRegister);
			$theme->set('inputPassword', $inputPassword);

			echo $theme->output('site/login/default');
		}
	}

	public static function getThemeInfo( $name )
	{
		jimport( 'joomla.filesystem.file' );

		$mainframe	= JFactory::getApplication();

		$file =	'';

		// We need to specify if the template override folder also have config.ini file
		if ( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'config.ini' ) )
		{
			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'config.ini';
		}

		// then check the current theme folder
		elseif ( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog'. DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.ini' ) )
		{
			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.ini';
		}

		if( !empty( $file ) )
		{
			$raw		= JFile::read( $file );
			$registry	= EB::registry($raw);

			return $registry;
		}
		return EB::registry();
	}

	/**
	 * Generates a html code for category selection in backend
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function populateCategoryFilter($eleName, $catId = '', $attributes, $defaultText = 'COM_EASYBLOG_SELECT_A_CATEGORY', $className = '')
	{
		$model = EB::model('Category');
		$categories = $model->generateCategoryFilterList();

		$options = "";

		if ($categories) {

			$selected = !$catId ? ' selected="selected"' : '';
			$options .= '<option value="0"' . $selected . '>' . JText::_($defaultText) . '</option>';


			foreach ($categories as $category) {

				$selected = $category->id == $catId ? ' selected="selected"' : '';

				$space = '';
				$sup = '';

				if ($category->depth > 0) {

					$sup	= '<sup>|_</sup>';

					for ($d = 0; $d < $category->depth; $d++) {
						$space .= '&nbsp;&nbsp;&nbsp;';
					}
				}

				$options .= '<option value="' . $category->id . '"' . $selected . '>' . $space . $sup . JText::_($category->title) . '</option>';
			}
		}

		$html = '';
		$html .= '<select name="' . $eleName . '" id="' . $eleName .'" class="' . $className . '" ' . $attributes . '>';
		$html .= $options;
		$html .= '</select>';

		return $html;
	}


	/**
	 * Generates a html code for category selection.
	 *
	 * @access	public
	 */
	public static function populateCategories($parentId, $userId, $outType, $eleName, $default, $isWrite = false, $isPublishedOnly = false, $isFrontendWrite = false, $exclusion = array(), $attributes = '', $defaultText = 'COM_EASYBLOG_SELECT_PARENT_CATEGORY', $customClass = '')
	{
		$catModel = EB::model('Category');
		$parentCat = null;

		if (!empty($userId)) {
			$parentCat = $catModel->getParentCategories($userId, 'blogger', $isPublishedOnly, $isFrontendWrite, $exclusion);
		} else if (!empty($parentId)) {
			$parentCat = $catModel->getParentCategories($parentId, 'category', $isPublishedOnly, $isFrontendWrite, $exclusion);
		} else {
			$parentCat = $catModel->getParentCategories('', 'all', $isPublishedOnly, $isFrontendWrite, $exclusion);
		}

		$ignorePrivate = true;

		if ($outType == 'link') {
			$ignorePrivate = false;
		}

		// Now let's do a loop to find it's child categories.
		if (!empty($parentCat)) {
			for ($i = 0; $i < count($parentCat); $i++) 	{
				$parent =& $parentCat[$i];

				//reset
				$parent->childs = null;

				EB::buildNestedCategories($parent->id, $parent, $ignorePrivate, $isPublishedOnly, $isFrontendWrite, $exclusion);
			}
		}

		if ($isWrite) {
			$defaultCatId = EB::getDefaultCategoryId();
			$default = (empty($default)) ? $defaultCatId : $default;
		}

		$formEle = '';

		if ($outType == 'select' && $isWrite) {
			$selected = !$default ? ' selected="selected"' : '';
			$formEle .= '<option value="0"' . $selected . '>' . JText::_('COM_EASYBLOG_SELECT_A_CATEGORY') . '</option>';
		}

		if (!$isWrite) {
			$formEle .=	'<option value="0">' . JText::_($defaultText) . '</option>';
		}

		if ($parentCat) {
			foreach ($parentCat as $category) {
				if ($outType == 'popup') {
					$formEle .= '<div class="category-list-item" id="'.$category->id.'"><a href="javascript:void(0);" onclick="eblog.dashboard.selectCategory(\''. $category->id . '\')">' . $category->title . '</a>';
					$formEle .= '<input type="hidden" id="category-list-item-'.$category->id.'" value="'.$category->title.'" />';
					$formEle .= '</div>';
				} else {
					$selected = ($category->id == $default) ? ' selected="selected"' : '';
					$formEle .= '<option value="'.$category->id.'" ' . $selected. '>' . JText::_($category->title) . '</option>';
				}

				EB::accessNestedCategories($category, $formEle, '0', $default, $outType);
			}
		}

		$className = 'form-control';

		if ($customClass) {
			$className = $customClass;
		}

		$html = '';
		$html .= '<select name="' . $eleName . '" id="' . $eleName .'" class="' . $className . '" ' . $attributes . '>';
		$html .= $formEle;
		$html .= '</select>';

		return $html;
	}


	public static function buildNestedCategories($parentId, &$parent, $ignorePrivate = false, $isPublishedOnly = false, $isWrite = false, $exclusion = array())
	{
		$my = JFactory::getUser();

		$childs = array();

		//lets try to get from cache if there is any
		if (EB::cache()->exists($parentId, 'cats')) {
			$data = EB::cache()->get($parentId, 'cats');

			if (isset($data['child'])) {
				$childs = $data['child'];
			} else {
				return false;
			}
		} else {
			$catModel = EB::model( 'Categories');
			$childs = $catModel->getChildCategories($parentId, $isPublishedOnly, $isWrite , $exclusion );
		}

		if (!$childs) {
			return false;
		}

		$items = array();

		foreach($childs as $child) {
			$items[$child->id] = $child;
		}

		$parent->childs = array();

		$catLib = EB::category();
		$catLib::addChilds($parent, $items);

		return false;
	}


	public static function accessNestedCategories($arr, &$html, $deep='0', $default='0', $type='select', $linkDelimiter = '')
	{
		if (isset($arr->childs) && is_array($arr->childs)) {
			$sup	= '<sup>|_</sup>';
			$space	= '';
			$ld		= (empty($linkDelimiter)) ? '>' : $linkDelimiter;

			if($type == 'select' || $type == 'popup') {
				$deep++;
				for($d=0; $d < $deep; $d++)
				{
					$space .= '&nbsp;&nbsp;&nbsp;';
				}
			}

			for ($j	= 0; $j < count($arr->childs); $j++) {
				$child	= $arr->childs[$j];

				$cat = EB::table('Category');
				$cat->bind($child);

				if($type == 'select') {
					$selected	= ($child->id == $default) ? ' selected="selected"' : '';

					$html	.= '<option value="'.$child->id.'" ' . $selected . '>' . $space . $sup . JText::_($child->title)  . '</option>';
				} else if($type == 'popup') {
					$html	.= '<div class="category-list-item" id="'.$child->id.'">' . $space . $sup . '<a href="javascript:void(0);" onclick="eblog.dashboard.selectCategory(\''. $child->id. '\')">' . JText::_($child->title) . '</a>';
					$html	.= '<input type="hidden" id="category-list-item-'.$child->id.'" value="'. JText::_($child->title) .'" />';
					$html	.= '</div>';
				} else {
					$str	= '<a href="' . $cat->getPermalink() . '">' . $cat->getTitle() . '</a>';
					$html	.= (empty($html)) ? $str : $ld . $str;
				}

				if ($type == 'select') {
					EB::accessNestedCategories($child, $html, $deep, $default, $type, $linkDelimiter);
				}
			}
		}

		return false;
	}



	public static function accessNestedCategoriesId($arr, &$newArr)
	{
		if(isset($arr->childs) && is_array($arr->childs)) {

			for ($j	= 0; $j < count($arr->childs); $j++) {
				$child		= $arr->childs[$j];
				$newArr[]	= $child->id;
				EB::accessNestedCategoriesId($child, $newArr);
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Retrieve backward linkage from a child category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function populateCategoryLinkage($childId)
	{
		$arr = array();
		$category = EB::table('Category');
		$category->load($childId);

		$obj = new stdClass();
		$obj->id = $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;

		$arr[] = $obj;

		if ((!empty($category->parent_id))) {
			EB::accessCategoryLinkage($category->parent_id, $arr);
		}

		$arr = array_reverse($arr);
		return $arr;

	}

	public static function accessCategoryLinkage($childId, &$arr)
	{
		$category	= EB::table('Category');
		$category->load($childId);

		$obj = new stdClass();
		$obj->id = $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;



		$arr[] = $obj;

		if ((!empty($category->parent_id))) {
			EB::accessCategoryLinkage($category->parent_id, $arr);
		} else {
			return false;
		}
	}


	/**
	 * Get post title by ID
	 */
	public static function getPostTitle($id)
	{
		$db = EB::db();

		$query = 'SELECT ' . $db->nameQuote('title') . ' FROM ' . $db->nameQuote('#__easyblog_post') . ' WHERE id = ' . $db->Quote($id);
		$db->setQuery($query);
		return $db->loadResult();
	}

	public static function storeSession($data, $key, $ns = 'COM_EASYBLOG')
	{
		$mySess	= JFactory::getSession();
		$mySess->set($key, $data, $ns);
	}

	public static function getSession($key, $ns = 'COM_EASYBLOG')
	{
		$data = null;
		$mySess	= JFactory::getSession();
		if ($mySess->has($key, $ns)) {
			$data = $mySess->get($key, '', $ns);
			$mySess->clear($key, $ns);
			return $data;
		} else {
			return $data;
		}
	}

	public static function clearSession($key, $ns = 'COM_EASYBLOG')
	{
		$mySess = JFactory::getSession();
		if($mySess->has($key, $ns))
		{
			$mySess->clear($key, $ns);
		}
		return true;
	}

	/**
	 * Renders the ajax library
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function ajax()
	{
		static $ajax = null;

		if (!$ajax) {

			require_once(__DIR__ . '/ajax/ajax.php');

			$ajax = new EasyBlogAjax();
		}

		return $ajax;
	}

	/**
	 * Disallow users to view the content
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function showAccessDenied($type='', $access = '0')
	{
		$message = JText::_('COM_EASYBLOG_SORRY_YOU_DO_NOT_HAVE_PERMISSION_TO_VIEW');

		if ($type == 'teamblog' && $access == '1') {
			$message = JText::_('COM_EASYBLOG_TEAMBLOG_MEMBERS_ONLY');
		}

		$theme = EB::template();
		$theme->set('message', $message);

		$result = $theme->output('site/blogs/access.denied');

		return $result;
	}

	public static function getJoomlaVersion()
	{
		$jVerArr = explode('.', JVERSION);
		$jVersion = $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	/**
	 * Determines if the current instance of Joomla is 3.1 and above
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isJoomla31()
	{
		$state = false;

		if (EB::getJoomlaVersion() >= '3.1' && !EB::isJoomla40()) {
			$state = true;
		}

		return $state;
	}

	/**
	 * Determines if the current instance of Joomla is 3.1 and above
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function isJoomla40()
	{
		return EB::getJoomlaVersion() >= '4.0';
	}

	/**
	 * Retrieves all usergroups ids on the site
	 *
	 * @since   5.2.6
	 * @access  public
	 */
	public static function getUsergroupsIds($idOnly = false)
	{
		$db = EB::db();

		$column = $idOnly ? 'a.`id`' : 'a.*, COUNT(DISTINCT(b.`id`)) AS `level`';

		$query 	= 'SELECT ' . $column . ' FROM ' . $db->quoteName('#__usergroups') . ' AS a';
		$query .= ' LEFT JOIN ' . $db->quoteName('#__usergroups') . ' AS b';
		$query .= ' ON a.`lft` > b.`lft` AND a.`rgt` < b.`rgt`';
		$query .= ' GROUP BY a.`id`, a.`title`, a.`lft`, a.`rgt`, a.`parent_id`';
		$query .= ' ORDER BY a.`lft` ASC';

		$db->setQuery($query);
		$groups = $idOnly ? $db->loadColumn() : $db->loadObjectList();

		return $groups;
	}

	/**
	 * Retrieves a list of super users from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getSAUsersIds()
	{
		$db = EB::db();

		$query	= 'SELECT a.`id`, a.`title`';
		$query	.= ' FROM `#__usergroups` AS a';
		$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query	.= ' GROUP BY a.id';
		$query	.= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$saGroup = array();
		foreach($result as $group)
		{
			if(JAccess::checkGroup($group->id, 'core.admin'))
			{
				$saGroup[]  = $group;
			}
		}


		//now we got all the SA groups. Time to get the users
		$saUsers = array();
		if(count($saGroup) > 0)
		{
			foreach($saGroup as $sag)
			{
				$userArr = JAccess::getUsersByGroup($sag->id);
				if(count($userArr) > 0)
				{
					foreach($userArr as $user)
					{
						$saUsers[] = $user;
					}
				}
			}
		}

		return $saUsers;
	}

	/**
	 * Retrieves the default super administrator user id
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getDefaultSAIds()
	{
		$saUsers = EB::getSAUsersIds();
		$saUserId = $saUsers[0];

		return $saUserId;
	}

	public static function getFirstImage( $content )
	{
		//try to search for the 1st img in the blog
		$img		= '';
		$pattern	= '#<img[^>]*>#i';
		preg_match( $pattern , $content , $matches );

		if( $matches )
		{
			$img	= $matches[0];
		}


		//image found. now we process further to get the absolute image path.
		if(! empty($img) )
		{
			//get the img src

			$pattern = '/src\s*=\s*"(.+?)"/i';
			preg_match($pattern, $img, $matches);
			if($matches)
			{
				$imgPath	= $matches[1];
				$imgSrc		= EB::image()->rel2abs($imgPath, JURI::root());

				return $imgSrc;
			}
		}

		return false;
	}

	public static function getBloggerTheme()
	{
		$id		= EBR::isBloggerMode();

		if( empty( $id ) )
		{
			return false;
		}

		$profile = EB::user($id);

		$userparams	= EB::registry($profile->params);

		$userTheme = $userparams->get('theme', false);

		return (!$userTheme || $userTheme == 'global') ? false : $userTheme;
		// return $userparams->get('theme', false);
	}

	public static function getFeaturedImage( $content )
	{
		$pattern = '#<img class="featured[^>]*>#i';
		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			return $matches[0];
		}

		// If featured image is not supplied, try to use the first image as the featured post.
		$pattern				= '#<img[^>]*>#i';

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			return $matches[0];
		}

		// If all else fail, try to use the default image
		return false;
	}

	public static function getUserGids($userId = '', $implode = false)
	{
		$user = '';

		if (empty($userId)) {
			$user = JFactory::getUser();
		} else {
			$user = JFactory::getUser($userId);
		}

		$grpId = array();

		if ($user->id == 0) {
			$grpId	= JAccess::getGroupsByUser(0, false);
		} else {
			$grpId	= JAccess::getGroupsByUser($user->id, false);
		}

		if (empty($grpId)) {
			//this case shouldn't happen but it happened. sigh.
			$grpId[] = '1';
		}

		if ($implode) {
			$gids = '';
			if (count($grpId) > 0) {
				foreach ($grpId as $id) {
					$gids .= (empty($gids)) ? $id : ',' . $id;
				}
			}
			return $gids;
		}

		return $grpId;

	}

	/**
	 * Retrieves Joomla's cache object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getCache()
	{
		$jconfig = EB::jconfig();

		$options = array(
			'defaultgroup'	=> '',
			'storage' 		=> $jconfig->get('cache_handler', ''),
			'caching'		=> true,
			'cachebase'		=> $jconfig->get('cache_path', JPATH_SITE . '/cache')
		);

		$cache = JCache::getInstance('', $options);

		return $cache;
	}



	public static function getAccessibleCategories( $parentId = 0 )
	{
		$db			= EB::db();
		$my			= JFactory::getUser();

		$gids		= '';
		$catQuery	= 	'select distinct a.`id`, a.`private`';
		$catQuery	.=  ' from `#__easyblog_category` as a';
		$catQuery	.=  ' where (a.`private` = ' . $db->Quote('0');

		if( $my->id != 0 )
		{
			$catQuery	.=  ' OR a.`private` = ' . $db->Quote('1');
		}

		$gid	= array();
		if( $my->id == 0 )
		{
			$gid	= JAccess::getGroupsByUser(0, false);
		}
		else
		{
			$gid	= JAccess::getGroupsByUser($my->id, false);
		}

		if( count( $gid ) > 0 )
		{
			foreach( $gid as $id)
			{
				$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			}
		}

		$catQuery	.= ' OR a.`id` IN (';
		$catQuery	.= '     SELECT c.category_id FROM `#__easyblog_category_acl` as c ';
		$catQuery	.= '        WHERE c.acl_id = ' .$db->Quote( CATEGORY_ACL_ACTION_VIEW );
		$catQuery	.= '        AND c.content_id IN (' . $gids . ') )';
		$catQuery	.= ')';

		if ($parentId) {
			$catQuery   .= ' AND a.parent_id = ' . $db->Quote($parentId);
		}

		$db->setQuery($catQuery);
		$result = $db->loadObjectList();

		return $result;
	}

	public static function getPrivateCategories()
	{
		$db				= EB::db();
		$my				= JFactory::getUser();
		$excludeCats	= array();

		$catQuery = '';
		// get all private categories id
		if($my->id == 0)
		{
			$catQuery	= 	'select distinct a.`id`, a.`private`';
			$catQuery	.=  ' from `#__easyblog_category` as a';
			$catQuery	.=	' 	left join `#__easyblog_category_acl` as b on a.`id` = b.`category_id` and b.`acl_id` = ' . $db->Quote( CATEGORY_ACL_ACTION_VIEW );
			$catQuery	.=  ' where a.`private` != ' . $db->Quote('0');

			$gid	= array();
			$gids	= '';

			$gid = JAccess::getGroupsByUser(0, false);

			if (count($gid) > 0) {
				foreach( $gid as $id) {
					$gids .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
				$catQuery .= ' and b.`category_id` NOT IN (';
				$catQuery .= '     SELECT c.category_id FROM `#__easyblog_category_acl` as c ';
				$catQuery .= '        WHERE c.acl_id = ' .$db->Quote( CATEGORY_ACL_ACTION_VIEW );
				$catQuery .= '        AND c.content_id IN (' . $gids . ') )';
			}

		} else {
			$gid = EB::getUserGids();
			$gids = '';

			if (count($gid) > 0) {

				foreach ($gid as $id) {
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
			}

			$catQuery = 'select id from `#__easyblog_category` as a';
			$catQuery .= ' where not exists (';
			$catQuery .= '		select b.category_id from `#__easyblog_category_acl` as b';
			$catQuery .= '			where b.category_id = a.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_VIEW );
			$catQuery .= '			and b.type = ' . $db->Quote('group');
			$catQuery .= '			and b.content_id IN (' . $gids . ')';
			$catQuery .= '      )';
			$catQuery .= ' and a.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
		}

		if( !empty( $catQuery ) )
		{

			$db->setQuery($catQuery);
			$result = $db->loadObjectList();

			for($i=0; $i < count($result); $i++)
			{
				$item			=& $result[$i];
				$item->childs	= null;

				EB::buildNestedCategories($item->id, $item);

				$catIds = array();
				$catIds[] = $item->id;
				EB::accessNestedCategoriesId($item, $catIds);

				$excludeCats = array_merge($excludeCats, $catIds);
			}
		}

		return $excludeCats;
	}

	public static function getViewableTeamIds($userId = '')
	{

		$db	= EB::db();
		$my	= '';

		if( empty($userId) )
		{
			$my = JFactory::getUser();
		}
		else
		{
			$my = JFactory::getUser($userId);
		}

		$teamBlogIds = '';


		if( $my->id == 0)
		{
			//get team id with access == 3
			$query	= 'select `id` FROM `#__easyblog_team` where `access` = ' . $db->Quote( '3' );
			$query	.= ' and `published` = ' . $db->Quote( '1' );
			$db->setQuery($query);

			$result	= $db->loadResultArray();
			return $result;
		}
		else
		{

			$gid = EB::getUserGids($userId);
			$gids = '';

			if (count( $gid ) > 0) {
				foreach ($gid as $id) {
					$gids .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
			}

			// get the teamid from this user.
			$query	= 'select distinct `id` from `#__easyblog_team` as t left join `#__easyblog_team_users` as tu on t.id = tu.team_id';
			$query	.= ' left join `#__easyblog_team_groups` as tg on t.id = tg.team_id';
			$query	.= ' where t.`published` = ' . $db->Quote( '1' );
			$query	.= ' and (tu.`user_id` = ' . $db->Quote( $my->id );
			$query	.= ' OR t.`access` IN (2, 3)';
			$query	.= ' OR tg.group_id IN (' . $gids . ')';
			$query	.= ')';

			$db->setQuery($query);

			$result = $db->loadResultArray();
			return $result;
		}

	}

	public static function getDefaultCategoryId()
	{
		$db = EB::db();

		$gid = EB::getUserGids();
		$gids = '';
		if( count( $gid ) > 0 )
		{
			foreach( $gid as $id)
			{
				$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			}
		}

		$query	= 'SELECT a.id';
		$query	.= ' FROM `#__easyblog_category` AS a';
		$query	.= ' WHERE a.`published` = ' . $db->Quote( '1' );
		$query	.= ' AND a.`default` = ' . $db->Quote( '1' );
		$query	.= ' and a.id not in (';
		$query	.= ' 	select id from `#__easyblog_category` as c';
		$query	.= ' 	where not exists (';
		$query	.= '			select b.category_id from `#__easyblog_category_acl` as b';
		$query	.= '				where b.category_id = c.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_SELECT );
		$query	.= '				and b.type = ' . $db->Quote('group');
		$query	.= '				and b.content_id IN (' . $gids . ')';
		$query	.= '		)';
		$query	.= '	and c.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
		$query	.= '	)';
		$query	.= ' AND a.`parent_id` NOT IN (SELECT `id` FROM `#__easyblog_category` AS e WHERE e.`published` = ' . $db->Quote( '0' ) . ' AND e.`parent_id` = ' . $db->Quote( '0' ) . ' )';
		$query	.= ' ORDER BY a.`lft` LIMIT 1';

		$db->setQuery( $query );
		$result = $db->loadResult();

		return ( empty( $result ) ) ? '0' : $result ;
	}

	public static function isBlogger( $userId )
	{
		if( empty( $userId ) )
			return false;

		$acl = EB::acl($userId);
		if ($acl->get('add_entry')) {
			return true;
		} else {
			return false;
		}

	}

	public static function getUniqueFileName($originalFilename, $path)
	{
		$ext			= JFile::getExt($originalFilename);
		$ext			= $ext ? '.'.$ext : '';
		$uniqueFilename	= JFile::stripExt($originalFilename);

		$i = 1;

		while( JFile::exists($path.DIRECTORY_SEPARATOR.$uniqueFilename.$ext) )
		{
			// $uniqueFilename	= JFile::stripExt($originalFilename) . '-' . $i;
			$uniqueFilename	= JFile::stripExt($originalFilename) . '_' . $i . '_' . EB::date()->toFormat( "%Y%m%d-%H%M%S" );
			$i++;
		}

		//remove the space into '-'
		$uniqueFilename = str_ireplace(' ', '-', $uniqueFilename);

		return $uniqueFilename.$ext;
	}

	/**
	 * Retrieves the current language
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCurrentLanguage()
	{
		static $language = null;

		if (JFactory::getApplication()->isAdmin()) {
			return false;
		}

		if (is_null($language)) {

			$language = false;

			// When language filter is enabled, we need to detect the appropriate contents
			$multiLanguage = JFactory::getApplication()->getLanguageFilter();

			if ($multiLanguage) {
				$language = JFactory::getLanguage()->getTag();
			}
		}

		return $language;
	}

	public static function getCategoryMenuBloggerId()
	{
		$app = JFactory::getApplication();

		$itemId	= $app->input->get('Itemid', 0);
		$menu = $app->getMenu();
		$menuparams	= $menu->getParams( $itemId );
		$catBloggerId = $menuparams->get('cat_bloggerid', '');

		return $catBloggerId;
	}

	/**
	 * Adds canonical URL to satisfy google bots in case they think that it's a duplicated content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function addCanonicalURL( $extraFishes = array() )
	{
		if (empty( $extraFishes ))
		{
			return;
		}

		$juri = JURI::getInstance();

		foreach( $extraFishes as $fish )
		{
			$juri->delVar( $fish );
		}

		$preferredURL	= $juri->toString();

		jimport('joomla.filter.filterinput');
		$inputFilter	= JFilterInput::getInstance();
		$preferredURL	= $inputFilter->clean($preferredURL, 'string');

		$document	= JFactory::getDocument();
		$document->addHeadLink( $preferredURL, 'canonical', 'rel');
	}

	public static function addScriptDeclaration( $code='' )
	{
		return '<script type="text/javascript">EasyBlog.ready(function($){' . $code . '});</script>';
	}

	/**
	 * Generates the unsubscribe link for emails
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getUnsubscribeLink($subdata, $external=false, $isAllType = false, $email = '')
	{
		$easyblogItemId	= EBR::getItemId( 'latest' );

		if ($isAllType && $email) {

			$types = array();
			$ids = array();
			$tokens = array();

			foreach($subdata as $type => $id) {
				$types[] = $type;

				$tmpId = explode('|', $id);
				$ids[] = $tmpId[0];
				$tokens[] = md5($tmpId[0] . $tmpId[1]);
			}

			$stype = implode(',', $types);
			$sid = implode(',', $ids);
			$stoken = implode(',', $tokens);

			$unsubdata = base64_encode("type=".$stype."\r\nsid=".$sid."\r\nuid=".$email."\r\ntoken=".$stoken);

		} else {
			$unsubdata = base64_encode("type=".$subdata->type."\r\nsid=".$subdata->id."\r\nuid=".$subdata->user_id."\r\ntoken=".md5($subdata->id.$subdata->created));
		}

		return EBR::getRoutedURL('index.php?option=com_easyblog&task=subscription.unsubscribe&data='.$unsubdata.'&Itemid=' . $easyblogItemId, false, $external);
	}

	/**
	 * Retrieves the editor except composer page
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getEditor($nameOnly = false)
	{
		$config = EB::config();
		$jConfig = EB::jConfig();

		// If use system editor, we should check if the configured editor exists or enabled.
		$selectedEditor = $config->get('layout_editor');

		// if use build-in composer, we should check from the global configuration setting
		if ($selectedEditor == 'composer') {
			$selectedEditor = $jConfig->get('editor');
		}

		// Test if the plugin is enabled
		$enabled = JPluginHelper::isEnabled('editors', $selectedEditor);

		// If the editor isn't enabled, we need to intelligently find one that is enabled.
		if (!$enabled) {

			$model = EB::model('Settings');
			$randomEditor = $model->getAvailableEditor();

			if (!$randomEditor) {
				// No editors enabled on the site. WTF?
				EB::info()->set(JText::_('COM_EASYBLOG_NO_EDITORS_ENABLED_ON_SITE'), 'error');

				return false;
			}

			// Use the random enabled editor
			$selectedEditor = $randomEditor;

			// Show some error message that the configured editor isn't available.
			EB::info()->set(JText::sprintf('COM_EASYBLOG_SELECTED_EDITOR_NOT_ENABLED', $selectedEditor, $randomEditor), 'error');
		}

		if ($nameOnly) {
			return $selectedEditor;
		}

		$editor = EBFactory::getEditor($selectedEditor);

		return $editor;
	}

	/**
	 * Renders the edit profile link in EasyBlog
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getEditProfileLink()
	{
		$link = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile');
		$config = EB::config();

		if ($config->get('integrations_easysocial_editprofile') && EB::easysocial()->exists()) {
			$link = ESR::profile(array('layout' => 'edit'));
		}

		return $link;
	}

	/**
	 * Determines if user registration is enabled in Joomla
	 *
	 * @since	5.0.18
	 * @access	public
	 */
	public static function isRegistrationEnabled()
	{
		$params = JComponentHelper::getParams('com_users');

		if ($params->get('allowUserRegistration')) {
			return true;
		}

		return false;
	}

	/**
	 * Renders the registration link within EasyBlog extension
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getRegistrationLink()
	{
		$config = EB::config();
		$provider = $config->get('main_login_provider');

		$link = JRoute::_('index.php?option=com_users&view=registration');


		if ($provider == 'easysocial' && EB::easysocial()->exists()) {
			$link = ESR::registration();
		}

		if ($provider == 'cb') {
			$link = JRoute::_('index.php?option=com_comprofiler&task=registers');
		}

		if ($provider == 'jomsocial') {
			$link = JRoute::_('index.php?option=com_community&view=register');
		}

		return $link;
	}

	/**
	 * Generates the login link within EasyBlog extension
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getLoginLink($returnURL = '')
	{
		$config = EB::config();

		if (!empty($returnURL)) {
			$return = '&return=' . $returnURL;
		}

		$default = EBR::_('index.php?option=com_users&view=login' . $return);

		// Default link
		$link = $default;
		$loginProvider = $config->get('main_login_provider');

		if ($loginProvider == 'easysocial') {
			$easysocial = EB::easysocial();

			if ($easysocial->exists()) {
				$link = ESR::login(array('return' => $return));
			}
		}

		if ($loginProvider == 'cb') {
			$link = JRoute::_('index.php?option=com_comprofiler&task=login' . $return);
		}

		if ($loginProvider == 'easyblog') {
			$link = JRoute::_('index.php?option=com_easyblog&view=login' . $return);
		}

		return $link;
	}

	/**
	 * Generates the reset password link within EasyBlog extension
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getResetPasswordLink()
	{
		$config = EB::config();
		$provider = $config->get('main_login_provider');

		$link = JRoute::_('index.php?option=com_users&view=reset');

		if ($provider == 'easysocial' && EB::easysocial()->exists()) {
			$link = ESR::account(array('layout' => 'forgetPassword'));
		}

		if ($provider == 'cb') {
			$link = JRoute::_('index.php?option=com_comprofiler&task=lostpassword');
		}

		return $link;
	}

	/**
	 * Generates the remind username link
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getRemindUsernameLink()
	{
		static $link = null;

		if (!is_null($link)) {
			return $link;
		}

		$config = EB::config();
		$provider = $config->get('main_login_provider');
		$link = JRoute::_('index.php?option=com_users&view=remind');

		if ($provider == 'easysocial' && EB::easysocial()->exists()) {
			$link = ESR::account(array('layout' => 'forgetUsername'));
		}

		return $link;
	}

	/**
	 * Legacy method for installations prior to 3.5
	 *
	 * @deprecated 3.5
	 * @since 3.5
	 */
	public function processVideos( $content , $created_by )
	{
		return EB::videos()->processVideos( $content );
	}

	/**
	 *Retrieves a list of category id that should be included
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getCategoryInclusion($categories)
	{
		if (!$categories) {
			return '';
		}

		// No filtering applied since the value is 'all'
		if (!empty($categories) && $categories == 'all') {
			return '';
		}

		// No filtering applied since the value is 'all'
		if (is_array($categories) && in_array('all', $categories)) {
			return '';
		}

		if (is_array($categories)) {
			return $categories;
		}

		$inclusion = explode(',', $categories);

		return $inclusion;
	}


	public static function uniqueLinkSegments($urls = '')
	{
		// if empty or sh404sef enabled, we dont do any process to the links. #1594
		if (!$urls || EBR::isSh404Enabled()) {
			return $urls;
		}

		$container  = explode('/', $urls);
		$container	= array_unique($container);
		$urls = implode('/', $container);

		return $urls;
	}

	/**
	 * Retrieves the base url
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getBaseUrl()
	{
		static $url = null;

		if (is_null($url)) {

			$app = JFactory::getApplication();
			$config = EB::jConfig();

			$uri = EBFactory::getURI();
			$language = $uri->getVar('lang', 'none');
			$router = $app->getRouter();

			// Ensure that the language doesn't contain any strange characters.
			$language = str_ireplace(array('"',"'"), '', $language);

			$url = rtrim(JURI::base(), '/') . '/';

			// If we should use http://site.com/index.php for the ajax calls, we need to append it here
			if (EB::config()->get('ajax_use_index')) {
				$url .= 'index.php';
			}

			$url .= '?option=com_easyblog&lang=' . $language;

			$pluginEnabled = JPluginHelper::isEnabled('system', 'languagefilter');

			// When SEF is enabled, the URL should be different otherwise Joomla will keep redirecting
			if (EBRouter::getMode() == EASYBLOG_JROUTER_MODE_SEF && $pluginEnabled && !EBR::isSh404Enabled()) {

				$sefs = JLanguageHelper::getLanguages('sef');
				$lang_codes = JLanguageHelper::getLanguages('lang_code');

				$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
				$params = new JRegistry();
				$params->loadString(empty($plugin) ? '' : $plugin->params);
				$removeLangCode = is_null($params) ? 'null' : $params->get('remove_default_prefix', 'null');

				$rewrite = $config->get('sef_rewrite');

				if ($removeLangCode) {

					$defaultLang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
					$currentLang = $app->input->cookie->getString(JApplicationHelper::getHash('language'), $defaultLang);

					$defaultSefLang = $lang_codes[$defaultLang]->sef;
					$currentSefLang = $lang_codes[$currentLang]->sef;

					if ($defaultSefLang == $currentSefLang) {
						$language = '';
					} else {
						$language = $currentSefLang;
					}

				} else {
					// Replace the path if it's on subfolders
					$base = str_ireplace(JURI::root(true), '', $uri->getPath());

					if ($rewrite) {
						$path = $base;
					} else {
						$path = EBString::substr($base, 10);
					}

					// Remove trailing / from the url
					$path = EBString::trim($path, '/');
					$parts = explode('/', $path);

					if ($parts) {
						// First segment will always be the language filter.
						$language = reset($parts);
					} else {
						$language = 'none';
					}
				}

				if ($language) {
					$language .= '/';
				}

				if ($rewrite) {
					$url = rtrim(JURI::root(), '/') . '/' . $language . '?option=com_easyblog';
				} else {
					$url = rtrim(JURI::root(), '/') . '/index.php/' . $language . '?option=com_easyblog';
				}
			}

			// Append the item id if necessary
			$activeMenu = $app->getMenu()->getActive();

			if ($activeMenu && isset($activeMenu->id)) {
				$url .= '&Itemid=' . $activeMenu->id;
			}

			// Some SEF components tries to do a 301 redirect from non-www prefix to www prefix.
			$currentUrl = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

			if ($currentUrl) {

				// When the url contains www and the current accessed url does not contain www, fix it.
				if (stristr($currentUrl, 'www') === false && stristr($url, 'www') !== false) {
					$url = str_ireplace('www.', '', $url);
				}

				// When the url does not contain www and the current accessed url contains www.
				if (stristr($currentUrl, 'www') !== false && stristr($url, 'www') === false) {
					$url = str_ireplace('://', '://www.', $url);
				}
			}
		}

		return $url;
	}

	/**
	 * Creates a new template object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function template($theme = false, $options = array())
	{
		require_once(dirname(__FILE__) . '/themes/themes.php');

		$theme = new EasyBlogThemes($theme, $options);

		return $theme;
	}

	public static function getTemplate($theme = false, $options = array())
	{
		return EB::template($theme, $options);
	}

	/**
	 * Determines if the provided theme is a legacy theme
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isLegacyTheme($theme)
	{
		$file 	= EBLOG_THEMES . '/' . $theme . '/config.json';

		$exists = JFile::exists($file);

		return !$exists;
	}

	/**
	 * Loads a list of services
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function loadServices()
	{
		$files = JFolder::files(JPATH_ROOT . '/components/com_easyblog/services', '.', false, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));

		if (!$files) {
			return;
		}

		foreach ($files as $file) {
			require_once($file);
		}

	}

	/**
	 * Check for valid token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function checkToken()
	{
		// Check for request forgeries
		JSession::checkToken('request') or jexit( 'Invalid Token' );
	}

	/**
	 * Content formatter for the blogs
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function formatter($type, $items, $cache = true, $options = array())
	{
		require_once(dirname(__FILE__) . '/formatter/formatter.php');

		$formatter 	= new EasyBlogFormatter($type, $items, $cache, $options);

		return $formatter->execute();
	}

	/**
	 * Converts to sef links
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function _($link, $xhtml = true)
	{
		require_once(dirname(__FILE__) . '/router.php');

		return EBR::_($link, $xhtml);
	}

	/**
	 * Detects if the folder exist based on the path given. If it doesn't exist, create it.
	 *
	 * @since	4.0
	 * @param	string	$path		The path to the folder.
	 * @return	boolean				True if exists (after creation or before creation) and false otherwise.
	 */
	public static function makeFolder( $path )
	{
		jimport('joomla.filesystem.folder');

		// If folder exists, we don't need to do anything
		if (JFolder::exists($path)) {
			return true;
		}

		// Folder doesn't exist, let's try to create it.
		if (JFolder::create($path)) {
			return true;
		}

		return false;
	}

	/**
	 * Converts an argument into an array.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function makeArray($item, $delimeter = null)
	{
		// If this is already an array, we don't need to do anything here.
		if (is_array($item)) {
			return $item;
		}

		// Test if source is a SocialRegistry/JRegistry object
		if ($item instanceof JRegistry) {
			return $item->toArray();
		}

		// Test if source is an object.
		if (is_object($item)) {
			return EBArrayHelper::fromObject($item);
		}

		if (is_integer($item)) {
			return array($item);
		}

		// Test if source is a string.
		if (is_string($item)) {

			if ($item == '') {
				return array();
			}

			// Test for comma separated values.
			if (!is_null($delimeter) && stristr($item, $delimeter) !== false) {
				$data = explode($delimeter, $item);

				return $data;
			}

			// Test for JSON array string
			$pattern = '#^\s*//.+$#m';
			$item = trim(preg_replace($pattern, '', $item));

			if ((substr($item, 0, 1) === '[' && substr($item, -1, 1) === ']')) {
				return json_decode($item);
			}

			// Test for JSON object string, but convert it into array
			if ((substr($item, 0, 1) === '{' && substr($item, -1, 1) === '}')) {
				$result = json_decode($item);

				return EBArrayHelper::fromObject($result);
			}

			return array($item);
		}

		return false;
	}

	/**
	 * Proxy for post library
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function post($uid = null, $revisionId = null)
	{
		require_once(dirname(__FILE__) . '/post/post.php');

		$post = new EasyBlogPost($uid, $revisionId);

		return $post;
	}

	/**
	 * Proxy for document library
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function document($json=null)
	{
		require_once(dirname(__FILE__) . '/document/document.php');

		$document = new EasyBlogDocument($json);

		return $document;
	}

	/**
	 * Proxy for location library.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function location($provider = null)
	{
		require_once(dirname(__FILE__) . '/location/location.php');

		$service = new EasyBlogLocation($provider);

		return $service;
	}

	/**
	 * Retrieves the profiler
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function profiler()
	{
		static $profiler = null;

		if (!$profiler) {
			require_once(__DIR__ . '/profiler/profiler.php');

			$profiler = new EasyBlogProfiler();
		}

		return $profiler;
	}

	/**
	 * Determines if language association is enabled
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isAssociationEnabled()
	{
		static $enabled = null;

		if (is_null($enabled)) {

			// Default to disabled
			$enabled = JLanguageMultilang::isEnabled();

			if ($enabled) {
				$params = new JRegistry(JPluginHelper::getPlugin('system', 'languagefilter')->params);
				$enabled = (boolean) $params->get('item_associations', true);
			}
		}

		return $enabled;
	}

	/**
	 * determined if falang activated.
	 *
	 * @since	5.0.42
	 * @access	public
	 */
	public static function isFalangActivated()
	{
		static $_cache = null;

		if (is_null($_cache)) {

			$pluginInstalled = JPluginHelper::getPlugin('system', 'falangdriver');
			$pluginEnabled = JPluginHelper::isEnabled('system', 'falangdriver');

			$exists = JFile::exists(JPATH_ADMINISTRATOR . '/components/com_falang/classes/FalangManager.class.php');
			$_cache = false;

			if ($pluginInstalled && $pluginEnabled && $exists) {
				$_cache = true;
			}
		}

		return $_cache;
	}

	/**
	 * cache for post related items.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function cache()
	{
		static $cache = null;

		if (!$cache) {
			require_once(__DIR__ . '/cache/cache.php');

			$cache = new EasyBlogCache();
		}

		return $cache;
	}

	/**
	 * math lib
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function math()
	{
		static $math = null;

		if (!$math) {
			require_once(__DIR__ . '/math/math.php');

			$math = new EasyBlogMath();
		}

		return $math;
	}

	/**
	 * function to the column collation that compatible with Joomla 3.5 jos_users table collation.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getUsersTableCollation($from)
	{
		static $collationType = array();

		if (!isset($collationType[$from])) {

			// Default value
			$collationType[$from] = '';

			$jVersion = EB::getJoomlaVersion();

			if ($jVersion >= '3.5') {
				$jConfig = EB::jconfig();
				$dbType = $jConfig->get('dbtype');

				if ($dbType == 'mysql' || $dbType == 'mysqli' || $dbType == 'pdomysql') {
					$db = EB::db();
					$dbversion = $db->getVersion();
					$dbversion = (float) $dbversion;

					if ($dbversion >= '5.1') {

						$prefix = $db->getPrefix();

						$tableName = $prefix . 'users';

						$query = "SHOW TABLE STATUS WHERE `Name` = " . $db->Quote($tableName);
						$db->setQuery($query);
						$result = $db->loadObject();

						$collation = $result->Collation;

						if (strpos($collation, 'mb4_') !== false) {

							if ($from == 'eb') {
								$tableName2 = $prefix . 'easyblog_subscriptions';

								$query = "SHOW TABLE STATUS WHERE `Name` = " . $db->Quote($tableName2);
								$db->setQuery($query);
								$result = $db->loadObject();

								$collation2 = $result->Collation;

								if (strpos($collation2, 'mb4_') !== false) {
									$collationType[$from] = 'COLLATE utf8mb4_unicode_ci';
								} else {
									$collationType[$from] = 'COLLATE utf8_unicode_ci';
								}
							} else {
								$collationType[$from] = 'COLLATE utf8mb4_unicode_ci';
							}

						} else if ($db->hasUTFSupport()) {
							$collationType[$from] = 'COLLATE utf8_unicode_ci';
						}
					}
				}
			}
		}

		return $collationType[$from];
	}


	/**
	 * This method will intelligently determine which menu params this post should be inheriting from
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getMenuParams($id, $type, $menuParams = false, $debug = false)
	{
		static $items = array();

		if (!isset($items[$id])) {

			$config = EB::config();

			$model = EB::model('Menu');

			// If there is an article menu item associated with this post, use this
			$menuId = 0;

			if ($type == 'listing') {
				$listingParams = $model->getDefaultXMLParams();
				$arrTmpParams = $listingParams->toArray();

				foreach ($arrTmpParams as $key => $val) {
					$listingParams->set($key, $config->get($type . '_' . $key));
				}

				$items[$id] = $listingParams;
				return $items[$id];
			}

			if ($type == 'categories' || $type == 'category') {
				$menuLayout = 'default';

				if ($type == 'category') {
					$menuLayout = 'listings';
					$menuId = $model->getMenusByCategoryId($id);
				} else {
					$menuId = $model->getMenus('categories');
				}

				$defaultParams = $model->getXMLParams('categories', $menuLayout);
			}

			if ($type == 'tag') {
				$menuId = $model->getMenusByTagId($id);
				$defaultParams = $model->getXMLParams('tags', 'tag');
			}

			if ($type == 'blogger' || $type == 'bloggers') {
				$menuLayout = 'default';

				if ($type == 'blogger') {
					$menuLayout = 'listings';
					$menuId = $model->getMenusByBloggerId($id);
				} else {
					$menuId = $model->getMenus('blogger');
				}

				$defaultParams = $model->getXMLParams('blogger', $menuLayout);
			}

			if ($menuId) {
				$params = $model->getMenuParamsById($menuId);
				$arrParams = $params->toArray();

				foreach ($arrParams as $key => $value) {
					if ($value == '-1') {
						$params->set($key, $config->get($type . '_' . $key));
					}
				}

				$items[$id] = $params;
			} else {
				$arrTmpParams = $defaultParams->toArray();

				foreach ($arrTmpParams as $key => $value) {
					$defaultParams->set($key, $config->get($type . '_' . $key));
				}

				$items[$id] = $defaultParams;
			}
		}

		return $items[$id];
	}

	/**
	 * Normalize directory separator
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function normalizeSeparator($path)
	{
		$path = str_ireplace(array( '\\' ,'/' ) , '/' , $path);

		return $path;
	}

	/**
	 * Set callback url in joomla session
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function setCallback($data)
	{
		$session = JFactory::getSession();

		// Serialize the callback data.
		$data = serialize($data);

		// Store the data
		$session->set('easyblog.callback', $data, EBLOG_SESSION_NAMESPACE);
	}

	/**
	 * Retrieve callback url from joomla session
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getCallback($default = '', $resetSession = true)
	{
		$session = JFactory::getSession();
		$data = $session->get('easyblog.callback', '', EBLOG_SESSION_NAMESPACE);

		$data = unserialize($data);

		// Clear off the session once it's been picked up.
		if ($resetSession) {
			$session->clear('easyblog.callback', EBLOG_SESSION_NAMESPACE);
		}

		if (!$data && $default) {
			return $default;
		}

		return $data;
	}

	/**
	 * Determine whether the site enable multilingual.
	 *
	 * @since	5.1.14
	 * @access	public
	 */
	public static function isSiteMultilingualEnabled()
	{
		// check if the languagefilter plugin enabled
		$pluginEnabled = JPluginHelper::isEnabled('system', 'languagefilter');

		return $pluginEnabled;
	}

	/**
	 * Give a proper redirection when the user does not have the permission to view item.
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function getErrorRedirection($message = null)
	{
		$config = EB::config();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!$message) {
			$message = JText::_('COM_EASYBLOG_NOT_ALLOWED_TO_PERFORM_ACTION');
		}

		$redirection = $config->get('system_error_redirection', false);

		if ($redirection) {
			EB::requireLogin();

			EB::info()->set($message, 'error');
			return $app->redirect(EBR::_('index.php?option=com_easyblog&view=latest'), false);
		}

		// default redirection
		return JError::raiseError(404, $message);
	}

	/**
	 * Reads a XML file.
	 *
	 * @since   5.2
	 * @access  public
	 */
	 public static function getXml($data, $isFile = true)
	 {
		$class = 'SimpleXMLElement';

		if (class_exists('JXMLElement')) {
			$class = 'JXMLElement';
		}

		if ($isFile) {
			// Try to load the XML file
			$xml = simplexml_load_file($data, $class);

		} else {
			// Try to load the XML string
			$xml = simplexml_load_string($data, $class);
		}

		if ($xml === false) {
			foreach (libxml_get_errors() as $error) {
				echo "\t", $error->message;
			}
		}

		return $xml;
	 }

	 /**
	  * Retrieves logo
	  *
	  * @since	5.1
	  * @access	public
	  */
	 public static function getLogo($type = 'email', $forceDefault = false)
	 {
		$config = EB::config();
		$logo = rtrim(JURI::root(), '/') . '/media/com_easyblog/images/' . $type . '/logo.png';

		if ($forceDefault || ($type == 'email' && !$config->get('custom_email_logo'))) {
			return $logo;
		}

		if (EB::hasOverrideLogo($type)) {
			$override = EB::getOverridePath($type);
			$override = rtrim(JURI::root(), '/') . $override;
			return $override;
		}

		return $logo;
	 }

	 /**
	  * Determine if custom logo is exists
	  *
	  * @since	5.1
	  * @access	public
	  */
	 public static function hasOverrideLogo($type)
	 {
		$path = JPATH_ROOT . EB::getOverridePath($type);

		if (JFile::exists($path)) {
			return true;
		}

		return false;
	 }

	 /**
	  * Get override path for email logo
	  *
	  * @since	5.1
	  * @access	public
	  */
	 public static function getOverridePath($type)
	 {
		// Get current template
		$defaultJoomlaTemplate = EB::getCurrentTemplate();

		$path = '/templates/' . $defaultJoomlaTemplate . '/html/com_easyblog/' . $type . '/logo.png';

		return $path;
	 }
}


class EasyBlogHelper extends EB {}


