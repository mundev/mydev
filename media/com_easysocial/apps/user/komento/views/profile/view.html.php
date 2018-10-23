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

class KomentoViewProfile extends SocialAppsView
{
	/**
	 * Displays the application output of Komento on their profile page
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function display($userId = null, $docType = null)
	{
		if (!SocialKomentoHelper::exists()) {
			return;
		}

		$user = ES::user($userId);
		$params = $this->getUserParams($userId);

		$this->setTitle('APP_USER_KOMENTO_VIEW_PROFILE_TITLE');
		
		// Get the Komento comments model
		$model = KT::model('Comments');

		// Set options for comments retrival
		$options = array(
							'userid' => $user->id,
							'threaded' => 0,
							'sort' => 'latest',
							'limit' => $params->get('total-profile', 5)
						);

		$comments = $model->getComments('all', 'all', $options);
		$comments = KT::formatter('comment', $comments);

		$this->set('comments', $comments);
		$this->set('user', $user);

		echo parent::display('profile/default');
	}
}
