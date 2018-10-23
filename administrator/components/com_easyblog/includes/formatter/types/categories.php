<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogFormatterCategories extends EasyBlogFormatterStandard
{
	public function execute()
	{
		if (!$this->items) {
			return $this->items;
		}

		$config = EB::config();
		$limit = EB::call('Pagination', 'getLimit');

		$cacheOptions = array('cachePosts' => true, 'cacheAuthors' => true, 'cacheAuthorCount' => false);

		if (isset($this->options['cachePosts']) && !$this->options['cachePosts']) {
			$cacheOptions['cachePosts'] = false;
		}

		if (isset($this->options['cacheAuthors']) && !$this->options['cacheAuthors']) {
			$cacheOptions['cacheAuthors'] = false;
		}

		if (isset($this->options['cacheAuthorsCount']) && !$this->options['cacheAuthorsCount']) {
			$cacheOptions['cacheAuthorsCount'] = true;
		}

		// lets cache these categories
		EB::cache()->insertCategories($this->items, $cacheOptions);

		$categories = array();

		// Get the category model
		$model = EB::model('Category');

		foreach ($this->items as $row) {

			// We want to load the table objects
			$category = EB::table('Category');
			$category->bind($row);

			// binding the extra info
			if (isset($row->cnt)) {
				$category->cnt = $row->cnt;
			}

			// Format the childs
			$category->childs = array();

			// Build childs list
			EB::buildNestedCategories($category->id, $category, false, true);


			// Parameterize initial subcategories to display. Ability to configure from backend.
			$nestedLinks = '';
			$subcategoryLimit = $this->app->getCfg('list_limit') == 0 ? 5 : $this->app->getCfg('list_limit');

			if (count($category->childs) > $subcategoryLimit) {

				$initialNestedLinks = '';
				$initialRow = new stdClass();
				$initialRow->childs = array_slice($category->childs, 0, $subcategoryLimit);

				EB::accessNestedCategories($initialRow, $initialNestedLinks, '0', '', 'link', ', ');

				$moreNestedLinks = '';
				$moreRow = new stdClass();
				$moreRow->childs = array_slice($category->childs, $subcategoryLimit);

				EB::accessNestedCategories($moreRow, $moreNestedLinks, '0', '', 'link', ', ');

				// Hide more nested links until triggered
				$nestedLinks .= $initialNestedLinks;
				$nestedLinks .= '<span class="more-subcategories-toggle" data-more-categories-link> ' . JText::_('COM_EASYBLOG_AND') . ' <a href="javascript:void(0);">' . JText::sprintf('COM_EASYBLOG_OTHER_SUBCATEGORIES', count($category->childs) - $subcategoryLimit) . '</a></span>';
				$nestedLinks .= '<span class="more-subcategories" style="display: none;" data-more-categories>, ' . $moreNestedLinks . '</span>';

			} else {
				EB::accessNestedCategories($category, $nestedLinks, '0', '', 'link', ', ');
			}

			// Set the nested links
			$category->nestedLink = $nestedLinks;

			// Get a list of nested categories and itself.
			$filterCategories = array($category->id);
			EB::accessNestedCategoriesId($category, $filterCategories);

			// Get a list of blog posts from this category
			$blogs = array();

			// we need to get the setting from themes instead as
			// the configuration can be override by menu item.

			$themes = EB::themes();
			$showPosts = $themes->getParam('category_posts');
			$showAuthors = $themes->getParam('category_authors');

			if ($showPosts) {

				if (EB::cache()->exists($category->id, 'cats')) {
					$data = EB::cache()->get($category->id, 'cats');

					if (isset($data['post'])) {
						$blogs = $data['post'];
					}

				} else {
					$blogs = $model->getPosts($filterCategories, $limit);
				}

				// Format the blog posts
				$options = array(
							'cacheComment' => false,
							'cacheCommentCount' => false,
							'cacheRatings' => false,
							'cacheVoted' => false,
							'cacheTags' => false,
							'cacheAuthors' => false,
							'loadAuthor' => false
							);

				$blogs = EB::formatter('list', $blogs, true, $options);

			}

			// Assign other attributes to the category object
			$category->blogs = $blogs;

			// Get the total number of posts in the category
			if (! isset($category->cnt)) {
				$category->cnt = $model->getTotalPostCount($filterCategories);
			}

			// Get a list of active authors within this category.
			$category->authors = array();
			$category->authorsCount = array();
			if ($showAuthors) {
				// $category->authors = $category->getActiveBloggers();
				$category->authorsCount = $category->getActiveAuthorsCount();
			}

			// Check isCategorySubscribed
			$category->isCategorySubscribed = $model->isCategorySubscribedEmail($category->id, $this->my->email);

			// We need to get the subscription id
			$category->subscriptionId = false;

			if ($category->isCategorySubscribed) {
				$subscriptionModel = EB::model('Subscription');
				$category->subscriptionId = $subscriptionModel->getSubscriptionId($this->my->email, $category->id, EBLOG_SUBSCRIPTION_CATEGORY);
			}

			$categories[] = $category;
		}

		return $categories;
	}
}
