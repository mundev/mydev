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

require_once(dirname(__FILE__) . '/controller.php');

class EasySocialControllerInstallation extends EasySocialSetupController
{
	/**
	 * Retrieves the main menu item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getMainMenuType()
	{
		$this->engine();

		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__menu');
		$sql->column('menutype');
		$sql->where('home', '1');

		$db->setQuery($sql);
		$menuType = $db->loadResult();

		return $menuType;
	}

	/**
	 * Install default workflows
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function installWorkflows()
	{
		$this->engine();
		$results = array();

		$db = ES::db();
		$sql = $db->sql();

		// We check by the existing of each categories type.
		// User profiles
		$sql->select('#__social_profiles');
		$sql->column('id');
		$sql->limit(0, 1);

		$db->setQuery($sql);
		$id = $db->loadResult();

		if (!$id) {
			$workflow = ES::workflows(0, 'user');
			$workflow->createDefaultWorkflow();

			$results[] = $this->getResultObj('Created default user workflow successfully.', true);
		} else {
			$results[] = $this->getResultObj('Skipping user workflows creation as the workflow already exists on the site.', true);
		}

		// Clusters
		$types = array('group', 'event', 'page');

		$previous = $this->getPreviousVersion('scriptversion');
		$legacy = false;

		// Check if this is upgraded from version 1.x
		$parts = explode('.', $previous);

		if ($parts[0] == 1) {
			$legacy = true;
		}

		// Create default workflows for each types above
		foreach ($types as $type) {
			$sql = $db->sql();

			$sql->select('#__social_clusters_categories');
			$sql->column('COUNT(1)');
			$sql->where('type', $type);

			$db->setQuery($sql);
			$total = $db->loadResult();

			if (!$total) {
				$workflow = ES::workflows(0, $type);
				$workflow->createDefaultWorkflow($legacy);
				$results[] = $this->getResultObj('Created default ' . $type . ' workflow successfully.', true);
			} else {
				$results[] = $this->getResultObj('Skipping ' . $type . ' workflows creation as the workflow already exists on the site.', true);
			}
		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';
			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Install default custom profiles and fields
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installProfiles()
	{
		$this->engine();

		$results = array();

		// Create the default custom profile first.
		$results[] = $this->createCustomProfile();

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class 	= $obj->state ? 'success' : 'error';
			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}


		return $this->output( $result );
	}


	/**
	 * Creates default group categories
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function installDefaultGroupCategories()
	{
		$this->engine();

		$db = ES::db();
		$sql = $db->sql();

		$sql->select( '#__social_clusters_categories' );
		$sql->column( 'COUNT(1)' );
		$sql->where('type', SOCIAL_TYPE_GROUP);

		$db->setQuery( $sql );
		$total 	= $db->loadResult();

		// There are categories already, we shouldn't be doing anything here.
		if ($total) {
			$result = $this->getResultObj('Skipping default group category creation as there are already categories created on the site.', true);

			return $this->output($result);
		}

		$categories = array('general','automobile','technology','business','music');

		foreach ($categories as $categoryKey) {
			$results[] = $this->createGroupCategory($categoryKey);
		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class 	= $obj->state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Creates default page categories
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function installDefaultPageCategories()
	{
		$this->engine();

		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters_categories');
		$sql->column('COUNT(1)');
		$sql->where('type', SOCIAL_TYPE_PAGE);

		$db->setQuery($sql);
		$total = $db->loadResult();

		// There are categories already, we shouldn't be doing anything here.
		if ($total) {
			$result = $this->getResultObj('Skipping default page category creation as there are already categories created on the site.', true);
			return $this->output($result);
		}

		$categories = array('general','automobile','brand','business','artist', 'organization');

		foreach ($categories as $categoryKey) {
			$results[] = $this->createPageCategory($categoryKey);
		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach($results as $obj) {
			$class = $obj->state ? 'success' : 'error';
			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Creates default group categories
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function installDefaultEventCategories()
	{
		$this->engine();

		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters_categories');
		$sql->column('COUNT(1)');
		$sql->where('type', SOCIAL_TYPE_EVENT);

		$db->setQuery( $sql );
		$total = $db->loadResult();

		// There are categories already, we shouldn't be doing anything here.
		if ($total) {
			$result = $this->getResultObj('Skipping default event category creation as there are already categories created on the site.', true);

			return $this->output($result);
		}

		$categories = array('general', 'meeting');

		foreach ($categories as $categoryKey) {
			$results[] = $this->createEventCategory($categoryKey);
		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Creates default video categories
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function installDefaultVideoCategories()
	{
		$this->engine();

		$db = ES::db();
		$sql = $db->sql();

		// Check if there are any video categories already exists on the site
		$sql->select('#__social_videos_categories');
		$sql->column('COUNT(1)');

		$db->setQuery($sql);
		$total = $db->loadResult();

		// There are categories already, we shouldn't be doing anything here.
		if ($total) {
			$result = $this->getResultObj('Skipping default video category creation as there are already categories created on the site.', true);

			return $this->output($result);
		}

		$categories = array('General', 'Music', 'Sports', 'News', 'Gaming', 'Movies', 'Documentary', 'Fashion', 'Travel', 'Technology');
		$i = 0;

		foreach ($categories as $categoryKey) {
			$results[] = $this->createVideoCategory($categoryKey, $i);
			$i++;

		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Creates default audio genres
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function installDefaultAudioGenres()
	{
		$this->engine();

		$db = ES::db();
		$sql = $db->sql();

		// Check if there are any audio genre already exists on the site
		$sql->select('#__social_audios_genres');
		$sql->column('COUNT(1)');

		$db->setQuery($sql);
		$total = $db->loadResult();

		// There are genres already, we shouldn't be doing anything here.
		if ($total) {
			$result = $this->getResultObj('Skipping default audio genre creation as there are already genres created on the site.', true);

			return $this->output($result);
		}

		$genres = array('Country', 'Rock', 'Disco', 'Pop', 'Classical', 'Instrumental', 'Techno', 'Alternative', 'Jazz', 'Blues');
		$i = 0;

		foreach ($genres as $genreKey) {
			$results[] = $this->createAudioGenre($genreKey, $i);
			$i++;

		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}


	/**
	 * Synchronizes database tables
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function syncDB()
	{
		$this->engine();

		// Get this installations version
		$version = $this->getInstalledVersion();

		// Get previous version installed
		$previous = $this->getPreviousVersion( 'dbversion' );

		// Get total tables affected
		$affected = ES::syncDB($previous);

		// If the previous version is empty, we can skip this altogether as we know this is a fresh installation
		if (!empty($affected)) {
			$result = $this->getResultObj(JText::sprintf('COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_DB_SYNCED', $version), 1, 'Success');
		} else {
			$result = $this->getResultObj(JText::sprintf('COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_DB_NOTHING_TO_SYNC', $version), 1, 'Success');
		}

		// @TODO: In the future synchronize database table indexes here.

		// Update the version in the database to the latest now
		$config = ES::table('Config');
		$exists = $config->load(array('type' => 'dbversion'));
		$config->type = 'dbversion';
		$config->value = $version;

		$config->store();

		return $this->output($result);
	}

	public function createGroupCategory($categoryTitle)
	{
		$key = strtoupper($categoryTitle);
		$title = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_GROUP_CATEGORY_' . $key);
		$desc = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_GROUP_CATEGORY_' . $key . '_DESC');

		$category = ES::table( 'GroupCategory' );
		$category->alias = strtolower( $categoryTitle );
		$category->title = $title;
		$category->description = $desc;
		$category->type = SOCIAL_TYPE_GROUP;
		$category->created = ES::date()->toSql();
		$category->uid = ES::user()->id;
		$category->state = SOCIAL_STATE_PUBLISHED;

		$category->store();
		$category->assignWorkflow();

		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('Created group category <b>%1$s</b>', $title);

		return $result;
	}

	public function createPageCategory( $categoryTitle )
	{
		$key = strtoupper($categoryTitle);
		$title = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_PAGE_CATEGORY_' . $key);
		$desc = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_PAGE_CATEGORY_' . $key . '_DESC');

		$category = ES::table('PageCategory');
		$category->alias = strtolower($categoryTitle);
		$category->title = $title;
		$category->description = $desc;
		$category->type = SOCIAL_TYPE_PAGE;
		$category->created = ES::date()->toSql();
		$category->uid = ES::user()->id;
		$category->state = SOCIAL_STATE_PUBLISHED;

		$category->store();
		$category->assignWorkflow();

		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('Created page category <b>%1$s</b>', $title);

		return $result;
	}

	public function createEventCategory($categoryTitle)
	{
		$key = strtoupper($categoryTitle);
		$title = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_EVENT_CATEGORY_' . $key);
		$desc = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_EVENT_CATEGORY_' . $key . '_DESC');

		$category = ES::table('EventCategory');
		$category->alias = strtolower($categoryTitle);
		$category->title = $title;
		$category->description = $desc;
		$category->type = SOCIAL_TYPE_EVENT;
		$category->created = ES::date()->toSql();
		$category->uid = ES::user()->id;
		$category->state = SOCIAL_STATE_PUBLISHED;

		$category->store();
		$category->assignWorkflow();

		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('Created event category <b>%1$s</b>', $title);

		return $result;
	}

	public function createVideoCategory($categoryTitle, $i = 0)
	{
		$key = strtoupper($categoryTitle);
		$title = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_VIDEO_CATEGORY_' . $key);
		$desc = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_VIDEO_CATEGORY_' . $key . '_DESC');

		$category = ES::table('VideoCategory');
		$category->title = ucfirst($title);
		$category->alias = strtolower($title);
		$category->description = $desc;

		if ($i == 0) {
			$category->default = true;
		}

		// Get the current user's id
		$category->user_id = ES::user()->id;

		$category->state = true;
		$category->store();


		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('Created video category <b>%1$s</b>', $title);

		return $result;
	}

	public function createAudioGenre($genreTitle, $i = 0)
	{
		$key = strtoupper($genreTitle);
		$title = JText::_('COM_ES_INSTALLATION_DEFAULT_AUDIO_GENRE_' . $key);
		$desc = JText::_('COM_ES_INSTALLATION_DEFAULT_AUDIO_GENRE_' . $key . '_DESC');

		$genre = ES::table('AudioGenre');
		$genre->title = ucfirst($title);
		$genre->alias = strtolower($title);
		$genre->description = $desc;

		if ($i == 0) {
			$genre->default = true;
		}

		// Get the current user's id
		$genre->user_id = ES::user()->id;

		$genre->state = true;
		$genre->store();

		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('Created audio genre <b>%1$s</b>', $title);

		return $result;
	}

	/**
	 * Creates the default custom profile
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function createCustomProfile()
	{
		$this->engine();

		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_profiles');
		$sql->column('id');
		$sql->limit(0, 1);

		$db->setQuery($sql);
		$id = $db->loadResult();

		// We don't have to do anything since there's already a default profile
		if ($id) {
			$this->updateConfig('oauth.facebook.registration.profile', $id);

			$result = $this->getResultObj('Skipping custom profile creation as there are other custom profiles already installed.', true);
			return $result;
		}

		// If it doesn't exist, we'll have to create it.
		$profile = ES::table('Profile');
		$profile->title = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_PROFILE_TITLE');
		$profile->description = JText::_('COM_EASYSOCIAL_INSTALLATION_DEFAULT_PROFILE_DESC');

		// Get the default user group that the site is configured and select this group as the default for this profile.
		$usersConfig = JComponentHelper::getParams('com_users');
		$group = array($usersConfig->get('new_usertype'));

		// Set the group for this default profile
		$profile->gid = json_encode($group);
		$profile->default = 1;
		$profile->state = SOCIAL_STATE_PUBLISHED;

		// Set the default params for profile
		$params = ES::registry();
		$params->set('delete_account', 0);
		$params->set('theme', '');
		$params->set('registration', 'approvals');

		$profile->params = $params->toString();

		// Try to save the profile.
		$state = $profile->store();

		// Assign default workflow
		$profile->assignWorkflow();

		if (!$state) {
			$result = $this->getResultObj('COM_EASYSOCIAL_INSTALLATION_ERROR_CREATE_DEFAULT_PROFILE', false);
			return $result;
		}

		$this->updateConfig('oauth.facebook.registration.profile', $profile->id);

		$result = $this->getResultObj('Created default profile successfully.', true);

		return $result;
	}

	/**
	 * Saves a configuration item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function updateConfig($key, $value)
	{
		$this->engine();

		$config = ES::config();
		$config->set($key, $value);

		$jsonString = $config->toString();

		$configTable = ES::table('Config');

		if (!$configTable->load('site')) {
			$configTable->type = 'site';
		}

		$configTable->set('value', $jsonString);
		return $configTable->store();
	}

	/**
	 * Installs a single custom field
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installField($path, $group = 'user')
	{
		$this->engine();

		// Retrieve the installer library.
		$installer = FD::get('Installer');

		// Get the element
		$element = basename($path);

		// Try to load the installation from path.
		$state = $installer->load($path);

		// Try to load and see if the previous field apps already has a record
		$oldField = FD::table('App');
		$fieldExists = $oldField->load(array('type' => SOCIAL_APPS_TYPE_FIELDS , 'element' => $element, 'group' => $group));

		// If there's an error, we need to log it down.
		if (!$state) {

			$result = $this->getResultObj(JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_FIELD_ERROR_LOADING_FIELD', ucfirst($element)), false);

			return $result;
		}

		// Let's try to install it now.
		$app = $installer->install();

		// If there's an error installing, log this down.
		if ($app === false) {

			$result = $this->getResultObj(JText::sprintf('COM_EASYSOCIAL_INSTALLATION_FIELD_ERROR_INSTALLING_FIELD', ucfirst($element)), false);

			return $result;
		}

		// If the field apps already exist, use the previous title.
		if ($fieldExists) {
			$app->title = $oldField->title;
			$app->alias = $oldField->alias;
		}

		// Ensure that the field apps is published
		$app->state	= $fieldExists ? $oldField->state : SOCIAL_STATE_PUBLISHED;
		$app->store();

		$result = $this->getResultObj(JText::sprintf('COM_EASYSOCIAL_INSTALLATION_FIELD_SUCCESS_INSTALLING_FIELD', ucfirst($element)), true);

		return $result;
	}

	/**
	 * Retrieves the extension id
	 *
	 * @since	2.0.10
	 * @access	public
	 */
	public function getExtensionId()
	{
		$this->engine();

		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__extensions', 'id');
		$sql->where('element', 'com_easysocial');

		$db->setQuery($sql);

		// Get the extension id
		$extensionId = $db->loadResult();

		return $extensionId;
	}

