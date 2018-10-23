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

class SocialUserAppEvents extends SocialAppItem
{
	/**
	 * Handles notifications for events
	 *
	 * @since	2.0.15
	 * @access	public
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed = array('events.reminder');

		if (!in_array($item->cmd, $allowed) && !in_array($item->context_type, $allowed)) {
			return;
		}

		// Event reminder
		if ($item->cmd == 'events.reminder') {
			$params = $item->getParams();
			$total = (int) $params->get('total');

			$item->title = JText::sprintf('APP_USER_EVENTS_NEW_UPCOMING_MULTIPLE_EVENTS', $params->get('total'));

			if ($total == 1) {
				$events = $params->get('events');
				$events = json_decode($events);
				$event = ES::event($events[0]);

				$item->title = JText::sprintf('APP_USER_EVENTS_NEW_UPCOMING_SINGLE_EVENT', $event->getEventStart()->format(JText::_('DATE_FORMAT_LC3')));

				$item->content = $event->getTitle();
				$item->image = $event->getAvatar();
			}
			
		}

		// there is nothing to process.
		return false;
	}

	/**
	 * Redirects the user to the appropriate page
	 *
	 * @since	2.0.15
	 * @access	public
	 */
	public function onBeforeNotificationRedirect(&$item)
	{
		$allowed = array('events.reminder');

		if (!in_array($item->cmd, $allowed) && !in_array($item->context_type, $allowed)) {
			return;
		}

		$params = $item->getParams();
		$total = (int) $params->get('total');

		// Update the redirection link
		if ($total > 1) {
			$item->url = ESR::events(array('filter' => 'upcoming'));
		} else {
			$events = $params->get('events');
			$events = json_decode($events);
			$event = ES::event($events[0]);

			$item->url = $event->getPermalink();
		}
	}

