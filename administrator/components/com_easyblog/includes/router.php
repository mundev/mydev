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

jimport('joomla.filter.filteroutput');

class EBR extends EasyBlog
{
	static $posts = array();

	/**
	 * Retrieve all views used in EasyBlog frontend.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function getSystemViews($translate = true)
	{
		static $sysViews = array();

		// $sysViews = array('archive', 'blogger', 'calendar', 'categories', 'comments', 'composer', 'crawler', 'dashboard',
		// 				'download', 'entry', 'featured', 'grid', 'latest', 'login', 'magazine', 'mediamanager', 'myblog',
		// 				'quickpost', 'ratings', 'reports', 'revisions', 'rsd', 'search', 'subscription', 'tags', 'teamblog',
		// 				'templates', 'xmlrpc'
		// 			);

		$idx = 'views_' . (int) $translate;

		if (! isset($sysViews[$idx])) {

			$files = JFolder::folders(JPATH_ROOT . '/components/com_easyblog/views');

			$sysViews[$idx] = $files;

			if ($translate) {

				$views = array();

				foreach ($files as $file) {
					$views[] = EBR::translate($file);
				}

				$sysViews[$idx] = $views;
			}
		}

		return $sysViews[$idx];
	}

	/**
	 * Retrieve all menu's from the site associated with EasyBlog
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getMenus($view, $layout = null, $id = null, $lang = null)
	{
		static $menus = null;
		static $selection = array();

		// Always ensure that layout is lowercased
		if (!is_null($layout)) {
			$layout = strtolower($layout);
		}

		// We want to cache the selection user made.
		// $key = $view . $layout . $id;
		$language = false;
		$languageTag = JFactory::getLanguage()->getTag();

		// If language filter is enabled, we need to get the language tag
		if (!JFactory::getApplication()->isAdmin()) {
			$language = JFactory::getApplication()->getLanguageFilter();
			$languageTag = JFactory::getLanguage()->getTag();
		}

		// var_dump($lang);
		if ($lang) {
			$languageTag = $lang;
		}

		$key = $view . $layout . $id . $languageTag;

		// Preload the list of menus first.
		if (is_null($menus)) {

			$model = EB::model('Menu');
			$result = $model->getAssociatedMenus();

			if (!$result) {
				return $result;
			}

			$menus = array();

			foreach ($result as $row) {

				// Remove the index.php?option=com_easyblog from the link
				$tmp = str_ireplace('index.php?option=com_easyblog', '', $row->link);

				// Parse the URL
				parse_str($tmp, $segments);

				// Convert the segments to std class
				$segments = (object) $segments;

				// if there is no view, most likely this menu item is a external link type. lets skip this item.
				if(!isset($segments->view)) {
					continue;
				}

				$menu = new stdClass();
				$menu->segments = $segments;
				$menu->link = $row->link;
				$menu->view = $segments->view;
				$menu->layout = isset($segments->layout) ? $segments->layout : 0;

				if (!$menu->layout && $menu->view == 'entry') {
					$menu->layout = 'entry';
				}

				$menu->id = $row->id;

				// var_dump($row->language);

				// this is the safe step to ensure later we will have atlest one menu item to retrive.
				$menus[$menu->view][$menu->layout]['*'][] = $menu;
				$menus[$menu->view][$menu->layout][$row->language][] = $menu;
			}

		}

		// Get the current selection of menus from the cache
		if (!isset($selection[$key])) {

			// Search for $view only. Does not care about layout nor the id
			if (isset($menus[$view]) && isset($menus[$view]) && is_null($layout)) {
				if (isset($menus[$view][0][$languageTag])) {
					$selection[$key] = $menus[$view][0][$languageTag];
				} else if (isset($menus[$view][0]['*'])) {
					$selection[$key] = $menus[$view][0]['*'];

				} else {
					$selection[$key] = false;
				}

			}


			// Searches for $view and $layout only.
			if (isset($menus[$view]) && isset($menus[$view]) && !is_null($layout) && isset($menus[$view][$layout]) && (is_null($id) || empty($id))) {
			$selection[$key] = isset($menus[$view][$layout][$languageTag]) ? $menus[$view][$layout][$languageTag] : $menus[$view][$layout]['*'];
			}

			// // view=entry is unique because it doesn't have a layout
			// if ($view == 'entry') {
			//     dump($layout, $selection[$key]);
			// }

			// Searches for $view $layout and $id
			if (isset($menus[$view]) && !is_null($layout) && isset($menus[$view][$layout]) && !is_null($id) && !empty($id)) {

				$found = false;
				if ($languageTag != '*' && isset($menus[$view][$layout][$languageTag])) {
					$tmp = $menus[$view][$layout][$languageTag];

					foreach ($tmp as $tmpMenu) {
						// Backward compatibility support. Try to get the ID from the new alias style, ID:ALIAS
						$parts = explode(':', $id);
						$legacyId = null;

						if (count($parts) > 1) {
							$legacyId = $parts[0];
						}

						if (isset($tmpMenu->segments->id) && ($tmpMenu->segments->id == $id || $tmpMenu->segments->id == $legacyId)) {
							$found = true;
							$selection[$key] = array($tmpMenu);
							break;
						}
					}
				}

				// in some situation where there are records in $menus[$view][$layout][$languageTag] but the correct item actually fall under
				// $menus[$view][$layout][*]. Due to this reason, we have no choice but to loop through all. #131
				if (! $found) {
					$tmp = $menus[$view][$layout]['*'];

					foreach ($tmp as $tmpMenu) {

						// Backward compatibility support. Try to get the ID from the new alias style, ID:ALIAS
						$parts = explode(':', $id);
						$legacyId = null;

						if (count($parts) > 1) {
							$legacyId = $parts[0];
						}

						if (isset($tmpMenu->segments->id) && ($tmpMenu->segments->id == $id || $tmpMenu->segments->id == $legacyId)) {
							$found = true;
							$selection[$key] = array($tmpMenu);
							break;
						}
					}

				}

			}

			// If we still can't find any menu, skip this altogether.
			if (!isset($selection[$key])) {
				$selection[$key] = false;
			}

			// Flatten the array so that it would be easier for the caller.
			if (is_array($selection[$key])) {
				$selection[$key] = $selection[$key][0];
			}
		}

		return $selection[$key];
	}

	/**
	 * Generates a permalink given a string
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function normalizePermalink($string)
	{
		$config = EB::config();
		$permalink = '';

		if (EBR::isSefEnabled() && $config->get('main_sef_unicode')) {
			$permalink = JFilterOutput::stringURLUnicodeSlug($string);
			return $permalink;
		}

		// Replace accents to get accurate string
		$string = EBR::replaceAccents($string);

		// no unicode supported.
		$permalink = JFilterOutput::stringURLSafe($string);

		// check if anything return or not. If not, then we give a date as the alias.
		if (trim(str_replace('-','',$permalink)) == '') {
			$date = EB::date();
			$permalink = $date->format("%Y-%m-%d-%H-%M-%S");
		}

		return $permalink;
	}

	/**
	 * Generates the query string for language selection.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getLanguageQuery($concate = 'AND', $column = 'language')
	{
		$language = JFactory::getLanguage();
		$tag = $language->getTag();
		$query = '';

		$concate = (! $concate) ? 'AND' : $concate;
		$column = (! $column) ? 'language' : $column;


		if (!empty($tag) && $tag != '*') {
			$db = EB::db();
			$query = ' ' . $concate . ' (' . $db->qn($column) . '=' . $db->Quote($tag) . ' OR ' . $db->qn($column) . '=' . $db->Quote('') . ' OR ' . $db->qn($column) . '=' . $db->Quote('*') . ')';
		}

		return $query;
	}

	/**
	 * Assign a post statically so that we can retrieve it without loading
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function setPost(EasyBlogPost $post)
	{
		EBR::$posts[(int) $post->id] = $post;
	}

	/**
	 * Get site langauge code
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getSiteLanguageTag($langSEF)
	{
		static $cache = null;

		if (is_null($cache)) {
			$db = EB::db();

			$query = "select * from #__languages";
			$db->setQuery($query);

			$results = $db->loadObjectList();

			if ($results) {
				foreach($results as $item) {
					$cache[$item->sef] = $item->lang_code;
					$cache[$item->lang_code] = $item->sef;
				}
			}
		}

		if (isset($cache[$langSEF])) {
			return $cache[$langSEF];
		}

		return $langSEF;
	}

	/**
	 * Converts the non sef links to SEF links when necessary
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function _($url, $xhtml = true, $ssl = null, $search = false, $isCanonical = false, $jRouted = true)
	{
		static $cache = array();
		static $itemIds = array();

		// Cache index
		$key = $url . (int) $xhtml . (int) $isCanonical . (int) $jRouted;

		// If the url has already loaded previously, do not need to load it again.
		if (isset($cache[$key])) {
			return $cache[$key];
		}

		$config = EB::config();
		$app = JFactory::getApplication();
		$input = $app->input;

		// Parse the url
		parse_str($url, $query);

		// Get the view portion from the query string
		$view = isset($query['view']) ? $query['view'] : 'latest';
		$layout = isset($query['layout']) ? $query['layout'] : null;
		$itemId = isset($query['Itemid']) ? $query['Itemid'] : '';
		$task = isset($query['task']) ? $query['task'] : '';
		$id = isset($query['id']) ? $query['id'] : null;
		$sort = isset($query['sort']) ? $query['sort'] : null;
		$lang = isset($query['lang']) ? $query['lang'] : null;
		$search = isset($query['search']) ? $query['search'] : null;
		$ordering = isset($query['ordering']) ? $query['ordering'] : null;
		$return = isset($query['return']) ? $query['return'] : null;
		$query = isset($query['query']) ? $query['query'] : null;

		// we know the lang that we passed in is the short tag. we need to get the full tag. e.g. en-GB
		if ($lang) {
			$lang = EBR::getSiteLanguageTag($lang);
		}

		$multiLingualEnabled = false;
		$multiLingualRemoveLang = false;

		// if this is a canonical link for post sef #1038
		if ($isCanonical && $view == 'entry' && $id && !$lang) {

			// we need to check if this post has language configured or not.
			// if no, we need to default to site language if multilinguage enabled.
			$multiLingualEnabled = JPluginHelper::isEnabled('system', 'languagefilter');
			if ($multiLingualEnabled) {

				$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
				$params = new JRegistry();
				$params->loadString(empty($plugin) ? '' : $plugin->params);
				$multiLingualRemoveLang = is_null($params) ? 'null' : $params->get('remove_default_prefix', 'null');

				// Get the post data from the cache
				$postCache = EB::cache();
				$post = $postCache->get($id, 'post');

				if ($post->language == '*' || !$post->language) {
					// lets use default language.
					$lang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
				} else {
					$lang = $post->language;
				}

				$langtag = EBR::getSiteLanguageTag($lang);

				$url = $url . '&lang=' . $langtag;
			}
		}

		$dropSegment = false;

		// Get routing behavior
		$behavior = $config->get('main_routing', 'default');

		// we no longer support currentactive in 5.0. lets set to 'default' if this is an upgrade from 3.9 .
		$behavior = ($behavior == 'currentactive') ? 'default' : $behavior;

		// settings for "use menu id"
		if ($behavior == 'menuitemid') {

			// Get the menu id from the settings
			$itemId = $config->get('main_routing_itemid');
			if (! $itemId) {
				// if admin did not specify any item id, lets fall back to default style.
				$behavior = 'default';
			}
		}

		// Default routing behavior
		if ($behavior == 'default') {

			// The default menu in the event we can't find anything for the url
			$defaultMenu = EBR::getMenus('latest', null, null, $lang);

			if ($defaultMenu === false) {
				// the site has no menu item created for 'latest' view. Lets try grid view.
				$defaultMenu = EBR::getMenus('grid', null, null, $lang);

				// try magazine view.
				if ($defaultMenu === false) {
					$defaultMenu = EBR::getMenus('magazie', null, null, $lang);
				}

				if ($defaultMenu === false) {
					// the site has no menu item created for 'latest' view. Lets try all categories view.
					$defaultMenu = EBR::getMenus('categories', null, null, $lang);
				}

				// thats strange. lets try further. single category layout.
				if ($defaultMenu === false) {
					$defaultMenu = EBR::getMenus('categories', 'listings', null, $lang);
				}
			}

			// Entry view needs to be treated differently.
			if ($view == 'entry' && !$layout) {

				// Respect which settings the user configured
				$respectView = $config->get('main_routing_entry');

				// Entry view has higher precedence over all
				$menu = EBR::getMenus('entry', 'entry', $id, $lang);

				if ($menu) {
					$dropSegment = true;
				} else {

					// Get the post data from the cache
					$postCache = EB::cache();
					$post = $postCache->get($id, 'post');

					// Get the category the post is created in
					if ($respectView == 'categories') {
						$menu = EBR::getMenus('categories', 'listings', $post->category_id, $lang);
					}

					if ($respectView == 'blogger') {
						$menu = EBR::getMenus('blogger', 'listings', $post->created_by, $lang);
					}

					if ($respectView == 'teamblog' && $post->source_type == EASYBLOG_POST_SOURCE_TEAM) {
						$menu = EBR::getMenus('teamblog', 'listings', $post->source_id, $lang);
					}
				}
			}

			// Get the default menu that the current view should use
			if (($view != 'entry' || ($view == 'entry' && $layout == 'preview')) && $view != 'composer') {
				$menu = EBR::getMenus($view);

				// If there's a layout an id accompanying the view, we should search for a menu to a single item layout.
				if ($layout && $id) {
					$itemMenu = EBR::getMenus($view, $layout, $id);

					// If there's a menu item created on the site associated with this item, we need to drop the segment
					// to avoid redundant duplicate urls.
					// E.g:
					// menu alias = test
					// post alias = test
					// result = /test/test

					if ($itemMenu) {
						$menu = $itemMenu;
						$dropSegment = true;
					}
				} else if ($layout) {

					// this section here is to cater a view + layout page.
					// e.g dashboard/entries

					$itemMenu = EBR::getMenus($view, $layout);

					if ($itemMenu) {
						$menu = $itemMenu;
						$dropSegment = true;
					}
				}

				// If there is a menu created for the view, we just drop the segment
				if (!$layout && !$id && $menu) {
					$dropSegment = true;
				}

				// Some query strings may have "sort" in them.
				if ($sort) {
					$dropSegment = false;
				}

				// Some query strings may have "search" in them.
				if ($search) {
					$dropSegment = false;
				}

				// Some query strings may have "query" in them to represent search
				if ($query) {
					$dropSegment = false;
				}

				if ($ordering) {
					$dropSegment = false;
				}
			}

			// If we still cannot find any menu, use the default menu :(
			if (!isset($menu) || !$menu) {
				$menu = $defaultMenu;
			}

			// Only proceed when there is at least 1 menu created on the site for EasyBlog
			if (isset($menu) && $menu) {
				$itemId = $menu->id;
			}

			// If this is a task, we shouldn't drop any segments at all
			if ($task) {
				$dropSegment = false;
			}
		}

		// If there's an item id located for the url, we need to intelligently apply it into the url.
		if ($itemId) {

			// We need to respect dropSegment to avoid duplicated menu and view name.
			// For instance, if a menu is called "categories" which links to the categories page, it would be /categories/categories
			if ($dropSegment && EBR::isSefEnabled()) {
				$url = 'index.php?Itemid=' . $itemId;

				// Append return url if exists
				if ($return) {
					$url .= '&return=' . $return;
				}
			} else {
				$url = EBR::appendItemIdToQueryString($url, $itemId);
			}
		}


		$itemUrl = ($jRouted) ? JRoute::_($url, $xhtml, $ssl) : $url;

		// #1084
		if ($jRouted && $isCanonical && $multiLingualRemoveLang && $lang) {
			$defaultSiteLang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
			if ($lang == $defaultSiteLang) {
				$defaultLangTag = EBR::getSiteLanguageTag($lang);
				$itemUrl = EBString::str_ireplace('/' . $defaultLangTag . '/', '/', $itemUrl);
			}
		}

		$cache[$key] = $itemUrl;
		return $cache[$key];
	}

	/**
	 * Appends a fragment to the url as it would intelligent detect if it should use & or ? to join the query string
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function appendFormatToQueryString($url, $format = null)
	{
		if (!$format) {
			return $url;
		}

		if (EBR::isSefEnabled()) {
			$url .= '?format=' . $format;

			return $url;
		}

		$url .= '&format=' . $format;

		return $url;
	}

	/**
	 * Fixes a URL if it contains anchor links
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function appendItemIdToQueryString($url, $itemId)
	{
		$itemId = '&Itemid=' . $itemId;
		$anchor = EBString::strpos($url, '#');

		if ($anchor === false) {
			$url .= $itemId;

			return $url;
		}

		$url = EBString::str_ireplace('#', $itemId . '#', $url);

		return $url;
	}

	/**
	 * Determiens if SEF is enabled on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function isSefEnabled()
	{
		$jConfig = EB::jConfig();
		$isSef = false;
		$isSef = EBR::isSh404Enabled();

		// If sh404sef not enabled, we need to check if joomla has it enabled
		if (!$isSef) {
			$isSef = $jConfig->get('sef');
		}

		return $isSef;
	}

	/**
	 * Determiens we should include ID into permalink or not.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public static function isIDRequired()
	{
		$config = EB::config();
		$db = EB::db();

		if (!EBR::isSefEnabled() || $config->get('main_sef_useid')) {
			return true;
		}

		if ($config->get('main_sef_unicode') && !$db->hasUTF8mb4Support()) {
			return true;
		}

		return false;
	}

	/**
	 * Due to the fact that SH404 doesn't rewrite urls from the back end, we need to check if they exist
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function isSh404Enabled()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php';
		$enabled = false;

		if (defined('SH404SEF_AUTOLOADER_LOADED') && JFile::exists($file)) {
			require_once($file);

			if (class_exists('shRouter')) {
				$sh404Config = shRouter::shGetConfig();

				if ($sh404Config->Enabled) {
					$enabled = true;
				}
			}
		}

		return $enabled;
	}

	/**
	 * Retrieves the custom permalink
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getCustomPermalink(EasyBlogPost $post)
	{
		$config = EB::config();
		$custom = $config->get('main_sef_custom');

		$date = EB::date($post->created);

		$postPermalink = $post->permalink;

		if (EBR::isIDRequired()) {
			$postPermalink = $post->id . '-' . $postPermalink;
		}

		$fallback = $date->toFormat('%Y') . '/' . $date->toFormat('%m') . '/' . $date->toFormat('%d') . '/' . $postPermalink;

		// If the user didn't enter any values for the custom sef, we'll just load the default one which is the 'date' based
		if (!$custom) {
			return $fallback;
		}

		// Break down parts of the url defined by the admin
		$pieces = explode('/', $custom);

		if (!$pieces) {
			return $fallback;
		}

		$result = array();

		foreach ($pieces as $piece) {

			$piece = str_ireplace('%year_num%', $date->format('Y'), $piece);
			$piece = str_ireplace('%month_num%', $date->format('m'), $piece);
			$piece = str_ireplace('%day_num%', $date->format('d'), $piece);
			$piece = str_ireplace('%day%', $date->format('A'), $piece);
			$piece = str_ireplace('%month%', $date->format('b'), $piece);
			$piece = str_ireplace('%blog_id%', $post->id, $piece);
			$piece = str_ireplace('%category%', $post->getPrimaryCategory()->getAlias(), $piece);
			$piece = str_ireplace('%category_id%', $post->getPrimaryCategory()->id, $piece);

			$result[] = $piece;
		}

		$url = implode('/', $result);
		$url .= '/' . $postPermalink;

		return $url;
	}


	/**
	 * Retrieves the external url
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getRoutedURL($url, $xhtml = false, $external = false, $isCanonical = false, $forceRouted = false)
	{
		// If this is not an external link, just pass it to joomla's router
		if (!$external) {
			return EBR::_($url, $xhtml, null, false, $isCanonical);
		}

		$app = JFactory::getApplication();
		$uri = JURI::getInstance();
		$config = EB::jConfig();

		$dashboard = false;

		// Check if the current menu view is pointing to the dashboard view
		if (!$app->isAdmin()) {

			$menu = JFactory::getApplication()->getMenu()->getActive();

			if (isset($menu->link) && $menu->link) {
				$pos = strpos($menu->link, 'view=dashboard');

				if ($pos !== false) {
					$dashboard = true;
				}
			}
		}

		// Address issues with JRoute as it will include the /administrator/ portion in the url if this link
		// is being generated from the back end.
		if ($app->isAdmin() && EBR::isSefEnabled()) {

			$base64Url = base64_encode($url);

			// lets send to frontend to process the sef link.
			$targetUrl = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&task=route.sef&url=' . $base64Url;

			$connector = EB::connector();
			$connector->addUrl($targetUrl);
			$connector->execute();
			$response = $connector->getResult($targetUrl);

			$data = json_decode($response);

			if (($data && isset($data->link) && $data->link) || $forceRouted) {
				$routedUrl = $data->link;
				$routedUrl = $uri->toString(array('scheme', 'host', 'port')) . '/' . ltrim($routedUrl, '/');

				return $routedUrl;
			}

			return $url;
		}

		$url = EBR::_($url, $xhtml, null, $dashboard, $isCanonical);
		$url = str_replace('/administrator/', '/', $url);
		$url = ltrim($url, '/');

		// We need to use $uri->toString() because JURI::root() may contain a subfolder which will be duplicated
		// since $url already has the subfolder.
		return $uri->toString(array('scheme', 'host', 'port')) . '/' . $url;
	}

	/**
	 * Better method to replace accents rather than relying on JFilter
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function replaceAccents($string)
	{
		$a = array('Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß' , 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
		$b = array('AE', 'ae', 'OE', 'oe', 'UE', 'ue', 'ss', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');

		return str_replace($a, $b, $string);
	}

	/**
	 * Get menu item based on entry view
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getItemIdByEntry($blogId)
	{
		static $entriesItems	= null;

		if(!isset($entriesItems[ $blogId ]))
		{
			$db		= EB::db();

			// We need to check against the correct latest entry to be used based on the category this article is in
			$query	= 'SELECT ' . $db->nameQuote('id') . ',' . $db->nameQuote('params') . ' FROM ' . $db->nameQuote('#__menu')
					. 'WHERE ' . $db->nameQuote('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=latest')
					. 'AND ' . $db->nameQuote('published') . '=' . $db->Quote('1')
					. EBR::getLanguageQuery();

			$db->setQuery($query);
			$menus	= $db->loadObjectList();

			$blog = EB::post($blogId);

			if ($menus) {
				foreach ($menus as $menu) {

					$params = EB::registry($menu->params);
					$inclusion = EB::getCategoryInclusion($params->get('inclusion'));

					if (empty($inclusion)) {
						continue;
					}

					if (!is_array($inclusion)) {
						$inclusion = array($inclusion);
					}

					if (in_array($blog->category_id , $inclusion)) {
						$entriesItems[$blogId] = $menu->id;
					}
				}
			}

			// Test if there is any entry specific view as this will always override the latest above.
			$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__menu') . ' '
					. 'WHERE ' . $db->nameQuote('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=entry&id='.$blogId) . ' '
					. 'AND ' . $db->nameQuote('published') . '=' . $db->Quote('1')
					. EBR::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery($query);
			$itemid = $db->loadResult();

			if($itemid)
			{
				$entriesItems[ $blogId ]    = $itemid;
			}
			else
			{
				// this is to check if we used category menu item from this post or not.
				// if yes, we do nothing. if not, we need to update the cache object so that the next checking will
				// not execute sql again.

				if (isset($entriesItems[ $blogId ])) {
					return $entriesItems[ $blogId ];
				} else
				{
					$entriesItems[ $blogId ] = '';
				}
			}

		}

		return $entriesItems[ $blogId ];
	}

	/**
	 * Retrieves the itemid associated with a dashboard layout
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getItemIdByDashboardLayout($layout)
	{
		static $items = array();

		if (!isset($items[$layout])) {
			$model = EB::model('Menu');
			$items[$layout] = $model->getMenus('dashboard', $layout);
		}

		return $items[$layout];
	}

	/**
	 * Retrieve the itemid associated with a team blog.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getItemIdByTeamBlog($id)
	{
		static $items = array();

		if (!isset($items[$id])) {
			$model = EB::model('Menu');
			$items[$id] = $model->getMenusByTeamId($id);
		}

		return $items[$id];
	}

	/**
	 * Retrieves the itemid based on the all categories listings
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getItemIdByAllCategories()
	{
		static $item = false;

		if (!$item) {
			$model = EB::model('Menu');
			$item = $model->getMenusByAllCategory();
		}

		return $item;
	}

	/**
	 * Retrieves the itemid based on the category id
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getItemIdByCategories($id)
	{
		static $items = array();

		if (!isset($items[$id])) {
			$model = EB::model('Menu');
			$items[$id] = $model->getMenusByCategoryId($id);
		}

		return $items[$id];
	}

	/**
	 * Retrieve menu id by specific blogger
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getItemIdByBlogger($id)
	{
		static $items = array();

		if (!isset($items[$id])) {
			$model = EB::model('Menu');
			$items[$id] = $model->getMenusByBloggerId($id);
		}

		return $items[$id];
	}

	/**
	 * Retrieves itemid associated with a tag id.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getItemIdByTag($id)
	{
		static $items = array();

		if (!isset($items[$id])) {
			$model = EB::model('Menu');
			$items[$id] = $model->getMenusByTagId($id);
		}

		return $items[$id];
	}

	/**
	 * Get menu item based on specific view
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getItemId($view = '', $exactMatch = false)
	{
		static $items = null;

		if (!isset($items[ $view ])) {
			$db	= EB::db();

			switch ($view) {
				case 'archive':
					$view='archive';
					break;
				case 'blogger':
					$view='blogger';
					break;
				case 'calendar':
					$view='calendar';
					break;
				case 'categories':
					$view='categories';
					break;
				case 'dashboard':
					$view='dashboard';
					break;
				case 'myblog':
					$view='myblog';
					break;
				case 'profile';
					$view='dashboard&layout=profile';
					break;
				case 'subscription':
					$view='subscription';
					break;
				case 'tags':
					$view='tags';
					break;
				case 'teamblog':
					$view='teamblog';
					break;
				case 'search':
					$view='search';
					break;
				case 'magazine':
					$view='magazine';
					break;
				case 'latest':
				default:
					$view='latest';
					break;
			}

			$config = EB::config();

			$routingBehavior = $config->get('main_routing', 'default');

			// since 5.0, we no longer support currentactive menu item. lets default to 'default' if this is an upgrade from 3.9
			$routingBehavior = ($routingBehavior == 'currentactive') ? 'default' : $routingBehavior;


			if ($routingBehavior == 'menuitemid') {
				$routingMenuItem = $config->get('main_routing_itemid','');
				$items[ $view ]	= $routingMenuItem;
			} else {
				$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__menu') . ' '
						. 'WHERE (' . $db->nameQuote('link') . '=' . $db->Quote('index.php?option=com_easyblog&view='.$view) . ' '
						. 'OR ' . $db->nameQuote('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view='.$view.'&limit=%') . ') '
						. 'AND ' . $db->nameQuote('published') . '=' . $db->Quote('1')
						. 'AND ' . $db->nameQuote('client_id') . '=' . $db->Quote('0')
						. self::getLanguageQuery()
						. ' LIMIT 1';
				$db->setQuery($query);
				$itemid = $db->loadResult();


				if (!$exactMatch) {

					// @rule: Try to fetch based on the current view.
					if (empty($itemid)) {
						$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__menu') . ' '
								. 'WHERE ' . $db->nameQuote('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=' . $view . '%') . ' '
								. 'AND ' . $db->nameQuote('published') . '=' . $db->Quote('1')
								. 'AND ' . $db->nameQuote('client_id') . '=' . $db->Quote('0')
								. self::getLanguageQuery()
								. ' LIMIT 1';
						$db->setQuery($query);
						$itemid = $db->loadResult();
					}

				}

				if (empty($itemid)) {
					$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__menu') . ' '
							. 'WHERE ' . $db->nameQuote('link') . '=' . $db->Quote('index.php?option=com_easyblog&view=latest') . ' '
							. 'AND ' . $db->nameQuote('published') . '=' . $db->Quote('1')
							. 'AND ' . $db->nameQuote('client_id') . '=' . $db->Quote('0')
							. self::getLanguageQuery()
							. ' LIMIT 1';
					$db->setQuery($query);
					$itemid = $db->loadResult();
				}

				//last try. get anything view that from easyblog.
				if (empty($itemid)) {
					$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__menu') . ' '
							. 'WHERE ' . $db->nameQuote('link') . ' LIKE ' . $db->Quote('index.php?option=com_easyblog&view=%') . ' '
							. 'AND ' . $db->nameQuote('published') . '=' . $db->Quote('1')
							. 'AND ' . $db->nameQuote('client_id') . '=' . $db->Quote('0')
							. self::getLanguageQuery()
							. ' ORDER BY `id` LIMIT 1';
					$db->setQuery($query);
					$itemid = $db->loadResult();
				}

				// if still failed the get any item id, then get the joomla default menu item id.
				if (empty($itemid)) {
					$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__menu') . ' '
							. 'WHERE `home` = ' . $db->Quote('1') . ' '
							. 'AND ' . $db->nameQuote('published') . '=' . $db->Quote('1')
							. 'AND ' . $db->nameQuote('client_id') . '=' . $db->Quote('0')
							. self::getLanguageQuery()
							. ' ORDER BY `id` LIMIT 1';
					$db->setQuery($query);
					$itemid = $db->loadResult();
				}

				$items[ $view ]	= !empty($itemid)? $itemid : 1;
			}
		}
		return $items[ $view ];
	}

	/**
	 * Encode segments to follow Joomla format.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function encodeSegments($segments)
	{
		$total = count($segments);

		for ($i = 0; $i < $total; $i++) {
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		return $segments;
	}

	/**
	 * Retrieves the blogger id given the menu id
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getBloggerIdFromMenu($id)
	{
		$model = EB::model('Menu');
		$link = $model->getMenuLink($id);

		parse_str($link, $queryStrings);

		return $queryStrings['id'];
	}

	/**
	 * Determines if the current URL is on blogger mode
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function isBloggerMode()
	{
		return EB::isBloggerMode();
	}

	/**
	 * Determines if the menu is a standalone blogger mode
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function isMenuABloggerMode($itemId)
	{
		$app = JFactory::getApplication();

		if ($app->isAdmin()) {
			return false;
		}

		$menu = $app->getMenu();
		$params = $menu->getParams($itemId);

		$isBloggerMode = $params->get('standalone_blog', false);

		return $isBloggerMode;
	}

	/**
	 * Determines if the given view is the current active menu item.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function isCurrentActiveMenu($view, $id = 0)
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu()->getActive();

		if (!$menu) {
			return false;
		}

		if ($id && strpos($menu->link, 'view=' . $view) !== false && strpos($menu->link, 'id=' . $id) !== false) {
			return true;
		}

		if (strpos($menu->link, 'view=' . $view) !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Provides translations for SEF links
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function translate($val)
	{
		$config = EB::config();

		if (!$config->get('main_url_translation', 0)) {
			return $val;
		}

		// Get the current site language
		$defaultLang = EB::getCurrentLanguage();

		// If the language filter plugin is not enable
		if ($defaultLang === false) {
			// Get default site language
			$langParams = JComponentHelper::getParams('com_languages');
			$defaultLang = $langParams->get('site');
		}

		JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT, $defaultLang);
		$new = JText::_('COM_EASYBLOG_SEF_' . strtoupper($val));

		// If translation fails, we try to use the original value instead.
		if (stristr($new, 'COM_EASYBLOG_SEF_') === false) {
			return $new;
		}

		return $val;
	}

	/**
	 * Retrieves the referer url that are being accessed
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getReferer($isCallback = false)
	{
		$uri = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

		if ($isCallback) {
			return '&callback=' . base64_encode($uri);
		}

		return $uri;
	}

	/**
	 * Retrieves the current url that are being accessed
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function current($isCallback = false)
	{
		$uri = self::getCurrentURI();

		if ($isCallback) {
			return '&callback=' . base64_encode($uri);
		}

		return $uri;
	}

	/**
	 * Method to retrieves current uri that are being accessed
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getCurrentURI()
	{
		$url = JURI::getInstance()->toString();

		return $url;
	}
}

// Deprecated @since 5.0 . Use @EBR instead.
// class EasyBlogRouter extends EBR { }
