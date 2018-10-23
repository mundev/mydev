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

// Include the fields library
ES::import('admin:/includes/fields/dependencies');

class SocialFieldsUserEasyblog_biography extends SocialFieldItem
{
	public function __construct()
	{
		parent::__construct();
	}

	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array		The post data.
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 */
	public function onRegister(&$post, &$registration)
	{
		if (!$this->exists()) {
			return;
		}

		// Check for errors
		$error = $registration->getErrors($this->inputName);

		$value = isset($post[$this->inputName]) ? $post[$this->inputName] : '';

		// Get joomla default editor
		$editor = $this->getEditor();		

		// Set errors.
		$this->set('error', $error);
		$this->set('value', $this->escape($value));
		$this->set('editor', $editor);

		return $this->display();
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The post data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 */
	public function onRegisterValidate(&$post)
	{
		if (!$this->exists()) {
			return;
		}

		$bio = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->validateField($bio);
	}

	/**
	 * Executes after a user's registration is saved.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The post data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 */
	public function onRegisterAfterSave(&$post, &$user)
	{
		if (!$this->exists()) {
			return;
		}

		$profile = $this->getEasyBlogProfile($user);

		$value = $this->escape($profile->biography);
		$bio = $this->input->get($this->inputName, $value, 'raw');

		if (!is_null($this->userExist($user->id))) {
			$this->saveBiography($user->id, $bio);
			return true;
		}

		$table = EB::table('Profile');
		$table->load($user->id);
		$table->set('biography', $bio);
		$table->store();

		// Remove the data from $post to prevent description saving in fields table
		unset($post[$this->inputName]);

		return true;
	}

	/**
     * Check if the user is already in Easyblog User table.
     *
     * @since   1.0
     * @access  public
     * @param   int     The user's id
     * @param   String  The desciption text
     * @return  bool    Determines if the system should proceed or throw errors.
     */
    public function userExist($userid)
    {
        if (!$this->exists()) {
            return;
        }
        $db = ES::db();

        $query = 'SELECT * FROM ' . $db->quoteName('#__easyblog_users') . ' '
				. 'WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($userid);

		$db->setQuery($query);
		$result	= $db->loadObject();

        return $result;
    }

	/**
	 * Responsible to output the html codes that is displayed to
	 * a user when they edit their profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser		The user that is being edited.
	 * @return
	 */
	public function onEdit(&$post, &$user, $errors)
	{
		if (!$this->exists()) {
			return;
		}

		$profile = $this->getEasyBlogProfile($user);

		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $profile->biography;

		// Get errors
		$error = $this->getError($errors);

		// Get joomla default editor
		$editor = $this->getEditor();

		$this->set('value', $value);
		$this->set('error', $error);
		$this->set('editor', $editor);

		return $this->display();
	}

	/**
	 * Get default editor for biography field
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getEditor()
	{
		// Get eb config
		$ebConfig = EB::config();

		$defaultEditor = $this->params->get('editor');

		$editor = JFactory::getEditor($defaultEditor);

		return $editor;
	}

	/**
	 * Validates the field when the user edits their profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 */
	public function onEditValidate(&$post)
	{
		if (!$this->exists()) {
			return;
		}

		$bio = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->validateField($bio);
	}

	/**
	 * Executes before a user's edit is saved.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The post data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 */
	public function onEditAfterSave(&$post, &$user)
	{
		if (!$this->exists()) {
			return;
		}

		$profile = $this->getEasyBlogProfile($user);

		$value = $this->escape($profile->biography);		
		$bio = $this->input->get($this->inputName, $value, 'raw');

		// Check if the current user is it exist in easyblog users table 
		if (!is_null($this->userExist($user->id))) {
			
			// Store the biography field
			$this->saveBiography($user->id, $bio);

			return true;
		}

		$table = EB::table('Profile');
		$table->load($user->id);
		$table->set('biography', $bio);
		$table->store();

		// Remove the data from $post to prevent description saving in fields table
		unset($post[$this->inputName]);

		return true;
	}

	/**
	 * Allows caller to save the user's biography
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int 	The user's id.
	 * @param	string	The biography text
	 * @return
	 */
	private function saveBiography($id, $biography)
	{
		if (!$this->exists()) {
			return;
		}

		$db = ES::db();
		$sql = $db->sql();

		$query = "update `#__easyblog_users` set " . $db->nameQuote('biography') ."=". $db->Quote($biography) . " where " . $db->nameQuote('id') . "=" . $db->Quote($id);
		$sql->raw($query);

		$db->setQuery($sql);
		$state = $db->query();

		return true;
	}

	/**
	 * Responsible to output the html codes that is displayed to
	 * a user when their profile is viewed.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onDisplay($user)
	{
		if (!$this->exists()) {
			return;
		}

		$profile = $this->getEasyBlogProfile($user);

		// Push variables into theme.
		$this->set('value', $profile->biography);

		return $this->display();
	}

	/**
	 * Validates the custom field
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function validateField($bio)
	{
		if (!$this->exists()) {
			return;
		}

		// Verify that the field are not empty
		if(empty($bio) && $this->isRequired()) {
			$this->setError(JText::_('PLG_FIELDS_USER_EASYBLOG_BIO_EMPTY'));
			return false;
		}

		return true;
	}

	/**
	 * Returns the profile object in EasyBlog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialUser	The user's object
	 * @return	EasyBlogProfileTable
	 */
	private function getEasyBlogProfile($user)
	{
		if (!$this->exists()) {
			return;
		}

		$blogProfile = EB::table('Profile');
		$blogProfile->load($user->id);

		return $blogProfile;
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @return	string	The html output.
	 */
	public function onSample()
	{
		return $this->display();
	}

	/**
	 * Checks if this field is filled in.
	 *
	 * @since  2.0
	 * @access public
	 * @param  array
	 * @param  SocialUser	$user	The user being checked.
	 */
	public function onProfileCompleteCheck($user)
	{
		if (!FD::config()->get('user.completeprofile.strict') && !$this->isRequired()) {
			return true;
		}

		// Get easyblog profile
		$profile = $this->getEasyBlogProfile($user);

		// Get the biography
		$biography = $profile->biography;

		return !empty($biography);
	}
}
