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

class SocialPageAppReviews extends SocialAppItem
{
	/**
	 * Notification triggered when generating notification item.
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed = array('page.moderate.review');

		// If the cmd not allowed, return.
		if (!in_array($item->cmd, $allowed)) {
			return;
		}

		$user = ES::user($item->actor_id);
		$page = ES::page($item->uid);
		$item->image = $page->getAvatar();

		if ($item->cmd == 'page.moderate.review') {
			$item->title = JText::sprintf('APP_REVIEW_NOTIFICATIONS_PENDING_MODERATION', $user->getName());
		}

	}

	/**
	 * Determines if the app should appear on the sidebar
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function appListing($view, $id, $type)
	{
		if ($type != SOCIAL_TYPE_PAGE) {
			return true;
		}

		// We should not display the reviews on the app if it's disabled
		$page = ES::page($id);
		$registry = $page->getParams();

		if (!$registry->get('reviews', true)) {
			return false;
		}

		return true;
	}

	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onStreamCountValidation(&$item, $includePrivacy = true)
	{
		// If this is not it's context, we don't want to do anything here.
		if ($item->context_type != 'reviews') {
			return false;
		}

		// if this is a cluster stream, let check if user can view this stream or not.
		$params = ES::registry($item->params);
		$page = ES::page($params->get('page'));

		if (!$page) {
			return;
		}

		$item->cnt = 1;

		if ($page->type != SOCIAL_PAGES_PUBLIC_TYPE) {
			if (!$page->isMember(ES::user()->id)) {
				$item->cnt = 0;
			}
		}

		return true;
	}

	/**
	 * Prepares the stream item for pages
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context != 'reviews') {
			return;
		}

		// page access checking
		$page = $item->getCluster();

		if (!$page) {
			return;
		}

		if (!$page->canViewItem()) {
			return;
		}

		// Ensure that announcements are enabled for this page
		$registry = $page->getParams();

		if (!$registry->get('reviews', true)) {
			return;
		}

		// Define standard stream looks
		$item->display = SOCIAL_STREAM_DISPLAY_FULL;
		$item->repost = false;

		$params = $item->getParams();

		if ($item->verb == 'create') {
			$this->prepareCreateStream($item, $page, $params);
		}
	}

	private function prepareCreateStream(SocialStreamItem &$item, SocialPage $page, $params)
	{
		$reviews = ES::table('Reviews');
		$reviews->load($params->get('reviews')->id);

		if (!$reviews->id) {
			return;
		}
		
		// Get the permalink
		$permalink = $reviews->getPermalink();

		// Get the app params
		$appParams 	= $this->getApp()->getParams();

		// Format the content
		$this->format($reviews, $appParams->get('stream_length'));

		// Attach actions to the stream
		$this->attachActions($item, $reviews, $permalink, $appParams, $page);

		$access = $page->getAccess();
		if ($this->my->isSiteAdmin() || $page->isAdmin() || ($access->get('stream.edit', 'admins') == 'members' && $item->actor->id == $this->my->id)) {
			$item->edit_link = $reviews->getEditPermalink();;
		}

		$this->set('item', $item);
		$this->set('cluster', $page);
		$this->set('appParams', $appParams);
		$this->set('permalink', $permalink);
		$this->set('reviews', $reviews);
		$this->set('actor', $item->actor);

		// Load up the contents now.
		$item->title = parent::display('themes:/site/streams/reviews/create.title');
		$item->preview = parent::display('themes:/site/streams/reviews/preview');
	}

	private function format(&$reviews, $length = 0)
	{
		if ($length == 0) {
			return;
		}

		$reviews->content = JString::substr(strip_tags($reviews->content), 0, $length) . ' ' . JText::_('COM_EASYSOCIAL_ELLIPSES');
	}

	private function attachActions(&$item, &$reviews, $permalink, $appParams, $page)
	{
		$commentParams = array('url' => $permalink, 'clusterId' => $page->id);
		// We need to link the comments to the reviews
		$item->comments = ES::comments($reviews->id, 'reviews', 'create', SOCIAL_APPS_GROUP_PAGE, $commentParams, $item->uid);

		// The comments for the stream item should link to the reviews itself.
		if (!$appParams->get('allow_comments') || !$reviews->comments) {
			$item->comments = false;
		}

		// The likes needs to be linked to the reviews itself
		$likes = ES::likes();
		$likes->get($reviews->id, 'reviews', 'create', SOCIAL_APPS_GROUP_PAGE, $item->uid, array('clusterId' => $page->id));

		$item->likes = $likes;
	}
}
