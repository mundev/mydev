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

class DiscussionsViewPages extends SocialAppsView
{
	public function display($pageId = null, $docType = null)
	{
		$page = ES::page($pageId);

		// Check if the viewer is allowed here.
		if (!$page->canViewItem()) {
			return $this->redirect($page->getPermalink(false));
		}

		$access = $page->getAccess();

		if (!$access->get('discussions.enabled', true)) {
			return false;
		}

		$this->setTitle('APP_DISCUSSIONS_APP_TITLE');

		// Get app params
		$params = $this->app->getParams();

		$model = ES::model('Discussions');
		$options = array('limit' => $params->get('total', 10));

		$counters = $model->getCounters($page);
		$discussions = $model->getDiscussions($page->id , SOCIAL_TYPE_PAGE, $options);
		
		$pagination = $model->getPagination();
		$pagination->setVar('option' , 'com_easysocial');
		$pagination->setVar('view' , 'pages');
		$pagination->setVar('layout' , 'item');
		$pagination->setVar('id' , $page->getAlias());
		$pagination->setVar('appId' , $this->app->getAlias());

		$this->set('app', $this->app);
		$this->set('counters', $counters);
		$this->set('params', $params);
		$this->set('pagination', $pagination);
		$this->set('cluster', $page);
		$this->set('discussions', $discussions);

		echo parent::display('themes:/site/discussions/default/default');
	}
}