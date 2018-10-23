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

require_once(__DIR__ . '/model.php');

class EasyBlogModelArchive extends EasyBlogAdminModel
{
	public $_total = null;
	public $_pagination = null;

	public function __construct()
	{
		parent::__construct();

		$limit = ($this->app->getCfg('list_limit') == 0) ? 5 : $this->app->getCfg('list_limit');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		// $limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieve a list of blog posts from a specific list of categories
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPosts($categories = array(), $limit = null)
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config	= EB::config();

		$catAccess = array();
		$query = array();


		if ($categories) {
			$catAccess['include'] = $categories;
		}

		$query[] = 'SELECT SQL_CALC_FOUND_ROWS a.* FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';

		// Build the WHERE clauses
		$query[] = 'WHERE a.' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_ARCHIVED);

		// If user is a guest, ensure that they can really view the blog post
		if ($this->my->guest) {
			$query[] = 'AND a.' . $db->quoteName('access') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		// Ensure that blogger mode is respected
		// Determines if this current request is standalone mode
		$blogger = EB::isBloggerMode();

		if ($blogger !== false) {
			$query[] = 'AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($blogger);
		}

		// Ensure that the blog posts is available site wide
		$query[] = 'AND a.' . $db->quoteName('source_id') . '=' . $db->Quote(0);

		// Filter by language
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query[] = 'AND (a.' . $db->quoteName('language') . '=' . $db->Quote($language) . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('*') . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('') . ')';
		}

		$catLib = EB::category();

		// sql for category access
		if ($config->get('main_category_privacy')) {
			$catAccessSQL = $catLib->genAccessSQL( 'a.`id`', $catAccess);
			$query[] = 'AND (' . $catAccessSQL . ')';

		} else {
			$catAccessSQL = $catLib->genBasicSQL('a.`id`', $catAccess);
			
			if ($catAccessSQL) {
				$query[] = ' AND ' . $catAccessSQL;
			}
		}

		// Ordering options
		$ordering = $config->get('layout_postsort', 'DESC');

		// Order the posts
		$query[] = 'ORDER BY a.' . $db->quoteName('created') . ' ' . $ordering;


		// Set the pagination
		$limit = ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');

		$query[] = 'LIMIT ' . $limitstart . ',' . $limit;

		// Glue back the sql queries into a single string.
		$query = implode(' ', $query);

		// Debug
		// echo str_ireplace('#__', 'jos_', $query);exit;

		$db->setQuery($query);

		if ($db->getErrorNum() > 0) {
			JError::raiseError($db->getErrorNum(), $db->getErrorMsg() . $db->stderr());
		}

		$result = $db->loadObjectList();

		$totalQuery = 'select FOUND_ROWS()';
		$db->setQuery($totalQuery);

		$this->_total = $db->loadResult();

