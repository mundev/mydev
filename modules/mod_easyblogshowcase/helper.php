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

class modEasyBlogShowcaseHelper extends EasyBlog
{
	public $lib = null;

	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	/**
	 * Retrieves a list of items for the module
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts()
	{
		$model = EB::model('Blog');

		// Determines if we should display featured or latest entries
		$type = $this->params->get('showposttype', 'featured');

		// Determines if we should filter by category
		$categoryId = $this->params->get('catid');

		$result = array();

		if ($categoryId && !is_array($categoryId)) {
			$categoryId = (int) $categoryId;
		}

		$excludeIds = array();

		// If type equal to latest only, we need to exclude featured post as well
		if ($type == 'latestOnly') {
			// Retrieve a list of featured blog posts on the site.
			$featured = $model->getFeaturedBlog();

			foreach ($featured as $item) {
				$excludeIds[] = $item->id;
			}
		}

		$inclusion = '';

		// Get a list of category inclusions
		$inclusion	= EB::getCategoryInclusion($categoryId);

		$subCat = $this->params->get('subcat', 1);

		// Include child category in the inclusions
		if ($subCat && !empty($inclusion)) {

			$tmpInclusion = array();

			foreach ($inclusion as $includeCatId) {

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

			$inclusion = $tmpInclusion;
		}

		// Let's get the post now
		if (($type == 'all' || $type == 'latestOnly')) {
			$result = $model->getBlogsBy('', '', 'latest' , $this->params->get( 'count' ) , EBLOG_FILTER_PUBLISHED, null, null, $excludeIds, false, false, false, array() , $inclusion);
		}

		// If not latest posttype, show featured post.
		if ($type == 'featured') {
			$result = $model->getFeaturedBlog($inclusion, $this->params->get('count'));
		}

		// If there's nothing to show at all, don't display anything
		if (!$result) {
			return $result;
		}

		$results = EB::formatter('list', $result);

		// Randomize items
		if ($this->params->get('autoshuffle')) {
			shuffle($results);
		}

		$contentKey	= $this->params->get('contentfrom', 'content');
		$textcount = $this->params->get('textlimit', '200');

		$posts = array();

		$layout = $this->getPhotoLayout();

		foreach ($results as $post) {

			// we will get the image 1st.
			$post->postCover = '';
			$post->photoLayout = '';

			if ($post->hasImage()) {
				$post->postCover = $post->getImage($layout->size, true, true);
			}

			if (!$post->hasImage() && $this->params->get('photo_legacy', true)) {
				$post->postCover = $post->getImage($layout->size, false, true);
			}

			$post->postCoverLayout = $layout;

			// now get the content
			$content = '';
			// Get the content from the selected source
			if ($contentKey == 'intro') {
				$content = $post->getIntro(true);
			} else {
				$content = $post->getContentWithoutIntro('entry');
			}

			// Truncate the content
			$content = EB::truncater()->stripTags($content);
			if (EBString::strlen($content) > $textcount) {
				$content = EBString::substr($content, 0, $textcount) . '...';
			}

			$post->content = $content;

			$posts[] = $post;

		}

		return $posts;
	}

	/**
	 * Retrieves the photo layout settings
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPhotoLayout()
	{
		$layout = new stdClass();
		$layout->layout = $this->params->get('photo_layout');
		$layout->size = $this->params->get('photo_size', 'medium');

		$layout->alignment = $layout->layout->alignment;
		$layout->alignment = ($layout->alignment == 'default') ? 'left' : $layout->alignment;

		if (!$layout->layout) {
			$layout->layout = new stdClass();
			$layout->layout->width = 260;
			$layout->layout->height = 200;
			$layout->layout->crop = true;
		}

		return $layout;
	}
}
