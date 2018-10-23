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

class EasyBlogAjax extends EasyBlog
{
	/**
	 * Generates the ajax url
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getUrl()
	{
		static $url;

		if (isset($url)) {
			return $url;
		}

		$uri = EBFactory::getURI();
		$language = $uri->getVar('lang', 'none');

		// Remove any ' or " from the language because language should only have -
		$app = JFactory::getApplication();
		$input = $app->input;

		$language = $input->get('lang', '', 'cmd');

		$jConfig = EB::jconfig();

		// Get the router
		$router = $app->getRouter();

		// It could be admin url or front end url
		$url = rtrim(JURI::base(), '/') . '/';

		// Determines if we should use index.php for the url
		if ($this->config->get('ajax_use_index')) {
			$url .= 'index.php';
		}

		// Append the url with the extension
		$url = $url . '?option=com_easyblog&lang=' . $language;

		// During SEF mode, we need to ensure that the URL is correct.
		$languageFilterEnabled = JPluginHelper::isEnabled("system","languagefilter");

		if (EBRouter::getMode() == EASYBLOG_JROUTER_MODE_SEF && $languageFilterEnabled) {

			$sefs = JLanguageHelper::getLanguages('sef');
			$lang_codes = JLanguageHelper::getLanguages('lang_code');

			$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
			$params = new JRegistry();
			$params->loadString(empty($plugin) ? '' : $plugin->params);
			$removeLangCode = is_null($params) ? 'null' : $params->get('remove_default_prefix', 'null');

			// Determines if the mod_rewrite is enabled on Joomla
			// $rewrite = $jConfig->getValue('sef_rewrite');
			$rewrite = $jConfig->get('sef_rewrite');

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

			if ($rewrite) {
				$url = rtrim(JURI::root(), '/') . '/' . $language . '?option=com_easyblog';
			} else {
				$url = rtrim(JURI::root(), '/') . '/index.php/' . $language . '?option=com_easyblog';
			}
		}

		$menu = JFactory::getApplication()->getmenu();

		if (!empty($menu)) {
			$item = $menu->getActive();

			if (isset($item->id)) {
				$url .= '&Itemid=' . $item->id;
			}
		}

		// Some SEF components tries to do a 301 redirect from non-www prefix to www prefix. Need to sort them out here.
		$currentURL = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

		if (!empty($currentURL)) {

			// When the url contains www and the current accessed url does not contain www, fix it.
			if (stristr($currentURL, 'www') === false && stristr($url, 'www') !== false) {
				$url = str_ireplace('www.', '', $url);
			}

			// When the url does not contain www and the current accessed url contains www.
			if (stristr($currentURL, 'www') !== false && stristr($url, 'www') === false) {
				$url = str_ireplace('://', '://www.', $url);
			}
		}

		return $url;
	}

	/**
	 * Processes ajax calls made on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function process()
	{
		// Get the namespace
		$namespace = $this->input->get('namespace', '', 'default');

		// Determines if this is an ajax call made to the site
		$isAjaxCall = $this->input->get('format', '', 'cmd') == 'ajax' && !empty($namespace);

		// If this is not an ajax call, there's no point proceeding with this.
		if(!$isAjaxCall) {
			return false;
		}

		// Process namespace string.
		// Legacy uses '.' as separator, we need to replace occurences of '.' with /
		$namespace = str_ireplace('.', '/', $namespace);
		$namespace = explode('/', $namespace);

		// @rule: All calls should be made a minimum out of 3 parts of dots (.)
		if (count($namespace) < 4) {
			$this->fail(JText::_('COM_EASYBLOG_INVALID_AJAX_CALL'));
			return $this->send();
		}

		/**
		 * Namespaces are broken into the following
		 *
		 * site/views/viewname/methodname - Front end ajax calls
		 * admin/views/viewname/methodname - Back end ajax calls
		 */
		list($location, $type, $name, $method) = $namespace;

		if ($type != 'views' && $type != 'controllers') {
			$this->fail(JText::_('Ajax calls are currently only serving views and controllers.'));
			return $this->send();
		}

		// Get the location
		$location = strtolower($location);
		$name = strtolower($name);

		$path = $location == 'admin' ? JPATH_ROOT . '/administrator' : JPATH_ROOT;
		$path .= '/components/com_easyblog';

		if ($type == 'views') {
			$path .= '/' . $type . '/' . $name . '/view.ajax.php';
		}

		if ($type == 'controllers') {
			$path .= '/' . $type . '/' . $name . '.php';
		}


		$classType = $type == 'views' ? 'View' : 'Controller';
		$class = 'EasyBlog' . $classType . preg_replace('/[^A-Z0-9_]/i', '', $name);

		if (!class_exists($class)) {

			jimport('joomla.filesystem.file');

			$exists = JFile::exists($path);

			if (!$exists) {
				$this->fail(JText::_('File does not exist.'));
				return $this->send();
			}

			require_once($path);
		}

		$obj = new $class();

		if (!method_exists($obj, $method)) {
			$this->fail(JText::sprintf('The method %1s does not exists.', $method));
			return $this->send();
		}

		// Call the method
		$obj->$method();

		return $this->send();
	}

	/**
	 * Allows caller to add commands to the ajax response chain
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function addCommand($type, &$data)
	{
		// Convert any exceptions to array
		foreach ($data as &$arg) {

			if ($arg instanceof EasyBlogException) {

				// Display console messages on javascript if neeeded
				if ($this->config->get('main_environment') == 'development') {
					$this->script('console.warn(' . $arg->toJSON() . ');');
				}

				$arg = $arg->toArray();
			}
		}

		$this->commands[] = array('type' => $type, 'data' => &$data);

		return $this;
	}

	/* This will handle all ajax commands e.g. success/fail/script */
	public function __call($method, $args)
	{
		$this->addCommand($method, $args);

		return $this;
	}

	public function verifyAccess($allowGuest=false)
	{
		if (!EB::checkToken()) {
			$this->reject(EB::exception('Invalid token'));
			$this->send();
		}

		if (!$allowGuest) {
			$my = JFactory::getUser();
			if ($my->guest) {
				$this->reject(EB::exception('You are not logged in!'));
				$this->send();
			}
		}
	}

	public function send()
	{
		// Isolate PHP errors and send it using notify command.
		$error_reporting = ob_get_contents();
		if (strlen(trim($error_reporting))) {
			$this->notify($error_reporting, 'debug');
		}
		ob_clean();

		// JSONP transport
		$callback = $this->input->get('callback', '');
		if ($callback) {
			header('Content-type: application/javascript; UTF-8');
			echo $callback . '(' . json_encode($this->commands) . ');';
			exit;
		}

		// IFRAME transport
		$transport = $this->input->get('transport');
		if ($transport=="iframe") {
			header('Content-type: text/html; UTF-8');
			echo '<textarea data-type="application/json" data-status="200" data-statusText="OK">' . json_encode($this->commands) . '</textarea>';
			exit;
		}

		// XHR transport
		header('Content-type: text/x-json; UTF-8');
		echo json_encode($this->commands);
		exit;
	}
}
