<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasySocialViewPages extends EasySocialSiteView
{
	/**
	 * Checks if this feature should be enabled or not.
	 *
	 * @since   2.0
	 * @access  private
	 */
	private function checkFeature()
	{
		// Do not allow user to access pages if it's not enabled
		if (!$this->config->get('pages.enabled')) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_PAGES_DISABLED'), SOCIAL_MSG_ERROR);

			// Set message
			$this->info->set($this->getMessage());

			$this->redirect(ESR::dashboard(array(), false));
		}
	}

	/**
	 * Default method to display the all pages page.
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function display($tpl = null)
	{
		$this->checkFeature();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Set Meta data
		ES::setMeta();

		// Set up the page
		$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PAGES'));
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PAGES'));
		$this->page->canonical(ESR::pages(array('external' => true)));

		$userId = $this->input->get('userid', 0, 'int');
		$userId = !$userId ? null : $userId;
		$user = ES::user($userId);

		// If there is a user id, means we are viewing profile's page listing
		// $browseView: is a listing view of all page.
		$browseView = true;

		// If no uid, means user is viewing the browsing all pages view
		// We define this browse view same like $showsidebar.
		// so it won't break when other customer that still using $showsidebar
		if ($userId && $user->id) {
			$browseView = false;
		}

		// This option determines if the sidebar should be visible
		$showSidebar = !$userId;

		// If we are viewing profile's page listing
		// Default the $filter to 'created'
		$defaultFilter = $browseView ? 'all' : 'created';

		// Get active filter
		$filter = $this->input->get('filter', $defaultFilter, 'cmd');
		$allowedFilter = array('all', 'invited', 'mine', 'featured', 'pending', 'liked', 'created', 'participated');

		// Only allow filters that we know.
		if (!empty($filter) && !in_array($filter, $allowedFilter)) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGES_INVALID_PAGE_ID'));
		}

		$model = ES::model('Pages');
		$options = array('state' => SOCIAL_STATE_PUBLISHED);

		// Only exclude featured page when viewing all pages
		if ($browseView) {
			$options['featured'] = false;
		}

		// If user is site admin, they should be able to see everything.
		$options['types'] = $this->my->isSiteAdmin() ? 'all' : 'user';

		// Determine the pagination limit
		$limit = ES::getLimit('pages_limit');
		$options['limit'] = $limit;

		// Determine if this is filtering pages by category
		$categoryId = $this->input->get('categoryid', 0, 'int');
		$activeCategory = false;

		if ($categoryId) {
			$activeCategory = ES::table('PageCategory');
			$state = $activeCategory->load($categoryId);

			if (!$state) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGES_INVALID_CATEGORY_ID'));
			}

			$options['category'] = $activeCategory->id;

			// check if this category is a container or not
			if ($activeCategory->container) {
				// Get all child ids from this category
				$categoryModel = ES::model('ClusterCategory');
				$childs = $categoryModel->getChildCategories($activeCategory->id, array(), SOCIAL_TYPE_PAGE, array('state' => SOCIAL_STATE_PUBLISHED));

				$childIds = array();

				foreach ($childs as $child) {
					$childIds[] = $child->id;
				}

				if (!empty($childIds)) {
					$options['category'] = $childIds;
				}
			}

			$this->set('activeCategory', $activeCategory);

			$filter = 'category';

			$this->page->title($activeCategory->get('title'));
		}

		// Since not logged in users cannot filter by 'invited' or 'mine', they shouldn't be able to access these filters at all
		if ($this->my->guest && ($filter == 'invited' || $filter == 'mine')) {
			return $this->app->redirect(ESR::dashboard(array(), false));
		}

		// If the default filter is invited, we only want to fetch pages that the user has been
		// invited to.
		if ($filter == 'invited') {
			$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PAGES_FILTER_INVITED'));
			$options['invited'] = $this->my->id;
			$options['types'] = 'all';
			$options['featured'] = '';
		}

		// Filter by own pages
		if ($filter == 'mine') {
			$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PAGES_FILTER_MY_PAGES'));
			$options['uid'] = $this->my->id;
			$options['types'] = 'all';
			$options['featured'] = '';
		}

		if ($filter == 'pending') {
			$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_PAGES_FILTER_PENDING');

			// groups that pending user's review.
			$options['uid'] = $this->my->id;
			$options['state'] = SOCIAL_CLUSTER_DRAFT;
			$options['types'] = 'user';
		}

		if ($filter == 'liked') {
			$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_PAGES_FILTER_LIKED');

			// Groups that user has liked
			$options['liked'] = $this->my->id;
			$options['featured'] = '';
		}

		// If in profile page listing, show only page created by that user
		// except in participated filter. because we need to show others' page as well
		if (!$browseView) {
			if ($filter != 'participated') {
				$options['uid'] = $user->id;
			} else {
				$options['liked'] = $user->id;
			}
		}

		// Get ordering option if any
		$ordering = $this->input->get('ordering', 'latest', 'cmd');
		$options['ordering'] = $ordering;

		$pages = array();

		// Get a list of pages
		if ($filter == 'featured') {
			$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PAGES_FILTER_FEATURED'));
			$options['featured'] = true;
		}

		// It's time to get the pages
		$pages = $model->getPages($options);

		// Load up the pagination for the pages here.
		$pagination = $model->getPagination();

		// Get a list of featured pages
		$featuredPages = array();

		if (($filter == 'all' || $categoryId)) {
			$options['featured'] = true;
			$featuredPages = $model->getPages($options);
		}

		$counters = new stdClass();

		// Get total number of pages on the site
		$counters->totalPages = $model->getTotalPages(array('types' => $this->my->isSiteAdmin() ? 'all' : 'user'));

		// Get total number of featured pages on the site
		$counters->totalFeaturedPages = $model->getTotalPages(array('featured' => true));

		if ($this->my->id != 0) {
			// Get the total number of pages the user created
			$counters->totalCreatedPages = $this->my->getTotalPagesCreated();

			// Get the total number of groups the user created but required review
			$counters->totalPendingPages = $this->my->getTotalPendingReview(SOCIAL_TYPE_PAGE);

			// Get total number of invitations
			$counters->totalInvites = $model->getTotalInvites($this->my->id);

			// Get total number of liked pages
			$counters->getTotalParticipatedPages = $model->getTotalParticipatedPages($this->my->id);
		}

		// Get the sorting URL
		$sortItems = new stdClass();
		$sortingTypes = array('latest', 'name', 'popular');

		if ($browseView) {
			foreach ($sortingTypes as $sortingType) {

				$sortItems->{$sortingType} = new stdClass();

				// attributes
				$sortAttributes = array('data-sorting', 'data-filter="' . $filter . '"', 'data-type="' . $sortingType . '"');
				if ($activeCategory) {
					$sortAttributes[] = 'data-id="' . $activeCategory->id . '"';
				}

				//url
				$urlOptions = array();

				$urlFilter = $filter;
				if ($urlFilter == 'category') {
					$urlFilter = 'all';
				}

				$urlOptions['filter'] = $urlFilter;
				$urlOptions['ordering'] = $sortingType;
				if ($activeCategory) {
					$urlOptions['categoryid'] = $activeCategory->getAlias();
				}
				$sortUrl = ESR::pages($urlOptions);

				$sortItems->{$sortingType}->attributes = $sortAttributes;
				$sortItems->{$sortingType}->url = $sortUrl;
			}
		}

		$emptyText = 'COM_EASYSOCIAL_PAGES_EMPTY_' . strtoupper($filter);

		// If this is viewing profile's event, we display a different empty text
		if (!$browseView) {
			$emptyText = 'COM_ES_PAGES_EMPTY_' . strtoupper($filter);

			if (!$user->isViewer()) {
				$emptyText = 'COM_ES_PAGES_USER_EMPTY_' . strtoupper($filter);
			}
		}

		if (!$browseView) {
			$counters->totalCreatedPages = $user->getTotalPagesCreated();
			$counters->getTotalParticipatedPages = $model->getTotalParticipatedPages($user->id);
		}

		// Determines if the "showMyPages" filter link should appear
		$showMyPages = true;
		$showPendingPages = false;
		$showTotalInvites = false;

		if ($this->my->id) {
			$showPendingPages = $this->my->getTotalPendingReview(SOCIAL_TYPE_PAGE) > 0;
			$showTotalInvites = $model->getTotalInvites($this->my->id) > 0;
		}

		if (!$browseView && !$user->isViewer()) {
			$showPendingPages = false;
			$showTotalInvites = false;
		}

		// We gonna show the 'showMyPages' only if the user is viewing browse all page page
		if (!$this->my->id || !$browseView) {
			$showMyPages = false;
		}

		// Generate links for filters
		$filtersLink = new stdClass;
		$linkOptions = array();

		// If the user is viewing others' listing, we should respect that
		if ($userId) {
			$linkOptions['userid'] = $user->getAlias();
		}

		$filtersLink->all = ESR::pages($linkOptions);
		$filtersLink->featured = ESR::pages(array_merge(array('filter' => 'featured'), $linkOptions));
		$filtersLink->pending = ESR::pages(array_merge(array('filter' => 'pending'), $linkOptions));
		$filtersLink->invited = ESR::pages(array_merge(array('filter' => 'invited'), $linkOptions));
		$filtersLink->created = ESR::pages(array_merge(array('filter' => 'created'), $linkOptions));
		$filtersLink->participated = ESR::pages(array_merge(array('filter' => 'participated'), $linkOptions));

		$this->set('filtersLink', $filtersLink);
		$this->set('showTotalInvites', $showTotalInvites);
		$this->set('showPendingPages', $showPendingPages);
		$this->set('showMyPages', $showMyPages);
		$this->set('browseView', $browseView);
		$this->set('showSidebar', $showSidebar);
		$this->set('sortItems', $sortItems);
		$this->set('activeCategory', $activeCategory);
		$this->set('counters', $counters);
		$this->set('pagination', $pagination);
		$this->set('featuredPages', $featuredPages);
		$this->set('pages', $pages);
		$this->set('filter', $filter);
		$this->set('user', $user);
		$this->set('ordering', $ordering);
		$this->set('emptyText', $emptyText);
		$this->set('activeUserId', $userId);

		return parent::display('site/pages/default/default');
	}


	/**
	 * Method to display page search results
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function search($tpl = null)
	{
		$this->checkFeature();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Set up the page
		$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PAGES'));
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PAGES'));
		$this->page->canonical(ESR::pages(array('external' => true)));

		$userId = $this->input->get('userid', 0, 'int');
		$userId = !$userId ? null : $userId;
		$user = ES::user($userId);


		// If there is a user id, means we are viewing profile's page listing
		// $browseView: is a listing view of all page.
		$browseView = true;

		// This option determines if the sidebar should be visible
		$showSidebar = true;

		// If we are viewing profile's page listing
		// Default the $filter to 'created'
		$defaultFilter = $browseView ? 'all' : 'created';

		// Get active filter
		$filter = 'search';

		$model = ES::model('Pages');

		// Determine the pagination limit
		$limit = ES::getLimit('pages_limit');

		// Get ordering option if any
		$ordering = $this->input->get('ordering', 'latest', 'cmd');

		// $data = $this->input->getArray('get');

		$pagecategory = $this->input->get('pagecategory', '', 'default');
		$pagecreator = $this->input->get('pagecreator', '', 'default');

		// $days = $this->input->get('day', array(), 'default');
		// $start = $this->input->get('hourstart', array(), 'default');
		// $end = $this->input->get('hourend', array(), 'default');

		$days = $this->input->get('day', 'all', 'default');
		$start = $this->input->get('hourstart', '00:00', 'default');
		$end = $this->input->get('hourend', '24:00', 'default');

		$daytimes = array();

		if ($days) {
			$cnt = count($days);
			for ($i = 0; $i < $cnt; $i++) {
				$string = $days[$i] . '|' . $start . '|' . $end;
				$daytimes[] = $string;
			}
		}

		$pagecategory = explode(',', $pagecategory);
		if ($pagecategory && !is_array($pagecategory)) {
			$pagecategory = array($pagecategory);
		}

		$pagecreator = explode(',', $pagecreator);
		if ($pagecreator && !is_array($pagecreator)) {
			$pagecreator = array($pagecreator);
		}


		$searchOptions = array();
		$searchOptions['limit'] = $limit;
		$searchOptions['match'] = 'any';
		$searchOptions['sort'] = $ordering;
		$searchOptions['categoryIds'] = $pagecategory;
		$searchOptions['authorIds'] = $pagecreator;
		$searchOptions['daytimes'] = $daytimes;

		$model = ES::model('pages');
		$pages = $model->searchPageByHours($searchOptions);

		// Load up the pagination for the pages here.
		$pagination = $model->getPagination();

		$pagination->setVar('Itemid', ESR::getItemId('pages'));
		$pagination->setVar('view', 'pages');
		$pagination->setVar('layout', 'search');
		$pagination->setVar('filter', 'search');

		$emptyText = 'COM_ES_PAGES_EMPTY_SEARCH';

		$counters = new stdClass();
		$counters->totalPages = $model->getTotalPages(array('types' => $this->my->isSiteAdmin() ? 'all' : 'user'));
		$counters->totalFeaturedPages = $model->getTotalPages(array('featured' => true));

		// turn off all these in sidebar in search page.
		$showMyPages = false;
		$showPendingPages = false;
		$showTotalInvites = false;

		$filtersLink = new stdClass;
		$linkOptions = array();

		$filtersLink->all = ESR::pages($linkOptions);
		$filtersLink->featured = ESR::pages(array_merge(array('filter' => 'featured'), $linkOptions));

		$this->set('filtersLink', $filtersLink);
		$this->set('showTotalInvites', $showTotalInvites);
		$this->set('showPendingPages', $showPendingPages);
		$this->set('showMyPages', $showMyPages);
		$this->set('browseView', $browseView);
		$this->set('showSidebar', $showSidebar);
		$this->set('sortItems', '');
		$this->set('activeCategory', false);
		$this->set('counters', $counters);
		$this->set('pagination', $pagination);
		$this->set('featuredPages', array());
		$this->set('pages', $pages);
		$this->set('filter', $filter);
		$this->set('user', $user);
		$this->set('ordering', $ordering);
		$this->set('emptyText', $emptyText);
		$this->set('activeUserId', $userId);

		return parent::display('site/pages/default/default');
	}




	/**
	 * Default method to display the page creation page.
	 * This is the first page that displays the category selection.
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function create($tpl = null)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Only users with valid account is allowed to create
		ES::requireLogin();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		if (!$this->my->canCreatePages()) {
			$this->setMessage(JText::_('COM_ES_PAGES_NOT_ALLOWED_TO_CREATE_PAGE'), SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::pages(array(), false));
		}

		// Detect for an existing create page session.
		$session = JFactory::getSession();

		$stepSession = ES::table('StepSession');

		// If user doesn't have a record in stepSession yet, we need to create this.
		if (!$stepSession->load($session->getId())) {
			$stepSession->set('session_id', $session->getId());
			$stepSession->set('created', ES::get('Date')->toMySQL());
			$stepSession->set('type', SOCIAL_TYPE_PAGE);

			if (!$stepSession->store()) {
				$this->setError($stepSession->getError());
				return false;
			}
		}

		$model = ES::model('Pages');

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
				$this->setMessage(JText::sprintf('COM_EASYSOCIAL_PAGES_INSUFFICIENT_POINTS', $requiredPoints), SOCIAL_MSG_ERROR);
				$this->info->set($this->getMessage());

				return $this->redirect(ESR::pages(array(), false));
			}

			// Store the category id into the session.
			$session->set('category_id', $category->id, SOCIAL_SESSION_NAMESPACE);

			// Set the current category id.
			$stepSession->uid = $category->id;

			// When user accesses this page, the following will be the first page
			$stepSession->step = 1;

			// Add the first step into the accessible list.
			$stepSession->addStepAccess(1);

			// Let's save this into a temporary table to avoid missing data.
			$stepSession->store();

			$this->steps();
			return;
		}

		// Set the page title
		$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SELECT_PAGE_CATEGORY'));

		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PAGES'), ESR::pages());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SELECT_PAGE_CATEGORY'));

		$this->set('categories', $categories);
		$this->set('backId', 0);
		$this->set('clusterType', SOCIAL_TYPE_PAGES);

		parent::display('site/clusters/create/default');
	}

	/**
	 * Post process after user withdraws application to join the page
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function withdraw($page)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Set message
		$this->info->set($this->getMessage());

		$permalink = $page->getPermalink(false);

		return $this->redirect($permalink);
	}

	/**
	 * Post process after a user unlike a page
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function unlike($page)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Set message
		$this->info->set($this->getMessage());

		$permalink = $page->getPermalink(false);

		$returnUrl = $this->getReturnUrl($permalink);

		return $this->redirect($returnUrl);
	}

	/**
	 * The workflow for creating a new page.
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function steps()
	{
		// Only users with a valid account is allowed here.
		ES::requireLogin();

		// Check if the user is allowed to create page or not.
		$my = ES::user();

		if (!$this->my->canCreatePages()) {
			return $this->exception('COM_EASYSOCIAL_PAGES_NOT_ALLOWED_TO_CREATE_PAGE');
		}
		// Retrieve the user's session.
		$session = JFactory::getSession();
		$stepSession = ES::table('StepSession');
		$stepSession->load($session->getId());

		// If there's no page creation info stored, the user must be a lost user.
		if (is_null($stepSession->step)) {
			return $this->exception('COM_ES_UNABLE_TO_DETECT_ACTIVE_STEP');
		}

		// Get the category that is being selected
		$categoryId = $stepSession->uid;

		// Load up the category
		$category = ES::table('PageCategory');
		$category->load($categoryId);

		// Check if there is any workflow.
		if (!$category->getWorkflow()->id) {
			return $this->exception(JText::sprintf('COM_ES_NO_WORKFLOW_DETECTED', SOCIAL_TYPE_PAGE));
		}

		// Check if user really has access to create pages from this category
		if (!$category->hasAccess('create', $my->getProfile()->id) && !$my->isSiteAdmin()) {
			return $this->exception(JText::sprintf('COM_EASYSOCIAL_PAGES_NOT_ALLOWED_TO_CREATE_PAGE_IN_CATEGORY', $category->getTitle()));
		}

		// Get the current step index
		$stepIndex = JRequest::getInt('step', 1);

		// Determine the sequence from the step
		$currentStep = $category->getSequenceFromIndex($stepIndex, SOCIAL_PAGES_VIEW_REGISTRATION);

		// Users should not be allowed to proceed to a future step if they didn't traverse their sibling steps.
		if (empty($stepSession->session_id) || ($stepIndex > 1 && !$stepSession->hasStepAccess($stepIndex))) {
			return $this->exception(JText::sprintf('COM_ES_PLEASE_COMPLETE_PREVIOUS_STEP_FIRST', $currentStep));
		}

		// Check if this is a valid step in the page
		if (!$category->isValidStep($currentStep, SOCIAL_PAGES_VIEW_REGISTRATION)) {
			return $this->exception(JText::sprintf('COM_ES_NO_ACCESS_TO_THE_STEP', $currentStep));
		}

		// Remember current state of page creation step
		$stepSession->set('step', $stepIndex);
		$stepSession->store();

		// Load the current workflow / step.
		$step = ES::table('FieldStep');
		$step->loadBySequence($category->getWorkflow()->id, SOCIAL_TYPE_CLUSTERS, $currentStep);

		// Determine the total steps for this page.
		$totalSteps = $category->getTotalSteps();

		// Try to retrieve any available errors from the current page creation object.
		$errors = $stepSession->getErrors();

		// Try to remember the state of the user data that they have entered.
		$data = $stepSession->getValues();

		// Since they are bound to the respective pages, assign the fields into the appropriate pages.
		$args = array(&$data, &$stepSession);

		// Get fields library as we need to format them.
		$fields = ES::fields();
		$fields->init(array('privacy' => false));

		// Retrieve custom fields for the current step
		$fieldsModel = ES::model('Fields');
		$customFields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'visible' => SOCIAL_PAGES_VIEW_REGISTRATION));

		// Set the breadcrumb
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PAGES'), ESR::pages());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGES_START_YOUR_PAGE'), ESR::pages(array('layout' => 'create')));
		$this->page->breadcrumb($step->get('title'));

		// Set the page title
		$this->page->title($step->get('title'));

		// Set the callback for the triggered custom fields
		$callback = array($fields->getHandler(), 'getOutput');

		// Trigger onRegister for custom fields.
		if (!empty($customFields)) {
			$fields->trigger('onRegister', SOCIAL_FIELDS_GROUP_PAGE, $customFields, $args, $callback);
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

		// Pass in the steps for this page category.
		$steps = $category->getSteps(SOCIAL_PAGES_VIEW_REGISTRATION);

		// Get the total steps
		$totalSteps = $category->getTotalSteps(SOCIAL_PAGES_VIEW_REGISTRATION);

		// Format the steps
		if ($steps) {
			$counter = 0;

			foreach ($steps as &$step) {
				$stepClass = $step->sequence == $currentStep || $currentStep > $step->sequence || $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? ' active' : '';
				$stepClass .= $step->sequence < $currentStep || $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? $stepClass . ' past' : '';

				$step->css = $stepClass;
				$step->permalink = 'javascript:void(0);';

				if ($stepSession->hasStepAccess($step->sequence) && $step->sequence != $currentStep) {
					$step->permalink = ESR::pages(array('layout' => 'steps', 'step' => $counter));
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

		parent::display('site/pages/steps/default');
	}

	/**
	 * Editing a page
	 *
	 * @since   2.0
	 * @access  public
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

		// Get the page id
		$id = $this->input->get('id', 0, 'int');

		// Load the page
		$page = ES::page($id);

		if (!$id || !$page) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_PAGES_INVALID_PAGE_ID'), SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::dashboard(array(), false));
		}

		// Check if the user is allowed to edit this page
		if (!$page->isOwner() && !$page->isAdmin() && !$this->my->isSiteAdmin()) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_PAGES_NO_ACCESS'), SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::dashboard(array(), false));
		}

		// Set the breadcrumb
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_PAGE_BREADCRUMB'), ESR::pages());
		$this->page->breadcrumb($page->getName(), $page->getPermalink());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_EDIT_PAGE_BREADCRUMB'));

		// Set the page title
		$this->page->title(JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_PAGES_EDIT', $page->getName()));

		// Load up the category
		$category = ES::table('PageCategory');
		$category->load($page->category_id);

		// Get the steps model
		$stepsModel = ES::model('Steps');
		$steps = $stepsModel->getSteps($category->getWorkflow()->id, SOCIAL_TYPE_CLUSTERS, SOCIAL_PROFILES_VIEW_EDIT);
		$fieldsModel = ES::model('Fields');

		// Get custom fields library.
		$fields = ES::fields();

		// Enforce privacy to be false for pages
		$fields->init(array('privacy' => false));

		// Set the callback for the triggered custom fields
		$callback = array($fields->getHandler(), 'getOutput');

		$conditionalFields = array();

		// Get the custom fields for each of the steps.
		foreach($steps as &$step) {
			$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $page->id, 'dataType' => SOCIAL_TYPE_PAGE, 'visible' => 'edit'));

			// Trigger onEdit for custom fields.
			if (!empty($step->fields)) {
				$post = JRequest::get('post');
				$args = array(&$post, &$page, $errors);
				$fields->trigger('onEdit', SOCIAL_TYPE_PAGE, $step->fields, $args, $callback);

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

		// retrieve page's approval the rejected reason.
		$rejectedReasons = array();
		if ($page->isDraft()) {
			$rejectedReasons = $page->getRejectedReasons();
		}

		$this->set('conditionalFields', $conditionalFields);
		$this->set('page', $page);
		$this->set('steps', $steps);
		$this->set('rejectedReasons', $rejectedReasons);

		echo parent::display('site/pages/edit/default');
	}

	/**
	 * Method is invoked each time a step is saved. Responsible to redirect or show necessary info about the current step.
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function saveStep($session, $currentIndex, $completed = false)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Set message
		$this->info->set($this->getMessage());

		// If there's an error, redirect back user to the correct step and show the error.
		if ($this->hasErrors()) {
			return $this->redirect(ESR::pages(array('layout' => 'steps', 'step' => $session->step), false));
		}

		// Registration is not completed yet, redirect user to the appropriate step.
		return $this->redirect(ESR::pages(array('layout' => 'steps', 'step' => $session->step), false));
	}

	/**
	 * Renders the about view for pages
	 *
	 * @since   2.1.0
	 * @access  public
	 */
	private function about($page)
	{
		$pagesModel = ES::model('Pages');
		$steps = $pagesModel->getAbout($page);

		$this->set('steps', $steps);
		$this->set('layout', 'info');

		return parent::display('site/pages/about/default');
	}

	/**
	 * Renders the app view for pages
	 *
	 * @since   2.1.0
	 * @access  public
	 */
	public function app(&$app, $page)
	{
		$app->loadCss();

		// manually build up the language constant for the app title name
		$element = strtoupper($app->element);
		$group = strtoupper($app->group);
		$appTitle = JText::_('APP_' . $element . '_' . $group . '_TITLE');

		$this->page->title($page->getName() . ' - ' . $appTitle);
		$this->page->breadcrumb($appTitle);

		// Load the library.
		$lib = ES::apps();
		$contents = $lib->renderView(SOCIAL_APPS_VIEW_TYPE_EMBED, 'pages', $app, array('pageId' => $page->id));

		$layout = 'apps.' . $app->element;

		if ($layout == 'apps.followers') {
			$layout = 'members';
		}

		$this->set('layout', $layout);
		$this->set('contents', $contents);

		return parent::display('site/pages/app/default');
	}

	/**
	 * Default method to display the page entry page.
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function item($tpl = null)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		$id = $this->input->get('id', 0, 'int');
		$page = ES::page($id);

		// Check if the page is valid
		if (!$id || !$page->id || !$page->isPublished() || !$page->canAccess()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_NOT_FOUND'));
		}

		// The owner of the page is blocked by the creator of the page
		if ($this->my->id != $page->creator_uid && $this->my->isBlockedBy($page->creator_uid)) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_NOT_FOUND'));
		}

		// Set the page properties
		$title = $page->getName();

		// Set the page properties.
		$this->page->title($title);
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGES_PAGE_TITLE'), ESR::pages());
		$this->page->breadcrumb($page->getName());

		$this->set('page', $page);

		// Display private page contents;
		if (($page->isInviteOnly() || $page->isClosed()) && !$page->isMember() && !$this->my->isSiteAdmin()) {
			return parent::display('site/pages/restricted/default');
		}

		// Update the hit counter
		$page->hit();

		// Render page headers
		$page->renderHeaders();

		$hashtag = $this->input->get('tag', '', 'default');
		$hashtagAlias = $this->input->get('tag', '', 'default');

		$defaultDisplay = $this->config->get('pages.item.display', 'timeline');
		$layout = $this->input->get('type', '', 'cmd');

		// The current view could be for apps
		$appId = $this->input->get('appId', 0, 'int');

		if (!$appId && !$hashtag && !$layout) {
			$layout = $defaultDisplay;
		}

		// If the initial request is to view the group's about we should process them here
		if ($layout == 'info') {
			return $this->about($page);
		}

		if ($appId) {

			// Load the application.
			$app = ES::table('App');
			$app->load($appId);

			if (!$app->hasAccess($page->category_id)) {
				return $this->exception('COM_EASYSOCIAL_PAGE_DOES_NOT_HAVE_ACCESS');
			}

			return $this->app($app, $page);
		}

		// Initiate stream lib
		$stream = ES::stream();
		$contents = '';
		$filterId = $this->input->get('filterId', 0, 'int');

		if ($layout == 'filterForm') {
			$contents = $stream->getFilterForm($page->id, SOCIAL_TYPE_PAGE, $filterId);
		}

		// Determines if we should display the stream moderation
		$moderation = $page->getParams()->get('stream_moderation', null);
		$showPendingPostFilter = false;
		$totalPendingPost = 0;

		if ($moderation !== null && $moderation) {
			$showPendingPostFilter = true;
			$totalPendingPost = $page->getTotalPendingPosts();
		}

		// Get page's filter for this logged in user.
		$filters = $page->getFilters();

		// Get custom filter actions
		$customFilterActions = array();

		if ($page->canCreateStreamFilter()) {
			$customFilterActions['link'] = ESR::pages(array('layout' => 'item', 'id' => $page->getAlias(), 'type' => 'filterForm'));
			$customFilterActions['attributes'] = 'data-create-filter';
			$customFilterActions['icon'] = 'fa-plus';
		}

		// Determine if we should display news feed filter on the side
		$displayFeedsFilter = false;

		if (($page->isAdmin() || $page->isOwner() || $this->my->isSiteAdmin()) && $showPendingPostFilter) {
			$displayFeedsFilter = true;
		}

		if ($customFilterActions || $hashtag || ($filters && count($filters) > 0)) {
			$displayFeedsFilter = true;
		}

		// Get the timeline link
		$defaultDisplay = $this->config->get('pages.item.display', 'timeline');
		$timelinePermalink = $page->getPermalink();
		$aboutPermalink = ESR::pages(array('id' => $page->getAlias(), 'type' => 'info', 'layout' => 'item'));

		if ($defaultDisplay == 'info') {
			$aboutPermalink = $timelinePermalink;
			$timelinePermalink = ESR::pages(array('id' => $page->getAlias(), 'type' => 'timeline', 'layout' => 'item'));
		}

		$this->set('aboutPermalink', $aboutPermalink);
		$this->set('displayFeedsFilter', $displayFeedsFilter);
		$this->set('filters', $filters);
		$this->set('customFilterActions', $customFilterActions);
		$this->set('title', $title);
		$this->set('showPendingPostFilter', $showPendingPostFilter);
		$this->set('totalPendingPost', $totalPendingPost);
		$this->set('layout', $layout);
		$this->set('filterId', $filterId);
		$this->set('contents', $contents);

		if (!empty($contents)) {
			return parent::display('site/pages/item/default');
		}


		// If there's a hash tag, try to get the actual title to display on the site
		if ($hashtag) {
			$type = 'hashtag';

			$tag = $stream->getHashTag($hashtag);
			$hashtag = $tag->title;
		}

		// Retrieve story form for page
		$story = ES::get('Story', SOCIAL_TYPE_PAGE);
		$story->setCluster($page->id, SOCIAL_TYPE_PAGE);
		$story->showPrivacy(false);

		// Only Page admin able to select the Post As dropdown
		if ($page->isAdmin()) {
			$story->showPostAs(true);
		}

		if ($hashtag) {
			$story->setHashtags(array($hashtag));
		}

		// Only page followers allowed to post story updates on page page.
		if ($page->isMember() || $page->isAdmin()) {

			$stream->story = $story;
			$params = $page->getParams();
			$permissions = $params->get('stream_permissions', null);

			// If permissions has been configured before.
			if (!is_null($permissions)) {

				// If the user is not an admin, ensure that permissions has follower
				if (!$page->isAdmin() && !in_array('member', $permissions)) {
					unset($stream->story);
				}

				// If the user is an admin, ensure that permissions has admin
				if ($page->isAdmin() && !in_array('admin', $permissions) && !$page->isOwner()) {
					unset($stream->story);
				}
			}
		}

		//lets get the sticky posts 1st
		$stickies = $stream->getStickies(array('clusterId' => $page->id, 'clusterType'  => SOCIAL_TYPE_PAGE, 'limit' => 0));

		if ($stickies) {
			$stream->stickies = $stickies;
		}

		// lets get stream items for this page
		$options = array('clusterId' => $page->id, 'clusterType' => SOCIAL_TYPE_PAGE, 'nosticky' => true);

		// Set the stream display options
		$displayOptions = array();

		if ($layout != 'filterForm') {
			$filterId = $this->input->get('filterId', 0, 'int');
			$streamFilter = '';

			if ($filterId) {
				$streamFilter = ES::table('StreamFilter');
				$streamFilter->load($filterId);

				$hashtags = $streamFilter->getHashTag();
				$tags = explode(',', $hashtags);

				if ($tags) {
					$options['tag'] = $tags;

					$hashtagRule = $this->config->get('stream.filter.hashtag', '');
					if ($hashtagRule == 'and') {
						$options['matchAllTags'] = true;
					}

					// Set the hashtag in story panel form
					$story->setHashtags($tags);
				}
			}
		}

		// we only want streams thats has this hashtag associated.
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

		// RSS
		if ($this->config->get('stream.rss.enabled')) {
			$this->addRss(ESR::pages(array('id' => $page->getAlias(), 'layout' => 'item'), false));
		}

		$this->set('rssLink', $this->rssLink);
		$this->set('stream', $stream);
		$this->set('hashtag', $hashtag);
		$this->set('hashtagAlias', $hashtagAlias);
		$this->set('streamFilter', $streamFilter);

		return parent::display('site/pages/item/default');
	}

	/**
	 * Post process after a page is created
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function complete($page)
	{
		$this->info->set($this->getMessage());

		$url = ESR::pages(array(), false);

		if ($page->state == SOCIAL_STATE_PUBLISHED) {
			$url = ESR::pages(array('layout' => 'item', 'id' => $page->getAlias()), false);
		}

		$this->redirect($url);
	}

	/**
	 * Displays information from pages within a particular category
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function category()
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Get the category id from the query
		$id = $this->input->get('id', 0, 'int');

		$category = ES::table('PageCategory');
		$category->load($id);

		// Check if the category is valid
		if (!$id || !$category->id) {
			return $this->exception('COM_EASYSOCIAL_PAGES_INVALID_PAGE_ID');
		}

		// Load backend language file
		ES::language()->loadAdmin();

		// Set the page title to this category
		$this->page->title($category->get('title'));
		$this->page->description($category->getDescription());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PAGES'), ESR::pages());
		$this->page->breadcrumb($category->get('title'));

		$ids = $category->id;

		$categoryModel = ES::model('ClusterCategory');

		// check if this category is a container or not
		if ($category->container) {
			// Get all child ids from this category
			$childs = $categoryModel->getChildCategories($category->id, array(), SOCIAL_TYPE_PAGE, array('state' => SOCIAL_STATE_PUBLISHED));

			$childIds = array();

			foreach ($childs as $child) {
				$childIds[] = $child->id;
			}

			if (!empty($childIds)) {
				$ids = $childIds;
			}
		}

		// Get recent 10 pages from this category
		$options = array('sort' => 'random', 'category' => $ids, 'state' => SOCIAL_STATE_PUBLISHED);

		$model = ES::model('Pages');
		$pages = $model->getPages($options);

		// Get random followers from this category
		$randomMembers = $model->getRandomCategoryFollowers($ids, SOCIAL_CLUSTER_CATEGORY_MEMBERS_LIMIT);

		// Get total pages within a category
		$totalPages = $model->getTotalPages(array('category_id' => $ids));

		// Get total albums within a category
		$totalAlbums = $model->getTotalAlbums(array('category_id' => $ids));

		// Get the stream for this page
		$stream = ES::stream();
		$stream->get(array('clusterCategory' => $ids, 'clusterType' => SOCIAL_TYPE_PAGE), array('perspective' => 'dashboard'));

		// Get random albums for pages in this category
		$randomAlbums = $model->getRandomAlbums(array('category_id' => $ids, 'core' => false));

		$childs = $categoryModel->getImmediateChildCategories($category->id, SOCIAL_TYPE_PAGE);

		$this->set('randomAlbums', $randomAlbums);
		$this->set('stream', $stream);
		$this->set('totalPages', $totalPages);
		$this->set('randomMembers', $randomMembers);
		$this->set('pages', $pages);
		$this->set('category', $category);
		$this->set('totalAlbums', $totalAlbums);
		$this->set('childs', $childs);

		parent::display('site/pages/category/default');
	}

	/**
	 * Post process after a user is rejected to like the page
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function reject($page)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		$permalink = $page->getPermalink(false);

		$returnUrl = $this->getReturnUrl($permalink);

		$this->redirect($returnUrl);
	}

	/**
	 * Post process after the page avatar is removed
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function removeAvatar(SocialPage $page)
	{
		// Set message
		$this->info->set($this->getMessage());

		$permalink = $page->getPermalink(false);

		$this->redirect($permalink);
	}

	/**
	 * Post process after a user is deleted from the page
	 *
	 * @since   2.0
	 * @access  public
	 * @param   SocialPage
	 */
	public function removeFollower(SocialPage $page)
	{
		$returnUrl = $this->getReturnUrl($page->getPermalink(false));

		// Set message
		$this->info->set($this->getMessage());

		$this->redirect($returnUrl);
	}

	/**
	 * Post process after promote a user as admin
	 *
	 * @since   2.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function promote(SocialPage $page)
	{
		$returnUrl = $this->getReturnUrl($page->getPermalink(false));

		$this->info->set($this->getMessage());

		$this->redirect($returnUrl);
	}

	/**
	 * Post process after demoted a user
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function demote(SocialPage $page)
	{
		$returnUrl = $this->getReturnUrl($page->getPermalink(false));

		$this->info->set($this->getMessage());

		$this->redirect($returnUrl);
	}

	/**
	 * Post process after a user is approved to join the page
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function approve($page = null)
	{
		$this->info->set($this->getMessage());

		// Default redirect
		$redirect = ESR::pages(array('layout' => 'item', 'id' => $page->getAlias()), false);

		$redirect = $this->getReturnUrl($redirect);

		$this->redirect($redirect);
	}

	/**
	 * Post process after a user is invited to join the page
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function invite(SocialPage $page)
	{
		// Set message
		$this->info->set($this->getMessage());

		$redirect = $this->getReturnUrl($page->getPermalink(false));

		$this->redirect($redirect);
	}

	/**
	 * Post process after a page is published
	 *
	 * @since   2.0
	 * @access  public
	 * @param   SocialPage
	 */
	public function approvePage()
	{
		// Set message
		$this->info->set($this->getMessage());

		$this->redirect(ESR::pages(array(), false));
	}

	/**
	 * Post process after a page is rejected
	 *
	 * @since   2.0
	 * @access  public
	 * @param   SocialPage
	 */
	public function rejectPage()
	{
		// Set message
		$this->info->set($this->getMessage());

		$this->redirect(ESR::pages(array(), false));
	}


	/**
	 * Post process after a page is set as featured
	 *
	 * @since   2.0
	 * @access  public
	 * @param   SocialPage
	 */
	public function setFeatured($page)
	{
		$this->info->set($this->getMessage());

		$permalink = $page->getPermalink(false);

		$returnUrl = $this->getReturnUrl($permalink);

		$this->redirect($returnUrl);
	}

	/**
	 * Post process after a page is removed from being featured
	 *
	 * @since   2.0
	 * @access  public
	 * @param   SocialPage
	 */
	public function removeFeatured($page)
	{
		$this->info->set($this->getMessage());

		$permalink = $page->getPermalink(false);

		$returnUrl = $this->getReturnUrl($permalink);

		$this->redirect($returnUrl);
	}

	/**
	 * Post process after category has been selected
	 *
	 * @since   2.0
	 * @access  public
	 * @return
	 */
	public function selectCategory()
	{
		// Set message
		$this->info->set($this->getMessage());

		// @task: Check for errors.
		if ($this->hasErrors()) {
			return $this->redirect(ESR::pages(array(), false));
		}

		// @task: We always know that after selecting the category type, the next step would always be the first step.
		$url = ESR::pages(array('layout' => 'steps', 'step' => 1), false);

		return $this->redirect($url);
	}

	/**
	 * Post process when a page is deleted
	 *
	 * @since   2.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function delete()
	{
		// Set message
		$this->info->set($this->getMessage());

		$this->redirect(ESR::pages(array(), false));
	}

	/**
	 * Post process when a page is unpublished
	 *
	 * @since   2.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function unpublish()
	{
		// Set message
		$this->info->set($this->getMessage());

		$this->redirect(ESR::pages(array(), false));
	}

	/**
	 * Post process after saving page
	 *
	 * @since   2.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function update($page)
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Set message
		$this->info->set($this->getMessage());

		$url = '';
		if ($page->isPending()) {
			$url = ESR::pages(array(), false);
		} else {
			$url = $page->getPermalink(false);
		}

		return $this->redirect($url);
	}

	/**
	 * Post process after a user response to the invitation.
	 *
	 * @since   2.1
	 * @access  public
	 * @param   string
	 */
	public function respondInvitation($page, $action)
	{
		$this->info->set($this->getMessage());

		if ($action == 'reject') {
			$redirect = ESR::pages(array('filter' => 'invited'), false);
			return $this->redirect($redirect);
		}

		// if accept from email n not logged in,
		// we should redirect to dashboard
		if (!$page->canAccess()) {
			$redirect = ESR::dashboard();
		}

		$redirect = ESR::pages(array('layout' => 'item', 'id' => $page->getAlias()), false);
		return $this->redirect($redirect);
	}


	/**
	 * Post process after saving page filter
	 *
	 * @since   2.0
	 * @access  public
	 * @param   StreamFilter object
	 * @return
	 */
	public function saveFilter($filter, $pageId)
	{
		// Unauthorized users should not be allowed to access this page.
		ES::requireLogin();

		// Set message
		$this->info->set($this->getMessage());

		$page = ES::page($pageId);

		$this->redirect(ESR::pages(array('layout' => 'item', 'id' => $page->getAlias()), false));
	}

	/**
	 * Allows viewer to download a file from the page
	 *
	 * @since   2.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function download()
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Currently only registered users are allowed to view a file.
		ES::requireLogin();

		// Get the file id from the request
		$fileId = JRequest::getInt('fileid', null);

		$file = ES::table('File');
		$file->load($fileId);

		if (!$file->id || !$fileId) {
			// Throw error message here.
			$this->redirect(ESR::dashboard(array(), false));
		}

		// Load up the page
		$page = ES::page($file->uid);

		// Ensure that the user can really view this page
		if (!$page->canViewItem()) {
			// Throw error message here.
			$this->redirect(ESR::dashboard(array(), false));
		}

		$file->download();
		exit;
	}

	/**
	 * Allows viewer to download a conversation file
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function preview()
	{
		// Check if this feature is enabled.
		$this->checkFeature();

		// Get the file id from the request
		$fileId = JRequest::getInt('fileid', null);

		$file = ES::table('File');
		$file->load($fileId);

		if (!$file->id || !$fileId) {
			// Throw error message here.
			$this->redirect(ESR::dashboard(array(), false));
		}

		// Load up the page
		$page = ES::page($file->uid);

		// Ensure that the user can really view this page
		if (!$page->canViewItem()) {
			// Throw error message here.
			$this->redirect(ESR::dashboard(array(), false));
		}

		$file->preview();
		exit;
	}

}
