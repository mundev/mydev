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
	 * Post processing when filtering users
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function filter($result)
	{
		// Default sorting
		$sort = $this->config->get('users.listings.sorting');
		$id = $this->input->get('id', 0, 'int');
		$filter = $this->input->get('type', '', 'word');
		$activeProfile = false;

		if (isset($result->profile) && $result->profile) {
			$activeProfile = $result->profile;
		}

		$users = $result->users;
		$pagination = $result->pagination;
		$displayOptions = isset($result->displayOptions) ? $result->displayOptions : false;
		$searchFilter = isset($result->searchFilter) ? $result->searchFilter : false;

		$actualFilter = $this->input->get('id', 'all', 'word');
		$sortItems = new stdClass();
		$sortingTypes = array('latest', 'lastlogin', 'alphabetical');

		if ($filter == 'profiles') {
			$actualFilter = 'profiles';
		}

		// Fixed messed up filter wording. #1680
		$filterRewording = array(
					'profiles' => 'profiletype',
					'all' => 'all',
					'photos' => 'photos',
					'online' => 'online',
					'blocked' => 'blocked'
				);

		if (isset($filterRewording[$actualFilter])) {
			$filter = $filterRewording[$actualFilter];
		}

		foreach ($sortingTypes as $sortingType) {

			$sortItems->{$sortingType} = new stdClass();

			// attributes
			$sortAttributes = array('data-sort', 'data-filter="' . $actualFilter . '"', 'data-type="' . $sortingType . '"');

			//url
			$urlOptions = array();
			$urlOptions['filter'] = $filter;
			$urlOptions['sort'] = $sortingType;

			if (isset($id) && $id) {
				$urlOptions['id'] = $id;
			}

			$sortUrl = ESR::users($urlOptions);

			$sortItems->{$sortingType}->attributes = $sortAttributes;
			$sortItems->{$sortingType}->url = $sortUrl;
		}

		$theme = ES::themes();
		$theme->set('searchFilter', $searchFilter);
		$theme->set('activeProfile', $activeProfile);
		$theme->set('displayOptions', $displayOptions);
		$theme->set('pagination', $pagination);
		$theme->set('users', $users);
		$theme->set('showSort', true);
		$theme->set('filter', $result->filter);
		$theme->set('sort', $sort);
		$theme->set('sortItems', $sortItems);

		$namespace = 'wrapper';

		if ($result->sortRequest) {
			$namespace = 'items';
		}

		$contents = $theme->output('site/users/default/' . $namespace);

		return $this->ajax->resolve($contents, $result->hasSorting);
	}

	/**
	 * Responsible to render a popbox containing a list of users
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function popbox()
	{
		ES::language()->loadSite();

		$ids = JRequest::getVar('ids', '');

		if (!$ids) {
			return $this->ajax->reject();
		}

		$ids = explode('|', $ids);
		$users = ES::user($ids);

		$theme = ES::themes();
		$theme->set('users', $users);
		$output = $theme->output('site/users/popbox.users');

		return $this->ajax->resolve($html);
	}
}
