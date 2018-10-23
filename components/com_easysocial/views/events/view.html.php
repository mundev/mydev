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

class EasySocialViewEvents extends EasySocialSiteView
{
	/**
	 * Checks if the event feature is enabled.
	 *
	 * @since  1.3
	 * @access public
	 */
	private function checkFeature()
	{
		if (!$this->config->get('events.enabled')) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_DISABLED'), SOCIAL_MSG_ERROR);

			$this->info->set($this->getMessage());

			$this->redirect(ESR::dashboard(array(), false));
			$this->close();
		}
	}


	/**
	 * Renders the about page
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function about($event)
	{
		$model = ES::model('Events');
		$steps = $model->getAbout($event);

		$options = array('id' => $event->getAlias(), 'page' => 'info', 'layout' => 'item', 'external' => true);
		$infoLink = FRoute::events($options, false);

		// generate canonical link for the event info page
		$this->page->canonical($infoLink);

		$this->set('layout', 'info');
		$this->set('event', $event);
		$this->set('steps', $steps);

		return parent::display('site/events/about/default');
	}

	/**
	 * Renders the app view for event
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function app($event, $app)
	{
		if (!$app->hasAccess($event->category_id)) {
			return $this->exception('COM_EASYSOCIAL_EVENT_DOES_NOT_HAVE_ACCESS');
		}

		// lets load backend language as well.
		ES::language()->loadAdmin();

		$app->loadCss();

		$event->renderPageTitle($app->get('title'), 'events');

		$appsLib  = ES::apps();
		$contents = $appsLib->renderView(SOCIAL_APPS_VIEW_TYPE_EMBED, 'events', $app, array('eventId' => $event->id));

		$layout = 'apps.' . $app->element;

		// To know which is the active item on the cover navigation
		if ($app->element == 'guests') {
			$layout = 'guests';
		}

		$this->set('event', $event);
		$this->set('contents', $contents);
		$this->set('layout', $layout);

		return parent::display('site/events/app/default');
	}

	/**
	 * Renders the calendar layout
	 *
	 * @since   2.2.3
	 * @access  public
	 */
	public function calendar()
	{
		$unix = $this->input->getString('date', ES::date()->toUnix());

		$day = date('d', $unix);
		$month = date('m', $unix);
		$year = date('Y', $unix);

		// Create a calendar object
		$calendar = new stdClass();
		$calendar->year = $year;
		$calendar->month = $month;

		// Configurable start of week
		$startOfWeek = $this->config->get('events.startofweek');

		// Here we generate the first day of the month
		$calendar->first_day = mktime(0, 0, 0, $month, 1, $year);

		// This gets us the month name
		$calendar->title = date('F', $calendar->first_day);

		// Sets the calendar header
		$date = ES::date($unix, false);
		$calendar->header = $date->format(JText::_('COM_EASYSOCIAL_DATE_MY'));

		// Here we find out what day of the week the first day of the month falls on
		$calendar->day_of_week = date('D', $calendar->first_day);

		// Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
		$dayOfWeek = $date->getDayOfWeek($calendar->day_of_week);

		// Day of week is dependent on the start of the week
		if ($dayOfWeek < $startOfWeek) {
			$calendar->blank = 7 - $startOfWeek + $dayOfWeek;
		} else {
			$calendar->blank = $dayOfWeek - $startOfWeek;
		}

		// Due to timezone issue, we will use the mid date of the month to get the next / previous months. #300
		$midMonth = ES::date($date->format('Y-m') . '-15');

		// Previous month
		$calendar->previous = strtotime('-1 month', $midMonth->toUnix());

		// Next month
		$calendar->next = strtotime('+1 month', $midMonth->toUnix());

		// Determine how many days are there in the current month
		$calendar->days_in_month = date('t', $calendar->first_day);

		$categoryId = $this->input->get('categoryId', 0, 'int');
		$clusterId = $this->input->get('clusterId', 0, 'int');
		
		$this->set('calendar', $calendar);
		$this->set('categoryId', $categoryId);
		$this->set('clusterId', $clusterId);

		return parent::display('site/events/calendar/default');
	}

	/**
	 * Displays the event listing main page.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function display($tpl = null)
	{
		$this->checkFeature();

		// Check for profile completeness
		ES::checkCompleteProfile();

		// Set Meta data
		ES::setMeta();

		// Add canonical tag for event listing page
		$this->page->canonical(ESR::events(array('external' => true)));

		$model = ES::model('Events');

		// Default page title
		$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS';

		// Get the user's id
		// Here means we are viewing the user's event
		$userid = $this->input->get('userid', null, 'int');
		$user = ES::user($userid);

		// uid is for cluster. if exist, means we are viewing cluster's event
		$uid = $this->input->get('uid', null, 'int');

		// Get the cluster type group/page
		$eventCluster = $this->input->get('type', '', 'string');

		// [no longer use this]
		$showSidebar = !$uid && !$userid;

		// If no uid or userid, means user is viewing the browsing all events view
		// We define this browse view same like $showsidebar.
		// so it won't break when other customer that still using $showsidebar
		$browseView = !$uid && !$userid;

		// Get the filter
		$filter = $this->input->get('filter', 'all', 'string');
		$ordering = $this->input->get('ordering', 'start', 'word');
		$includePast = $this->input->get('includePast', false, 'bool');

		// Check if the current filter is allowed
		$allowedFilter = array('date', 'week1', 'week2', 'all', 'featured', 'mine', 'participated', 'invited', 'going', 'pending', 'maybe', 'notgoing', 'past', 'ongoing', 'upcoming', 'nearby', 'review', 'created');

		if (!in_array($filter, $allowedFilter)) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_EVENTS_INVALID_FILTER_ID'));
		}

		// Since not logged in users cannot filter by 'invited' or 'mine' and etc, they shouldn't be able to access these filters at all
		$disallowedGuestFilters = array('invited', 'mine', 'going', 'maybe', 'notgoing', 'participated');

		if ($this->my->guest && in_array($filter, $disallowedGuestFilters)) {
			return $this->app->redirect(ESR::dashboard(array(), false));
		}

		// Theme related settings
		$showSorting = true;
		$showPastFilter = true;
		$showDistance = false;
		$showDistanceSorting = false;
		$hasLocation = false;
		$showDateNavigation = false;
		$distance = $this->config->get('events.nearby.radius');

		// Flag to see if this process should be delayed
		// Currently it is for the case of nearby filter
		// Nearby filter can only work if the location is retrieved through javascript
		$delayed = false;

		// Default options for listing
		$options = array(
						'state' => SOCIAL_STATE_PUBLISHED,
						'ordering' => $ordering,
						'type' => $this->my->isSiteAdmin() ? 'all' : 'user',
						'featured' => false,
						'limit' => ES::getLimit('events_limit')
					);

		// Default create event URL
		$createUrl = array('layout' => 'create');

		if ($options['ordering'] == 'created') {
			$options['direction'] = 'desc';
		}

		$cluster = false;

		// If listing cluster events, we need to fetch all events and not just events the user can see
		if ($eventCluster) {
			$options['type'] = 'all';
		}

		if ($eventCluster == SOCIAL_TYPE_PAGE) {
			$cluster = ES::cluster(SOCIAL_TYPE_PAGE, $uid);
			$options['page_id'] = $cluster->id;
			$createUrl['page_id'] = $cluster->id;
		}

		if ($eventCluster == SOCIAL_TYPE_GROUP) {
			$cluster = ES::cluster(SOCIAL_TYPE_GROUP, $uid);
			$options['group_id'] = $cluster->id;
			$createUrl['group_id'] = $cluster->id;
		}

		$this->set('cluster', $cluster);

		// Check if the cluster is private
		// If yes, we show restricted page instead
		if ($cluster && !$cluster->canViewEvent()) {
			return parent::display('site/events/restricted');
		}

		// We do not want to include past events by default unless explicitly enabled
		if (!$includePast && $filter != 'mine') {
			$options['ongoing'] = true;
			$options['upcoming'] = true;
		}

		// Set the route options so that filter can add extra parameters
		$routeOptions = array('option' => SOCIAL_COMPONENT_NAME, 'view' => 'events', 'filter' => $filter);

		if ($cluster) {
			$title = JText::_('COM_ES_CREATED_EVENTS') . ' - ' . $cluster->getTitle();
			$routeOptions['uid'] = $cluster->getAlias();
			$routeOptions['type'] = $cluster->cluster_type;

			// Increment the hit counter
			$cluster->hit();
		}

		// If user is an admin then he should be able to see all events
		// If not then we set guestuid as the user id without any guest state
		// This is because event list should consist of
		// Open, Closed, and for Invite Only if the user is part of it
		// If filter by guest state, then types is always all because we only get what the user is involved

		// [Category Filter]
		// Determines if this request is filtering events by specific category
		$categoryId = $this->input->get('categoryid', 0, 'int');
		$activeCategory = false;

		if ($categoryId) {

			$activeCategory = ES::table('EventCategory');
			$state = $activeCategory->load($categoryId);

			if (!$state) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_EVENTS_INVALID_CATEGORY_ID'));
			}

			$options['category'] = $activeCategory->id;

			// check if this category is a container or not
			if ($activeCategory->container) {
				// Get all child ids from this category
				$categoryModel = ES::model('ClusterCategory');
				$childs = $categoryModel->getChildCategories($activeCategory->id, array(), SOCIAL_TYPE_EVENT, array('state' => SOCIAL_STATE_PUBLISHED));

				$childIds = array();

				foreach ($childs as $child) {
					$childIds[] = $child->id;
				}

				if (!empty($childIds)) {
					$options['category'] = $childIds;
				}
			}

			// Set the filter to all since it's the same as filtering by all
			$filter = 'all';
			$title = $activeCategory->getTitle();
			$routeOptions['categoryid'] = $activeCategory->getAlias();
		}

		$featuredEvents = array();

		// Process featured events
		if (($filter === 'all' && ($browseView || $uid))) {

			$featuredOptions = array(
									'featured' => true,
									'state' => SOCIAL_STATE_PUBLISHED
								);

			if ($eventCluster == SOCIAL_TYPE_PAGE) {
				$featuredOptions['page_id'] = $uid;
			}

			if ($eventCluster == SOCIAL_TYPE_GROUP) {
				$featuredOptions['group_id'] = $uid;
			}

			if ($userid && $user->id) {
				$options['creator_type'] = SOCIAL_TYPE_USER;
				$featuredOptions['creator_uid'] = $userid;
			}

			if ($activeCategory) {
				$featuredOptions['category'] = $activeCategory->id;
			}

			$featuredEvents = $model->getEvents($featuredOptions);
		}

		// Filter by featured events
		if ($filter === 'featured') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_FEATURED';
			$options['featured'] = true;
		}

		// Filter by current user's event
		if ($filter === 'mine') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_MINE';

			$options['creator_uid'] = $this->my->id;
			$options['creator_type'] = SOCIAL_TYPE_USER;
			$options['creator_join'] = true;
			$options['type'] = 'all';
			$options['featured'] = 'all';
			$includePast = true;
		}

		if ($filter == 'review') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_REVIEW';

			// events that pending user's review.
			$options['creator_uid'] = $this->my->id;
			$options['creator_type'] = SOCIAL_TYPE_USER;
			$options['state'] = SOCIAL_CLUSTER_DRAFT;
		}

		// Filter by participated events
		if ($filter === 'participated' && !$browseView) {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_PARTICIPATED';

			$options['creator_uid'] = $user->id;
			$options['creator_type'] = SOCIAL_TYPE_USER;
			$options['creator_join'] = true;
			$options['type'] = 'all';
			$options['featured'] = 'all';
		}

		// Filter by invited events
		if ($filter === 'invited') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_INVITED';
			$options['gueststate'] = SOCIAL_EVENT_GUEST_INVITED;
		}

		// Filter by attending
		if ($filter === 'going') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_GOING';
			$options['gueststate'] = SOCIAL_EVENT_GUEST_GOING;
		}

		// Filter by pending events
		if ($filter === 'pending') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_PENDING';
			$options['gueststate'] = SOCIAL_EVENT_GUEST_PENDING;
		}

		// Filter by maybe state
		if ($filter === 'maybe') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_MAYBE';
			$options['gueststate'] = SOCIAL_EVENT_GUEST_MAYBE;
		}

		// Filter by not going state
		if ($filter === 'notgoing') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_NOTGOING';
			$options['gueststate'] = SOCIAL_EVENT_GUEST_NOTGOING;
		}

		// Filters that are related to the current logged in user
		$filtersRelatedToUser = array('invited', 'going', 'pending', 'maybe', 'notgoing');

		if (in_array($filter, $filtersRelatedToUser)) {
			$options['guestuid'] = $this->my->id;
			$options['type'] = 'all';
		}

		// Filter by past events
		if ($filter === 'past') {
			$showPastFilter = false;
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_PAST';

			$options['ordering'] = 'created';
			$options['direction'] = 'desc';
			$options['past'] = true;

			// Past event should show all events regardless of featured status. #1402
			if (isset($options['featured'])) {
				unset($options['featured']);
			}

			// For past events, these needs to be off
			$options['ongoing'] = false;
			$options['upcoming'] = false;
		}

		if ($filter === 'ongoing') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_ONGOING';
			$options['ongoing'] = true;
		}

		if ($filter === 'upcoming') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_UPCOMING';
			$options['upcoming'] = true;
		}

		// Date navigation
		$activeDate = false;
		$navigation = new stdClass();

		// Get the date filters
		$activeDateFilter = '';

		// Filtering by specific date
		if ($filter == 'date') {
			// We do not want to show sorting
			$showSorting = false;
			$showPastFilter = false;
			$showDateNavigation = true;

			// Default to today
			$activeDateFilter = 'today';
			$dateString = $this->input->get('date', '', 'string');

			// The only way to determine if the user is filtering by today, tomorrow, month or year is to break up the "-"
			if (!$dateString) {
				$parts = array();
			} else {
				$parts = explode('-', $dateString);
			}

			$totalParts = count($parts);

			// Try to see if it is tomorrow.
			if ($totalParts == 3) {
				$tomorrow = ES::date('+1 day')->format('Y-m-d');

				if ($tomorrow == $dateString) {
					$activeDateFilter = 'tomorrow';
				} else {
					$activeDateFilter = 'normal';
				}
			}

			if ($totalParts == 2) {
				$activeDateFilter = 'month';
				$year = $parts[0];
				$month = $parts[1];
			}

			if ($totalParts == 1) {
				$activeDateFilter = 'year';
				$year = (int) $dateString;
			}

			// Regardless of the include past option or not, we should just display the events since they are filtered by date
			if (isset($options['ongoing'])) {
				unset($options['ongoing']);
			}

			if (isset($options['upcoming'])) {
				unset($options['upcoming']);
			}
		}

		$dateLinks = new stdClass();
		$dateLinks->today = ES::event()->getFilterPermalink(array('filter' => 'date', 'date' => ES::date()->format('Y-m-d'), 'cluster' => $cluster));
		$dateLinks->tomorrow = ES::event()->getFilterPermalink(array('filter' => 'date', 'date' => ES::date('+1 day')->format('Y-m-d'), 'cluster' => $cluster));
		$dateLinks->month = ES::event()->getFilterPermalink(array('filter' => 'date', 'date' => ES::date()->format('Y-m'), 'cluster' => $cluster));
		$dateLinks->year = ES::event()->getFilterPermalink(array('filter' => 'date', 'date' => ES::date()->format('Y'), 'cluster' => $cluster));

		if ($activeDateFilter == 'today') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_TODAY';

			// Get today's date
			$activeDate = ES::date();
			$start = $activeDate->format('Y-m-d 00:00:00');
			$end = $activeDate->format('Y-m-d 23:59:59');

			$options['start-after'] = $start;
			$options['start-before'] = $end;

			// Get the date navigation above the events
			$yesterday = ES::date('-1 day')->format('Y-m-d');
			$tomorrow = ES::date('+1 day')->format('Y-m-d');

			$navigation->previous = $yesterday;
			$navigation->next = $tomorrow;
		}

		if ($activeDateFilter == 'tomorrow') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_TOMORROW';

			// Get today's date
			$activeDate = ES::date('+1 day');
			$start = $activeDate->format('Y-m-d 00:00:00');
			$end = $activeDate->format('Y-m-d 23:59:59');

			$options['start-after'] = $start;
			$options['start-before'] = $end;

			$navigation->previous = ES::date()->format('Y-m-d');
			$navigation->next = ES::date('+2 days')->format('Y-m-d');
		}

		if ($activeDateFilter == 'month') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_MONTH';

			$start = $parts[0] . '-' . $parts[1] . '-01';

			// Need to get the month's maximum day
			$monthDate = ES::date($start, false);
			$maxDay = $monthDate->format('t');

			$end = $parts[0] . '-' . $parts[1] . '-' . str_pad($maxDay, 2, '0', STR_PAD_LEFT);

			$activeDate = ES::date($dateString, false);

			$this->page->description(JText::sprintf('COM_ES_EVENTS_META_AVAILABLE_EVENTS', $activeDate->format('DATE_FORMAT_LC1')));

			$options['start-after'] = $start . ' 00:00:00';
			$options['start-before'] = $end . ' 23:59:59';

			// due to the timezone issue, for safety purposely, we will use the mid date of the month to get the next / previous months. #5553
			$previous = ES::date($dateString .'-15')->modify('-1 month');
			$next = ES::date($dateString .'-15')->modify('+1 month');

			// Set the navigation dates
			$navigation->previous = $previous->format('Y-m');
			$navigation->next = $next->format('Y-m');

			// Should also include featured event
			$options['featured'] = 'all';
		}

		if ($activeDateFilter == 'year') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_YEAR';

			$currentYear = (int) ES::date()->format('Y');

			$yearDiff = $year - $currentYear;

			$startStr = 'first day of January +' . $yearDiff . ' year';
			$endStr = 'last day of December +' . $yearDiff . ' year';

			$activeDate = ES::date($startStr, false);

			$start = $activeDate->format('Y-m-d 00:00:00');
			$end = ES::date($endStr)->format('Y-m-d 23:59:59');

			$options['start-after'] = $start;
			$options['start-before'] = $end;

			$navigation->previous = $year - 1;
			$navigation->next = $year + 1;

			// Should also include featured event
			$options['featured'] = 'all';
		}

		if ($activeDateFilter == 'normal') {
			// Depending on the input format.
			// Could be by year, year-month or year-month-day
			$now = ES::date();
			list($nowYMD, $nowHMS) = explode(' ', $now->toSql(true));

			// Get the input for the date
			$input = $this->input->get('date', '', 'string');

			// We need segments to be populated. If no input is passed, then it is today, and we use today as YMD then
			if (!$input) {
				$input = $nowYMD;
			}

			$activeDate = ES::date($input, false);

			$segments = explode('-', $input);

			$start = $nowYMD;
			$end = $nowYMD;

			// Depending on the amount of segments
			// 1 = filter by year
			// 2 = filter by month
			// 3 = filter by day

			$mode = count($segments);

			if ($mode == 1) {
				$start = $segments[0] . '-01-01';
				$end = $segments[0] . '-12-31';
			}

			if ($mode == 2) {
					$start = $segments[0] . '-' . $segments[1] . '-01';
					// Need to get the month's maximum day
					$monthDate = ES::date($start);
					$maxDay = $monthDate->format('t');

					$end = $segments[0] . '-' . $segments[1] . '-' . str_pad($maxDay, 2, '0', STR_PAD_LEFT);
			}

			if ($mode == 3) {
				$start = $segments[0] . '-' . $segments[1] . '-' . $segments[2];
				$end = $segments[0] . '-' . $segments[1] . '-' . $segments[2];
			}

			$options['dateRange'] = true;
			$options['range-start'] = $start . ' 00:00:00';
			$options['range-end'] = $end . ' 23:59:59';
			$options['featured'] = 'all';

			$previous = ES::date($input, false)->modify('-1 day');
			$next = ES::date($input, false)->modify('+1 day');

			// Set the navigation dates
			$navigation->previous = $previous->format('Y-m-d');
			$navigation->next = $next->format('Y-m-d');
		}

		if ($filter === 'week1') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_UPCOMING_1WEEK';

			$start = ES::date()->format('Y-m-d 00:00:00');
			$end = ES::date('+1 week')->format('Y-m-d 23:59:59');

			$options['start-after'] = $start;
			$options['start-before'] = $end;

			$showPastFilter = false;
		}

		// Filter by upcoming events (2 weeks)
		if ($filter === 'week2') {
			$title = 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_UPCOMING_2WEEK';

			$now = ES::date();
			$week2 = ES::date($now->toUnix() + 60*60*24*14);

			$options['start-after'] = $now->toSql();
			$options['start-before'] = $week2->toSql();

			$showPastFilter = false;
		}

		// Check if there is any location data
		$userLocation = JFactory::getSession()->get('events.userlocation', array(), SOCIAL_SESSION_NAMESPACE);

		// Filter by nearby location
		if ($filter === 'nearby') {
			$distance = $this->input->get('distance', $this->config->get('events.nearby.radius'), 'int');

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

				// We do not want to include past events here
				if (!$includePast) {
					$options['ongoing'] = true;
					$options['upcoming'] = true;
				}

				$showDistance = true;
				$showDistanceSorting = true;
			}
		}

		// if viewer viewing another person event page, then we only want to fetch the event from person beign viewed
		if ($userid && $user->id) {
			$options['creator_uid'] = $user->id;
			$options['creator_type'] = SOCIAL_TYPE_USER;
			$options['type'] = 'all';
			$options['featured'] = 'all';
		}

		$events = array();

		// Get a list of events if this is not delayed?
		if (!$delayed) {
			$events = $model->getEvents($options);
		}

		// Get the pagination
		$pagination = $model->getPagination();

		$clusterOptions = array();

		if ($eventCluster == SOCIAL_TYPE_PAGE) {
			$clusterOptions['page_id'] = $cluster->id;
		}

		if ($eventCluster == SOCIAL_TYPE_GROUP) {
			$clusterOptions['group_id'] = $cluster->id;
		}

		// Prepare the counters on the sidebar
		$counters = new stdClass();
		$sortingUrls = array();

		// Get total all event
		$allOptions = array('state' => SOCIAL_STATE_PUBLISHED, 'type' => $this->my->isSiteAdmin() ? 'all' : 'user', 'ongoing' => true, 'upcoming' => true);
		$allOptions = array_merge($allOptions, $clusterOptions);

		$counters->all = $model->getTotalEvents($allOptions);
		$counters->featured = $model->getTotalFeaturedEvents($clusterOptions);
		$counters->created = $model->getTotalCreatedJoinedEvents(null, $clusterOptions);
		$counters->invited = $model->getTotalInvitedEvents(null, $clusterOptions);
		$counters->week1 = $model->getTotalWeekEvents(1, null, $clusterOptions);
		$counters->week2 = $model->getTotalWeekEvents(2, null, $clusterOptions);
		$counters->past = $model->getTotalPastEvents(null, $clusterOptions);
		$counters->today = $model->getTotalEventsToday('today', $clusterOptions);
		$counters->tomorrow = $model->getTotalEventsTomorrow('', $clusterOptions);
		$counters->month = $model->getTotalMonthEvents('', null, $clusterOptions);
		$counters->year = $model->getTotalYearEvents('', null, $clusterOptions);
		$counters->totalPendingEvents = 0;

		if (!$browseView) {
			if ($eventCluster) {
				// If user is viewing cluster's event,
				// we get total of all events regardless created by that logged in user or not
				$clusterOptions['viewClusterEvents'] = true;
				$clusterOptions['featured'] = false;
				$counters->created = $model->getTotalCreatedJoinedEvents(null, $clusterOptions);
			} else {
				$counters->created = $model->getTotalCreatedJoinedEvents($user->id, array('excludeJoinEvent' => true, 'ongoing' => true, 'upcoming' => true));
				$counters->participated = $model->getTotalCreatedJoinedEvents($user->id, array('ongoing' => true, 'upcoming' => true));
			}
		}

		// retrieve pending review events count
		if ($this->my->id != 0) {
			// Get the total number of groups the user created but required review
			$counters->totalPendingEvents = $this->my->getTotalPendingReview(SOCIAL_TYPE_EVENT);
		}

		// If the user is viewing others' listing, we should respect that
		if (!$browseView && !$cluster) {
			$routeOptions['userid'] = $user->getAlias();
		}

		// We use start as key because order is always start by default, and it is the page default link
		$sortingUrls['start'] = array('nopast' => ESR::events($routeOptions));

		if (!$delayed) {

			// Only need to create the "order by created" link.
			if ($showSorting) {
				$sortingUrls['created'] = array('nopast' => ESR::events(array_merge($routeOptions, array('ordering' => 'created'))));
			}

			// Only need to create the "order by distance" link.
			if ($showDistanceSorting) {
				$sortingUrls['distance'] = array('nopast' => ESR::events(array_merge($routeOptions, array('ordering' => 'distance'))));
			}

			// If past filter is displayed on the page, then we need to generate the past links counter part
			if ($showPastFilter) {
				$sortingUrls['start']['past'] = ESR::events(array_merge($routeOptions, array('includePast' => 1)));

				// Only need to create the "order by created" link.
				if ($showSorting) {
					$sortingUrls['created']['past'] = ESR::events(array_merge($routeOptions, array('ordering' => 'created', 'includePast' => 1)));
				}

				// Only need to create the "order by distance" link.
				if ($showDistanceSorting) {
					$sortingUrls['distance']['past'] = ESR::events(array_merge($routeOptions, array('ordering' => 'distance', 'includePast' => 1)));
				}
			}
		}

		$dateStrings = $this->input->get('date', '', 'string');

		// Add canonical for each of the different day.
		if (!empty($dateStrings)) {
			$activeDates = ES::date($dateStrings, false);
			$eventTimestamp = strtotime($activeDates->toSql());

			$finalDate = ES::event()->getDateObject($eventTimestamp);
			$checkCurrentDay = ES::event()->isCurrentDay($finalDate);

			if ($checkCurrentDay) {
				$this->page->canonical('index.php?option=com_easysocial&view=events&filter=date');
			} else {
				$this->page->canonical('index.php?option=com_easysocial&view=events&filter=date&date=' . $dateStrings);
			}
		}

		// Set page attributes
		$this->page->title($title);
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS'));

		// Determines if the "showMyEvents" filter link should appear
		$showMyEvents = true;
		$showPendingEvents = false;
		$showTotalInvites = false;

		if ($this->my->id) {
			$showPendingEvents = $counters->totalPendingEvents > 0;
			$showTotalInvites = $counters->invited > 0;
		}

		// Generate empty text here
		$emptyText = 'COM_EASYSOCIAL_EVENTS_EMPTY_' . strtoupper($filter);

		if ($cluster) {
			$emptyText = 'COM_ES_CLUSTER_EVENTS_EMPTY';
		}

		// If not browse view, we default the filter to 'created'
		if (!$browseView) {

			$filter = $filter != 'all' ? $filter : 'created';

			// If this is not browseview and user is viewing others event listing
			// Hide the pending and invite filter
			if ($user->id != $this->my->id) {
				$showPendingEvents = false;
				$showTotalInvites = false;
			}

			// If this is viewing profile's event, we display a different empty text
			$emptyText = 'COM_ES_EVENTS_EMPTY_' . strtoupper($filter);

			if (!$user->isViewer()) {
				$emptyText = 'COM_ES_EVENTS_USER_EMPTY_' . strtoupper($filter);
			}
		}

		// We gonna show the 'showMyEvents' only if the user is viewing browse all group page
		if (!$this->my->id || !$browseView) {
			$showMyEvents = false;
		}

		// Generate links for filters
		$filtersLink = new stdClass;
		$linkOptions = array('cluster' => $cluster);

		// If the user is viewing others' listing, we should respect that
		if (!$browseView && !$cluster) {
			$linkOptions['userid'] = $user->getAlias();
		}

		$filtersLink->all = ES::event()->getFilterPermalink(array_merge(array('filter' => 'all'),$linkOptions));
		$filtersLink->featured = ES::event()->getFilterPermalink(array_merge(array('filter' => 'featured'),$linkOptions));
		$filtersLink->pending = ES::event()->getFilterPermalink(array_merge(array('filter' => 'pending'),$linkOptions));
		$filtersLink->invited = ES::event()->getFilterPermalink(array_merge(array('filter' => 'invited'),$linkOptions));
		$filtersLink->created = ES::event()->getFilterPermalink(array_merge(array('filter' => 'created'),$linkOptions));
		$filtersLink->participated = ES::event()->getFilterPermalink(array_merge(array('filter' => 'participated'),$linkOptions));
		$filtersLink->past = ES::event()->getFilterPermalink(array_merge(array('filter' => 'past'),$linkOptions));

		$this->set('filtersLink', $filtersLink);
		$this->set('emptyText', $emptyText);
		$this->set('showSidebar', $showSidebar);
		$this->set('browseView', $browseView);
		$this->set('showMyEvents', $showMyEvents);
		$this->set('showPendingEvents', $showPendingEvents);
		$this->set('showTotalInvites', $showTotalInvites);

		// Event records
		$this->set('activeCategory', $activeCategory);
		$this->set('featuredEvents', $featuredEvents);
		$this->set('events', $events);
		$this->set('pagination', $pagination);

		// Set the date filters on sidebar
		$this->set('dateLinks', $dateLinks);
		$this->set('activeDateFilter', $activeDateFilter);
		$this->set('activeDate', $activeDate);

		// Date navigation
		$this->set('showDateNavigation', $showDateNavigation);
		$this->set('navigation', $navigation);

		// Other visiblity properties
		$this->set('showSorting', $showSorting);
		$this->set('showPastFilter', $showPastFilter);

		// Distance
		$this->set('distance', $distance);
		$this->set('distanceUnit', $this->config->get('general.location.proximity.unit'));
		$this->set('showDistance', $showDistance);
		$this->set('showDistanceSorting', $showDistanceSorting);

		// Sidebar items
		$this->set('counters', $counters);
		$this->set('filter', $filter);

		// Contents
		$this->set('title', $title);

		$this->set('userLocation', $userLocation);
		$this->set('sortingUrls', $sortingUrls);
		$this->set('ordering', $ordering);

		$this->set('hasLocation', $hasLocation);
		$this->set('includePast', $includePast);
		$this->set('delayed', $delayed);
		$this->set('activeUser', $user);

		// Create event URL
		$this->set('createUrl', $createUrl);

		return parent::display('site/events/default/default');
	}

	/**
	 * Displays the category selection for creating an event.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function create()
	{
		// Check if events is enabled.
		$this->checkFeature();

		ES::requireLogin();
		ES::checkCompleteProfile();

		// Ensure that the user's acl is allowed to create events
		if (!$this->my->isSiteAdmin() && !$this->my->getAccess()->get('events.create')) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NOT_ALLOWED_TO_CREATE_EVENT'), SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::dashboard(array(), false));
		}

		// Ensure that the user did not exceed the number of allowed events
		if (!$this->my->isSiteAdmin() && $this->my->getAccess()->intervalExceeded('events.limit', $this->my->id)) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EXCEEDED_CREATE_EVENT_LIMIT'), SOCIAL_MSG_ERROR);
			$this->info->set($this->getMessage());

			return $this->redirect(ESR::events(array(), false));
		}

		$categoryRouteBaseOptions = array('controller' => 'events' , 'task' => 'selectCategory');

		// Support group events
		$groupId = $this->input->getInt('group_id', 0);

		if (!empty($groupId)) {
			$group = ES::group($groupId);

			if (!$group->canCreateEvent()) {
				$this->info->set(false, JText::_('COM_EASYSOCIAL_GROUPS_EVENTS_NO_PERMISSION_TO_CREATE_EVENT'), SOCIAL_MSG_ERROR);

				return $this->redirect($group->getPermalink());
			}

			$categoryRouteBaseOptions['group_id'] = $groupId;

			$this->set('group', $group);
		}

		// Support page events
		$pageId = $this->input->getInt('page_id', 0);

		if (!empty($pageId)) {
			$page = ES::page($pageId);

			if (!$page->canCreateEvent()) {
				$this->info->set(false, JText::_('COM_EASYSOCIAL_PAGES_EVENTS_NO_PERMISSION_TO_CREATE_EVENT'), SOCIAL_MSG_ERROR);

				return $this->redirect($page->getPermalink());
			}

			$categoryRouteBaseOptions['page_id'] = $pageId;

			$this->set('page', $page);
		}

		$this->set('categoryRouteBaseOptions', $categoryRouteBaseOptions);

		// Detect for an existing create event session.
		$session = JFactory::getSession();

		// Load up necessary model and tables.
		$stepSession = ES::table('StepSession');

		// If user doesn't have a record in stepSession yet, we need to create this.
		if (!$stepSession->load($session->getId())) {
			$stepSession->set('session_id', $session->getId());
			$stepSession->set('created', ES::get('Date')->toMySQL());
			$stepSession->set('type', SOCIAL_TYPE_EVENT);

			if (!$stepSession->store()) {
				$this->setError($stepSession->getError());
				return false;
			}
		}

		// Generate the breadcrumb for this page
		$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SELECT_EVENT_CATEGORY'));
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS') , ESR::events());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SELECT_EVENT_CATEGORY'));

		// Get the list of categories
		$model = ES::model('EventCategories');
		$categories = $model->getCreatableCategories($this->my->getProfile()->id, true);

		$allCategories = $model->getCreatableCategories($this->my->getProfile()->id);

		// If there only one category, we want to skip the category selection page
		if (count($allCategories) == 1) {

			$category = $categories[0];

			// need to check if this clsuter category has creation limit based on user points or not.
			if (!$category->hasPointsToCreate($this->my->id)) {
				$requiredPoints = $category->getPointsToCreate($this->my->id);
				$this->setMessage(JText::sprintf('COM_EASYSOCIAL_EVENTS_INSUFFICIENT_POINTS', $requiredPoints), SOCIAL_MSG_ERROR);
				$this->info->set($this->getMessage());

				return $this->redirect(ESR::events(array(), false));
			}

			// Store the category id into the session.
			$session->set('category_id', $category->id, SOCIAL_SESSION_NAMESPACE);

			// Set the current category id.
			$stepSession->uid = $category->id;

			// When user accesses this page, the following will be the first page
			$stepSession->step = 1;

			// Add the first step into the accessible list.
			$stepSession->addStepAccess(1);

			if (!empty($groupId)) {
				$stepSession->setValue('group_id', $groupId);
			}

			if (!empty($pageId)) {
				$stepSession->setValue('page_id', $pageId);
			}

			// Let's save this into a temporary table to avoid missing data.
			$stepSession->store();

			$this->steps();
			return;
		}

		$this->set('categories', $categories);
		$this->set('backId', 0);

		parent::display('site/events/create/default');
	}

	/**
	 * Post action after selecting a category for creation to redirect to steps.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function selectCategory($container = null)
	{
		$this->info->set($this->getMessage());

		if ($this->hasErrors()) {

			// Support for group events
			// If there is a group id, we redirect back to the group instead
			$groupId = $this->input->getInt('group_id');
			if (!empty($groupId)) {
				$group = ES::group($groupId);

				return $this->redirect($group->getPermalink());
			}

			// Support for page event
			$pageId = $this->input->getInt('page_id');
			if (!empty($pageId)) {
				$page = ES::page($pageId);

				return $this->redirect($page->getPermalink());
			}

			if ($container) {
				return $this->redirect(ESR::events(array('layout' => 'create'), false));
			}

			return $this->redirect(ESR::events(array(), false));
		}

		$url = ESR::events(array('layout' => 'steps', 'step' => 1), false);

		return $this->redirect($url);
	}

	/**
	 * Displays the event creation steps.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function steps()
	{
		// Require user to be logged in
		ES::requireLogin();

		// Check for profile completeness
		ES::checkCompleteProfile();

		if (!$this->my->isSiteAdmin() && !$this->my->getAccess()->get('events.create')) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_NOT_ALLOWED_TO_CREATE_EVENT'), SOCIAL_MSG_ERROR);

			$this->info->set($this->getMessage());

			return $this->redirect(ESR::dashboard());
		}

		if (!$this->my->isSiteAdmin() && $this->my->getAccess()->intervalExceeded('events.limit', $this->my->id)) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_EVENTS_EXCEEDED_CREATE_EVENT_LIMIT'), SOCIAL_MSG_ERROR);

			$this->info->set($this->getMessage());

			return $this->redirect(ESR::events());
		}

		$session = JFactory::getSession();

		$stepSession = ES::table('StepSession');
		$stepSession->load(array('session_id' => $session->getId(), 'type' => SOCIAL_TYPE_EVENT));

		if (empty($stepSession->step)) {
			$this->info->set(false, 'COM_EASYSOCIAL_EVENTS_UNABLE_TO_DETECT_CREATION_SESSION', SOCIAL_MSG_ERROR);

			return $this->redirect(ESR::events());
		}

		$categoryId = $stepSession->uid;

		$category = ES::table('EventCategory');
		$category->load($categoryId);

		// Check if there is any workflow.
		if (!$category->getWorkflow()->id) {
			return $this->exception(JText::sprintf('COM_ES_NO_WORKFLOW_DETECTED', SOCIAL_TYPE_EVENT));
		}

		if (!$category->hasAccess('create', $this->my->getProfile()->id) && !$this->my->isSiteAdmin()) {
			$this->info->set(false, JText::_('COM_EASYSOCIAL_EVENTS_NOT_ALLOWED_TO_CREATE_EVENT'), SOCIAL_MSG_ERROR);

			return $this->redirect(ESR::events());
		}

		// Get the step
		$stepIndex = $this->input->get('step', 1, 'int');

		$sequence = $category->getSequenceFromIndex($stepIndex , SOCIAL_EVENT_VIEW_REGISTRATION);

		if (empty($sequence)) {
			$this->info->set(false, JText::_('COM_EASYSOCIAL_EVENTS_NO_VALID_CREATION_STEP'), SOCIAL_MSG_ERROR);

			return $this->redirect(ESR::events(array('layout' => 'create')));
		}

		// We only check if step index is not 1
		if ($stepIndex > 1 && !$stepSession->hasStepAccess($stepIndex)) {
			$this->info->set(false, JText::_('COM_EASYSOCIAL_EVENTS_PLEASE_COMPLETE_PREVIOUS_STEP_FIRST'), SOCIAL_MSG_ERROR);

			return $this->redirect(ESR::events(array('layout' => 'steps', 'step' => 1)));
		}

		if (!$category->isValidStep($sequence, SOCIAL_EVENT_VIEW_REGISTRATION)) {

			$this->info->set(false, JText::_('COM_EASYSOCIAL_EVENTS_INVALID_CREATION_STEP'), SOCIAL_MSG_ERROR);

			return $this->redirect(ESR::events(array('layout' => 'steps', 'step' => 1)));
		}

		$stepSession->set('step', $stepIndex);
		$stepSession->store();

		$reg = ES::registry();
		$reg->load($stepSession->values);

		// Support for group events
		$groupId = $reg->get('group_id');

		if (!empty($groupId)) {
			$group = ES::group($groupId);

			if (!$group->canCreateEvent()) {
				$this->info->set(false, JText::_('COM_EASYSOCIAL_GROUPS_EVENTS_NO_PERMISSION_TO_CREATE_EVENT'), SOCIAL_MSG_ERROR);

				return $this->redirect($group->getPermalink());
			}

			$this->set('group', $group);
		}

		// Support for page events
		$pageId = $reg->get('page_id');

		if (!empty($pageId)) {
			$page = ES::page($pageId);

			if (!$page->canCreateEvent()) {
				$this->info->set(false, JText::_('COM_EASYSOCIAL_PAGES_EVENTS_NO_PERMISSION_TO_CREATE_EVENT'), SOCIAL_MSG_ERROR);

				return $this->redirect($page->getPermalink());
			}

			$this->set('page', $page);
		}

		$step = ES::table('FieldStep');
		$step->loadBySequence($category->getWorkflow()->id, SOCIAL_TYPE_CLUSTERS, $sequence);

		$totalSteps = $category->getTotalSteps();

		$errors = $stepSession->getErrors();

		$data = $stepSession->getValues();

		$args = array(&$data, &$stepSession, &$category);

		$fields = ES::fields();

		// Enforce privacy option to be false for events
		$fields->init(array('privacy' => false));

		$fieldsModel = ES::model('Fields');

		$customFields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'visible' => SOCIAL_EVENT_VIEW_REGISTRATION));

		$callback = array($fields->getHandler(), 'getOutput');

		if (!empty($customFields)) {
			$fields->trigger('onRegister', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args, $callback);
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

		$steps = $category->getSteps(SOCIAL_EVENT_VIEW_REGISTRATION);

		// Pass in the steps for this profile type.
		$steps = $category->getSteps(SOCIAL_GROUPS_VIEW_REGISTRATION);

		// Get the total steps
		$totalSteps = $category->getTotalSteps(SOCIAL_PROFILES_VIEW_REGISTRATION);

		// Set the breadcrumbs and page title
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS'), ESR::events());

		if (!empty($groupId)) {
			$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SELECT_EVENT_CATEGORY'), ESR::events(array('layout' => 'create', 'group_id' => $groupId)));
		} else if (!empty($pageId)) {
			$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SELECT_EVENT_CATEGORY'), ESR::events(array('layout' => 'create', 'page_id' => $pageId)));
		} else {
			$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SELECT_EVENT_CATEGORY'), ESR::events(array('layout' => 'create')));
		}

		$this->page->breadcrumb($step->get('title'));
		$this->page->title($step->get('title'));

		// Format the steps
		if ($steps) {
			$counter = 0;

			foreach ($steps as &$step) {
				$stepClass = $step->sequence == $sequence || $sequence > $step->sequence || $sequence == SOCIAL_REGISTER_COMPLETED_STEP ? ' active' : '';
				$stepClass .= $step->sequence < $sequence || $sequence == SOCIAL_REGISTER_COMPLETED_STEP ? $stepClass . ' past' : '';

				$step->css = $stepClass;
				$step->permalink = 'javascript:void(0);';

				if ($stepSession->hasStepAccess($step->sequence) && $step->sequence != $sequence) {
					$step->permalink = ESR::events(array('layout' => 'steps', 'step' => $counter));
				}
			}

			$counter++;
		}

		$totalSteps = $category->getTotalSteps(SOCIAL_EVENT_VIEW_REGISTRATION);

		$this->set('conditionalFields', $conditionalFields);
		$this->set('stepSession', $stepSession);
		$this->set('steps', $steps);
		$this->set('currentStep', $sequence);
		$this->set('currentIndex', $stepIndex);
		$this->set('totalSteps', $totalSteps);
		$this->set('step', $step);
		$this->set('fields', $customFields);
		$this->set('errors', $errors);
		$this->set('category', $category);

		parent::display('site/events/steps/default');
	}

	/**
	 * Post action for saving a step during event creation to redirect either to the next step or the complete page.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function saveStep($stepSession = null)
	{
		// Set any messages
		$this->info->set($this->getMessage());

		if ($this->hasErrors()) {
			if (!empty($stepSession)) {
				return $this->redirect(ESR::events(array('layout' => 'steps', 'step' => $stepSession->step), false));
			} else {
				return $this->redirect(ESR::events(array('layout' => 'steps', 'step' => 1), false));
			}
		}

		return $this->redirect(ESR::events(array('layout' => 'steps', 'step' => $stepSession->step), false));
	}

	/**
	 * Post action after completing an event creation to redirect either to the event listing for the event item.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function complete($event, $startDatetime)
	{
		// Recurring support
		// If no recurring data, then just redirect accordingly.
		// If event is in pending, then also redirect accordingly.
		if (empty($event->recurringData) || $event->isPending()) {
			$this->info->set($this->getMessage());

			if ($event->isPublished()) {
				return $this->redirect(ESR::events(array('layout' => 'item', 'id' => $event->getAlias()), false));
			}

			$options = array();
			if ($event->isClusterEvent()) {
				$cluster = $event->getCluster();

				$options['uid'] = $cluster->getAlias();
				$options['type'] = $cluster->getType();
			}

			return $this->redirect(ESR::events($options, false));
		}

		// If has recurring data, then we need to show the complete page to create all the necessary recurring events
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS'), ESR::events());
		$this->page->breadcrumb($event->getName(), $event->getPermalink());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_CREATE_RECURRING_EVENT'));

		$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_CREATE_RECURRING_EVENT'));

		// Retrieve event start date time from the custom fields
		// Ensure that these date time should follow what user set from the custom field
		$startDatetime = ES::date($startDatetime, false);

		// Get the recurring schedule
		$schedule = ES::model('Events')->getRecurringSchedule(array(
			// 'eventStart' => $event->getEventStart(),
			'eventStart' => $startDatetime,
			'end' => $event->recurringData->end,
			'type' => $event->recurringData->type,
			'daily' => $event->recurringData->daily
		));

		$this->set('schedule', $schedule);
		$this->set('event', $event);

		echo parent::display('site/events/create/recurring');
	}

	/**
	 * Displays the edit event page.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function edit($errors = null)
	{
		ES::requireLogin();

		ES::checkCompleteProfile();

		$info = $this->info;

		if (!empty($errors)) {
			$info->set($this->getMessage());
		}

		$my = ES::user();

		$eventid = JRequest::getInt('id');

		$event = ES::event($eventid);

		if (empty($event) || empty($event->id)) {
			$info->set(false, JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'), SOCIAL_MSG_ERROR);
			return $this->redirect(ESR::events());
		}

		$guest = $event->getGuest($my->id);

		if (!$guest->isOwner() && !$guest->isAdmin() && !$my->isSiteAdmin()) {
			$info->set(false, JText::_('COM_EASYSOCIAL_EVENTS_NOT_ALLOWED_TO_EDIT_EVENT'), SOCIAL_MSG_ERROR);

			return $this->redirect(ESR::events());
		}

		ES::language()->loadAdmin();

		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS'), ESR::events());
		$this->page->breadcrumb($event->getName(), $event->getPermalink());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_EDIT_EVENT'));

		$this->page->title(JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_EDIT_EVENT_TITLE', $event->getName()));

		$category = ES::table('EventCategory');
		$category->load($event->category_id);

		$stepsModel = ES::model('Steps');
		$steps = $stepsModel->getSteps($category->getWorkflow()->id, SOCIAL_TYPE_CLUSTERS, SOCIAL_EVENT_VIEW_EDIT);

		$fieldsModel = ES::model('Fields');

		$fieldsLib = ES::fields();

		// Enforce privacy to be false for events
		$fieldsLib->init(array('privacy' => false));

		$callback = array($fieldsLib->getHandler(), 'getOutput');

		$conditionalFields = array();

		foreach ($steps as &$step) {
			$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $event->id, 'dataType' => SOCIAL_TYPE_EVENT, 'visible' => SOCIAL_EVENT_VIEW_EDIT));

			if (!empty($step->fields)) {
				$post = JRequest::get('POST');
				$args = array(&$post, &$event, $errors);
				$fieldsLib->trigger('onEdit', SOCIAL_TYPE_EVENT, $step->fields, $args, $callback);

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

		// retrieve events's approval the rejected reason.
		$rejectedReasons = array();
		if ($event->isDraft()) {
			$rejectedReasons = $event->getRejectedReasons();
		}

		$this->set('conditionalFields', $conditionalFields);
		$this->set('event', $event);
		$this->set('steps', $steps);
		$this->set('rejectedReasons', $rejectedReasons);

		echo parent::display('site/events/edit/default');
	}

	/**
	 * Post action after updating an event to redirect to appropriately.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function update($event = null)
	{
		// Recurring support
		// If applies to all, we need to show a "progress update" page to update all childs through ajax.
		$applyAll = !empty($event) && $event->hasRecurringEvents() && $this->input->getInt('applyRecurring');

		// Check if need to create recurring event
		$createRecurring = !empty($event->recurringData);

		// If no apply, and no recurring create, then redirect accordingly.
		if (!$applyAll && !$createRecurring) {
			$this->info->set($this->getMessage());

			if ($this->hasErrors() || empty($event)) {
				return $this->redirect(ESR::events());
			}

			$url = '';
			if ($event->isPending()) {
				$url = ESR::events(array(), false);
			} else {
				$url = $event->getPermalink(false);
			}

			return $this->redirect($url);
		}

		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS'), ESR::events());
		$this->page->breadcrumb($event->getName(), $event->getPermalink());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_EDIT_EVENT'));

		$this->page->title(JText::sprintf('COM_EASYSOCIAL_PAGE_TITLE_EDIT_EVENT_TITLE', $event->getName()));

		$post = JRequest::get('POST', 2);

		$json = ES::json();
		$data = array();

		$disallowed = array(ES::token(), 'option', 'task', 'controller');

		foreach ($post as $key => $value) {
			if (in_array($key, $disallowed)) {
				continue;
			}

			if (is_array($value)) {
				$value = $json->encode($value);
			}

			$data[$key] = $value;
		}

		$string = $json->encode($data);

		$this->set('data', $string);
		$this->set('event', $event);

		$updateids = array();

		if ($applyAll) {
			$children = $event->getRecurringEvents();

			foreach ($children as $child) {
				$updateids[] = $child->id;
			}
		}

		$this->set('updateids', $json->encode($updateids));

		$schedule = array();

		if ($createRecurring) {
			// If there is recurring data, then we back up the post values and the recurring data in the the event params
			$clusterTable = ES::table('Cluster');
			$clusterTable->load($event->id);
			$eventParams = ES::makeObject($clusterTable->params);
			$eventParams->postdata = $data;
			$eventParams->recurringData = $event->recurringData;
			$clusterTable->params = ES::json()->encode($eventParams);
			$clusterTable->store();

			// Get the recurring schedule
			$schedule = ES::model('Events')->getRecurringSchedule(array(
				'eventStart' => $event->getEventStart(),
				'end' => $event->recurringData->end,
				'type' => $event->recurringData->type,
				'daily' => $event->recurringData->daily
			));
		}

		$this->set('schedule', $json->encode($schedule));

		echo parent::display('site/events/update/default');
	}

	/**
	 * Post process after the event avatar is removed
	 *
	 * @since   1.3
	 * @access  public
	 * @param   SocialEvent     The event object
	 */
	public function removeAvatar(SocialEvent $event)
	{
		$this->info->set($this->getMessage());

		$permalink = $event->getPermalink(false);

		$this->redirect($permalink);
	}

	/**
	 * Post processing after removing a guest from an event
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function removeGuest(SocialEvent $event)
	{
		$this->info->set($this->getMessage());

		// Get the members page url
		$redirect = $event->getAppPermalink('guests', false);

		return $this->redirect($redirect);
	}

	/**
	 * Displays the event item page.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function item()
	{
		// Check if events is enabled.
		$this->checkFeature();

		// Check for profile completeness
		ES::checkCompleteProfile();

		// Get the event id
		$id = $this->input->get('id', 0, 'int');
		$event = ES::event($id);

		// Set the default redirect url
		$redirect = ESR::events(array(), false);

		if (!$event || !$event->id || !$event->isPublished() || !$event->canViewEvent()) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_EVENTS_INVALID_EVENT_ID'));
		}

		// Determines if the current user is a guest of this event
		$guest = $event->getGuest($this->my->id);

		// Check if the current logged in user blocked by the event creator or not.
		if ($this->my->id != $event->creator_uid && ES::user()->isBlockedBy($event->creator_uid)) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_EVENTS_EVENT_UNAVAILABLE'));
		}

		// Increment the hit counter
		$event->hit();

		// Append additional meta details
		$event->renderHeaders();

		// Render start_time and end_time opengraph tag. #203
		$event->renderStartTimeHeader();

		// Set the page attributes
		$title = $event->getName();

		// set Page title
		ES::cluster('event', $id)->renderPageTitle(null, 'events');

		if ($event->isClusterEvent()) {
			$cluster = $event->getCluster();

			$this->page->breadcrumb($cluster->getName(), $cluster->getPermalink());
		} else {
			$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_EVENTS', ESR::events());
		}

		$this->page->breadcrumb($title);

		// Check to see if the user could really see the event items
		if (!$event->canViewItem()) {
			$this->set('guest', $guest);
			$this->set('event', $event);

			return parent::display('site/events/item/restricted');
		}

		// Determine if the current request is for "tags"
		$contents = '';

		$hashtag = $this->input->get('tag', '', 'default');
		$hashtagAlias = $this->input->get('tag', '', 'default');

		$layout = $this->input->get('page', '', 'cmd');
		$appId = $this->input->get('appId', 0, 'int');

		// Get the default filter type to display
		if (!$appId && !$layout && !$hashtag) {
			$layout = $this->config->get('events.item.display', 'timeline');
		}

		// Filter by event info
		if ($layout == 'info') {
			return $this->about($event);
		}

		if ($appId) {
			$app = ES::table('App');
			$app->load($appId);

			return $this->app($event, $app);
		}

		// Determines if the current request is to filter specific items
		$filterId = $this->input->get('filterId', 0, 'int');
		$streamFilter = '';

		// Load Stream filter table
		if ($filterId) {
			$streamFilter = ES::table('StreamFilter');
			$streamFilter->load($filterId);
		}

		// Get a list of filters
		$filters = $event->getFilters();

		// Initiate stream api
		$stream = ES::stream();

		$this->set('filterId', $filterId);

		// If the current view is to display filters form
		if ($layout == 'filterForm' && $guest->isGuest()) {
			$contents = $stream->getFilterForm($event->id, SOCIAL_TYPE_EVENT, $filterId);
		}

		// Get custom filter actions
		$customFilterActions = array();

		if ($event->canCreateStreamFilter()) {
			$customFilterActions['link'] = ESR::events(array('layout' => 'item', 'id' => $event->getAlias(), 'type' => 'filterForm'));
			$customFilterActions['attributes'] = 'data-create-filter';
			$customFilterActions['icon'] = 'fa-plus';
		}

		// Add canonical link for event single page
		$this->page->canonical($event->getPermalink(false, true));

		// Determine if we should display news feed filter on the side
		$displayFeedsFilter = false;

		if ($event->isAdmin() || $event->isOwner() || $this->my->isSiteAdmin()) {
			$displayFeedsFilter = true;
		}

		if ($customFilterActions || $hashtag || ($filters && count($filters) > 0)) {
			$displayFeedsFilter = true;
		}

		// Get the timeline link
		$defaultDisplay = $this->config->get('events.item.display', 'timeline');
		$timelinePermalink = $event->getPermalink();
		$aboutPermalink = ESR::events(array('id' => $event->getAlias(), 'page' => 'info', 'layout' => 'item'));

		if ($defaultDisplay == 'info') {
			$aboutPermalink = $timelinePermalink;
			$timelinePermalink = ESR::events(array('id' => $event->getAlias(), 'page' => 'timeline', 'layout' => 'item'));
		}

		$this->set('aboutPermalink', $aboutPermalink);
		$this->set('displayFeedsFilter', $displayFeedsFilter);
		$this->set('customFilterActions', $customFilterActions);
		$this->set('filters', $filters);
		$this->set('guest', $guest);
		$this->set('event', $event);
		$this->set('appId', $appId);
		$this->set('layout', $layout);
		$this->set('title', $title);
		$this->set('streamFilter', $streamFilter);
		$this->set('contents', $contents);
		$this->set('stream', $stream);

		if (!empty($contents)) {
			return parent::display('site/events/item/default');
		}

		//lets get the sticky posts 1st
		$stickies = $stream->getStickies(array('clusterId' => $event->id, 'clusterType' => $event->cluster_type, 'limit' => 0));
		if ($stickies) {
			$stream->stickies = $stickies;
		}

		$streamOptions = array('clusterId' => $event->id, 'clusterType' => $event->cluster_type, 'nosticky' => true);

		// Load the story
		$story = ES::story($event->cluster_type);
		$story->setCluster($event->id, $event->cluster_type);
		$story->showPrivacy(false);

		if (!empty($streamFilter->id)) {
			$tags = $streamFilter->getHashtag();
			$tags = explode(',', $tags);

			if ($tags) {
				$streamOptions['tag'] = $tags;

				$hashtagRule = $this->config->get('stream.filter.hashtag', '');
				if ($hashtagRule == 'and') {
					$streamOptions['matchAllTags'] = true;
				}

				$story->setHashtags($tags);
			}
		}

		if (!empty($hashtag)) {
			$tag = $stream->getHashTag($hashtag);

			if (!empty($tag->id)) {
				$this->set('hashtag', $tag->title);
				$this->set('hashtagAlias', $hashtagAlias);

				$story->setHashtags(array($tag->title));

				$streamOptions['tag'] = array($tag->title);
			}
		}

		// Only allow users with access to post into this event
		if ($this->my->canPostClusterStory(SOCIAL_TYPE_EVENT, $event->id)) {
			$stream->story = $story;
		}

		$stream->get($streamOptions);

		// RSS
		if ($this->config->get('stream.rss.enabled')) {
			$this->addRss($event->getPermalink());
		}

		$this->set('rssLink', $this->rssLink);
		$this->set('stream', $stream);

		parent::display('site/events/item/default');
	}

	/**
	 * Displays the category item page.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function category()
	{
		// Check if events is enabled.
		$this->checkFeature();

		ES::checkCompleteProfile();

		// Get the current category
		$id = $this->input->get('id', 0, 'int');

		// Pagination for the stream
		$startlimit = $this->input->get('limitstart', 0, 'int');

		$category = ES::table('EventCategory');
		$state = $category->load($id);

		if (!$state) {
			$this->info->set(false, JText::_('COM_EASYSOCIAL_EVENTS_INVALID_CATEGORY_ID'), SOCIAL_MSG_ERROR);

			return $this->redirect(ESR::events());
		}

		ES::language()->loadAdmin();

		$this->page->title($category->get('title'));
		$this->page->description($category->getDescription());

		// Add breadcrumbs
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS'), ESR::events());
		$this->page->breadcrumb($category->get('title'));

		$model = ES::model('Events');
		$categoryModel = ES::model('EventCategories');

		$ids = $category->id;

		$clusterModel = ES::model('ClusterCategory');

		// check if this category is a container or not
		if ($category->container) {
			// Get all child ids from this category
			$childs = $clusterModel->getChildCategories($category->id, array(), SOCIAL_TYPE_EVENT, array('state' => SOCIAL_STATE_PUBLISHED));

			$childIds = array();

			foreach ($childs as $child) {
				$childIds[] = $child->id;
			}

			if (!empty($childIds)) {
				$ids = $childIds;
			}
		}

		$events = $model->getEvents(array('state' => SOCIAL_STATE_PUBLISHED, 'sort' => 'random', 'category' => $ids, 'featured' => false, 'limit' => 5, 'limitstart' => 0, 'type' => array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE)));

		$featuredEvents = $model->getEvents(array('state' => SOCIAL_STATE_PUBLISHED, 'sort' => 'random', 'category' => $ids, 'featured' => true, 'limit' => 5, 'limitstart' => 0, 'type' => array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE)));

		$randomGuests = $categoryModel->getRandomCategoryGuests($ids, SOCIAL_CLUSTER_CATEGORY_MEMBERS_LIMIT);

		$randomAlbums = $categoryModel->getRandomCategoryAlbums($ids);

		$totalEvents = $model->getTotalEvents(array('state' => SOCIAL_STATE_PUBLISHED, 'category' => $ids, 'type' => array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE)));

		$totalAlbums = $categoryModel->getTotalAlbums($ids);

		$stream = ES::stream();
		$stream->get(array('clusterCategory' => $ids, 'clusterType' => SOCIAL_TYPE_EVENT, 'startlimit' => $startlimit), array('perspective' => 'dashboard'));


		$childs = $clusterModel->getImmediateChildCategories($category->id, SOCIAL_TYPE_EVENT);

		$this->set('events', $events);
		$this->set('featuredEvents', $featuredEvents);
		$this->set('randomGuests', $randomGuests);
		$this->set('randomAlbums', $randomAlbums);
		$this->set('totalEvents', $totalEvents);
		$this->set('totalAlbums', $totalAlbums);
		$this->set('stream', $stream);
		$this->set('childs', $childs);
		$this->set('category', $category);

		return parent::display('site/events/category/default');
	}

	/**
	 * Post action after saving a filter to redirect back to event item.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function saveFilter()
	{
		$eventId = JRequest::getInt('uid');
		$event = ES::event($eventId);

		if ($this->hasErrors()) {
			$this->info->set($this->getMessage());
		}

		$this->redirect($event->getPermalink());
	}


	/**
	 * Allows viewer to view a file
	 *
	 * @since   1.3
	 * @access  public
	 */
	public function preview()
	{
		// Check if events is enabled.
		$this->checkFeature();

		// Get the file id from the request
		$id = $this->input->get('fileid', 0, 'int');

		$file = ES::table('File');
		$file->load($id);

		if(!$file->id || !$id) {
			// Throw error message here.
			$this->redirect(ESR::dashboard(array(), false));
			$this->close();
		}

		// Load up the event
		$event = ES::event($file->uid);

		// Ensure that the user is really allowed to view this item
		if (!$event->canViewItem()) {
			// Throw error message here.
			$this->redirect(ESR::dashboard(array(), false));
			$this->close();
		}

		$file->preview();
		exit;
	}

	/**
	 * Post action after a guest response from an event to redirect back to the event.
	 *
	 * @since   1.3
	 * @access  public
	 */
	public function guestResponse()
	{
		$this->info->set($this->getMessage());

		$id = $this->input->getInt('id', 0);

		// Load the event
		$event = ES::event($id);

		if (empty($event) || empty($event->id)) {
			return $this->redirect(ESR::events());
		}

		return $this->redirect($event->getPermalink());
	}

	/**
	 * Post process after a user is approved to attend the event
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function approveGuest($event = null)
	{
		$this->info->set($this->getMessage());

		// Default redirect
		$redirect = ESR::events(array('layout' => 'item', 'id' => $event->getAlias()), false);

		$redirect = $this->getReturnUrl($redirect);

		return $this->redirect($redirect);
	}

	/**
	 * Post process after a user is rejected to attend the event
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function rejectGuest($event = null)
	{
		$this->info->set($this->getMessage());

		// Default redirect
		$redirect = ESR::events(array('layout' => 'item', 'id' => $event->getAlias()), false);

		$redirect = $this->getReturnUrl($redirect);

		return $this->redirect($redirect);
	}

	/**
	 * Post action after approving an event to redirect to the event item page.
	 *
	 * @since   1.3
	 * @access  public
	 */
	public function approveEvent($event = null)
	{
		$createRecurring = !empty($event) && $event->getParams()->exists('recurringData');

		if (!$createRecurring) {
			$this->info->set($this->getMessage());

			if ($this->hasErrors()) {
				return $this->redirect(ESR::events());
			}

			return $this->redirect($event->getPermalink());
		}

		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_EVENTS'), ESR::events());
		$this->page->breadcrumb($event->getName(), $event->getPermalink());
		$this->page->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_CREATE_RECURRING_EVENT'));

		$this->page->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_CREATE_RECURRING_EVENT'));

		$params = $event->getParams();

		// Get the recurring schedule
		$schedule = ES::model('Events')->getRecurringSchedule(array(
			'eventStart' => $event->getEventStart(),
			'end' => $params->get('recurringData')->end,
			'type' => $params->get('recurringData')->type,
			'daily' => $params->get('recurringData')->daily
		));

		$this->set('schedule', $schedule);

		$this->set('event', $event);

		echo parent::display('site/events/create/recurring');
	}

	/**
	 * Post action after rejecting an event to redirect to the event listing page.
	 *
	 * @since   1.3
	 * @access  public
	 */
	public function rejectEvent($event = null)
	{
		$this->info->set($this->getMessage());

		return $this->redirect(ESR::events());
	}

	/**
	 * Post processing after featuring an event
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function setFeatured(SocialEvent $event)
	{
		$this->info->set($this->getMessage());

		$permalink = $event->getPermalink(false);

		$returnUrl = $this->getReturnUrl($permalink);

		$this->redirect($returnUrl);
	}

	/**
	 * Post processing after unfeaturing an event
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function removeFeatured(SocialEvent $event)
	{
		$this->info->set($this->getMessage());

		$permalink = $event->getPermalink(false);

		$returnUrl = $this->getReturnUrl($permalink);

		$this->redirect($returnUrl);
	}

	/**
	 * Post processing after unpublishing an event
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function unpublish()
	{
		$this->info->set($this->getMessage());

		// Get the redirection link
		$redirect = ESR::events(array(), false);

		return $this->redirect($redirect);
	}

	/**
	 * Post processing after deleting an event
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function delete($event)
	{
		$this->info->set($this->getMessage());

		// Get the redirection link
		$options = array();

		if ($event->isClusterEvent()) {
			$cluster = $event->getCluster();
			$options['uid'] = $cluster->getAlias();
			$options['type'] = $cluster->getType();
		}

		$redirect = ESR::events($options, false);

		return $this->redirect($redirect);
	}

	public function itemAction($event = null)
	{
		// Check if events is enabled.
		$this->checkFeature();

		$this->info->set($this->getMessage());

		$action = $this->input->getString('action');
		$from = $this->input->getString('from');

		// If action is feature or unfeature, and the action is executed from the item page, then we redirect to the event item page.
		if (in_array($action, array('unfeature', 'feature')) && $from == 'item' && !empty($event)) {
			return $this->redirect($event->getPermalink());
		}

		// Else if the action is delete or unpublish, regardless of where is it executed from, we always go back to the listing page.
		return $this->redirect(ESR::events());
	}

	/**
	 * Allows viewer to download a file from an event.
	 *
	 * @since   1.3
	 * @access  public
	 */
	public function download()
	{
		// Currently only registered users are allowed to view a file.
		ES::requireLogin();

		// Get the file id from the request
		$fileId = JRequest::getInt('fileid', null);

		$file = ES::table('File');
		$file->load($fileId);

		if (!$file->id || !$fileId) {
			// Throw error message here.
			$this->redirect(ESR::dashboard(array(), false));
			$this->close();
		}

		// Load up the event
		$event = ES::event($file->uid);

		// Ensure that the user can really view this event
		if(!$event->canViewItem())
		{
			// Throw error message here.
			$this->redirect(ESR::dashboard(array(), false));
			$this->close();
		}

		$file->download();
		exit;
	}

	public function updateRecurringSuccess()
	{
		ES::requireLogin();

		ES::checkToken();

		$this->info->set(false, JText::_('COM_EASYSOCIAL_EVENTS_UPDATED_RECURRING_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);

		// Delete session data if there is any
		$session = JFactory::getSession();
		$stepSession = ES::table('StepSession');
		$state = $stepSession->load(array('session_id' => $session->getId(), 'type' => SOCIAL_TYPE_EVENT));
		if ($state) {
			$stepSession->delete();
		}

		$id = $this->input->getInt('id');

		$event = ES::event($id);

		// Remove the post data from params
		$clusterTable = ES::table('Cluster');
		$clusterTable->load($event->id);
		$eventParams = ES::makeObject($clusterTable->params);
		unset($eventParams->postdata);
		$clusterTable->params = ES::json()->encode($eventParams);
		$clusterTable->store();

		$this->redirect($event->getPermalink());
	}

	public function createRecurringSuccess()
	{
		ES::requireLogin();

		ES::checkToken();

		$this->info->set(false, JText::_('COM_EASYSOCIAL_EVENTS_CREATED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);

		// Delete session data if there is any
		$session = JFactory::getSession();
		$stepSession = ES::table('StepSession');
		$state = $stepSession->load(array('session_id' => $session->getId(), 'type' => SOCIAL_TYPE_EVENT));

		if ($state) {
			$stepSession->delete();
		}

		$id = $this->input->getInt('id');

		$event = ES::event($id);

		// Remove the post data from params
		$clusterTable = ES::table('Cluster');
		$clusterTable->load($event->id);
		$eventParams = ES::makeObject($clusterTable->params);
		unset($eventParams->postdata);
		$clusterTable->params = ES::json()->encode($eventParams);
		$clusterTable->store();

		$this->redirect($event->getPermalink());
	}

	/**
	 * Post process after a user has invited a friend
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function invite($event)
	{
		// Set the necessary message
		$this->info->set($this->getMessage());

		$permalink = $event->getPermalink(false);

		$returnUrl = $this->getReturnUrl($permalink);

		$this->redirect($returnUrl);
	}

	/**
	 * Post processing after guest is promoted to admin
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function promoteGuest(SocialEvent $event)
	{
		$this->info->set($this->getMessage());

		// Get the members page url
		$redirect = $event->getAppPermalink('guests', false);

		return $this->redirect($redirect);
	}

	/**
	 * Post processing after guest is demoted from admin role
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function demoteGuest(SocialEvent $event)
	{
		$this->info->set($this->getMessage());

		$redirect = $event->getAppPermalink('guests', false);

		return $this->redirect($redirect);
	}
}
