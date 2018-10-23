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

ES::import('admin:/includes/fields/dependencies');

require_once(dirname(__FILE__) . '/helper.php');

class SocialFieldsUserAvatar extends SocialFieldItem
{
	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onRegister(&$post, &$registration)
	{
		$avatars = array();
		$config = ES::config();

		// Get the default avatar
		$systemAvatar = ES::getDefaultAvatar($this->group, SOCIAL_AVATAR_SQUARE);

		$defaultAvatarId = '';

		$avatarModel = ES::model('Avatars');

		// Load the default avatars
		if (isset($registration->profile_id)) {
			$avatars = $avatarModel->getDefaultAvatars($registration->profile_id);
		}

		// Need to double check again if there profile id is 0 and do not have detect any avatars
		if (empty($avatars) && (isset($registration->profile_id) && !$registration->profile_id)) {
			
			$profileId = $this->config->get('registrations.mini.profile', 'default');

			if ($profileId === 'default') {
				$model = ES::model('Profiles');
				$profileId = $model->getDefaultProfile()->id;
			}
			
			$avatars = $avatarModel->getDefaultAvatars($profileId);
		}

		foreach($avatars as $avatar) {
			if ($avatar->default) {
				$systemAvatar = $avatar->getSource(SOCIAL_AVATAR_SQUARE);
				$defaultAvatarId = $avatar->id;
			}
		}

		// Set errors
		$error = $registration->getErrors($this->inputName);

		// Set the blank avatar
		$this->set('defaultAvatarId', $defaultAvatarId);
		$this->set('imageSource', $systemAvatar);
		$this->set('avatars', $avatars);
		$this->set('error', $error);
		$this->set('hasAvatar', false);
		$this->set('systemAvatar', $systemAvatar);

		// Display the output.
		return $this->display();
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onRegisterValidate(&$post, &$registration)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		$state 	= $this->validate($value);

		return $state;
	}

