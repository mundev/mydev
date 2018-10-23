<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include the fields library
ES::import('admin:/includes/fields/dependencies');

/**
 * Field application for Joomla full name.
 *
 * @since	1.0
 * @author	Adelene Tea <adelene@stackideas.com>
 */
class SocialFieldsUserEasyblog_permalink extends SocialFieldItem
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
	 */
	public function onRegister(&$post, &$registration)
	{
		if (!$this->exists()) {
			return;
		}

		// Check for errors
		$error = $registration->getErrors($this->inputName);
		$value = isset($post[$this->inputName]) ? $post[$this->inputName] : '';

		// Set errors.
		$this->set('userid', null);
		$this->set('error', $error);
		$this->set('value', $value);

		return $this->display();
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onRegisterValidate(&$post)
	{
		if (!$this->exists()) {
			return;
		}

		$permalink = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->validateField($permalink);
	}

	/**
	 * Executes after a user's registration is saved.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onRegisterAfterSave(&$post, &$user)
	{
		if (!$this->exists()) {
			return;
		}

		// We do not need to validate against reconfirm here
		$permalink = !empty( $post[$this->inputName]) ? $post[$this->inputName] : '';

		if (!is_null($this->userExist($user->id))) {

			$this->savePermalink($user->id, $permalink);

			return true;
		}

		$table = EB::table('Profile');
		$table->load($user->id);
		$table->set('permalink', $permalink);
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
     * Executes after a user's registration is saved.
     *
     * @since   1.0
     * @access  public
     */
    public function savePermalink($id, $permalink)
    {
        if (!$this->exists()) {
            return;
        }

        // filter those invalid value
        if ($permalink) {
			$permalink = JFilterOutput::stringURLSafe($permalink);
        }

        $db = ES::db();
        $sql = $db->sql();

        $query = "update `#__easyblog_users` set " . $db->nameQuote('permalink') ."=". $db->Quote($permalink) . " where " . $db->nameQuote('id') . "=" . $db->Quote($id);

        $sql->raw($query);

        $db->setQuery($sql);
        $state = $db->query();

        return true;
    }

	/**
	 * Responsible to output the html codes that is displayed to
	 * a user when they edit their profile.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onEdit( &$post, &$user, $errors )
	{
		if (!$this->exists()) {
			return;
		}

		$blogProfile = $this->getEasyBlogProfile($user);

		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $blogProfile->permalink;

		if (empty($value)) {
			$value = $user->username;
		}

		$error = $this->getError($errors);

		$this->set('userid', $user->id);
		$this->set('value', $value);
		$this->set('error', $error);

		return $this->display();
	}

	/**
	 * Validates the field when the user edits their profile.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onEditValidate(&$post)
	{
		if (!$this->exists()) {
			return;
		}

		$permalink = !empty( $post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->validateField($permalink);
	}

	/**
	 * Executes before a user's edit is saved.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onEditAfterSave(&$post, &$user)
	{
		if (!$this->exists()) {
			return;
		}

		// We do not need to validate against reconfirm here
		$permalink = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		$this->savePermalink($user->id, $permalink);

		// Remove the data from $post to prevent description saving in fields table
		unset($post[$this->inputName]);

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
		$this->set('blogProfile', $profile);
		$this->set('value', $profile->permalink);

		return $this->display();
	}

	/**
	 * Validates the custom field
	 *
	 * @since	1.0
	 * @access	public
	 */
	private function validateField($permalink)
	{
		if (!$this->exists()) {
			return;
		}

		// Verify that the field are not empty
		if (empty($permalink) && $this->isRequired()) {
			$this->setError( JText::_('PLG_FIELDS_JOOMLA_EASYBLOG_PERMALINK_EMPTY_PERMALINK'));

			return false;
		}

		return true;
	}

	/**
	 * Returns the profile object in EasyBlog
	 *
	 * @since	1.0
	 * @access	public
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
	 */
	public function onSample()
	{
		return $this->display();
	}
}
