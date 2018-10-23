<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/model.php');

class EasyBlogModelSearch extends EasyBlogAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total = null;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	public function getTotal()
	{
		// Load total number of rows
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->_buildQuery());
		}

		return $this->_total;
	}

	public function _buildQuery()
	{
		$db = EB::db();

		// used for privacy
		$queryWhere = '';
		$queryExclude = '';
		$queryExcludePending = '';
		$excludeCats = array();
		$isBloggerMode = EBR::isBloggerMode();

		$where = array();
		$where2 = array();
		$text = $this->input->get('query', '', 'default');
		$catId = $this->input->get('category_id', 0, 'int');

		$words = explode(' ', $text);
		$wheres = array();

		foreach ($words as $word) {
			$word = $db->Quote('%' . $db->getEscaped($word, true) . '%', false);

			$where[] = 'a.`title` LIKE ' . $word;
			$where[] = 'a.`content` LIKE ' . $word;
			$where[] = 'a.`intro` LIKE ' . $word;

			$where2[] = 't.title LIKE ' . $word;
			$wheres2[] = implode(' OR ', $where2);

			$wheres[] = implode(' OR ', $where);
		}

		$where = '(' . implode(') OR (', $wheres) . ')';
		$where2 = '(' . implode(') OR (', $wheres2) . ')';

		$isJSGrpPluginInstalled = false;
		$isJSGrpPluginInstalled = JPluginHelper::isEnabled('system', 'groupeasyblog');
		$isEventPluginInstalled = JPluginHelper::isEnabled('system', 'eventeasyblog');
		$isJSInstalled = false; // need to check if the site installed jomsocial.

		if (JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR .'core.php')) {
			$isJSInstalled = true;
		}

		$includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent = ($isEventPluginInstalled && $isJSInstalled) ? true : false;

		$query = '';

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ((a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

		if ($this->config->get('main_includeteamblogpost')) {
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

		// category access here
		$config = EB::config();
		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccessSQL = $catLib->genAccessSQL('a.`id`');
			$queryWhere .= ' AND (' . $catAccessSQL . ')';
		}

		if ($isBloggerMode) {
			$queryWhere .= ' AND a.`created_by`=' . $db->Quote($isBloggerMode);
		}

		if ($catId) {

			// There is a possibility that this is parent category
			$model = EB::model('Categories');
			$categories = $model->getChildCategories($catId);

			$catIds = array($catId);

			foreach ($categories as $category) {
				$catIds[] = (int) $category->id;
			}

			$queryWhere .= ' AND pc.`category_id` IN(' . implode(',', $catIds) . ')';
		}

		$query = 'SELECT * from (';
		$query .= 'SELECT a.*, CONCAT(a.`content`, a.`intro`) AS text, ';

		// %this is text%
		$textquery = $db->Quote('%'.$db->getEscaped($text, true).'%', false);
		$caseQuery = '((CASE WHEN a.`title` = ' . $db->Quote($text) . ' THEN 4 ELSE 0 END) + (CASE WHEN a.`title` LIKE ' . $textquery . ' THEN 3 ELSE 0 END)';
		$caseQuery .= ' + (CASE WHEN a.`content` LIKE ' . $textquery . ' THEN 2 ELSE 0 END) + (CASE WHEN a.`intro` LIKE ' . $textquery . ' THEN 2 ELSE 0 END)) as score';

		$query .= $caseQuery;

		$query .= ' FROM `#__easyblog_post` as a USE INDEX (`easyblog_post_searchnew`)';

		// Always inner join with jos_users and a.created_by so that only valid blogs are loaded
		$query .= ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS c ON a.`created_by`=c.`id`';

		// Always inner join with the category table so that only published are loaded
		$query .= ' INNER JOIN ' . $db->nameQuote('#__easyblog_category') . ' AS cat ON cat.`id` = a.`category_id`';

		if ($catId) {
			$query .= ' INNER JOIN ' . $db->nameQuote('#__easyblog_post_category') . ' AS pc ON a.`id` = pc.`post_id`';
		}

		$query .= ' WHERE (' . $where;

		$query .= ' OR a.`id` IN(';
		$query .= '        SELECT tp.`post_id` FROM `#__easyblog_tag` AS t ';
		$query .= '        INNER JOIN `#__easyblog_post_tag` AS tp ON tp.`tag_id` = t.`id` ';
		$query .= '        WHERE ' . $where2;
		$query .= '))';

		//blog privacy setting
		// @integrations: jomsocial privacy
		$privateBlog = '';

		$easysocial = EB::easysocial();
		$jomsocial = EB::jomsocial();

		if ($this->config->get('integrations_es_privacy') && $easysocial->exists() && !EB::isSiteAdmin()) {
			$esPrivacyQuery = $easysocial->buildPrivacyQuery('a');
			$privateBlog .= $esPrivacyQuery;
		} else if ($this->config->get('main_jomsocial_privacy') && $jomsocial->exists() && !EB::isSiteAdmin()) {
			$jsFriends = CFactory::getModel('Friends');
			$friends = $jsFriends->getFriendIds($this->my->id);

			// Insert query here.
			$privateBlog .= ' AND (';
			$privateBlog .= ' (a.`access`= 0) OR';
			$privateBlog .= ' ((a.`access` = 20) AND (' . $db->Quote($this->my->id) . ' > 0)) OR';

			if (empty($friends)) {
				$privateBlog .= ' ((a.`access` = 30) AND (1 = 2)) OR';
			} else {
				$privateBlog .= ' ((a.`access` = 30) AND (a.' . $db->nameQuote('created_by') . ' IN (' . implode(',', $friends) . '))) OR';
			}

			$privateBlog .= ' ((a.`access` = 40) AND (a.' . $db->nameQuote('created_by') .'=' . $this->my->id . '))';
			$privateBlog .= ')';
		} else {
			if ($this->my->id == 0) {
				$privateBlog .= ' AND a.`access` = ' . $db->Quote(0);
			}
		}

		if ($privateBlog) {
			$query .= $privateBlog;
		}

		// do not show unpublished post
		$query .= ' AND a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' AND a.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		// Ensure that post category is published
		$query .= ' AND cat.`published` = ' . $db->Quote(EASYBLOG_CATEGORY_PUBLISHED);

		// @rule: When language filter is enabled, we need to detect the appropriate contents
		if (!$this->app->isAdmin()) {
			$filterLanguage = $this->app->getLanguageFilter();

			if ($filterLanguage) {
				$query  .= EBR::getLanguageQuery('AND', 'a.language');
			}
		}

		$query .= $queryWhere;
		$query .= ' ) as x';
		$query .= ' ORDER BY x.`score` DESC';

		return $query;
	}

	public function getData()
	{
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	public function searchtext($text)
	{
		if (empty($text)) {
			return false;
		}

		// Set input query
		$this->input->set('query', $text);

		return $this->getData();
	}
}
