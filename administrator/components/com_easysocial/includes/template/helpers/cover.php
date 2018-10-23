<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class ThemesHelperCover extends ThemesHelperAbstract
{
	/**
	 * Determines if the current active item is an app type
	 *
	 * @since	2.1.0
	 * @access	private
	 */
	private function isAppActive($currentActive)
	{
		$isAppActive = stristr($currentActive, 'apps.') !== false;

		return $isAppActive;
	}

	/**
	 * Renders the heading for an event
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function event(SocialEvent $event, $active = 'timeline')
	{
		static $items = array();

		if (isset($items[$event->id])) {
			return $items[$event->id];
		}

		$totalPendingGuests = 0;

		if ($event->isAdmin()) {
			$totalPendingGuests = $event->getTotalPendingGuests();
		}

		$cover = $event->getCoverData();


		$model = ES::model('Apps');
		$apps = $model->getEventApps($event->id);

		// We need to exclude certain apps since they are already rendered under the apps dropdown
		$exclusion = array('followers');
		$tmp = array();

		foreach ($apps as $app) {
			if (!in_array($app->element, $exclusion)) {
				$tmp[] = $app;
			}
		}

		$apps = $tmp;

		$returnUrl = base64_encode(JRequest::getUri());

		// Get the timeline link
		$defaultDisplay = $this->config->get('events.item.display', 'timeline');
		$timelinePermalink = $event->getPermalink();
		$aboutPermalink = ESR::events(array('id' => $event->getAlias(), 'page' => 'info', 'layout' => 'item'));

		if ($defaultDisplay == 'info') {
			$aboutPermalink = $timelinePermalink;
			$timelinePermalink = ESR::events(array('id' => $event->getAlias(), 'page' => 'timeline', 'layout' => 'item'));
		}

		$showMore = false;

		if ($event->allowPhotos() || $event->allowVideos() || $event->allowAudios() || $apps) {
			$showMore = true;
		}

		$isAppActive = $this->isAppActive($active);

		// Since some of the links are hidden on the apps, we need to check if apps should be active
		if (!$isAppActive) {
			// On mobile devices, we group up the audio, video and albums under the more dropdown
			if ($this->isMobile() && ($active == 'videos' || $active == 'audios' || $active == 'albums')) {
				$isAppActive = true;
			}
		}

		$cluster = null;

		if ($event->isClusterEvent()) {
			$cluster = $event->getCluster();
		}

		$theme = ES::themes();
		$theme->set('isAppActive', $isAppActive);
		$theme->set('timelinePermalink', $timelinePermalink);
		$theme->set('aboutPermalink', $aboutPermalink);
		$theme->set('totalPendingGuests', $totalPendingGuests);
		$theme->set('active', $active);
		$theme->set('event', $event);
		$theme->set('cover', $cover);
		$theme->set('apps', $apps);
		$theme->set('showMore', $showMore);
		$theme->set('returnUrl', $returnUrl);
		$theme->set('cluster', $cluster);

		$items[$event->id] = $theme->output('site/helpers/cover/event');

		return $items[$event->id];
	}

	/**
	 * Renders the heading for a page
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function page(SocialPage $page, $active = 'timeline')
	{
		static $items = array();

		if (isset($items[$page->id])) {
			return $items[$page->id];
		}

		$pendingFollowers = 0;

		if ($page->isAdmin()) {
			$pendingFollowers = $page->getTotalPendingFollowers();
		}

		$cover = $page->getCoverData();

		$model = ES::model('Apps');
		$apps = $model->getPageApps($page->id);

		$events = null;

		// These are the known apps that would be rendered below the cover
		$knownApps = array('events');

		// We need to exclude certain apps since they are already rendered under the apps dropdown
		$exclusion = array('followers');

		$exclusion = array_merge($exclusion, $knownApps);
		$tmp = array();

		foreach ($apps as $app) {
			if (!in_array($app->element, $exclusion)) {
				$tmp[] = $app;
			}

			if (in_array($app->element, $knownApps)) {
				${$app->element} = $app;
			}
		}

		$apps = $tmp;

		// Get the timeline link
		$defaultDisplay = $this->config->get('pages.item.display', 'timeline');
		$timelinePermalink = $page->getPermalink();
		$aboutPermalink = ESR::pages(array('id' => $page->getAlias(), 'type' => 'info', 'layout' => 'item'));

		if ($defaultDisplay == 'info') {
			$aboutPermalink = $timelinePermalink;
			$timelinePermalink = ESR::pages(array('id' => $page->getAlias(), 'type' => 'timeline', 'layout' => 'item'));
		}

		$showMore = false;

		if ($page->allowPhotos() || $page->allowVideos() || $page->allowAudios() || $page->canViewEvent() || $apps) {
			$showMore = true;
		}

		$isAppActive = $this->isAppActive($active);

		if (!$isAppActive) {

			// On mobile devices, we group up the audio, video and albums under the more dropdown
			if ($this->isMobile() && ($active == 'videos' || $active == 'audios' || $active == 'albums' || $active == 'events')) {
				$isAppActive = true;
			}

			// On mobile 
			if ($this->isMobile()) {

			}
		}

		$theme = ES::themes();
		$theme->set('isAppActive', $isAppActive);
		$theme->set('timelinePermalink', $timelinePermalink);
		$theme->set('aboutPermalink', $aboutPermalink);
		$theme->set('pendingFollowers', $pendingFollowers);
		$theme->set('active', $active);
		$theme->set('page', $page);
		$theme->set('cover', $cover);
		$theme->set('apps', $apps);
		$theme->set('showMore', $showMore);
		
		foreach ($knownApps as $knownApp) {
			$theme->set($knownApp, ${$knownApp});
		}

		$items[$page->id] = $theme->output('site/helpers/cover/page');

		return $items[$page->id];
	}

	/**
	 * Renders the heading for a group
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function group(SocialGroup $group, $active = 'timeline')
	{
		static $items = array();

		if (isset($items[$group->id])) {
			return $items[$group->id];
		}

		$pendingMembers = 0;

		if ($group->isAdmin()) {
			$pendingMembers = $group->getTotalPendingMembers();
		}

		$cover = $group->getCoverData();

		$model = ES::model('Apps');
		$apps = $model->getGroupApps($group->id);

		$events = null;
		
		// These are the known apps that would be rendered below the cover
		$knownApps = array('events');

		// We need to exclude certain apps since they are already rendered under the apps dropdown
		$exclusion = array('members');
		$exclusion = array_merge($exclusion, $knownApps);

		$tmp = array();

		foreach ($apps as $app) {
			if (!in_array($app->element, $exclusion)) {
				$tmp[] = $app;
			}

			if (in_array($app->element, $knownApps)) {
				${$app->element} = $app;
			}
		}

		$apps = $tmp;

		// Get the timeline link
		$defaultDisplay = $this->config->get('groups.item.display', 'timeline');
		$timelinePermalink = $group->getPermalink();
		$aboutPermalink = ESR::groups(array('id' => $group->getAlias(), 'type' => 'info', 'layout' => 'item'));

		if ($defaultDisplay == 'info') {
			$aboutPermalink = $timelinePermalink;
			$timelinePermalink = ESR::groups(array('id' => $group->getAlias(), 'type' => 'timeline', 'layout' => 'item'));
		}

		$showMore = false;

		if ($group->allowPhotos() || $group->allowVideos() || $group->allowAudios() || $group->canViewEvent() || $apps) {
			$showMore = true;
		}

		$isAppActive = $this->isAppActive($active);

		if (!$isAppActive) {
			// On mobile devices, we group up the audio, video and albums under the more dropdown
			if ($this->isMobile() && ($active == 'videos' || $active == 'audios' || $active == 'albums' || $active == 'events')) {
				$isAppActive = true;
			}
		}

		$theme = ES::themes();
		$theme->set('isAppActive', $isAppActive);
		$theme->set('timelinePermalink', $timelinePermalink);
		$theme->set('aboutPermalink', $aboutPermalink);
		$theme->set('pendingMembers', $pendingMembers);
		$theme->set('active', $active);
		$theme->set('group', $group);
		$theme->set('cover', $cover);
		$theme->set('apps', $apps);
		$theme->set('showMore', $showMore);

		foreach ($knownApps as $knownApp) {
			$theme->set($knownApp, ${$knownApp});
		}

		$items[$group->id] = $theme->output('site/helpers/cover/group');

		return $items[$group->id];
	}

	/**
	 * Renders the heading for a user
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function user(SocialUser $user, $active = 'timeline')
	{
		static $items = array();

		if (isset($items[$user->id])) {
			return $items[$user->id];
		}

		// Get user's cover object
		$cover = $user->getCoverData();

		// If we're setting a cover
		$coverId = $this->input->get('cover_id', 0, 'int');

		// Load cover photo
		if ($coverId) {
			$coverTable = ES::table('Photo');
			$coverTable->load($coverId);

			// If the cover photo belongs to the user
			if ($coverTable->isMine()) {
				$newCover = $coverTable;
			}
		}

		// Determines if the avatar should be visible
		$photoTable = $user->getAvatarPhoto();
		$showAvatar = $photoTable && $this->my->getPrivacy()->validate('photos.view', $photoTable->id, SOCIAL_TYPE_PHOTO, $user->id);

		// Determines if the user can view the album of the user
		$showPhotoPopup = true;

		if ($photoTable) {
			$photoLib = ES::photo($user->id, SOCIAL_TYPE_USER, $photoTable);
			$showPhotoPopup = $photoLib->viewable();
		}

		// Get lists of badges of the user.
		$badges = $user->getBadges();

		// Determine if user has pending friends
		$pendingFriends = 0;

		if ($user->id == $this->my->id) {
			$model = ES::model('Friends');
			$pendingFriends = $model->getTotalPendingFriends($user->id);
		}

		// Get the timeline link
		$defaultDisplay = $this->config->get('users.profile.display', 'timeline');
		$timelinePermalink = $user->getPermalink();
		$aboutPermalink = ESR::profile(array('id' => $user->getAlias(), 'layout' => 'about'));

		if ($defaultDisplay == 'about') {
			$aboutPermalink = $timelinePermalink;
			$timelinePermalink = ESR::profile(array('id' => $user->getAlias(), 'layout' => 'timeline'));
		}

		// Retrieve list of apps for this user
		$profile = $user->getProfile();

		$model = ES::model('Apps');
		$apps = $model->getUserApps($user->id, true, array('includeDefault' => true));

		$appsDropdown = array();

		// These are core apps that is not included in model->getUserApps
		$coreApps = array('followers', 'pages', 'events', 'groups', 'polls');

		foreach ($coreApps as $core) {
			if ($this->config->get($core . '.enabled')) {
				$app = new stdClass;
				$app->title = JText::_('COM_EASYSOCIAL_' . strtoupper($core));
				$app->pageTitle = JText::_('COM_EASYSOCIAL_' . strtoupper($core));
				$app->permalink = ESR::$core(array('userid' => $user->getAlias()));
				$app->active = $core;

				$appsDropdown[] = $app;
			}
		}

		if (is_array($apps)) {
			foreach ($apps as $app) {
				$app->title = $app->getAppTitle();
				$app->pageTitle = $app->getPageTitle();
				$app->permalink = ESR::profile(array('id' => $user->getAlias(), 'appId' => $app->getAlias()));
				$app->active = 'apps.' . $app->element;

				$appsDropdown[] = $app;
			}
		}
		
		$showMore = false;

		if ($appsDropdown && $this->config->get('users.layout.sidebarapps') && count($appsDropdown) > 1) {
			$showMore = true;
		}

		$isAppActive = $this->isAppActive($active);

		// Since some of the links are hidden on the apps, we need to check if apps should be active
		if (!$isAppActive) {
			if ($active == 'groups' || $active == 'followers' || $active == 'pages' || $active == 'events' || $active == 'polls') {
				$isAppActive = true;
			}

			// On mobile devices, we group up the audio, video and albums under the more dropdown
			if ($this->isMobile() && ($active == 'videos' || $active == 'audios' || $active == 'albums')) {
				$isAppActive = true;
			}
		}

		$showOnlineState = $this->config->get('users.online.state', true);
		$showBrowseApps = $user->isViewer() && $this->config->get('users.layout.apps');

		$theme = ES::themes();
		$theme->set('showPhotoPopup', $showPhotoPopup);
		$theme->set('showOnlineState', $showOnlineState);
		$theme->set('isAppActive', $isAppActive);
		$theme->set('timelinePermalink', $timelinePermalink);
		$theme->set('aboutPermalink', $aboutPermalink);
		$theme->set('defaultDisplay', $defaultDisplay);
		$theme->set('pendingFriends', $pendingFriends);
		$theme->set('active', $active);
		$theme->set('apps', $apps);
		$theme->set('user', $user);
		$theme->set('cover', $cover);
		$theme->set('showAvatar', $showAvatar);
		$theme->set('showMore', $showMore);
		$theme->set('showBrowseApps', $showBrowseApps);
		$theme->set('badges', $badges);
		$theme->set('appsDropdown', $appsDropdown);

		$items[$user->id] = $theme->output('site/helpers/cover/user');

		return $items[$user->id];
	}
}
