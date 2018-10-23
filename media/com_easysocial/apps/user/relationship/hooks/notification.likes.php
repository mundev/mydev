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

class SocialUserAppRelationshipHookNotificationLikes extends SocialAppHooks
{
	public function execute(&$item)
	{
		$contexts = explode('.', $item->context_type);

		$streamItem = ES::table('StreamItem');
		$streamItem->load(array('context_id' => $item->uid, 'context_type' => $contexts[0], 'verb' => $contexts[2]));

		$stream = ES::table('Stream');
		$stream->load($streamItem->uid);

		// If the skipExcludeUser is true, we don't unset myself from the list
		$excludeCurrentViewer = (isset($item->skipExcludeUser) && $item->skipExcludeUser) ? false : true;

		$users = $this->getReactionUsers($item->uid, $item->context_type, $item->actor_id, $excludeCurrentViewer);
		$names = $this->getNames($users);

		// Assign first users from likers for avatar
		$item->userOverride = ES::user($users[0]);

		if ($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
			$item->title = JText::sprintf($this->getPlurality('APP_USER_RELATIONSHIP_USER_LIKES_YOUR_RELATIONSHIP_STATUS', $users), $names);
			return;
		}

		if ($stream->actor_id == $item->actor_id && count($users) == 1) {
			$item->title = JText::sprintf($this->getGenderForLanguage('APP_USER_RELATIONSHIP_OWNER_LIKES_RELATIONSHIP_STATUS', $stream->actor_id), $names);
			return;
		}

		$item->title = JText::sprintf($this->getPlurality('APP_USER_RELATIONSHIP_USER_LIKES_USER_RELATIONSHIP_STATUS', $users), $names, ES::user($stream->actor_id)->getName());
		return;
	}
}
