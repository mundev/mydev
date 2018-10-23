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

class FollowersWidgetsDashboard extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function sidebarBottom()
	{
		// Get application params
		$appParams = $this->getParams();
		$params = $this->getUserParams($this->my->id);

		if ($appParams->get('widget_suggestions', true)) {
			echo $this->getSuggestions($params);
		}
	}

	/**
	 * Display a list of followers for the user
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getSuggestions(&$params)
	{
		$appParams = $this->app->getParams();

		if (!$params->get('widget_suggestions', $appParams->get('widget_suggestions', true))) {
			return;
		}

		$limit = $params->get('limit', $appParams->get('widget_suggestions_total', 5));

		$model = ES::model('Followers');
		$users = $model->getSuggestions($this->my->id, array('max' => $limit));
		$total = $model->getTotalSuggestions($this->my->id);

		if (!$users) {
			return;
		}

		$theme = ES::themes();
		$theme->set('total', $total);
		$theme->set('users', $users);
		$theme->set('limit', $limit);

		return $theme->output('themes:/apps/user/followers/widgets/dashboard/suggestions');
	}

}
