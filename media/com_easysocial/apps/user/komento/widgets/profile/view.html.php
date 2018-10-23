<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ROOT . '/media/com_easysocial/apps/user/komento/helper.php');

class KomentoWidgetsProfile extends SocialAppsWidgets
{
	/**
	 * Renders a list of recent comments posted by the user
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function sidebarBottom($user)
	{
		if (!SocialKomentoHelper::exists()) {
			return;
		}

		// Get the user params
		$userParams = $this->getUserParams($user->id);
		$appParams = $this->getParams();

		// User might not want to show this app in their profile.
		$enabled = $userParams->get('widget-profile', true) && $appParams->get('widget-profile');

		if (!$enabled) {
			return;
		}

		$output = $this->getRecentComments($user);

		echo $output;
	}


	/**
	 * Display the list of photos a user has uploaded
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function getRecentComments(SocialUser $user)
	{
		$model = KT::model('Comments');
		$params	= $this->getUserParams($user->id);

		// Set options for comments retrival
		$options = array(
						'userid' => $user->id,
						'threaded' => 0,
						'sort' => 'latest',
						'limit' => $params->get('total-profile', 5)
					);

		$comments = $model->getComments('all', 'all', $options);

		if (!$comments) {
			return;
		}

		$comments = KT::formatter('comment', $comments);

		// Get total comments posted by user
		$total = $model->getTotalComment($user->id);

		$this->set('total', $total);
		$this->set('comments', $comments);
		$this->set('user', $user);
		$this->set('params', $params);

		return parent::display('widgets/profile/default');
	}
}
