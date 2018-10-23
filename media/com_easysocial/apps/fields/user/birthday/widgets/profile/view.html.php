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

ES::import('fields:/user/datetime/datetime');

class BirthdayFieldWidgetsProfile extends EasySocial
{
	/**
	 * Renders the custom field in profileIntro position
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function profileIntro($key, SocialUser $user, $field)
	{
		// Get the value of the field
		$value = $field->data;

		// If user didn't set their gender, don't need to do anything
		if (!$value) {
			return;
		}

		// Ensure that the user can view this field
		if (!$this->my->canViewField($user, $field->id)) {
			return;
		}

		// Privacy for birthday field is different
		$privacy = $this->my->getPrivacy();

		// Get the params of the custom fields
		$params = $field->getParams();
		$allowViewYear = $privacy->validate('field.birthday.year', $field->id, 'year', $user->id);

		if ($params->get('show_age') && !$allowViewYear) {
			return;
		}

		// Empty value. just return empty string.
		if (is_array($value) && isset($value['date']) && !$value['date']) {
			return;
		}
		
		// We do not want to set a timezone on birthday field
		if (is_array($value) && isset($value['timezone'])) {
			unset($value['timezone']);
		}

		$data = new SocialFieldsUserDateTimeObject($value);
		$date = null;

		if (!empty($data->year) && !empty($data->month) && !empty($data->day)) {
			$date = $data->year . '-' . $data->month . '-' . $data->day;
		}

		if (!$date) {
			return;
		}

		// Display year by default
		$displayYear = true;



		// Compute the age now.
		if ($params->get('show_age')) {
			$value = $this->getAge($date);
		} else {

			$format = $allowViewYear ? 'd M Y' : 'd M';

			switch($params->get('date_format')) {
				case 2:
				case '2':
					$format = $allowViewYear ? 'M d Y' : 'M d';
					break;
				case 3:
				case '3':
					$format = $allowViewYear ? 'Y d M' : 'd M';
					break;
				case 4:
				case '4':
					$format = $allowViewYear ? 'Y M d' : 'M d';
					break;
			}

			$value = ES::date($date, false)->format($format);
		}


		$search = false;

		if ($field->isSearchable()) {
			$date = $data->format('Y-m-d');

			$options = array();
			$options['layout'] = 'advanced';
			$options['criterias[]'] = $field->unique_key . '|' . $field->element;
			$options['operators[]'] = 'between';
			$options['conditions[]'] = $date . ' 00:00:00' . '|' . $date . ' 23:59:59';

			$search = ESR::search($options);
		}

		$theme = ES::themes();
		$theme->set('value', $value);
		$theme->set('params', $params);
		$theme->set('search', $search);

		echo $theme->output('fields/user/birthday/widgets/display');
	}

	/**
	 * Renders the age of the user.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getAge($value)
	{
		$birthDate = new DateTime($value);

		$now = new DateTime();
		$years = floor(($now->format('U') - $birthDate->format('U')) / (60*60*24*365));

		return $years;
	}
}
