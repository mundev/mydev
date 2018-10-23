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

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerSearch extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Search within EasyBlog
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function query()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the query
		$query = $this->input->get('query', '', 'string');

		// Get category if exists
		$catId = $this->input->get('category_id', 0, 'int');

		$queryUrl = 'index.php?option=com_easyblog&view=search&query=' . $query;

		if ($catId) {
			$queryUrl .= '&category_id=' . $catId;
		}

		$url = EB::_($queryUrl, false);

		$this->app->redirect($url);
	}

	/**
	 * Search blogger
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function blogger()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the query
		$search = $this->input->get('search', '', 'string');

		$url = EB::_('index.php?option=com_easyblog&view=blogger&search=' . $search, false);
		
		return $this->app->redirect($url);
	}
}
