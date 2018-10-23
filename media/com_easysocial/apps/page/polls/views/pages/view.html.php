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

class PollsViewPages extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($pageId = null, $docType = null)
	{
		$page = ES::page($pageId);

		// Check if the viewer is allowed here.
		if (!$page->canViewItem()) {
			return $this->redirect($page->getPermalink(false));
		}

		$this->setTitle('APP_POLLS_APP_TITLE');
		
		// Get app params
		$params = $this->app->getParams();
		
		$options = array('cluster_id' => $pageId);

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

		$createButton = ESR::polls(array('layout' => 'create', 'clusterType' => SOCIAL_TYPE_PAGE, 'clusterId' => $page->id));
		$showCreateButton = $page->canCreatePolls() ? true : false;

		$filterAllLink = ESR::pages(array('layout' => 'item', 'id' => $page->getAlias(), 'appId' => $this->app->getAlias()));
		$filterMineLink = ESR::pages(array('layout' => 'item', 'id' => $page->getAlias(), 'appId' => $this->app->getAlias(), 'filter' => 'mine'));

		$theme = ES::themes();
		$theme->set('cluster', $page);
		$theme->set('filterAllLink', $filterAllLink);
		$theme->set('filterMineLink', $filterMineLink);
		$theme->set('createButton', $createButton);
		$theme->set('polls', $polls);
		$theme->set('params', $params);
		$theme->set('pagination', $pagination);
		$theme->set('filter', $filter);
		$theme->set('showCreateButton', $showCreateButton);
		$theme->set('snackbar', false);
		$theme->set('user', false);

		echo $theme->output('site/polls/default/default');
	}
}