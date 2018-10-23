<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/apps/apps');

class SocialUserAppNotes extends SocialAppItem
{
	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onStreamCountValidation( &$item, $includePrivacy = true )
	{
		// If this is not it's context, we don't want to do anything here.
		if( $item->context_type != 'notes' )
		{
			return false;
		}

		$item->cnt = 1;

		if( $includePrivacy )
		{
			$uid		= $item->id;
			$my         = FD::user();
			$privacy	= FD::privacy( $my->id );

			$sModel = FD::model( 'Stream' );
			$aItem 	= $sModel->getActivityItem( $item->id, 'uid' );

			if( $aItem )
			{
				$uid 	= $aItem[0]->id;

				if( !$privacy->validate( 'core.view', $uid , SOCIAL_TYPE_ACTIVITY , $item->actor_id ) )
				{
					$item->cnt = 0;
				}
			}
		}

		return true;
	}

	/**
	 * Responsible to return the excluded verb from this app context
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onStreamVerbExclude( &$exclude )
	{
		// Get app params
		$params		= $this->getParams();

		$excludeVerb = false;

		if(! $params->get('stream_update', true)) {
			$excludeVerb[] = 'update';
		}

		if (! $params->get('stream_create', true)) {
			$excludeVerb[] = 'create';
		}

		if ($excludeVerb !== false) {
			$exclude['notes'] = $excludeVerb;
		}
	}

	/**
	 * Prepares the stream item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context !== 'notes') {
			return;
		}

		// Determine if we should display the stream items
		$params = $this->getParams();

		$allowed = array('create', 'update');

		if (!in_array($item->verb, $allowed)) {
			return;
		}

		if (!$params->get('stream_' . $item->verb, true)) {
			return;
		}

		// Load the note
		$note = $this->getTable('Note');
		$note->load($item->contextId);

		$item->comments = ES::comments($item->contextId, $item->context, $item->verb, SOCIAL_APPS_GROUP_USER, array('url' => $note->getPermalink()));
		$item->likes = ES::likes($item->contextId, $item->context, $item->verb, SOCIAL_APPS_GROUP_USER, $item->uid);
		$item->repost = ES::repost($item->uid, SOCIAL_TYPE_STREAM);

		$this->set('params', $params);
		$this->set('note', $note);
		$this->set('actor', $item->actor);

		$item->display = SOCIAL_STREAM_DISPLAY_FULL;
		$item->title = parent::display('themes:/site/streams/notes/' . $item->verb . '.title');
		$item->preview = parent::display('themes:/site/streams/notes/preview');

		// Append the opengraph tags
		$item->addOgDescription($note->getContent());
	}


	/**
	 * Processes notifications
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		// Likes on note
		$allowed = array('notes.user.create', 'notes.user.update');

		if (!in_array($item->context_type, $allowed)) {
			return;
		}

		if ($item->type == 'likes') {

			$note 			= $this->getTable( 'Note' );
			$note->load($item->uid);

			$obj	= $this->getHook('notification', 'likes');
			$obj->execute($item, $note);

			return;
		}

		if ($item->type == 'comments') {

			$note 			= $this->getTable( 'Note' );
			$note->load($item->uid);

			$obj	= $this->getHook('notification', 'comments');
			$obj->execute($item, $note);

			return;
		}
	}

	/**
	 * Processes notifications
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onAfterCommentSave($comment)
	{
		$allowed 	= array('notes.user.create', 'notes.user.update');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		// Get the verb
		$segments 		= explode('.', $comment->element);
		$verb 			= isset($segments[2]) ? $segments[2] : '';

		if (!$verb) {
			return;
		}

		// Get the note object
		$note 			= $this->getTable( 'Note' );
		$note->load($comment->uid);

		$emailOptions 	= array(
			'title'		=> 'APP_USER_NOTES_EMAILS_COMMENT_ITEM_TITLE',
			'template'	=> 'apps/user/notes/comment.item',
			'comment'	=> $comment->comment,
			'permalink'	=> $note->getPermalink(true, true)
		);

		$systemOptions 	= array(
			'title'			=> '',
			'content'		=> $comment->comment,
			'context_type'	=> $comment->element,
			'url'			=> $note->getPermalink(false, false, false),
			'actor_id'		=> $comment->created_by,
			'uid'			=> $comment->uid,
			'aggregate'		=> true
		);

		// Notify the note owner if the commenter is not the note owner
		if ($comment->created_by != $note->user_id) {
			FD::notify('comments.item', array($note->user_id), $emailOptions, $systemOptions);
		}

		// Get a list of recipients to be notified for this stream item.
		// We exclude the owner of the note and the actor of the like here
		$recipients = $this->getStreamNotificationTargets($comment->uid, 'notes', 'user', $verb, array(), array($note->user_id, $comment->created_by));

		$emailOptions['title'] = 'APP_USER_NOTES_EMAILS_COMMENT_INVOLVED_TITLE';
		$emailOptions['template'] = 'apps/user/notes/comment.involved';

		// Notify participating users
		FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
	}

	/**
	 * Processes notifications
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onAfterLikeSave($likes)
	{
		$allowed = array('notes.user.create', 'notes.user.update');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		// Get the verb
		$segments = explode('.', $likes->type);
		$verb = $segments[2];

		$note = $this->getTable('Note');
		$note->load($likes->uid);

		$systemOptions 	= array(
			'title' 		=> '',
			'context_type' 	=> $likes->type,
			'url' 			=> $note->getPermalink(false, false, false),
			'actor_id' 		=> $likes->created_by,
			'uid'			=> $likes->uid,
			'aggregate'		=> true
		);

		// Notify the owner first if the liker is not the note owner
		if ($likes->created_by != $note->user_id) {
			ES::notify('likes.item', array($note->user_id), array(), $systemOptions);
		}

		// Get a list of recipients to be notified for this stream item
		// We exclude the owner of the note and the actor of the like here
		$recipients = $this->getStreamNotificationTargets($likes->uid, 'notes', 'user', $verb, array(), array($note->user_id, $likes->created_by));

		ES::notify('likes.involved', $recipients, array(), $systemOptions);
	}

	/**
	 * Prepares the activity log
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onPrepareActivityLog(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context !== 'notes') {
			return;
		}

		$note = $this->getTable('Note');
		$note->load($item->contextId);

		$this->set('note', $note);
		$this->set('actor', $item->actor);

		$item->title = parent::display('streams/' . $item->verb . '.title');
	}
}
