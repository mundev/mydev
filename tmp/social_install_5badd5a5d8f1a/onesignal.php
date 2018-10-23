<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/apps/apps');

require_once(__DIR__ . '/libraries/onesignal.php');

class SocialUserAppOneSignal extends SocialAppItem
{
	/**
	 * Notifies the user when there is a new notification
	 *
	 * @since	2.0.0
	 * @access	public
	 */
	public function onSystemNotificationAfterCreate(SocialTableNotification &$item, SocialNotificationTemplate &$template)
	{
		$params = $this->getParams();

		if (!$params->get('push_notifications', true)) {
			return;
		}
		
		// We need to simulate onNotificationLoad so that the rest of the apps could format the item before we spit it out to the user.
		$dispatcher = ES::getInstance('Dispatcher');

		// We need to skip the exlude actor
		// Because we don't want to exclude actor of the action in the notification
		$item->skipExcludeUser = true;
		
		$type = $item->type;
		$args = array(&$item);

		// @trigger onNotificationLoad from user apps
		$dispatcher->trigger(SOCIAL_APPS_GROUP_USER, 'onNotificationLoad', $args, $type);

		// @trigger onNotificationLoad from group apps
		$dispatcher->trigger(SOCIAL_APPS_GROUP_GROUP, 'onNotificationLoad', $args, $type);

		// @trigger onNotificationLoad from event apps
		$dispatcher->trigger(SOCIAL_APPS_GROUP_EVENT, 'onNotificationLoad', $args, $type);

		// @trigger onNotificationLoad from page apps
		$dispatcher->trigger(SOCIAL_APPS_GROUP_PAGE, 'onNotificationLoad', $args, $type);

		// Exclude notifications
		if (isset($item->exclude) && $item->exclude) {
			return;
		}

		// Get the target and actor
		$actor = ES::user($item->actor_id);
		$target = ES::user($item->target_id);

		// Format the item
		ES::notification()->formatItem($item);

		// The permalink should be linked to the notification view so that we can update the "seen" state
		$permalink = ESR::notifications(array('id' => $item->id, 'layout' => 'route', 'external' => true));

		$lib = new OneSignal($params);
		$options = array(
				'title' => JText::_('APP_USER_ONESIGNAL_NEW_NOTIFICATION_TITLE'),
				'contents' => strip_tags($item->title),
				'permalink' => $permalink,
				'icon' => $actor->getAvatar()
			);		


		$response = $lib->notify($target, $options);
	}

	/**
	 * Notifies the user when they have a new notification
	 *
	 * @since	2.0.0
	 * @access	public
	 */
	public function onConversationAfterSave(SocialConversation $conversation)
	{
		$params = $this->getParams();

		if (!$params->get('push_messages', true)) {
			return;
		}

		// The sender is always the current logged in user since they are the one that invoked this.
		$creator = $this->my;

		$options = array(
						'title' => JText::_('APP_USER_ONESIGNAL_NEW_MESSAGE_TITLE'),
						'contents' => JText::sprintf('APP_USER_ONESIGNAL_NEW_MESSAGE_CONTENTS', $creator->getName()),
						'permalink' => $conversation->getPermalink(false, true),
						'icon' => $creator->getAvatar()
					);		

		$lib = new OneSignal($params);

		// Exclude the current user from being notified
		$exclusion = array($this->my->id);
		$recipients = $conversation->getParticipants($exclusion);

		if (!$recipients) {
			return;
		}
		
		foreach ($recipients as $recipient) {
			$lib->notify($recipient, $options);
		}
	}

	/**
	 * Notifies the user when a friend request has been made
	 *
	 * @since	2.0.0
	 * @access	public
	 */
	public function onFriendRequest($table, $requester, $target)
	{
		$params = $this->getParams();

		if (!$params->get('push_friends', true)) {
			return;
		}

		$options = array(
				'title' => JText::_('APP_USER_ONESIGNAL_NEW_FRIEND_REQUEST_TITLE'),
				'contents' => JText::sprintf('APP_USER_ONESIGNAL_NEW_FRIEND_REQUEST_CONTENTS', $requester->getName()),
				'permalink' => $requester->getPermalink(false, true),
				'icon' => $requester->getAvatar()
			);

		$lib = new OneSignal($this->getParams());
		$lib->notify($target, $options);
	}

	/**
	 * When the extension ends, we'll attach the output so that we can get users to subscribe
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onComponentEnd()
	{
		// Only process on html views
		if ($this->doc->getType() != 'html') {
			return;
		}

		// This shouldn't be rendered at the backend
		if ($this->app->isAdmin() || !$this->my->id) {
			return;
		}

		$params = $this->getParams();

		// Normalize the subdomain
		$subdomain = $params->get('subdomain', '');

		if ($subdomain && stristr($subdomain, 'https://') === false) {
			$subdomain = 'https://' . $subdomain;
		}

		// Add manifest header for https site to work in chrome browser
		if (!$subdomain) {
			$this->doc->addCustomTag('<link rel="manifest" href="/manifest.json">');
		}

		$script = ES::script();
		$script->set('subdomain', $subdomain);
		$script->set('params', $params);
		$output = $script->output('apps/user/onesignal/script');

		$this->doc->addCustomTag($output);
	}

	/**
	 * Renders the alerts form so users can manage them
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onRenderAlerts(&$customAlerts)
	{
		$params = $this->getParams();

		// Normalize the subdomain
		$subdomain = $params->get('subdomain', '');

		if ($subdomain && stristr($subdomain, 'https://') === false) {
			$subdomain = 'https://' . $subdomain;
		}

		$this->set('subdomain', $subdomain);
		$this->set('params', $params);

		$obj = new stdClass();
		$obj->element = 'push';
		$obj->sidebar = parent::display('alerts/sidebar');
		$obj->contents = parent::display('alerts/contents');
		$obj->form = false;

		$customAlerts[] = $obj;
	}
}