	/**
	 * Creates the default menu for EasySocial
	 *
	 * @since	2.0.10
	 * @access	public
	 */
	public function createMenu()
	{
		$this->engine();

		$db = FD::db();

		// Get the extension id
		$extensionId = $this->getExtensionId();

		// Get the main menu that is used on the site.
		$menuType = $this->getMainMenuType();

		if( !$menuType )
		{
			return false;
		}

		$sql 	= $db->sql();

		$sql->select( '#__menu' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'link' , '%index.php?option=com_easysocial%' , 'LIKE' );
		$sql->where( 'type'	, 'component' );
		$sql->where( 'client_id'	, 0 );

		$db->setQuery( $sql );

		$exists	= $db->loadResult();

		if( $exists )
		{
			// we need to update all easysocial menu item with this new component id.
			$query = 'update `#__menu` set component_id = ' . $db->Quote( $extensionId );
			$query .= ' where `link` like ' . $db->Quote( '%index.php?option=com_easysocial%' );
			$query .= ' and `type` = ' . $db->Quote( 'component' );
			$query .= ' and `client_id` = ' . $db->Quote( '0' );

			$sql->clear();
			$sql->raw( $query );
			$db->setQuery( $sql );
			$db->query();

			return $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_SITE_MENU_UPDATED' ) , true );
		}

		$menu 					= JTable::getInstance( 'Menu' );
		$menu->menuType 		= $menuType;
		$menu->title 			= JText::_( 'COM_EASYSOCIAL_INSTALLATION_DEFAULT_MENU_COMMUNITY' );
		$menu->alias 			= 'community';
		$menu->path 			= 'easysocial';
		$menu->link 			= 'index.php?option=com_easysocial&view=dashboard';
		$menu->type 			= 'component';
		$menu->published 		= 1;
		$menu->parent_id 		= 1;
		$menu->component_id 	= $extensionId;
		$menu->client_id 		= 0;
		$menu->language 		= '*';

		$menu->setLocation( '1' , 'last-child' );

		$state 	= $menu->store();

		// Assign modules to dashboard menu
		$this->installModulesMenu( $menu->id );

		return $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_SITE_MENU_CREATED' ) , true );
	}


