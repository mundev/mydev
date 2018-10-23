<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include the fields library
Foundry::import( 'admin:/includes/fields/dependencies' );


/**
 * Field application for Joomla full name.
 *
 * @since	1.0
 * @author	Adelene Tea <adelene@stackideas.com>
 */
class SocialFieldsUserEasyblog_desc extends SocialFieldItem
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
	 *
	 * @author	Adelene Tea <adelene@stackideas.com>
	 */
	public function onRegister( &$post , &$registration )
	{
		if (!$this->exists()) {
			return;
		}

		// Check for errors
		$error		= $registration->getErrors( $this->inputName );

		$value 		= isset( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : '';

		// Set errors.
		$this->set( 'error', $error );
		$this->set( 'value', $this->escape( $value ) );

		return $this->display();
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The post data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Adelene Tea <adelene@stackideas.com>
	 */
	public function onRegisterValidate( &$post )
	{
		if (!$this->exists()) {
			return;
		}

		$desc	 	= !empty( $post[$this->inputName] ) ? $post[$this->inputName] : '';

		return $this->validateField( $desc );
	}

	/**
	 * Executes after a user's registration is saved.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The post data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Adelene Tea <adelene@stackideas.com>
	 */
	public function onRegisterAfterSave( &$post, &$user )
	{
		if (!$this->exists()) {
			return;
		}

		// We do not need to validate against reconfirm here
		$desc	 	= !empty( $post[$this->inputName] ) ? $post[$this->inputName] : '';

		if (!is_null($this->userExist($user->id))) {

			$this->saveDescription($user->id, $desc);

			return true;
		}

		$table 	= EB::table('Profile');
		$table->load($user->id);
		$table->set('description', $desc);
		$table->store();
		// Remove the data from $post to prevent description saving in fields table
		unset( $post[$this->inputName] );

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
     *
     * @author  Nik Faris <nikfaris@stackideas.com>
     */
    public function userExist($userid)
    {
        if (!$this->exists()) {
            return;
        }
        $db = Foundry::db();

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
     * @param   int     The user's id
     * @param   String  The desciption text
     * @return  bool    Determines if the system should proceed or throw errors.
     *
     * @author  Mohd Yasser Ibz <mohdyasser@stackideas.com>
     */
    public function saveDescription($id, $description)
    {
        if (!$this->exists()) {
            return;
        }

        $db = Foundry::db();
        $sql = $db->sql();

        $query = "update `#__easyblog_users` set " . $db->nameQuote('description') ."=". $db->Quote($description) . " where " . $db->nameQuote('id') . "=" . $db->Quote($id);
        //echo($query);exit;
        $sql->raw( $query );

        $db->setQuery( $sql );
        $state = $db->query();

        return true;
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
	public function onEdit( &$post, &$user, $errors )
	{
		if (!$this->exists()) {
			return;
		}
		$blogProfile	= $this->getEasyBlogProfile( $user );

		$value			= !empty( $post[$this->inputName] ) ? $post[$this->inputName] : $blogProfile->description;

		$error = $this->getError( $errors );

		$this->set( 'value', $this->escape( $value ) );
		$this->set( 'error', $error );

		return $this->display();
	}

	/**
	 * Validates the field when the user edits their profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Adelene Tea <adelene@stackideas.com>
	 */
	public function onEditValidate( &$post )
	{
		if (!$this->exists()) {
			return;
		}
		$desc	 	= !empty( $post[$this->inputName] ) ? $post[$this->inputName] : '';

		return $this->validateField( $desc );
	}

	/**
	 * Executes before a user's edit is saved.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The post data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Adelene Tea <adelene@stackideas.com>
	 */
	public function onEditAfterSave( &$post, &$user )
	{
		if (!$this->exists()) {
			return;
		}

		// We do not need to validate against reconfirm here
		$desc	 	= !empty( $post[$this->inputName] ) ? $post[$this->inputName] : '';

		$this->saveDescription($user->id, $desc);

		// Remove the data from $post to prevent description saving in fields table
		unset( $post[$this->inputName] );

		return true;
	}

	/**
	 * Responsible to output the html codes that is displayed to
	 * a user when their profile is viewed.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onDisplay( $user )
	{
		if (!$this->exists()) {
			return;
		}

		$profile = $this->getEasyBlogProfile( $user );

		// Push variables into theme.
		$this->set( 'value'	, $this->escape( $profile->description ) );

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
	private function validateField( $desc )
	{
		if (!$this->exists()) {
			return;
		}

		// Verify that the field are not empty
		if( empty( $desc ) && $this->isRequired() )
		{
			$this->setError( JText::_( 'PLG_FIELDS_JOOMLA_EASYBLOG_DESC_EMPTY_DESC' ) );

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
	private function getEasyBlogProfile( $user )
	{
		if (!$this->exists()) {
			return;
		}

		$blogProfile	= EB::table('Profile');
		$blogProfile->load( $user->id );

		return $blogProfile;
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @return	string	The html output.
	 *
	 * @author	Adelene Tea <adelene@stackideas.com>
	 */
	public function onSample()
	{
		return $this->display();
	}
}
