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

class EasySocialViewFollowers extends EasySocialSiteView
{
	/**
	 * Determines if this feature is enabled
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function isEnabled()
	{
		if ($this->config->get('followers.enabled')) {
			return true;
		}

		return false;
	}

	/**
	 * Default method to display a list of friends a user has.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Ensure that the feature is enabled
		if (!$this->isEnabled()) {
			return $this->redirect(ESR::dashboard(array(), false));
		}

		// Check if there's an id.
		$id = $this->input->get('userid', null, 'int');

		// Get the user.
		$user = ES::user($id);

		// Lets check if this user is a ESAD user or not
		if (!$this->my->canView($user, 'followers.view') || !$user->hasCommunityAccess()) {

			$facebook = ES::oauth('facebook');
			$return = base64_encode(JRequest::getUri());

			$this->set('return', $return);
			$this->set('facebook', $facebook);
			$this->set('user', $user);

			parent::display('site/profile/restricted');
			return;
		}

		// If user is not found, we need to redirect back to the dashboard page
		if (!$user->id) {
			return $this->redirect(ESR::dashboard(array(), false));
		}

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Get current active filter.
		$active = $this->input->get('filter', 'followers', 'word');

		// Get the list of followers for this current user.
		$model = ES::model('Followers');
		$title = 'COM_EASYSOCIAL_PAGE_TITLE_FOLLOWERS';

		// Default to limit by 20 items per page
		$limit = ES::getLimit('followersLimit');
		$options['limit'] = $limit;

		if ($active == 'followers') {
			$users = $model->getFollowers($user->id, $options);
		}

		if ($active == 'following') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_FOLLOWING';
			$users = $model->getFollowing($user->id, $options);
		}

		if ($active == 'suggest') {
			$users = $model->getSuggestions($user->id, $options);
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_PEOPLE_TO_FOLLOW';
		}

		// Get the pagination
		$pagination = $model->getPagination();

		$filterFollowers = ESR::followers(array(), false);
		$filterFollowing = ESR::followers(array('filter' => 'following'), false);
		$filterSuggest = ESR::followers(array('filter' => 'suggest'), false);

		if (!$user->isViewer()) {
			$title = $user->getName() . ' - ' . JText::_($title);

			$filterFollowers = ESR::followers(array('userid' => $user->getAlias()), false);
			$filterFollowing = ESR::followers(array('userid' => $user->getAlias(), 'filter' => 'following'), false);
		}

		// Set page title
		$this->page->title($title);
		$this->page->breadcrumb($title);

		// canonical links
		$options = array('external' => true);

		if (!$user->isViewer()) {
			$options['userid'] = $user->getAlias();
		}

		if ($active && in_array($active, array('following', 'suggest'))) {
			$options['filter'] = $active;
		}

		$this->page->canonical(ESR::followers($options));

		// Get total followers and following
		$totalFollowers = $model->getTotalFollowers($user->id);
		$totalFollowing = $model->getTotalFollowing($user->id);
		$totalSuggest = $model->getTotalSuggestions($user->id);

		$this->set('pagination', $pagination);
		$this->set('user', $user);
		$this->set('active', $active);
		$this->set('filterFollowers', $filterFollowers);
		$this->set('filterFollowing', $filterFollowing);
		$this->set('filterSuggest', $filterSuggest);
		$this->set('totalFollowers', $totalFollowers);
		$this->set('totalFollowing', $totalFollowing);
		$this->set('totalSuggest', $totalSuggest);
		$this->set('currentUser', $user);
		$this->set('users', $users);

		// Load theme files.
		return parent::display('site/followers/default/default');
	}
}
