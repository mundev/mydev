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

// Import Joomla's pagination object
jimport('joomla.html.pagination');

class EasyBlogPagination extends JPagination
{
	public function __construct($total, $limitstart, $limit, $prefix = '')
	{
		parent::__construct($total, $limitstart, $limit, $prefix);
	}

	/**
	 * Alias to toHTML
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getPagesLinks($view = 'latest', $filtering = array(), $replace = false)
	{
		return $this->toHTML($view, $filtering, $replace);
	}

	/**
	 * Displays the pagination links at the bottom of the page.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function toHTML($view = 'index', $replace = false)
	{
		// Retrieve the pagination data.
		$data = $this->getData();

		// If there's no pagination at all, skip this
		if (count($data->pages) == $this->get('pages.total') && $this->get('pages.total') == 1 || $this->get('pages.total') == 0) {
			return false;
		}

		$queries = '';

		if (!empty($data) && $replace) {

			$currentPageLink = 'index.php?option=com_easyblog&view=' . $view . $queries;

			foreach ($data->pages as $page) {
				if (!empty($page->link)) {
					$limitstart = !empty($page->base) ? '&limitstart=' . $page->base : '';
					$page->link = EBR::_($currentPageLink . $limitstart);
				}
			}

			if (!empty($data->next->link)) {
				$limitstart = !empty($data->next->base) ? '&limitstart=' . $data->next->base : '';
				$data->next->link = EBR::_($currentPageLink . $limitstart);
			}

			if (!empyt($data->previous->link)) {
				$limitstart = !empty($data->previous->base) ? '&limitstart=' . $data->previous->base : '';
				$data->previous->link = EBR::_($currentPageLink . $limitstart);
			}
		}

		$template = EB::template();
		$template->set('data', $data);

		return $template->output('site/blogs/pagination/default');
	}

	/**
	 * Get current page url with pagination query
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getCurrentPageLink($view = 'latest', $external = false)
	{
		$data = $this->getData();

		$currentPageLink = 'index.php?option=com_easyblog&view=' . $view;
		$limitstart = $this->limitstart;

		if ($limitstart) {
			$limitstart = '&limitstart=' . $limitstart;
			$currentPageLink = $currentPageLink . $limitstart;
		}

		$url = JRoute::_($currentPageLink);

		// Ensure the url is internal
		if ($external && (stristr('http://', $url) !== false || stristr('https://', $url) !== false)) {
			$uri = JURI::getInstance();

			$url = ltrim($url, '/');
			$url = $uri->toString(array('scheme', 'host', 'port')) . '/' . $url;
		}

		return $url;
	}

	/**
	 * Retrieves the limit for pagination
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getLimit( $key = 'listlength' )
	{
		$app = JFactory::getApplication();
		$default = EB::jconfig()->get('list_limit');

		if ($app->isAdmin()) {
			return $default;
		}

		// Get the active menu
		$menu = $app->getMenu()->getActive();
		$limit = -2;

		// Get the pagination limit from the menu parameters
		if (is_object($menu)) {
			$params = $menu->params;

			if (!is_object($params) && is_string($params)) {
				$params = new JRegistry($params);
			}

			$limit = $params->get('limit', '-2');
		}

		// If there is no pagination limit set on the menu, try to use the limit from EasyBlog's settings
		if ($limit == '-2') {
			$config = EB::config();

			$index = 'layout_pagination_' . $key;

			if ($key == 'listlength') {
				$index = 'layout_listlength';
			}

			$limit = $config->get($index);
		}

		// Revert to joomla's pagination if configured to inherit from Joomla
		if ($limit == '0' || $limit == '-1' || $limit == '-2') {
			$limit = $default;
		}

		return $limit;
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function get($property, $default = null)
	{
		if (strpos($property, '.')) {
			$prop = explode('.', $property);
			$prop[1] = ucfirst($prop[1]);
			$property = implode($prop);
		}

		if (isset($this->$property)) {
			return $this->$property;
		}

		return $default;
	}	
}
