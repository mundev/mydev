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

class PollsViewEvents extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function display($eventId = null, $docType = null)
	{
		$event = ES::event($eventId);

		$access = $event->getAccess();

		if (!$access->get('polls.enabled')) {
			return $this->redirect($event->getPermalink(false));
		}

		if (!$event->canViewItem()) {
			return $this->redirect($event->getPermalink(false));
		}

		$this->setTitle('APP_POLLS_APP_TITLE');
		
		// Get app params
		$params = $this->app->getParams();
		
		$options = array('cluster_id' => $eventId);

		$filter = $this->input->get('filter', 'all', 'string');

		$title = 'COM_EASYSOCIAL_PAGE_TITLE_ALL_POLLS';

		if ($filter == 'mine' && !$this->my->id) {
			$filter = 'all';
		}

		if ($filter == 'mine') {
			$options['user_id'] = $this->my->id;
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_MY_POLLS';
		}

		$model = ES::model('Polls');
		$rows = $model->getPolls($options);
		$pagination = $model->getPagination();

		$polls = array();

		foreach ($rows as $row) {
			$table = ES::table('polls');
			$table->bind($row);

			$polls[] = $table;
		}

		$createButton = ESR::polls(array('layout' => 'create', 'clusterType' => SOCIAL_TYPE_EVENT, 'clusterId' => $event->id));

		$showCreateButton = $event->canCreatePolls() ? true : false;

		$filterAllLink = ESR::events(array('layout' => 'item', 'id' => $event->getAlias(), 'appId' => $this->app->getAlias()));
		$filterMineLink = ESR::events(array('layout' => 'item', 'id' => $event->getAlias(), 'appId' => $this->app->getAlias(), 'filter' => 'mine'));

		$theme = ES::themes();
		$theme->set('cluster', $event);
		$theme->set('filterAllLink', $filterAllLink);
		$theme->set('filterMineLink', $filterMineLink);
		$theme->set('createButton', $createButton);
		$theme->set('showCreateButton', $showCreateButton);
		$theme->set('polls', $polls);
		$theme->set('params', $params);
		$theme->set('pagination', $pagination);
		$theme->set('filter', $filter);
		$theme->set('snackbar', false);
		$theme->set('user', false);

		echo $theme->output('site/polls/default/default');
	}
}