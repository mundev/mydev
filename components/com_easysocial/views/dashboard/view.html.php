<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasySocialViewDashboard extends EasySocialSiteView
{
	/**
	 * Responsible to output the dashboard layout for the current logged in user.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// If the user is not logged in, display the dashboard's unity layout.
		if ($this->my->guest) {
			return $this->guests();
		}

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Define page properties
		$title = $this->my->getName() . ' - ' . JText::_('COM_EASYSOCIAL_PAGE_TITLE_DASHBOARD');
		$this->page->title($title);
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_DASHBOARD');

		// Set Meta data
		ES::setMeta();

		$contents = '';
		$start = $this->config->get('users.dashboard.start');

		// Check if there is any stream filtering or not.
		$filter	= $this->input->get('type', $start, 'word');

		// The filter 'all' is taken from the menu item the setting. all == user & friend, which mean in this case, is the 'me' filter.
		$filter = $filter == 'all' ? 'me' : $filter;

		// Used in conjunction with type=appFilter
		$filterId = '';

		// Determine if the current request is for "tags"
		$hashtag = $this->input->get('tag', '', 'default');
		$hashtagAlias = $hashtag;

		if (!empty($hashtag)) {
			$filter = 'hashtag';
		}

		// Retrieve user's status
		$story = ES::get('Story', SOCIAL_TYPE_USER);
		$story->setTarget($this->my->id);

		// Retrieve user's stream
		$stream = ES::stream();
		$stream->story  = $story;

		// Determines if we should be rendering streams from a cluster
		$cluster = false;
		$clusterId = false;

		$tags = array();

		// Default stream options
		$streamOptions = array();
		$feedOptions = array('filter' => $filter);

		// Filter by specific friend list item
		$listId = $this->input->get('listId', 0, 'int');

		if ($filter == 'list' && !empty($listId)) {

			$list = ES::table('List');
			$list->load($listId);

			$feedOptions['listId'] = $list->id;

			// Get list of users from this list.
			$friends = $list->getMembers();

			if ($friends) {
				$streamOptions['listId'] = $listId;
			} else {
				$stream->filter = 'list';
			}
		}

		// Get sticky items
		$stickyIds = array();
		$stickyOptions = array('userId' => $this->my->id, 'type' => 'sticky', 'adminOnly' => true);
		$stickies = $stream->getStickies($stickyOptions);

		// Only assign stickies if the result is an array
		if (is_array($stickies)) {

			foreach($stickies as $stick) {
				$stickyIds[] = $stick->uid;
			}

			$streamOptions['excludeStreamIds'] = $stickyIds;
			$stream->stickies = $stickies;
		}

		// By default we don't want any sticky item
		// in dashboard, we allow pinned items from all users.
		$streamOptions['nosticky'] = false;

		// Filter by specific #hashtag
		if ($filter == 'hashtag') {
			$tag = $this->input->get('tag', '', 'default');
			$hashtag = $tag;
			$tags = array($tag);

			$streamOptions['tag'] = $tag;
			$feedOptions['tag'] = $hashtag;
		}

		// Filter by everyone
		if ($filter == 'everyone') {
			$streamOptions['guest'] = true;
			$streamOptions['ignoreUser'] = true;
		}

		// Filter by following
		if ($filter == 'following') {
			$streamOptions['context'] = SOCIAL_STREAM_CONTEXT_TYPE_ALL;
			$streamOptions['type'] = 'follow';
			$stream->stickies = false;
		}

		// Filter by bookmarks
		if ($filter == 'bookmarks') {

			if (!$this->config->get('stream.bookmarks.enabled')) {
				$this->info->set(false, 'COM_ES_NOT_ALLOWED_TO_ACCESS', 'error');
				$this->redirect(ESR::dashboard());
				return;
			}

			$streamOptions['guest'] = true;
			$streamOptions['type'] = 'bookmarks';
			$streamOptions['nosticky'] = false;
			$stream->stickies = false;
		}

		// Filter by apps
		if ($filter == 'appFilter') {
			$appType  = $this->input->get('filterid', '', 'string');
			$filterId = $appType;

			$stream->filter	= 'custom';
			$streamOptions['context'] = $appType;
			$streamOptions['aspect'] = 'dashboard';
		}

		$streamFilter = '';

		// Filter by custom filters
		if ($filter == 'filter') {

			$filterId = $this->input->get('filterid', 0, 'int');

			$streamFilter = ES::table('StreamFilter');
			$streamFilter->load($filterId);

			$stream->filter = 'custom';

			if ($streamFilter->id) {
				$hashtags = $streamFilter->getHashTag();
				$tags = explode(',', $hashtags);

				if ($tags) {
					$streamOptions['context'] = SOCIAL_STREAM_CONTEXT_TYPE_ALL;
					$streamOptions['tag'] = $tags;

					$hashtagRule = $this->config->get('stream.filter.hashtag', '');
					if ($hashtagRule == 'and') {
						$streamOptions['matchAllTags'] = true;
					}
				}

				$feedOptions['filterid'] = $streamFilter->getAlias();
			}
		}

		// Stream filter form
		if ($filter == 'filterForm') {
			$id = $this->input->get('filterid', 0, 'int');

			// Get the filter form
			$contents = ES::stream()->getFilterForm($this->my->id, SOCIAL_TYPE_USER, $id);
		}

		// Filter by sticky
		if ($filter == 'sticky') {

			if (!$this->config->get('stream.pin.enabled')) {
				$this->info->set(false, 'COM_ES_NOT_ALLOWED_TO_ACCESS', 'error');
				$this->redirect(ESR::dashboard());
				return;
			}

			// If the type is sticky, we override all the $streamoptions
			$stream->stickies = false;
			$streamOptions = array('userId' => $this->my->id, 'type' => 'sticky', 'includeClusterSticky' => true);
		}

		$stream->get($streamOptions);
		$story->setHashtags($tags);

		$id = $this->input->get('id', 0, 'int');

		if ($id) {
			$feedOptions['id'] = $id;
		}

		// Add the rss links
		if ($this->config->get('stream.rss.enabled')) {
			$this->addRss(ESR::dashboard($feedOptions, false));
		}

		$filterList = array();
		$friendLists = array();
		$appFilters = array();
		$createCustomFilter = array();
		$groups = array();
		$pages = array();
		$events = array();
		$showMoreGroups = false;
		$showMorePages = false;
		$showMoreEvents = false;

		if ($this->config->get('users.dashboard.sidebar') != 'hidden') {
			$model = ES::model('Stream');

			// Get stream filter list
			$filterList = $model->getFilters($this->my->id);

			if ($this->config->get('users.dashboard.appfilters')) {
				$appFilters = $model->getAppFilters(SOCIAL_TYPE_USER);
			}

			$groups = $this->getGroups();
			$showMoreGroups = $this->config->get('users.dashboard.groupslimit') < $this->my->getTotalGroups();

			$pages = $this->getPages();
			$showMorePages = $this->config->get('users.dashboard.pageslimit') < $this->my->getTotalPages();

			$events = $this->getEvents();
			$showMoreEvents = $this->config->get('users.dashboard.eventslimit') < $this->my->getTotalEvents();

			if ($this->config->get('users.dashboard.customfilters')) {
				$createCustomFilter = array('link' => ESR::dashboard(array('type' => 'filterForm')), 'icon' => 'fa-plus', 'attributes' => 'data-create-filter');
			}

			// Retrieves a list of available friend lists
			$listsModel = ES::model('Lists');
			$listLimit = $this->config->get('lists.display.limit');

			// Get the friend's list.
			$friendLists = $listsModel->setLimit($listLimit)->getLists(array('user_id' => $this->my->id));
		}

		$this->set('createCustomFilter', $createCustomFilter);
		$this->set('events', $events);
		$this->set('showMoreEvents', $showMoreEvents);

		$this->set('groups', $groups);
		$this->set('showMoreGroups', $showMoreGroups);
		$this->set('pages', $pages);
		$this->set('showMorePages', $showMorePages);
		$this->set('friendLists', $friendLists);
		$this->set('title'	, $title);

		$this->set('cluster', $cluster);
		$this->set('clusterId', $clusterId);
		$this->set('rssLink', $this->rssLink);

		$this->set('filterId', $filterId);
		$this->set('appFilters', $appFilters);
		$this->set('hashtag', $hashtag);
		$this->set('hashtagAlias', $hashtagAlias);
		$this->set('listId', $listId);
		$this->set('filter', $filter);

		$this->set('contents', $contents);
		$this->set('user', $this->my);
		$this->set('stream', $stream);
		$this->set('filterList', $filterList);
		$this->set('streamFilter', $streamFilter);

		return parent::display('site/dashboard/default/default');
	}

	/**
	 * Retrieves events that should appear on the sidebar
	 *
	 * @since	2.0
	 * @access	public
	 */
	private function getEvents()
	{
		// Retrieve participated events
		$model = ES::model('Events');
		$options = array('guestuid' => $this->my->id, 'ongoing' => true, 'upcoming' => true, 'ordering' => 'start');

		$options['limit'] = $this->config->get('users.dashboard.eventslimit');

		// Only show published event
		$options['state'] = SOCIAL_CLUSTER_PUBLISHED;

		$events = $model->getEvents($options);

		return $events;
	}

	/**
	 * Retrieves pages that should be displayed on the sidebar
	 *
	 * @since	2.0
	 * @access	public
	 */
	private function getPages()
	{
		$model = ES::model('Pages');
		$limit = $this->config->get('users.dashboard.pageslimit');
		$pages = $model->getUserPages($this->my->id, 0, $limit);

		return $pages;
	}

	/**
	 * Retrieves groups that should be displayed on the sidebar
	 *
	 * @since	2.0
	 * @access	public
	 */
	private function getGroups()
	{
		$model = ES::model('Groups');
		$limit = $this->config->get('users.dashboard.groupslimit');
		$groups = $model->getUserGroups($this->my->id, 0, $limit);

		return $groups;
	}

	/**
	 * Displays the guest view for the dashboard
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function guests()
	{
		// Add the rss links
		if ($this->config->get('stream.rss.enabled')) {
			$this->addRss(ESR::dashboard(array(), false));
		}

		// Default stream filter
		$filter = 'everyone';

		// Determine if the current request is for "tags"
		$hashtag = $this->input->get('tag', '', 'default');

		if (!empty($hashtag)) {
			$filter = 'hashtag';
		}

		// Get the layout to use.
		$stream = ES::stream();
		$stream->getPublicStream($this->config->get('stream.pagination.pagelimit', 10), 0, $hashtag);

		// Get default return url
		$return = ESR::getMenuLink($this->config->get('general.site.login'));
		$return = ES::getCallback($return);

		// If return value is empty, always redirect back to the dashboard
		if (!$return) {
			$return = ESR::dashboard(array(), false);
		}

		// In guests view, there shouldn't be an app id
		$appId = $this->input->get('appId', '', 'default');

		if ($appId) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_IS_NOT_AVAILABLE'));
		}

		// Ensure that the return url is always encoded correctly.
		$return = base64_encode($return);

		// check if there are any modules assigned in dashboard sidebar position or not.
		$hasSidebarModules = $this->hasSideBarModules();

		$this->set('rssLink', $this->rssLink);
		$this->set('filter', $filter);
		$this->set('hashtag', $hashtag);
		$this->set('stream', $stream);
		$this->set('return', $return);
		$this->set('hasSidebarModules', $hasSidebarModules);

		echo parent::display('site/dashboard/guests/default');
	}

	/**
	 * private method to check if there is any modules to display to guest or not.
	 *
	 * @since	2.0
	 * @access	public
	 */
	private function hasSideBarModules()
	{
		$sidebarPositions = array('es-dashboard-sidebar-top', 'es-dashboard-sidebar-before-newsfeeds', 'es-dashboard-sidebar-after-newsfeeds', 'es-dashboard-sidebar-bottom');

		foreach ($sidebarPositions as $position) {
			$modules = JModuleHelper::getModules($position);

			$checkedModules = array();
			// check for mod_easysocial_profile_statistic modules. if exits, remove this module as this module is not meant for guest.
			if ($modules) {
				foreach ($modules as $module) {
					if ($module->module != 'mod_easysocial_profile_statistic') {
						$checkedModules[] = $module;
					}
				}
			}

			if ($checkedModules) {
				return true;
			}
		}

		return false;
	}
}