	/**
	 * install module and assign to unity view
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installModulesMenu( $dashboardMenuId = null )
	{
		$this->engine();

		$db 	= FD::db();
		$sql 	= $db->sql();

		$modulesToInstall = array();

		// register modules here.

		// online user
		$modSetting = new stdClass();
		$modSetting->title 		= 'Online Users';
		$modSetting->name 		= 'mod_easysocial_users';
		$modSetting->position 	= 'es-dashboard-sidebar-bottom';
		$modSetting->config 	= array('filter' 	=> 'online',
										'total' 	=> '5',
										'ordering' 	=> 'name',
										'direction' => 'asc' );
		$modulesToInstall[] 	= $modSetting;

		// Recent user
		$modSetting = new stdClass();
		$modSetting->title 		= 'Recent Users';
		$modSetting->name 		= 'mod_easysocial_users';
		$modSetting->position 	= 'es-dashboard-sidebar-bottom';
		$modSetting->config 	= array('filter' 	=> 'recent',
										'total' 	=> '5',
										'ordering' 	=> 'registerDate',
										'direction' => 'desc' );
		$modulesToInstall[] 	= $modSetting;

		// Recent albums
		$modSetting = new stdClass();
		$modSetting->title 		= 'Recent Albums';
		$modSetting->name 		= 'mod_easysocial_albums';
		$modSetting->position 	= 'es-dashboard-sidebar-bottom';
		$modSetting->config 	= array();
		$modulesToInstall[] 	= $modSetting;

		// leaderboard
		$modSetting = new stdClass();
		$modSetting->title 		= 'Leaderboard';
		$modSetting->name 		= 'mod_easysocial_leaderboard';
		$modSetting->position 	= 'es-dashboard-sidebar-bottom';
		$modSetting->config 	= array('total' => '5');
		$modulesToInstall[] 	= $modSetting;

		// Dating Search
		$modSetting = new stdClass();
		$modSetting->title = 'Search For People';
		$modSetting->name = 'mod_easysocial_dating_search';
		$modSetting->position = 'es-users-sidebar-bottom';
		$modSetting->config = array('searchname' 	=> '1',
										'searchgender' 	=> '1',
										'searchage' 	=> '1',
										'searchdistance' => '1' );
		$modulesToInstall[] 	= $modSetting;


		// real work here.
		foreach( $modulesToInstall as $module )
		{
			$jMod	= JTable::getInstance( 'Module' );

			$jMod->title 		= $module->title;
			$jMod->ordering 	= $this->getModuleOrdering( $module->position );
			$jMod->position 	= $module->position;
			$jMod->published 	= 1;
			$jMod->module 		= $module->name;
			$jMod->access 		= 1;

			if( $module->config )
			{
				$jMod->params 		= FD::json()->encode( $module->config );
			}
			else
			{
				$jMod->params 		= '';
			}

			$jMod->client_id 	= 0;
			$jMod->language 	= '*';

			$state = $jMod->store();

			if( $state && $dashboardMenuId )
			{
				// lets add into module menu.
				$modMenu = new stdClass();
				$modMenu->moduleid 	= $jMod->id;
				$modMenu->menuid 	= $dashboardMenuId;

				$state	= $db->insertObject( '#__modules_menu' , $modMenu );
			}

		}

	}


	/**
	 * get ordering based on the module position.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getModuleOrdering( $position )
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = 'select `ordering` from `#__modules` where `position` = ' . $db->Quote( $position );
		$query .= ' order by `ordering` desc limit 1';
		$sql->raw( $query );

		$db->setQuery( $sql );

		$result = $db->loadResult();

		return ( $result ) ? $result + 1 : 1;

	}

	/**
	 * Post installation process
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installPost()
	{
		$results = array();

		// Update the api key on the server with the one from the bootstrap
		$this->updateConfig('general.key', ES_KEY);

		// Update existing email logo override to a new home
		$this->updateEmailLogoOverrides();

		// Update existing video logo and watermark override to a new path
		$this->updateVideoImagesOverride();

		// Here we update the config for automatic purge sent email
		$previous = $this->getPreviousVersion('scriptversion');

		// This is hardcoded for upgrades from 1.x to 2.x
		$parts = explode('.', $previous);

		if ($parts[0] == 1) {
			$this->renameTemplateOverrides();
		}

		// Setup site menu.
		$results[] = $this->createMenu('site');

		// Now we need to update the #__update_sites row to include the api key as well as the domain
		$this->updateJoomlaUpdater();

		// Update the manifest_cache in #__extensions table
		$this->updateManifestCache();

		// Delete the easysocial from the Updates table
		$this->deleteUpdateRecord();

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		// Cleanup temporary files from the tmp folder
		$tmp = dirname(dirname(__FILE__)) . '/tmp';
		$folders = JFolder::folders($tmp, '.', false, true);

		if ($folders) {
			foreach ($folders as $folder) {
				@JFolder::delete($folder);
			}
		}

		// Here we need to delete those files that has been removed in 2.0 to avoid possible error
		$this->removeLegacyFiles();

		// Update installation package to 'launcher'
		$this->updatePackage();

		return $this->output($result);
	}

	/**
	 * Update installation package to launcher package to update issue via update button
	 *
	 * @since	2.1.3
	 * @access	public
	 */
	public function updatePackage()
	{
		// now we need to update the ES_INSTALLER to launcher to that the update button will
		// work correctly. #1558
		$path = JPATH_ADMINISTRATOR . '/components/com_easysocial/setup/bootstrap.php';

		// Read the contents
		$contents = JFile::read($path);

		$contents = str_ireplace("define('ES_INSTALLER', 'full');", "define('ES_INSTALLER', 'launcher');", $contents);
		$contents = preg_replace('/define\(\'ES_PACKAGE\', \'.*\'\);/i', "define('ES_PACKAGE', '');", $contents);

		JFile::write($path, $contents);
	}


