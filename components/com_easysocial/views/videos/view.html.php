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

ES::import('site:/views/views');

class EasySocialViewVideos extends EasySocialSiteView
{
	/**
	 * Renders the all videos page
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Default page title 'All Videos'
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_VIDEOS_FILTER_ALL');

		// Get the video model
		$model = ES::model('Videos');

		// Get all passed variables
		$filter = $this->input->get('filter', 'all', 'word');
		$activeCategory = $this->input->get('categoryId', '', 'int');
		$rawUid = $this->input->get('uid', '', 'default');
		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'word');
		$sort = $this->input->get('sort', 'latest', 'word');
		$hashtags = $this->input->get('hashtag', '', 'string');
		$hashtagFilterId = $this->input->get('hashtagFilterId', 0, 'int');

		// this checking is to prevent user from entering the invalid valid which might cause php fatal error on later processing.
		if (!$sort) {
			$sort = 'latest';
		}

		// Display the sidebar when the viewer is viewing another node videos (Deprecated)
		$showSidebar = !$uid;

		// Prepare the options
		$options = array();

		// Construct the video creation link
		$createLinkOptions = array('layout' => 'form');
		$currentCategory = null;

		// If this is filtered by category, we shouldn't set active on the filter.
		if ($activeCategory) {
			$filter = 'category';
			$createLinkOptions['categoryId'] = $activeCategory;

			// Load the category object for later
			$currentCategory = ES::table('VideoCategory');
			$currentCategory->load($activeCategory);
		}

		// Set the filter
		$options['filter'] = $filter;
		$options['category'] = $activeCategory;
		$options['featured'] = false;

		$tagsFilter = ES::Table('TagsFilter');
		$canonicalOptions = array('external' => true);

		if ($hashtagFilterId) {

			// Set to display all videos including featured video
			$options['includeFeatured'] = true;

			$tagsFilter->load($hashtagFilterId);

			$hashtags = $tagsFilter->getHashtag();

			$canonicalOptions['hashtagFilterId'] = $tagsFilter->getAlias();
		}

		if ($sort) {
			$options['sort'] = $sort;
		}

		// If user is viewing my specific filters, we need to update the title accordingly.
		if ($filter && $filter != 'category') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_VIDEOS_FILTER_' . strtoupper($filter);
			$this->page->title($title);
		}

		// Custom filter creation link
		$customFilterLinkOptions = array('filter' => 'filterForm');

		$from = 'listing';
		$cluster = null;

		// Only for clusters
		if ($uid && $type && $type != SOCIAL_TYPE_USER) {

			$from = $type;

			$cluster = ES::cluster($type, $uid);

			$createLinkOptions['uid'] = $cluster->getAlias();
			$createLinkOptions['type'] = $type;

			$customFilterLinkOptions['uid'] = $cluster->getAlias();
			$customFilterLinkOptions['type'] = $type;

			$options['uid'] = $uid;
			$options['type'] = $type;

			$canonicalOptions['uid'] = $cluster->getAlias();
			$canonicalOptions['type'] = $type;

			// Set the page title for cluster video page
			$cluster->renderPageTitle(null, 'videos');

			// Increment the hit.
			$cluster->hit();
		}

		if ($type == SOCIAL_TYPE_USER && $filter != 'pending') {

			$from = $type;

			// If user is viewing their own videos, we should use filter = mine
			$options['filter'] = SOCIAL_TYPE_USER;

			if ($uid == $this->my->id) {
				$options['filter'] = 'mine';
				$options['featured'] = false;
			} else {
				$options['userid'] = $uid;

				$canonicalOptions['uid'] = ES::user($uid)->getAlias();
				$canonicalOptions['type'] = SOCIAL_TYPE_USER;
			}
		}

		// this checking used in normal videos to include the featured videos when 'featured' filter clicked.
		if ($filter == 'featured') {
			$options['featured'] = true;
			$canonicalOptions['filter'] = 'featured';
		}

		if ($filter == 'mine') {
			$options['featured'] = false;
			$canonicalOptions['filter'] = 'mine';
		}

		// For pending filters, we only want to retrieve videos uploaded by the current user
		if ($filter == 'pending') {
			$options['userid'] = $this->my->id;
		}

		$options['limit'] = ES::getLimit('videos_limit', 20);

		if ($hashtags) {
			$options['hashtags'] = $hashtags;
			$options['includeFeatured'] = true;

			if (!$hashtagFilterId) {
				$canonicalOptions['hashtag'] = $hashtags;
			}
		}

		// Get a list of videos from the site
		$videos = $model->getVideos($options);
		$pagination = $model->getPagination();

		// Process the author for this video
		$videos = $this->processAuthor($videos, $cluster);

		// Get featured videos
		$featuredVideos = array();

		if (!($hashtagFilterId || $hashtags)) {
			$options['featured'] = true;
			$options['limit'] = false;
			$featuredVideos = $model->getVideos($options);

			// Process the author for this video
			$featuredVideos = $this->processAuthor($featuredVideos, $cluster);
		}

		$filterOutput = '';

		if ($filter == 'filterForm') {
			$theme = ES::themes();

			// Get the filter id if the user is editing the filter
			$id = $this->input->get('id', 0, 'int');

			// Get cluster id
			$cid = $this->input->get('uid', 0, 'int');

			// Try to load the filter
			$filter = ES::table('TagsFilter');

			if ($id) {
				$filter->load($id);
				$title = 'COM_EASYSOCIAL_PAGE_TITLE_VIDEOS_FILTER_EDIT_FILTER';
				$this->page->title($title);
			}

			$theme->set('filter', $filter);
			$theme->set('clusterType', $type);
			$theme->set('filterType', 'videos');
			$theme->set('cid', $cid);

			$filterOutput = $theme->output('site/videos/form/filter');
		}

		// Get the total number of videos on the site
		$total = $model->getTotalVideos($options);

		// Get the total nuber of videos the current user has
		$totalUserVideos = $model->getTotalUserVideos($this->my->id);

		// Get the total number of featured videos on the site.
		$totalFeatured = $model->getTotalFeaturedVideos($options);

		// Get the total number of pending videos on the site.
		$totalPending = $model->getTotalPendingVideos($this->my->id);

		$createLink = ESR::videos($createLinkOptions);

		$customFilterLink = ESR::videos($customFilterLinkOptions);

		// Determines if the current viewer is allowed to create new video
		$adapter = ES::video($uid, $type);

		// Determines if the user can access this videos section.
		// Instead of showing user 404 page, just show the restricted area.
		if (!$adapter->canAccessVideos()) {
			return $this->restricted($uid, $type);
		}

		$allowCreation = $adapter->allowCreation();

		// If the current type is user, we shouldn't display the creation if they are viewing another person's list of videos
		if ($type == SOCIAL_TYPE_USER && $uid != $this->my->id) {
			$allowCreation = false;
		}

		// Default video title
		if ($uid && $type) {
			$this->page->title($adapter->getListingPageTitle());
		}

		// Featured videos title
		if ($filter == 'featured') {
			$this->page->title($adapter->getFeaturedPageTitle());
		}

		$allVideosPageTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_VIDEOS_FILTER_ALL');
		$featuredVideosPageTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_VIDEOS_FILTER_FEATURED');

		// If this is filter by category, we need to set the category title as the page title
		if ($filter == 'category' && $currentCategory) {

			$this->page->title($currentCategory->title);

			if ($uid && $type) {
				$this->page->title($adapter->getCategoryPageTitle($currentCategory));
			}

			$canonicalOptions['categoryId'] = $currentCategory->getAlias();
		}

		// If there is a uid and type present, we need to update the title of the page
		if ($uid && $type) {
			$allVideosPageTitle = $adapter->getListingPageTitle();
			$featuredVideosPageTitle = $adapter->getFeaturedPageTitle();
		}

		// Get a list of video categories on the site
		$categories = $model->getCategories(array('ordering' => 'ordering', 'direction' => 'asc', 'pagination' => false));

		// We assign page title for each category
		foreach ($categories as &$category) {

			$category->pageTitle = $category->title;

			if ($uid && $type) {
				$category->pageTitle = $adapter->getCategoryPageTitle($category);
			}
		}

		$canCreateFilter = false;
		$hashtagFilter = false;

		// Get available hashtag filter
		if ($this->my->id) {
			$tabLib = ES::tag();
			$hashtagFilter = $tabLib->getFilters($this->my->id, 'videos', $cluster);

			$canCreateFilter = true;
		}

		// Generate correct return urls for operations performed here
		$returnUrl = ESR::videos();

		if ($uid && $type) {
			$returnUrl = $adapter->getAllVideosLink($filter);
		}

		$returnUrl = base64_encode($returnUrl);

		$sortItems = new stdClass();
		$sortingTypes = array('latest', 'alphabetical', 'popular', 'commented', 'likes');
		foreach ($sortingTypes as $sortingType) {

			$sortItems->{$sortingType} = new stdClass();

			// attributes
			$sortAttributes = array('data-sorting', 'data-filter="' . $filter . '"', 'data-type="' . $sortingType . '"');

			//url
			$urlOptions = array();
			$urlOptions['sort'] = $sortingType;

			if ($currentCategory) {
				$urlOptions['categoryId'] = $currentCategory->getAlias();
				$sortAttributes[] = 'data-id="' . $currentCategory->id . '"';
			} else {
				$urlOptions['filter'] = $filter;
			}

			$sortUrl = ESR::videos($urlOptions);

			$sortItems->{$sortingType}->attributes = $sortAttributes;
			$sortItems->{$sortingType}->url = $sortUrl;
		}

		// If no uid, means user is viewing the browsing all video view
		// We define this browse view same like $showsidebar.
		// so it won't break when other customer that still using $showsidebar
		$browseView = !$uid;
		$showPendingVideos = ($totalPending > 0);

		if ($uid && $type == SOCIAL_TYPE_USER) {
			// Get the total video for the currently viewed user
			$total = $model->getTotalVideos(array('uid' => $uid, 'type' => SOCIAL_TYPE_USER));

			if ($uid != $this->my->id) {
				$showPendingVideos = false;
			}
		}

		// Determines if the "My Videos" link should appear
		$showMyVideos = true;

		// We gonna show the 'My videos' if the user is viewing browse all videos page
		if (!$this->my->id || ($uid && $type) || !$browseView) {
			$showMyVideos = false;
		}

		$adapter->setBreadcrumbs($this->getLayout());

		$this->set('showPendingVideos', $showPendingVideos);
		$this->set('browseView', $browseView);
		$this->set('showSidebar', $showSidebar);
		$this->set('returnUrl', $returnUrl);
		$this->set('featuredVideosPageTitle', $featuredVideosPageTitle);
		$this->set('allVideosPageTitle', $allVideosPageTitle);
		$this->set('showMyVideos', $showMyVideos);
		$this->set('rawUid', $rawUid);
		$this->set('uid', $uid);
		$this->set('type', $type);
		$this->set('adapter', $adapter);
		$this->set('allowCreation', $allowCreation);
		$this->set('cluster', $cluster);
		$this->set('featuredVideos', $featuredVideos);
		$this->set('createLink', $createLink);
		$this->set('currentCategory', $activeCategory);
		$this->set('activeCategory', $currentCategory);
		$this->set('filter', $filter);
		$this->set('totalFeatured', $totalFeatured);
		$this->set('totalPending', $totalPending);
		$this->set('totalUserVideos', $totalUserVideos);
		$this->set('total', $total);
		$this->set('videos', $videos);
		$this->set('categories', $categories);
		$this->set('sort', $sort);
		$this->set('hashtagFilter', $hashtagFilter);
		$this->set('canCreateFilter', $canCreateFilter);
		$this->set('hashtags', $hashtags);
		$this->set('tagsFilter', $tagsFilter);
		$this->set('filterOutput', $filterOutput);
		$this->set('customFilterLink', $customFilterLink);
		$this->set('pagination', $pagination);
		$this->set('sortItems', $sortItems);
		$this->set('featuredVideos', $featuredVideos);
		$this->set('from', $from);

		// add canonical links on videos page.
		$this->page->canonical(ESR::videos($canonicalOptions));


		if ($featuredVideos && $filter != 'featured') {
			$theme = ES::themes();
			$theme->set('browseView', $browseView);
			$theme->set('showSidebar', $showSidebar);
			$theme->set('rawUid', $rawUid);
			$theme->set('type', $type);
			$theme->set('isFeatured', true);
			$theme->set('featuredVideos', $featuredVideos);
			$theme->set('videos', $featuredVideos);
			$theme->set('returnUrl', $returnUrl);
			$theme->set('showSidebar', $showSidebar);
			$theme->set('sort', $sort);
			$theme->set('sortItems', $sortItems);
			$theme->set('pagination', '');
			$theme->set('from', $from);
			$theme->set('cluster', $cluster);

			$featuredOutput = $theme->output('site/videos/default/item.list');
			$this->set('featuredOutput', $featuredOutput);
		}

		parent::display('site/videos/default/default');
	}

	/**
	 * Category Layout (Deprecated)
	 * We no longer use single category video layout as everything is handle in display function
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function category($tpl = null)
	{
		// Get category id
		$categoryId = $this->input->get('id', 0, 'int');

		// Set back the id to the request
		$this->input->set('categoryId', $categoryId);

		// Redirect to main view
		return $this->display();
	}

	/**
	 * Process the video author
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function processAuthor($videos, $cluster)
	{
		$processedVideos = array();

		foreach ($videos as $video) {
			$video->creator = $video->getVideoCreator($cluster);

			$processedVideos[] = $video;
		}

		return $processedVideos;
	}

	/**
	 * Displays a restricted page
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id
	 */
	public function restricted($uid = null, $type = SOCIAL_TYPE_USER)
	{
		// Cluster types
		$clusterTypes = array(SOCIAL_TYPE_GROUP, SOCIAL_TYPE_PAGE, SOCIAL_TYPE_EVENT);

		if ($type == SOCIAL_TYPE_USER) {
			$node = FD::user($uid);
		}

		if (in_array($type, $clusterTypes)) {
			$node = ES::cluster($type, $uid);
		}

		$this->set('showProfileHeader', true);
		$this->set('uid', $uid);
		$this->set('type', $type);
		$this->set('node', $node);

		echo parent::display('site/videos/restricted');
	}

