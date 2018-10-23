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

class SocialFieldsPageHours extends SocialFieldItem
{
	/**
	 * trigger for preparing criteria
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onPrepareAdvancedSearch(SocialAdvancedSearchField $field, $mode, &$queries, &$oQueries, $criteria, $operator, $condition, $datakey = '')
	{
		if ($field->type != 'hours') {
			return;
		}

		$day = '';
		$start = '';
		$end = '';

		if (strpos($condition, '|')) {
			$data = explode('|', $condition);

			$start = $data[0];
			$end = $data[1];

			if ($operator == 'hourswithday') {
				$day = $data[0];
				$start = $data[1];
				$end = $data[2];
			}


			if (!$start) {
				if (!$end) {
					// no value. nothing to search
					return;
				} else {
					$start = $end;
				}
			}

			if (!$end) {
				$end = $start;
			}
		}

		// dump($criteria, $operator, $condition, $datakey);


		$db = ES::db();

		$query = 'select distinct a.`uid`';
		$query .= ' from `#__social_fields_data` as a';
		$query .= ' inner join `#__social_fields` as b on a.`field_id` = b.`id` and b.`unique_key` = ' . $db->Quote($field->code);
		$query .= ' where a.`type` = ' . $db->Quote($field->group);

		$key = '';
		if (!$datakey || $datakey == 'day') {
			$key = 'days';
			$query .= ' AND a.`datakey` = ' . $db->Quote('days');
		}


		// $query .= ' AND ';

		switch($operator) {
			case 'hourswithday':
				// this only supported hours search that has to take days into considaration in one query.
				if ($day == 'all') {
					$day = '';
				}


				// for start time
				$query .= 'AND (a.`datakey` like ' . $db->Quote('start-' . $day . '%');
				$query .= ' and (a.`raw` >= ' . $db->Quote($start);
				if ($end >= $start) {
					$query .= ' and a.`raw` <= ' . $db->Quote($end);
				}
				$query .= ')';

				$query .= ')';
				$query .= ' and exists (select aa.`uid` from `#__social_fields_data` as aa where aa.`uid` = a.`uid`';
				if (!$day) {
					$query .= '					and (aa.`datakey` = REPLACE(a.`datakey`, ' . $db->Quote('start-') . ', ' . $db->Quote('end-') . ')';
				} else {
					$query .= '					and (aa.`datakey` like ' . $db->Quote('end-' . $day . '%');
				}

				// for end time.
				if ($end == $start) {
					$query .= ' 				and aa.`raw` >= ' . $db->Quote($end) . ')';
				} else if ($end > $start) {

					$query .= ' 				and (aa.`raw` >= ' . $db->Quote($start);
					$query .= ' 				and aa.`raw` <= ' . $db->Quote($end) . ')';

					if ($end == '24:00') {
						$query .= ' OR aa.`raw` >= ' . $db->Quote('12:00') . ')';
					} else {
						$query .= ')';
					}

				} else {
					$query .= ' 				and aa.`raw` >= ' . $db->Quote($end) . ')';
				}

				$query .= ')';



				break;

			case 'between':

				$test = '';

				if ($key == 'days') {
					if ($end < $start) {
						$a = $end;
						$end = $start;
						$start = $a;
					}

					$diff = ($end - $start) - 1;

					$test = $start;

					for ($i = 1; $i <= $diff; $i++) {
						$test = $test . ' ' . ($start + $i);
					}

					$test .= ' ' . $end;

					$query .= ' AND a.`raw` LIKE ' . $db->Quote('%' . $test . '%');
				} else {
					// hours

					// handle 24 hours
					if ($start == '00:00' && $end == '23:00') {
						$query .= 'AND (a.`datakey` = ' . $db->Quote('data');
						$query .= ' and a.`raw` LIKE ' . $db->Quote('always%');
						$query .= ')';
					} else {

						$query .= 'AND (a.`datakey` like ' . $db->Quote('start-%');
						$query .= ' and ((a.`raw` >= ' . $db->Quote($start);

						// for end time are smaller than start time, we dont want to filter by end time.
						// e.g. hours between 11pm to 3am
						if ($end > $start) {
							$query .= ' and a.`raw` <= ' . $db->Quote($end);
						}

						// reason we start > 12:00 is for working your from night to morning. e.g. 11pm to 4am
						$query .= ') OR a.`raw` >= ' . $db->Quote('12:00') . ')';

						$query .= ')';
						$query .= ' and exists (select aa.`uid` from `#__social_fields_data` as aa where aa.`uid` = a.`uid`';
						// $query .= '					and (aa.`datakey` like ' . $db->Quote('end-%');
						$query .= '					and (aa.`datakey` = REPLACE(a.`datakey`, ' . $db->Quote('start-') . ', ' . $db->Quote('end-') . ')';

						if ($end > $start || $end == $start) {
							$query .= ' 				and aa.`raw` > ' . $db->Quote($start);
							$query .= ' 				and aa.`raw` <= ' . $db->Quote($end) . ')';

						} else {
							$query .= ' 				and aa.`raw` >= ' . $db->Quote($end) . ')';
						}

						$query .= ')';
					}
				}


				break;
			case 'notequal':
				// we use not equel for 'close' operation

				if ($key == 'days') {

					// this is to support any days (as long its not closed on everyday) from page search module.
					$subCond = ($condition) ? '%' . $condition . '%' : '';
					$query .= ' AND a.`raw` NOT LIKE ' . $db->Quote($subCond);
				} else {
					// hours
					$data = explode(':', $condition);
					$start = $data[0] . ':' . '00';
					$end = $data[0] . ':' . '59';

					$query .= 'AND (a.`datakey` like ' . $db->Quote('end-%');
					$query .= ' and a.`raw` >= ' . $db->Quote($start) . ' and a.`raw` <= ' . $db->Quote($end);
					$query .= ')';
				}


				break;
			case 'equal':
			default:

				if ($key == 'days') {
					$query .= ' AND a.`raw` LIKE ' . $db->Quote('%' . $condition . '%');
				} else {
					// hours
					$data = explode(':', $condition);
					$start = $data[0] . ':' . '00';
					$end = $data[0] . ':' . '59';

					$query .= 'AND (a.`datakey` like ' . $db->Quote('start-%');
					$query .= ' and a.`raw` >= ' . $db->Quote($start) . ' and a.`raw` <= ' . $db->Quote($end);
					$query .= ')';
				}

				break;
		}


		$queries[] = $query;

		// echo $query;
		// echo '<br />';

		// dump($criteria, $operator, $condition, $datakey);
	}

	/**
	 * trigger for preparing data keys
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onPrepareDataKey(SocialAdvancedSearchField $field, &$keys, &$hasKey, &$selected)
	{
		if ($field->type != 'hours') {
			return;
		}

		// we want to reset the data keys
		$keys = array(
			'day' => JText::_('COM_ES_PAGE_FIELD_DAY'), //full names
			'hour' => JText::_('COM_ES_PAGE_FIELD_HOUR')
		);

		$hasKey = true;
	}

	/**
	 * trigger for preparing operators
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onPrepareOperator(SocialAdvancedSearchField $field, &$operators, &$selected)
	{
		if ($field->type != 'hours') {
			return;
		}

		$openLabel = 'COM_ES_PAGE_FIELD_DAY_OPEN_ON';
		$openBetweenLabel = 'COM_ES_PAGE_FIELD_OPEN_BETWEEN';
		$closeLabel = 'COM_ES_PAGE_FIELD_DAY_CLOSED_ON';

		if ($field->keys == 'hour') {
			$openLabel = 'COM_ES_PAGE_FIELD_HOUR_OPEN_AT';
			$closeLabel = 'COM_ES_PAGE_FIELD_HOUR_CLOSED_AT';
		}

		// we want to reset the opreators
		$operators = array(
			'equal' => JText::_($openLabel),
			'between' => JText::_($openBetweenLabel),
			'notequal' => JText::_($closeLabel),
		);
	}

	/**
	 * trigger for preparing condition
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onPrepareCondition(SocialAdvancedSearchField $field, SocialAdvancedSearchCondition &$condition, &$selected)
	{
		if ($field->type != 'hours') {
			return;
		}

		$condition->type = $field->type;

		// always default to true for hours field.
		$condition->show = true;

		$days = $this->getDays();
		$hours = $this->getHours();

		$list = array();

		$namespace = 'day';

		if (!$field->keys) {
			$field->keys = 'day';
		}

		if ($field->keys == 'day') {
			$list = $days;

			if ($condition->operator == 'between') {
				$namespace .= '.range';
				$condition->range = true;
			}

		} else if ($field->keys == 'hour'){

			foreach($hours as $hour) {
				$option = new stdClass();
				$option->title = JText::_($hour .':00');
				$option->value = $hour .':00';

				$list[] = $option;
			}

			$namespace = 'hour';

			if ($condition->operator == 'between') {
				$namespace .= '.range';
				$condition->range = true;
			}
		}

		$condition->list = $list;
		$condition->theme = 'fields/page/hours/conditions/' . $namespace;

	}


	/**
	 * Retrieves the days in a week
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function getDays()
	{
		$items = array('MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY');
		$days = array();

		$i = 1;
		foreach ($items as &$item) {
			$day = new stdClass();
			$day->id = 'day-' . $i;
			$day->title = JText::_($item);
			$day->value = $i;

			$days[] = $day;

			$i++;
		}

		return $days;
	}

	/**
	 * Retrieves the days in a week
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function getHours()
	{
		$hours = array();

		for($i = 0; $i <= 23; $i++) {
			$hours[] = str_pad($i, 2, '0', STR_PAD_LEFT);
		}

		return $hours;
	}

	/**
	 * Retrieves the minutes in an hour
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function getMinutes()
	{
		$minutes = array();

		for($i = 0; $i < 60; $i += 20) {
			$minutes[] = str_pad($i, 2, '0', STR_PAD_LEFT);;
		}

		return $minutes;
	}

	/**
	 * Retrieves the stored hours
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function getData()
	{
		$result = null;

		// Page has business hours stored
		if (isset($this->value['data']) && $this->value['data']) {
			$result = json_decode($this->value['data']);

			$days = array();

			// For some reason typecasting from an object to an array loss the key values.
			// We need to loop manually here.
			if (isset($result->days)) {

				foreach ($result->days as $day => $hours) {
					$days[$day] = $hours;
				}

				$result->days = $days;
			}
		}

		// User probably did not change this yet
		if (!$result) {
			$result = new stdClass();
			$result->hours_type = 'disabled';
		}

		if (!isset($result->days)) {
			$result->days = array();
		}

		return $result;
	}

	/**
	 * Displays the form for page owner to define permissions
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onRegister(&$post, &$session)
	{
		// Get the posted value if there's any
		$value = !empty($post['stream_moderation']) ? $post['stream_moderation'] : '';

		$data = $this->getData();

		$timeFormat = $this->params->get('time_format');
		$allowMultiple = $this->params->get('multiple', false);


		$days = $this->getDays();
		$hours = $this->getHours();
		$minutes = $this->getMinutes();

		$this->set('data', $data);
		$this->set('hours', $hours);
		$this->set('minutes', $minutes);
		$this->set('days', $days);
		$this->set('timeFormat', $timeFormat);
		$this->set('allowMultiple', $allowMultiple);

		return $this->display();
	}

	/**
	 * Displays the form for page owner to define permissions when page is being edited
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onEdit(&$post, SocialPage &$page, $errors)
	{
		$timeFormat = $this->params->get('time_format');
		$allowMultiple = $this->params->get('multiple', false);
		$data = $this->getData();
		$days = $this->getDays();

		$this->set('data', $data);
		$this->set('days', $days);
		$this->set('timeFormat', $timeFormat);
		$this->set('allowMultiple', $allowMultiple);

		return $this->display();
	}

	/**
	 * Processes the save for new page creation
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onRegisterBeforeSave(&$post, &$page)
	{
		return $this->onBeforeSave($post, $page);
	}

	/**
	 * Processes the save for page editing
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onEditBeforeSave(&$post, SocialPage &$page)
	{
		return $this->onBeforeSave($post, $page);
	}

	/**
	 * Processes the save for page editing
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onBeforeSave(&$post, SocialPage &$page)
	{
		// We need to normalize the data
		$raw = isset($post[$this->inputName]) ? $post[$this->inputName] : '';

		if ($raw) {

			$data = json_decode($raw);

			$result = new stdClass();
			$result->hours_type = $data->hours_type;

			// We only want to process the business operation hours if they have selected a day
			$days = $this->normalize((array)$data, 'days', false);

			$aDays = array();
			$aHours = array();

			$fieldData = array();


			// Process each days
			if ($data->hours_type == 'selected' && $days) {
				$result->days = array();

				foreach ($days as $day) {
					$prop = 'day-' . $day;
					$result->days[$day] = $data->$prop;

					$hourObj = $data->$prop;

					$aDays[] = $day;

					$count = count($hourObj->start_hour);

					$startHours = $hourObj->start_hour[0];
					$endHours = $hourObj->end_hour[0];

					if ($hourObj->start_period[0] == 'pm') {
						$hours = explode(':', $hourObj->start_hour[0]);
						$startHours = (int) $hours[0] + 12 . ':' . $hours[1];
						if ($hours[0] == 12) {
							$startHours = (int) $hours[0] . ':' . $hours[1];
						}
					}

					if ($hourObj->end_period[0] == 'pm') {
						$hours = explode(':', $hourObj->end_hour[0]);
						$endHours = (int) $hours[0] + 12 . ':' . $hours[1];
						if ($hours[0] == 12) {
							$endHours = (int) $hours[0] . ':' . $hours[1];
						}

					}

					$fieldData['start-' . $day ] = str_pad($startHours, 5, '0', STR_PAD_LEFT);
					$fieldData['end-' . $day ] = str_pad($endHours, 5, '0', STR_PAD_LEFT);

					if ($count > 1) {

						unset($fieldData['start-' . $day ]);
						unset($fieldData['end-' . $day ]);

						$oriStartHours = array();
						$oriEndHours = array();

						// here we need to sort the timings
						for ($i = 0; $i < $count; $i++) {

							$startHours = $hourObj->start_hour[$i];
							$endHours = $hourObj->end_hour[$i];

							// temporary store the ori value
							$oriStartHours[$i] = $hourObj->start_hour[$i];
							$oriEndHours[$i] = $hourObj->end_hour[$i];

							if ($hourObj->start_period[$i] == 'pm') {
								$hours = explode(':', $hourObj->start_hour[$i]);
								$startHours = (int) $hours[0] + 12 . ':' . $hours[1];
								if ($hours[0] == 12) {
									$startHours = (int) $hours[0] . ':' . $hours[1];
								}

								$hourObj->start_hour[$i] = $startHours;
							}

							if ($hourObj->end_period[$i] == 'pm') {
								$hours = explode(':', $hourObj->end_hour[$i]);
								$endHours = (int) $hours[0] + 12 . ':' . $hours[1];
								if ($hours[0] == 12) {
									$endHours = (int) $hours[0] . ':' . $hours[1];
								}

								$hourObj->end_hour[$i] = $endHours;
							}
						}

						// sort the array and maintain its index keys so that we can get pair from other array correctly.
						asort($hourObj->start_hour, SORT_NATURAL);

						$aStartHours = array();
						$aStartPeriods = array();
						$aEndHours = array();
						$aEndPeriods = array();

						foreach ($hourObj->start_hour as $idx => $val) {
							// since we are doing sorting here, we need to update all the other arrays as well.
							$aStartHours[] = $oriStartHours[$idx]; // get the original value
							$aStartPeriods[] = $hourObj->start_period[$idx];
							$aEndHours[] = $oriEndHours[$idx]; // get the original value
							$aEndPeriods[] = $hourObj->end_period[$idx];
						}

						$i = 1;
						foreach ($hourObj->start_hour as $idx => $val) {
							$startHours = $hourObj->start_hour[$idx];
							$endHours = $hourObj->end_hour[$idx];

							$endKey = $day . '-' . ($i++);

							$fieldData['start-' . $endKey ] = str_pad($startHours, 5, '0', STR_PAD_LEFT);
							$fieldData['end-' . $endKey ] = str_pad($endHours, 5, '0', STR_PAD_LEFT);
						}

						// reset the values
						$result->days[$day]->start_hour = $aStartHours;
						$result->days[$day]->start_period = $aStartPeriods;
						$result->days[$day]->end_hour = $aEndHours;
						$result->days[$day]->end_period = $aEndPeriods;

					}

					// Get the user timezone
					$timezone = $this->getUserTimezone();
					$fieldData['timezone'] = $timezone;
				}
			}

			// dump($result->days);exit;

			$fieldData['data'] = $result;
			$fieldData['days'] = $aDays;

			$post[$this->inputName] = $fieldData;
		}
	}

	protected function getUserTimezone()
	{
		$tz = ES::user()->getParam('timezone');

		if (empty($tz)) {
			$tz = JFactory::getConfig()->get('offset', 'UTC');
		}

		return $tz;
	}
}