	/**
	 * Once the installation is completed, we need to update Joomla's update site table with the appropriate data
	 *
	 * @since	2.0.10
	 * @access	public
	 */
	public function updateJoomlaUpdater()
	{
		$this->engine();

		$extensionId = $this->getExtensionId();

		$db = JFactory::getDBO();
		$query = array();
		$query[] = 'SELECT ' . $db->quoteName('update_site_id') . ' FROM ' . $db->quoteName('#__update_sites_extensions');
		$query[] = 'WHERE ' . $db->quoteName('extension_id') . '=' . $db->Quote($extensionId);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$updateSiteId = $db->loadResult();

		$defaultLocation = 'https://stackideas.com/jupdates/manifest/easysocial';
		$location = $defaultLocation . '?apikey=' . ES_KEY;

		// For some Joomla versions, there is no tables/updatesite.php
		// Hence, the JTable::getInstance('UpdateSite') will return null
		$table = JTable::getInstance('UpdateSite');

		if ($table) {
			// Now we need to update the url
			$exists = $table->load($updateSiteId);

			if (!$exists) {
				return false;
			}

			$table->location = $location;
			$table->store();

		} else {
			$query	= 'UPDATE '. $db->quoteName('#__update_sites')
					. ' SET ' . $db->quoteName('location') . ' = ' . $db->Quote($location)
					. ' WHERE ' . $db->quoteName('update_site_id') . ' = ' . $db->Quote($updateSiteId);
			$db->setQuery($query);
			$db->query();
		}

		// Cleanup unwanted data from updates table
		// Since Joomla will always try to add a new record when it doesn't find the same match, we need to delete records created
		// for https://stackideas.com/jupdates/manifest/easysocial
		$query = 'SELECT * FROM ' . $db->quoteName('#__update_sites') . ' WHERE ' . $db->quoteName('location') . '=' . $db->Quote($defaultLocation);
		$db->setQuery($query);

		$defaultSites = $db->loadObjectList();

		if (!$defaultSites) {
			return true;
		}

		foreach ($defaultSites as $site) {
			$query = 'DELETE FROM ' . $db->quoteName('#__update_sites') . ' WHERE ' . $db->quoteName('update_site_id') . '=' . $db->Quote($site->update_site_id);
			$db->setQuery($query);
			$db->Query();

			$query = 'DELETE FROM ' . $db->quoteName('#__update_sites_extensions') . ' WHERE ' . $db->quoteName('update_site_id') . '=' . $db->Quote($site->update_site_id);
			$db->setQuery($query);
			$db->Query();
		}

		return true;
	}

