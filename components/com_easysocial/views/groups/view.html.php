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

class EasySocialViewGroups extends EasySocialSiteView
{
	/**
	 * Checks if this feature should be enabled or not.
	 *
	 * @since	1.2
	 * @access	private
	 */
	private function checkFeature()
	{
		// Do not allow user to access groups if it's not enabled
		if (!$this->config->get('groups.enabled')) {
			$this->setMessage('COM_EASYSOCIAL_GROUPS_DISABLED', SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());
			$this->redirect(ESR::dashboard(array(), false));
		}
	}

	/**
	 * Renders the about view for groups
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function about($group)
	{
		$groupsModel = ES::model('Groups');
		$steps = $groupsModel->getAbout($group);

		$this->set('layout', 'info');
		$this->set('steps', $steps);

		return parent::display('site/groups/about/default');
	}

	/**
	 * Renders the app view for groups
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function app($group, $app)
	{
		// Ensure that the group really can access the app
		if (!$app->hasAccess($group->category_id)) {
			return $this->exception('COM_EASYSOCIAL_GROUP_DOES_NOT_HAVE_ACCESS');
		}

		$app->loadCss();

		$this->page->title($group->getName() . ' - ' . $app->_('title'));
		$this->page->breadcrumb($app->_('title'));

		// Load the library.
		$lib = ES::apps();
		$contents = $lib->renderView(SOCIAL_APPS_VIEW_TYPE_EMBED, 'groups', $app, array('groupId' => $group->id));

		$layout = 'apps.' . $app->element;

		// For members we need to update the correct layout
		if ($app->element == 'members') {
			$layout = 'members';
		}

		$this->set('layout', $layout);
		$this->set('contents', $contents);

		return parent::display('site/groups/app/default');
	}

	/**
	 * Default method to display the all groups page.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check if the group features are available on the site
		$this->checkFeature();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Set Meta data
		ES::setMeta();

		// Set the document properties
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_GROUPS');
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_GROUPS');
		$this->page->canonical(ESR::groups(array('external' => true)));

		// Determines if we should render groups created by a specific user
		$userId = $this->input->get('userid', 0, 'int');
		$userId = !$userId ? null : $userId;
		$user = ES::user($userId);

		// If there is a user id, means we are viewing profile's group listing
		// $browseView: is a listing view of all group.
		$browseView = true;

		// If no uid, means user is viewing the browsing all pages view
		// We define this browse view same like $showsidebar.
		// so it won't break when other customer that still using $showsidebar
		if ($userId && $user->id) {
			$browseView = false;
		}

		$activeUserId = $userId;

		// If we are viewing profile's page listing
		// Default the $filter to 'created'
		$defaultFilter = $browseView ? 'all' : 'created';

		// Only allow filters that we know.
		$filter = $this->input->get('filter', $defaultFilter, 'cmd');
		$allowedFilters = array('all', 'invited', 'mine', 'featured', 'pending', 'nearby', 'created', 'participated');

		if (!empty($filter) && !in_array($filter, $allowedFilters)) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID'));
		}

		$model = ES::model('Groups');
		$options = array('state' => SOCIAL_STATE_PUBLISHED);

		// Only exclude featured group when viewing all groups
		if ($browseView) {
			$options['featured'] = false;
		}

		// If user is site admin, they should be able to see everything.
		$options['types'] = $this->my->isSiteAdmin() ? 'all' : 'user';

		// If in profile group listing, show only group created by that user
		// except in participated filter. because we need to show others' group as well
		if (!$browseView) {
			if ($filter != 'participated') {
				$options['uid'] = $user->id;
			} else {
				$options['userid'] = $user->id;
				$options['types'] = 'participated';
			}
		}

		$hasLocation = false;

		// Check if there is any location data
		$userLocation = JFactory::getSession()->get('groups.userlocation', array(), SOCIAL_SESSION_NAMESPACE);
		$distance = 10;
		$showDistance = false;
		$showDistanceSorting = false;

		// Default snackbar heading
		$heading = 'COM_EASYSOCIAL_GROUPS';

		// Filter by nearby location
		if ($filter === 'nearby') {
			$distance = $this->input->get('distance', 10, 'int');

			if (!empty($distance) && $distance != 10) {
				$routeOptions['distance'] = $distance;
			}

			$title = JText::sprintf('COM_EASYSOCIAL_EVENTS_IN_DISTANCE_RADIUS', $distance, $this->config->get('general.location.proximity.unit'));

			$hasLocation = !empty($userLocation) && !empty($userLocation['latitude']) && !empty($userLocation['longitude']);

			// If there is no location, then we need to delay the event retrieval process
			$delayed = !$hasLocation ? true : false;

			// We do not want to display sorting by default
			$showSorting = false;

			if ($hasLocation) {
				$options['location'] = true;
				$options['distance'] = $distance;
				$options['latitude'] = $userLocation['latitude'];
				$options['longitude'] = $userLocation['longitude'];
				$options['range'] = '<=';

				$showDistance = true;
				$showDistanceSorting = true;

				$heading = JText::sprintf('COM_ES_GROUPS_IN_RADIUS', $distance, $this->config->get('general.location.proximity.unit'));
			}
		}

		// Determine the pagination limit
		$limit = ES::getLimit('groups_limit');
		$options['limit'] = $limit;

		// Determine if this is filtering groups by category
		$categoryId = $this->input->get('categoryid', 0, 'int');
		$activeCategory = false;

		if ($categoryId) {
			$activeCategory = ES::table('GroupCategory');
			$state = $activeCategory->load($categoryId);

			if (!$state) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_INVALID_CATEGORY_ID'));
			}

			$options['category'] = $activeCategory->id;

			// check if this category is a container or not
			if ($activeCategory->container) {
				// Get all child ids from this category
				$categoryModel = ES::model('ClusterCategory');
				$childs = $categoryModel->getChildCategories($activeCategory->id, array(), SOCIAL_TYPE_GROUP, array('state' => SOCIAL_STATE_PUBLISHED));

				$childIds = array();

				foreach ($childs as $child) {
					$childIds[] = $child->id;
				}

				if (!empty($childIds)) {
					$options['category'] = $childIds;
				}
			}

			$this->page->title($activeCategory->getTitle());
		}

		// Since not logged in users cannot filter by 'invited' or 'mine', they shouldn't be able to access these filters at all
		if ($this->my->guest && ($filter == 'invited' || $filter == 'mine')) {
			return $this->app->redirect(ESR::dashboard(array(), false));
		}

		// If the default filter is invited, we only want to fetch groups that the user has been
		// invited to.
		if ($filter == 'invited') {
			$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_INVITED');

			$options['invited'] = $this->my->id;
			$options['types'] = 'all';
			$options['featured'] = '';
		}

		// Filter by own groups
		if ($filter == 'mine') {
			$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_MY_GROUPS');

			// if we are fetching own groups, we just need to enabled featured flags, but we do not need to fetch the featured groups separately
			// so that when viewing, we will not see two sections.
			$options['featured'] = '';
			$options['state'] = SOCIAL_STATE_PUBLISHED;
			$options['uid'] = $this->my->id;
			$options['types'] = 'participated';
		}

		if ($filter == 'pending') {
			$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_PENDING');

			// groups that pending user's review.
			$options['uid'] = $this->my->id;
			$options['state'] = SOCIAL_CLUSTER_DRAFT;
			$options['types'] = 'user';
		}

		// Get ordering option if any
		$ordering = $this->input->get('ordering', 'latest', 'cmd');
		$options['ordering'] = $ordering;

		$groups = array();

		// Get a list of groups
		if ($filter == 'featured') {
			$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_FEATURED');
			$options['featured'] = true;
		}

		$groups = $model->getGroups($options);
		$pagination = $model->getPagination();

		// Get a list of featured groups
		$featuredGroups = array();
		$showAllFeatured = false;

		if ($filter == 'all' || $categoryId) {
			$options['featured'] = true;
			$featuredGroups	= $model->getGroups($options);
		}

		$counters = new stdClass();

		// Get total number of featured groups on the site
		$counters->totalFeaturedGroups = $model->getTotalGroups(array('featured' => true));

		// Get total number of groups on the site
		$counters->totalGroups = $model->getTotalGroups(array('types' => $this->my->isSiteAdmin() ? 'all' : 'user'));

		// Determine if we should show all link underneath featured group listings.
		if (!empty($featuredGroups) && $counters->totalFeaturedGroups > count($featuredGroups)) {
			$showAllFeatured = true;
		}

		if ($this->my->id != 0) {
			// Get the total number of groups the user created
			$counters->totalCreatedGroups = $this->my->getTotalGroupsCreated();

			// Get the total number of groups the user participated
			$counters->totalParticipatedGroups = $this->my->getTotalGroups();

			// Get the total number of groups the user created but required review
			$counters->totalPendingGroups = $this->my->getTotalPendingReview(SOCIAL_TYPE_GROUP);

			// Get total number of invitations
			$counters->totalInvites = $model->getTotalInvites($this->my->id);
		}

		if (!$browseView) {
			$counters->totalCreatedGroups = $user->getTotalGroupsCreated();
			$counters->totalParticipatedGroups = $model->getTotalParticipatedGroups($user->id);
		}

		// Get the sorting urls
		$sortItems = new stdClass();
		$sortItems->latest = new stdClass();
		$sortItems->name = new stdClass();
		$sortItems->popular = new stdClass();

		$sortAttributes = array('data-sorting', 'data-filter="' . $filter . '"');
		$sortUrls = array('filter' => $filter);

		if ($activeCategory) {
			$sortAttributes[] = 'data-id="' . $activeCategory->id . '"';
			$sortUrls['categoryid'] = $activeCategory->getAlias();
		}

		$sortItems->latest->attributes = array_merge($sortAttributes, array('data-type="latest"'));
		$sortItems->latest->url = ESR::groups(array_merge($sortUrls, array('ordering' => 'latest')));

		$sortItems->name->attributes = array_merge($sortAttributes, array('data-type="name"'));
		$sortItems->name->url = ESR::groups(array_merge($sortUrls, array('ordering' => 'name')));

		$sortItems->popular->attributes = array_merge($sortAttributes, array('data-type="popular"'));
		$sortItems->popular->url = ESR::groups(array_merge($sortUrls, array('ordering' => 'popular')));

		// Only display sidebar when viewing all groups
		$showSidebar = !$activeUserId;

		$emptyText = 'COM_EASYSOCIAL_GROUPS_EMPTY_' . strtoupper($filter);

		// If this is viewing profile's event, we display a different empty text
		if (!$browseView) {
			$emptyText = 'COM_ES_GROUPS_EMPTY_' . strtoupper($filter);

			if (!$user->isViewer()) {
				$emptyText = 'COM_ES_GROUPS_USER_EMPTY_' . strtoupper($filter);
			}
		}

		// Determines if the "showMyGroups" filter link should appear
		$showMyGroups = true;
		$showPendingGroups = false;
		$showTotalInvites = false;

		if ($this->my->id) {
			$showPendingGroups = $this->my->getTotalPendingReview(SOCIAL_TYPE_GROUP) > 0;
			$showTotalInvites = $model->getTotalInvites($this->my->id) > 0;
		}

		if (!$browseView && !$user->isViewer()) {
			$showPendingGroups = false;
			$showTotalInvites = false;
		}

		// We gonna show the 'showMyGroups' only if the user is viewing browse all group page
		if (!$this->my->id || !$browseView) {
			$showMyGroups = false;
		}

		// Generate links for filters
		$filtersLink = new stdClass;
		$linkOptions = array();

		// If the user is viewing others' listing, we should respect that
		if ($activeUserId) {
			$linkOptions['userid'] = $user->getAlias();
		}

		$filtersLink->all = ESR::groups($linkOptions);
		$filtersLink->featured = ESR::groups(array_merge(array('filter' => 'featured'), $linkOptions));
		$filtersLink->pending = ESR::groups(array_merge(array('filter' => 'pending'), $linkOptions));
		$filtersLink->invited = ESR::groups(array_merge(array('filter' => 'invited'), $linkOptions));
		$filtersLink->created = ESR::groups(array_merge(array('filter' => 'created'), $linkOptions));
		$filtersLink->participated = ESR::groups(array_merge(array('filter' => 'participated'), $linkOptions));

		$this->set('filtersLink', $filtersLink);
		$this->set('showTotalInvites', $showTotalInvites);
		$this->set('showPendingGroups', $showPendingGroups);
		$this->set('showMyGroups', $showMyGroups);
		$this->set('browseView', $browseView);

		// Distance
		$this->set('distance', $distance);
		$this->set('distanceUnit', $this->config->get('general.location.proximity.unit'));
		$this->set('showDistance', $showDistance);
		$this->set('showDistanceSorting', $showDistanceSorting);
		$this->set('hasLocation', $hasLocation);
		$this->set('userLocation', $userLocation);

		$this->set('heading', $heading);
		$this->set('activeUserId', $activeUserId);
		$this->set('showSidebar', $showSidebar);
		$this->set('sortItems', $sortItems);
		$this->set('activeCategory', $activeCategory);
		$this->set('counters', $counters);
		$this->set('pagination', $pagination);
		$this->set('featuredGroups', $featuredGroups);
		$this->set('groups', $groups);
		$this->set('filter', $filter);
		$this->set('user', $user);
		$this->set('ordering', $ordering);
		$this->set('emptyText', $emptyText);
		$this->set('showAllFeatured', $showAllFeatured);

		return parent::display('site/groups/default/default');
	}

	/**
	 * Default method to display the group creation page.
	 * This is the first page that displays the category selection.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function create($tpl = null)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Only users with valid account is allowed to create
		ES::requireLogin();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		if (!$this->my->canCreateGroups()) {
			$this->setMessage(JText::_('COM_ES_GROUPS_NOT_ALLOWED_TO_CREATE_GROUP'), SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::groups(array(), false));
		}

		// Detect for an existing create group session.
		$session = JFactory::getSession();

		$stepSession = FD::table('StepSession');

		// If user doesn't have a record in stepSession yet, we need to create this.
		if (!$stepSession->load($session->getId())) {
			$stepSession->set('session_id', $session->getId());
			$stepSession->set('created', FD::get('Date')->toMySQL());
			$stepSession->set('type', SOCIAL_TYPE_GROUP);

			if (!$stepSession->store()) {
				$this->setError($stepSession->getError());
				return false;
			}
		}

		$model = ES::model('Groups');

		// We want to get parent category only for the initial category selection
		$categories = $model->getCreatableCategories($this->my->getProfile()->id, true);

		// Include child categories
		$allCategories = $model->getCreatableCategories($this->my->getProfile()->id);

		// If there's only 1 category, we should just ignore this step and load the steps page.
		if (count($allCategories) == 1) {

			$category = $categories[0];

			// need to check if this clsuter category has creation limit based on user points or not.
			if (!$category->hasPointsToCreate($this->my->id)) {
				$requiredPoints = $category->getPointsToCreate($this->my->id);

				$this->setMessage(JText::sprintf('COM_EASYSOCIAL_GROUPS_INSUFFICIENT_POINTS', $requiredPoints), SOCIAL_MSG_ERROR);
				$this->info->set($this->getMessage());

				return $this->redirect(ESR::groups(array(), false));
			}

			// Store the category id into the session.
			$session->set('category_id', $category->id, SOCIAL_SESSION_NAMESPACE);

			// Set the current category id.
			$stepSession->uid 	= $category->id;

			// When user accesses this page, the following will be the first page
			$stepSession->step 	= 1;

			// Add the first step into the accessible list.
			$stepSession->addStepAccess(1);

			// Let's save this into a temporary table to avoid missing data.
			$stepSession->store();

			$this->steps();
			return;
		}

		// Set the page title
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_SELECT_GROUP_CATEGORY');
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_GROUPS', ESR::groups());
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_GROUPS');

		$this->set('categories', $categories);
		$this->set('backId', 0);
		$this->set('clusterType', SOCIAL_TYPE_GROUPS);

		parent::display('site/clusters/create/default');
	}

	/**
	 * Post process after user withdraws application to join the group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function withdraw($group)
	{
		// Check if this feature is enabled.
		$this->checkFeature();
		$this->info->set($this->getMessage());

		return $this->redirect(ESR::groups(array('layout' => 'item', 'id' => $group->getAlias()), false));
	}

	/**
	 * Post process after a user leaves a group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function leaveGroup($group)
	{
		// Check if this feature is enabled.
		$this->checkFeature();
		$this->info->set($this->getMessage());

		$redirect = ESR::groups(array('layout' => 'item', 'id' => $group->getAlias()), false);

		if ($group->isInviteOnly()) {
			$redirect = ESR::groups(array(), false);
		}

		return $this->redirect($redirect);
	}

	/**
	 * The workflow for creating a new group.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function steps()
	{
		// Only users with a valid account is allowed here.
		ES::requireLogin();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Ensure that the user is really allowed to create groups
		if (!$this->my->canCreateGroups()) {
			return $this->exception('COM_EASYSOCIAL_GROUPS_NOT_ALLOWED_TO_CREATE_GROUP');
		}

		// Retrieve the user's session.
		$session = JFactory::getSession();
		$stepSession = FD::table('StepSession');
		$stepSession->load($session->getId());

		// If there's no registration info stored, the user must be a lost user.
		if (is_null($stepSession->step)) {
			return $this->exception('COM_EASYSOCIAL_GROUPS_UNABLE_TO_DETECT_ACTIVE_STEP');
		}

		// Get the category that is being selected
		$categoryId = $stepSession->uid;

		// Load up the category
		$category = ES::table('GroupCategory');
		$category->load($categoryId);

		// Check if there is any workflow.
		if (!$category->getWorkflow()->id) {
			return $this->exception(JText::sprintf('COM_ES_NO_WORKFLOW_DETECTED', SOCIAL_TYPE_GROUP));
		}

		// Check if user really has access to create groups from this category
		if (!$category->hasAccess('create', $this->my->getProfile()->id) && !$this->my->isSiteAdmin()) {
			return $this->exception(JText::sprintf('COM_EASYSOCIAL_GROUPS_NOT_ALLOWED_TO_CREATE_GROUP_IN_CATEGORY', $category->getTitle()));
		}

		// Get the current step index
		$stepIndex = $this->input->get('step', 1, 'int');

		// Determine the sequence from the step
		$currentStep = $category->getSequenceFromIndex($stepIndex, SOCIAL_PROFILES_VIEW_REGISTRATION);

		// Users should not be allowed to proceed to a future step if they didn't traverse their sibling steps.
		if (empty($stepSession->session_id) || ($stepIndex > 1 && !$stepSession->hasStepAccess($stepIndex))) {
			return $this->exception(JText::sprintf('COM_EASYSOCIAL_GROUPS_PLEASE_COMPLETE_PREVIOUS_STEP_FIRST', $currentStep));
		}

		// Check if this is a valid step in the profile
		if (!$category->isValidStep($currentStep, SOCIAL_GROUPS_VIEW_REGISTRATION)) {
			return $this->exception(JText::sprintf('COM_EASYSOCIAL_GROUPS_NO_ACCESS_TO_THE_STEP', $currentStep));
		}

		// Remember current state of registration step
		$stepSession->set('step', $stepIndex);
		$stepSession->store();

		// Load the current workflow / step.
		$step = ES::table('FieldStep');
		$step->loadBySequence($category->getWorkflow()->id, SOCIAL_TYPE_CLUSTERS, $currentStep);

		// Determine the total steps for this profile.
		$totalSteps	= $category->getTotalSteps();

		// Try to retrieve any available errors from the current registration object.
		$errors = $stepSession->getErrors();

		// Try to remember the state of the user data that they have entered.
		$data = $stepSession->getValues();

		// Since they are bound to the respective groups, assign the fields into the appropriate groups.
		$args = array(&$data, &$stepSession);

		// Get fields library as we need to format them.
		$fields = ES::fields();
		$fields->init(array('privacy' => false));

		// Retrieve custom fields for the current step
		$fieldsModel = ES::model('Fields');
		$customFields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'visible' => SOCIAL_GROUPS_VIEW_REGISTRATION));

		// Set the breadcrumb
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_GROUPS'), ESR::groups());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_GROUPS_START_YOUR_GROUP'), ESR::groups(array('layout' => 'create')));
		$this->page->breadcrumb($step->_('title'));

		// Set the page title
		ES::document()->title($step->get('title'));

		// Set the callback for the triggered custom fields
		$callback = array($fields->getHandler(), 'getOutput');

		// Trigger onRegister for custom fields.
		if (!empty($customFields)) {
			$fields->trigger('onRegister', SOCIAL_FIELDS_GROUP_GROUP, $customFields, $args, $callback);
		}

		$conditionalFields = array();

		foreach ($customFields as $field) {
			if ($field->isConditional()) {
				$conditionalFields[$field->id] = false;
			}
		}

		if ($conditionalFields) {
			$conditionalFields = json_encode($conditionalFields);
		} else {
			$conditionalFields = false;
		}

		// Pass in the steps for this profile type.
		$steps = $category->getSteps(SOCIAL_GROUPS_VIEW_REGISTRATION);

		// Get the total steps
		$totalSteps = $category->getTotalSteps(SOCIAL_PROFILES_VIEW_REGISTRATION);

		// Format the steps
		if ($steps) {
			$counter = 0;

			foreach ($steps as &$step) {
				$stepClass = $step->sequence == $currentStep || $currentStep > $step->sequence || $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? ' active' : '';
				$stepClass .= $step->sequence < $currentStep || $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? $stepClass . ' past' : '';

				$step->css = $stepClass;
				$step->permalink = 'javascript:void(0);';

				if ($stepSession->hasStepAccess($step->sequence) && $step->sequence != $currentStep) {
					$step->permalink = ESR::groups(array('layout' => 'steps', 'step' => $counter));
				}
			}


			$counter++;
		}

		$this->set('conditionalFields', $conditionalFields);
		$this->set('stepSession', $stepSession);
		$this->set('steps', $steps);
		$this->set('currentStep', $currentStep);
		$this->set('currentIndex', $stepIndex);
		$this->set('totalSteps', $totalSteps);
		$this->set('step', $step);
		$this->set('fields', $customFields);
		$this->set('errors', $errors);
		$this->set('category', $category);

		return parent::display('site/groups/steps/default');
	}

	/**
	 * Renders the edit group page
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function edit($errors = false)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Only users with a valid account is allowed here.
		ES::requireLogin();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Load the language file from the back end.
		JFactory::getLanguage()->load('com_easysocial', JPATH_ADMINISTRATOR);

		// If have errors, then we set it
		if (!empty($errors)) {
			$this->info->set($this->getMessage());
		}

		// Get the group id
		$id = $this->input->get('id', 0, 'int');

		// Load the group
		$group = ES::group($id);

		if (!$id || !$group) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID'), SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::dashboard(array(), false));
		}

		// Check if the user is allowed to edit this group
		if (!$group->isOwner() && !$group->isAdmin() && !$this->my->isSiteAdmin()) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_NO_ACCESS'), SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::dashboard(array(), false));
		}

		// Set the breadcrumb
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_GROUP_PAGE_BREADCRUMB'), ESR::groups());
		$this->page->breadcrumb($group->getName(), $group->getPermalink());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_GROUP_EDIT_PAGE_BREADCRUMB'));

		// Set the page title
		$this->page->title(JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_GROUPS_EDIT', $group->getName()));

		// Load up the category
		$category = ES::table('GroupCategory');
		$category->load($group->category_id);

		// Get the steps model
		$model = ES::model('Steps');
		$steps = $model->getSteps($category->getWorkflow()->id, SOCIAL_TYPE_CLUSTERS, SOCIAL_PROFILES_VIEW_EDIT);
		$fieldsModel = ES::model('Fields');

		// Get custom fields library.
		$fields = FD::fields();

		// Enforce privacy to be false for groups
		$fields->init(array('privacy' => false));

		// Set the callback for the triggered custom fields
		$callback = array($fields->getHandler(), 'getOutput');

		$conditionalFields = array();

		// Get the custom fields for each of the steps.
		foreach ($steps as &$step) {
			$step->fields 	= $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $group->id, 'dataType' => SOCIAL_TYPE_GROUP, 'visible' => 'edit'));

			// Trigger onEdit for custom fields.
			if (!empty($step->fields)) {
				$post = JRequest::get('post');
				$args = array(&$post, &$group, $errors);
				$fields->trigger('onEdit', SOCIAL_TYPE_GROUP, $step->fields, $args, $callback);

				foreach ($step->fields as $field) {
					if ($field->isConditional()) {
						$conditionalFields[$field->id] = false;
					}
				}
			}
		}

		if ($conditionalFields) {
			$conditionalFields = json_encode($conditionalFields);
		} else {
			$conditionalFields = false;
		}

		// retrieve group's approval the rejected reason.
		$rejectedReasons = array();
		if ($group->isDraft()) {
			$rejectedReasons = $group->getRejectedReasons();
		}

		$this->set('conditionalFields', $conditionalFields);
		$this->set('group', $group);
		$this->set('steps', $steps);
		$this->set('rejectedReasons', $rejectedReasons);

		echo parent::display('site/groups/edit/default');
	}

	/**
	 * Method is invoked each time a step is saved. Responsible to redirect or show necessary info about the current step.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function saveStep($session, $currentIndex, $completed = false)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		$info 		= FD::info();
		$config 	= FD::config();

		// Set any message that was passed from the controller.
		$info->set($this->getMessage());

		// If there's an error, redirect back user to the correct step and show the error.
		if($this->hasErrors())
		{
			return $this->redirect(ESR::groups(array('layout' => 'steps', 'step' => $session->step), false));
		}

		// Registration is not completed yet, redirect user to the appropriate step.
		return $this->redirect(ESR::groups(array('layout' => 'steps', 'step' => $session->step), false));
	}

	/**
	 * Displays the group item page
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function item($tpl = null)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Get the group item
		$id = $this->input->get('id', 0, 'int');
		$group = ES::group($id);

		// Check if the group is valid
		if (!$id || !$group->id || !$group->isPublished() || !$group->canAccess()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_GROUP_NOT_FOUND'));
		}

		// If the user is not the owner and the user has been blocked by the group creator
		if ($this->my->id != $group->creator_uid && $this->my->isBlockedBy($group->creator_uid)) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_GROUPS_GROUP_NOT_FOUND'));
		}

		// Set the page properties
		$title = $group->getName();

		$this->page->title($title);
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_GROUPS_PAGE_TITLE'), ESR::groups());
		$this->page->breadcrumb($title);

		$this->set('group', $group);

		// Display private group contents
		if (($group->isInviteOnly() || $group->isClosed()) && !$group->isMember() && !$this->my->isSiteAdmin()) {
			return parent::display('site/groups/restricted/default');
		}

		$appId = $this->input->get('appId', 0, 'int');
		$hashtag = $this->input->get('tag', '', 'default');
		$hashtagAlias = $this->input->get('tag', '', 'default');
		$defaultDisplay = $this->config->get('groups.item.display', 'timeline');
		$layout = $this->input->get('type', '', 'cmd');

		// If it is a hashtag view, let the timeline be the default display
		if (!$appId && !$layout && !$hashtag) {
			$layout = $defaultDisplay;
		}

		// Update the hit counter
		$group->hit();

		// Render group headers
		$group->renderHeaders();

		// Render about on group pages
		if ($layout == 'info') {
			return $this->about($group);
		}

		// Render apps on group pages
		if ($appId) {
			$app = ES::table('App');
			$app->load($appId);

			return $this->app($group, $app);
		}

		if (!$appId && $hashtag) {
			$layout = 'hashtag';
		}

		// Initiate stream lib
		$stream = ES::stream();

		// Get custom filter actions
		$customFilterActions = array();

		if ($group->canCreateStreamFilter()) {
			$customFilterActions['link'] = ESR::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'filterForm'));
			$customFilterActions['attributes'] = 'data-create-filter';
			$customFilterActions['icon'] = 'fa-plus';
		}

		$contents = '';
		$filters = $group->getFilters();
		$filterId = $this->input->get('filterId', 0, 'int');

		if ($layout == 'filterForm') {
			$contents = $stream->getFilterForm($group->id, SOCIAL_TYPE_GROUP, $filterId);
		}

		// Determines if we should display the stream moderation
		$moderation = $group->getParams()->get('stream_moderation', null);
		$showPendingPostFilter = false;
		$totalPendingPost = 0;

		if ($moderation !== null && $moderation) {
			$showPendingPostFilter = true;
			$totalPendingPost = $group->getTotalPendingPosts();
		}

		// Set the canonical data to the current group
		$this->page->canonical($group->getPermalink(false, true));

		// Determine if we should display news feed filter on the side
		$displayFeedsFilter = false;

		if (($group->isAdmin() || $group->isOwner() || $this->my->isSiteAdmin()) && $showPendingPostFilter) {
			$displayFeedsFilter = true;
		}

		if ($customFilterActions || $hashtag || ($filters && count($filters) > 0)) {
			$displayFeedsFilter = true;
		}

		// Get the timeline link
		$defaultDisplay = $this->config->get('groups.item.display', 'timeline');
		$timelinePermalink = $group->getPermalink();
		$aboutPermalink = ESR::groups(array('id' => $group->getAlias(), 'type' => 'info', 'layout' => 'item'));

		if ($defaultDisplay == 'info') {
			$aboutPermalink = $timelinePermalink;
			$timelinePermalink = ESR::groups(array('id' => $group->getAlias(), 'type' => 'timeline', 'layout' => 'item'));
		}

		$this->set('aboutPermalink', $aboutPermalink);
		$this->set('displayFeedsFilter', $displayFeedsFilter);
		$this->set('filters', $filters);
		$this->set('customFilterActions', $customFilterActions);
		$this->set('appId', $appId);
		$this->set('title', $title);
		$this->set('showPendingPostFilter', $showPendingPostFilter);
		$this->set('totalPendingPost', $totalPendingPost);
		$this->set('layout', $layout);
		$this->set('filterId', $filterId);
		$this->set('contents', $contents);
		$this->set('hashtag', $hashtag);
		$this->set('stream', $stream);

		if (!empty($contents)) {
			return parent::display('site/groups/item/default');
		}

		// If there's a hash tag, try to get the actual title to display on the site
		if ($hashtag) {
			$tag = $stream->getHashTag($hashtag);
			$hashtag = $tag->title;
		}

		// Retrieve story form for group
		$story = ES::get('Story', SOCIAL_TYPE_GROUP);
		$story->setCluster($group->id, SOCIAL_TYPE_GROUP);
		$story->showPrivacy(false);

		if ($hashtag) {
			$story->setHashtags(array($hashtag));
		}

		// Only group members allowed to post story updates on group page.
		if ($this->my->canPostClusterStory(SOCIAL_TYPE_GROUP, $group->id)) {

			// Set the story data on the stream
			$stream->story = $story;
		}

		//lets get the sticky posts 1st
		$stickies = $stream->getStickies(array('clusterId' => $group->id, 'clusterType' 	=> SOCIAL_TYPE_GROUP, 'limit' => 0));
		if ($stickies) {
			$stream->stickies = $stickies;
		}

		// lets get stream items for this group
		$options = array('clusterId' => $group->id, 'clusterType' 	=> SOCIAL_TYPE_GROUP, 'nosticky' => true);
		$displayOptions = array();

		// stream filter id
		$filterId = $this->input->get('filterId', 0, 'int');
		$streamFilter = '';

		if ($filterId) {
			$streamFilter = FD::table('StreamFilter');
			$streamFilter->load( $filterId );

			$hashtags = $streamFilter->getHashTag();
			$tags = explode(',', $hashtags);

			if ($tags) {
				$options['tag'] = $tags;

				$story->setHashtags($tags);

				$hashtagRule = $this->config->get('stream.filter.hashtag', '');
				if ($hashtagRule == 'and') {
					$options['matchAllTags'] = true;
				}
			}
		}

		// Show stream items that is associated with the hashtag
		if ($hashtag) {
			$options['tag'] = array($hashtag);
		}

		if ($layout == 'moderation') {
			$options['onlyModerated'] = true;
			$options['nosticky'] = true;

			unset($stream->story);
			unset($stream->stickies);
		}

		$stream->get($options, $displayOptions);

		if ($this->config->get('stream.rss.enabled')) {
			$this->addRss(ESR::groups(array('id' => $group->getAlias(), 'layout' => 'item'), false));
		}

		$this->set('rssLink', $this->rssLink);
		$this->set('stream', $stream);
		$this->set('hashtag', $hashtag);
		$this->set('hashtagAlias', $hashtagAlias);
		$this->set('streamFilter', $streamFilter);

		return parent::display('site/groups/item/default');
	}

	/**
	 * Post process after a group is created
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function complete($group)
	{
		$this->info->set($this->getMessage());

		$url = ESR::groups(array(), false);

		if ($group->isPublished()) {
			$url = ESR::groups(array('layout' => 'item', 'id' => $group->getAlias()), false);
		}

		$this->redirect($url);
	}

	/**
	 * Displays information from groups within a particular category
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function category()
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Get the category id from the query
		$id = $this->input->get('id', 0, 'int');

		$category = ES::table('GroupCategory');
		$category->load($id);

		// Check if the category is valid
		if (!$id || !$category->id) {
			return $this->exception('COM_EASYSOCIAL_GROUPS_INVALID_GROUP_ID');
		}

		// Load backend language file
		ES::language()->loadAdmin();

		// Set the page title to this category
		$this->page->title($category->get('title'));
		$this->page->description($category->getDescription());

		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_GROUPS', ESR::groups());
		$this->page->breadcrumb($category->_('title'));

		$ids = $category->id;

		$categoryModel = ES::model('ClusterCategory');

		// check if this category is a container or not
		if ($category->container) {
			// Get all child ids from this category
			$childs = $categoryModel->getChildCategories($category->id, array(), SOCIAL_TYPE_GROUP, array('state' => SOCIAL_STATE_PUBLISHED));

			$childIds = array();

			foreach ($childs as $child) {
				$childIds[] = $child->id;
			}

			if (!empty($childIds)) {
				$ids = $childIds;
			}
		}

		// Get recent 10 groups from this category
		$options = array('sort' => 'random', 'category' => $ids, 'state' => SOCIAL_STATE_PUBLISHED);

		$model = FD::model('Groups');
		$groups = $model->getGroups($options);

		// Get random members from this category
		$randomMembers = $model->getRandomCategoryMembers($ids, SOCIAL_CLUSTER_CATEGORY_MEMBERS_LIMIT);

		// Get total groups within a category
		$totalGroups = $model->getTotalGroups(array('category_id' => $ids));

		// Get total albums within a category
		$totalAlbums = $model->getTotalAlbums(array('category_id' => $ids));

		// Get the stream for this group
		$stream = ES::stream();
		$stream->get(array('clusterCategory' => $ids, 'clusterType' => SOCIAL_TYPE_GROUP), array('perspective' => 'dashboard'));

		// Get random albums for groups in this category
		$randomAlbums = $model->getRandomAlbums(array('category_id' => $ids, 'core' => false));

		$childs = $categoryModel->getImmediateChildCategories($category->id, SOCIAL_TYPE_GROUP);

		$this->set('randomAlbums', $randomAlbums);
		$this->set('stream', $stream);
		$this->set('totalGroups', $totalGroups);
		$this->set('randomMembers', $randomMembers);
		$this->set('groups', $groups);
		$this->set('category', $category);
		$this->set('totalAlbums', $totalAlbums);
		$this->set('childs', $childs);

		return parent::display('site/groups/category/default');
	}

	/**
	 * Post process after a user is rejected to join the group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function reject($group)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		$permalink = $group->getPermalink(false);

		$returnUrl = $this->getReturnUrl($permalink);

		$this->redirect($returnUrl);
	}

	/**
	 * Post process after the group avatar is removed
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function removeAvatar(SocialGroup $group)
	{
		FD::info()->set($this->getMessage());

		$permalink 	= $group->getPermalink(false);

		$this->redirect($permalink);
	}

	/**
	 * Post process after a user is deleted from the group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function removeMember(SocialGroup $group)
	{
		// Determines if we need to redirect
		$returnUrl = $this->getReturnUrl($group->getPermalink(false));

		// Set the necessary message
		$this->info->set($this->getMessage());

		return $this->redirect($returnUrl);
	}

	/**
	 * Post process after a user is approved to join the group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function approve($group = null)
	{
		$this->info->set($this->getMessage());

		// Default redirect
		$redirect = ESR::groups(array('layout' => 'item', 'id' => $group->getAlias()), false);

		$redirect = $this->getReturnUrl($redirect);

		return $this->redirect($redirect);
	}

	/**
	 * Post process after a user has been promoted to be admin of the group
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function promote(SocialGroup $group)
	{
		// Determines if we need to redirect
		$returnUrl = $this->getReturnUrl($group->getPermalink(false));

		// Set the message
		$this->info->set($this->getMessage());

		return $this->redirect($returnUrl);
	}

	/**
	 * Post process after a user has been demoted
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function demote(SocialGroup $group)
	{
		// Determines if we need to redirect
		$returnUrl = $this->getReturnUrl($group->getPermalink(false));

		// Set the message
		$this->info->set($this->getMessage());

		return $this->redirect($returnUrl);
	}

	/**
	 * Post process after a user is invited to join the group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function invite(SocialGroup $group)
	{
		$this->info->set($this->getMessage());

		$redirect = $this->getReturnUrl($group->getPermalink(false));

		$this->redirect($redirect);
	}

	/**
	 * Post process after a group is published
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function approveGroup()
	{
		FD::info()->set($this->getMessage());

		$this->redirect(ESR::groups(array(), false));
	}

	/**
	 * Post process after a group is rejected
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function rejectGroup()
	{
		FD::info()->set($this->getMessage());

		$this->redirect(ESR::groups(array(), false));
	}


	/**
	 * Post process after a group is set as featured
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function setFeatured($group)
	{
		$this->info->set($this->getMessage());

		$permalink = $group->getPermalink(false);

		$returnUrl = $this->getReturnUrl($permalink);

		$this->redirect($returnUrl);
	}

	/**
	 * Post process after a group is removed from being featured
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function removeFeatured($group)
	{
		$this->info->set($this->getMessage());

		$permalink = $group->getPermalink(false);

		$returnUrl = $this->getReturnUrl($permalink);

		$this->redirect($returnUrl);
	}

	/**
	 * Post process after category has been selected
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function selectCategory()
	{
		// Set message data.
		FD::info()->set($this->getMessage());

		// @task: Check for errors.
		if ($this->hasErrors()) {
			return $this->redirect(ESR::groups(array(), false));
		}

		// @task: We always know that after selecting the profile type, the next step would always be the first step.
		$url = ESR::groups(array('layout' => 'steps', 'step' => 1), false);

		return $this->redirect($url);
	}

	/**
	 * Post process when a group is deleted
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function delete()
	{
		FD::info()->set($this->getMessage());

		$this->redirect(ESR::groups(array(), false));
	}

	/**
	 * Post process when a group is unpublished
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function unpublish()
	{
		FD::info()->set($this->getMessage());

		$this->redirect(ESR::groups(array(), false));
	}

	/**
	 * Post process after saving group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function update($group)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		FD::info()->set($this->getMessage());

		$url = '';
		if ($group->isPending()) {
			$url = ESR::groups(array(), false);
		} else {
			$url = $group->getPermalink(false);
		}

		return $this->redirect($url);
	}

	/**
	 * Post process after a user response to the invitation.
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function respondInvitation($group, $action)
	{
		$this->info->set($this->getMessage());

		if ($action == 'reject') {
			$redirect = ESR::groups(array('filter' => 'invited'), false);
			return $this->redirect($redirect);
		}

		$redirect = ESR::groups(array('layout' => 'item', 'id' => $group->getAlias()), false);

		// if accept from email n not logged in,
		// we should redirect to dashboard
		if (!$group->canAccess()) {
			$redirect = ESR::dashboard();
		}

		return $this->redirect($redirect);
	}


	/**
	 * Post process after saving group filter
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function saveFilter($filter, $groupId)
	{
		// Unauthorized users should not be allowed to access this page.
		ES::requireLogin();

		ES::info()->set($this->getMessage());

		$group = ES::group($groupId);

		$this->redirect(ESR::groups(array('layout' => 'item', 'id' => $group->getAlias()), false));
	}

	/**
	 * Post process after adding group filter
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function addFilter($filter, $groupId)
	{
		$this->saveFilter($filter, $groupId);
	}

	/**
	 * Allows viewer to download a file from the group
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function download()
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Currently only registered users are allowed to view a file.
		FD::requireLogin();

		// Get the file id from the request
		$fileId 	= JRequest::getInt('fileid', null);

		$file 	= FD::table('File');
		$file->load($fileId);

		if(!$file->id || !$fileId)
		{
			// Throw error message here.
			$this->redirect(ESR::dashboard(array(), false));
			$this->close();
		}

		// Load up the group
		$group		= FD::group($file->uid);

		// Ensure that the user can really view this group
		if(!$group->canViewItem())
		{
			// Throw error message here.
			$this->redirect(ESR::dashboard(array(), false));
			$this->close();
		}

		$file->download();
		exit;
	}

	/**
	 * Allows viewer to download a conversation file
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function preview()
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Get the file id from the request
		$fileId = $this->input->get('fileid', null, 'int');

		$file = ES::table('File');
		$file->load($fileId);

		if (!$file->id || !$fileId) {
			$this->redirect(ESR::dashboard(array(), false));
			$this->close();
		}

		// Load up the group
		$group = ES::group($file->uid);

		// Ensure that the user can really view this group
		if (!$group->canViewItem()) {
			$this->redirect(ESR::dashboard(array(), false));
			$this->close();
		}

		$file->preview();
		exit;
	}
}
