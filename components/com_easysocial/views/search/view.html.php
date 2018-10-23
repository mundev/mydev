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

class EasySocialViewSearch extends EasySocialSiteView
{
	/**
	 * Renders the standard search layout
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		ES::checkCompleteProfile();

		// Get the current logged in user.
		$query = $this->input->get('q', '', 'default');

		// Get the selected filters
		$selectedFilters = $this->input->get('filtertypes', array(), 'default');

		// Load up the model
		$indexerModel = ES::model('Indexer');

		// Retrieve a list of supported types
		$allowedTypes = $indexerModel->getSupportedType();

		// Options
		$limit = ES::getLimit('search_limit');

		// Try to search for the result now
		$lib = ES::search();
		$data = $lib->search($query, 0, $limit, $selectedFilters);

		// Get filters
		$filters = $lib->getFilters();

		$length = JString::strlen($query);

		// Set page attributes
		$this->page->title('COM_EASYSOCIAL_PAGE_TITLE_SEARCH');
		$this->page->breadcrumb('COM_EASYSOCIAL_PAGE_TITLE_SEARCH');

		$this->set('length', $length);
		$this->set('selectedFilters', $selectedFilters);
		$this->set('result', $data->result);
		$this->set('query', $query);
		$this->set('total', $data->total);
		$this->set('next_limit', $data->next_limit);
		$this->set('filters', $filters);

		echo parent::display('site/search/default/default');
	}

	/**
	 * Renders the advanced search layout
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function advanced($tpl = null)
	{
		// Check for user profile completeness
		ES::checkCompleteProfile();

		// Advanced search filter id.
		$fid = $this->input->get('fid', 0, 'int');
		$uid = $this->input->get('uid', 0, 'int');

		// Advanced search type
		$type = $this->input->get('type', SOCIAL_FIELDS_GROUP_USER, 'default');

		// setting page title and breadcrumb.
		$pageTitle = 'COM_EASYSOCIAL_PAGE_TITLE_ADVANCED_SEARCH';
		$breadcrumb = 'COM_EASYSOCIAL_PAGE_TITLE_ADVANCED_SEARCH';

		// Set page attributes
		$this->page->title($pageTitle);
		$this->page->breadcrumb($breadcrumb);

		// Get values from posted data
		$match = $this->input->get('matchType', 'all', 'default');
		$avatarOnly = $this->input->get('avatarOnly', 0, 'int');
		$onlineOnly = $this->input->get('onlineOnly', 0, 'int');
		$sort = $this->input->get('sort', $this->config->get('users.advancedsearch.sorting'), 'default');

		// Get values from posted data
		$searchConfig = array();
		$searchConfig['criterias'] = $this->input->get('criterias', '', 'default');
		$searchConfig['datakeys'] = $this->input->get('datakeys', '', 'default');
		$searchConfig['operators'] = $this->input->get('operators', '', 'default');
		$searchConfig['conditions'] = $this->input->get('conditions', '', 'default');
		$searchConfig['match'] = $match;
		$searchConfig['avatarOnly'] = $avatarOnly;
		$searchConfig['onlineOnly'] = $onlineOnly;
		$searchConfig['sort'] = $sort;

		$activeFilter = ES::table('SearchFilter');

		$routerSegment = array();
		$routerSegment['layout'] = 'advanced';

		// we need to load the data from db and do the search based on the saved filter.
		if ($fid && empty($searchConfig['criterias'])) {

			$activeFilter->load($fid);

			$type = $activeFilter->element;

			$routerSegment['fid'] = $activeFilter->getAlias();

			// Get the search configuration
			$searchConfig = $activeFilter->getSearchConfig();

			$match = $searchConfig['match'];
			$avatarOnly = $searchConfig['avatarOnly'];
			$sort = $searchConfig['sort'];
		}

		// Set the type for router
		$routerSegment['type'] = $type;

		$model = ES::model('Search');
		$userFilters = $model->getFilters($type, $this->my->id, false);

		// We also need to get the sitewidefilters
		$siteWideFilters = $model->getSiteWideFilters($type);

		$filters = array_merge($userFilters, $siteWideFilters);

		$lib = ES::advancedsearch($type);

		// Default values
		$results = null;
		$total = 0;
		$nextlimit = null;
		$criteriaHTML = '';

		$displayOptions = array();

		// If there are criterias, we know the user is making a post request to search
		if ($searchConfig['criterias']) {
			$results = $lib->search($searchConfig);

			$displayOptions = $lib->getDisplayOptions();
			$total = $lib->getTotal();
			$nextlimit = $lib->getNextLimit();
		}

		// Get search criteria output
		$criteriaHTML = $lib->getCriteriaHTML(array(), $searchConfig);

		if (!$criteriaHTML) {
			$criteriaHTML = $lib->getCriteriaHTML(array());
		}

		// Get the criteria template
		$criteriaTemplate = $lib->getCriteriaHTML(array('isTemplate' => true));

		$adapters = $lib->getAdapters();

		$this->set('adapters', $adapters);
		$this->set('lib', $lib);
		$this->set('type', $type);
		$this->set('routerSegment', $routerSegment);
		$this->set('criteriaHTML', $criteriaHTML);
		$this->set('criteriaTemplate', $criteriaTemplate);
		$this->set('match', $match);
		$this->set('avatarOnly', $avatarOnly);
		$this->set('onlineOnly', $onlineOnly);
		$this->set('sort', $sort);
		$this->set('results', $results);
		$this->set('total', $total);
		$this->set('nextlimit', $nextlimit);
		$this->set('filters', $filters);
		$this->set('fid', $fid);
		$this->set('activeFilter', $activeFilter);
		$this->set('displayOptions', $displayOptions);

		$namespace = 'default';

		return parent::display('site/search/advanced/' . $namespace);
	}

	/**
	 * Post processing after a filter is deleted
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function deleteFilter(SocialTableSearchFilter $table)
	{
		$this->info->set($this->getMessage());

		$redirect = ESR::search(array('layout' => 'advanced', 'type' => $table->element), false);

		return $this->redirect($redirect);
	}

	/**
	 * Post processing after adding a new filter
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function addFilter($filter)
	{
		$this->info->set($this->getMessage());

		$redirect = $filter->getPermalink(true, false);

		return $this->redirect($redirect);
	}


	/**
	 * Post processing after searching
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function query($redirect)
	{
		return $this->app->redirect($redirect);
	}
}