	/**
	 * Update the manifest cache
	 *
	 * @since   2.0.13
	 * @access  public
	 */
	public function updateManifestCache()
	{
		$extensionId = $this->getExtensionId();
		$manifest_details = JInstaller::parseXMLInstallFile(JPATH_ROOT. '/administrator/components/com_easysocial/easysocial.xml');
		$manifest = json_encode($manifest_details);

		// For some Joomla versions, there is no tables/Extension.php
		// Hence, the JTable::getInstance('Extension') will return null
		$table = JTable::getInstance('Extension');

		if ($table) {
			$exists = $table->load($extensionId);

			if (!$exists) {
				return false;
			}

			$table->manifest_cache = $manifest;
			$table->store();
		} else {
			$query	= 'UPDATE '. $db->quoteName('#__extensions')
					. ' SET ' . $db->quoteName('manifest_cache') . ' = ' . $db->Quote($manifest)
					. ' WHERE ' . $db->quoteName('extension_id') . ' = ' . $db->Quote($extensionId);
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Delete record in updates table
	 *
	 * @since   2.0.13
	 * @access  public
	 */
	public function deleteUpdateRecord()
	{
		$db = JFactory::getDBO();

		$query = 'DELETE FROM ' . $db->quoteName('#__updates') . ' WHERE ' . $db->quoteName('extension_id') . '=' . $db->Quote($this->getExtensionId());
		$db->setQuery($query);
		$db->Query();
	}

	/**
	 * Removed unused files from the site
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function removeLegacyFiles()
	{
		// Backend files
		$files = array();
		$files[] = '/components/com_easysocial/includes/crawler/hooks/images.php';
		$files[] = '/components/com_easysocial/themes/default/settings/form/pages/general/emails.php';
		$files[] = '/components/com_easysocial/themes/default/settings/form/pages/general/emails.js';
		$files[] = '/components/com_easysocial/defaults/sidebar/access.json';
		$files[] = '/components/com_easysocial/defaults/sidebar/maintenance.json';
		$files[] = '/components/com_easysocial/themes/default/settings/form/pages/general/login.php';
		$files[] = '/components/com_easysocial/themes/default/settings/form/pages/general/login.js';

		foreach ($files as $file) {

			// Append administrator path
			$file = JPATH_ADMINISTRATOR . $file;

			if (JFile::exists($file)) {
				JFile::delete($file);
			}
		}

		// Frontend files
		$frontFiles = array();
		$frontFiles[] = '/components/com_easysocial/views/polls/metadata.xml';

		foreach ($frontFiles as $file) {

			// Append full path
			$file = JPATH_ROOT . $file;

			if (JFile::exists($file)) {
				JFile::delete($file);
			}
		}

		// Media files
		$mediaFiles = array();
		$mediaFiles[] = '/media/com_easysocial/apps/user/followers/themes/default/widgets/dashboard/suggestions.js';

		foreach ($mediaFiles as $file) {

			$file = JPATH_ROOT . $file;

			if (JFile::exists($file)) {
				JFile::delete($file);
			}
		}
	}

	/**
	 * Update old email logo override to new path
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function updateEmailLogoOverrides()
	{
		if (ES::hasOverride('email_logo')) {
			return;
		}

		$assets = ES::assets();

		// Set the logo for the generic email template
		$override = JPATH_ROOT . '/templates/' . $assets->getJoomlaTemplate() . '/html/com_easysocial/emails/logo.png';
		$exists = JFile::exists($override);

		// Copy the file over but retain original logo
		if ($exists) {
			$newOverride = JPATH_ROOT . '/images/easysocial_override/email_logo.png';

			// Normalize seprator
			$override = ES::normalizeSeparator($override);
			$newOverride = ES::normalizeSeparator($newOverride);

			$logo = JFile::read($override);

			JFile::write($newOverride, $logo);
		}
	}

	/**
	 * Update old email logo override to new path
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function updateVideoImagesOverride()
	{
		$overrides = array('video_logo', 'video_watermark');

		foreach ($overrides as $file) {
			if (ES::hasOverride($file)) {
				continue;
			}

			$assets = ES::assets();

			$tmp = explode('_', $file);

			// Set the logo for the generic email template
			$override = JPATH_ROOT . '/templates/' . $assets->getJoomlaTemplate() . '/html/com_easysocial/videos/' . $tmp[1] . '.png';
			$exists = JFile::exists($override);

			// Copy the file over but retain original logo
			if ($exists) {
				$newOverride = JPATH_ROOT . '/images/easysocial_override/' . $file . '.png';

				// Normalize seprator
				$override = ES::normalizeSeparator($override);
				$newOverride = ES::normalizeSeparator($newOverride);

				$logo = JFile::read($override);

				JFile::write($newOverride, $logo);
			}
		}
	}

	/**
	 * Rename template overrides folder
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function renameTemplateOverrides()
	{
		$this->engine();

		// Get current site's template
		$model = ES::model('Themes');
		$template = $model->getCurrentTemplate();

		// Check if there is a template override for component
		$path = JPATH_ROOT . '/templates/' . $template . '/html/com_easysocial';

		$date = JFactory::getDate();
		$postfix = $date->format('j') . '-' . $date->format('n') . '-' . $date->format('Y');

		// Try to rename the folder
		if (JFolder::exists($path)) {
			$newPath = $path . '_' . $postfix;

			JFolder::move($path, $newPath);
		}

		// Now we need to rename module folders
		$path = JPATH_ROOT . '/templates/' . $template . '/html';
		$pattern = 'mod_easysocial_*';

		$folders = JFolder::folders($path, $pattern, false, true);

		if ($folders) {
			foreach ($folders as $folder) {

				// We need to rename it this way so that in the next update, the backup folder wont be renamed again
				$newPath = str_ireplace('mod_easysocial', 'backups_easysocial', $folder);
				$newPath = $newPath . '_' . $postfix;

				JFolder::move($folder, $newPath);
			}
		}
	}

	/**
	 * Install alert rules
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installAlerts()
	{
		// Get the path to the defaults folder
		$path = JPATH_ADMINISTRATOR . '/components/com_easysocial/defaults/alerts';

		$this->engine();

		// Retrieve the privacy model to scan for the path
		$model 	= FD::model( 'Alert' );

		// Scan and install privacy
		$total 	= 0;
		$files 	= JFolder::files( $path , '.alert' , false , true );

		if( $files )
		{
			foreach( $files as $file )
			{
				$model->install( $file );
				$total 	+= 1;
			}
		}

		return $this->output( $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_ALERT_SUCCESS' , $total ) , true ) );
	}

	/**
	 * Install reactions default data
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function installReactions()
	{
		$this->engine();

		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_reactions');
		$sql->column('id');
		$sql->limit(0, 1);

		$db->setQuery($sql);
		$id = $db->loadResult();

		// We don't have to do anything since there's already a default reactions
		if ($id) {
			$result = $this->getResultObj('Skipping reactions installation since reactions already exists on the site', true);
			return $this->output($result);
		}

		$query = "INSERT INTO `#__social_reactions` (`id`, `action`, `published`, `created`, `params`) VALUES
					(1, 'like', 1, '0000-00-00 00:00:00', ''),
					(2, 'happy', 1, '0000-00-00 00:00:00', ''),
					(3, 'love', 1, '0000-00-00 00:00:00', ''),
					(4, 'angry', 1, '0000-00-00 00:00:00', ''),
					(5, 'wow', 1, '0000-00-00 00:00:00', ''),
					(6, 'sad', 1, '0000-00-00 00:00:00', '');";

		$db->setQuery($query);
		$db->query();

		return $this->output($this->getResultObj(JText::_('Reactions initialized successfully'), true));
	}

	/**
	 * Install privacy items.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installPrivacy()
	{
		if( $this->isDevelopment() )
		{
			return $this->output( $this->getResultObj( 'ok' , true )  );
		}

		// Get the temporary path from the server.
		$tmpPath 		= JRequest::getVar( 'path' );

		// There should be a queries.zip archive in the archive.
		$archivePath 	= $tmpPath . '/privacy.zip';

		// Where the badges should reside after extraction
		$path 			= $tmpPath . '/privacy';

		// Extract badges
		$state 	= JArchive::extract( $archivePath , $path );

		if( !$state )
		{
			return $this->output( $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_PRIVACY' ) , false ) );
		}

		$this->engine();

		// Retrieve the privacy model to scan for the path
		$model 	= FD::model( 'Privacy' );

		// Scan and install privacy
		$totalPrivacy 	= 0;
		$files 			= JFolder::files( $path , '.privacy' , false , true );

		if( $files )
		{
			foreach( $files as $file )
			{
				$model->install( $file );
				$totalPrivacy 	+= 1;
			}
		}

		return $this->output( $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_PRIVACY_SUCCESS' , $totalPrivacy ) , true ) );
	}

	/**
	 * Install access rules on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installAccess()
	{
		if( $this->isDevelopment() )
		{
			return $this->output( $this->getResultObj( 'ok' , true )  );
		}

		$this->engine();

		// Scan and install alert files
		$model 	= FD::model('AccessRules');
		$path 	= JPATH_ADMINISTRATOR . '/components/com_easysocial/defaults/access';
		$files	= JFolder::files($path, '.access$', true, true);

		$totalRules	= 0;

		if ($files) {

			foreach ($files as $file) {

				$model->install($file);

				$totalRules += 1;
			}
		}

		return $this->output( $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_RULES_SUCCESS' , $totalRules ) , true ) );
	}

	/**
	 * Install points on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installPoints()
	{
		if( $this->isDevelopment() )
		{
			return $this->output( $this->getResultObj( 'ok' , true )  );
		}

		// Get the temporary path from the server.
		$tmpPath 		= JRequest::getVar( 'path' );

		// There should be a queries.zip archive in the archive.
		$archivePath 	= $tmpPath . '/points.zip';

		// Where the badges should reside after extraction
		$path 			= $tmpPath . '/points';

		// Extract badges
		$state 	= JArchive::extract( $archivePath , $path );

		if( !$state )
		{
			return $this->output( $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_POINTS' ) , false ) );
		}

		$this->engine();

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Points' );

		// Scan and install badges
		$points = JFolder::files( $path , '.points' , true , true );

		$totalPoints 	= 0;

		if( $points )
		{
			foreach( $points as $point )
			{
				$model->install( $point );

				$totalPoints 	+= 1;
			}
		}

		return $this->output( $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_POINTS_SUCCESS' , $totalPoints ) , true ) );
	}

	/**
	 * Installation of plugins on the site
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function installPlugins()
	{
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('ok', true) );
		}

		$this->engine();

		// Get the path to the current installer archive
		$tmpPath = JRequest::getVar('path');

		// Path to the archive
		$archivePath = $tmpPath . '/plugins.zip';

		// Where should the archive be extrated to
		$path = $tmpPath . '/plugins';

		$state = JArchive::extract($archivePath, $path);

		if (!$state) {
			return $this->output($this->getResultObj(JText::_('COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_PLUGINS'), false));
		}

		// Get a list of apps we should install.
		$groups = JFolder::folders($path, '.', false, true);

		// Get Joomla's installer instance
		$installer = JInstaller::getInstance();

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($groups as $group) {

			// Now we find the plugin info
			$plugins = JFolder::folders( $group , '.' , false , true );
			$groupName = basename($group);
			$groupName = ucfirst($groupName);

			foreach ($plugins as $pluginPath) {

				$pluginName = basename($pluginPath);
				$pluginName = ucfirst($pluginName);

				// We need to try to load the plugin first to determine if it really exists
				$plugin = JTable::getInstance('extension');
				$options = array('folder' => strtolower($groupName), 'element' => strtolower($pluginName));
				$exists = $plugin->load($options);

				// Allow overwriting existing plugins
				$installer->setOverwrite(true);
				$state = $installer->install($pluginPath);

				if (!$exists) {
					$plugin->load($options);
				}


				// Load the plugin and ensure that it's published
				if ($state) {

					// If the plugin was previously disabled, do not turn this on.
					if (($exists && $plugin->enabled) || !$exists) {
						$plugin->state = true;
						$plugin->enabled = true;
					}

					$plugin->store();
				}

				$message = $state ? JText::sprintf('COM_EASYSOCIAL_INSTALLATION_SUCCESS_PLUGIN', $groupName, $pluginName) : JText::sprintf('COM_EASYSOCIAL_INSTALLATION_ERROR_PLUGIN', $groupName, $pluginName);
				$class = $state ? 'success' : 'error';

				$result->message .= '<div class="text-' . $class . '">' . $message . '</div>';
			}
		}

		return $this->output($result);
	}

	/**
	 * Installation of admin modules on the site
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function installAdminModules()
	{
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('ok', true));
		}

		$this->engine();

		// Get the path to the current installer archive
		$tmpPath = JRequest::getVar('path');

		// Path to the archive
		$archivePath = $tmpPath . '/adminmodules.zip';

		if (!JFile::exists($archivePath)) {
			return $this->output($this->getResultObj(JText::_('COM_EASYSOCIAL_INSTALLATION_NO_MODULES_AVAILABLE'), true));
		}

		// Where should the archive be extrated to
		$path = $tmpPath . '/adminmodules';

		$state = JArchive::extract($archivePath, $path);

		if (!$state) {
			return $this->output($this->getResultObj('COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_MODULES', false));
		}

		// We need to exclude mod_easysocial_dummy since this module is added in the admin to satisfy phing's zip task.
		$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'mod_sample', 'mod_easysocial_dummy');
		$modules = JFolder::folders($path, '.', false, true, $exclude);

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		$db = ES::db();
		$sql = $db->sql();

		foreach ($modules as $module) {
			$moduleName = basename($module);

			// Get Joomla's installer instance
			$installer = new JInstaller();

			// Allow overwriting existing plugins
			$installer->setOverwrite(true);
			$state = $installer->install($module);

			if($state) {

				// We need to check if this module record already exists in module_menu or not. if not, lets create one for this module.
				$query = 'select a.`id`, b.`moduleid` from #__modules as a';
				$query .= ' left join `#__modules_menu` as b on a.`id` = b.`moduleid`';
				$query .= ' where a.`module` = ' . $db->Quote($moduleName);
				$query .= ' and b.`moduleid` is null';

				$sql->clear();
				$sql->raw($query);
				$db->setQuery($sql);

				$results = $db->loadObjectList();

				if ($results) {
					foreach ($results as $item) {
						$modMenu = new stdClass();
						$modMenu->moduleid = $item->id;
						$modMenu->menuid = 0;

						$db->insertObject('#__modules_menu', $modMenu);

						$jModule = JTable::getInstance('Module');
						$jModule->load($item->id);
						$jModule->position = 'cpanel';
						$jModule->published = 1;
						$jModule->store();
					}
				}
			}

			// Set the position of the module to cpanel
			$message = $state ? JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_SUCCESS_MODULE' , $moduleName ) : JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_ERROR_MODULE' , $moduleName );

			$class = $state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Installation of modules on the site
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function installModules()
	{
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('ok', true));
		}

		$this->engine();

		// Get the path to the current installer archive
		$tmpPath = JRequest::getVar('path');

		// Path to the archive
		$archivePath = $tmpPath . '/modules.zip';

		if (!JFile::exists($archivePath)) {
			return $this->output($this->getResultObj(JText::_('COM_EASYSOCIAL_INSTALLATION_NO_MODULES_AVAILABLE'), true));
		}
		// Where should the archive be extrated to
		$path = $tmpPath . '/modules';

		$state = JArchive::extract($archivePath, $path);

		if (!$state) {
			return $this->output($this->getResultObj('COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_MODULES', false));
		}

		// Get a list of apps we should install.
		$modules = JFolder::folders( $path , '.' , false , true );

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($modules as $module) {
			$moduleName = basename($module);

			// Get Joomla's installer instance
			$installer = new JInstaller();

			// Allow overwriting existing plugins
			$installer->setOverwrite(true);
			$state = $installer->install($module);

			if($state) {
				$db = FD::db();
				$sql = $db->sql();

				$query = 'update `#__extensions` set `access` = 1';
				$query .= ' where `type` = ' . $db->Quote( 'module' );
				$query .= ' and `element` = ' . $db->Quote( $moduleName );
				$query .= ' and `access` = ' . $db->Quote( '0' );

				$sql->clear();
				$sql->raw( $query );
				$db->setQuery( $sql );
				$db->query();

				// we need to check if this module record already exists in module_menu or not. if not, lets create one for this module.
				$query = 'select a.`id`, b.`moduleid` from #__modules as a';
				$query .= ' left join `#__modules_menu` as b on a.`id` = b.`moduleid`';
				$query .= ' where a.`module` = ' . $db->Quote( $moduleName );
				$query .= ' and b.`moduleid` is null';

				$sql->clear();
				$sql->raw( $query );
				$db->setQuery( $sql );

				$results = $db->loadObjectList();

				if( $results )
				{
					foreach( $results as $item )
					{
						// lets add into module menu.
						$modMenu = new stdClass();
						$modMenu->moduleid 	= $item->id;
						$modMenu->menuid 	= 0;

						$db->insertObject( '#__modules_menu' , $modMenu );
					}
				}

			}

			$message = $state ? JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_SUCCESS_MODULE' , $moduleName ) : JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_ERROR_MODULE' , $moduleName );

			$class = $state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Uninstallation of modules on the site
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function uninstallModules()
	{
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('ok', true));
		}

		$this->engine();

		$modules = array('mod_easysocial_easyblog_posts', 'mod_easysocial_registration_requester');

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($modules as $moduleName) {

			$modulePath = JPATH_ROOT . '/modules/' . $moduleName;

			$state = true;

			if (!JFolder::exists($modulePath)) {
				$message = JText::sprintf('COM_EASYSOCIAL_UNINSTALLATION_NO_MODULE', $moduleName);

				$result->state = true;
				$result->message = '<div class="text-success">' . $message . '</div>';
				return $this->output($result);
			}

			$state = JFolder::delete($modulePath);

			if ($state) {
				$db = ES::db();
				$sql = $db->sql();

				// Remove from extensions table
				$query = 'delete from `#__extensions` ';
				$query .= ' where `type` = ' . $db->Quote('module');
				$query .= ' and `element` = ' . $db->Quote($moduleName);

				$sql->clear();
				$sql->raw($query);
				$db->setQuery($sql);
				$db->query();

				// we need to check if this module record exists in module or not. if yes, delete it.
				$query = 'select `id` from #__modules';
				$query .= ' where `module` = ' . $db->Quote($moduleName);

				$sql->clear();
				$sql->raw($query);
				$db->setQuery($sql);

				$results = $db->loadObjectList();

				if ($results) {
					foreach ($results as $item) {
						// Remove from Modules table
						$query = 'delete from `#__modules` ';
						$query .= ' where `id` = ' . $db->Quote($item->id);

						$sql->clear();
						$sql->raw($query);
						$db->setQuery($sql);
						$db->query();

						// Remove from Module_menu table if any
						$query = 'delete from `#__modules_menu` ';
						$query .= ' where `moduleid` = ' . $db->Quote($item->id);

						$sql->clear();
						$sql->raw($query);
						$db->setQuery($sql);
						$db->query();
					}
				}

			}

			$message = $state ? JText::sprintf('COM_EASYSOCIAL_UNINSTALLATION_SUCCESS_MODULE', $moduleName) : JText::sprintf('COM_EASYSOCIAL_UNINSTALLATION_ERROR_MODULE', $moduleName);

			$class = $state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Install badges on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installBadges()
	{
		if( $this->isDevelopment() )
		{
			return $this->output( $this->getResultObj( 'ok' , true )  );
		}

		// Get the temporary path from the server.
		$tmpPath 		= JRequest::getVar( 'path' );

		// There should be a queries.zip archive in the archive.
		$archivePath 	= $tmpPath . '/badges.zip';

		// Where the badges should reside after extraction
		$path 			= $tmpPath . '/badges';

		// Extract badges
		$state 	= JArchive::extract( $archivePath , $path );

		if( !$state )
		{
			return $this->output( $this->getResultObj( JText::_( 'COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_BADGES' ) , false ) );
		}

		$this->engine();

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Badges' );

		// Scan and install badges
		$badges = JFolder::files( $path , '.badge$' , true , true );

		$totalBadges 	= 0;

		if( $badges )
		{
			foreach( $badges as $badge )
			{
				$model->install( $badge );

				$totalBadges 	+= 1;
			}
		}

		// After installing the badge, copy the badges folder over to ADMIN/com_easysocial/defaults/
		JFolder::copy($path, JPATH_ADMINISTRATOR . '/components/com_easysocial/defaults/badges', '', true);

		return $this->output( $this->getResultObj( JText::sprintf( 'COM_EASYSOCIAL_INSTALLATION_BADGES_SUCCESS' , $totalBadges ) , true ) );
	}

	/**
	 * Performs the installation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function install()
	{
		$item 	= JRequest::getWord( 'item' , '' );

		$method	= 'install' . ucfirst( $item );

		$this->$method();
	}

	/**
	 * Responsible to install apps
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installApps()
	{
		// For development mode, we want to skip all this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('Skipping this step because we are in development mode.', true));
		}

		// Get the group of apps to install.
		$group = JRequest::getVar('group');

		// Get the temporary path to the archive
		$tmpPath = JRequest::getVar('path');

		// Get the archive path
		$archivePath = $tmpPath . '/' . $group . 'apps.zip';

		// Where the extracted items should reside.
		$path = $tmpPath . '/' . $group . 'apps';

		// Detect if the target folder exists
		$target = JPATH_ROOT . '/media/com_easysocial/apps/' . $group;

		// Try to extract the archive first
		$state = JArchive::extract($archivePath, $path);

		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_ERROR_EXTRACT_APPS', $group);

			return $this->output($result);
		}

		// If the apps folder does not exist, create it first.
		$exists = JFolder::exists($target);

		if (!$exists) {
			$state = JFolder::create($target);

			if (!$state) {
				$result = new stdClass();
				$result->state = false;
				$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_ERROR_CREATE_APPS_FOLDER', $target);

				return $this->output($result);
			}
		}

		// Get a list of apps within this folder.
		$apps = JFolder::folders($path, '.', false, true);
		$totalApps 	= 0;

		// If there are no apps to install, just silently continue
		if (!$apps) {
			$result = new stdClass();
			$result->state = true;
			$result->message = JText::_('COM_EASYSOCIAL_INSTALLATION_APPS_NO_APPS');

			return $this->output($result);
		}

		$results = array();

		// Go through the list of apps on the site and try to install them.
		foreach ($apps as $app) {
			$results[] = $this->installApp($app, $target, $group);
			$totalApps += 1;
		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';
			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}

	/**
	 * Installs Single Application
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installApp($appArchivePath, $target, $group = 'user')
	{
		// Get the element of the app
		$element = basename($appArchivePath);
		$element = str_ireplace('.zip', '' , $element);

		// Get the installation source folder.
		$path = dirname($appArchivePath) . '/' . $element;

		// Include core library
		$this->engine();

		// Get installer library
		$installer = ES::get('Installer');

		// Try to load the installation from path.
		$state = $installer->load($path);

		// Try to load and see if the previous app already has a record
		$oldApp = ES::table('App');
		$appExists = $oldApp->load(array('type' => SOCIAL_TYPE_APPS, 'element' => $element, 'group' => $group));

		// If there's an error with this app, we should silently continue
		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_ERROR_LOADING_APP', $element);

			return $result;
		}

		// Let's try to install the app.
		$app = $installer->install();

		// If there's an error with this app, we should silently continue
		if ($app === false) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_ERROR_INSTALLING_APP', $element);

			return $result;
		}

		// If application already exist, use the previous title.
		if ($appExists) {
			$app->title = $oldApp->title;
			$app->alias = $oldApp->alias;
		}

		$app->state = $appExists ? $oldApp->state : SOCIAL_STATE_PUBLISHED;
		$app->store();

		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_APPS_INSTALLED_APP_SUCCESS', $element);

		return $result;
	}

	/**
	 * Responsible to copy the necessary files over.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installCopy()
	{
		$type = JRequest::getVar('type', '');

		// Get the temporary path from the server.
		$tmpPath = JRequest::getVar('path');

		// Get the path to the zip file
		$archivePath = $tmpPath . '/' . $type . '.zip';

		// Where the extracted items should reside
		$path = $tmpPath . '/' . $type;

		// For development mode, we want to skip all this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('Skipping this step because we are in development mode.', true));
		}

		// Extract the admin folder
		$state = JArchive::extract($archivePath, $path);

		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_COPY_ERROR_UNABLE_EXTRACT', $type);

			return $this->output($result);
		}

		// Look for files in this path
		$files = JFolder::files($path, '.', false, true);

		// Look for folders in this path
		$folders = JFolder::folders($path, '.', false, true);

		// Construct the target path first.
		if ($type == 'admin') {
			$target = JPATH_ADMINISTRATOR . '/components/com_easysocial';
		}

		if ($type == 'site') {
			$target = JPATH_ROOT . '/components/com_easysocial';
		}

		// Languages
		if ($type == 'languages') {

			// Admin language files
			$adminPath = JPATH_ADMINISTRATOR . '/language/en-GB';
			$adminSource = $path . '/admin/en-GB.com_easysocial.ini';
			$adminSysSource	= $path . '/admin/en-GB.com_easysocial.sys.ini';

			JFile::copy($adminSource, $adminPath . '/en-GB.com_easysocial.ini');
			JFile::copy($adminSysSource, $adminPath . '/en-GB.com_easysocial.sys.ini');

			// Site language files
			$sitePath = JPATH_ROOT . '/language/en-GB';
			$siteSource = $path . '/site/en-GB.com_easysocial.ini';

			JFile::copy($siteSource, $sitePath . '/en-GB.com_easysocial.ini');

			$result = new stdClass();
			$result->state = true;
			$result->message = JText::_('COM_EASYSOCIAL_INSTALLATION_LANGUAGES_UPDATED');

			return $this->output($result);
		}

		if ($type == 'media') {
			$target = JPATH_ROOT . '/media/com_easysocial';
		}

		// Ensure that the target folder exists
		if (!JFolder::exists($target)) {
			JFolder::create($target);
		}

		// Scan for files in the folder
		$totalFiles = 0;

		foreach ($files as $file) {

			$name = basename($file);
			$targetFile	= $target . '/' . $name;

			// For site's cron.php, we need to ensure that we do not replace it.
			if ($type == 'site' && $name == 'cron.php') {

				// Check if the targets exists
				if (JFile::exists($targetFile)) {
					continue;
				}

			}

			JFile::copy($file, $targetFile);

			$totalFiles += 1;
		}

		// Scan for folders in this folder
		$totalFolders = 0;

		foreach ($folders as $folder) {

			$name = basename($folder);
			$targetFolder = $target . '/' . $name;

			// Try to copy the folder over
			JFolder::copy($folder, $targetFolder, '', true);

			$totalFolders += 1;
		}

		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_COPY_FILES_SUCCESS', $totalFiles, $totalFolders);

		return $this->output($result);
	}

	/**
	 * Perform installation of SQL queries
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function installSQL()
	{
		// Get the temporary path from the server.
		$tmpPath = JRequest::getVar('path');

		// There should be a queries.zip archive in the archive.
		$tmpQueriesPath = $tmpPath . '/queries.zip';

		// Extract the queries
		$path = $tmpPath . '/queries';

		// Check if this folder exists.
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}

		$state = JArchive::extract($tmpQueriesPath, $path);

		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::_('COM_EASYSOCIAL_INSTALLATION_ERROR_UNABLE_EXTRACT_QUERIES');

			return $this->output($result);
		}

		// Check for utf8mb4 queries files
		$utf8mb4Path = $path . '/utf8mb4';
		$utf8mb4QueryFiles = JFolder::files($utf8mb4Path, '.', false, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', '.php'));

		// Let's check if the db version support utf8mb4 or not
		if ($utf8mb4QueryFiles) {
			$jConfig = JFactory::getConfig();
			$dbType = $jConfig->get('dbtype');
			$columnExist = true;

			if ($dbType == 'mysql' || $dbType == 'mysqli' || $dbType == 'pdomysql') {
				$db = JFactory::getDBO();

				$dbversion = $db->getVersion();
				$dbversion = (float) $dbversion;

				// db version supported. Let's run the queries
				if ($dbversion >= '5.5') {

					foreach ($utf8mb4QueryFiles as $utf8mb4QueryFile) {
						$contents = JFile::read($utf8mb4QueryFile);
						$queries = JInstallerHelper::splitSql($contents);

						foreach ($queries as $query) {
							$query = trim($query);

							if (!empty($query)) {
								$db->setQuery($query);
								$db->execute();
							}
						}
					}
				}
			}
		}

		// Get the list of files in the folder.
		$queryFiles = JFolder::files($path, '.', false, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', '.php'));

		// When there are no queries file, we should just display a proper warning instead of exit
		if (!$queryFiles) {
			$result = new stdClass();
			$result->state = true;
			$result->message = JText::_('COM_EASYSOCIAL_INSTALLATION_ERROR_EMPTY_QUERIES_FOLDER');

			return $this->output($result);
		}

		$db = JFactory::getDBO();
		$total = 0;

		foreach ($queryFiles as $file) {
			$contents = JFile::read($file);
			$queries = $db->splitSql($contents);

			foreach ($queries as $query) {
				$query = trim($query);

				if (!empty($query)) {
					$db->setQuery($query);
					$db->execute();
				}
			}

			$total 	+= 1;
		}

		// for upgrades, we need to unsure #__social_users.verified is already installed.
		$this->installUserVerifiedColumn();
		$this->installUserSocialParamColumn();

		$result = new stdClass();
		$result->state = true;
		$result->message = JText::sprintf('COM_EASYSOCIAL_INSTALLATION_SQL_EXECUTED_SUCCESS', $total);

		return $this->output($result);
	}

	/**
	 * Downloads the file from the server
	 *
	 * @since	2.0.9
	 * @access	public
	 */
	public function download()
	{
		$info = $this->getInfo();

		if (!$info) {
			$result = new stdClass();
			$result->state = false;
			$result->message = JText::_('COM_EASYSOCIAL_INSTALLATION_ERROR_REQUEST_INFO');

			$this->output($result);
			exit;
		}

		// If our server returns any error messages other than the standard ones
		if (isset($info->error) && $info->error != 408) {
			$result = new stdClass();
			$result->state = false;
			$result->message = $info->error;

			$this->output($result);
			exit;
		}

		// If it hits any error from the server, skip this
		if (isset($info->error) && $info->error == 408) {
			$result = new stdClass();
			$result->state = false;
			$result->message = $info->message;

			$this->output($result);
			exit;
		}

		// Download the component installer.
		$storage = $this->getDownloadFile($info);

		// This only happens when there is no result returned from the server
		if ($storage === false) {
			$result = new stdClass();
			$result->state = false;
			$result->message = 'There was some errors when downloading the file from the server.';

			$this->output($result);
			exit;
		}

		// Extract files here.
		$tmp = ES_TMP . '/com_easysocial_v' . $info->version;

		if (JFolder::exists($tmp)) {
			JFolder::delete($tmp);
		}

		// Try to extract the files
		$state = JArchive::extract($storage, $tmp);

		// If there is an error extracting the zip file, then there is a possibility that the server returned a json string
		if (!$state) {

			$contents = JFile::read($storage);
			$result = json_decode($contents);

			if (is_object($result)) {
				$result->state = false;
				$this->output($result);
				exit;
			}

			$result = new stdClass();
			$result->state = false;
			$result->message = 'There was some errors when extracting the archive from the server. If the problem still persists, please contact our support team.<br /><br /><a href="https://stackideas.com/forums" class="btn btn-default" target="_blank">Contact Support</a>';

			$this->output($result);
			exit;
		}


		// Get the md5 hash of the stored file
		$hash = md5_file($storage);

		// Check if the md5 check sum matches the one provided from the server.
		if (!in_array($hash, $info->md5)) {
			$result = new stdClass();
			$result->state = false;
			$result->message = 'The MD5 hash of the downloaded file does not match. Please contact our support team to look into this.<br /><br /><a href="https://stackideas.com/forums" class="btn btn-default" target="_blank">Contact Support</a>';

			$this->output($result);
			exit;
		}

		// After installation is completed, cleanup all zip files from the site
		$this->cleanupZipFiles(dirname($storage));

		$result = new stdClass();
		$result->message = 'Installation file downloaded successfully';
		$result->state = $state;
		$result->path = $tmp;

		$this->output($result);
	}

	/**
	 * Allows cleanup of installation files
	 *
	 * @since	1.3
	 * @access	public
	 */
	private function cleanupZipFiles($path)
	{
		$zipFiles = JFolder::files($path, '.zip', false, true);

		if ($zipFiles) {
			foreach ($zipFiles as $file) {
				@JFile::delete($file);
			}
		}

		return true;
	}

	/**
	 * For users who uploaded the installer and needs a manual extraction
	 *
	 * @since	2.0.9
	 * @access	public
	 */
	public function extract()
	{
		// Check the api key from the request
		$apiKey = JRequest::getVar('apikey', '');

		// Construct the storage path
		$storage = ES_PACKAGES . '/' . ES_PACKAGE;
		$exists = JFile::exists($storage);

		// Test if package really exists
		if (!$exists) {
			$result = new stdClass();
			$result->state = false;
			$result->message = 'The component package does not exist on the site.<br />Please contact our support team to look into this.';

			$this->output($result);
			exit;
		}

		// Get the folder name
		$folderName = basename($storage);
		$folderName = str_ireplace('.zip', '', $folderName);

		// Extract files here.
		$tmp = ES_TMP . '/' . $folderName;

		// Ensure that there is no such folders exists on the site
		if (JFolder::exists($tmp)) {
			JFolder::delete($tmp);
		}

		// Try to extract the files
		$state = JArchive::extract($storage, $tmp);

		// Regardless of the extraction state, delete the zip file otherwise anyone can download the zip file.
		@JFile::delete($storage);

		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = 'There was some errors when extracting the zip file';

			$this->output($result);
			exit;
		}

		$result = new stdClass();

		$result->message = 'Installation archive extracted successfully';
		$result->state = $state;
		$result->path = $tmp;

		$this->output($result);
	}

	/**
	 * Downloads the installation files from our installation API
	 *
	 * @since	2.0.9
	 * @access	public
	 */
	public function getDownloadFile($info)
	{
		$ch = curl_init(ES_DOWNLOADER);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . ES_KEY . '&version=' . $info->version);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 35000);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$contents = curl_exec($ch);
		curl_close($ch);

