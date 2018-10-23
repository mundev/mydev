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

ES::import('site:/views/views');

class EasySocialViewPolls extends EasySocialSiteView
{
	/**
	 * Renders a list of polls created on the site
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for user profile completeness
		ES::checkCompleteProfile();

		$filter = $this->input->get('filter', 'all', 'string');
		$userId = $this->input->get('userid', 0, 'int');
		$user = false;

		if ($userId) {
			return $this->displayUser($userId);
		}

		$options = array();
		$canonicalOptions = array('external' => true);

		$title = 'COM_EASYSOCIAL_PAGE_TITLE_ALL_POLLS';

		if ($filter == 'mine' && !$this->my->id) {
			$filter = 'all';
		}

		if ($filter == 'mine') {
			$options['user_id'] = $this->my->id;
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_MY_POLLS';

			$canonicalOptions['filter'] = 'mine';
		}

		$this->page->title($title);
		$this->page->breadcrumb($title);

		// add canonical link
		$this->page->canonical(ESR::polls($canonicalOptions));

		$model = ES::model('Polls');
		$result = $model->getPolls($options);
		$pagination = $model->getPagination();

		$polls = array();

		if ($result) {
			foreach ($result as $row) {
				$poll = ES::table('Polls');
				$poll->bind($row);

				$polls[] = $poll;
			}
		}

		$snackbar = JText::_('COM_EASYSOCIAL_POLLS');

		$createButton = ESR::polls(array('layout' => 'create'));

		$showCreateButton = $this->my->canCreatePolls() ? true : false;

		$filterAllLink = ESR::polls(array('filter' => 'all'));
		$filterMineLink = ESR::polls(array('filter' => 'mine'));

		$this->set('cluster', false);
		$this->set('filterAllLink', $filterAllLink);
		$this->set('filterMineLink', $filterMineLink);
		$this->set('createButton', $createButton);
		$this->set('showCreateButton', $showCreateButton);
		$this->set('snackbar', $snackbar);
		$this->set('filter', $filter);
		$this->set('polls', $polls);
		$this->set('pagination', $pagination);
		$this->set('user', false);

		echo parent::display('site/polls/default/default');
	}

	/**
	 * Display user polls
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function displayUser($userId)
	{
		$user = ES::user($userId);

		$options = array();
		$options['user_id'] = $userId;

		$title = JText::sprintf('COM_ES_PAGE_TITLE_USER_POLLS', $user->getName());

		$this->page->title($title);
		$this->page->breadcrumb($title);

		$model = ES::model('Polls');
		$result = $model->getPolls($options);
		$pagination = $model->getPagination();

		$polls = array();

		if ($result) {
			foreach ($result as $row) {
				$poll = ES::table('Polls');
				$poll->bind($row);

				$polls[] = $poll;
			}
		}

		// Directly get the total from the query above
		$total = $model->getState('total');

		$createButton = ESR::polls(array('layout' => 'create'));
		$showCreateButton = $this->my->canCreatePolls() && $user->isViewer() ? true : false;

		$filter = $user->isViewer() ? 'mine' : 'user';

		$this->set('cluster', false);
		$this->set('createButton', $createButton);
		$this->set('filter', $filter);
		$this->set('polls', $polls);
		$this->set('pagination', $pagination);
		$this->set('snackbar', false);
		$this->set('showStatistic', true);
		$this->set('total', $total);
		$this->set('showCreateButton', $showCreateButton);
		$this->set('user', $user);

		echo parent::display('site/polls/default/default');
	}

	/**
	 * Renders poll creation form for cluster
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function createCluster()
	{
		$clusterType = $this->input->get('clusterType', SOCIAL_TYPE_USER, 'default');
		$clusterId = $this->input->get('clusterId', 0, 'int');

		if ($clusterType == SOCIAL_TYPE_USER || !$clusterId) {
			return $this->app->redirect(ESR::polls(array(), false));
		}

		$cluster = ES::cluster($clusterType, $clusterId);
		
		// Increment the hit counter
		$cluster->hit();

		if (!$cluster->id) {
			return $this->app->redirect(ESR::polls(array(), false));
		}

		if ($this->hasErrors()) {
			$this->info->set($this->getMessage());
		}

		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_CREATE_POLL');

		$polls = ES::polls();

		$this->set('polls', $polls);
		$this->set('cluster', $cluster);

		parent::display('site/polls/create/default');
	}

	/**
	 * Renders the poll creation form
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function create()
	{
		$clusterType = $this->input->get('clusterType', SOCIAL_TYPE_USER, 'default');
		$clusterId = $this->input->get('clusterId', 0, 'int');

		if ($clusterType != SOCIAL_TYPE_USER && $clusterId) {
			return $this->createCluster();
		}

		// User might invoke the url to reach this page
		if (!$this->my->canCreatePolls()) {
			$this->app->redirect(ESR::polls(array(), false));
			return;
		}

		if ($this->hasErrors()) {
			$this->info->set($this->getMessage());
		}

		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_CREATE_POLL');

		$polls = ES::polls();

		$this->set('polls', $polls);

		parent::display('site/polls/create/default');
	}

	/**
	 * Called during saving poll from form. Story uses a different method from the app
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function postCreate(SocialTablePolls $table)
	{
		$this->info->set($this->getMessage());

		$redirect = $table->getPermalink(false);

		$this->redirect($redirect);
		return;
	}
}
