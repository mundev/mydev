<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialUserAppBlogHookNotificationLikes extends SocialAppHooks
{
	public function execute(&$item)
	{
		// If the skipExcludeUser is true, we don't unset myself from the list
		$excludeCurrentViewer = (isset($item->skipExcludeUser) && $item->skipExcludeUser) ? false : true;

		$users = $this->getReactionUsers($item->uid, $item->context_type, $item->actor_id, $excludeCurrentViewer);

		$names = $this->getNames($users);

		// When user likes on a story
		if ($item->context_type == 'blog.user.create') {
			$post = EB::post($item->uid);

			// We need to determine if the user is the owner
			if ($post->created_by == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
				$item->title = JText::sprintf($this->getPlurality('APP_USER_BLOG_NOTIFICATIONS_USER_LIKES_YOUR_POST', $users), $names);
				return;
			}

			// We do not need to pluralize here since we know there's always only 1 recipient
			if ($item->actor_id == $post->created_by && count($users) == 1) {
				$item->title = JText::sprintf($this->getGenderForLanguage('APP_USER_BLOG_NOTIFICATIONS_USER_LIKES_POST', $item->actor_id), ES::user($post->created_by)->getName());
				return;
			}

			// For other users, we just post a generic message
			$item->title = JText::sprintf($this->getPlurality('APP_USER_BLOG_NOTIFICATIONS_USER_LIKES_USERS_POST', $users), $names, ES::user($post->created_by)->getName());

			return;
		}

		return;
	}
}
