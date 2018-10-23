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

class EasySocialViewActivities extends EasySocialSiteView
{
	public function display($tpl = null)
	{
		ES::requireLogin();
		ES::checkCompleteProfile();

		if (!$this->config->get('activity.logs.enabled')) {
			return $this->redirect(ESR::dashboard(array(), false));
		}
		// Get the necessary attributes from the request
		$filterType = $this->input->get('type', 'all', 'default');
		$active = $filterType;
		$context = SOCIAL_STREAM_CONTEXT_TYPE_ALL;

		// Default title
		$title = JText::sprintf('COM_EASYSOCIAL_ACTIVITY_ITEM_TITLE', ucfirst($filterType));
		switch ($filterType) {
			case 'hiddenapp':
				$title = 'COM_EASYSOCIAL_ACTIVITY_HIDDEN_APPS';
				break;

			case 'hidden':
				$title = 'COM_EASYSOCIAL_ACTIVITY_HIDDEN_ACTIVITIES';
				break;

			case 'hiddenactor':
				$title = 'COM_EASYSOCIAL_ACTIVITY_HIDDEN_ACTORS';
				break;

			case 'all':
				$title = 'COM_EASYSOCIAL_ACTIVITY_ALL_ACTIVITIES';
				break;

			default:
				break;
		}

		// Set the page title
		$this->page->title($title);

		// Set the page breadcrumb
		$this->page->breadcrumb($title);

		if ($filterType != 'all' && $filterType != 'hidden' && $filterType != 'hiddenapp' && $filterType != 'hiddenactor') {
			$context = $filterType;
			$filterType = 'all';
		}

		// Load up activities model
		$model = FD::model('Activities');

		if ($filterType == 'hiddenapp') {
			$activities = $model->getHiddenApps($this->my->id);
			$nextLimit = $model->getNextLimit('0');
		} else if($filterType == 'hiddenactor') {
			$activities = $model->getHiddenActors($this->my->id);
			$nextLimit = $model->getNextLimit('0');
		} else {
			// Retrieve user activities.
			$stream = FD::stream();
			$options = array('uId' => $this->my->id, 'context' => $context, 'filter' => $filterType);

			$activities = $stream->getActivityLogs($options);
			$nextLimit = $stream->getActivityNextLimit();
		}

		// Get a list of apps
		$result = $model->getApps();
		$apps = array();

		foreach ($result as $app) {
			if (!$app->hasActivityLog()) {
				continue;
			}

			$app->favicon = '';
			$app->image = $app->getIcon();
			$favicon = $app->getFavIcon();

			if ($favicon) {
				$app->favicon = $favicon;
			}

			// Load the app's css
			$app->loadCss();

			$apps[] = $app;
		}

		$this->set('active', $active);
		$this->set('title', JText::_($title));
		$this->set('apps', $apps);
		$this->set('activities', $activities);
		$this->set('nextlimit', $nextLimit);
		$this->set('filtertype', $filterType);

		echo parent::display('site/activities/default/default');
	}

}
