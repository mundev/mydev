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

class GenderFieldWidgetsProfile extends EasySocial
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

		if (!$this->my->canViewField($user, $field->id)) {
			return;
		}

		$theme = ES::themes();

		$search = false;

		if ($field->isSearchable()) {
			$options = array();
			$options['layout'] = 'advanced';
			$options['criterias[]'] = $field->unique_key . '|' . $field->element;
			$options['operators[]'] = 'equal';
			$options['conditions[]'] = $value;

			$search = ESR::search($options);
		}

		$theme->set('value', $value);
		$theme->set('params', $field->getParams());
		$theme->set('search', $search);

		echo $theme->output('fields/user/gender/widgets/display');
	}
}
