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

class SocialTableFriendInvite extends SocialTable
{
	public $id = null;
	public $user_id	= null;
	public $email = null;
	public $created = null;
	public $message = null;
	public $registered_id = null;

	public function __construct($db)
	{
		parent::__construct('#__social_friends_invitations', 'id', $db);
	}

	/**
	 * Automatically add the inviter and the target as friends
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function makeFriends()
	{
		if (!$this->user_id || !$this->registered_id) {
			return false;
		}

		$model = ES::model('Friends');
		$state = $model->request($this->user_id, $this->registered_id, SOCIAL_FRIENDS_STATE_FRIENDS);

		if (!$state) {
			$this->setError($model->getError());
			return false;
		}

		// Assign points to the user that created this invite because the invitee registered on the site
		$points = ES::points();
		$points->assign('friends.registered', 'com_easysocial' , $this->user_id);

		// @badge: friends.registered
		// Assign badge for the person that invited friend already registered on the site.
		$badge = ES::badges();
		$badge->log('com_easysocial', 'friends.registered', $this->user_id, JText::_('COM_EASYSOCIAL_FRIENDS_BADGE_INVITED_FRIEND_REGISTERED'));

		return true;
	}

	/**
	 * Overrides the parent's implementation of store
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function store($pk = null)
	{
		$isNew = !$this->id;

		// Save this into the table first
		parent::store($pk);

		// Add this into the mail queue
		if ($isNew) {
			$this->send($isNew);
		}
	}

	/**
	 * Send friend invitation
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function send($isNew = false)
	{
		$jconfig  = ES::jconfig();
		$mailer   = ES::mailer();
		$template = $mailer->getTemplate();

		$sender = ES::user($this->user_id);

		$params = new stdClass;
		$params->senderName = $sender->getName();
		$params->message = $this->message;
		$params->siteName = $jconfig->getValue('sitename');
		$params->manageAlerts = false;
		$params->link = FRoute::registration(array('invite' => $this->id, 'external' => true));

		// it seems like some mail server disallow to change the sender name and reply to. we will commment out this for now.
		// $template->setSender($sender->getName(), $sender->email);
		// $template->setReplyTo($sender->email);

		$template->setRecipient('', $this->email);
		$template->setTitle(JText::sprintf('COM_EASYSOCIAL_FRIENDS_INVITE_MAIL_SUBJECT', $jconfig->getValue('sitename')));
		$template->setTemplate('site/friends/invite', $params);

		$mailer->create($template);

		if ($isNew) {
			// Assign points to the user that created this invite
			$points = ES::points();
			$points->assign('friends.invite', 'com_easysocial' , $this->user_id);		
		} else {
			// Once the invitation resent, we need to update the date
			$this->created = JFactory::getDate()->toSql();
			parent::store();
		}
	}

}
