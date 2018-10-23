<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

class DiscussControllerDiscuss extends SocialAppsController
{
	/**
	 * Display user discussion
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getDiscussions()
	{
		$userId = $this->input->get('user_id', 0, 'int');
		$filter = $this->input->get('filter', '', 'default');

		$lang = JFactory::getLanguage();
		$lang->load('com_easydiscuss', JPATH_ROOT);

		$user = ES::user($userId);

		$app = $this->getApp();

		// Get recent new post created
		$model	= ED::model('Posts');

		if ($filter == 'unresolved' || $filter == 'resolved') {

			$resolve = null;

			if ($filter == 'resolved') {
				$resolve = true;
			}

			$posts = $model->getUnresolvedFromUser($userId, $resolve);
		} else if ($filter == 'userreplies') {
			$posts = $model->getRepliesFromUser($userId, 'lastreplied');

		} else {
			$options = array('filter' => $filter, 'userId' => $userId, 'includeAnonymous' => false);

			// If the post is anonymous we shouldn't show to public.
			if (ED::user()->id == $userId) {
				$options['includeAnonymous'] = true;
				$options['private'] = true;
			}

			$posts = $model->getDiscussions($options);
		}

		// Format discussions
		$posts = ED::formatPost($posts, false, true);

		$empty = false;

		if (!$posts) {
			$empty = true;
		}

		// Build pagination layout
		$pagination = $model->getPagination();
		$pagination->pagination->setAdditionalUrlParam('option', 'com_easysocial');
		$pagination->pagination->setAdditionalUrlParam('view', 'profile');
		$pagination->pagination->setAdditionalUrlParam('id', $userId);
		$pagination->pagination->setAdditionalUrlParam('appId', $app->getAlias());
		$pagination->pagination->setAdditionalUrlParam('filter', $filter);

		$theme = ES::themes();
		$theme->set('user', $user);
		$theme->set('posts', $posts);
		$theme->set('pagination', $pagination);

		$contents = $theme->output('apps/user/discuss/profile/items');

		return $this->ajax->resolve($contents, $empty, $pagination);
	}
}
