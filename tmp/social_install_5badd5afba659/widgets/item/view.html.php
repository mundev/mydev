<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class HoursFieldWidgetsItem extends EasySocial
{
	/**
	 * Renders the opening hours of a business
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function coverFooter(SocialPage $page, SocialTableField $field)
	{
		$output = $this->processData($page, $field);

		echo $output;
	}

	/**
	 * Display user audio on the side bar
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function sidebarTop(SocialPage $page, SocialTableField $field)
	{
		$output = $this->processData($page, $field, 'sidebar');

		echo $output;
	}

	/**
	 * Retrive open-close data
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function processData($page, $field, $layout = 'display')
	{
		$theme = ES::themes();
		$params = $field->getParams();

		$timeFormat = ($params->get('time_format') == '1') ? 'h:iA' : 'H:i';
		$title = $params->get('title');

		// Get the field value
		$data = $field->getData($page->id, $page->getType());
		$timezone = isset($data['timezone']) ? $data['timezone'] : false;

		$data = isset($data['data']) ? $data['data'] : $data;
		$data = json_decode($data);

		if (!$data) {
			return;
		}

		// Page doesn't have a business operation hour
		if ($data->hours_type == 'disabled') {
			return;
		}

		// Default to be closed
		$open = false;
		$alwaysOpen = false;
		$closeTime = '';
		$openTime = '';

		// flag to determine if we need to show check time button or not.
		$checkTime = false;

		// Prepare the full details variable
		$fullDetails = false;
		$todayDetails = false;

		// The page might not set any business hours, we should just skip it.
		if ($data->hours_type == 'selected' && !isset($data->days)) {
			return;
		}

		// Get a date without timezone and apply the timezone from the field data (page owner's timezone)
		$date = ES::date('', false);

		if ($timezone) {
			$timezoneObj = new DateTimeZone($timezone);
			$date->setTimezone($timezoneObj);
		}

		$currentDay = $date->format('N');
		$currentHour = $date->format('h');


		// Opening times
		if ($data->hours_type == 'selected' && $data->days) {

			$currentTime = $date->toSql(true);

			$days = get_object_vars($data->days);
			$openDays = array_keys($days);

			// Compare the working days first
			if (in_array($currentDay, $openDays)) {

				$hasMultiple = is_array($data->days->$currentDay->start_hour) && count($data->days->$currentDay->start_hour) > 1;
				$isLegacy = is_array($data->days->$currentDay->start_hour) ? false : true;

				if (!$isLegacy && $hasMultiple) {
					$checkTime = true;
				} else {

					// reset the values
					if (!$isLegacy && !$hasMultiple) {
						$data = $this->resetDataArray($data, $currentDay);
					}

					// Build the open / close hours
					$openingString = $data->days->$currentDay->start_hour;
					$closingString = $data->days->$currentDay->end_hour;

					if ($timeFormat == 'h:iA') {
						if (isset($data->days->$currentDay->start_period)) {
							$openingString .= ' ' . (string) $data->days->$currentDay->start_period;
						}

						if (isset($data->days->$currentDay->end_period)) {
							$closingString .= ' ' . (string) $data->days->$currentDay->end_period;
						}
					}

					// We need to check yesterday's open-close time first.
					// If closing time is lesser than the opening, means the closing is today.
					// eg: 9PM (Wed) - 4AM (Thu)
					$yesterdayData = $this->yesterday($data, $timeFormat);

					// We need to check today's open-close time as well.
					// If closing time is lesser than the opening, means the closing is the tomorrow.
					// eg: 9PM (Thu) - 4AM (Fri)
					$todayData = $this->today($data, $timeFormat);

					$opening = JFactory::getDate($openingString);
					$closing = JFactory::getDate($closingString);

					$openTime = $opening->format($timeFormat);
					$closeTime = $closing->format($timeFormat);

					// Compare the work hours now
					if ($currentTime > $opening->toSql() && $currentTime < $closing->toSql()) {
						$open = true;
					}

					// We need to check if yesterday's closing is today
					if ($yesterdayData && $currentTime < $yesterdayData['closing']->toSql()) {
						$open = true;
						$openTime = $yesterdayData['opening']->format($timeFormat . ' D');
						$closeTime = $yesterdayData['closing']->format($timeFormat . ' D');
					}

					// We need to check if the page is closing tomorow
					if ($todayData && $currentTime >= $todayData['opening']->toSql()) {
						$open = true;
						$openTime = $todayData['opening']->format($timeFormat . ' D');
						$closeTime = $todayData['closing']->format($timeFormat . ' D');
					}


				}
			}
		}

		if ($data->hours_type == 'always') {
			$open = true;
			$alwaysOpen = true;
		}

		// If not open today, we shoud display the next open day
		if (!$open) {
			$nextOpenTime = $this->getNextOpen($data, $timeFormat);
			$theme->set('nextOpenTime', $nextOpenTime);
		}

		// if (($layout == 'sidebar' && !$alwaysOpen)) {
			$fullDetails = $this->getFullDetails($data, $timeFormat, $open);
		// }

		// if ($layout != 'sidebar' && $checkTime) {
		// 	$todayDetails = $this->getTodayDetails($data, $timeFormat, $open);
		// }

		$theme->set('openTime', $openTime);
		$theme->set('closeTime', $closeTime);
		$theme->set('alwaysOpen', $alwaysOpen);
		$theme->set('open', $open);
		$theme->set('title', $title);
		$theme->set('fullDetails', $fullDetails);
		// $theme->set('todayDetails', $todayDetails);
		$theme->set('timezone', $timezone);
		$theme->set('checkTime', $checkTime);

		$output = $theme->output('fields/page/hours/widgets/' . $layout);

		return $output;
	}

	/**
	 * Retireve day operational hours
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getTodayDetails($data, $timeFormat)
	{
		if (empty($data)) {
			return;
		}

		$date = ES::date();
		$today = $date->format('N');

		$count = count($data->days->$today->start_hour);

		$string = '';
		for ($t = 0; $t < $count; $t++) {
			// Build the open / close hours
			$dayOpeningString = $data->days->$today->start_hour[$t];
			$dayClosingString = $data->days->$today->end_hour[$t];

			if ($timeFormat == 'h:iA') {
				$dayOpeningString .= ' ' . $data->days->$today->start_period[$t];
				$dayClosingString .= ' ' . $data->days->$today->end_period[$t];
			}

			$dayOpening = JFactory::getDate($dayOpeningString);
			$dayClosing = JFactory::getDate($dayClosingString);

			$openTime = $dayOpening->format($timeFormat);
			$closeTime = $dayClosing->format($timeFormat);

			$string .= $openTime . ' &mdash; ' . $closeTime;
			// we need to check if the closing is the next day
			if ($dayClosing->toSql() < $dayOpening->toSql()) {
				$tomorrowString = JFactory::getDate()->dayToString($today + 1, true);
				$string .= ' (' . $tomorrowString . ')';
			}

			$string .= "<br>";
		}

		$details = new stdClass;
		$details->class = 't-text--success';
		$details->string = $string;

		return $details;
	}



	/**
	 * Retireve full operational hours
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getFullDetails($data, $timeFormat, $open)
	{
		if (empty($data)) {
			return;
		}

		$date = ES::date();
		$today = $date->format('N');

		for ($i=0; $i < 7; $i++) {

			$details = new stdClass;
			$dayString = JFactory::getDate()->dayToString($i);

			$class = '';

			if ($today == $i) {
				$class = $open ? 't-text--success' : 't-text--danger';
			}

			$details->class = $class;

			if (!isset($data->days->$i)) {
				$details->string = JText::_('COM_EASYSOCIAL_PAGE_FIELD_HOURS_CLOSED');
				$fullDetails[$dayString] = $details;
				continue;
			}

			$hasMultiple = is_array($data->days->$i->start_hour) && count($data->days->$i->start_hour) > 1;
			$isLegacy = is_array($data->days->$i->start_hour) ? false : true;


			if (!$isLegacy && $hasMultiple) {

				if ($today == $i) {
					$class = 't-text--success';
				}


				$count = count($data->days->$i->start_hour);

				$string = '';
				for ($t = 0; $t < $count; $t++) {
					// Build the open / close hours
					$dayOpeningString = $data->days->$i->start_hour[$t];
					$dayClosingString = $data->days->$i->end_hour[$t];

					if ($timeFormat == 'h:iA') {
						$dayOpeningString .= ' ' . $data->days->$i->start_period[$t];
						$dayClosingString .= ' ' . $data->days->$i->end_period[$t];
					}

					$dayOpening = JFactory::getDate($dayOpeningString);
					$dayClosing = JFactory::getDate($dayClosingString);

					$openTime = $dayOpening->format($timeFormat);
					$closeTime = $dayClosing->format($timeFormat);

					$string .= $openTime . ' &mdash; ' . $closeTime;
					// we need to check if the closing is the next day
					if ($dayClosing->toSql() < $dayOpening->toSql()) {
						$tomorrowString = JFactory::getDate()->dayToString($i + 1, true);
						$string .= ' (' . $tomorrowString . ')';
					}

					$string .= "<br>";
				}

				$details->class = $class;
				$details->string = $string;
				$fullDetails[$dayString] = $details;

				continue;
			}


			// reset the values
			if (!$isLegacy && !$hasMultiple) {
				$data = $this->resetDataArray($data, $i);
			}

			// Build the open / close hours
			$dayOpeningString = $data->days->$i->start_hour;
			$dayClosingString = $data->days->$i->end_hour;

			if ($timeFormat == 'h:iA') {
				if (isset($data->days->$i->start_period)) {
					$dayOpeningString .= ' ' . $data->days->$i->start_period;
				}

				if (isset($data->days->$i->end_period)) {
					$dayClosingString .= ' ' . $data->days->$i->end_period;
				}
			}

			$dayOpening = JFactory::getDate($dayOpeningString);
			$dayClosing = JFactory::getDate($dayClosingString);

			$openTime = $dayOpening->format($timeFormat);
			$closeTime = $dayClosing->format($timeFormat);

			$string = $openTime . ' &mdash; ' . $closeTime;
			// we need to check if the closing is the next day
			if ($dayClosing->toSql() < $dayOpening->toSql()) {
				$tomorrowString = JFactory::getDate()->dayToString($i + 1, true);
				$string .= ' (' . $tomorrowString . ')';
			}

			$details->class = $class;
			$details->string = $string;
			$fullDetails[$dayString] = $details;
		}

		return $fullDetails;
	}

	/**
	 * Retrieves the next open time
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getNextOpen($data, $timeFormat)
	{
		$date = ES::date();
		$today = (int) $date->format('N'); // '2' - TUESDAY
		$count = 0;
		$nextOpen = false;

		if (isset($data->days->$today)) {
			$hasMultiple = is_array($data->days->$today->start_hour) && count($data->days->$today->start_hour) > 1;
			$isLegacy = is_array($data->days->$today->start_hour) ? false : true;

			if (!$isLegacy && $hasMultiple) {
				return false;
			}
		}


		// This checking is for if it is already closed today
		// Only if today is not holiday
		if (isset($data->days->$today)) {
			// Get current datetime
			$currentTime = $date->toSql(true);

			// Get today's closing time
			$closingString = $data->days->$today->end_hour;

			if ($timeFormat == 'h:iA') {
				$closingString .= ' ' . $data->days->$today->end_period;
			}

			$closing = JFactory::getDate($closingString);

			// If today's already closed, we check for tomorrow
			if ($currentTime >= $closing->toSql()) {
				$today++;
				$count = 1;
			}
		}

		for ($i=$today; $i < 8; $i++) {

			// Once found the first day of the next opening, we skip the rest.
			if (isset($data->days->$i)) {
				$nextOpen = $i;
				break;
			}
			$count++;
		}

		// This checking is for if it will close for the rest of the week
		// We need to check for next week.
		if (!$nextOpen) {
			for ($i=1; $i < 8; $i++) {

				if (isset($data->days->$i)) {
					$nextOpen = $i;
					break;
				}
				$count++;
			}
		}

		// check for nextOpen
		if (isset($data->days->$nextOpen)) {
			$hasMultiple = is_array($data->days->$nextOpen->start_hour) && count($data->days->$nextOpen->start_hour) > 1;
			$isLegacy = is_array($data->days->$nextOpen->start_hour) ? false : true;

			if (!$isLegacy && $hasMultiple) {
				return false;
			}
		}

		// reset the values
		if (!$isLegacy && !$hasMultiple) {
			$data = $this->resetDataArray($data, $nextOpen);
		}

		$nextOpeningString = $data->days->$nextOpen->start_hour;

		if ($timeFormat == 'h:iA') {
			$nextOpeningString .= ' ' . $data->days->$nextOpen->start_period;
		}

		$nextOpening = JFactory::getDate($nextOpeningString . ' +' . $count . ' day');

		// If next open is today, we display 'TODAY' instead
		if ($count == 0) {
			return JText::sprintf('COM_ES_PAGE_FIELD_HOURS_TODAY', $nextOpening->format($timeFormat));
		}

		return $nextOpening->format($timeFormat . ' D');
	}

	/**
	 * To identify if today's closing time is tommorow
	 *
	 * @since   2.0.16
	 * @access  public
	 */
	public function today($data, $timeFormat)
	{
		$date = ES::date();
		$today = $date->format('N');

		$todayOpeningString = $data->days->$today->start_hour;
		$todayClosingString = $data->days->$today->end_hour;

		if ($timeFormat == 'h:iA') {
			if (isset($data->days->$today->start_period)) {
				$todayOpeningString .= ' ' . $data->days->$today->start_period;
			}

			if (isset($data->days->$today->end_period)) {
				$todayClosingString .= ' ' . $data->days->$today->end_period;
			}
		}

		$todayOpening = JFactory::getDate($todayOpeningString);
		$todayClosing = JFactory::getDate($todayClosingString);

		$todayData = false;

		// if closing is lower than opening (9pm-4am), means the closing is tomorrow
		if ($todayClosing->toSql() <= $todayOpening->toSql()) {
			$todayData = array();
			$todayData['opening'] = $todayOpening;

			// We need to get today's closing date obj
			$todayData['closing'] = JFactory::getDate($todayClosing . ' +1 day');
		}

		return $todayData;
	}

	/**
	 * To identify if yesterday's closing time is today
	 *
	 * @since   2.0.16
	 * @access  public
	 */
	public function yesterday($data, $timeFormat)
	{
		$date = ES::date();
		$today = $date->format('N');

		// Get yesterday data
		$yesterday = $today - 1;

		// If yesterday equals to 0, means it is the last day of the week (7).
		if ($yesterday == 0) {
			$yesterday = 7;
		}

		if (! isset($data->days->$yesterday)) {
			return false;
		}

		$hasMultiple = is_array($data->days->$yesterday->start_hour) && count($data->days->$yesterday->start_hour) > 1;
		$isLegacy = is_array($data->days->$yesterday->start_hour) ? false : true;

		if (!$isLegacy && $hasMultiple) {
			return false;
		}

		// reset the values
		if (!$isLegacy && !$hasMultiple) {
			$data = $this->resetDataArray($data, $yesterday);
		}

		$yesterdayOpeningString = $data->days->$yesterday->start_hour;
		$yesterdayClosingString = $data->days->$yesterday->end_hour;

		if ($timeFormat == 'h:iA') {
			if (isset($data->days->$yesterday->start_period)) {
				$yesterdayOpeningString .= ' ' . $data->days->$yesterday->start_period;
			}

			if (isset($data->days->$yesterday->end_period)) {
				$yesterdayClosingString .= ' ' . $data->days->$yesterday->end_period;
			}
		}

		// Must -1 so that it will get yesterday date instead of today.
		$yesterdayOpening = JFactory::getDate($yesterdayOpeningString . ' -1 day');
		$yesterdayClosing = JFactory::getDate($yesterdayClosingString . ' -1 day');

		$yesterdayData = false;

		if ($yesterdayClosing->toSql() <= $yesterdayOpening->toSql()) {
			$yesterdayData = array();
			$yesterdayData['opening'] = $yesterdayOpening;

			// We need to get today's closing date obj
			$yesterdayData['closing'] = JFactory::getDate($yesterdayClosing . ' +1 day');
		}

		return $yesterdayData;
	}

	/**
	 * reset day value
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function resetDataArray($data, $idx)
	{
		if (isset($data->days) && isset($data->days->$idx) && is_array($data->days->$idx->start_hour)) {
			$data->days->$idx->start_hour = $data->days->$idx->start_hour[0];
			if (isset($data->days->$idx->start_period)) {
				$data->days->$idx->start_period = $data->days->$idx->start_period[0];
			}
			$data->days->$idx->end_hour = $data->days->$idx->end_hour[0];
			if (isset($data->days->$idx->end_period)) {
				$data->days->$idx->end_period = $data->days->$idx->end_period[0];
			}

		}

		return $data;
	}

}
