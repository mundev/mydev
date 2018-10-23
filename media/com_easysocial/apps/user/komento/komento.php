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

ES::import('admin:/includes/apps/apps');

class SocialUserAppKomento extends SocialAppItem
{
	/**
	 * Determines if Komento exists on the site
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$file = JPATH_ADMINISTRATOR . '/components/com_komento/includes/komento.php';

			$exists = JFile::exists($file);

			if ($exists) {
				require_once($file);
			}
		}

		return $exists;
	}

	/**
	 * Responsible to display the stream items from Komento
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context !== 'komento' || !in_array($item->verb, array('comment', 'reply', 'like')) || $this->exists() === false) {
			return;
		}

		$element = $item->context;
		$uid = $item->contextId;

		// Get the user's privacy
		$privacy = $this->my->getPrivacy();

		if ($includePrivacy && !$privacy->validate('core.view', $uid, $element, $item->actor->id)) {
			return;
		}

		// Get the comment object
		$comment = KT::comment($item->contextId);

		if (!$comment->id) {
			return;
		}



		if ($item->verb === 'like') {
			$item->likes = false;
			$item->comments = false;
			$item->repost = false;
			$item->display = SOCIAL_STREAM_DISPLAY_MINI;
		} else {
			$item->display = SOCIAL_STREAM_DISPLAY_FULL;

			$item->likes = ES::likes($item->uid, 'komento', 'create', SOCIAL_APPS_GROUP_USER, $item->uid);

			$streamComments = ES::comments($item->uid, 'komento', 'create', SOCIAL_APPS_GROUP_USER, array('url' => ESR::stream(array('layout' => 'item', 'id' => $item->uid))), $item->uid);
			$item->comments = $streamComments;
		}

		if ($includePrivacy) {
			$item->privacy = $privacy->form($uid, $element, $item->actor->id, 'core.view');
		}

		// If that is guest comment, we need to get back the guest name.
		if ($item->actor->id == 0) {
			$item->actor->name = $comment->getAuthorName();
		}

		// Get a list of attachments in the comment
		$attachments = $comment->getAttachments('all');

		$this->set('attachments', $attachments);
		$this->set('actor', $item->actor);
		$this->set('comment', $comment);

		// Prior to 3.0, Komento is also storing the content. We should empty this.
		$item->content = '';

		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->preview = parent::display('streams/preview');
	}

	/**
	 * When a user posts a comment on a comment, we'll try to sync it with Komento
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onAfterCommentSave(&$comment)
	{
		$identifier = explode('.', $comment->element);

		if (empty($identifier[0]) || $identifier[0] !== 'komento' || !$this->exists()) {
			return;
		}

		// If this comment is injected by Komento, then we don't proceed
		$source = $comment->getParams()->get('komento');

		if (!empty($source->source)) {
			return;
		}

		// Get the actor of the comment
		$actor = ES::user($comment->created_by);

		// Get the stream id first
		$streamId = $comment->uid;

		// From the stream id, we get the context id
		$streamTable = ES::table('streamitem');
		$state = $streamTable->load(array('uid' => $streamId));

		if (!$state) {
			return;
		}

		$pid = $streamTable->context_id;

		// Get the parent comment from Komento first
		$parent = KT::comment($streamTable->context_id);

		if ($parent === false) {
			return;
		}

		$config = KT::config();

		if (!$config->get('enable_easysocial_sync_comment')) {
			return;
		}

		$obj = new stdClass();
		$obj->created_by = $comment->created_by;
		$obj->created = $comment->created;
		$obj->publish_up = $comment->created;
		$obj->comment = $comment->comment;
		$obj->parent_id = $pid;
		$obj->depth = $parent->depth + 1;
		$obj->component = $parent->component;
		$obj->cid = $parent->cid;
		$obj->name = $actor->getName();
		$obj->email = $actor->email;
		$obj->published = SOCIAL_STATE_PUBLISHED;

		// We do this checking because there is a possibility that the comment is added by admin, which is not the comment actor itself
		if ($this->my->id == $actor->id) {
			$obj->ip = JRequest::getVar('REMOTE_ADDR', '', 'SERVER');
		}

		// Set the extended parameters of the comment in Komento to
		// prevent Komento from duplicating another comment
		$socialObject = new stdClass();
		$socialObject->source = $comment->id;

		$url = $comment->getParams()->get('url');

		if (!empty($url)) {
			$socialObject->url = $url;
		}

		$komentoParams = new JRegistry();
		$komentoParams->set('social', $socialObject);

		$obj->params = $komentoParams->toString();

		$komentoComment = KT::comment();
		$komentoComment->bind($obj);
		$komentoComment->save();

		// We need to inject this data back into Social Comments
		$comment->setParam('komento', (object) array('target' => $komentoComment->id));
		$comment->store();
	}

	/**
	 * When a comment is deleted, synchronize with Komento so that the comment will also be deleted on Komento
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onAfterDeleteComment(&$comment)
	{
		$identifier = explode('.', $comment->element);

		if (empty($identifier[0]) || $identifier[0] !== 'komento' || !$this->exists()) {
			return;
		}

		$params = $comment->getParams()->get('komento');

		if (empty($params)) {
			return;
		}

		if (!empty($params->target)) {
			$komentoComment = KT::comment($params->target);
			$komentoComment->delete();
		}

		if (!empty($params->source)) {
			$komentoComment = KT::comment($params->source);
			$komentoComment->delete();
		}
	}

	/**
	 * Synchronize likes on the comment with the comment on Komento
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onAfterLikeSave(&$likes)
	{
		$identifier = explode('.', $likes->type);

		if (empty($identifier[0]) || $identifier[0] !== 'komento' || !$this->exists()) {
			return;
		}

		$streamId = $likes->uid;

		$streamTable = ES::table('streamitem');
		$state = $streamTable->load(array('uid' => $streamId));

		if ($state) {

			$actionTable = KT::getTable('actions');
			$exists = $actionTable->load(array('type' => 'likes', 'comment_id' => $streamTable->context_id, 'action_by' => $likes->created_by));

			// If it doesn't exist, we'll create a new like action
			if (!$exists) {

				$comment = KT::comment($streamTable->context_id);

				KT::likes()->like($comment);
			}
		}
	}

	/**
	 * Synchronize likes on the comment when user unlikes a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onAfterLikeDelete(&$likes)
	{
		$identifier = explode('.', $likes->type);

		if (empty($identifier[0]) || $identifier[0] !== 'komento' || !$this->exists()) {
			return;
		}

		$streamId = $likes->uid;

		$streamTable = ES::table('streamitem');
		$state = $streamTable->load(array('uid' => $streamId));

		if ($state) {
			$actionTable = KT::getTable('actions');
			$state = $actionTable->load(array('type' => 'likes', 'comment_id' => $streamTable->context_id, 'action_by' => $likes->created_by));

			if ($state) {
				$comment = KT::comment($streamTable->context_id);
				KT::likes()->unlike($comment);
			}
		}
	}

	/**
	 * Synchronizes likes on comments in EasySocial to Komento replies
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onAfterLikeComment($element, $uid, $comment)
	{
		$identifier = explode('.', $element);

		if (empty($identifier[0]) || $identifier[0] !== 'komento' || !$this->exists()) {
			return;
		}

		$params = $comment->getParams();

		$komentoParams = $params->get('komento');

		if (empty($komentoParams->target)) {
			return;
		}

		$id = $komentoParams->target;

		$komentoComment = KT::comment($id);
		KT::likes()->like($comment);
	}

	/**
	 * Synchronizes likes on comments in EasySocial to Komento replies
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function onAfterUnlikeComment($element, $uid, $comment)
	{
		$identifier = explode('.', $element);

		if (empty($identifier[0]) || $identifier[0] !== 'komento' || !$this->exists()) {
			return;
		}

		$params = $comment->getParams();

		$komentoParams = $params->get('komento');

		if (empty($komentoParams->target)) {
			return;
		}

		$id = $komentoParams->target;

		$komentoComment = KT::comment($id);
		KT::likes()->unlike($comment);
	}

	public function onNotificationLoad(SocialTableNotification &$item)
	{
		if ($item->type !== 'komento') {
			return;
		}

		if (empty($item->params)) {
			return;
		}

		list($element, $action) = explode('.', $item->cmd);

		$params = ES::makeObject($item->params);

		$item->title = JText::sprintf('APP_USER_KOMENTO_NOTIFY_' . strtoupper($params->target) . '_' . strtoupper($action), ES::user($params->owner)->getName(), $params->contentTitle);

		if ($action == 'like') {
			$item->title = JText::sprintf('APP_USER_KOMENTO_NOTIFY_' . strtoupper($params->target) . '_' . strtoupper($action), ES::user($item->actor_id)->getName(), $params->contentTitle);
		}

		return $item;
	}
}
