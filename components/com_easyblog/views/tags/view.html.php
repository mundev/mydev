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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewTags extends EasyBlogView
{
	public function __construct($options = array())
	{
		$input = JFactory::getApplication()->input;
		$layout = $input->get('layout', '', 'word');

		// This menu should not inherit from the listing params
		if ($layout != 'tag') {
			$this->paramsPrefix = '';
		}

		if ($layout == 'tag') {
			$this->paramsPrefix = 'tag';
		}

		parent::__construct($options);
	}

	/**
	 * Displays all tags on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Set meta tags for tags view
		EB::setMeta(META_ID_TAGS, META_TYPE_VIEW);

		// Set breadcrumb
		$this->setViewBreadcrumb('tags');

		// Set page title
		$title = EB::getPageTitle(JText::_('COM_EASYBLOG_TAGS_PAGE_TITLE'));
		$this->setPageTitle($title, '', $this->config->get('main_pagetitle_autoappend'));

		// Add canonical URL to satify Googlebot. Incase they think it's duplicated content.
		$this->canonical('index.php?option=com_easyblog&view=tags', array('ordering', 'sorting'));

		// Retrieve search values
		$search = $this->input->get('search', '', 'string');

		// Get the model
		$model = EB::model('Tags');

		// Get other sorting and filters
		$ordering = EBString::strtolower($this->input->get('ordering', '', 'string'));
		$sorting = EBString::strtolower($this->input->get('sorting', $this->config->get('main_tags_sorting'), 'string'));

		// Get the tags
		// for now we hardcode the limit as tags page pagination special.
		$limit = EASYBLOG_TAGS_LIMIT;
		$result = $model->getTagCloud($limit, $ordering, $sorting, true, $search, false, true);
		$pagination = $model->getPagination();

		// Format the tags
		$tags = EB::formatter('tags', $result, true);

		// Since the ordering options is already removed, we will just hard code the ordering here. #1433
		$titleURL = 'index.php?option=com_easyblog&view=tags&ordering=title&sorting=asc';
		$postURL = 'index.php?option=com_easyblog&view=tags&ordering=postcount&sorting=desc';

		$showRss = true;

		if (!$this->config->get('main_rss') || !$this->theme->params->get('tag_rss', true)) {
			$showRss = false;
		}

		$this->set('showRss', $showRss);
		$this->set('titleURL', $titleURL);
		$this->set('postURL', $postURL);
		$this->set('tags', $tags);
		$this->set('sorting', $sorting);
		$this->set('ordering', $ordering);
		$this->set('pagination', $pagination);

		parent::display('tags/default');
	}

	/**
	 * Displays blog listings by specific tags on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function tag()
	{
		// Get the tag id
		$id = $this->input->get('id', '', 'default');

		// Add noindex for tags listing by default
		if ($this->config->get('main_tag_noindex', true)) {
			$this->doc->setMetadata('robots', 'noindex,follow');
		}

		// Load the tag object
		$tag = EB::table('Tag');
		$tag->load($id);

		// The tag could be a permalink
		if (!$tag->id) {
			$tag->load($id, true);
		}

		if ($tag->id) {
			EB::cache()->set($tag, 'tag');
		}

		// Set page title
		$this->setPageTitle($tag->getTitle(), '' , $this->config->get('main_pagetitle_autoappend'));

		// set meta tags for tags view
		EB::setMeta(META_ID_TAGS, META_TYPE_VIEW, $tag->getTitle() . ' - ' . EB::getPageTitle($this->config->get('main_title')) );

		// Add canonical URL
		$this->canonical('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $id);

		// Set breadcrumb
		if (!EBR::isCurrentActiveMenu('tags')) {
			$this->setPathway(JText::_('COM_EASYBLOG_TAGS_BREADCRUMB'), EBR::_('index.php?option=com_easyblog&view=tags'));
		}

		$this->setPathway($tag->getTitle());

		// Get the blogs model
		$blogModel = EB::model('Blog');
		$tagModel = EB::model('Tags');

		$limit = EB::getViewLimit('tag_posts_limit', 'tag');

		$options = array();
		$params = $this->theme->params;

		$options['order'] = $params->get('layout_postorder');
		$options['sort'] = $params->get('layout_postsort');

		// Get the blog posts now
		$rows = $blogModel->getTaggedBlogs($tag->id, $limit, '', '', false, $options);

		// Get the pagination
		$pagination	= $blogModel->getPagination();

		if (is_object($pagination) && method_exists($pagination, 'setAdditionalUrlParam')) {
			$pagination->setAdditionalUrlParam('id', $tag->id);
		}

		// Get total number of private blog posts
		$privateCount = 0;

		// Get total number of team blog count
		$teamblogCount = 0;

		if ($this->my->guest) {
			$privateCount = $tagModel->getTagPrivateBlogCount($id);
		}

		// Determines if we should get the team blog count
		if (!$this->config->get('main_includeteamblogpost')) {
			$teamblogCount = $tagModel->getTeamBlogCount($id);
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

		// Format the blog posts using the standard list formatter
		$posts = EB::formatter('list', $rows, true, $options);

		$return = base64_encode($tag->getPermalink());

		$this->set('return', $return);
		$this->set('tag', $tag);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('private', $privateCount);
		$this->set('team', $teamblogCount);

		parent::display('tags/item');
	}
}