	/**
	 * Renders the events story form
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onPrepareStoryPanel($story)
	{
		// If the anywhereId exists, means this came from Anywhere module
		// We need to exclude event form from it.
		if (!is_null($story->anywhereId)) {
			return;
		}

		// Check if user are allowed to create event.
		if (!$this->my->canCreateEvents()) {
			return;
		}

		// We only allow event creation on dashboard, which means if the story target and current logged in user is different, then we don't show this
		// Empty target is also allowed because it means no target.
		if (!empty($story->target) && $story->target != $this->my->id) {
			return;
		}

		$params = $this->getParams();

		// Determine if we should attach ourselves here.
		if (!$params->get('story_event', true)) {
			return;
		}

		// Ensure that the user has access to create events
		if (!$this->my->getAccess()->get('events.create') || !$this->getApp()->hasAccess($this->my->profile_id)) {
			return;
		}

		// Ensure that events is enabled
		if (!$this->config->get('events.enabled')) {
			return;
		}

		// Create plugin object
		$plugin = $story->createPlugin('event', 'panel');

		// Get the theme class
		$theme = ES::themes();
		$theme->set('title', $plugin->title);

		// Get the available event category
		$categories = ES::model('EventCategories')->getCreatableCategories(ES::user()->getProfile()->id);

		$theme->set('categories', $categories);

		$plugin->button->html = $theme->output('site/story/events/button');
		$plugin->content->html = $theme->output('site/story/events/form');

		$script = ES::get('Script');
		$plugin->script = $script->output('site/story/events/plugin');

		return $plugin;
	}

	public function onBeforeStorySave(&$template, &$stream, &$content)
	{
		$params = $this->getParams();

		// Determine if we should attach ourselves here.
		if (!$params->get('story_event', true)) {
			return;
		}

		$in = FD::input();

		$title = $in->getString('event_title');
		$description = $in->getString('event_description');
		$categoryid = $in->getInt('event_category');
		$startDatetime = $in->getString('event_start');
		$endDatetime = $in->getString('event_end');
		$timezone = $in->getString('event_timezone');

		// If no category id, then we don't proceed
		if (empty($categoryid)) {
			return;
		}

		// Perhaps in the future we use FD::model('Event')->createEvent() instead.
		// For now just hardcode it here to prevent field triggering and figuring out how to punch data into the respective field data because the form is not rendered through field trigger.

		$my = FD::user();

		$event = FD::event();

		$event->title = $title;

		$event->description = $description;

		// Set a default params for this event first
		$event->params = '{"photo":{"albums":true},"news":true,"discussions":true,"allownotgoingguest":false,"allowmaybe":true,"guestlimit":0}';

		$event->type = SOCIAL_EVENT_TYPE_PUBLIC;
		$event->creator_uid = $my->id;
		$event->creator_type = SOCIAL_TYPE_USER;
		$event->category_id = $categoryid;
		$event->cluster_type = SOCIAL_TYPE_EVENT;
		$event->alias = FD::model('Events')->getUniqueAlias($title);
		$event->created = FD::date()->toSql();
		$event->key = md5($event->created . $my->password . uniqid());

		$event->state = SOCIAL_CLUSTER_PENDING;

		if ($my->isSiteAdmin() || !$my->getAccess()->get('events.moderate')) {
			$event->state = SOCIAL_CLUSTER_PUBLISHED;
		}

		// Trigger apps
		FD::apps()->load(SOCIAL_TYPE_USER);

		$dispatcher  = FD::dispatcher();
		$triggerArgs = array(&$event, &$my, true);

		// @trigger: onEventBeforeSave
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventBeforeSave', $triggerArgs);

		$state = $event->save();

		// Notifies admin when a new event is created
		if ($event->state === SOCIAL_CLUSTER_PENDING || !$my->isSiteAdmin()) {
			FD::model('Events')->notifyAdmins($event);
		}

		// Process the event meta
		$this->processEventMeta($event, $startDatetime, $endDatetime, $timezone);

		// Recreate the event object
		SocialEvent::$instances[$event->id] = null;
		$event = FD::event($event->id);

		// Create a new owner object
		$event->createOwner($my->id);

		// @trigger: onEventAfterSave
		$triggerArgs = array(&$event, &$my, true);
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventAfterSave' , $triggerArgs);

		// Due to inconsistency, we don't use SOCIAL_TYPE_EVENT.
		// Instead we use "events" because app elements are named with 's', namely users, groups, events.
		$template->context_type = 'events';

		$template->context_id = $event->id;
		$template->cluster_access = $event->type;
		$template->cluster_type = $event->cluster_type;
		$template->cluster_id = $event->id;

		$params = array(
			'event' => $event
		);

		$template->setParams(FD::json()->encode($params));
	}

	public function getDatetimeObject($data = null)
	{
		$dateObj = new SocialEventStartendObject;

		if (empty($data)) {
			return $dateObj;
		}

		$dateObj->load($data);

		return $dateObj;
	}

	public function processEventMeta($event, $startDatetime, $endDatetime, $timezone)
	{
		// We get the joomla timezone
		$original_TZ = new DateTimeZone(JFactory::getConfig()->get('offset'));

		// Get the date with timezone
		$tempStartDate = JFactory::getDate($startDatetime, $original_TZ);
		$tempEndDate = JFactory::getDate($endDatetime, $original_TZ);

		// Check for timezone. If the timezone has been changed, get the new startend date
		if ((!empty($timezone) && $timezone !== 'UTC')) {
			$dtz = new DateTimeZone($timezone);

			// Creates a new datetime string with user input timezone as predefined timezone
			$newStartDatetime = JFactory::getDate($startDatetime, $dtz);
			$newEndDatetime = JFactory::getDate($endDatetime, $dtz);

			// Reverse the timezone back to UTC
			$startDatetime = $newStartDatetime->toSql();
			$endDatetime = $newEndDatetime->toSql();
		}

		$startDatetimeObj = $this->getDatetimeObject($startDatetime);
		$endDatetimeObj = $this->getDatetimeObject($endDatetime);

		// Convert the date to non-offset date
		$nonOffsetStartDate = $tempStartDate->toSql();
		$nonOffsetEndDate = $tempEndDate->toSql();

		$tempStartDatetimeObj = $this->getDatetimeObject($nonOffsetStartDate);
		$tempEndDatetimeObj = $this->getDatetimeObject($nonOffsetEndDate);

		$startString = $startDatetimeObj->toSql();
		$endString = $endDatetime ? $endDatetimeObj->toSql() : '0000-00-00 00:00:00';

		$tempStartString = $tempStartDatetimeObj->toSql();
		$tempEndString = $endDatetime ? $tempEndDatetimeObj->toSql() : '0000-00-00 00:00:00';

		$startGMT = $startString;
		$endGMT = $endString;

		// if no timezone, we need to use the non-offset for the start_gmt column
		// This column used when checking for upcoming event reminder
		if (empty($timezone)) {
			$startGMT = $tempStartString;
			$endGMT = $tempEndString;
		}

		$eventMeta = $event->meta;
		// Set the meta for start end timezone
		$eventMeta->cluster_id = $event->id;
		$eventMeta->start = $startString;
		$eventMeta->end = $endString;
		$eventMeta->timezone = $timezone;
		$eventMeta->start_gmt = $startGMT;
		$eventMeta->end_gmt = $endGMT;

		$eventMeta->store();
	}

	public function onAfterCommentSave($comment)
	{
		$segments = explode('.', $comment->element);

		if (count($segments) !== 3 || $segments[1] !== SOCIAL_TYPE_EVENT) {
			return;
		}

		list($element, $group, $verb) = explode('.', $comment->element);

		// Get the actor
		$actor = FD::user($comment->created_by);

		if ($element === 'events') {
			$event = FD::event($comment->uid);

			$stream = FD::table('Stream');
			$stream->load($comment->stream_id);

			$owner = FD::user($stream->actor_id);

			// APP_USER_EVENTS_EMAILS_FEATURE_COMMENT_ITEM_SUBJECT
			// APP_USER_EVENTS_EMAILS_CREATE_COMMENT_ITEM_SUBJECT
			// APP_USER_EVENTS_EMAILS_UPDATE_COMMENT_ITEM_SUBJECT
			// APP_USER_EVENTS_EMAILS_FEATURE_COMMENT_INVOLVED_SUBJECT
			// APP_USER_EVENTS_EMAILS_CREATE_COMMENT_INVOLVED_SUBJECT
			// APP_USER_EVENTS_EMAILS_UPDATE_COMMENT_INVOLVED_SUBJECT

			// apps/user/events/feature.comment.item
			// apps/user/events/create.comment.item
			// apps/user/events/update.comment.item
			// apps/user/events/feature.comment.involved
			// apps/user/events/create.comment.involved
			// apps/user/events/update.comment.involved

			$emailOptions = array(
				'title' => 'APP_USER_EVENTS_EMAILS_' . strtoupper($verb) . '_COMMENT_ITEM_SUBJECT',
				'template' => 'apps/user/events/' . $verb . '.comment.item',
				'permalink' => $stream->getPermalink(true, true),
				'actor' => $actor->getName(),
				'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
				'actorLink' => $actor->getPermalink(true, true),
				'comment' => $comment->comment
			);

			 $systemOptions  = array(
				'context_type' => $comment->element,
				'content' => $comment->comment,
				'url' => $stream->getPermalink(false, false, false),
				'actor_id' => $comment->created_by,
				'uid' => $comment->uid,
				'aggregate' => true
			);

			 // Notify the owner first
			 if ($comment->created_by != $owner->id) {
				FD::notify('comments.item', array($owner->id), $emailOptions, $systemOptions);
			 }

			 // Get a list of recipients to be notified for this stream item
			 // We exclude the owner of the discussion and the actor of the comment here
			 $recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($owner->id, $comment->created_by));

			 $emailOptions['title'] = 'APP_USER_EVENTS_EMAILS_' . strtoupper($verb) . '_COMMENT_INVOLVED_SUBJECT';
			 $emailOptions['template'] = 'apps/user/events/' . $verb . '.comment.involved';

			 // Notify other participating users
			 FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
		}

		if ($element === 'guests') {
			$guest = FD::table('EventGuest');
			$guest->load($comment->uid);

			$event = FD::event($guest->cluster_id);

			$stream = FD::table('Stream');
			$stream->load($comment->stream_id);

			$owner = FD::user($stream->actor_id);

			// APP_USER_EVENTS_GUESTS_EMAILS_MAKEADMIN_COMMENT_ITEM_SUBJECT
			// APP_USER_EVENTS_GUESTS_EMAILS_GOING_COMMENT_ITEM_SUBJECT
			// APP_USER_EVENTS_GUESTS_EMAILS_NOTGOING_COMMENT_ITEM_SUBJECT
			// APP_USER_EVENTS_GUESTS_EMAILS_MAKEADMIN_COMMENT_INVOLVED_SUBJECT
			// APP_USER_EVENTS_GUESTS_EMAILS_GOING_COMMENT_INVOLVED_SUBJECT
			// APP_USER_EVENTS_GUESTS_EMAILS_NOTGOING_COMMENT_INVOLVED_SUBJECT

			// apps/user/events/guest.makeadmin.comment.item
			// apps/user/events/guest.going.comment.item
			// apps/user/events/guest.notgoing.comment.item
			// apps/user/events/guest.makeadmin.comment.involved
			// apps/user/events/guest.going.comment.involved
			// apps/user/events/guest.notgoing.comment.involved

			$emailOptions = array(
				'title' => 'APP_USER_EVENTS_GUESTS_EMAILS_' . strtoupper($verb) . '_COMMENT_ITEM_SUBJECT',
				'template' => 'apps/user/events/guest.' . $verb . '.comment.item',
				'permalink' => $stream->getPermalink(true, true),
				'actor' => $actor->getName(),
				'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
				'actorLink' => $actor->getPermalink(true, true),
				'comment' => $comment->comment
			);

			 $systemOptions  = array(
				'context_type' => $comment->element,
				'content' => $comment->comment,
				'url' => $stream->getPermalink(false, false, false),
				'actor_id' => $comment->created_by,
				'uid' => $comment->uid,
				'aggregate' => true
			);

			 // Notify the owner first
			 if ($comment->created_by != $owner->id) {
				FD::notify('comments.item', array($owner->id), $emailOptions, $systemOptions);
			 }

			 // Get a list of recipients to be notified for this stream item
			 // We exclude the owner of the discussion and the actor of the comment here
			 $recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($owner->id, $comment->created_by));

			 $emailOptions['title'] = 'APP_USER_EVENTS_GUESTS_EMAILS_' . strtoupper($verb) . '_COMMENT_INVOLVED_SUBJECT';
			 $emailOptions['template'] = 'apps/user/events/guest.' . $verb . '.comment.involved';

			 // Notify other participating users
			 FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
		}

		if ($element === 'discussions') {

			// Uses app/event/discussions onAfterCommentSave logic and language strings since it is the same

			$stream = FD::table('Stream');
			$stream->load($comment->stream_id);

			// Get the discussion object since it's tied to the stream
			$discussion = FD::table('Discussion');
			$discussion->load($comment->uid);

			// APP_EVENT_DISCUSSIONS_EMAILS_CREATE_COMMENT_ITEM_SUBJECT
			// APP_EVENT_DISCUSSIONS_EMAILS_CREATE_COMMENT_INVOLVED_SUBJECT
			// APP_EVENT_DISCUSSIONS_EMAILS_REPLY_COMMENT_ITEM_SUBJECT
			// APP_EVENT_DISCUSSIONS_EMAILS_REPLY_COMMENT_INVOLVED_SUBJECT

			// apps/event/discussions/create.comment.item
			// apps/event/discussions/create.comment.involved
			// apps/event/discussions/reply.comment.item
			// apps/event/discussions/reply.comment.involved

			$emailOptions = array(
				'title' => 'APP_EVENT_DISCUSSIONS_EMAILS_' . strtoupper($verb) . '_COMMENT_ITEM_SUBJECT',
				'template' => 'apps/event/discussions/' . $verb . '.comment.item',
				'permalink' => $stream->getPermalink(true, true),
				'actor' => $actor->getName(),
				'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
				'actorLink' => $actor->getPermalink(true, true),
				'comment' => $comment->comment
			);

			 $systemOptions  = array(
				'context_type' => $comment->element,
				'content' => $comment->comment,
				'url' => $stream->getPermalink(false, false, false),
				'actor_id' => $comment->created_by,
				'uid' => $comment->uid,
				'aggregate' => true
			);

			 // Notify the owner first
			 if ($comment->created_by != $discussion->created_by) {
				FD::notify('comments.item', array($discussion->created_by), $emailOptions, $systemOptions);
			 }

			 // Get a list of recipients to be notified for this stream item
			 // We exclude the owner of the discussion and the actor of the comment here
			 $recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($discussion->created_by, $comment->created_by));

			 $emailOptions['title'] = 'APP_EVENT_DISCUSSIONS_EMAILS_' . strtoupper($verb) . '_COMMENT_INVOLVED_SUBJECT';
			$emailOptions['template'] = 'apps/event/discussions/' . $verb . '.comment.involved';

			 // Notify other participating users
			 FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
		}

		if ($element === 'tasks') {
			// Uses app/event/tasks onAfterCommentSave logic and language strings since it is the same

			$identifier = $verb == 'createMilestone' ? 'milestone' : 'task';

			// Get the milestone/task table
			$table = FD::table($identifier);
			$table->load($comment->uid);

			// Get the owner
			$owner = FD::user($table->owner_id);

			// Get the event
			$event = FD::event($table->uid);

			$recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($owner->id, $comment->created_by));

			// APP_EVENT_TASKS_EMAILS_COMMENTED_ON_YOUR_MILESTONE_SUBJECT
			// APP_EVENT_TASKS_EMAILS_COMMENTED_ON_YOUR_TASK_SUBJECT
			// APP_EVENT_TASKS_EMAILS_COMMENTED_ON_A_MILESTONE_SUBJECT
			// APP_EVENT_TASKS_EMAILS_COMMENTED_ON_A_TASK_SUBJECT

			// apps/event/tasks/comment.milestone
			// apps/event/tasks/comment.task
			// apps/event/tasks/comment.milestone.involved
			// apps/event/tasks/comment.task.involved

			$emailOptions = array(
				'title' => 'APP_EVENT_TASKS_EMAILS_COMMENTED_ON_YOUR_' . strtoupper($identifier) . '_SUBJECT',
				'template' => 'apps/event/tasks/comment.' . $identifier,
				'permalink' => FRoute::stream(array('layout' => 'item', 'id' => $comment->stream_id, 'external' => true)),
				'actor' => $actor->getName(),
				'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
				'actorLink' => $actor->getPermalink(true, true),
				'comment' => $comment->comment
			);

			$systemOptions = array(
				'context_type' => $comment->element,
				'content' => $comment->element,
				'url' => FRoute::stream(array('layout' => 'item', 'id' => $comment->stream_id, 'sef' => false)),
				'actor_id' => $comment->created_by,
				'uid' => $comment->uid,
				'aggregate' => true
			);

			// Notify the owner first
			if ($comment->created_by != $owner->id) {
				FD::notify('comments.item', array($owner->id), $emailOptions, $systemOptions);
			}

			// Get a list of recipients to be notified for this stream item
			// We exclude the owner of the note and the actor of the like here
			$recipients = $this->getStreamNotificationTargets($comment->uid, $element, $group, $verb, array(), array($owner->id, $comment->created_by));

			$emailOptions['title'] = 'APP_EVENT_TASKS_EMAILS_COMMENTED_ON_A_' . strtoupper($identifier) . '_SUBJECT';
			$emailOptions['template'] = 'apps/event/tasks/comment.' . $identifier . '.involved';

			// Notify other participating users
			FD::notify('comments.involved', $recipients, $emailOptions, $systemOptions);
		}
	}

	public function onBeforeGetStream(array &$options, $view = '')
	{
		if ($view != 'dashboard') {
			return;
		}

		$allowedContext = array('events','story','photos', 'tasks', 'discussions', 'guests');

		if (is_array($options['context']) && in_array('events', $options['context'])){
			// we need to make sure the stream return only cluster stream.
			$options['clusterType'] = SOCIAL_TYPE_EVENT;
		} else if ($options['context'] === 'events') {
			$options['context']     = $allowedContext;
			$options['clusterType'] = SOCIAL_TYPE_EVENT;
		}
	}

	public function onStreamVerbExclude(&$exclude)
	{
		$params = $this->getParams();

		$excludeVerb = array();

		if (!$params->get('stream_create', true)) {
			$excludeVerb[] = 'create';
		}


		$excludeVerb[] = 'update';
		$exclude['events'] = $excludeVerb;


		// since we are remmoving these stream setting from user app, for site that upgraded from 1.4, we still
		// need to exclude these items.
		$excludeVerb = array();

		if (!$params->get('stream_going', true)) {
			$excludeVerb[] = 'going';
		}

		$excludeVerb[] = 'feature';
		$excludeVerb[] = 'update';
		$excludeVerb[] = 'makeadmin';
		$excludeVerb[] = 'notgoing';

		$exclude['guests'] = $excludeVerb;

	}
}

class SocialEventStartendObject
{
	public $year;
	public $month;
	public $day;
	public $hour = '00';
	public $minute = '00';
	public $second = '00';

	private $date;

	public function __construct()
	{
		$args = func_get_args();

		if (empty($args)) {
			return true;
		}

		return call_user_func_array(array($this, 'load'), $args);
	}

	public function load()
	{
		$args = func_get_args();

		$count = func_num_args();

		if ($count === 1 && is_string($args[0]) && !empty($args[0])) {
			$json = FD::json();

			if ($json->isJsonString($args[0])) {
				$args[0] = $json->decode($args[0]);
			} else {
				if (strtotime($args[0])) {
					$args[0] = FD::date($args[0], false);
				}
			}
		}

		$keys = array('year', 'month', 'day', 'hour', 'minute', 'second');

		if ($count === 1 && (is_object($args[0]) || is_array($args[0]))) {
			$date = (object) $args[0];

			foreach ($keys as $key) {
				if (isset($date->$key)) {
					$this->$key = $date->$key;
				}
			}
		}

		if ($count === 1 && $args[0] instanceof SocialDate) {
			$date = $args[0];

			$this->year = $date->toFormat('Y');
			$this->month = $date->toFormat('m');
			$this->day = $date->toFormat('d');
			$this->hour = $date->toFormat('H');
			$this->minute = $date->toFormat('i');
			$this->second = $date->toFormat('s');
		}

		if ($count > 1) {
			foreach ($args as $i => $arg) {
				$this->{$keys[$i]} = $arg;
			}
		}

		$this->date = $this->toDate();

		return true;
	}

	public function isEmpty()
	{
		foreach ($this->toArray() as $k => $v) {
			// we do not want to test against the private 'date'
			if ($k == 'date') {
				continue;
			}
			if (empty($v)) {
				return true;
			}
		}

		return false;
	}

	public function isValid()
	{
		return !$this->isEmpty() && strtotime($this->day . '-' . $this->month . '-' . $this->year . ' ' . $this->hour . ':' . $this->minute . ':' . $this->second);
	}

	public function toJSON()
	{
		return FD::json()->encode($this->toArray());
	}

	public function toDate()
	{
		if (empty($this->date)) {
			if ($this->isEmpty()) {
				$this->date = FD::date();
			} else {
				$this->date = FD::date($this->year . '-' . $this->month . '-' . $this->day . ' ' . $this->hour . ':' . $this->minute . ':' . $this->second, false);
			}
		}

		return $this->date;
	}

	public function toArray($publicOnly = true)
	{
		if ($publicOnly) {
			return call_user_func('get_object_vars', $this);
		}

		return get_object_vars($this);
	}

	public function toFormat($format, $local = true)
	{
		return $this->toDate()->toFormat($format);
	}

	public function toSql()
	{
		return $this->toDate()->toSql();
	}

	public function toString()
	{
		return $this->day . ' ' . JText::_($this->toFormat('F')) . ' ' . $this->year;
	}

	public function toUnix()
	{
		return $this->toDate()->toUnix();
	}

	public function toAge()
	{
		$now = FD::date();

		$years = floor(($now->toFormat('U') - $this->toFormat('U')) / (60*60*24*365));

		return $years;
	}

	public function setTimezone($dtz)
	{
		if (empty($dtz)) {
			return $this;
		}

		if (is_string($dtz)) {
			$dtz = new DateTimeZone($dtz);
		}

		$this->date->setTimezone($dtz);
	}

	public function __toString()
	{
		return $this->isValid() ? $this->toString() : '';
	}

	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->toDate(), $method), $arguments);
	}
}