		// Set the storage page
		$storage = ES_PACKAGES . '/easysocial_v' . $info->version . '_component.zip';

		// Delete zip archive if it already exists.
		if (JFile::exists($storage)) {
			JFile::delete($storage);
		}

		$state = JFile::write($storage, $contents);

		if (!$state || !$contents) {
			return false;
		}

		return $storage;
	}

	/**
	 * Installs fields based on group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function installFields()
	{
		// For development mode, we want to skip all this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('ok', true));
		}

		// Get the group of apps to install.
		$group = JRequest::getVar('group');

		// Get the temporary path to the archive
		$tmpPath = JRequest::getVar('path');

		// Get the archive path
		$archivePath = $tmpPath . '/' . $group . 'fields.zip';

		// Where the extracted items should reside.
		$path = $tmpPath . '/' . $group . 'fields';

		// Detect if the target folder exists
		$target = JPATH_ROOT . '/media/com_easysocial/apps/fields/' . $group;

		// Try to extract the archive first
		$state = JArchive::extract( $archivePath , $path );

		if (!$state) {
			$result = new stdClass();
			$result->state = false;
			$result->message = "There was some errors when extracting fields.zip. Please check folder's permission.";

			return $this->output($result);
		}

		// If the apps folder does not exist, create it first.
		if (!JFolder::exists($target)) {
			$state 	= JFolder::create( $target );

			if (!$state) {
				$result = new stdClass();
				$result->state = false;
				$result->message = JText::sprintf('There was some permission errors when trying to create the folder below:<br /><br />%1%s', $target);

				return $this->output($result);
			}
		}

		// Get a list of apps within this folder.
		$fields = JFolder::folders( $path , '.' , false , true );
		$totalFields = 0;

		// If there are no apps to install, just silently continue
		if (!$fields) {
			$result = new stdClass();
			$result->state = true;
			$result->message = 'There are no fields to be installed currently. Skipping this.';

			return $this->output($result);
		}

		$results = array();

		// Go through the list of apps on the site and try to install them.
		foreach ($fields as $field) {
			$results[] = $this->installField($field, $group);
			$totalFields += 1;
		}

		$result = new stdClass();
		$result->state = true;
		$result->message = '';

		foreach ($results as $obj) {
			$class = $obj->state ? 'success' : 'error';

			$result->message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		return $this->output($result);
	}
}
