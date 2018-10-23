<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class DiscussViewProfile extends SocialAppsView
{
	/**
	 * Determines if EasyDiscuss is installed on the site
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function exists()
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

		if (!JFile::exists($path)) {
			return false;
		}

		require_once($path);

		return true;
	}

	/**
	 * Displays the application output in the profile.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function display($userId = null, $docType = null)
	{
		// Check if EasyDiscuss really exists on the site
		if (!$this::exists()) {
			return;
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_easydiscuss', JPATH_ROOT);

		$filter = $this->input->get('filter', 'userposts', 'default');

		$user = ES::user($userId);

		// Get recent new post created
		$model	= ED::model('Posts');

		if ($filter == 'unresolved' || $filter == 'resolved') {
			$resolve = $filter == 'unresolved' ? 0 : 1;
			$posts = $model->getUnresolvedFromUser($userId, $resolve);
		} else if ($filter == 'userreplies') {
			$posts = $model->getRepliesFromUser($userId, 'lastreplied');
		} else {
			$options = array('filter' => $filter, 'userId' => $userId, 'includeAnonymous' => false);

			// If the post is anonymous we shouldn't show to public.
			if (ED::user()->id == $userId) {
				$options['includeAnonymous'] = true;
				$options['private'] = true;
			}

			$posts = $model->getDiscussions($options);
		}

		// Check whether user are allowed to create discussion
		$canCreateDiscussion = $this->canCreateDiscussion($userId);

		// Format discussions
		$posts = ED::formatPost($posts, false, true);

		// Build pagination layout
		$pagination = $model->getPagination();
		$pagination->pagination->setAdditionalUrlParam('option', 'com_easysocial');
		$pagination->pagination->setAdditionalUrlParam('view', 'profile');
		$pagination->pagination->setAdditionalUrlParam('id', $userId);
		$pagination->pagination->setAdditionalUrlParam('appId', $this->app->getAlias());
		$pagination->pagination->setAdditionalUrlParam('filter', $filter);

		$namespace = 'profile/default';

		$this->set('user', $user);
		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		$this->set('filter', $filter);
		$this->set('canCreateDiscussion', $canCreateDiscussion);

		echo parent::display($namespace);
	}

	/**
	 * Determine whether user are able to create discussion
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function canCreateDiscussion($userId)
	{
		$acl = ED::ACL($userId);

		if (!$acl->allowed('add_question')) {
			return false;
		}

		if (ES::user()->id != $userId) {
			return false;
		}

		return true;
	}
}
