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
	 * Get additional cluster items to render on the sidebar
	 * since the sidebar only displays limited clusters based on theme settings.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getMoreClusters()
	{
		$type = $this->input->get('type', '', 'word');
		$allowedClusters = array('groups', 'events', 'pages');

		if (!in_array($type, $allowedClusters)) {
			return $this->exception();
		}

		$method = 'getMore' . ucfirst($type);
		$output = $this->$method();

		return $this->ajax->resolve($output);
	}

	private function getMoreEvents()
	{
		// Retrieve user's events
		$model = ES::model('Events');
		$options = array('guestuid' => $this->my->id, 'ongoing' => true, 'upcoming' => true, 'ordering' => 'start');

		$events = $model->getEvents($options);

		$theme = ES::themes();
		$theme->set('events', $events);
		$output = $theme->output('site/dashboard/default/filter.events');

		return $output;
	}

	private function getMorePages()
	{
		$model = ES::model('Pages');
		$pages = $model->getUserPages($this->my->id, 0);

		$theme = ES::themes();
		$theme->set('pages', $pages);
		$output = $theme->output('site/dashboard/default/filter.pages');

		return $output;
	}

	private function getMoreGroups()
	{
		$model = ES::model('Groups');
		$groups = $model->getUserGroups($this->my->id);

		$theme = ES::themes();
		$theme->set('groups', $groups);
		$output = $theme->output('site/dashboard/default/filter.groups');

		return $output;
	}

	/**
	 * Retrieves the stream contents.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getStream($stream , $type = '', $hashtags = array(), $streamFilter = '')
	{
		// Generate RSS link for this view
		$options = array('filter' => $type);
		$id = $this->input->get('id', 0, 'default');

		if ($id) {

			if ($type == 'custom') {
				$sfilter = FD::table('StreamFilter');
				$sfilter->load($id);

				$options['filter'] = 'filter';
				$options['filterid'] = $sfilter->id . ':' . $sfilter->alias;
			} else if ($type == 'list') {
				$options['listId'] = $id;
			} else {
				$options['id'] = $id;
			}
		}

		$this->addRss(FRoute::dashboard($options, false));

		// Get the stream count
		$count = $stream->getCount();

		// Retrieve the story lib
		$story = FD::get('Story', SOCIAL_TYPE_USER);

		// Get the tags
		if ($hashtags) {
			$hashtags = FD::makeArray($hashtags);
			$story->setHashtags($hashtags);
		}

		$allowedClusters = array(SOCIAL_TYPE_GROUP, SOCIAL_TYPE_EVENT, SOCIAL_TYPE_PAGE);
		$cluster = false;

		// If the stream is a group type, we need to set the story
		if (in_array($type, $allowedClusters)) {
			$story = FD::get('Story', $type);

			$clusterId = $this->input->getInt('id', 0);

			$story->setCluster($clusterId, $type);
			$story->showPrivacy(false);

			$cluster = ES::cluster($type, $clusterId);
		}

		// Set the story to the stream
		$stream->story = $story;

		$theme = ES::themes();
		$theme->set('rssLink', $this->rssLink);
		$theme->set('cluster', $cluster);
		$theme->set('hashtag', false);
		$theme->set('stream', $stream);
		$theme->set('story', $story);
		$theme->set('streamcount', $count);
		$theme->set('streamFilter', $streamFilter);

		$contents = $theme->output('site/dashboard/default/feeds');

		return $this->ajax->resolve($contents, $count);
	}

	/**
	 * Hides the welcome message
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function hideWelcome()
	{
		$this->my->setConfig('showwelcome', 0);
		$this->my->storeConfig();

		$message = JText::_('COM_EASYSOCIAL_WELCOME_MESSAGE_DONE');
		return $this->ajax->resolve($message);
	}
}