	/**
	 * Once a user registration is completed, the field should automatically
	 * move the temporary avatars into the user's folder if required.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onRegisterAfterSave(&$post, &$user)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		// If the user did not select an avatar, we should pre-select it for them.
		if ($value) {

			$deleteTmpFolder = true;

			// If the obj is event, we cant delete the tmp folder. 
			// Because there is possibility this event has recurring event
			if ($user->getType() == 'event') {
				$deleteTmpFolder = false;
			}

			$this->createAvatar($value, $user->id, false, true, true, $deleteTmpFolder);
		}

		$default = false;

		// If the user did not select a default avatar, or did not upload an avatar, check if there's a default avatar selected for them.
		if (isset($user->profile_id)) {
			$default = ES::model('Avatars')->getDefaultAvatars($user->profile_id, SOCIAL_TYPE_PROFILES, true);
		}

		if (!$value && $default) {

			$default = $default[0];

			$tmp = new stdClass();
			$tmp->type = 'gallery';
			$tmp->source = $default->id;
			$tmp->data = '';
			$tmp = ES::json()->encode($tmp);

			$this->createAvatar($tmp, $user->id, false, true, true);
		}

		unset($post[$this->inputName]);
	}


	/**
	 * Processes before the user account is created when user signs in with oauth.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onRegisterOAuthAfterSave(&$data, &$oauthClient, SocialUser &$user, $import)
	{
		if (!$import) {
			return;
		}

		// Let's see if avatarUrl is provided.
		if (!isset($data['avatar']) || empty($data['avatar'])) {
			return;
		}

		$avatarUrl	 		= $data['avatar'];

		// Store the avatar internally.
		$key 				= md5($data['oauth_id'] . $data['username']);
		$tmpAvatarPath 		= SOCIAL_MEDIA . '/tmp/' . $key;
		$tmpAvatarFile 		= $tmpAvatarPath . '/' . $key;

		jimport('joomla.filesystem.folder');

		if (!JFolder::exists($tmpAvatarPath)) {
			$state 	= JFolder::create($tmpAvatarPath);
		}

		$connector 	= ES::get('Connector');
		$connector->addUrl($avatarUrl);
		$connector->connect();

		$contents 	= $connector->getResult($avatarUrl);

		jimport('joomla.filesystem.file');

		if (!JFile::write($tmpAvatarFile, $contents)) {
			return;
		}

		$image = ES::image();
		$image->load($tmpAvatarFile);

		$avatar		= ES::avatar($image, $user->id, SOCIAL_TYPE_USER);

		// Check if there's a profile photos album that already exists.
		$albumModel	= ES::model('Albums');

		// Retrieve the user's default album
		$album		= $albumModel->getDefaultAlbum($user->id, SOCIAL_TYPE_USER, SOCIAL_ALBUM_PROFILE_PHOTOS);

		$photo 				= ES::table('Photo');
		$photo->uid 		= $user->id;
		$photo->user_id 	= $user->id;
		$photo->type 		= SOCIAL_TYPE_USER;
		$photo->album_id 	= $album->id;
		$photo->title 		= $user->getName();
		$photo->caption 	= JText::_('COM_EASYSOCIAL_PHOTO_IMPORTED_FROM_FACEBOOK');
		$photo->ordering	= 0;

		// We need to set the photo state to "SOCIAL_PHOTOS_STATE_TMP"
		$photo->state 		= SOCIAL_PHOTOS_STATE_TMP;

		// Try to store the photo first
		$state 		= $photo->store();

		if (!$state) {
			$this->setError(JText::_('PLG_FIELDS_AVATAR_ERROR_CREATING_PHOTO_OBJECT'));
			return false;
		}

		// Push all the ordering of the photo down
		$photosModel = ES::model('photos');
		$photosModel->pushPhotosOrdering($album->id, $photo->id);

		// If album doesn't have a cover, set the current photo as the cover.
		if (!$album->hasCover()) {
			$album->cover_id 	= $photo->id;

			// Store the album
			$album->store();
		}

		// Get the photos library
		$photoLib 	= ES::get('Photos', $image);
		$storage   = $photoLib->getStoragePath($album->id, $photo->id);
		$paths 		= $photoLib->create($storage);

		// Create metadata about the photos
		foreach ($paths as $type => $fileName) {
			$meta 				= ES::table('PhotoMeta');
			$meta->photo_id		= $photo->id;
			$meta->group 		= SOCIAL_PHOTOS_META_PATH;
			$meta->property 	= $type;
			$meta->value		= $storage . '/' . $fileName;

			$meta->store();
		}

		// Synchronize Indexer
		$indexer 	= ES::get('Indexer');
		$template	= $indexer->getTemplate();
		$template->setContent($photo->title, $photo->caption);

		// $url 	= FRoute::photos(array('layout' => 'item', 'id' => $photo->getAlias()));
		$url 	= $photo->getPermalink();
		$url 	= '/' . ltrim($url, '/');
		$url 	= str_replace('/administrator/', '/', $url);

		$template->setSource($photo->id, SOCIAL_INDEXER_TYPE_PHOTOS, $photo->uid, $url);
		$template->setThumbnail($photo->getSource('thumbnail'));

		$indexer->index($template);

		$options = array();

		if ($user->state == SOCIAL_USER_STATE_PENDING) {
			$options['addstream'] = false;
		}

		// Create the avatars now
		$avatar->store($photo, $options, true);

		// Once we are done creating the avatar, delete the temporary folder.
		$state		= JFolder::delete($tmpAvatarPath);
	}

	/**
	 * Displays the field input for user when they edit their account.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onEdit(&$post, &$user, $errors)
	{
		$avatars = array();
		$defaultAvatarId = '';

		$systemAvatar = ES::getDefaultAvatar($this->group, SOCIAL_AVATAR_SQUARE);

		// Load the default avatars
		if (isset($user->profile_id)) {
			$model = ES::model('Avatars');
			$avatars = $model->getDefaultAvatars($user->profile_id);

			foreach($avatars as $avatar) {
				if ($avatar->default) {
					$systemAvatar = $avatar->getSource(SOCIAL_AVATAR_SQUARE);
					$defaultAvatarId = $avatar->id;
				}
			}
		}

		$imageSource = $user->hasAvatar() ? $user->getAvatar(SOCIAL_AVATAR_SQUARE) : '';

		// Set errors
		$error = $this->getError($errors);

		// Set the blank avatar
		$this->set('group', $this->group);
		$this->set('defaultAvatarId', $defaultAvatarId);
		$this->set('avatars', $avatars);
		$this->set('imageSource', $imageSource);
		$this->set('error', $error);
		$this->set('hasAvatar', $user->hasAvatar());
		$this->set('systemAvatar', $systemAvatar);

		// Display the output.
		return $this->display();
	}

	/**
	 * Performs validation checks when a user edits their profile
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onAdminEditValidate(&$post, &$user)
	{
		return true;
	}

	/**
	 * Performs validation checks when a user edits their profile
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onEditValidate(&$post, &$user)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		$state 	= $this->validate($value, $user);

		return $state;
	}

	/**
	 * Once a user edit is completed, the field should automatically
	 * move the temporary avatars into the user's folder if required.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onEditAfterSave(&$post, &$user)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		if (!empty($value)) {

			$deleteTmpFolder = true;

			// If the obj is event, we cant delete the tmp folder. 
			// Because there is possibility this event has recurring event
			if ($user->getType() == 'event') {
				$deleteTmpFolder = false;
			}

			$this->createAvatar($value, $user->id, true, empty($post['applyRecurring']), false, $deleteTmpFolder);
		}

		unset($post[$this->inputName]);
	}

	/**
	 * Performs validation checks when a user edits their profile
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function validate($value, $user = null)
	{
		if ((empty($user) || !$user->hasAvatar()) && $this->isRequired() && empty($value)) {
			$this->setError(JText::_('PLG_FIELDS_AVATAR_VALIDATION_EMPTY_PROFILE_PICTURE'));

			return false;
		}

		if (!empty($value)) {
			$value = ES::json()->decode($value);
			$sourceValue = isset($value->source) ? $value->source : '';

			// Trim of excess white space
			$sourceValue = trim($sourceValue);

			if ((empty($user) || !$user->hasAvatar()) && $this->isRequired() && empty($sourceValue)) {
				$this->setError(JText::_('PLG_FIELDS_AVATAR_VALIDATION_EMPTY_PROFILE_PICTURE'));

				return false;
			}
		}

		return true;
	}

	public function createAvatar($value, $uid, $createStream = true, $deleteImage = true, $isNewlyRegistered = false, $deleteTmpFolder = true)
	{
		$value = ES::makeObject($value);

		if (!empty($value->data)) {
			$value->data = ES::makeObject($value->data);
		}

		if ($value->type === 'remove') {
			$table = ES::table('avatar');
			$state = $table->load(array('uid' => $uid, 'type' => $this->group));

			if ($state) {
				$table->delete();

				if ($this->group == SOCIAL_TYPE_USER) {

					$user = ES::user($uid);

					// Prepare the dispatcher
					ES::apps()->load(SOCIAL_TYPE_USER);
					$dispatcher = ES::dispatcher();
					$args = array(&$user, &$table);

					// @trigger: onUserAvatarRemove
					$dispatcher->trigger(SOCIAL_TYPE_USER, 'onUserAvatarRemove', $args);
				} 				
			}

			return true;
		}

		if ($value->type === 'gallery') {
			$table = ES::table('avatar');
			$state = $table->load(array('uid' => $uid, 'type' => $this->group));

			if (!$state) {
				$table->uid = $uid;
				$table->type = $this->group;
			}

			$table->avatar_id = $value->source;

			$table->store();

			return true;
		}

		if ($value->type === 'upload') {
			$data = new stdClass();

			if (!empty($value->path)) {
				$image = ES::image();
				$image->load($value->path);

				$avatar	= ES::avatar($image, $uid, $this->group);

				// Check if there's a profile photos album that already exists.
				$albumModel	= ES::model('Albums');

				// Retrieve the user's default album
				$album = $albumModel->getDefaultAlbum($uid, $this->group, SOCIAL_ALBUM_PROFILE_PHOTOS);

				$photo = ES::table('Photo');
				$photo->uid = $uid;
				$photo->type = $this->group;
				$photo->user_id = $this->group == SOCIAL_TYPE_USER ? $uid : ES::user()->id;
				$photo->album_id = $album->id;
				$photo->title = $value->name;
				$photo->caption = '';
				$photo->ordering = 0;

				// We need to set the photo state to "SOCIAL_PHOTOS_STATE_TMP"
				$photo->state = SOCIAL_PHOTOS_STATE_TMP;

				// Try to store the photo first
				$state = $photo->store();

				if (!$state) {
					$this->setError(JText::_('PLG_FIELDS_AVATAR_ERROR_CREATING_PHOTO_OBJECT'));
					return false;
				}

				// Push all the ordering of the photo down
				$photosModel = ES::model('photos');
				$photosModel->pushPhotosOrdering($album->id, $photo->id);

				// If album doesn't have a cover, set the current photo as the cover.
				if (!$album->hasCover()) {
					$album->cover_id = $photo->id;

					// Store the album
					$album->store();
				}

				// Get the photos library
				$photoLib = ES::get('Photos', $image);
				$storage = $photoLib->getStoragePath($album->id, $photo->id);
				$paths = $photoLib->create($storage);

				// Create metadata about the photos
				foreach ($paths as $type => $fileName) {
					$meta = ES::table('PhotoMeta');
					$meta->photo_id = $photo->id;
					$meta->group = SOCIAL_PHOTOS_META_PATH;
					$meta->property = $type;
					$meta->value = $storage . '/' . $fileName;

					$meta->store();
				}

				// Synchronize Indexer
				$indexer = ES::get('Indexer');
				$template = $indexer->getTemplate();
				$template->setContent($photo->title, $photo->caption);

				$url = $photo->getPermalink();
				$url = '/' . ltrim($url, '/');
				$url = str_replace('/administrator/', '/', $url);

				$template->setSource($photo->id, SOCIAL_INDEXER_TYPE_PHOTOS, $photo->uid, $url);
				$template->setThumbnail($photo->getSource('thumbnail'));

				$indexer->index($template);

				// Crop the image to follow the avatar format. Get the dimensions from the request.
				if (!empty($value->data) && is_object($value->data)) {
					$width = $value->data->width;
					$height = $value->data->height;
					$top = $value->data->top;
					$left = $value->data->left;

					$avatar->crop($top, $left, $width, $height);
				}

				$options = array();
				
				// Create the avatars now
				if (!$createStream) {
					$options = array( 'addstream' => false );
				}
				
				$tmpPath = dirname($value->path);

				$options['deleteimage'] = false;

				$avatar->store($photo, $options, $isNewlyRegistered);

				// Delete the temporary folder.
				if ($deleteTmpFolder) {
					JFolder::delete($tmpPath);
				}
			}

			return true;
		}
	}

	/**
	 * Gets the meta data for avatar from the OAuth client
	 *
	 * @since  1.2
	 * @access public
	 */
	public function onOAuthGetUserMeta(&$details, &$client)
	{
		$type = $client->getType();

		if (!isset($details['avatar']) && $this->config->get('oauth.' . $type . '.registration.avatar')) {
			$details['avatar'] = $client->getAvatar($details, 'original');
		}
	}

	/**
	 * Copies the avatar if the user is linking an existing account with an oauth account
	 *
	 * @since  1.2
	 * @access public
	 */
	public function onLinkOAuthAfterSave(&$meta, &$client, &$user)
	{
		$importAvatar = JRequest::getBool('importAvatar', false);

		if ($importAvatar) {
			return $this->onRegisterOAuthAfterSave($meta, $client, $user, true);
		}

		return;
	}

	/**
	 * Checks if this field is complete.
	 *
	 * @since  1.2
	 * @access public
	 */
	public function onFieldCheck($user)
	{
		return $this->validate($this->value, $user);
	}

	/**
	 * Checks if this field is filled in.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function onProfileCompleteCheck($user)
	{
		if (!$this->config->get('user.completeprofile.strict') && !$this->isRequired()) {
			return true;
		}

		return $user->hasAvatar();
	}
}
