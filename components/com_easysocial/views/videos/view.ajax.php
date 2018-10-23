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

class EasySocialViewVideos extends EasySocialSiteView
{
	/**
	 * Renders the embed video dialog
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function embed()
	{
		$id = $this->input->get('id', 0, 'int');

		$table = ES::table('Video');
		$table->load($id);

		$video = ES::video($table);

		$theme = ES::themes();
		$theme->set('video', $video);
		$output = $theme->output('site/videos/dialogs/embed');

		return $this->ajax->resolve($output);
	}

	/**
	 * Processes videos
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function process(SocialVideo $video)
	{
		return $this->ajax->resolve();
	}

	/**
	 * Returns the video after upload
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function uploadFile($video)
	{
		if ($video->getError()) {
			return $this->ajax->reject($video->getError());
		}

		return $this->ajax->resolve($video->table);
	}

	/**
	 * Returns the status of the processing
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function status($video, $progress)
	{
		$permalink = '';

		// this method is being called from both backend and frontend. we need to redirect to proper page. #597
		if (JFactory::getApplication()->isAdmin()) {
			$permalink = 'index.php?option=com_easysocial&view=videos&layout=form&id=' . $video->id;
		} else {
			$permalink = $video->getPermalink(false);
		}

		// Once the progress is complete, we need to send the url to the video
		if ($progress === true) {

			if (JFactory::getApplication()->isAdmin()) {
				$this->setMessage('COM_EASYSOCIAL_VIDEOS_UPDATED_SUCCESS', SOCIAL_MSG_SUCCESS);
				$this->info->set($this->getMessage());
			}

			return $this->ajax->resolve($permalink, 'done', $video->export(), $video->getThumbnail());
		}

		return $this->ajax->resolve($permalink, $progress);
	}

	/**
	 * Displays confirmation to feature videos
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function confirmFeature()
	{
		// Get the video id
		$id = $this->input->get('id', 0, 'int');

		// Determines if the user wants to specify a custom callback url
		$callback = $this->input->get('callbackUrl', '', 'default');

		// Ensure that the user is really allowed to feature this video
		$videoTable = ES::table('Video');
		$videoTable->load($id);

		$video = ES::video($videoTable->uid, $videoTable->type, $videoTable);

		if (!$video->canFeature()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_FEATURE'));
		}

		$theme = ES::themes();
		$theme->set('id', $id);
		$theme->set('callback', $callback);

		$output = $theme->output('site/videos/dialogs/feature');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays confirmation to unfeature videos
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function confirmUnfeature()
	{
		// Get the video id
		$id = $this->input->get('id', 0, 'int');

		// Determines if the user wants to specify a custom callback url
		$callback = $this->input->get('callbackUrl', '', 'default');

		// Ensure that the user is really allowed to delete this video
		$videoTable = ES::table('Video');
		$videoTable->load($id);

		$video = ES::video($videoTable->uid, $videoTable->type, $videoTable);

		if (!$video->canUnfeature()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_UNFEATURE'));
		}

		$theme = ES::themes();
		$theme->set('id', $id);
		$theme->set('callback', $callback);

		$output = $theme->output('site/videos/dialogs/unfeature');

		return $this->ajax->resolve($output);
	}

	/**
	 * Post processing after a tag is deleted
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function removeTag()
	{
		return $this->ajax->resolve();
	}

	/**
	 * Displays confirmation to delete videos
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function confirmDelete()
	{
		// Get the video id
		$id = $this->input->get('id', 0, 'int');

		$videoTable = ES::table('Video');
		$videoTable->load($id);

		// Ensure that the user is really allowed to delete this video
		$video = ES::video($videoTable);

		if (!$video->canDelete()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_TO_DELETE'));
		}

		$theme = ES::themes();
		$theme->set('id', $id);

		$output = $theme->output('site/videos/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Display confirmation to delete video filter
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function confirmDeleteFilter()
	{
		// Get the filter id
		$id = $this->input->get('id', 0, 'int');
		$cid = $this->input->get('cid', 0, 'int');
		$clusterType = $this->input->get('clusterType', '', 'string');

		$theme = ES::themes();
		$theme->set('id', $id);
		$theme->set('cid', $cid);
		$theme->set('clusterType', $clusterType);

		$output = $theme->output('site/videos/dialogs/deleteFilter');

		return $this->ajax->resolve($output);
	}

	/**
	 * Post processing after video is tagged with people
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function tag(SocialVideo $video, $tags = array())
	{
		$theme = ES::themes();
		$theme->set('video', $video);
		$theme->set('usersTags', $tags);

		$output = $theme->output('site/videos/item/tags.user');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays encoding message
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function showEncodingMessage()
	{
		$theme = ES::themes();

		$output = $theme->output('site/videos/dialogs/encoding');

		return $this->ajax->resolve($output);
	}

	/**
	 * Displays a dialog for users to tag
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function tagPeople()
	{
		$theme = ES::themes();

		// Get the video id
		$id = $this->input->get('id', 0, 'int');
		$exclusion = $this->input->get('exclusion', array(), 'array');

		$video = ES::video($id);

		// Get a list of users that are already tagged with this video
		$tags = $video->getTags();

		$theme->set('exclusion', $exclusion);

		$output = $theme->output('site/videos/dialogs/tag');

		return $this->ajax->resolve($output);
	}

	/**
	 * Post processing after retrieving videos
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getVideos($videos = array(), $featuredVideos = array(), $pagination = null, $filter = null, $adapter, $rawUid, $uid, $type, $hashtags, $tagsFilter, $isSortingRequest, $from = false, $categoryId = false, $activeCategory = null, $cluster = null)
	{
		$output = '';

		// Generate correct return urls for operations performed here
		$returnUrl = ESR::videos();

		if ($uid && $type) {
			$returnUrl = $adapter->getAllVideosLink($filter);
		}

		$returnUrl = ES::formatCallback($returnUrl);
		$returnUrl = base64_encode($returnUrl);

		// Get the sorting URL
		$sortItems = new stdClass();
		$sortingTypes = array('latest', 'alphabetical', 'popular', 'commented', 'likes');
		foreach ($sortingTypes as $sortingType) {

			$sortItems->{$sortingType} = new stdClass();

			// attributes
			$sortAttributes = array('data-sorting', 'data-filter="' . $filter . '"', 'data-type="' . $sortingType . '"');

			//url
			$urlOptions = array();

			if ($categoryId) {
				$urlOptions['categoryId'] = $categoryId;
			} else {
				$urlOptions['filter'] = $filter;
			}

			$urlOptions['sort'] = $sortingType;

			$sortUrl = ESR::videos($urlOptions);

			$sortItems->{$sortingType}->attributes = $sortAttributes;
			$sortItems->{$sortingType}->url = $sortUrl;
		}

		// We define this browse view same like $showsidebar. 
		// so it won't break when other customer that still using $showsidebar
		$browseView = !$uid;

		$theme = ES::themes();
		$theme->set('browseView', $browseView);
		$theme->set('activeCategory', $activeCategory);
		$theme->set('returnUrl', $returnUrl);
		$theme->set('rawUid', $rawUid);
		$theme->set('uid', $uid);
		$theme->set('type', $type);
		$theme->set('hashtags', $hashtags);
		$theme->set('tagsFilter', $tagsFilter);
		$theme->set('sortItems', $sortItems);
		$theme->set('sort', $sortingType);
		$theme->set('from', $from);
		$theme->set('cluster', $cluster);
		
		// if this is a sorting request.
		if ($isSortingRequest) {
			$contents = '';
			// Now retrieve the contents of the normal videos
			$theme->set('videos', $videos);
			$theme->set('showSidebar', true);
			$theme->set('pagination', $pagination);
			$contents .= $theme->output('site/videos/default/item.list');

			return $this->ajax->resolve($contents);
		}

		// below are the procesing when filter is click.
		$theme->set('featuredVideos', $featuredVideos);
		$featuredOutput = '';

		// If there is a list of featured videos, we need to output them as well
		if ($featuredVideos) {
			$theme->set('showSidebar', true);
			$theme->set('filter', 'featured');
			$theme->set('videos', $featuredVideos);
			$theme->set('pagination', '');
			$featuredOutput = $theme->output('site/videos/default/item.list');
		}

		$theme->set('featuredOutput', $featuredOutput);

		$theme->set('filter', $filter);
		$theme->set('isFeatured', false);

		// Since ajax calls should only happen when sidebar is available, we default it to true
		$showSidebar = true;

		if ($filter == 'featured') {
			$theme->set('isFeatured', true);
			// $showSidebar = false;
		}

		$theme->set('showSidebar', $showSidebar);

		if ($pagination) {
			$theme->set('pagination', $pagination);
		}

		$theme->set('videos', $videos);

		$output .= $theme->output('site/videos/default/items');

		return $this->ajax->resolve($output);
	}

	/**
	 * Display Filter Form
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFilterForm()
	{
		$theme = ES::themes();

		// Get the filter id if the user is editing the filter
		$filterType = $this->input->get('type', '', 'word');
		$id = $this->input->get('id', 0, 'int');

		// Get cluster id
		$cid = $this->input->get('cid', 0, 'int');
		$clusterType = $this->input->get('clusterType', '', 'string');

		// Try to load the filter
		$filter = ES::table('TagsFilter');

		if ($id) {
			$filter->load($id);
		}

		$theme->set('filter', $filter);
		$theme->set('filterType', $filterType);
		$theme->set('cid', $cid);
		$theme->set('clusterType', $clusterType);

		$output = $theme->output('site/videos/form/filter');

		return $this->ajax->resolve($output);
	}
}
