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

class ReviewsViewEdit extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since   2.1
	 * @access  public
	 * @param   int     The user id that is currently being viewed.
	 */
	public function display($uid = null , $docType = null)
	{
		ES::requireLogin();

		$group = ES::group($uid);

		// Set the group title
		ES::document()->title(JText::_('APP_GROUP_REVIEWS_GROUP_TITLE_EDIT'));

		// Get the reviews item
		$reviews = ES::table('Reviews');
		$reviews->load(JRequest::getInt('reviewId'));

		if ($reviews->created_by != $this->my->id && !$group->isAdmin()) {
			ES::info()->set(false, JText::_('COM_EASYSOCIAL_GROUPS_ONLY_MEMBER_ARE_ALLOWED'), SOCIAL_MSG_ERROR);
			return $this->redirect($group->getPermalink(false));
		}

		$this->set('reviews', $reviews);
		$this->set('cluster', $group);
		$this->set('returnUrl', '');

		echo parent::display('themes:/site/reviews/form/default');
	}
}
