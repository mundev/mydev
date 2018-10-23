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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewMagazine extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// Add the RSS headers on the page.
		EB::feeds()->addHeaders('index.php?option=com_easyblog');

		// Add breadcrumbs on the site menu.
		$this->setPathway('COM_EASYBLOG_LATEST_BREADCRUMB');

		$params = $this->theme->params;

		// Retrieve the leading article type.
		$leadingArticleType = $params->get('magazine_leading_article_type');

		// Retrieve the leading article categories. Fallback if the category is not set.
		$leadingArticleCategories = $params->get('magazine_leading_article_category', array());

		// Clear leadingArticleCategory if user selected all categories
		if ($leadingArticleCategories) {
			$leadingArticleCategories = array_diff($leadingArticleCategories, array('all'));
		}

		$model = EB::model('Blog');
		$leadingArticle = "";
		$excludeBlogs = array();

		// Latest post
		if ($leadingArticleType == 'latestPost') {
			
			$latestPost = $model->getBlogsby('', '', '', '1', EBLOG_FILTER_PUBLISHED, false, false, '', false, false, true, '', $leadingArticleCategories, null, 'listlength', 
				false, '', '', '');

			if ($latestPost) {

				// Formatting the leading article
				$latestPost = EB::formatter('list', $latestPost, false);

				$leadingArticle = $latestPost[0];
				$excludeBlogs[] = $leadingArticle->id;
			}
		}
		
		// Latest featured
		if ($leadingArticleType == 'latestFeatured') {
			
			$featured = $model->getFeaturedBlog($leadingArticleCategories, '1');

			// Format leadingArticle
			$featured = EB::formatter('featured', $featured, false);

			$leadingArticle = $featured[0];
		}

		// Single post
		if ($leadingArticleType == 'singlePost') {

			// Retrieve the post entered by user.
			$leadingArticleId = $params->get('magazine_leading_article', false);

			if ($leadingArticleId) {
				$excludeBlogs[] = $leadingArticleId;
				$leadingArticle = EB::post($leadingArticleId);
			}
		}

		// Determine if we should explicitly include authors.
		$includeAuthors = array();

		if ($params->get('magazine_inclusion_authors', false)) {
			$includeAuthors = $params->get('magazine_inclusion_authors');
		}

		// Determine if we should explicitly exclude authors.
		$excludeAuthors = array();

		if ($params->get('magazine_exclusion_authors', false)) {
			$excludeAuthors = $params->get('magazine_exclusion_authors');
		}

		// Determine if we should exclude featured post from list.
		$excludeFeatured = $params->get('magazine_exclude_featured', false);

		// Determine the list limit for the list article
		$listLimit = $params->get('listLimit', '6');

		// Retrieve the list article categories.
		$listArticleCategories = $params->get('magazine_list_article_category', array());
		
		// Fetch all blog entries based on the defined information above.
		$data = $model->getBlogsby('', '', '', $listLimit, EBLOG_FILTER_PUBLISHED, false, false, $excludeBlogs, false, false, true, '', $listArticleCategories, null, 'listlength', 
			false, $includeAuthors, $excludeAuthors, $excludeFeatured);

		// Format blog items without caching.
		$posts = EB::formatter('list', $data, false);

		// Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_MAGAZINE_PAGE_TITLE'));

		// Set the page title
		$this->setPageTitle($title, '', $this->config->get('main_pagetitle_autoappend'));

		// Add canonical URLs.
		$this->canonical('index.php?option=com_easyblog');

		// Get the current url
		$return = EBR::_('index.php?option=com_easyblog', false);

		// Set view all link
		$viewAll = EBR::_('index.php?option=com_easyblog&view=latest');

		EB::setMeta(0, META_TYPE_VIEW);

		$this->set('return', $return);
		$this->set('viewAll', $viewAll);
		$this->set('leadingArticle', $leadingArticle);
		$this->set('posts', $posts);

		$magazineLayout = $params->get('magazine_style');
		parent::display('blogs/magazine/' . $magazineLayout);
	}
}
