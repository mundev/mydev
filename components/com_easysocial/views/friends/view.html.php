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

class EasySocialViewFriends extends EasySocialSiteView
{
	/**
	 * Default method to display a list of friends a user has.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for user profile completeness
		ES::checkCompleteProfile();

		if (! $this->config->get('friends.enabled')) {
			return $this->exception('COM_EASYSOCIAL_FRIENDS_ERROR_FRIENDS_DISABLED');
		}

		// Check if there's an id.
		// Okay, we need to use getInt to prevent someone inject invalid data from the url. just do another checking later.
		$id = $this->input->get('userid', null, 'int');

		// This is to ensure that the id != 0. if 0, we set to null to get the current user.
		if (empty($id)) {
			$id = null;
		}

		// Get the user.
		$user = ES::user($id);

		// If a ugest is trying to view their own friend list, it should never happen
		if (!$user->id) {
			return ES::requireLogin();
		}
		
		// Let's test if the current viewer is allowed to view this profile.
		if (!$user->isViewer() && !$this->my->canView($user, 'friends.view')) {
			$this->set('showProfileHeader', true);
			$this->set('user', $user);

			parent::display('site/friends/restricted');
			return;
		}

		// Lets check if this user is a ESAD user or not
		if (!$user->hasCommunityAccess()) {

			$facebook = ES::oauth('facebook');
			$return = base64_encode(JRequest::getUri());

			$this->set('return', $return);
			$this->set('facebook', $facebook);
			$this->set('user', $user);

			parent::display('site/profile/restricted');
			return;
		}

		// Get the list of friends this user has.
		$model = ES::model('Friends');
		$limit = ES::getLimit('friendslimit');

		$options = array('state' => SOCIAL_FRIENDS_STATE_FRIENDS, 'limit' => $limit);

		// By default the view is "All Friends"
		$filter = $this->input->get('filter', 'all', 'cmd');

		// If current view is pending, we need to only get pending friends.
		if ($filter == 'pending') {
			$options['state'] = SOCIAL_FRIENDS_STATE_PENDING;
		}

		if ($filter == 'request') {
			$options['state'] = SOCIAL_FRIENDS_STATE_PENDING;
			$options['isRequest'] = true;
		}

		// Detect if list id is provided.
		$listId = $this->input->get('listId', 0, 'int');

		// Get the active list
		$activeList = ES::table('List');
		$activeList->load($listId);

		// Check if list id is provided.
		$filter = $listId ? 'list' : $filter;

		if ($activeList->id) {
			$options['list_id']	= $activeList->id;
		}

		// Get the list of lists the user has.
		$listModel = ES::model('Lists');
		$limit = $this->config->get('lists.display.limit', 5);

		// Get the list items.
		$lists = $listModel->getLists(array('user_id' => $user->id));

		// Get counters
		$totalPendingFriends = $model->getTotalPendingFriends($user->id);
		$totalRequestSent = $model->getTotalRequestSent($user->id);
		$totalFriendsList = $user->getTotalFriendsList();
		$totalFriends = $model->getTotalFriends($user->id);
		$totalInvites = $model->getTotalInvites($user->id);
		$totalFriendSuggest = $model->getSuggestedFriends($this->my->id, null, true);
		$totalMutualFriends = 0;

		// We only want to run the query if the user is not the viewer
		if (!$user->isViewer()) {
			$totalMutualFriends = $model->getMutualFriendCount($this->my->id, $user->id);
		}


		$friends = array();
		$title = 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS';

		if ($activeList->id) {
			$title = $activeList->get('title');
		}

		if ($filter == 'mutual') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_MUTUAL_FRIENDS';
			$mutuallimit = ES::getLimit('friendslimit');

			$friends = $model->getMutualFriends($this->my->id, $user->id, $mutuallimit);

			// Set breadcrumbs
			$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS', ESR::friends());
		}

		if ($filter == 'pending') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_PENDING_APPROVAL';

			// Set breadcrumbs
			$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS', ESR::friends());
		}

		if ($filter == 'request') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_REQUESTS';

			$options['state'] = SOCIAL_FRIENDS_STATE_PENDING;
			$options['isRequest'] = true;

			$friends = $model->getFriends($user->id, $options);

			// Set breadcrumbs
			$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS', ESR::friends());
		}

		if ($filter == 'suggest') {

			$friends = $model->getSuggestedFriends($this->my->id, ES::getLimit('friendslimit'));

			// Set document properties
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_SUGGESTIONS';
			$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS', ESR::friends());
		}

		// Ensure that invites are enabled
		if ($filter == 'invites' && !$this->config->get('friends.invites.enabled')) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_FEATURE_NOT_AVAILABLE'));
		}

		if ($filter == 'invites') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_INVITES';
			$friends = $model->getInvitedUsers($user->id);
		}

		if ($filter == 'all' || $filter == 'pending' || $filter == 'list') {
			$friends = $model->getFriends($user->id, $options);
		}

		// Get pagination
		$pagination	= $model->getPagination();

		// Set additional params for the pagination links
		$pagination->setVar('view', 'friends');

		if (!$user->isViewer()) {
			$pagination->setVar('userid', $user->getAlias());
		}


		// Construct the user alias for friends list
		$userAlias = $user->isViewer() ? '' : $user->getAlias();

		// Set the page title
		if (!$user->isViewer()) {
			$title = $user->getName() . ' - ' . JText::_($title);
		}


		// Set page attributes
		$this->page->title($title);
		$this->page->breadcrumb($title);

		// canonical links
		$options = array('external' => true);

		if (!$user->isViewer()) {
			$options['userid'] = $user->getAlias();
		}

		if ($filter && $filter == 'list') {
			$options['listId'] = $listId;

		} elseif ($filter && $filter != 'all') {
			$options['userid'] = $userAlias;
			$options['filter'] = $filter;

			if ($filter == 'list') {
				$options['listId'] = $filter;
			}
		}

		$this->page->canonical(ESR::friends($options));

		// Counters
		$this->set('totalInvites', $totalInvites);
		$this->set('totalPendingFriends', $totalPendingFriends);
		$this->set('totalRequestSent', $totalRequestSent);
		$this->set('totalFriendSuggest'	, $totalFriendSuggest);
		$this->set('totalMutualFriends', $totalMutualFriends);
		$this->set('totalFriends', $totalFriends);

		$this->set('user', $user);
		$this->set('userAlias', $userAlias);

		// Push vars to the theme
		$this->set('pagination', $pagination);

		$this->set('filter', $filter);
		$this->set('activeList', $activeList);
		$this->set('friends', $friends);
		$this->set('lists', $lists);


		// Load theme files.
		return parent::display('site/friends/default/default');
	}

	/**
	 * Displays the list form.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function listForm()
	{
		// Ensure that user is logged in.
		ES::requireLogin();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		if (! $this->config->get('friends.enabled')) {
			return $this->exception('COM_EASYSOCIAL_FRIENDS_ERROR_FRIENDS_DISABLED');
		}

		$this->info->set($this->getMessage());

		// Get the list id.
		$id = $this->input->get('id', 0, 'int');

		$list = ES::table('List');
		$list->load($id);

		if (!ES::lists()->canCreateList()) {
			return $this->exception('COM_EASYSOCIAL_FRIENDS_LISTS_ACCESS_NOT_ALLOWED');
		}

		// Check if this list is being edited.
		if ($id && !$list->id) {
			$this->setMessage('COM_EASYSOCIAL_FRIENDS_INVALID_LIST_ID_PROVIDED', SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::friends(array(), false));
		}

		// Set the page title
		$title = 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_LIST_FORM';

		if ($list->id) {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_EDIT_LIST_FORM';
		}

		// Get list of users from this list.
		$result = $list->getMembers();
		$members = array();

		if ($result) {
			$members = ES::user($result);
		}

		$this->set('members', $members);
		$this->set('list', $list);
		$this->set('id', $id);

		// Load theme files.
		echo parent::display('site/friends/listform/default');
	}

	/**
	 * Displays the invite friends form
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function invite()
	{
		// Requires user to be logged into the site
		ES::requireLogin();

		// Ensure that invites are enabled
		if (!$this->config->get('friends.invites.enabled')) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_FEATURE_NOT_AVAILABLE'));
		}

		if (! $this->config->get('friends.enabled')) {
			return $this->exception('COM_EASYSOCIAL_FRIENDS_ERROR_FRIENDS_DISABLED');
		}

		$defaultEditor = JFactory::getConfig()->get('editor');
		$editor = ES::editor()->getEditor($defaultEditor);

		$this->set('editor', $editor);
		parent::display('site/friends/invite/default');
	}

	/**
	 * Post processing after inviting a friend
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function sendInvites()
	{
		ES::info()->set($this->getMessage());

		if ($this->hasErrors()) {
			return $this->redirect(ESR::friends(array('layout' => 'invite'), false));
		}

		return $this->redirect(ESR::friends(array('filter' => 'invites'), false));
	}

	/**
	 * Perform redirection after the list is created.
	 *
	 * @since	1.0
	 * @access	public
	 **/
	public function storeList($list)
	{
		if (! $this->config->get('friends.enabled')) {
			return $this->exception('COM_EASYSOCIAL_FRIENDS_ERROR_FRIENDS_DISABLED');
		}

		$this->info->set($this->getMessage());

		$this->redirect(ESR::friends(array(), false));
	}

	/**
	 * This view is responsible to approve pending friend requests.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function approve()
	{
		if (! $this->config->get('friends.enabled')) {
			return $this->exception('COM_EASYSOCIAL_FRIENDS_ERROR_FRIENDS_DISABLED');
		}

		// Get the return url.
		$return = JRequest::getVar('return', null);

		$info	= ES::info();

		// Set the message data
		$info->set($this->getMessage());

		return $this->redirect(ESR::friends(array(), false));
	}

	/**
	 * Post processing of delete list item.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function deleteList()
	{
		if (! $this->config->get('friends.enabled')) {
			return $this->exception('COM_EASYSOCIAL_FRIENDS_ERROR_FRIENDS_DISABLED');
		}


		$this->info->set($this->getMessage());

		$redirect = ESR::friends(array(), false);

		return $this->redirect($redirect);
	}
}
