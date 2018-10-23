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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewFeatured extends EasyBlogView
{
	/**
	 * Default display method for featured listings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Set the meta tags for this page
		EB::setMeta(META_ID_FEATURED, META_TYPE_VIEW);

		// Add the RSS headers on the page
		EB::feeds()->addHeaders('index.php?option=com_easyblog&view=featured');

		// Add breadcrumbs on the site menu.
		$this->setPathway('COM_EASYBLOG_FEATURED_BREADCRUMB');

		// Get the number of categories to show per page
		$limit = EB::getViewLimit('limit', 'featured');
		$options = array('limit' => $limit);

		// Get the model
		$model = EB::model('Featured');

		// Get a list of featured posts
		$posts = $model->getPosts($options);

		// Get the pagination
		$pagination	= $model->getPagination();

		// Format the posts
		$posts = EB::formatter('list', $posts);

		// Set the page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_FEATURED_PAGE_TITLE'));
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Get the current url
		$return = EBR::_('index.php?option=com_easyblog', false);

		$this->set('return', $return);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);

		parent::display('blogs/featured/default');
	}
}