	/**
	 * Displays the single video item
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function item()
	{
		// Get the video id
		$id = $this->input->get('id', 0, 'int');

		$table = ES::table('Video');
		$table->load($id);

		// Load up the video
		$video = ES::video($table->uid, $table->type, $table);

		// Ensure that the viewer can really view the video
		if (!$video->isViewable()) {
			return $this->restricted($table->uid, $table->type);
		}

		$from = $this->input->get('from', '', 'default');

		// Add canonical tags
		$this->page->canonical($video->getPermalink());

		// Set the page title
		$this->page->title($video->getTitle());

		// Add oembed tag
		$this->page->oembed($video->getExternalPermalink('oembed'));

		$video->setBreadcrumbs($this->getLayout());

		// Whenever a viewer visits a video, increment the hit counter
		$video->hit();

		// Retrieve the reports library
		$reports = $video->getReports();

		$streamId = $video->getStreamId('create');

		// Retrieve the comments library
		$comments = $video->getComments('create', $streamId);

		// Retrieve the likes library
		$likes = $video->getLikes('create', $streamId);

		// Retrieve the privacy library
		$privacyButton = $video->getPrivacyButton();

		// Retrieve the sharing library
		$sharing = $video->getSharing();

		// Retrieve users tagging
		$usersTags = $video->getEntityTags();
		$usersTagsList = '';

		if ($usersTags) {
			$usersTagsArray = array();

			foreach ($usersTags as $tag) {
				$usersTagsArray[] = $tag->item_id;
			}

			$usersTagsList = json_encode($usersTagsArray);
		}

		// Retrive tags
		$tags = $video->getTags();

		// Retrieve the cluster associated with the video
		$cluster = $video->getCluster();

		// Build user alias
		$creator = $video->getVideoCreator($cluster);

		// Render meta headers
		$video->renderHeaders();

		// Get random videos from the same category
		$otherVideos = array();

		if ($this->config->get('video.layout.item.recent')) {
			$options = array('category_id' => $video->category_id, 'exclusion' => $video->id, 'limit' => $this->config->get('video.layout.item.total'));
			$model = ES::model('Videos');
			$otherVideos = $model->getVideos($options);
		}

		// Update the back link if there is an "uid" or "type" in the url
		$uid = $this->input->get('uid', '');
		$type = $this->input->get('type', '');
		$backLink = ESR::videos();

		// var_dump($uid, $type);

		if (!$uid && !$type) {
			// we will try to get from the current active menu item.
			$menu = $this->app->getMenu();
			if ($menu) {
				$activeMenu = $menu->getActive();

				$xQuery = $activeMenu->query;
				$xView = isset($xQuery['view']) ? $xQuery['view'] : '';
				$xLayout = isset($xQuery['layout']) ? $xQuery['layout'] : '';
				$xId = isset($xQuery['id']) ? (int) $xQuery['id'] : '';

				if ($xView == 'videos' && $xLayout == 'item' && $xId == $video->id) {
					if ($cluster) {
						$uid = $video->uid;
						$type = $video->type;
					}
				}
			}
		}

		if ($from == 'user') {
			$backLink = ESR::videos(array('uid' => $video->getAuthor()->getAlias(), 'type' => 'user'));

		} else if ($uid && $type && $from != 'listing') {
			$backLink = $video->getAllVideosLink();
		}

		// Generate a return url
		$returnUrl = base64_encode($video->getPermalink());

		$this->set('returnUrl', $returnUrl);
		$this->set('usersTagsList', $usersTagsList);
		$this->set('otherVideos', $otherVideos);
		$this->set('backLink', $backLink);
		$this->set('tags', $tags);
		$this->set('usersTags', $usersTags);
		$this->set('sharing', $sharing);
		$this->set('reports', $reports);
		$this->set('comments', $comments);
		$this->set('likes', $likes);
		$this->set('privacyButton', $privacyButton);
		$this->set('video', $video);
		$this->set('creator', $creator);

		$this->set('uid', $uid);
		$this->set('type', $type);

		echo parent::display('site/videos/item/default');
	}

	/**
	 * Displays the form to create a video
	 *
	 * @since	2.0.14
	 * @access	public
	 */
	public function form()
	{
		// Only logged in users should be allowed to create videos
		ES::requireLogin();

		// Determines if a video is being edited
		$id = $this->input->get('id', 0, 'int');
		$uid = $this->input->get('uid', null, 'int');
		$type = $this->input->get('type', null, 'word');

		// Load the video
		$video = ES::video($uid, $type, $id);

		// Increment the hit counter
		if (in_array($type, array(SOCIAL_TYPE_EVENT, SOCIAL_TYPE_PAGE, SOCIAL_TYPE_GROUP))) {
			$clusters = ES::$type($uid);
		}

		// Retrieve any previous data
		$session = JFactory::getSession();
		$data = $session->get('videos.form', null, SOCIAL_SESSION_NAMESPACE);

		if ($data) {
			$data = json_decode($data);

			// Ensure that it matches the id
			if (!$video->id || ($video->id && $video->id == $data->id)) {
				$video->bind($data);
			}
		}

		// Ensure that the current user can create this video
		if (!$id && !$video->canUpload() && !$video->canEmbed()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_ADDING_VIDEOS'));
		}

		// Ensure that the current user can really edit this video
		if ($id && !$video->isEditable()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_EDITING'));
		}

		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_CREATE_VIDEO');

		if ($id && !$video->isNew()) {
			$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_EDIT_VIDEO');
		}

		$model = ES::model('Videos');

		// Pre-selection of a category
		$defaultCategory = $model->getDefaultCategory();
		$defaultCategory = $defaultCategory ? $defaultCategory->id : 0;

		$defaultCategory = $this->input->get('categoryId', $defaultCategory, 'int');

		// Get a list of video categories
		$options = array();

		if (!$this->my->isSiteAdmin()) {
			$options = array('respectAccess' => true, 'profileId' => $this->my->getProfile()->id);
		}

		$options['ordering'] = 'ordering';

		$categories = $model->getCategories($options);

		$privacy = ES::privacy();

		// Retrieve video tags
		$userTags = $video->getEntityTags();
		$userTagItemList = array();

		if ($userTags) {
			foreach($userTags as $userTag) {
				$userTagItemList[] = $userTag->item_id;
			}
		}

		$hashtags = $video->getTags(true);

		$isCluster = ($uid && $type && $type != SOCIAL_TYPE_USER) ? true : false; 
		$type = $isCluster ? $type : SOCIAL_TYPE_USER;

		// Construct the cancel link
		$options = array();

		if ($uid && $type) {
			$options['uid'] = $uid;
			$options['type'] = $type;
		}

		$returnLink = ESR::videos($options);

		if ($video->id) {
			$returnLink = $video->getPermalink();
		}

		// Get the maximum file size allowed
		$uploadLimit = $video->getUploadLimit(false);

		$video->setBreadcrumbs($this->getLayout());

		$this->set('returnLink', $returnLink);
		$this->set('uploadLimit', $uploadLimit);
		$this->set('defaultCategory', $defaultCategory);
		$this->set('userTags', $userTags);
		$this->set('userTagItemList', $userTagItemList);
		$this->set('hashtags', $hashtags);
		$this->set('video', $video);
		$this->set('privacy', $privacy);
		$this->set('categories', $categories);
		$this->set('isCluster', $isCluster);
		$this->set('type', $type);

		return parent::display('site/videos/form/default');
	}