		$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);

		return $result;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		return $this->_total;
	}

	public function getFilterType($filterType = 'normal')
	{
		return $filterType == 'normal' ? EASYBLOG_POST_NORMAL : EASYBLOG_POST_ARCHIVED;
	}

	public function getArchiveMinMaxYear($filterType = 'normal')
	{
		$db = EB::db();
		$user = JFactory::getUser();

		$filterType = $this->getFilterType($filterType);

		$query	= 'SELECT YEAR(MIN( '.$db->nameQuote('created').' )) AS minyear, '
				. 'YEAR(MAX( '.$db->nameQuote('created').' )) AS maxyear '
				. 'FROM '.$db->nameQuote('#__easyblog_post').' '
				. 'WHERE '.$db->nameQuote('published').' = '.$db->Quote(EASYBLOG_POST_PUBLISHED) .' '
				. 'AND '.$db->nameQuote('state').' = '.$db->Quote($filterType) .' ';

		if (empty($user->id)) {
			$query .= 'AND ' . $db->nameQuote('access') . ' = ' . $db->Quote('0') . ' ';
		}

		$db->setQuery($query);
		$row = $db->loadAssoc();

		$year = $row;

		if (empty($row['minyear']) || empty($row['maxyear'])) {
			$year = array();
		}

		return $year;
	}

	public function getArchivePostCount($yearStart='', $yearStop='0', $excludeCats = '')
	{
		$result = self::getArchivePostCounts($yearStart, $yearStop, $excludeCats, '');
		return $result;
	}

	public function getArchivePostCounts($yearStart='', $yearStop='0', $excludeCats = '', $includeCats = '', $filter = '', $filterId = '', $filterType = 'normal')
	{
		$db = EB::db();
		$user = JFactory::getUser();
		$config = EB::config();

		$catAccess = array();

		$filterType = $this->getFilterType($filterType);

		if (empty($yearStart)) {
			$year = $this->getArchiveMinMaxYear($filterType);
			$yearStart = $year['maxyear'];
		}

		$fr = $yearStart - 1;
		$to	= $yearStart + 1;

		if (!empty($yearStop)) {
			$fr = $yearStart - 1;
			$to	= $yearStop + 1;
		}

		if (!is_array($excludeCats) && !empty($excludeCats)) {
			$excludeCats = explode(',', $excludeCats);
		
		} else if (!is_array($excludeCats) && empty($excludeCats)) {
			$excludeCats = array();
		}

		if (!is_array($includeCats) && !empty($includeCats)) {
			$includeCats = explode(',', $includeCats);
		
		} else if (!is_array($includeCats) && empty($includeCats)) {
			$includeCats = array();
		}

		$includeCats = array_diff($includeCats, $excludeCats);

		if (!empty($excludeCats) && count($excludeCats) >= 1) {
			$catAccess['exclude'] = $excludeCats;
		}

		if (!empty($includeCats) && count($includeCats) >= 1) {
			$catAccess['include'] = $includeCats;
		}

		//blog privacy setting
		// @integrations: jomsocial privacy
		$privateBlog = '';

		$easysocial = EB::easysocial();
		$jomsocial = EB::jomsocial();

		if ($easysocial->exists() && !EB::isSiteAdmin() && $config->get('integrations_es_privacy')) {
			$esPrivacyQuery = $easysocial->buildPrivacyQuery( 'a' );
			$privateBlog .= $esPrivacyQuery;
		} else if ($config->get('main_jomsocial_privacy') && $jomsocial->exists() && !EB::isSiteAdmin()) {
			$jsFriends = CFactory::getModel('Friends');
			$friends = $jsFriends->getFriendIds($user->id);

			// Insert query here.
			$privateBlog .= ' AND (';
			$privateBlog .= ' (a.`access`= 0 ) OR';
			$privateBlog .= ' ( (a.`access` = 20) AND (' . $db->Quote($user->id) . ' > 0 ) ) OR';

			if (empty($friends)) {
				$privateBlog .= ' ( (a.`access` = 30) AND ( 1 = 2 ) ) OR';
			} else {
				$privateBlog .= ' ( (a.`access` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$privateBlog .= ' ( (a.`access` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $user->id . ') )';
			$privateBlog .= ' )';
		} else {

			if ($user->id == 0) {
				$privateBlog .= ' AND a.`access` = ' . $db->Quote(0);
			}
		}

		$joinTeam = '';

		$FilterSQL = '';
		if ($filter != ''){

			$FilterSQL = '';
			switch( $filter )
			  {
			   case 'blogger':
				$FilterSQL = 'AND a.'.$db->nameQuote('created_by').' = '.$db->Quote($filterId);
				break;
			   case 'team':
				$FilterSQL = 'AND (a.' . $db->quoteName('source_type') . ' = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM) . ' and a.'.$db->quoteName('source_id').' = '.$db->Quote($filterId) . ')';
				break;
			   default :
				break;
			  }
		}
		$languageFilterSQL = '';

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();
		if ($filterLanguage) {
			$languageFilterSQL	.= EBR::getLanguageQuery('AND', 'a.language');
		}

		$queryCategory = '';
		$catLib = EB::category();

		if ($config->get('main_category_privacy')) {

			$catAccessSQL = $catLib->genAccessSQL( 'a.`id`', $catAccess);
			$queryCategory = ' AND (' . $catAccessSQL . ') ';
		
		} else {
			
			$catAccessSQL = $catLib->genBasicSQL('a.`id`', $catAccess);

			if ($catAccessSQL) {
				$queryCategory = ' AND ' . $catAccessSQL;
			}
		}

		$query	= 'SELECT COUNT(1) as count, MONTH( a.'.$db->nameQuote('created').' ) AS month, YEAR( a.'.$db->nameQuote('created').' ) AS year '
				. 'FROM '.$db->nameQuote('#__easyblog_post').' AS a '
				. $joinTeam.' '
				. 'WHERE a.'.$db->nameQuote('published') .'=' . $db->Quote(EASYBLOG_POST_PUBLISHED) . ' '
				. 'AND a.'.$db->nameQuote('state') .'=' . $db->Quote($filterType) . ' '
				. $privateBlog.' '
				. $languageFilterSQL. ' '
				. $FilterSQL. ' '
				. 'AND ( a.'.$db->nameQuote('created').' > '.$db->Quote($fr.'-12-31 23:59:59').' AND a.'.$db->nameQuote('created').' < '.$db->Quote($to.'-01-01 00:00:00').')';

		if ($queryCategory) {
			$query .= $queryCategory;
		}

		$query .= ' GROUP BY year, month DESC'
				. ' ORDER BY a.'.$db->nameQuote('created') . ' DESC';

		$db->setQuery($query);
		$row = $db->loadAssocList();

		if (empty($row)) {
			return false;
		}

		$postCount = new stdClass();

		foreach($row as $data) {

			if (!isset($postCount->{$data['year']})) {
				$postCount->{$data['year']} = new stdClass();
			}

			$postCount->{$data['year']}->{$data['month']} = $data['count'];
		}

		return $postCount;
	}


	public function getArchivePostCountByMonth($month='', $year='', $showPrivate=true)
	{
		$db = EB::db();
		$user = JFactory::getUser();

		$privateBlog = $showPrivate? '' : 'AND '.$db->nameQuote('access').' = '. $db->Quote('0');

		$tzoffset   = EasyBlogDateHelper::getOffSet( true );
		$query	= 'SELECT COUNT(1) as count, DAY( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS day,';
		$query	.= ' MONTH( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS month,';
		$query	.= ' YEAR( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS year ';
		$query	.= ' FROM '.$db->nameQuote('#__easyblog_post');
		$query	.= ' WHERE '.$db->nameQuote('published').' = '.$db->Quote(EASYBLOG_POST_PUBLISHED);
		$query	.= ' AND '.$db->nameQuote('state').' = '.$db->Quote(EASYBLOG_POST_NORMAL);
		$query	.= ' ' . $privateBlog;
		$query	.= ' AND ('.$db->nameQuote('created').' > '.$db->Quote($year.'-'.$month.'-01 00:00:00').' AND '.$db->nameQuote('created').' < '.$db->Quote($year.'-'.$month.'-31 23:59:59').')';
		$query	.= ' GROUP BY day, year, month ';
		$query	.= ' ORDER BY '.$db->nameQuote('created').' ASC ';

		$db->setQuery($query);
		$row = $db->loadAssocList();

		$postCount = new stdClass();

		for ($i=1; $i<=31; $i++) {
			$postCount->{$year}->{$month}->{$i} = 0;
		}

		if (!empty($row)) {

			foreach($row as $data) {
				$postCount->{$year}->{$month}->{$data['day']} = $data['count'];
			}
		}

		return $postCount;
	}

	/**
	 * Retrieves a simple list of blog posts by specific month / year
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getArchivePostListByMonth($month='', $year='', $showPrivate = false, $category = '', $filterType = 'normal', $options = array())
	{
		$db = EB::db();
		$user = JFactory::getUser();
		$config = EB::config();

		// used for privacy
		$queryWhere = '';
		$queryExclude = '';
		$queryExcludePending = '';
		$excludeCats = array();

		if ($user->id == 0) {
			$showPrivate = false;
		}

		// flag to determine if query should use select count on paginatin or not.
		// #1557
		$useSelectCount = true;

		$usePagination = isset($options['usePagination']) ? $options['usePagination'] : false;
		$limit = isset($options['limit']) ? $options['limit'] : 0;

		$filterType = $this->getFilterType($filterType);

		// Blog privacy setting
		// @integrations: jomsocial privacy
		$privateBlog = '';

		if ($config->get('integrations_es_privacy') && EB::easysocial()->exists() && !EB::isSiteAdmin()) {

			$useSelectCount = false;

			$esPrivacyQuery = EB::easysocial()->buildPrivacyQuery('a');
			$privateBlog .= $esPrivacyQuery;
		} else if ($config->get('main_jomsocial_privacy') && EB::jomsocial()->exists() && !EB::isSiteAdmin()) {

			$useSelectCount = false;

			$friendsModel = CFactory::getModel('Friends');
			$friends = $friendsModel->getFriendIds( $user->id );

			// Insert query here.
			$privateBlog .= ' AND (';
			$privateBlog .= ' (a.`access`= 0 ) OR';
			$privateBlog .= ' ( (a.`access` = 20) AND (' . $db->Quote( $user->id ) . ' > 0 ) ) OR';

			if (!$friends) {
				$privateBlog .= ' ( (a.`access` = 30) AND ( 1 = 2 ) ) OR';
			} else {
				$privateBlog .= ' ( (a.`access` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$privateBlog .= ' ( (a.`access` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $user->id . ') )';
			$privateBlog .= ' )';
		} else {

			if ($user->id == 0) {
				$privateBlog .= ' AND a.`access` = ' . $db->Quote(0);
			}
		}

		// Join the query ?
		$privateBlog = $showPrivate? '' : $privateBlog;


		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		if (EB::jomsocial()->exists()) {
			$isJSInstalled = true;
		}

		$includeJSGrp	= ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ( (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

		if ($config->get('main_includeteamblogpost')) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
		}

		if ($includeJSEvent) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, 'a');
		}

		if ($includeJSGrp) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, 'a');
		}

		if ($includeJSGrp || $includeJSGrp) {
			$useSelectCount = false;
		}

		if (EB::easysocial()->exists()) {

			if (EB::easysocial()->isBlogAppInstalled('group')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'a');
			}

			if (EB::easysocial()->isBlogAppInstalled('page')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE, 'a');
			}

			if (EB::easysocial()->isBlogAppInstalled('event')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'a');
			}

			if (EB::easysocial()->isBlogAppInstalled('group') || EB::easysocial()->isBlogAppInstalled('page') || EB::easysocial()->isBlogAppInstalled('event')) {
				$useSelectCount = false;
			}
		}

		$contributeSQL .= ')';

		$queryWhere .= $contributeSQL;

		//get teamblogs id.
		$query = '';

		$extraSQL = '';

		// If this is on blogger mode, we need to only pick items from the blogger.
		$blogger = EBR::isBloggerMode();
		
		if ($blogger !== false) {
			$extraSQL = ' AND a.`created_by` = ' . $db->Quote($blogger);
		}

		$tzoffset = EB::date()->getOffSet(true);

		$header = 'SELECT SQL_CALC_FOUND_ROWS';
		
		if ($useSelectCount) {
			$header = 'SELECT ';
		}

		// header
		$header .= ' a.*, DAY( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS day,';
		$header .= ' MONTH( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS month,';
		$header .= ' YEAR( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS year ';
		$header .= ', 0 as `featured`';

		// conditions
		$query = ' FROM '.$db->nameQuote('#__easyblog_post') . ' as a';
		$query .= ' WHERE a.`published` = '.$db->Quote(EASYBLOG_POST_PUBLISHED).' ';
		$query .= ' AND a.' . $db->quoteName('state') . ' = '.$db->Quote($filterType).' ';
		$query .= $privateBlog.' ';
		$query .= ' AND (a.`created` > ' . $db->Quote($year.'-'.$month.'-01 00:00:00') . ' AND a.`created` < ' . $db->Quote($year.'-'.$month.'-31 23:59:59').') ';

		// Filter by language
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query .= 'AND (a.' . $db->quoteName('language') . '=' . $db->Quote($language) . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('*') . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('') . ')';
		}

		$catLib = EB::category();
		$options = array();

		if ($category) {
			$categories	= explode(',', $category);
			$options['include'] = $categories;
		}

		// If do not display private posts, we need to append additional queries here.
		if (!$showPrivate && $config->get('main_category_privacy')) {

			$useSelectCount = false;

			$catAccessSQL = $catLib->genAccessSQL('a.`id`', $options);
			$query .= ' AND (' . $catAccessSQL . ')';
		
		} else {
			$catAccessSQL = $catLib->genBasicSQL('a.`id`', $options);
			
			if ($catAccessSQL) {
				$query .= ' AND ' . $catAccessSQL;
			}
		}

		$query .= $extraSQL . ' ';
		$query .= $queryWhere;

		// prepare for count query
		$cntQuery = $query;

		// append order by and limit into main query
		$query .= ' ORDER BY a.`created` ASC ';

		$limitstart = $this->getState('limitstart', 0);

		//limit
		if (!empty($limit)) {
			if ($usePagination) {
				$query .= " LIMIT $limitstart, $limit";

			} else {
				$query	.= ' LIMIT ' . (INT) $limit;
			}
		}

		$mainQuery = $header . $query;

		// Debugging
		// echo str_ireplace('#__', 'jos_', $mainQuery);
		// echo '<br><br>';

		$db->setQuery($mainQuery);
		$posts = $db->loadObjectList();

		if ($limit && $usePagination) {

			$countSQL = 'select count(1) ' . $cntQuery;

			if (! $useSelectCount) {
				$countSQL = "select FOUND_ROWS()";
			}

			$db->setQuery($countSQL);

			$this->_total = $db->loadResult();
			$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);
		}

		return $posts;
	}

	/**
	 * Retrieves a list of blog posts by specific month
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getArchivePostByMonth($month='', $year='', $showPrivate = false, $category = '')
	{
		$db = EB::db();
		$user = JFactory::getUser();
		$config = EB::config();

		// used for privacy
		$queryWhere = '';
		$queryExclude = '';
		$queryExcludePending = '';
		$excludeCats = array();

		if( $user->id == 0) {
			$showPrivate = false;
		}

		// Blog privacy setting
		// @integrations: jomsocial privacy
		$privateBlog = '';

		if ($config->get('integrations_es_privacy') && EB::easysocial()->exists() && !EB::isSiteAdmin()) {
			$esPrivacyQuery = EB::easysocial()->buildPrivacyQuery('a');
			$privateBlog .= $esPrivacyQuery;

		} else if ($config->get('main_jomsocial_privacy') && EB::jomsocial()->exists() && !EB::isSiteAdmin()) {

			$friendsModel = CFactory::getModel('Friends');
			$friends = $friendsModel->getFriendIds( $user->id );

			// Insert query here.
			$privateBlog .= ' AND (';
			$privateBlog .= ' (a.`access`= 0 ) OR';
			$privateBlog .= ' ( (a.`access` = 20) AND (' . $db->Quote( $user->id ) . ' > 0 ) ) OR';

			if (!$friends) {
				$privateBlog .= ' ( (a.`access` = 30) AND ( 1 = 2 ) ) OR';
			} else {
				$privateBlog .= ' ( (a.`access` = 30) AND ( a.' . $db->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$privateBlog .= ' ( (a.`access` = 40) AND ( a.' . $db->nameQuote( 'created_by' ) .'=' . $user->id . ') )';
			$privateBlog .= ' )';

		} else {

			if ($user->id == 0) {
				$privateBlog .= ' AND a.`access` = ' . $db->Quote(0);
			}
		}

		// Join the query ?
		$privateBlog = $showPrivate? '' : $privateBlog;


		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		if (EB::jomsocial()->exists()) {
			$isJSInstalled = true;
		}

		$includeJSGrp	= ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ( (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

		if ($config->get('main_includeteamblogpost')) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
		}

		if ($includeJSEvent) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, 'a');
		}

		if ($includeJSGrp) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, 'a');
		}

		if (EB::easysocial()->exists()) {
			if (EB::easysocial()->isBlogAppInstalled('group')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, 'a');
			}

			if (EB::easysocial()->isBlogAppInstalled('page')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE, 'a');
			}

			if (EB::easysocial()->isBlogAppInstalled('event')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, 'a');
			}
		}

		$contributeSQL .= ')';

		$queryWhere .= $contributeSQL;

		//get teamblogs id.
		$query = '';

		$extraSQL = '';

		// If this is on blogger mode, we need to only pick items from the blogger.
		$blogger = EBR::isBloggerMode();

		if ($blogger !== false) {
			$extraSQL = ' AND a.`created_by` = ' . $db->Quote($blogger);
		}

		$tzoffset = EB::date()->getOffSet(true);

		$query = 'SELECT a.*, DAY( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS day,';
		$query .= ' MONTH( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS month,';
		$query .= ' YEAR( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS year ';
		$query .= ', 0 as `featured`';
		$query .= ' FROM '.$db->nameQuote('#__easyblog_post') . ' as a';
		$query .= ' WHERE a.`published` = '.$db->Quote(EASYBLOG_POST_PUBLISHED).' ';
		$query .= ' AND a.' . $db->quoteName('state') . ' = '.$db->Quote(EASYBLOG_POST_NORMAL).' ';
		$query .= $privateBlog.' ';
		$query .= ' AND (a.`created` > ' . $db->Quote($year.'-'.$month.'-01 00:00:00') . ' AND a.`created` < ' . $db->Quote($year.'-'.$month.'-31 23:59:59').') ';

		// Filter by language
		$language = EB::getCurrentLanguage();

		if ($language) {
			$query .= 'AND (a.' . $db->quoteName('language') . '=' . $db->Quote($language) . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('*') . ' OR a.' . $db->quoteName('language') . '=' . $db->Quote('') . ')';
		}

		$catLib = EB::category();
		$options = array();

		if ($category) {
			$categories	= explode(',', $category);
			$options['include'] = $categories;
		}		

		// If do not display private posts, we need to append additional queries here.
		if (!$showPrivate && $config->get('main_category_privacy')) {

			$catAccessSQL = $catLib->genAccessSQL('a.`id`', $options);
			$query .= ' AND (' . $catAccessSQL . ')';

		} else {
			$catAccessSQL = $catLib->genBasicSQL('a.`id`', $options);

			if ($catAccessSQL) {
				$query .= ' AND ' . $catAccessSQL;
			}
		}

		$query  .= $extraSQL . ' ';
		$query	.= $queryWhere;
		$query  .= ' ORDER BY a.`created` ASC ';

		// echo $query;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Format the blog posts
		$options = array(
					'cacheComment' => false,
					'cacheCommentCount' => false,
					'cacheRatings' => false,
					'cacheTags' => false,
					'cacheAuthors' => false,
					'loadAuthor' => false
				);

		$posts = EB::formatter('list', $result, true, $options);

		$postCount = new EasyblogCalendarObject($month, $year);

		if (!empty($result)) {
			foreach ($result as $row) {

				$post = EB::post($row->id);

				if ($postCount->{$year}->{$month}->{$row->day} == 0) {
					$postCount->{$year}->{$month}->{$row->day} = array($post);
				} else {
					array_push($postCount->{$year}->{$month}->{$row->day}, $post);
				}
			}
		}

		return $postCount;
	}

}

class EasyblogCalendarObject
{
	public function __construct($month, $year)
	{
		$this->{$year} = new stdClass();
		$this->{$year}->{$month} = new stdClass();

		for ($i=1; $i<=31; $i++) {
			$this->{$year}->{$month}->{$i} = 0;
		}
	}
}
