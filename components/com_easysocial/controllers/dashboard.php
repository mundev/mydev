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

class EasySocialControllerDashboard extends EasySocialController
{
	/**
	 * Retrieves the stream contents.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getStream()
	{
		ES::requireLogin();
		ES::checkToken();


		$this->input->set('view', 'dashboard');

		$hashtags = array();

		// Get the type of the stream to load.
		$type = $this->input->get('type', '', 'word');

		// Get the stream
		$stream = ES::stream();

		if (!$type) {
			$this->view->setMessage('COM_EASYSOCIAL_STREAM_INVALID_FEED_TYPE', ES_ERROR);
			return $this->view->call(__FUNCTION__, $stream, $type);
		}

		// Default stream options
		$streamOptions = array();

		// Get feeds from user's friend list.
		if ($type == 'list') {

			// The id of the friend list.
			$id = $this->input->get('id', 0, 'int');

			$list = ES::table('List');
			$list->load($id);

			if (!$id || !$list->id) {
				$this->view->setMessage('COM_EASYSOCIAL_STREAM_INVALID_LIST_ID_PROVIDED', ES_ERROR);
				return $this->view->call(__FUNCTION__, $stream, $type);
			}

			// Get list of users from this list.
			$friends = $list->getMembers();

			if ($friends) {
				$streamOptions['listId'] = $id;
			} else {
				$stream->filter = 'list';
			}
		}

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

		$streamOptions['nosticky'] = false;

		if ($type == 'following') {
			$streamOptions['context'] = SOCIAL_STREAM_CONTEXT_TYPE_ALL;
			$streamOptions['type'] = 'follow';
			$stream->stickies = false;
		}

		// Filter by bookmarks
		if ($type == 'bookmarks' && $this->config->get('stream.bookmarks.enabled')) {
			$streamOptions['guest'] = true;
			$streamOptions['type'] = 'bookmarks';
			$stream->stickies = false;
			$streamOptions['nosticky'] = false;
		}

		// Get feeds from everyone
		if ($type == 'everyone') {
			$streamOptions['guest'] = true;
			$streamOptions['ignoreUser'] = true;
		}

		if ($type == 'appFilter') {

			// we need to use string and not 'word' due to some app name has number. e.g k2
			$appType = $this->input->get('id', '', 'string');
			$streamOptions['context'] = $appType;
			$streamOptions['aspect'] = 'dashboard';
			$stream->filter	= 'custom';
		}

		// Filter by sticky
		if ($type == 'sticky' && $this->config->get('stream.pin.enabled')) {
			// If the type is sticky, we override all the $streamoptions
			$stream->stickies = false;
			$streamOptions = array('userId' => $this->my->id, 'type' => 'sticky', 'includeClusterSticky' => true);
		}

		// Filter stream items by event
		if ($type == 'event') {
			$id    = $this->input->get('id', 0, 'int');
			$event = ES::event($id);

			// Check if the user is a member of the group
			if (!$event->getGuest()->isGuest() && !$this->my->isSiteAdmin()) {
				$this->view->setMessage('COM_EASYSOCIAL_STREAM_EVENTS_NO_PERMISSIONS', ES_ERROR);
				return $this->view->call(__FUNCTION__, $stream, $type);
			}

			//lets get the sticky posts 1st
			$stickies = $stream->getStickies(array('clusterId' => $id, 'clusterType' => SOCIAL_TYPE_EVENT, 'limit' => 0));
			if ($stickies) {
				$stream->stickies = $stickies;
			}

			$streamOptions = array('clusterId' => $id , 'clusterType' => SOCIAL_TYPE_EVENT, 'nosticky' => true);
		}

		if ($type == 'group') {

			$id    = $this->input->get('id', 0, 'int');
			$group = ES::group($id);

			// Check if the user is a member of the group
			if (!$group->isMember() && !$this->my->isSiteAdmin()) {
				$this->view->setMessage('COM_EASYSOCIAL_STREAM_GROUPS_NO_PERMISSIONS', ES_ERROR);
				return $this->view->call(__FUNCTION__, $stream, $type);
			}

			//lets get the sticky posts 1st
			$stickies = $stream->getStickies(array('clusterId' => $id, 'clusterType' => SOCIAL_TYPE_GROUP, 'limit' => 0));
			if ($stickies) {
				$stream->stickies = $stickies;
			}

			// $stream->get(array('clusterId' => $id , 'clusterType' => SOCIAL_TYPE_GROUP, 'nosticky' => true));

			$streamOptions = array('clusterId' => $id , 'clusterType' => SOCIAL_TYPE_GROUP, 'nosticky' => true);
		}

		if ($type == 'page') {

			$id = $this->input->get('id', 0, 'int');
			$page = ES::page($id);

			// Check if the user is a member of the page
			if (!$page->isMember() && !$this->my->isSiteAdmin()) {
				$this->view->setMessage('COM_EASYSOCIAL_STREAM_PAGES_NO_PERMISSIONS', ES_ERROR);
				return $this->view->call(__FUNCTION__, $stream, $type);
			}

			//lets get the sticky posts 1st
			$stickies = $stream->getStickies(array('clusterId' => $id, 'clusterType' => SOCIAL_TYPE_PAGE, 'limit' => 0));

			if ($stickies) {
				$stream->stickies = $stickies;
			}

			$streamOptions = array('clusterId' => $id , 'clusterType' => SOCIAL_TYPE_PAGE, 'nosticky' => true);
		}

		$streamFilter = '';

		// custom filter.
		if ($type == 'custom') {

			// Get the id
			$id = $this->input->get('id', 0, 'int');

			$streamFilter = ES::table('StreamFilter');
			$streamFilter->load($id);

			$stream->filter = 'custom';

			if ($streamFilter->id) {
				$hashtags = $streamFilter->getHashTag();
				$hashtags = explode(',', $hashtags);

				if ($hashtags) {
					$streamOptions = array('context' => SOCIAL_STREAM_CONTEXT_TYPE_ALL , 'tag' => $hashtags, 'nosticky' => true);

					$hashtagRule = $this->config->get('stream.filter.hashtag', '');
					if ($hashtagRule == 'and') {
						$streamOptions['matchAllTags'] = true;
					}
				}
			}
		}

		$stream->get($streamOptions);

		return $this->view->call(__FUNCTION__, $stream, $type, $hashtags, $streamFilter);
	}

	/**
	 * Retrieves the dashboard contents.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getAppContents()
	{
		ES::requireLogin();
		ES::checkToken();

		$appId = $this->input->get('appId', 0, 'int');

		$app = ES::table('App');
		$state = $app->load($appId);

		if (!$appId || !$state) {
			$this->view->setMessage('COM_EASYSOCIAL_APPS_INVALID_APP_ID_PROVIDED', ES_ERROR);
			return $this->view->call(__FUNCTION__ , $app);
		}

		// Check if the user has access to this app or not.
		if (!$app->accessible($this->my->id)) {
			$this->view->setMessage('COM_EASYSOCIAL_APPS_PLEASE_INSTALL_APP_FIRST', ES_ERROR);
			return $this->view->call(__FUNCTION__ , $app);
		}

		return $this->view->call(__FUNCTION__ , $app);
	}
}
