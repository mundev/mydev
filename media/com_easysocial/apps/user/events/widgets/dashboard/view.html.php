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

class EventsWidgetsDashboard extends SocialAppsWidgets
{
	public function sidebarBottom()
	{
		ES::requireLogin();

		$params = $this->getParams();

		if (!$params->get('widget_dashboard', true)) {
			return;
		}

		$days = $params->get('widget_dashboard_days', 14);
		$total = $params->get('widget_dashboard_total', 5);

		$date = ES::date();
		$now = $date->toSql();
		$future = ES::date($date->toUnix() + ($days * 24*60*60))->toSql();

		$options = array();
		$options['start-after'] = $now;
		$options['start-before'] = $future;
		$options['limit'] = $total;
		$options['state'] = SOCIAL_STATE_PUBLISHED;
		$options['ordering'] = 'start';
		$options['type'] = 'user';

		$model = ES::model('Events');
		$events = $model->getEvents($options);

		if (empty($events)) {
			return;
		}

		$theme = ES::themes();
		$theme->set('events', $events);
		$theme->set('app', $this->app);

		echo $theme->output('themes:/apps/user/events/widgets/dashboard/upcoming');
	}
}
