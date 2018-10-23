<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerProfile extends EasyBlogController
{
	/**
	 * Saves a user profile
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// Check for request forgeries
		EB::checkToken();

		// Require user to be logged in
		EB::requireLogin();

		// Get the post data here
		$post = $this->input->getArray('post');

		// Since adsense codes may contain html codes
		$post['adsense_code'] = $this->input->get('adsense_code', '', 'raw');

		// Prepare the redirection url
		$redirect = EB::_('index.php?option=com_easyblog&view=dashboard&layout=profile', false);

		$post['description'] = $this->input->get('description', '', 'raw');
		$post['biography'] = $this->input->get('biography', '', 'raw');

		// Trim data
		array_walk($post, array($this, '_trim'));

		if (!$this->validateProfile($post)) {
			return $this->app->redirect($redirect);
		}

		$this->my->name = $post['fullname'];
		$this->my->save();

		// Determines if we should save the user's params.
		if ($this->config->get('main_joomlauserparams')) {
			$email = $post['email'];
			$password = $post['password'];
			$password2 = $post['password2'];

			if (EBString::strlen($password) || EBString::strlen($password2)) {

				if ($password != $password2) {
					EB::info()->set(JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_PASSWORD_ERROR'), 'error');

					return $this->app->redirect($redirect);
				}
			}

			// Store Joomla info
			$user 	= JFactory::getUser();
			$data 	= array('email' => $email, 'password' => $password, 'password2' => $password2);

			// Bind data
			$user->bind($data);

			$state 	= $user->save();

			if (!$state) {
				EB::info()->set($user->getError(), 'error');

				return $this->app->redirect($redirect);
			}

			$session = JFactory::getSession();
			$session->set('user', $user);

			$table = JTable::getInstance('Session');
			$table->load($session->getId());
			$table->username = $user->get('username');
			$table->store();
		}

		// Set the permalink
		$post['permalink'] = isset($post['user_permalink']) ? $post['user_permalink'] : '';

		// filter those invalid value
		if ($post['permalink']) {
			$post['permalink'] = JFilterOutput::stringURLSafe($post['permalink']);
		}

		unset($post['user_permalink']);

		// Get users model
		$model = EB::model('Users');

		// Ensure that the permalink doesn't exist
		if ($model->permalinkExists($post['permalink'], $this->my->id)) {
			EB::info()->set(JText::_( 'COM_EASYBLOG_DASHBOARD_ACCOUNT_PERMALINK_EXISTS'), 'error');

			return $this->app->redirect($redirect);
		}

		// Load up EasyBlog's profile
		$profile = EB::user($this->my->id);
		$profile->bind($post);

		// Bind Feedburner data
		$profile->bindFeedburner($post, $this->acl);

		// Bind oauth settings
		$profile->bindOauth($post, $this->acl);

		// Bind adsense settings
		$profile->bindAdsense($post, $this->acl);

		// Bind avatar
		$avatar = $this->input->files->get('avatar', '');

		// Save avatar
		if (isset($avatar['tmp_name']) && !empty($avatar['tmp_name'])) {
			$profile->bindAvatar($avatar, $this->acl);
		}

		$acl = EB::acl();

		//save meta
		if ($acl->get('add_entry')) {
			//meta post info
			$metaId = $this->input->get('metaid', 0, 'int');
			$metapos = array();

			$metapost['keywords'] = $this->input->get('metakeywords', '', 'raw');
			$metapost['description'] = $this->input->get('metadescription', '', 'raw');
			$metapost['content_id'] = $this->my->id;
			$metapost['type'] = META_TYPE_BLOGGER;

			$meta = EB::table('Meta');
			$meta->load($metaId);
			$meta->bind($metapost);
			$meta->store();
		}

		//save params
		$userparams	= EB::registry();
		$userparams->set( 'theme', $post['theme'] );

		// Save Facebook profile url
		if (isset($post['facebook_profile_url'])) {
			$userparams->set('facebook_profile_url', $post['facebook_profile_url']);
		}

		$profile->params = $userparams->toString();

		// If user is allowed to save their settings
		if ($this->config->get('main_joomlauserparams')) {
			$this->my->save(true);
		}

		$state 	= $profile->store();

		if (!$state) {
			EB::info()->set(JText::_('COM_EASYBLOG_DASHBOARD_PROFILE_UPDATE_FAILED'), 'error');

			return $this->app->redirect($redirect);
		}

		EB::info()->set(JText::_('COM_EASYBLOG_DASHBOARD_PROFILE_UPDATE_SUCCESS'), 'success');
		return $this->app->redirect($redirect);
	}

	public function _trim(&$text)
	{
		$text = EBString::trim($text);
	}

	/**
	 * Performs profile validation
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function validateProfile($post)
	{
		$valid = true;

		if (EBString::strlen($post['fullname']) == 0) {
			$message = JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_REALNAME_EMPTY');
			$valid	= false;
		}

		if (EBString::strlen($post['nickname']) == 0) {
			$message = JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_NICKNAME_EMPTY');
			$valid	= false;
		}

		if (!$valid) {
			EB::info()->set($message, 'error');
		}

		return $valid;
	}

	/**
	 * Allow current user to remove their own profile picture.
	 *
	 */
	public function removePicture()
	{
		$mainframe = JFactory::getApplication();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if (!$this->config->get('layout_avatar') || !$acl->get('upload_avatar')) {
			EB::info()->set( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_DELETE_PROFILE_PICTURE' ) , 'error' );
			$mainframe->redirect( EBR::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
			$mainframe->close();
		}

		$profile = EB::user($my->id);

		$avatar_config_path = $this->config->get('main_avatarpath');
		$avatar_config_path = rtrim($avatar_config_path, '/');
		$avatar_config_path = str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);
		$path				= JPATH_ROOT . DIRECTORY_SEPARATOR . $avatar_config_path . DIRECTORY_SEPARATOR . $profile->avatar;

		if( !JFile::delete( $path ) )
		{
			EB::info()->set( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_DELETE_PROFILE_PICTURE' ) , 'error' );
			$mainframe->redirect( EBR::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
			$mainframe->close();
		}

		// @rule: Update avatar in database
		$profile->avatar	= '';
		$profile->store();

		EB::info()->set( JText::_( 'COM_EASYBLOG_PROFILE_PICTURE_REMOVED' ) );
		$mainframe->redirect( EBR::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
		$mainframe->close();
	}

	/**
	 * Allow user to remove personal info from the site (GDPR Compliance)
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function deleteinforequest()
	{
		$config = EB::config();
		$jConfig = EB::jconfig();

		if (!$config->get('gdpr_enabled', false)) {
			return;
		}

		$data = $this->input->get('data');
		$email = base64_decode($data);

		$senderEmail = $config->get('notification_from_email', $jConfig->get('mailfrom'));
		$senderName = $config->get('notification_from_name', $jConfig->get('fromname'));
		$sender = array($senderEmail, $senderName);

		// Get the user id by email
		$model = EB::model('Users');
		$uid = $model->getUserIdByEmail($email);

		// If no uid means this is request from guest
		if (!$uid) {
			$uid = 0;
		}

		$key = $uid . '|' . $email;
		$confirmLink = EBR::getRoutedURL('index.php?option=com_easyblog&view=download&layout=deleteinfo&key=' . base64_encode($key), false, true);

		$data = array('confirmDeleteInfoLink' => $confirmLink, 'uid' => $uid);

		$title = JText::_('COM_EB_MAIL_TEMPLATE_DELETEINFO_CONFIRMATION_TITLE');
		$body = EB::notification()->getTemplateContents('delete.info.verify', $data);

		// Generate a verification email and directly send to user
		$mailer = JFactory::getMailer();
		$state = $mailer->sendMail($senderEmail, $senderName, $email, $title, $body, true);

		if ($state) {
			EB::info()->set(JText::sprintf('COM_EB_DELETE_USER_DETAILS_VERIFICATION_SENT', $email), 'success');
			return $this->app->redirect('index.php?option=com_easyblog');
		}
	}

	/**
	 * Allow caller to delete user details
	 *
	 * @since   5.2.0
	 * @access  public
	 */
	public function deleteInfo()
	{
		// Check for request forgeries
		EB::checkToken();

		if (! $this->config->get('gdpr_enabled')) {
			return EB::exception(JText::_('COM_EB_GDPR_DOWNLOAD_DISABLED'), EASYBLOG_MSG_INFO);
		}

		// Get the composite keys
		$data = $this->input->get('key', '', 'raw');
		$password = $this->input->get('password', '', 'raw');
		$redirect = EB::_('index.php?option=com_easyblog&view=latest', false);

		if (!$data) {
			return JError::raiseError(404, JText::_('COM_EB_INVALID_TOKEN_PROVIDED'));
		}

		$keys = base64_decode($data);
		$key = explode('|', $keys);

		$userId = $key[0];
		$email = $key[1];

		// If this is registered user
		// We need to authenticate this action
		if ($userId) {
			$user = JFactory::getUser($userId);

			if (!$password) {
				return JError::raiseError(404, JText::_('COM_EB_INVALID_TOKEN_PROVIDED'));
			}

			// authenticate user
			$username = $user->username;

			// Populate the data array:
			$data = array();
			$data['username'] = $username;
			$data['password'] = $password;
			$data['secretkey'] = '';

			// Get the log in options.
			$options = array();
			$options['remember'] = false;
			$options['return'] = '';
			$options['silent'] = true;

			// Get the log in credentials.
			$credentials = array();
			$credentials['username']  = $data['username'];
			$credentials['password']  = $data['password'];
			$credentials['secretkey'] = $data['secretkey'];

			// perform user login here.
			$state = $this->app->login($credentials, $options);

			if ($state) {
				$deleteState = EB::gdpr()->removeUserDetails($user);
			}
		} else {
			// Validate the email
			$isValid = EB::string()->isValidEmail($email);

			// We need to ensure that this email is not belong to any registered user on the site
			$model = EB::model('Users');
			$uid = $model->getUserIdByEmail($email);

			if (!$uid) {
				$deleteState = EB::gdpr()->removeGuestDetails($email);
			}
		}

		$redirect = EB::_('index.php?option=com_easyblog', false);

		if (!$deleteState) {
			EB::info()->set(JText::_('COM_EB_DELETE_USER_DETAILS_FAILED'), 'error');
			return $this->app->redirect($redirect);
		}

		EB::info()->set(JText::_('COM_EB_DELETE_USER_DETAILS_SUCCESS'), 'success');
		return $this->app->redirect($redirect);

	}
}
