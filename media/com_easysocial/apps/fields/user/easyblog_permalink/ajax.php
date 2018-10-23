<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include dependencies
ES::import('admin:/includes/fields/dependencies');
ES::import('fields:/user/permalink/helper');

class SocialFieldsUserEasyBlog_permalink extends SocialFieldItem
{
	/**
	 * Ensures that the permalink is valid
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isValid()
	{
		// // Get the user's id
		$id = $this->input->get('userid', 0, 'int');
		$permalink = $this->input->get('permalink', '', 'default');

		// Check if the field is required
		if (!$this->field->isRequired() && !$permalink) {
			return true;
		}

		$user = EB::user($id);

		// Ensure that the permalink doesn't exist
		$model = EB::model('Users');
		$exists = $model->permalinkExists($permalink, $user->id);
		if ($exists) {
			return $this->ajax->reject(JText::_('PLG_FIELDS_EASYBLOG_PERMALINK_EXISTS'));
		}

		$message = JText::_('PLG_FIELDS_EASYBLOG_PERMALINK_AVAILABLE');
		return $this->ajax->resolve($message);
	}
}
