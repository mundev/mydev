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

class EasyBlogViewBlogger extends EasyBlogView
{
	/**
	 * Retrieve a list of tags used by author
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getTags()
	{
		// Get the id to lookup for
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->resolve('');
		}

		$model = EB::model('Blogger');
		$results = $model->getTagUsed($id);

		if (!$results) {
			return $this->ajax->resolve('');
		}

		// Get the default pagination limit for authors
		$limit = EB::getViewLimit('author_tags_limit', 'bloggers');
		$limit = $limit == 0 ? 5 : $limit;

		$tags = array();

		foreach ($results as $result) {
			$tag = EB::table('Tag');
			$tag->bind($result);

			$tags[]	= $tag;
		}

		$theme = EB::template();
		$theme->set('tags', $tags);
		$theme->set('limit', $limit);

		$output = $theme->output('site/authors/tags');

		return $this->ajax->resolve($output);
	}


	/**
	 * Retrieve a list of category used by author
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getCategories()
	{
		// Get the id to lookup for
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->resolve('');
		}

		$model = EB::model('Blogger');
		$results = $model->getCategoryUsed($id);

		if (!$results) {
			return $this->ajax->resolve('');
		}

		// Get the default pagination limit for authors
		$limitCats = EB::getViewLimit('author_categories_limit', 'bloggers');
		$limitCats = $limitCats == 0 ? 5 : $limitCats;

		$theme = EB::template();
		$theme->set('categories', $results);
		$theme->set('limitCats', $limitCats);

		$output = $theme->output('site/authors/categories');

		return $this->ajax->resolve($output);
	}

}
