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

class EasyBlogViewLatest extends EasyBlogView
{
	/**
	 * Displays the frontpage blog listings on the site.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Add the RSS headers on the page
		EB::feeds()->addHeaders('index.php?option=com_easyblog');

		// Add breadcrumbs on the site menu.
		$this->setPathway('COM_EASYBLOG_LATEST_BREADCRUMB');

		// Get the current active menu's properties.
		$params = $this->theme->params;
		$catInclusion = '';

		if ($params) {

			// Get a list of category inclusions
			$catInclusion	= EB::getCategoryInclusion($params->get('inclusion'));

			if ($params->get('includesubcategories', 0) && !empty($catInclusion)) {

				$tmpInclusion = array();

				foreach ($catInclusion as $includeCatId) {

					// Retrieve nested categories
					$category = new stdClass();
					$category->id = $includeCatId;
					$category->childs = null;

					EB::buildNestedCategories($category->id, $category);

					$linkage = '';
					EB::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');

					$catIds = array();
					$catIds[] = $category->id;
					EB::accessNestedCategoriesId($category, $catIds);

					$tmpInclusion = array_merge($tmpInclusion, $catIds);
				}

				$catInclusion = $tmpInclusion;
			}
		}

		// Sorting for the posts
		$sort = $this->input->get('sort', $this->config->get('layout_postorder'), 'cmd');
		$model = EB::model('Blog');

		$tobeCached = array();

		// Retrieve a list of featured blog posts on the site.
		$featured = $model->getFeaturedBlog($catInclusion);
		$excludeIds = array();

		// Test if user also wants the featured items to be appearing in the blog listings on the front page.
		// Otherwise, we'll need to exclude the featured id's from appearing on the front page.
		if (!$this->theme->params->get('post_include_featured', true)) {
			foreach ($featured as $item) {
				$excludeIds[] = $item->id;
			}
		}

		// Admin might want to display the featured blogs on all pages.
		$start = $this->input->get('start', 0, 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		if (!$this->theme->params->get('featured_slider_all_pages') && ($start != 0 || $limitstart != 0)) {
			$featured = array();
		}

		if ($featured) {
			$tobeCached = array_merge($tobeCached, $featured);
		}


		$excludedCategories = array();
		if ($params->get('exclusion_categories', false)) {
			$excludedCategories = $params->get('exclusion_categories');

		} else {
			// upgrades compatibility
			$tmpExcludeCategories = $this->config->get('layout_exclude_categories', null);
			if ($tmpExcludeCategories) {
				$excludedCategories	= explode( ',' , $tmpExcludeCategories );
			}
		}

		// Determines if we should explicitly include authors
		$includeAuthors = array();

		if ($params->get('inclusion_authors', false)) {
			$includeAuthors = $params->get('inclusion_authors');
		}

		// Determines if we should explicitly exclude authors
		$excludeAuthors = array();

		if ($params->get('exclusion_authors', false)) {
			$excludeAuthors = $params->get('exclusion_authors');
		}

		// Determines if we should explicitly include tags
		$includeTags = array();

		if ($params->get('inclusion_tags', false)) {
			$includeTags = $params->get('inclusion_tags');
		}

		// Check if this is filter by custom field
		$filter = $this->input->get('filter', false);
		$fields = array();
		$options = array();

		// Check if this user has saved filter search before
		$filterSaved = EB::model('fields')->getSavedFilter();

		if ($filter == 'field') {
			$filterVars = $this->input->input->getArray();
			$filterMode = $this->input->get('filtermode', 'include');

			foreach ($filterVars as $key => $value) {

				if (strpos($key, 'field') !== false) {
					$fieldId = explode('-', $key);
					$fieldId = $fieldId[1];

					$fields[$fieldId] = $filterVars[$key];

				}
			}
			// If there is a category inclusion from the field filter,
			// We override the existing cat inclusion
			$inclusion = $this->input->get('inclusion', false);

			if ($inclusion) {
				$catInclusion = $inclusion;
			}

			$options['fieldsFilterRule'] = $filterMode;
			$options['fieldsFilter'] = $fields;
		} else if ($filterSaved) {
			$params = json_decode($filterSaved->params);

			foreach ($params as $filter) {
				if (strpos($filter->name, 'field') !== false) {
					$fieldId = explode('-', $filter->name);
					$fieldId = $fieldId[1];

					$fields[$fieldId][] = $filter->value;
				}

				if ($filter->name == 'inclusion') {
					$catInclusion = $filter->value;
				}

				if ($filter->name == 'filtermode') {
					$options['fieldsFilterRule'] = $filter->value;
				}
			}

			$options['fieldsFilter'] = $fields;
		}

		$max = 0;
		$limit = EB::getViewLimit();

		// Determine if loadmore should be shown by adding one extra limit into the query
		if ($this->config->get('listing_pagination_style') == 'autoload') {
			$max = $limit + 1;
			$options['paginationType'] = 'loadmore';
		}

		// Fetch the blog entries.
		$data = $model->getBlogsBy('', '', $sort, 0, EBLOG_FILTER_PUBLISHED, null, true, $excludeIds, false, false, true, $excludedCategories, $catInclusion, null, 'listlength', $this->theme->params->get('post_pin_featured', false),
					$includeAuthors, $excludeAuthors, false, $includeTags, $options);

		$showLoadMore = false;

		if ($data) {

			if ($this->config->get('listing_pagination_style') == 'autoload' && count($data) == $max) {
				$showLoadMore = true;

				// Take out the last post
				array_pop($data);
			}

			$tobeCached = array_merge($tobeCached, $data);
		}

		// Format the blog posts
		$options = array(
					'cacheComment' => false,
					'cacheCommentCount' => false,
					'cacheRatings' => false,
					'cacheTags' => false,
					'cacheAuthors' => false,
					'loadAuthor' => false
					);

		$themes = EB::themes();

		if ($themes->getParam('post_comment_counter', 0)) {
			$options['cacheCommentCount'] = true;
		}

		if ($themes->getParam('post_comment_preview', 0)) {
			$options['cacheComment'] = true;
		}

		if ($themes->getParam('post_tags', 0)) {
			$options['cacheTags'] = true;
		}

		if ($themes->getParam('post_ratings', 0)) {
			$options['cacheRatings'] = true;
		}

		if ($themes->getParam('post_author', 0) || $themes->getParam('post_author_avatar', 0)) {
			$options['cacheAuthors'] = true;
			$options['loadAuthor'] = true;
		}

		// we will cache it here.
		if ($tobeCached) {
			EB::cache()->insert($tobeCached, $options);
		}

		// Get the pagination
		$pagination	= $model->getPagination();
		$paginationLink = $pagination->getPagesLinks();
		$currentPageLink = $pagination->getCurrentPageLink('latest', true);

		if ($featured) {
			// Format the featured items without caching
			$featured = EB::formatter('featured', $featured, false, $options);
		}

		// Perform blog formatting without caching
		$posts = EB::formatter('list', $data, false, $options);

		// Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_LATEST_PAGE_TITLE'));

		// Set the page title
		$this->setPageTitle($title, $pagination, $this->config->get('main_pagetitle_autoappend'));

		// Add canonical URLs.
		$this->canonical('index.php?option=com_easyblog');

		// Meta should be set later because formatter would have cached the post already.
		EB::setMeta(META_ID_LATEST, META_TYPE_VIEW);

		// Get the current url
		$return = EBR::current();
		$limitstart = $limitstart + $limit;

		$this->set('return', $return);
		$this->set('posts', $posts);
		$this->set('featured', $featured);
		$this->set('showLoadMore', $showLoadMore);
		$this->set('limitstart', $limitstart);
		$this->set('pagination', $paginationLink);
		$this->set('currentPageLink', $currentPageLink);

		parent::display('blogs/latest/default');
	}
}
