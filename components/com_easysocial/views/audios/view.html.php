<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Import parent view
ES::import('site:/views/views');

class EasySocialViewAudios extends EasySocialSiteView
{
	/**
	 * Renders the all audios page
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Default page title
		$title = 'COM_EASYSOCIAL_PAGE_TITLE_AUDIO_FILTER_ALL';
		$model = ES::model('Audios');

		// Get all the variables
		$rawUid = $this->input->get('uid', '', 'default');
		$filter = $this->input->get('filter', 'all', 'word');
		$activeGenre = $this->input->get('genreId', '', 'int');
		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'word');
		$sort = $this->input->get('sort', 'latest', 'word');
		$hashtags = $this->input->get('hashtag', '', 'word');
		$hashtagFilterId = $this->input->get('hashtagFilterId', 0, 'int');

		// Display the sidebar when the viewer is viewing another node audios
		$showSidebar = !$uid;

		// Prepare the options
		$options = array();

		// Detect if list id is provided.
		$listId = $this->input->get('listId', 0, 'int');

		// Get the active list
		$activeList = ES::table('List');
		$activeList->load($listId);

		// Check if list id is provided.
		$filter = $listId ? 'list' : $filter;

		// If exists, means user is viewing the playlist
		if ($activeList->id) {
			$options['list_id'] = $activeList->id;
			$title = $activeList->get('title');
		}

		$currentGenre = null;

		// Construct the audio creation link
		$createLinkOptions = array('layout' => 'form');

		// If this is filtered by genre, we shouldn't set active on the filter.
		if ($activeGenre) {
			$filter = 'genre';
			$createLinkOptions['genreId'] = $activeGenre;

			// Load the genre obj to be used later
			$currentGenre = ES::table('AudioGenre');
			$currentGenre->load($activeGenre);
		}

		// Set the filter
		$options['filter'] = $filter;
		$options['genre'] = $activeGenre;
		$options['featured'] = false;

		$tagsFilter = ES::Table('TagsFilter');

		if ($hashtagFilterId) {

			// Set to display all audios including featured audio
			$options['includeFeatured'] = true;

			$tagsFilter->load($hashtagFilterId);

			$hashtags = $tagsFilter->getHashtag();
		}

		if ($sort) {
			$options['sort'] = $sort;
		}

		// If user is viewing my specific filters, we need to update the title accordingly.
		if ($filter && $filter != 'genre' && $filter != 'list') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_AUDIO_FILTER_' . strtoupper($filter);
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
		}

		if ($type == SOCIAL_TYPE_USER && $filter != 'pending') {

			$from = $type;

			// If user is viewing their own audios, we should use filter = mine
			$options['filter'] = SOCIAL_TYPE_USER;

			if ($uid == $this->my->id) {
				$options['filter'] = 'mine';
				$options['featured'] = false;
			} else {
				$options['userid'] = $uid;
			}
		}

		// this checking used in normal audios to include the featured audios when 'featured' filter clicked.
		if ($filter == 'featured') {
			$options['featured'] = true;
		}

		if ($filter == 'mine') {
			$options['featured'] = false;
		}

		// For pending filters, we only want to retrieve audios uploaded by the current user
		if ($filter == 'pending') {
			$options['userid'] = $this->my->id;
		}

		$options['limit'] = ES::getLimit('audios_limit', 20);

		if ($hashtags) {
			$options['hashtags'] = $hashtags;
			$options['includeFeatured'] = true;
		}

		$playlistOutput = '';
		if ($filter == 'list') {
			// Get the audios from the playlist
			$items = $activeList->getItems(false);

			$audios = array();
			foreach ($items as $item) {
				$audio = ES::table('Audio');
				$audio->load($item->target_id);

				// Assign listmap id into the audio
				$audioObj = ES::audio($audio);
				$audioObj->listMapId = $item->id;
				$audios[] = $audioObj;
			}

			$theme = ES::themes();
			$theme->set('activeList', $activeList);
			$theme->set('audios', $audios);

			$playlistOutput = $theme->output('site/audios/player/playlist');
		}

		// Get a all audios from the site
		$audios = $model->getAudios($options);
		$pagination = $model->getPagination();

		// Process the author for this audio
		$audios = $this->processAuthor($audios, $cluster);

		// Get featured audios
		$featuredAudios = array();

		if (!($hashtagFilterId || $hashtags)) {
			$options['featured'] = true;
			$options['limit'] = false;
			$featuredAudios = $model->getAudios($options);

			// Process the author for this audio
			$featuredAudios = $this->processAuthor($featuredAudios, $cluster);
		}

		$filterOutput = '';

		if ($filter == 'filterForm') {
			$theme = ES::themes();

			// Get the filter id if the user is editing the filter
			$clusterType = $this->input->get('type', '', 'word');
			$id = $this->input->get('id', 0, 'int');

			// Get cluster id
			$cid = $this->input->get('uid', 0, 'int');

			// Try to load the filter
			$filter = ES::table('TagsFilter');

			if ($id) {
				$filter->load($id);
				$title = 'COM_EASYSOCIAL_PAGE_TITLE_AUDIO_FILTER_EDIT_FILTER';
			}

			$theme->set('filter', $filter);
			$theme->set('clusterType', $clusterType);
			$theme->set('filterType', 'audios');
			$theme->set('cid', $cid);

			$filterOutput = $theme->output('site/audios/form/filter');
		}

		// Get the total number of audios on the site
		$total = $model->getTotalAudios($options);

		// Get the total number of audios the current user has
		$totalUserAudios = $model->getTotalUserAudios($this->my->id);

		// Get the total number of featured audios on the site.
		$totalFeatured = $model->getTotalFeaturedAudios($options);

		// Get the total number of pending audios on the site.
		$totalPending = $model->getTotalPendingAudios($this->my->id);

		$createLink = ESR::audios($createLinkOptions);

		$customFilterLink = ESR::audios($customFilterLinkOptions);

		// Determines if the current viewer is allowed to create new audio
		$adapter = ES::audio($uid, $type);

		// Determines if the user can access this audios section.
		// Instead of showing user 404 page, just show the restricted area.
		if (!$adapter->canAccessAudios()) {
			return $this->restricted($uid, $type);
		}

		$allowCreation = $adapter->allowCreation();

		// If the current type is user, we shouldn't display the creation if they are viewing another person's list of audios
		if ($type == SOCIAL_TYPE_USER && $uid != $this->my->id) {
			$allowCreation = false;
		}

		// Default audio title
		if ($uid && $type) {
			$title = $adapter->getListingPageTitle();
		}

		// Featured audios title
		if ($filter == 'featured') {
			$title = $adapter->getFeaturedPageTitle();
		}

		// If this is filter by genre, we need to set the genre title as the page title
		if ($filter == 'genre' && $currentGenre) {

			$title = $currentGenre->title;

			if ($uid && $type) {
				$title = $adapter->getGenrePageTitle($currentGenre);
			}
		}

		$allAudiosPageTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_AUDIO_FILTER_ALL');
		$featuredAudiosPageTitle = JText::_('COM_EASYSOCIAL_PAGE_TITLE_AUDIO_FILTER_FEATURED');

		// If there is a uid and type present, we need to update the title of the page
		if ($uid && $type) {
			$allAudiosPageTitle = $adapter->getListingPageTitle();
			$featuredAudiosPageTitle = $adapter->getFeaturedPageTitle();
		}

		// Get a list of audio genres on the site
		$genres = $model->getGenres(array('pagination' => false, 'ordering' => 'ordering', 'direction' => 'asc'));

		// Assign the page title to each genre
		foreach ($genres as &$genre) {

			$genre->pageTitle = $genre->title;

			if ($uid && $type) {
				$genre->pageTitle = $adapter->getGenrePageTitle($genre);
			}
		}

		// Generate correct return urls for operations performed here
		$returnUrl = ESR::audios();

		if ($uid && $type) {
			$returnUrl = $adapter->getAllAudiosLink($filter);
		}

		// Increment hit.
		$adapter->hit();

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

			if ($currentGenre) {
				$sortAttributes[] = 'data-id="' . $currentGenre->id . '"';
				$urlOptions['genreId'] = $currentGenre->getAlias();
			} else {
				$urlOptions['filter'] = $filter;
			}

			$sortUrl = ESR::audios($urlOptions);

			$sortItems->{$sortingType}->attributes = $sortAttributes;
			$sortItems->{$sortingType}->url = $sortUrl;
		}

		// Get the list of playlist the user has.
		$listModel = ES::model('Lists');

		// Get the list items.
		$lists = $listModel->getLists(array('user_id' => $this->my->id, 'type' => SOCIAL_TYPE_AUDIOS));

		// differentiate between browse all audio or viewing profile audio
		// If no uid, means user is viewing the browsing all audio view
		$browseView = !$uid;
		$canCreatePlaylist = ES::lists()->canCreatePlaylist();
		$showPendingAudios = ($totalPending > 0);

		// if the user A is viewing user B's listing, we get the playlist for user B
		if ($uid && $type == SOCIAL_TYPE_USER) {

			// Get the playlist for the currently viewed user
			$lists = $listModel->getLists(array('user_id' => $uid, 'type' => SOCIAL_TYPE_AUDIOS));

			// Only allow to create playlist if user is viewing his own
			$canCreatePlaylist = ES::lists()->canCreatePlaylist() && $uid == $this->my->id;

			// Get the total audio for the currently viewed user
			$total = $model->getTotalAudios(array('uid' => $uid, 'type' => SOCIAL_TYPE_USER));

			// if user is viewing others' listing, don't show the pending filter
			if ($uid != $this->my->id) {
				$showPendingAudios = false;
			}
		}

		// Determines if the "My Audios" link should appear
		$showMyAudios = true;

		// We gonna show the 'My audios' if the user is viewing browse all audio page
		if (!$this->my->id || ($uid && $type) || !$browseView) {
			$showMyAudios = false;
		}

		$layout = $this->getLayout();
		$adapter->setBreadcrumbs($layout);

		// Generate the page title
		$this->page->title($title);
		$this->set('showPendingAudios', $showPendingAudios);
		$this->set('canCreatePlaylist', $canCreatePlaylist);
		$this->set('browseView', $browseView);
		$this->set('showSidebar', $showSidebar);
		$this->set('returnUrl', $returnUrl);
		$this->set('featuredAudiosPageTitle', $featuredAudiosPageTitle);
		$this->set('allAudiosPageTitle', $allAudiosPageTitle);
		$this->set('showMyAudios', $showMyAudios);
		$this->set('rawUid', $rawUid);
		$this->set('uid', $uid);
		$this->set('type', $type);
		$this->set('adapter', $adapter);
		$this->set('allowCreation', $allowCreation);
		$this->set('cluster', $cluster);
		$this->set('featuredAudios', $featuredAudios);
		$this->set('createLink', $createLink);
		$this->set('currentGenre', $activeGenre);
		$this->set('activeGenre', $currentGenre);
		$this->set('filter', $filter);
		$this->set('totalFeatured', $totalFeatured);
		$this->set('totalPending', $totalPending);
		$this->set('totalUserAudios', $totalUserAudios);
		$this->set('total', $total);
		$this->set('audios', $audios);
		$this->set('genres', $genres);
		$this->set('sort', $sort);
		$this->set('hashtags', $hashtags);
		$this->set('tagsFilter', $tagsFilter);
		$this->set('filterOutput', $filterOutput);
		$this->set('customFilterLink', $customFilterLink);
		$this->set('pagination', $pagination);
		$this->set('sortItems', $sortItems);
		$this->set('featuredAudios', $featuredAudios);
		$this->set('from', $from);
		$this->set('lists', $lists);
		$this->set('activeList', $activeList);
		$this->set('playlistOutput', $playlistOutput);

		if ($featuredAudios && $filter != 'featured' && $filter != 'list') {
			$theme = ES::themes();
			$theme->set('browseView', $browseView);
			$theme->set('showSidebar', $showSidebar);
			$theme->set('rawUid', $rawUid);
			$theme->set('type', $type);
			$theme->set('isFeatured', true);
			$theme->set('featuredAudios', $featuredAudios);
			$theme->set('audios', $featuredAudios);
			$theme->set('returnUrl', $returnUrl);
			$theme->set('sort', $sort);
			$theme->set('sortItems', $sortItems);
			$theme->set('pagination', '');
			$theme->set('from', $from);
			$theme->set('cluster', $cluster);
			$theme->set('lists', $lists);

			$featuredOutput = $theme->output('site/audios/default/item.list');
			$this->set('featuredOutput', $featuredOutput);
		}

		echo parent::display('site/audios/default/default');
	}

	/**
	 * New Playlist form
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function playlistform()
	{
		// Ensure that user is logged in.
		ES::requireLogin();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		if (! $this->config->get('audio.enabled')) {
			return $this->exception('COM_ES_AUDIO_ERROR_AUDIO_DISABLED');
		}

		$this->info->set($this->getMessage());

		// Get the list id.
		$id = $this->input->get('listId', 0, 'int');

		$list = ES::table('List');
		$list->load($id);

		if (!ES::lists()->canCreatePlaylist()) {
			return $this->exception('COM_ES_AUDIO_PLAYLISTS_ACCESS_NOT_ALLOWED');
		}

		// Check if this list is being edited.
		if ($id && !$list->id) {
			$this->setMessage('COM_ES_AUDIO_INVALID_PLAYLIST_ID_PROVIDED', SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::audios(array(), false));
		}

		// Set the page title
		$title = 'COM_ES_PAGE_TITLE_AUDIO_CREATE_PLAYLIST_FORM';

		if ($list->id) {
			$title = 'COM_ES_PAGE_TITLE_AUDIO_EDIT_PLAYLIST_FORM';
		}

		$this->set('list', $list);
		$this->set('id', $id);

		// Load theme files.
		echo parent::display('site/audios/playlistform/default');
	}

	/**
	 * Process the audio author
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function processAuthor($audios, $cluster)
	{
		$processedAudios = array();

		foreach ($audios as $audio) {
			$audio->creator = $audio->getAudioCreator($cluster);

			$processedAudios[] = $audio;
		}

		return $processedAudios;
	}

	/**
	 * Displays a restricted page
	 *
	 * @since	1.0
	 * @access	public
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

		echo parent::display('site/audios/restricted');
	}

	/**
	 * Displays the single audio item
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function item()
	{
		// Get the audio id
		$id = $this->input->get('id', 0, 'int');

		$table = ES::table('Audio');
		$table->load($id);

		// Load up the audio
		$audio = ES::audio($table->uid, $table->type, $table);

		// Ensure that the viewer can really view the audio
		if (!$audio->isViewable()) {
			return $this->restricted($table->uid, $table->type);
		}

		$from = $this->input->get('from', '', 'default');

		// Add canonical tags
		$this->page->canonical($audio->getPermalink());

		// Set the page title
		$this->page->title($audio->getTitle());

		// Whenever a viewer visits an audio, increment the hit counter
		// Only for audio link
		if (!$audio->isUpload()) {
			$audio->hit();
		}

		// Retrieve the reports library
		$reports = $audio->getReports();

		$streamId = $audio->getStreamId('create');

		// Retrieve the comments library
		$comments = $audio->getComments('create', $streamId);

		// Retrieve the likes library
		$likes = $audio->getLikes('create', $streamId);

		// Retrieve the privacy library
		$privacyButton = $audio->getPrivacyButton();

		// Retrieve the sharing library
		$sharing = $audio->getSharing();

		// Retrieve users tagging
		$usersTags = $audio->getEntityTags();
		$usersTagsList = '';

		if ($usersTags) {
			$usersTagsArray = array();

			foreach ($usersTags as $tag) {
				$usersTagsArray[] = $tag->item_id;
			}

			$usersTagsList = json_encode($usersTagsArray);
		}

		// Retrive tags
		$tags = $audio->getTags();

		// Retrieve the cluster associated with the audio
		$cluster = $audio->getCluster();

		// Build user alias
		$creator = $audio->getAudioCreator($cluster);

		// Render meta headers
		$audio->renderHeaders();

		// Get random audios from the same genre
		$otherAudios = array();

		if ($this->config->get('audio.layout.item.recent')) {
			$options = array('genre_id' => $audio->genre_id, 'exclusion' => $audio->id, 'limit' => $this->config->get('audio.layout.item.total'));
			$model = ES::model('Audios');
			$otherAudios = $model->getAudios($options);
		}

		// Update the back link if there is an "uid" or "type" in the url
		$uid = $this->input->get('uid', '');
		$type = $this->input->get('type', '');
		$backLink = ESR::audios();

		if (!$uid && !$type) {
			// we will try to get from the current active menu item.
			$menu = $this->app->getMenu();
			if ($menu) {
				$activeMenu = $menu->getActive();

				$xQuery = $activeMenu->query;
				$xView = isset($xQuery['view']) ? $xQuery['view'] : '';
				$xLayout = isset($xQuery['layout']) ? $xQuery['layout'] : '';
				$xId = isset($xQuery['id']) ? (int) $xQuery['id'] : '';

				if ($xView == 'audios' && $xLayout == 'item' && $xId == $audio->id) {
					if ($cluster) {
						$uid = $audio->uid;
						$type = $audio->type;
					}
				}
			}
		}

		if ($from == 'user') {
			$backLink = ESR::audios(array('uid' => $audio->getAuthor()->getAlias(), 'type' => 'user'));
		} else if ($uid && $type && $from != 'listing') {
			$backLink = $audio->getAllAudiosLink();
		}

		// Generate a return url
		$returnUrl = base64_encode($audio->getPermalink());

		// Get the list of playlist the user has.
		$listModel = ES::model('Lists');

		// Get the list items.
		$lists = $listModel->getLists(array('user_id' => $this->my->id, 'type' => SOCIAL_TYPE_AUDIOS));

		$audio->setBreadcrumbs($this->getLayout());

		$this->set('lists', $lists);
		$this->set('returnUrl', $returnUrl);
		$this->set('usersTagsList', $usersTagsList);
		$this->set('otherAudios', $otherAudios);
		$this->set('backLink', $backLink);
		$this->set('tags', $tags);
		$this->set('usersTags', $usersTags);
		$this->set('sharing', $sharing);
		$this->set('reports', $reports);
		$this->set('comments', $comments);
		$this->set('likes', $likes);
		$this->set('privacyButton', $privacyButton);
		$this->set('audio', $audio);
		$this->set('creator', $creator);

		$this->set('uid', $uid);
		$this->set('type', $type);

		echo parent::display('site/audios/item/default');
	}

	/**
	 * Displays the edit form for an audio
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function form()
	{
		// Only logged in users should be allowed to create audios
		ES::requireLogin();

		// Determines if an audio is being edited
		$id = $this->input->get('id', 0, 'int');
		$uid = $this->input->get('uid', null, 'int');
		$type = $this->input->get('type', null, 'word');

		// Load the audio
		$audio = ES::audio($uid, $type, $id);

		// Increment the hit counter
		if (in_array($type, array(SOCIAL_TYPE_EVENT, SOCIAL_TYPE_PAGE, SOCIAL_TYPE_GROUP))) {
			$clusters = ES::$type($uid);
		}

		// Retrieve any previous data
		$session = JFactory::getSession();
		$data = $session->get('audios.form', null, SOCIAL_SESSION_NAMESPACE);

		if ($data) {
			$data = json_decode($data);

			// Ensure that it matches the id
			if (!$audio->id || ($audio->id && $audio->id == $data->id)) {
				$audio->bind($data);
			}
		}

		// Ensure that the current user can create this audio
		if (!$id && !$audio->canUpload() && !$audio->canEmbed()) {
			return JError::raiseError(500, JText::_('COM_ES_AUDIO_NOT_ALLOWED_ADDING_AUDIOS'));
		}

		// Ensure that the current user can really edit this audio
		if ($id && !$audio->isEditable()) {
			return JError::raiseError(500, JText::_('COM_ES_AUDIO_NOT_ALLOWED_EDITING'));
		}

		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_CREATE_AUDIO');

		if ($id && !$audio->isNew()) {
			$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_EDIT_AUDIO');
		}

		$model = ES::model('Audios');

		// Pre-selection of a genre
		$defaultGenre = $model->getDefaultGenre();
		$defaultGenre = $defaultGenre ? $defaultGenre->id : 0;

		$defaultGenre = $this->input->get('genreId', $defaultGenre, 'int');

		// Get a list of audio genres
		$options = array();

		if (!$this->my->isSiteAdmin()) {
			$options = array('respectAccess' => true, 'profileId' => $this->my->getProfile()->id);
		}

		$genres = $model->getGenres($options);

		$selectedGenre = $audio->genre_id ? $audio->genre_id : $defaultGenre;

		$privacy = ES::privacy();

		// Retrieve audio tags
		$userTags = $audio->getEntityTags();
		$userTagItemList = array();

		if ($userTags) {
			foreach($userTags as $userTag) {
				$userTagItemList[] = $userTag->item_id;
			}
		}

		$hashtags = $audio->getTags(true);

		$isCluster = ($uid && $type && $type != SOCIAL_TYPE_USER) ? true : false;

		// Construct the cancel link
		$options = array();

		if ($uid && $type) {
			$options['uid'] = $uid;
			$options['type'] = $type;
		}

		$returnLink = ESR::audios($options);

		if ($audio->id) {
			$returnLink = $audio->getPermalink();
		}

		// Get the maximum file size allowed
		$uploadLimit = $audio->getUploadLimit(false);

		$defaultAlbumart = $audio->getDefaultAlbumart();

		$supportedProviders = $audio->getSupportedProviders();
		$supportedProviders = implode(', ', $supportedProviders);

		$audio->setBreadcrumbs($this->getLayout());

		$this->set('returnLink', $returnLink);
		$this->set('uploadLimit', $uploadLimit);
		$this->set('selectedGenre', $selectedGenre);
		$this->set('userTags', $userTags);
		$this->set('userTagItemList', $userTagItemList);
		$this->set('hashtags', $hashtags);
		$this->set('audio', $audio);
		$this->set('privacy', $privacy);
		$this->set('genres', $genres);
		$this->set('isCluster', $isCluster);
		$this->set('defaultAlbumart', $defaultAlbumart);
		$this->set('supportedProviders', $supportedProviders);

		return parent::display('site/audios/form/default');
	}

	/**
	 * Displays the process to transcode the audio
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function process()
	{
		$id = $this->input->get('id', 0, 'int');
		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'word');

		$audio = ES::audio($uid, $type, $id);

		// Ensure that the current user really owns this audio
		if (!$audio->canProcess()) {
			return JError::raiseError(500, JText::_('COM_ES_AUDIO_NOT_ALLOWED_PROCESS'));
		}

		$cluster = null;

		if ($uid && $type) {
			$cluster = ES::cluster($type, $uid);
		}

		$this->set('cluster', $cluster);
		$this->set('uid', $uid);
		$this->set('type', $type);
		$this->set('audio', $audio);

		echo parent::display('site/audios/process/default');
	}

	/**
	 * Post process after an audio is deleted from the site
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function delete($audio)
	{
		$this->info->set($this->getMessage());

		$redirect = $audio->getAllAudiosLink('', false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after a filter is deleted
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function deleteFilter($cid, $clusterType)
	{
		$audio = ES::audio($cid, $clusterType);

		$this->info->set($this->getMessage());

		$redirect = $audio->getAllAudiosLink('', false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after an audio is unfeatured on the site
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function unfeature($audio, $callback = null)
	{
		$this->info->set($this->getMessage());

		$redirect = $audio->getAllAudiosLink('featured', false);
		$redirect = $this->getReturnUrl($redirect);

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after an audio is featured on the site
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function feature($audio, $callback = null)
	{
		$this->info->set($this->getMessage());

		$redirect = $audio->getAllAudiosLink('featured', false);
		$redirect = $this->getReturnUrl($redirect);

		return $this->app->redirect($redirect);
	}

	/**
	 * Post process after an audio is stored
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function save(SocialAudio $audio, $isNew, $file)
	{
		// If there's an error, redirect them back to the form
		if ($this->hasErrors()) {
			$this->info->set($this->getMessage());

			$options = array('layout' => 'form');

			if (!$audio->isNew()) {
				$options['id'] = $audio->id;
			}

			if ($audio->isCreatedInCluster()) {
				$options['uid'] = $audio->uid;
				$options['type'] = $audio->type;
			}

			$url = FRoute::audios($options, false);

			return $this->app->redirect($url);
		}

		$message = 'COM_ES_AUDIO_ADDED_SUCCESS';

		if (!$isNew) {
			$message = 'COM_ES_AUDIO_UPDATED_SUCCESS';
		}

		// If this is an audio link, we should just redirect to the audio page.
		if ($audio->isLink()) {

			$url = $audio->getPermalink(false);

			$this->setMessage($message, SOCIAL_MSG_SUCCESS);
			$this->info->set($this->getMessage());

			return $this->app->redirect($url);
		}


		// Should we redirect the user to the progress page or redirect to the pending audio page
		$options = array('id' => $audio->getAlias());

		if ($isNew && $file || !$isNew && $file) {
			// If audio will be processed by cronjob, do not redirect to the process page
			if (!$this->config->get('audio.autoencode')) {
				$options = array('filter' => 'pending');

				if ($isNew) {
					$message = 'COM_ES_AUDIO_UPLOAD_SUCCESS_AWAIT_PROCESSING';
				}
			} else if ($this->config->get('audio.allowencode')){
				$options['layout'] = 'process';

				if ($isNew) {
					$message = 'COM_ES_AUDIO_UPLOAD_SUCCESS_PROCESSING_AUDIO_NOW';
				}
			} else {
				if ($isNew) {
					$message = 'COM_ES_AUDIO_UPLOAD_SUCCESS';
				}
			}
		}

		if (!$isNew && $audio->isPublished()) {
			$options['layout'] = 'item';
		}

		$this->setMessage($message, SOCIAL_MSG_SUCCESS);
		$this->info->set($this->getMessage());

		if ($audio->isCreatedInCluster()) {
			$options['uid'] = $audio->uid;
			$options['type'] = $audio->type;
		}

		$url = ESR::audios($options, false);
		return $this->app->redirect($url);
	}

	/**
	 * Checks if this feature should be enabled or not.
	 *
	 * @since	2.1
	 * @access	private
	 */
	protected function isFeatureEnabled()
	{
		// Do not allow user to access groups if it's not enabled
		if (!$this->config->get('audio.enabled')) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_NOT_FOUND'));
		}
	}

	/**
	 * Post processing after tag filters is saved
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function saveFilter($uid, $clusterType)
	{
		$audio = ES::audio($uid, $clusterType);

		$this->info->set($this->getMessage());

		$redirect = $audio->getAllAudiosLink();
		$redirect = $this->getReturnUrl($redirect);

		return $this->app->redirect($redirect);
	}

	/**
	 * Perform redirection after the playlist is created.
	 *
	 * @since	2.1
	 * @access	public
	 **/
	public function storePlaylist($list)
	{
		if (!$this->config->get('audio.enabled')) {
			return $this->exception('COM_ES_AUDIO_ERROR_AUDIO_DISABLED');
		}

		$this->info->set($this->getMessage());

		$this->redirect(ESR::audios(array(), false));
	}

	/**
	 * Post processing of delete playlist
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function deletePlaylist()
	{

		$this->info->set($this->getMessage());

		$redirect = ESR::audios(array(), false);

		return $this->redirect($redirect);
	}

	/**
	 * Allows use to download an audio from the site.
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function download()
	{
		// Get the id of the audio
		$id = $this->input->get('id', null, 'int');

		$table = ES::table('Audio');
		$table->load($id);

		// Id provided must be valid
		if (!$id || !$table->id) {
			$this->setMessage(JText::_('COM_ES_AUDIO_INVALID_AUDIO_ID_PROVIDED'), ES_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::audios(array(), false));
		}

		// Load up the audio
		$audio = ES::audio($table->uid, $table->type, $table);

		// Let's try to download the file now
		$audio->download();
	}

}
