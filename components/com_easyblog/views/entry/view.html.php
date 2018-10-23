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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewEntry extends EasyBlogView
{
	/**
	 * Main display for the blog entry view
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Get the blog post id from the request
		$id = $this->input->get('id', 0, 'default');

		// Load the blog post now
		$post = EB::post($id);

		// If blog id is not provided correctly, throw a 404 error page
		if (!$id || !$post->id) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'));
		}

		// If the settings requires the user to be logged in, do not allow guests here.
		$post->requiresLoginToRead();

		// After the post is loaded, set it into the cache
		EB::cache()->insert(array($post));

		// Render necessary data on the headers
		$post->renderHeaders();

		// Check if blog is password protected.
		$protected = $this->isProtected($post);

		if ($protected !== false) {
			return;
		}

		// Perform validation checks to see if post is valid
		$exception = $post->checkView();

		if ($exception instanceof EasyBlogException) {
			EB::getErrorRedirection($exception->getMessage());
		}

		// Increment the hit counter for the blog post.
		$post->hit();

		// Format the post
		$post = EB::formatter('entry', $post);

		// Add bloggers breadcrumbs
		if (!EBR::isCurrentActiveMenu('blogger', $post->creator->id) && $this->config->get('layout_blogger_breadcrumb')) {
			$this->setPathway($post->creator->getName(), $post->creator->getPermalink());
		}

		// Add entry breadcrumb
		if (!EBR::isCurrentActiveMenu('entry', $post->id)) {
			$this->setPathway($post->title, '');
		}

		// Load up the blog model
		$model = EB::model('Blog');

		// Get author's recent posts.
		$recent = $this->getRecentPosts($post);

		// Add canonical URLs for the blog post
		$canonical = 'index.php?option=com_easyblog&view=entry&id=' . $post->id;
		$routeCanonical = true;

		// If the feed is imported from external feed, determine if we should be adding the original post permalink as canonical
		if ($post->isFromFeed()) {
			$feedHistory = EB::table('FeedHistory');
			$feedHistory->load(array('post_id' => $post->id));

			$feed = $feedHistory->getFeedTable();
			$feedParams = $feed->getParams();

			// Insert canonical link to the original parent
			if ($feedParams->get('canonical', false)) {
				$feedHistoryParams = $feedHistory->getParams();
				$originalPermalink = $feedHistoryParams->get('permalink', '');

				if ($originalPermalink) {
					$canonical = $originalPermalink;
					$routeCanonical = false;
				}
			}
		}

		$this->canonical($canonical, $routeCanonical);

		// Add AMP metadata on the page
		if ($this->config->get('main_amp')) {
			$this->amp($post->getPermalink(true, false, 'amp'), false);
		}

		$exclusion = array($post->id);

		// Prepare navigation object
		$navigation = $this->prepareNavigation($post, $exclusion);

		// Retrieve Google Adsense codes
		$adsense = EB::adsense()->html($post);

		// If a custom theme is setup for entries in the category, set a different theme
		if (!empty($post->category->theme)) {
			$this->setTheme($post->category->theme);
		}

		// Check if the user subscribed to this post.
		$subscription = EB::table('Subscriptions');

		if ($this->my->id) {
			$subscription->load(array('uid' => $post->id, 'utype' => 'entry', 'user_id' => $this->my->id));
		}

		$theme = EB::template();

		// Prepare related post
		$relatedPosts = array();

		// Get the menu params associated with this post
		$params = $post->getMenuParams();

		// Related posts seems to be missing from the theme file.
		if ($params->get('post_related', true)) {
			$relatedPosts = $model->getRelatedPosts($post->id, $params->get('post_related_limit', 5));

			// Format the related posts image
			if ($relatedPosts) {
				foreach ($relatedPosts as $relatedPost) {
					$relatedPost->postimage = $relatedPost->getImage('thumbnail', true, true);
				}
			}
		}

		if (!$post->posttype) {
			$post->posttype = 'standard';
		}

		// we need to test here if we should display the entry toolbars or admin toolbars or not to
		// prevent div.eb-entry-tools div added
		$hasEntryTools = false;
		$hasAdminTools = false;

		// lets test for entry tools
		if ($params->get('post_font_resize', true) ||
			$params->get('post_subscribe_link', true) ||
			($this->config->get('main_reporting') && (!$this->my->guest || $this->my->guest && $this->config->get('main_reporting_guests')) && $params->get('post_reporting', true)) ||
			$params->get('post_print', true) || $post->canFavourite() ||
			EB::bookmark()->allow($params)) {
			$hasEntryTools = true;
		}

		//now we test the entry admin tools
		if (EB::isSiteAdmin() ||
			($post->isMine() && !$post->hasRevisionWaitingForApproval()) ||
			($post->isMine() && $this->acl->get('publish_entry')) ||
			($post->isMine() && $this->acl->get('delete_entry')) ||
			$this->acl->get('feature_entry') ||
			$this->acl->get('moderate_entry')) {
			$hasAdminTools = true;
		}

		// We need to prepare the content here so that all the trigger will work correctly.
		if ($post->isPending()) {
			if ($theme->params->get('show_intro', true)) {
				$content = $post->getContent(EASYBLOG_VIEW_ENTRY, true, null, array('isPreview' => true, 'ignoreCache' => true));
			} else {
				$content = $post->getContentWithoutIntro(EASYBLOG_VIEW_ENTRY, true, array('isPreview' => true, 'ignoreCache' => true));
			}
		} else {
			if ($params->get('show_intro', true)) {
				$content = $post->getContent(EASYBLOG_VIEW_ENTRY);
			} else {
				$content = $post->getContentWithoutIntro();
			}
		}

		// Determines if the viewer has subscribed to the author
		$isBloggerSubscribed = false;

		if ($this->config->get('main_bloggersubscription')) {
			$bloggerModel = EB::model('Blogger');
			$isBloggerSubscribed = $bloggerModel->isBloggerSubscribedEmail($post->creator->id, $this->my->email);
		}

		// load language for blog app in mini header
		if (EB::easysocial()->exists()) {

			ES::initialize();

			$cluster = $post->source_type;
			$cluster = str_replace('easysocial.', '', $cluster);

			ES::language()->loadApp($cluster,'blog');
		}

		$prevId = false;
		$exclude = array();

		// Retrieve previous post id if exists
		if ($params->get('pagination_style') == 'autoload' && $navigation->prev && isset($navigation->prev->id)) {
			$prevId = $navigation->prev->id;
			$exclude[] = $post->id;
		}

		if ($navigation->prev && isset($navigation->prev->link)) {
			$this->doc->addHeadLink($this->escape($navigation->prev->link), 'prev');
		}

		if ($navigation->next && isset($navigation->next->link)) {
			$this->doc->addHeadLink($this->escape($navigation->next->link), 'next');
		}

		$exclude = json_encode($exclude);

		$gaEnabled = false;

		// Determine if Google Analytic is enabled for this page
		if ($this->config->get('main_google_analytics') && $this->config->get('main_google_analytics_id')) {
			$gaEnabled = true;
		}

		// Get the post rating value
		$ratings = $post->getRatings();

		$this->set('isBloggerSubscribed', $isBloggerSubscribed);
		$this->set('post', $post);
		$this->set('content', $content);
		$this->set('navigation', $navigation);
		$this->set('relatedPosts', $relatedPosts);
		$this->set('recent', $recent);
		$this->set('preview', false);
		$this->set('adsense' , $adsense);
		$this->set('subscription', $subscription);
		$this->set('hasEntryTools', $hasEntryTools);
		$this->set('hasAdminTools', $hasAdminTools);
		$this->set('prevId', $prevId);
		$this->set('exclude', $exclude);
		$this->set('gaEnabled', $gaEnabled);
		$this->set('ratings', $ratings);

		$this->theme->entryParams = $params;

		parent::display('blogs/entry/default');
	}

	/**
	 * Login layout for entry view
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function login()
	{
		$return = $this->input->get('return', '', 'string');

		if (!$return) {
			$return = base64_encode(EBR::_('index.php?option=com_easyblog', false));
		}

		$this->set('return', $return);

		parent::display('blogs/entry/login');
	}


	/**
	 * Determines if the current post is protected
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isProtected(EasyBlogPost &$post)
	{
		// Password protection disabled
		if (!$this->config->get('main_password_protect')) {
			return false;
		}

		// Site admin should not be restricted
		if (EB::isSiteAdmin()) {
			return false;
		}

		// Blog does not contain any password protection
		if (!$post->isPasswordProtected()) {
			return false;
		}

		// User already entered password
		if ($post->verifyPassword()) {
			return false;
		}

		$post = EB::formatter('entry', $post);

		// Check if the user subscribed to this post.
		$subscription = EB::table('Subscriptions');

		if ($this->my->id) {
			$subscription->load(array('uid' => $post->id, 'utype' => 'entry', 'user_id' => $this->my->id));
		}

		// Set the return url to the current url
		$return = base64_encode($post->getPermalink(false));

		// Get the menu params associated with this post
		$params = $post->getMenuParams();
		// $this->theme->params = $params;
		$this->theme->entryParams = $params;

		// we need to test here if we should display the entry toolbars or admin toolbars or not to
		// prevent div.eb-entry-tools div added
		$hasEntryTools = false;
		$hasAdminTools = false;

		// lets test for entry tools
		if ($params->get('post_font_resize', true) ||
			$params->get('post_subscribe_link', true) ||
			($this->config->get('main_reporting') && (!$this->my->guest || $this->my->guest && $this->config->get('main_reporting_guests')) && $params->get('post_reporting', true)) ||
			EB::bookmark()->allow($params)) {
			$hasEntryTools = true;
		}

		//now we test the entry admin tools
		if (EB::isSiteAdmin() ||
			($post->isMine() && !$post->hasRevisionWaitingForApproval()) ||
			($post->isMine() && $this->acl->get('publish_entry')) ||
			($post->isMine() && $this->acl->get('delete_entry')) ||
			$this->acl->get('feature_entry') ||
			$this->acl->get('moderate_entry')) {
			$hasAdminTools = true;
		}

		$navigation = $this->prepareNavigation($post);
		$prevId = false;

		if ($params->get('pagination_style') == 'autoload' && $navigation->prev && isset($navigation->prev->id)) {
			$prevId = $navigation->prev->id;
		}

		$theme = EB::themes();
		$theme->set('post', $post);
		$theme->set('preview', false);
		$theme->set('subscription', $subscription);
		$theme->set('hasEntryTools', $hasEntryTools);
		$theme->set('hasAdminTools', $hasAdminTools);
		$theme->set('prevId', $prevId);

		$output = $theme->output('site/blogs/entry/default.protected');

		$this->set('output', $output);
		$this->set('post', $post);
		$this->set('prevId', $prevId);

		parent::display('blogs/entry/default');

		return true;
	}

	/**
	 * Displays the latest entry on the site using the entry view
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function latest()
	{
		// Fetch the latest blog entry
		$model = EB::model('Blog');

		// Get the current active menu's properties.
		$menu = $this->app->getMenu()->getActive();
		$inclusion = '';

		if (is_object($menu)) {
			$params = EB::registry($menu->params);

			$data = $params->get('data');

			if (isset($data->inclusion)) {
				$inclusion = EB::getCategoryInclusion($params->get('data')->inclusion);
			}
		}

		// Retrieve a list of featured blog posts on the site.
		$featured = $model->getFeaturedBlog($inclusion);
		$excludeIds = array();

		// Test if user also wants the featured items to be appearing in the single latest menu on entry page.
		// Otherwise, we'll need to exclude the featured id's from appearing on the single latest entr page.
		if (!$this->theme->params->get('entry_include_featured', true)) {
			foreach ($featured as $item) {
				$excludeIds[] = $item->id;
			}
		}

		$items = $model->getBlogsBy('latest', 0, '', 1, EBLOG_FILTER_PUBLISHED, null, true, $excludeIds, false, false, true, array(), $inclusion);

		if (is_array($items) && !empty($items)) {
			$this->input->set('id', $items[0]->id);
			return $this->display();
		}

		echo JText::_( 'COM_EASYBLOG_NO_BLOG_ENTRY' );
	}

	/**
	 * Renders the blog post preview
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function preview($tpl = null)
	{
		// Get the blog post id from the request
		$id = $this->input->get('uid', '', 'default');

		// Load the blog post now
		$post = EB::post($id);

		// After the post is loaded, set it into the cache
		EB::cache()->insert(array($post));

		// If blog id is not provided correctly, throw a 404 error page
		if (!$id || !$post->id) {
			return JError::raiseError(404, JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'));
		}

		// Perform validation checks to see if post is valid
		$exception = $post->checkView();

		if ($exception instanceof EasyBlogException) {
			return JError::raiseError(400, $exception->getMessage());
		}

		// Render necessary data on the headers
		$post->renderHeaders(array('isPreview' => true));

		// Check if blog is password protected.
		$protected = $this->isProtected($post);

		if ($protected !== false) {
			return;
		}

		// If the viewer is the owner of the blog post, display a proper message
		if ($this->my->id == $post->created_by && !$post->isPublished()) {
			$notice = JText::_('COM_EASYBLOG_ENTRY_BLOG_UNPUBLISHED_VISIBLE_TO_OWNER');
		}

		if (EB::isSiteAdmin() && !$post->isPublished()) {
			$notice = JText::_('COM_EASYBLOG_ENTRY_BLOG_UNPUBLISHED_VISIBLE_TO_ADMIN');
		}

		// Format the post
		$post = EB::formatter('entry', $post);

		// Add bloggers breadcrumbs
		if (!EBR::isCurrentActiveMenu('blogger', $post->creator->id) && $this->config->get('layout_blogger_breadcrumb')) {
			$this->setPathway($post->creator->getName(), $post->creator->getPermalink());
		}

		// Add entry breadcrumb
		if (!EBR::isCurrentActiveMenu('entry', $post->id)) {
			$this->setPathway($post->title, '');
		}

		// Load up the blog model
		$model = EB::model('Blog');

		// Get author's recent posts.
		$recent = $this->getRecentPosts($post);

		// Add canonical URLs for the blog post
		$this->canonical('index.php?option=com_easyblog&view=entry&id=' . $post->id);

		// Prepare navigation object
		$navigation = $this->prepareNavigation($post);

		// Retrieve Google Adsense codes
		$adsense = EB::adsense()->html($post);

		// If a custom theme is setup for entries in the category, set a different theme
		if (!empty($post->category->theme)) {
			$this->setTheme($post->category->theme);
		}

		// Check if the user subscribed to this post.
		$isBlogSubscribed = $model->isBlogSubscribedEmail($post->id, $this->my->email);

		$theme = EB::themes();

		// Prepare related post
		$relatedPosts = array();

		// Related posts seems to be missing from the theme file.
		if ($theme->params->get('post_related', true)) {
			$relatedPosts = $model->getRelatedPosts($post->id, $theme->params->get('post_related_limit', 5));
		}

		if (!$post->posttype) {
			$post->posttype = 'standard';
		}

		// We will always allow tools to be enabled by default in preview layout.
		$hasEntryTools = true;
		$hasAdminTools = true;

		// We need to prepare the content here so that all the trigger will work correctly.
		if ($theme->params->get('show_intro', true)) {
			$content = $post->getContent(EASYBLOG_VIEW_ENTRY, true, null, array('isPreview' => true, 'ignoreCache' => true));
		} else {
			$content = $post->getContentWithoutIntro(EASYBLOG_VIEW_ENTRY, true, array('isPreview' => true, 'ignoreCache' => true));
		}

		// Get the revisions
		$revisions = $post->getRevisions();

		$this->set('isBloggerSubscribed', false);
		$this->set('post', $post);
		$this->set('revisions', $revisions);
		$this->set('content', $content);
		$this->set('navigation', $navigation);
		$this->set('relatedPosts', $relatedPosts);
		$this->set('recent', $recent);
		$this->set('preview', true);
		$this->set('prevId', false);
		$this->set('adsense' , $adsense);
		$this->set('isBlogSubscribed', $isBlogSubscribed);
		$this->set('hasEntryTools', $hasEntryTools);
		$this->set('hasAdminTools', $hasAdminTools);

		// Get the menu params associated with this post
		$params = $post->getMenuParams();

		$this->theme->entryParams = $params;

		parent::display('blogs/entry/default');
	}

	/**
	 * Retrieves a list of recent posts
	 *
	 * @since	4.0
	 * @access	private
	 */
	public function getRecentPosts(EasyBlogPost &$post)
	{
		$recent = array();
		if (!$post->category->getParam('show_author_box', true) || !$post->category->getParam('post_author_recent', true)) {
			return $recent;
		}

		$limit = $post->category->getParam('post_author_recent_limit', 5);

		$model = EB::model('Blog');
		$result = $model->getBlogsBy('blogger', $post->created_by, 'latest', $limit);

		if (!$result) {
			return $recent;
		}

		$posts = array();

		foreach ($result as $row) {
			$item = EB::post();
			$item->bind($row, array('force' => true));

			$posts[] = $item;
		}

		return $posts;
	}

	/**
	 * Prepares the blog navigation
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function prepareNavigation(EasyBlogPost &$post, $exclusion = array())
	{
		// Get the menu params associated with this post
		$params = $post->getMenuParams();

		$navigationType = $params->get('post_navigation_type', 'site');
		$paginationType = $params->get('pagination_style', 'normal');

		$model = EB::model('Blog');
		$navigation = $model->getPostNavigation($post, $navigationType, $exclusion);

		if ($navigation->prev) {

			$navigation->prev->link = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $navigation->prev->id);
			$navigation->prev->title = EBString::strlen($navigation->prev->title) > 50 ? EBString::substr($navigation->prev->title, 0, 50) . JText::_('COM_EASYBLOG_ELLIPSES') : $navigation->prev->title;
		}

		if ($paginationType == 'autoload' && $navigationType == 'random') {
			$navigation->next = false;
		}

		if ($navigation->next) {
			$nextPost = EB::post($navigation->next->id);

			$navigation->next->link = $nextPost->getPermalink();
			$navigation->next->title = EBString::strlen($navigation->next->title) > 50 ? EBString::substr($navigation->next->title, 0, 50) . JText::_('COM_EASYBLOG_ELLIPSES') : $navigation->next->title;
		}

		return $navigation;
	}
}
