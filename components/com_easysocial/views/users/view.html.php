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

ES::import('site:/views/views');

class EasySocialViewUsers extends EasySocialSiteView
{
	/**
	 * Displays a list of users on the site
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Retrieve the users model
		$model = ES::model('Users');

		$admin = $this->config->get('users.listings.admin') ? true : false;
		$options = array('includeAdmin' => $admin, 'exclusion' => $this->my->id);

		// Get the limit of total users to be displayed
		$limit = ES::getLimit('userslimit');
		$options['limit'] = $limit;

		$fid = 0;
		$filter = $this->input->get('filter', 'all', 'word');
		$sort = $this->input->get('sort', $this->config->get('users.listings.sorting'), 'word');

		// Do not display profile by default
		$profile = false;

		// Default title
		$title = 'COM_EASYSOCIAL_PAGE_TITLE_USERS';
		$breadcrumb = $title;

		// Set the sorting options
		$prefix = $filter == 'search' ? '' : 'a.';

		if ($sort == 'alphabetical') {
			$nameField = $this->config->get('users.displayName') == 'username' ? $prefix . 'username' : $prefix . 'name';

			$options['ordering'] = $nameField;
			$options['direction'] = 'ASC';

			$title = 'COM_ES_PAGE_TITLE_USERS_SORTED_BY_NAME';
		} else if ($sort == 'latest') {
			$options['ordering'] = $prefix . 'id';
			$options['direction'] = 'DESC';

			$title = 'COM_ES_PAGE_TITLE_USERS_SORTED_BY_RECENTLY_REGISTERED';
		} elseif ($sort == 'lastlogin') {
			$options['ordering'] = $prefix . 'lastvisitDate';
			$options['direction'] = 'DESC';

			$title = 'COM_ES_PAGE_TITLE_USERS_SORTED_BY_RECENTLY_LOGGED_IN';
		}

		$title = JText::_($title);

		$searchFilter = '';
		$displayOptions = '';

		if ($filter == 'search') {

			// search filter id
			$fid = $this->input->get('id', 0, 'int');

			$searchFilter = ES::table('SearchFilter');
			$searchFilter->load($fid);

			// Retrieve the users
			$result = $model->getUsersByFilter($fid, $options);
			$pagination = $model->getPagination();

			$displayOptions = $model->getDisplayOptions();

			// let reset the page title here
			$title = $searchFilter->get('title');

		} else if ($filter == 'profiletype') {

			// Get the profile object
			$fid = $this->input->get('id', 0, 'int');

			$profile = ES::table('Profile');
			$profile->load($fid);

			if (!$fid || !$profile->id) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_404_PROFILE_NOT_FOUND'));
			}

			$options['profile']	= $fid;

			// we only want published user.
			$options['published'] = 1;

			// exclude users who blocked the current logged in user.
			$options['excludeblocked'] = 1;

			$values = array();
			$values['criterias'] = $this->input->getVar('criterias');
			$values['datakeys'] = $this->input->getVar('datakeys');
			$values['operators'] = $this->input->getVar('operators');
			$values['conditions'] = $this->input->getVar('conditions');

			if ($values['criterias']) {

				// lets do some clean up here.
				for ($i = 0; $i < count($values['criterias']); $i++) {
					$criteria = $values['criterias'][$i];
					$condition = $values['conditions'][$i];
					$datakey = $values['datakeys'][$i];
					$operator = $values['operators'][$i];

					if (trim($condition)) {
						$searchOptions['criterias'][] = $criteria;
						$searchOptions['datakeys'][] = $datakey;
						$searchOptions['operators'][] = $operator;

						$field = explode('|', $criteria);

						$fieldCode = $field[0];
						$fieldType = $field[1];

						if ($fieldType == 'birthday') {

							// currently the value from form is in age format. we need to convert it into date time.
							$ages = explode('|', $condition);

							// this happen when start has value and end has no value
							if (!isset($ages[1])) {
								$ages[1] = $ages[0];
							}

							//this happen when start is empty and end has value
							if ($ages[1] && !$ages[0]) {
								$ages[0] = $ages[1];
							}

							$startdate = '';
							$enddate = '';

							$currentTimeStamp = ES::date()->toUnix();

							if ($ages[0] == $ages[1]) {
								$start = strtotime('-' . $ages[0] . ' years', $currentTimeStamp);

								$year = ES::date($start)->toFormat('Y');
								$startdate = $year . '-01-01 00:00:01';
								$enddate = ES::date($start)->toFormat('Y-m-d') . ' 23:59:59';
							} else {

								if ($ages[0]) {
									$start = strtotime('-' . $ages[0] . ' years', $currentTimeStamp);
									$year = ES::date($start)->toFormat('Y');
									$enddate = $year . '-12-31 23:59:59';
								}

								if ($ages[1]) {
									$end = strtotime('-' . $ages[1] . ' years', $currentTimeStamp);
									$year = ES::date($end)->toFormat('Y');
									$startdate = $year . '-01-01 00:00:01';
								}
							}

							$condition = $startdate . '|' . $enddate;
						}

						$searchOptions['conditions'][] = $condition;
					}

				}

				$searchOptions['match'] = 'and';
				$searchOptions['avatarOnly'] = false;

				if ($fid) {
					$searchOptions['profile'] = $fid;
				}

				$result = $model->getUsersByFilter('0', $options, $searchOptions);

			} else {
				// Retrieve the users
				$result = $model->getUsers($options);
			}

			$pagination = $model->getPagination();

			// let reset the page title here
			$title = $profile->get('title');

		} else {

			if ($filter == 'online' || $filter == 'photos' || $filter == 'blocked' || $filter == 'all' ) {

				// Need to exclude the current logged in user.
				$option['exclusion'] = $this->my->id;

				if ($filter == 'online') {
					$title = JText::_('COM_EASYSOCIAL_PAGE_TITLE_USERS_ONLINE_USERS');
					$breadcrumb = $title;
					$options['login'] = true;
				}

				if ($filter == 'photos') {
					$title = JText::_('COM_EASYSOCIAL_PAGE_TITLE_USERS_WITH_PHOTOS');
					$breadcrumb = $title;
					$options['picture'] = true;
				}

				if ($filter == 'blocked') {
					$title = JText::_('COM_EASYSOCIAL_PAGE_TITLE_USERS_BLOCKED');
					$breadcrumb = $title;
					$options['blocked'] = true;
				}

				// we only want published user.
				$options['published'] = 1;

				// exclude users who blocked the current logged in user.
				$options['excludeblocked'] = 1;

				// Retrieve the users
				$result = $model->getUsers($options);
				$pagination = $model->getPagination();
			}
		}

		$this->page->title($title);
		$this->page->breadcrumb($breadcrumb);

		// Add canonical tags for users page
		$this->page->canonical(ESR::users(array('external' => true)));

		// Retrieve a list of profile types on the site
		$profilesModel = ES::model('Profiles');

		// TODO: make this into setting.
		$showProfilesCount = $this->config->get('users.listings.profilescount', 0);

		$profiles = $profilesModel->getProfiles(array('state' => SOCIAL_STATE_PUBLISHED, 'includeAdmin' => $admin, 'excludeESAD' => true, 'validUser' => true, 'showCount' => $showProfilesCount));

		// Define those query strings here
		if ($filter != 'profiletype' && $filter != 'search') {
			$pagination->setVar('filter', $filter);
			$pagination->setVar('sort', $sort);
		}

		$userIds = array();
		$users = array();

		foreach ($result as $obj) {
			$userIds[] = $obj->id;
			$users[] = ES::user($obj->id);
		}

		// bind / set the fields_data into cache for later reference.
		// the requirement is to ES::user() first before you can call this setUserFieldsData();
		$model->setUserFieldsData($userIds);

		// get sitewide search filter
		$searchModel = ES::model('Search');
		$searchFilters = $searchModel->getSiteWideFilters();

		// $showSort = $filter == 'profiletype' || $filter == 'search' ? false : true;
		$showSort = true;

		// Build sorting url
		$sortItems = new stdClass();
		$sortingTypes = array('latest', 'lastlogin', 'alphabetical');

		foreach ($sortingTypes as $sortingType) {

			$sortItems->{$sortingType} = new stdClass();

			// attributes
			$sortAttributes = array('data-sort', 'data-filter="' . $filter . '"', 'data-type="' . $sortingType . '"');

			// url
			$urlOptions = array();
			$urlOptions['filter'] = $filter;
			$urlOptions['sort'] = $sortingType;

			if (isset($fid) && $fid) {
				$urlOptions['id'] = $fid;
			}

			$sortUrl = ESR::users($urlOptions);

			$sortItems->{$sortingType}->attributes = $sortAttributes;
			$sortItems->{$sortingType}->url = $sortUrl;
		}

		$createCustomFilter = false;

		if ($this->my->isSiteAdmin()) {
			$createCustomFilter = array('link' => ESR::search(array('layout' => 'advanced')), 'icon' => 'fa-plus');
		}

		$this->set('createCustomFilter', $createCustomFilter);
		$this->set('sortItems', $sortItems);
		$this->set('showSort', $showSort);
		$this->set('profiles', $profiles);
		$this->set('activeProfile', $profile);
		$this->set('activeTitle', $title);
		$this->set('pagination', $pagination);
		$this->set('filter', $filter);
		$this->set('sort', $sort);
		$this->set('users', $users);
		$this->set('fid', $fid);
		$this->set('searchFilters', $searchFilters);
		$this->set('searchFilter', $searchFilter);
		$this->set('displayOptions', $displayOptions);

		echo parent::display('site/users/default/default');
	}

	/**
	 * Displays a list of users on the site from dating search module
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function search($tpl = null)
	{
		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Retrieve the users model
		$model = ES::model('Users');

		$config = ES::config();
		$admin = $config->get('users.listings.admin') ? true : false;
		$options = array('includeAdmin' => $admin);

		$limit = ES::getLimit('userslimit');
		$options['limit'] = $limit;

		// Default title
		$title = JText::_('COM_EASYSOCIAL_PAGE_TITLE_USERS');

		$post = JRequest::get('POSTS');

		$sort = $this->input->get('sort', '', 'default');

		// Get values from posted data
		$values = array();
		$values['criterias'] = JRequest::getVar('criterias');
		$values['datakeys'] = JRequest::getVar('datakeys');
		$values['operators'] = JRequest::getVar('operators');
		$values['conditions'] = JRequest::getVar('conditions');

		$avatarOnly = JRequest::getVar('avatarOnly', false);
		$onlineOnly = JRequest::getVar('onlineOnly', false);

		$searchOptions = array();

		// lets do some clean up here.
		for ($i = 0; $i < count($values['criterias']); $i++) {
			$criteria = $values['criterias'][$i];
			$condition = $values['conditions'][$i];
			$datakey = $values['datakeys'][$i];
			$operator = $values['operators'][$i];

			if ($datakey == 'name' && $this->config->get('search.minimum')) {
				$length = JString::strlen($condition);

				if ($length < $this->config->get('search.characters')) {
					ES::info()->set(null, JText::sprintf('COM_ES_MIN_CHARACTERS_SEARCH', $this->config->get('search.characters')), SOCIAL_MSG_ERROR);

					$pagination = null;
					$result = null;
					$users = array();

					$displayOptions = $model->getDisplayOptions();

					$this->page->title($title);
					$this->page->breadcrumb($title);

					$filter = 'search';

					$createCustomFilter = false;

					if ($this->my->isSiteAdmin()) {
						$createCustomFilter = array('link' => ESR::search(array('layout' => 'advanced')), 'icon' => 'fa-plus');
					}

					$this->set('createCustomFilter', $createCustomFilter);
					$this->set('showSort', false);
					$this->set('issearch', true);
					$this->set('profiles', '');
					$this->set('activeProfile', '');
					$this->set('profile', '');
					$this->set('activeTitle', $title);
					$this->set('pagination', $pagination);
					$this->set('filter', $filter);
					$this->set('sort', $sort);
					$this->set('users', $users);
					$this->set('fid', '');
					$this->set('searchFilters', '');
					$this->set('searchFilter', '');
					$this->set('displayOptions', $displayOptions);

					return parent::display('site/users/default/default');
				}
			}

			if (trim($condition)) {
				$searchOptions['criterias'][] = $criteria;
				$searchOptions['datakeys'][] = $datakey;
				$searchOptions['operators'][] = $operator;

				$field = explode('|', $criteria);

				$fieldCode = $field[0];
				$fieldType = $field[1];

				if ($fieldType == 'birthday') {
					// currently the value from form is in age format. we need to convert it into date time.
					$ages = explode('|', $condition);

					$startdate = '';
					$enddate = '';

					$currentTimeStamp = ES::date()->toUnix();

					if (isset($ages[0]) && isset($ages[1]) && $ages[0] == $ages[1]) {
						$start = strtotime('-' . $ages[0] . ' years', $currentTimeStamp);

						$year = ES::date($start)->toFormat('Y');
						$startdate = $year . '-01-01 00:00:01';
						$enddate = ES::date($start)->toFormat('Y-m-d') . ' 23:59:59';
					} else {

						if (isset($ages[0]) && $ages[0]) {
							$start = strtotime('-' . $ages[0] . ' years', $currentTimeStamp);

							$year = ES::date($start)->toFormat('Y');
							$enddate = $year . '-12-31 23:59:59';
						}

						if (isset($ages[1]) && $ages[1]) {
							$end = strtotime('-' . $ages[1] . ' years', $currentTimeStamp);

							$year = ES::date($end)->toFormat('Y');
							$startdate = $year . '-01-01 00:00:01';
						}
					}

					$condition = $startdate . '|' . $enddate;
				}

				$searchOptions['conditions'][] = $condition;
			}
		}

		$pagination = null;
		$result = null;
		$users = array();

		if ($searchOptions) {
			$searchOptions['match'] = 'all';
			$searchOptions['avatarOnly'] = $avatarOnly;
			$searchOptions['onlineOnly'] = $onlineOnly;
			$searchOptions['sort'] = $sort;

			// Retrieve the users
			$result = $model->getUsersByFilter('0', $options, $searchOptions);
			$pagination = $model->getPagination();

			$itemId = $this->input->get('Itemid', ESR::getItemId('users'), 'int');

			$pagination->setVar('Itemid', $itemId);
			$pagination->setVar('view', 'users');
			$pagination->setVar('layout', 'search');
			$pagination->setVar('filter', 'search');
			$pagination->setVar('option', 'com_easysocial');

			if ($avatarOnly) {
				$pagination->setVar('avatarOnly', $avatarOnly);
			}

			if ($onlineOnly) {
				$pagination->setVar('onlineOnly', $onlineOnly);
			}

			for ($i = 0; $i < count($values['criterias']); $i++) {

				$criteria = $values['criterias'][$i];
				$condition = $values['conditions'][$i];
				$datakey = $values['datakeys'][$i];
				$operator = $values['operators'][$i];

				$field = explode('|', $criteria);

				$fieldCode = $field[0];
				$fieldType = $field[1];

				$pagination->setVar('criterias[' . $i . ']' , $criteria);
				$pagination->setVar('datakeys[' . $i . ']' , $datakey);
				$pagination->setVar('operators[' . $i . ']' , $operator);
				$pagination->setVar('conditions[' . $i . ']' , $condition);
			}

			if ($result) {
				foreach ($result as $obj) {
					$users[] = ES::user($obj->id);
				}
			}
		}

		$displayOptions = $model->getDisplayOptions();

		// Set the page title
		$this->page->title($title);

		// Set the page breadcrumb
		$this->page->breadcrumb($title);

		$filter = 'search';

		$createCustomFilter = false;

		if ($this->my->isSiteAdmin()) {
			$createCustomFilter = array('link' => ESR::search(array('layout' => 'advanced')), 'icon' => 'fa-plus');
		}

		$this->set('createCustomFilter', $createCustomFilter);
		$this->set('showSort', false);
		$this->set('issearch', true);
		$this->set('profiles', '');
		$this->set('activeProfile', '');
		$this->set('profile', '');
		$this->set('activeTitle', $title);
		$this->set('pagination', $pagination);
		$this->set('filter', $filter);
		$this->set('sort', $sort);
		$this->set('users', $users);
		$this->set('fid', '');
		$this->set('searchFilters', '');
		$this->set('searchFilter', '');
		$this->set('displayOptions', $displayOptions);

		return parent::display('site/users/default/default');
	}
}
