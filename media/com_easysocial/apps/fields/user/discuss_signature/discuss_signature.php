<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Unauthorized Access');

// Include the fields library
Foundry::import('admin:/includes/fields/dependencies');

class SocialFieldsUserDiscuss_signature extends SocialFieldItem
{
	/**
	 * Determines if EasyDiscuss exists on the site.
	 */
	private $exists = false;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{

		$file = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

		if (JFile::exists($file)) {

			$this->exists = true;
			require_once($file);

			// Load EasyDiscuss language files
			ED::loadlanguages();
		}

		parent::__construct();
	}

	/**
	 * Check if EasyDiscuss is exist
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 *
	 */
	public function attachHeaders()
	{
		ED::loadHeaders();
		$config = ED::config();
	}

	/**
	 * Retrieves EasyDiscuss profile table
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id.
	 * @return	DiscussTableProfile
	 */
	public function getProfile($id)
	{
		$discussProfile = ED::table('Profile');
		$discussProfile->load($id, true, false, true);

		return $discussProfile;
	}

	/**
	 * Validates the field when the user edits their profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onEditValidate(&$post)
	{
		return $this->validate($post);
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onRegisterValidate(&$post, &$registration)
	{
		return $this->validate();
	}

	/**
	 * Validates the field
	 *
	 * @since	1.0
	 * @access	private
	 * @param	Array
	 * @return
	 */
	private function validate($post)
	{
		$value = isset($post[$this->inputName]) ? $post[$this->inputName] : '';

		if ($this->isRequired() && empty($value)) {
			return $this->setError(JText::_('PLG_FIELDS_TEXTAREA_VALIDATION_PLEASE_ENTER_SOME_VALUES'));
		}
	}

	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 */
	public function onRegister(&$post, &$registration)
	{
		// Check if EasyDiscuss exists on the system
		if (!$this->exists) {
			return;
		}

		// Load EasyDiscuss headers
		$this->attachHeaders();

		// Get the value from posted data if it's available.
		$value = isset($post[$this->inputName]) ? $post[$this->inputName] : '';

		// Get any errors for this field.
		$error = $registration->getErrors($this->inputName);

		// Get editor for signature.
		$opt = array('defaults', '');
		$composer = ED::composer($opt);

		// Push to template
		$this->set('composer', $composer);
		$this->set('error', $error);
		$this->set('value', $this->escape($value));

		// Display the output.
		return $this->display();
	}

	/**
	 * Displays the field input for user on edit page
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser	The user object who is editting
	 * @param	Array		The post data in array
	 * @param	Array		The errors in array
	 *
	 */
	public function onEdit(&$post, &$user, $errors)
	{
		// Check if EasyDiscuss exists on the system
		if (!$this->exists) {
			return;
		}

		// Load EasyDiscuss headers
		$this->attachHeaders();

		// Get EasyDiscuss profile object
		$profile = $this->getProfile($user->id);

		$value = isset($post[$this->inputName]) ? $post[$this->inputName] : $profile->signature;

		$error = $this->getError($errors);

		// Get editor for signature.
		$opt = array('defaults', '');
		$composer = ED::composer($opt);

		$this->set('error', $error);
		$this->set('composer', $composer);
		$this->set('value', $this->escape($value));

		return $this->display();
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The html output.
	 *
	 */
	public function onSample()
	{
		if ($this->exists && JFactory::getDocument()->getType() === 'html') {
			$this->attachHeaders();
		}

		$this->set('exists', $this->exists);

		if (!$this->exists) {
			return $this->display('error');
		}

		return $this->display();
	}

	/**
	 * Display the output on user profile page
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string
	 *
	 * @author
	 */
	public function onDisplay($user)
	{
		if (!$this->exists) {
			return;
		}

		// Get EasyDiscuss profile object
		$profile = $this->getProfile($user->id);

		$value = $profile->signature;

		if (ED::config()->get('layout_editor') == 'bbcode') {
			$value = nl2br(ED::parser()->bbcode($value));
		} else {
			$value = trim($value);
		}		

		// Push variables into theme.
		$this->set('value', $value);

		return $this->display();
	}

	/**
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onEditAfterSave(&$data, &$user)
	{
		$state = $this->saveSignature($data, $user->id, $this->inputName);

		// Remove the data from $post to prevent description saving in fields table
		unset($data[$this->inputName]);
	}

	/**
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onRegisterAfterSave(&$data, &$user)
	{
		$state = $this->saveSignature($data, $user->id);

		// Remove the data from $post to prevent description saving in fields table
		unset($data[$this->inputName]);
	}

	/**
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveSignature($data, $userId)
	{
		if (!$this->exists) {
			return;
		}

		$state = true;
		
		if ($userId) {
			// Get the profile object
			$profile = $this->getProfile($userId);

			$profile->signature = $data[$this->inputName];

			$state = $profile->store();
		}

		return $state;
	}
}
