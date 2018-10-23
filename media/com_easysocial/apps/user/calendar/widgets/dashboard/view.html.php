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

class CalendarWidgetsDashboard extends SocialAppsWidgets
{
	/**
	 * Display user appointments
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function sidebarBottom()
	{
		if (!$this->my->id) {
			return;
		}

		// Get the application params
		$params = $this->getParams();

		if (!$params->get('widgets_upcoming', true)) {
			return;
		}

		echo $this->getUpcomingSchedules();
	}

	/**
	 * Displays online users widget
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getUpcomingSchedules()
	{
		$params = $this->getParams();

		$model = $this->getModel('Calendar');
		$days = $params->get('widgets_days', 14);
		$total = $params->get('widgets_total', 5);

		$result = $model->getUpcomingSchedules($this->my->id, $days, $total);
		$appointments = array();

		if (!$result) {
			return;
		}

		foreach ($result as $row) {
			$calendar = FD::table('Calendar');
			$calendar->bind( $row );

			$appointments[]	= $calendar;
		}

		$theme = ES::themes();
		$theme->set('appointments', $appointments);
		$theme->set('app', $this->app);

		return $theme->output('themes:/apps/user/calendar/widgets/dashboard/schedules');
	}
}