	/**
	 * Displays the process to transcode the video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function process()
	{
		$id = $this->input->get('id', 0, 'int');
		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'word');

		$video = ES::video($uid, $type, $id);

		// Ensure that the current user really owns this video
		if (!$video->canProcess()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_PROCESS'));
		}

		$cluster = null;

		if ($uid && $type) {
			$cluster = ES::cluster($type, $uid);
		}

		$this->set('cluster', $cluster);
		$this->set('uid', $uid);
		$this->set('type', $type);
		$this->set('video', $video);

		echo parent::display('site/videos/process/default');
	}

	/**
	 * Post process after a video is deleted from the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete($video)
	{
		$this->info->set($this->getMessage());

		$redirect = $video->getAllVideosLink('', false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after a filter is deleted
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFilter($cid, $clusterType)
	{
		$video = ES::video($cid, $clusterType);

		$this->info->set($this->getMessage());

		$redirect = $video->getAllVideosLink('', false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after a video is unfeatured on the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unfeature($video, $callback = null)
	{
		$this->info->set($this->getMessage());

		$redirect = $video->getAllVideosLink('featured', false);
		$redirect = $this->getReturnUrl($redirect);

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after a video is featured on the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function feature($video, $callback = null)
	{
		$this->info->set($this->getMessage());

		$redirect = $video->getAllVideosLink('featured', false);
		$redirect = $this->getReturnUrl($redirect);

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after a video is stored
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save(SocialVideo $video, $isNew, $file)
	{
		// If there's an error, redirect them back to the form
		if ($this->hasErrors()) {
			$this->info->set($this->getMessage());

			$options = array('layout' => 'form');

			if (!$video->isNew()) {
				$options['id'] = $video->id;
			}

			if ($video->isCreatedInCluster()) {
				$options['uid'] = $video->uid;
				$options['type'] = $video->type;
			}

			$url = FRoute::videos($options, false);

			return $this->app->redirect($url);
		}

		$message = 'COM_EASYSOCIAL_VIDEOS_ADDED_SUCCESS';

		if (!$isNew) {
			$message = 'COM_EASYSOCIAL_VIDEOS_UPDATED_SUCCESS';
		}

		// If this is a video link, we should just redirect to the video page.
		if ($video->isLink()) {

			$url = $video->getPermalink(false);

			$this->setMessage($message, SOCIAL_MSG_SUCCESS);
			$this->info->set($this->getMessage());

			return $this->app->redirect($url);
		}


		// Should we redirect the user to the progress page or redirect to the pending video page
		$options = array('id' => $video->getAlias());

		if ($isNew && $file || !$isNew && $file) {
			// If video will be processed by cronjob, do not redirect to the process page
			if (!$this->config->get('video.autoencode')) {
				$options = array('filter' => 'pending');

				if ($isNew) {
					$message = 'COM_EASYSOCIAL_VIDEOS_UPLOAD_SUCCESS_AWAIT_PROCESSING';
				}
			} else {
				$options['layout'] = 'process';
				$message = 'COM_EASYSOCIAL_VIDEOS_UPLOAD_SUCCESS_PROCESSING_VIDEO_NOW';
			}
		}

		if (!$isNew && !$file && $video->isPublished()) {
			$options['layout'] = 'item';
		}

		$this->setMessage($message, SOCIAL_MSG_SUCCESS);
		$this->info->set($this->getMessage());

		if ($video->isCreatedInCluster()) {
			$options['uid'] = $video->uid;
			$options['type'] = $video->type;
		}

		$url = ESR::videos($options, false);
		return $this->app->redirect($url);
	}

	/**
	 * Checks if this feature should be enabled or not.
	 *
	 * @since	1.4
	 * @access	private
	 */
	protected function isFeatureEnabled()
	{
		// Do not allow user to access groups if it's not enabled
		if (!$this->config->get('video.enabled')) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_NOT_FOUND'));
		}
	}

	/**
	 * Post processing after tag filters is saved
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveFilter($uid, $clusterType)
	{
		$video = ES::video($uid, $clusterType);

		$this->info->set($this->getMessage());

		$redirect = $video->getAllVideosLink();
		$redirect = $this->getReturnUrl($redirect);

		return $this->app->redirect($redirect);
	}
}
