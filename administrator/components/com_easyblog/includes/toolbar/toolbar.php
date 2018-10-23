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

class EasyBlogToolbar extends EasyBlog
{
	public function __construct()
	{
		parent::__construct();

		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->my = JFactory::getUser();
		$this->config = EB::config();
		$this->info = EB::info();
		$this->jconfig = EB::jconfig();
		$this->acl = EB::acl();
	}

	/**
	 * Retrieves the toolbar for the site.
	 *
	 * @since   5.2.0
	 * @access  public
	 */
	public function html($mobile = false)
	{
		$activeMenu = JFactory::getApplication()->getMenu()->getActive();
		$params = new JRegistry();

		if ($activeMenu) {
			$params = $activeMenu->params;
		}

		$heading = $params->get('show_page_heading', '');

		// If toolbar is disabled altogether, do not render anything
		if ((!$heading && !$this->config->get('layout_headers')) && (!$this->config->get('layout_toolbar') || !$this->acl->get('access_toolbar'))) {
			return;
		}

		// Get the current view
		$view = $this->input->get('view', '', 'cmd');

		// Get a list of available views
		$views = JFolder::folders(JPATH_COMPONENT . '/views');

		// Get the active view name
		$active = $this->input->get('view', '', 'cmd');

		// If the current active view doesn't exist on our known views, set the latest to be active by default.
		if (!in_array($active, $views)) {
			$active = 'latest';
		}

		// Rebuild the views
		$tmp = new stdClass();

		foreach ($views as $key) {
			$tmp->$key  = false;
		}

		// Reset back the views to the tmp variable
		$views = $tmp;

		// Set the active menu
		if (isset($views->$active)) {
			$views->$active = true;
		}

		// Get toolbar stuffs
		$title = $this->config->get('main_title');
		$desc = $this->config->get('main_description');
		$desc = nl2br($desc);
		$authorId = '';

		// Entry view, we want to load the toolbar
		if ($active == 'entry') {
			$blog = EB::table('Post');
			$blog->load($this->input->get('id', '', 'int'));

			$authorId = $blog->created_by;
		}

		// Blogger view, just get the id from the query
		if ($active == 'blogger') {
			$authorId = $this->input->get('id', 0, 'int');
		}

		// If the viewer is viewing a blogger, we'll need to display the header accordingly.
		if (($active == 'blogger' || $active == 'entry') && $authorId && $this->config->get('layout_headers_respect_author')) {

			$author = EB::user($authorId);

			$title = $author->title ? $author->title : '';
			$desc = $author->getDescription() ? $author->getDescription() : '';
		}

		// If the viewer is viewing a team
		if ($active == 'teamblog' && $this->config->get('main_includeteamblogdescription') && $this->config->get('layout_headers_respect_teamblog')) {

			// Only process the header when the viewer is on listings layout.
			if ($this->input->get('layout') == 'listings') {
				$team = EB::table('Teamblog');
				$team->load($this->input->get('id', '', 'int'));

				if ($team->includeTitleDesc()) {
					$title = $team->title ? JText::_($team->title) : $title;
					$desc = $team->getDescription() ? $team->getDescription() : $desc;
				}
			}
		}

		// Get the current menu id
		$itemId = $this->input->get('Itemid', 0, 'int');

		// Determines if the heading should be displayed
		if ($heading) {
			$title = $params->get('page_heading', $title);
		}

		// Get the total subscribers on the site
		$model = EB::model('Subscription');

		// Load up the subscription record for the current user.
		$subscription = EB::table('Subscriptions');

		if (!$this->my->guest) {
			$subscription->load(array('email' => $this->my->email, 'utype' => 'site'));
		}

		// Determines if this should be on blogger mode
		$bloggerMode = EBR::isBloggerMode();

		// Build the return url
		$return = base64_encode(JURI::getInstance()->toString());

		// Get total pending blog posts
		$totalPending = 0;
		$totalTeamRequests = 0;
		$totalPendingComments = 0;
		$totalReportPosts = 0;

		if (EB::isSiteAdmin() || $this->acl->get('moderate_entry') || ($this->acl->get('manage_pending') && $this->acl->get('manage_pending'))) {
			$pendingModel = EB::model('Pending');
			$totalPending = $pendingModel->getTotal();

			// Get total of report post
			$reportModel = EB::model('Reports');
			$totalReportPosts = $reportModel->getTotal();
		}

		// Get total team requests to join team.
		if (EB::isTeamAdmin()) {
			$teamModel = EB::model('TeamBlogs');
			$totalTeamRequests = $teamModel->getTotalRequest();
		}

		// Get total pending comments
		if (EB::isSiteAdmin() || $this->acl->get('moderate_comment')) {
			$commentModel = EB::model('Comments');
			$totalPendingComments = $commentModel->getTotalPending();
		}

		$showFooter = true;

		if (!$this->config->get('layout_latest') && !$this->config->get('layout_categories') && !$this->config->get('layout_tags')
			&& !$this->config->get('layout_bloggers') && !$this->config->get('layout_teamblog') && !$this->config->get('layout_archives')
			&& !$this->config->get('layout_calendar')) {
			$showFooter = false;
		}

		$showManage = false;

		if (EB::isSiteAdmin() || $this->acl->get('add_entry') || $this->acl->get('moderate_entry') || ($this->acl->get('manage_pending') && $this->acl->get('manage_pending')) || $this->acl->get('manage_comment') || $this->acl->get('create_category') ||
			$this->acl->get('create_tag') || $this->acl->get('create_team_blog')
		) {
			$showManage = true;
		}

		$layout = $this->input->get('layout', '', 'cmd');

		$query = $this->input->get('query', '', 'default');
		$categoryFilter = $this->input->get('category_id', 0, 'int');
		$query = trim($query);

		$categoryDropdown = EB::populateCategories('', '', 'select', 'category_id', $categoryFilter, false, true, false, array(), '', 'COM_EASYBLOG_FILTER_SELECT_CATEGORY');

		// Load the theme object
		$theme = EB::themes();
		$theme->set('categoryDropdown', $categoryDropdown);
		$theme->set('query', $query);
		$theme->set('showManage', $showManage);
		$theme->set('showFooter', $showFooter);
		$theme->set('totalPending', $totalPending);
		$theme->set('totalTeamRequests', $totalTeamRequests);
		$theme->set('totalPendingComments', $totalPendingComments);
		$theme->set('view', $view);
		$theme->set('subscription', $subscription);
		$theme->set('bloggerMode', $bloggerMode);
		$theme->set('heading', $heading);
		$theme->set('return', $return);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('views', $views);
		$theme->set('layout', $layout);
		$theme->set('totalReportPosts', $totalReportPosts);

		$namespace = 'site/toolbar/default';

		if ($mobile) {
			$namespace = 'site/toolbar/mobile.sidebar';
		}

		$output = $theme->output($namespace);

		return $output;
	}
}