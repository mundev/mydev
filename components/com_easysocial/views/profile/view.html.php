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

class EasySocialViewProfile extends EasySocialSiteView
{
	/**
	 * Determines if the view should be visible on lockdown mode
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function isLockDown()
	{
		return true;
	}

	/**
	 * Displays a user profile to a 3rd person perspective.
	 *
	 * @since	1.0
	 * @access	public
	 **/
	public function display($tpl = null)
	{
		// Get the user's id.
		$id = $this->input->get('id', 0, 'int');

		// The current logged in user might be viewing their own profile.
		if ($id == 0) {
			$id = ES::user()->id;
		}

		// When the user tries to view his own profile but if he isn't logged in, throw a login page.
		if ($id == 0) {
			ES::requireLogin();
		}

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Get the user's object.
		$user = ES::user($id);

		// If the user doesn't exist throw an error
		if (!$user->id) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		// If the user is blocked or the user doesn't have community access
		if (($this->my->id != $user->id && $this->my->isBlockedBy($user->id, true)) || !$user->hasCommunityAccess()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		// If the user is blocked, they should not be accessible
		if ($user->isBlock() && !$this->my->isSiteAdmin()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		// Determine if the current request is to load an app
		$appId = $this->input->get('appId', 0, 'int');

		// There is a possibility where the default profile page to render is the about section
		$defaultDisplay = $this->config->get('users.profile.display', 'timeline');
		$layout = $this->input->get('layout', '', 'cmd');

		if ($defaultDisplay == 'timeline' && !$layout && !$appId) {
			$layout = 'timeline';
		}

		if ($defaultDisplay == 'about' && $layout != 'timeline' && !$appId) {
			return $this->about();
		}

		// Set the page properties
		$this->page->title($this->string->escape($user->getName()));

		if ($defaultDisplay == 'about' && $layout == 'timeline') {
			$this->page->breadcrumb($this->string->escape($user->getName()), $user->getPermalink());
			$this->page->breadcrumb($this->string->escape('COM_ES_TIMELINE'));
		} else {
			$this->page->breadcrumb($this->string->escape($user->getName()));
		}

		// Let's test if the current viewer is allowed to view this profile.
		$canView = $user->isViewer() || $this->my->canView($user);

		$arguments = array(&$user, &$canView);

		$dispatcher = ES::dispatcher();
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onDisplayProfile', $arguments);

		if (!$canView) {

			$facebook = ES::oauth('facebook');
			$return = base64_encode($user->getPermalink());

			$this->set('return', $return);
			$this->set('facebook', $facebook);
			$this->set('user', $user);

			return parent::display('site/profile/restricted');
		}

		// Apply metadata tags.
		$user->renderHeaders();

		// Assign badge only when other users are viewing the person's profile
		if (!$this->my->isViewer() && $this->my->id) {
			ES::badges()->log('com_easysocial', 'profile.view', $this->my->id, 'COM_EASYSOCIAL_PROFILE_VIEWED_A_PROFILE');
		}

		// Default contents
		$contents = '';

		// Load the app when necessary
		if ($appId) {
			$app = ES::table('App');
			$app->load($appId);

			return $this->app($user, $app);
		}

		// Should we filter stream items by specific app types
		$appType = $this->input->get('filterid', '', 'string');

		// Get the stream lib
		$stream = ES::stream();

		// If contents is still empty at this point, then we just get the stream items as the content
		if (!$contents) {

			// Retrieve user's stream
			$theme = ES::themes();

			// Get story
			$story = ES::story(SOCIAL_TYPE_USER);
			$story->target = $user->id;

			//lets get the sticky posts 1st
			$stickies = $stream->getStickies(array('userId' => $user->id, 'limit' => 0));

			if ($stickies) {
				$stream->stickies = $stickies;
			}

			$startLimit = $this->input->get('limitstart', 0, 'int');
			$streamOptions = array('userId' => $user->id, 'nosticky' => true, 'startlimit' => $startLimit);
			$stream->get($streamOptions);

			// Only registered users can access the story form
			if (!$this->my->guest) {
				$stream->story = $story;
			}

			// Set stream to theme
			$theme->set('stream', $stream);
			$contents = $theme->output('site/profile/stream');
		}

		$this->set('layout', $layout);
		$this->set('contents' , $contents);
		$this->set('user', $user);
		$this->set('stream', $stream);

		return parent::display('site/profile/default/default');
	}

	/**
	 * Renders the app section of a user
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function app(&$user, $app)
	{
		// Check if the user has access to this app
		if (!$app->accessible($user->id)) {
			$this->info->set(false, JText::_('COM_ES_PROFILE_APP_IS_NOT_INSTALLED_BY_USER'), SOCIAL_MSG_ERROR);

			$redirect = ESR::profile(array('id' => $user->getAlias()), false);
			return $this->redirect($redirect);
		}

		// Set the page title
		$this->page->title(ES::string()->escape($user->getName()) . ' - ' . $app->get('title'));

		$lib = ES::apps();
		$contents = $lib->renderView(SOCIAL_APPS_VIEW_TYPE_EMBED, 'profile', $app, array('userId' => $user->id));

		$this->set('active', $app->element);
		$this->set('user', $user);
		$this->set('contents', $contents);

		return parent::display('site/profile/app/default');
	}

	/**
	 * Displays the about section of a user
	 *
	 * @since	1.0
	 * @access	public
	 **/
	public function about($tpl = null)
	{
		// Get the user's id.
		$id = $this->input->get('id', 0, 'int');

		// The current logged in user might be viewing their own profile.
		if ($id == 0) {
			$id = ES::user()->id;
		}

		// When the user tries to view his own profile but if he isn't logged in, throw a login page.
		if ($id == 0) {
			ES::requireLogin();
		}

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Get the user's object.
		$user = ES::user($id);

		// If the user doesn't exist throw an error
		if (!$user->id) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		// If the user is blocked or the user doesn't have community access
		if (($this->my->id != $user->id && $this->my->isBlockedBy($user->id)) || !$user->hasCommunityAccess()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		// If the user is blocked, they should not be accessible
		if ($user->isBlock() && !$this->my->isSiteAdmin()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		$this->page->title($this->string->escape($user->getName()));

		$defaultDisplay = $this->config->get('users.profile.display', 'timeline');

		if ($defaultDisplay == 'timeline') {
			$this->page->breadcrumb($this->string->escape($user->getName()), $user->getPermalink());
			$this->page->breadcrumb($this->string->escape('COM_ES_ABOUT'));
		} else {
			$this->page->breadcrumb($this->string->escape($user->getName()));
		}

		// Let's test if the current viewer is allowed to view this profile.
		if (!$user->isViewer() && !$this->my->canView($user)) {

			$facebook = ES::oauth('facebook');
			$return = base64_encode($user->getPermalink());

			$this->set('return', $return);
			$this->set('facebook', $facebook);
			$this->set('user', $user);

			return parent::display('site/profile/restricted');
		}

		// Apply metadata tags.
		$user->renderHeaders();

		// We should generate a canonical link if user is viewing the about section and the default page is about
		if ($this->config->get('users.profile.display') == 'about') {
			$timelinePermalink = ESR::profile(array('id' => $user->getAlias(), 'layout' => 'timeline', 'external' => true), false);
			$this->page->canonical($timelinePermalink);
		}

		$usersModel = ES::model('Users');
		$steps = $usersModel->getAbout($user);

		// Determines if the viewer can edit the profile
		$canEdit = $user->id == $this->my->id;

		$arguments = array(&$user, &$canEdit);

		$dispatcher = ES::dispatcher();
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onDisplayProfileAbout', $arguments);

		$this->set('canEdit', $canEdit);
		$this->set('steps', $steps);
		$this->set('user', $user);

		return parent::display('site/profile/about/default');
	}

	/**
	 * Responsible to output the edit profile layout
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function edit($errors = null)
	{
		// Unauthorized users should not be allowed to access this page.
		ES::requireLogin();

		// Set any messages here.
		$this->info->set($this->getMessage());

		// Load the language file from the back end.
		ES::language()->loadAdmin();

		// There is instances where we can allow user to edit another person's profile through an app
		$id = $this->input->get('id', $this->my->id, 'int');
		$canEdit = $id == $this->my->id;

		$user = ES::user($id);

		$arguments = array(&$user, &$canEdit);

		$dispatcher = ES::dispatcher();
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onDisplayProfileEdit', $arguments);

		// If not allowed to edit, throw an error
		if (!$canEdit) {
			return JError::raiseError(500, 'Not allowed');
		}

		// Get list of steps for this user's profile type.
		$profile = $user->getProfile();

		// Get workflow that are associated with this profile type
		$workflow = $profile->getWorkflow();

		// Get user's installed apps
		$appsModel = ES::model('Apps');
		$userApps = $appsModel->getUserApps($user->id);

		// Get the steps model
		$stepsModel = ES::model('Steps');
		$steps = $stepsModel->getSteps($workflow->id, SOCIAL_TYPE_PROFILES, SOCIAL_PROFILES_VIEW_EDIT);

		// Get custom fields model.
		$fieldsModel = ES::model('Fields');

		// Get custom fields library.
		$fields = ES::fields();

		// Set page attributes
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_ACCOUNT_SETTINGS');
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PROFILE', ESR::profile());
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_ACCOUNT_SETTINGS');

		// Check if there are any errors in the session
		// If session contains error, means that this is from the ES::fields()->checkCompleteProfile();
		if (empty($errors)) {
			$session = JFactory::getSession();
			$errors = $session->get('easysocial.profile.errors', '', SOCIAL_SESSION_NAMESPACE);

			if (!empty($errors)) {
				ES::info()->set(false, JText::_('COM_EASYSOCIAL_PROFILE_PLEASE_COMPLETE_YOUR_PROFILE'), SOCIAL_MSG_ERROR);
			}
		}

		// Set the callback for the triggered custom fields
		$callback = array($fields->getHandler(), 'getOutput');

		$conditionalFields = array();

		// preload fields for each steps.
		ES::cache()->cacheProfileStepsFields($steps, SOCIAL_PROFILES_VIEW_EDIT);

		// preload fields privacy.
		$tmpFields = array();
		foreach ($steps as &$step) {
			$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $user->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => 'edit'));
			if ($step->fields) {
				$tmpFields = array_merge($tmpFields, $step->fields);
			}
		}

		ES::cache()->cacheFieldsPrivacy($user->id, $tmpFields);

		// Get the custom fields for each of the steps.
		foreach ($steps as &$step) {

			// Trigger onEdit for custom fields.
			if (!empty($step->fields)) {

				$post = JRequest::get('post');
				$args = array( &$post, &$user, $errors);
				$fields->trigger('onEdit', SOCIAL_FIELDS_GROUP_USER, $step->fields, $args, $callback);
			}

			foreach ($step->fields as $field) {
				if ($field->isConditional()) {
					$conditionalFields[$field->id] = false;
				}
			}
		}

		if ($conditionalFields) {
			$conditionalFields = json_encode($conditionalFields);
		} else {
			$conditionalFields = false;
		}

		// Get oauth clients
		$oauthModel = ES::model('OAuth');
		$oauthClients = $oauthModel->getOauthClients($user->id);

		$profilesCount = 0;

		if ($user->canSwitchProfile()) {
			$profileModel = FD::model('Profiles');
			$profilesCount = $profileModel->getTotalProfiles();
		}

		$verification = ES::verification();
		$showVerificationLink = false;

		if ($verification->canRequest()) {
			$showVerificationLink = true;
		}

		// Determines if there are any active step in the query
		$activeStep = $this->input->get('activeStep', 0, 'int');

		$this->set('user', $user);
		$this->set('activeStep', $activeStep);
		$this->set('conditionalFields', $conditionalFields);
		$this->set('oauthClients', $oauthClients);
		$this->set('profile', $profile);
		$this->set('workflow', $workflow);
		$this->set('steps', $steps);
		$this->set('apps', $userApps);
		$this->set('profilesCount', $profilesCount);
		$this->set('showVerificationLink', $showVerificationLink);

		return parent::display('site/profile/edit/default');
	}

	/**
	 * profile switch page.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function switchProfile()
	{
		// Unauthorized users should not be allowed to access this page.
		ES::requireLogin();

		// Set any messages here.
		$this->info->set($this->getMessage());

		// Load the language file from the back end.
		ES::language()->loadAdmin();

		if (!$this->my->canSwitchProfile()) {
			$this->info->set(false, JText::_('COM_EASYSOCIAL_PROFILE_SWITCH_NOT_ALLOWED'), SOCIAL_MSG_ERROR);

			$redirect = ESR::profile(array('layout' => 'edit'), false);
			return $this->redirect($redirect);
		}

		// Get list of steps for this user's profile type.
		$profile = $this->my->getProfile();

		$profileModel = FD::model('Profiles');
		$profilesCount = $profileModel->getTotalProfiles();

		if ($profilesCount <= 1) {
			$this->info->set(false, JText::_('COM_EASYSOCIAL_PROFILE_SWITCH_NO_PROFILE_TO_SWITCH'), SOCIAL_MSG_ERROR);

			$redirect = ESR::profile(array('layout' => 'edit'), false);
			return $this->redirect($redirect);
		}


		$options = array('state'	=> SOCIAL_STATE_PUBLISHED,
						'ordering' => 'ordering',
						'limit' => SOCIAL_PAGINATION_NO_LIMIT,
						'excludeProfileIds' => array($profile->id),
						'registration' => true
					);

		$profiles = $profileModel->getProfiles($options);

		// Set the page title
		$this->page->title(JText::_('COM_EASYSOCIAL_PROFILE_SWITCH_SELECT_PROFILE_TYPE_TITLE'));
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PROFILE', ESR::profile());
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PROFILE_SWITCH');

		// The first profile selection page is always the first in the progress bar.
		$this->set('currentStep', SOCIAL_REGISTER_SELECTPROFILE_STEP);
		// $this->set('profileId', $profileId);
		$this->set('profiles', $profiles);

		return parent::display('site/profile/switch/default');

		return;
	}

	/**
	 * profile switch edit page.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function switchProfileEdit()
	{
		// Unauthorized users should not be allowed to access this page.
		ES::requireLogin();

		// Set any messages here.
		$this->info->set($this->getMessage());

		// Load the language file from the back end.
		ES::language()->loadAdmin();

		if (!$this->my->canSwitchProfile()) {
			$this->info->set(false, JText::_('COM_EASYSOCIAL_PROFILE_SWITCH_NOT_ALLOWED'), SOCIAL_MSG_ERROR);

			$redirect = ESR::profile(array('layout' => 'edit'), false);
			return $this->redirect($redirect);
		}

		$newProfileId = $this->input->get('profile_id', 0, 'int');

		$profile = ES::table('Profile');
		$profile->load($newProfileId);

		if (!$profile->id) {
			$this->info->set(false, JText::_('COM_EASYSOCIAL_PROFILE_SWITCH_INVALID_PROFILE_TYPE'), SOCIAL_MSG_ERROR);

			$redirect = ESR::profile(array('layout' => 'edit'), false);
			return $this->redirect($redirect);
		}

		// Set page attributes
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_ACCOUNT_SETTINGS');
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PROFILE', ESR::profile());
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_ACCOUNT_SETTINGS');


		// Get list of steps for this user's profile type.
		$currentProfile = $this->my->getProfile();

		// Get user's installed apps
		$appsModel = ES::model('Apps');
		$userApps = $appsModel->getUserApps($this->my->id);

		// Get the steps model
		$stepsModel = ES::model('Steps');
		$steps = $stepsModel->getSteps($profile->getWorkflow()->id, SOCIAL_TYPE_PROFILES, SOCIAL_PROFILES_VIEW_EDIT);

		// Get custom fields model.
		$fieldsModel = ES::model('Fields');

		// Get custom fields library.
		$fields = ES::fields();

		// Set page attributes
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_ACCOUNT_SETTINGS');
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PROFILE', ESR::profile());
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_ACCOUNT_SETTINGS');

		// Check if there are any errors in the session
		// If session contains error, means that this is from the ES::fields()->checkCompleteProfile();
		if (empty($errors)) {
			$session = JFactory::getSession();
			$errors = $session->get('easysocial.profile.errors', '', SOCIAL_SESSION_NAMESPACE);

			if (!empty($errors)) {
				ES::info()->set(false, JText::_('COM_EASYSOCIAL_PROFILE_PLEASE_COMPLETE_YOUR_PROFILE'), SOCIAL_MSG_ERROR);
			}
		}

		// Set the callback for the triggered custom fields
		$callback = array($fields->getHandler(), 'getOutput');

		$conditionalFields = array();

		// Get the custom fields for each of the steps.
		foreach ($steps as &$step) {

			$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $this->my->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => 'edit'));

			// Trigger onEdit for custom fields.
			if (!empty($step->fields)) {

				// here we need to inject the value from current profile.
				$post = array();
				foreach ($step->fields as $field) {
					$value = $this->my->getFieldData($field->unique_key);

					$fieldKey = SOCIAL_FIELDS_PREFIX . $field->id;
					$post[$fieldKey] = $value;
				}

				$args = array( &$post, &$this->my, $errors);
				$fields->trigger('onEdit' , SOCIAL_FIELDS_GROUP_USER , $step->fields , $args, $callback );
			}

			foreach ($step->fields as $field) {
				if ($field->isConditional()) {
					$conditionalFields[$field->id] = false;
				}
			}
		}

		if ($conditionalFields) {
			$conditionalFields = json_encode($conditionalFields);
		} else {
			$conditionalFields = false;
		}

		$this->set('profile', $profile);
		$this->set('steps', $steps);
		$this->set('currentProfile', $currentProfile);
		$this->set('conditionalFields', $conditionalFields);

		return parent::display('site/profile/switch/edit');

	}

	/**
	 * Renders the edit alerts form
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function editNotifications()
	{
		ES::requireLogin();
		ES::checkCompleteProfile();

		// Get the user notification settings
		$alerts = ES::alert()->getUserSettings($this->my->id);

		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_NOTIFICATION_SETTINGS');
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PROFILE', ESR::profile());
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_NOTIFICATION_SETTINGS');

		// Render custom alert settings from the app
		$customAlerts = array();

		// Load the library.
		$arguments = array(&$customAlerts);

		$dispatcher = ES::dispatcher();
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onRenderAlerts', $arguments);

		// Hardcode the groups
		$groups = array('system', 'others');

		// filter the alerts to remove the alerts for those disabled features. #717
		$filteredAlerts = array();

		foreach ($groups as $group) {

			$filteredAlerts[$group] = array();

			if (isset($alerts[$group])) {
				foreach ($alerts[$group] as $element => $alert) {

					if (($element == 'albums' || $element == 'photos') && !$this->config->get('photos.enabled')) {
						continue;
					}

					if ($element == 'broadcast' && !$this->config->get('notifications.broadcast.popup')) {
						continue;
					}

					if ($element == 'conversations' && !$this->config->get('conversations.enabled')) {
						continue;
					}

					if ($element == 'events' && !$this->config->get('events.enabled')) {
						continue;
					}

					if ($element == 'groups' && !$this->config->get('groups.enabled')) {
						continue;
					}

					if ($element == 'pages' && !$this->config->get('pages.enabled')) {
						continue;
					}

					if ($element == 'videos' && !$this->config->get('video.enabled')) {
						continue;
					}

					if (($element == 'badges') && !$this->config->get('badges.enabled')) {
						continue;
					}

					if ($element == 'friends' && !$this->config->get('friends.enabled')) {
						continue;
					}

					if ($element == 'polls' && !$this->config->get('polls.enabled')) {
						continue;
					}


					$filteredAlerts[$group][$element] = $alert;
				}
			}
		}

		$activeTab = $this->input->get('activeTab', '', 'cmd');

		$this->set('activeTab', $activeTab);
		$this->set('customAlerts', $customAlerts);
		$this->set('groups', $groups);
		$this->set('alerts', $filteredAlerts);

		parent::display('site/profile/editnotifications/default');
	}

	/**
	 * Allows caller to request to be submitted for verification
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function submitVerification()
	{
		$verification = ES::verification();

		if (!$verification->canRequest()) {

			// Display proper message if user has already request for verification previously.
			if ($verification->hasRequested()) {
				$this->setMessage('COM_ES_ALREADY_REQUEST_VERIFICATION', 'error');
				$this->info->set($this->getMessage());
				return $this->redirect(ESR::dashboard());
			}

			return JError::raiseError(500, 'This feature is not available');
		}

		parent::display('site/profile/submitverification/default');
	}

	/**
	 * GDPR download
	 *
	 * @since	2.2
	 * @access	public
	 */
	public function download()
	{
		// Unauthorized users should not be allowed to access this page.
		ES::requireLogin();

		if (!$this->config->get('users.download.enabled')) {
			$this->setMessage('COM_ES_RESTRICTED_AREA_DESC', 'error');
			$this->info->set($this->getMessage());

			$redirect = ESR::profile(array('layout' => 'edit'), false);
			return $this->redirect($redirect);
		}

		// Set page properties
		$this->page->title('COM_ES_PAGE_TITLE_GDPR_DOWNLOAD');
		$this->page->breadcrumb('COM_ES_PAGE_TITLE_GDPR_DOWNLOAD');

		// Check if this user has any download request or not
		$download = ES::table('Download');
		$download->load(array('userid' => $this->my->id));

		$this->set('download', $download);

		return parent::display('site/profile/download/default');
	}

	/**
	 * Post processing after saving verification
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function saveVerification()
	{
		$this->info->set($this->getMessage());

		$redirect = ESR::profile(array(), false);

		return $this->app->redirect($redirect);
	}

	/**
	 * Renders the edit privacy form
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function editPrivacy()
	{
		ES::requireLogin();
		ES::checkCompleteProfile();

		// Get user's privacy
		$privacyLib = ES::privacy($this->my->id);
		$result = $privacyLib->getData();

		if (!$this->config->get('privacy.enabled')) {
			return $this->redirect(ESR::dashboard(array(), false));
		}

		// Set page attributes
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_PRIVACY_SETTINGS');
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PROFILE', ESR::profile());
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_PRIVACY_SETTINGS');

		$privacy = array();

		// Update the privacy data with proper properties.
		foreach ($result as $group => $items) {

			// We do not want to show field privacy rules here because it does not make sense for user to set a default value
			// Most of the fields only have 1 and it is set in Edit Profile page
			if ($group === 'field') {
				continue;
			}

			// Only display such privacy rules if photos is enabled
			if (($group == 'albums' || $group == 'photos') && !$this->config->get('photos.enabled')) {
				continue;
			}

			// Only display videos privacy if videos is enabled.
			if ($group == 'videos' && !$this->config->get('video.enabled')) {
				continue;
			}

			// Do not display badges / achievements in privacy if badges is disabled
			if ($group == 'achievements' && !$this->config->get('badges.enabled')) {
				continue;
			}

			// Do not display followers privacy item
			if ($group == 'followers' && !$this->config->get('followers.enabled')) {
				continue;
			}

			// Do not display followers privacy item if friends disabled
			if ($group == 'friends' && !$this->config->get('friends.enabled')) {
				continue;
			}

			// Do not display points privacy item
			if ($group == 'points' && !$this->config->get('points.enabled')) {
				continue;
			}

			// Do not display application privacy item
			if ($group == 'apps' && !$this->config->get('apps.browser')) {
				continue;
			}

			// Do not display polls privacy item
			if ($group == 'polls' && !$this->config->get('polls.enabled')) {
				continue;
			}


			// Initialize the result
			$privacyGroup = new stdClass();
			$privacyGroup->title = JText::_('COM_EASYSOCIAL_PRIVACY_GROUP_' . strtoupper($group));
			$privacyGroup->element = $group;
			$privacyGroup->description = JText::_('COM_EASYSOCIAL_PRIVACY_GROUP_' . strtoupper($group) . '_DESC');

			foreach ($items as &$item) {

				// Conversations rule should only appear if it is enabled.
				if (($group == 'profiles' && $item->rule == 'post.message') && !$this->config->get('conversations.enabled')) {
					$item = null;
					continue;
				}

				// if friends disabled and the default value set to 'friends', lets override it to 'member'
				if (!$this->config->get('friends.enabled') && ($item->default == SOCIAL_PRIVACY_FRIENDS_OF_FRIEND || $item->default == SOCIAL_PRIVACY_FRIEND)) {
					$item->default = SOCIAL_PRIVACY_MEMBER;
				}



				$rule = JString::str_ireplace('.', '_', $item->rule);
				$rule = strtoupper($rule);

				$groupKey = strtoupper($group);

				// Determines if this has custom
				$item->hasCustom = $item->custom ? true : false;

				// If the rule is a custom rule, we need to set the ids
				$item->customIds = array();
				$item->customUsers = array();

				if ($item->hasCustom) {
					foreach ($item->custom as $friend) {
						$item->customIds[] = $friend->user_id;

						$user = ES::user($friend->user_id);
						$item->customUsers[] = $user;
					}
				}

				// Try to find an app element that is related to the privacy type
				$app = ES::table('App');
				$appExists = $app->load(array('element' => $item->type));

				if ($appExists) {
					$app->loadLanguage();
				}

				// Go through each options to get the selected item
				$item->selected = '';

				foreach ($item->options as $option => $value) {
					if ($value) {
						$item->selected = $option;
					}

					// We need to remove "Everyone" if the site lockdown is enabled
					if ($this->config->get('general.site.lockdown.enabled') && $option == SOCIAL_PRIVACY_0) {
						unset($item->options[$option]);
					}

					// we need to remove 'friends' / 'friend of friends' if Friends disabled.
					if (!$this->config->get('friends.enabled') && ($option == SOCIAL_PRIVACY_20 || $option == SOCIAL_PRIVACY_30)) {
						unset($item->options[$option]);

						// set member as default.
						if ($value) {
							$item->options[SOCIAL_PRIVACY_10] = 1;
							$item->selected = SOCIAL_PRIVACY_10;
						}
					}


				}

				$item->groupKey = $groupKey;
				$item->label = JText::_('COM_EASYSOCIAL_PRIVACY_LABEL_' . $groupKey . '_' . $rule );
				$item->tips = JText::_('COM_EASYSOCIAL_PRIVACY_TIPS_' . $groupKey . '_' . $rule );
			}

			$privacyGroup->items = $items;
			$privacy[] = $privacyGroup;
		}

		// Get a list of blocked users for this user
		$blockModel = ES::model('Blocks');
		$blockedUsers = $blockModel->getBlockedUsers($this->my->id);

		$activeTab = $this->input->get('activeTab', '', 'cmd');

		$this->set('activeTab', $activeTab);
		$this->set('blockedUsers', $blockedUsers);
		$this->set('privacy', $privacy);

		return parent::display('site/profile/editprivacy/default');
	}

	/**
	 * Handle save profiles.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function save()
	{
		$this->info->set($this->getMessage());

		$task = $this->input->getString('task');
		$userId = $this->input->get('userId', '', 'int');

		$options = array();

		if ($userId && $task != 'save') {
			$user = ES::user($userId);
			return $this->redirect($user->getPermalink(false));
		}

		if ($task == 'save') {
			$options['layout'] = 'edit';

			if ($userId) {
				$options['id'] = $userId;
			}
		}

		$this->redirect(ESR::profile($options, false));
	}

	/**
	 * Handle save notification.
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function saveNotification()
	{
		$this->info->set($this->getMessage());

		// Remember active tab
		$active = $this->input->get('activeTab', '', 'cmd');

		$return = ESR::profile(array('layout' => 'editNotifications', 'activeTab' => $active), false);
		$this->redirect($return);
	}


	/**
	 * Handle save privacy.
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function savePrivacy()
	{
		$this->info->set($this->getMessage());

		// Remember active tab
		$active = $this->input->get('activeTab', '', 'cmd');

		$return = ESR::profile(array('layout' => 'editPrivacy', 'activeTab' => $active), false);

		$this->redirect($return);
	}


	/**
	 * Allows viewer to download a file from the group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function downloadFile()
	{
		// Currently only registered users are allowed to view a file.
		ES::requireLogin();

		// Load the file object
		$id = $this->input->get('fileid', 0, 'int');
		$file = ES::table('File');
		$file->load($id);

		if (!$file->id || !$id) {
			// Throw error message here.
			$this->redirect(FRoute::dashboard(array(), false));
			$this->close();
		}

		// Add points for the user when they upload a file.
		ES::points()->assign('files.download', 'com_easysocial', $this->my->id);

		// @TODO: Check for the privacy.

		$file->download();
		exit;
	}

	/**
	 * Post process after removing an avatar
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function removeAvatar()
	{
		$this->info->set($this->getMessage());

		$return = $this->my->getPermalink();

		$this->redirect($return);
	}


	/**
	 * Post processing after the user wants to delete their account
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function delete()
	{
		$this->info->set($this->getMessage());

		$return = ESR::dashboard(array(), false);

		$this->redirect($return);
	}

	/**
	 * Post processing after the user switch profile type.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function completeSwitch()
	{
		$this->info->set($this->getMessage());

		$return = ESR::profile(array('layout' => 'edit'), false);
		$this->redirect($return);
	}
}
