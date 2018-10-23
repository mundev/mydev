<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/model.php');

class EasyBlogModelCategories extends EasyBlogAdminModel
{
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();

		$mainframe  = JFactory::getApplication();
		$limit = $mainframe->getUserStateFromRequest('com_easyblog.categories.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = (int) $this->input->get('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves the custom field group properties
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getCustomFieldGroup($id, $type = 'write')
	{
		$db = EB::db();

		$query = 'SELECT a.' . $db->quoteName('group_id') . ' FROM ' . $db->quoteName('#__easyblog_category_fields_groups') . ' AS a';
		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_fields_groups') . ' AS c';
		$query .= ' ON a.' . $db->quoteName('group_id') . ' = c.' . $db->quoteName('id');
		$query .= ' LEFT JOIN ' . $db->quoteName('#__easyblog_fields_groups_acl') . ' AS acl';
		$query .= ' ON c.' . $db->quoteName('id') . ' = acl.' . $db->quoteName('group_id');

		$query .= ' WHERE ' . $db->quoteName('category_id') . '=' . $db->Quote($id);

		$gid = EB::getUserGids();
		$gids = '';

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= (empty($gids)) ? $id : ',' . $id;
			}
		}

		// We need to check whether the user is belong to one of the group
		$query .= ' AND (';
		$query .= ' acl.' . $db->quoteName('acl_id') . ' IN(' . $gids . ')';
		$query .= ' AND acl.' . $db->quoteName('acl_type') . ' = ' . $db->Quote($type);
		$query .= ' OR acl.' . $db->quotename('id') . ' IS NULL';
		$query .= ' )';

		$db->setQuery($query);

		$id = $db->loadResult();

		$group = false;

		if ($id) {
			$group = EB::table('FieldGroup');
			$group->load($id);
		}

		return $group;
	}

	/**
	 * Retrieves the custom fields
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getCustomFields($id, $type = 'write')
	{
		$db = EB::db();

		$query = 'SELECT a.* FROM ' . $db->quoteName('#__easyblog_fields') . ' AS a';
		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_category_fields_groups') . ' AS b';
		$query .= ' ON a.' . $db->quoteName('group_id') . '= b.' . $db->quoteName('group_id');
		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_fields_groups') . ' AS c';
		$query .= ' ON b.' . $db->quoteName('group_id') . ' = c.' . $db->quoteName('id');
		$query .= ' LEFT JOIN ' . $db->quoteName('#__easyblog_fields_groups_acl') . ' AS acl';
		$query .= ' ON c.' . $db->quoteName('id') . ' = acl.' . $db->quoteName('group_id');

		$query .= ' WHERE b.' . $db->quoteName('category_id') . '=' . $db->Quote($id);

		$gid = EB::getUserGids();
		$gids = '';

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= (empty($gids)) ? $id : ',' . $id;
			}
		}

		// We need to check whether the user is belong to one of the group
		$query .= ' AND (';
		$query .= ' acl.' . $db->quoteName('acl_id') . ' IN(' . $gids . ')';
		$query .= ' AND acl.' . $db->quoteName('acl_type') . ' = ' . $db->Quote($type);
		$query .= ' OR acl.' . $db->quotename('id') . ' IS NULL';
		$query .= ' )';

		$query .= ' AND a.' . $db->quoteName('state') . '=' . $db->Quote(1);
		$query .= ' GROUP BY a.' . $db->quoteName('id');
		$query .= ' ORDER BY a.' . $db->quoteName('id') . ' ASC';

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		// Get the fields library
		$lib = EB::fields();

		// Initialize the default value
		$fields = array();

		foreach ($result as $row) {
			$field = EB::table('Field');
			$field->bind($row);

			$fields[] = $field;
		}

		return $fields;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @since   4.0
	 * @access public
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @since   4.0
	 * @access public
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination  = EB::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @since   4.0
	 * @access public
	 */
	public function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where      = $this->_buildQueryWhere();
		$orderby    = $this->_buildQueryOrderBy();
		$db         = EB::db();

		$query  = 'SELECT * FROM ' . $db->nameQuote('#__easyblog_category')
				. $where . ' '
				. $orderby;

		return $query;
	}

	/**
	 * Internal function to build query's where condition
	 *
	 * @since   4.0
	 * @access public
	 */
	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db = EB::db();

		$filter_state = $mainframe->getUserStateFromRequest('com_easyblog.categories.filter_state', 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest('com_easyblog.categories.search', 'search', '', 'string');
		$search = $db->getEscaped( trim(EBString::strtolower( $search ) ) );

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote('published') . '=' . $db->Quote('1');
			} else if ($filter_state == 'U')
			{
				$where[] = $db->nameQuote('published') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER(title) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	/**
	 * Internal function to build query
	 *
	 * @since   4.0
	 * @access public
	 */
	public function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easyblog.categories.filter_order',      'filter_order',     'lft', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easyblog.categories.filter_order_Dir',  'filter_order_Dir', '', 'word');

		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @since   4.0
	 * @access public
	 */
	public function getData($usePagination = true)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			if($usePagination)
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			else
				$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}


	/**
	 * Method to publish or unpublish categories
	 *
	 * @since   4.0
	 * @access public
	 */
	public function publish(&$categories, $publish = 1)
	{
		if (!$categories) {
			return false;
		}

		$db = EB::db();

		$categories = implode(',', $categories);

		$query  = 'UPDATE ' . $db->nameQuote('#__easyblog_category') . ' '
				. 'SET ' . $db->nameQuote('published') . '=' . $db->Quote($publish) . ' '
				. 'WHERE ' . $db->nameQuote('id') . ' IN (' . $categories . ')';


		if (!$publish) {
			$query .= 'AND ' . $db->nameQuote('default') . '=' . $db->Quote(0);
		}

		$db->setQuery($query);

		if (!$db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the list of categories associated with a particular blog post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getBlogCategories($id)
	{
		$db = EB::db();

		$query = 'SELECT a.* FROM ' . $db->nameQuote('#__easyblog_post_category') . 'as a';
		$query .= ' INNER JOIN '. $db->nameQuote('#__easyblog_category') . 'as b';
		$query .= ' on a.' . $db->nameQuote('category_id') . ' = b.' . $db->nameQuote('id') ;
		$query .= ' WHERE a.' . $db->nameQuote('post_id') . '=' . $db->Quote($id);
		$query .= ' ORDER BY b.' . $db->nameQuote('lft') . ' asc';

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return array();
		}


		$categories = array();

		foreach ($result as $row) {

			$category = EB::table('Category');
			$category->load($row->category_id);
			$category->primary = $row->primary;

			$categories[] = $category;
		}

		return $categories;
	}

	/**
	 * Returns the number of blog entries created within this category.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getUsedCount($categoryId , $published = false)
	{
		$dd = EB::db();

		$query  = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_post') . ' as a '
				. ' inner join ' . $db->nameQuote('#__easyblog_post_category') . ' as b '
				. ' on a.id = b.post_id'
				. ' WHERE b.' . $db->nameQuote('category_id') . '=' . $db->Quote($categoryId);

		if ($published) {
			$query  .= ' AND a.' . $db->nameQuote('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		}

		$query  .= ' AND a.' . $db->nameQuote('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0) {
			$query .= ' AND a.`access` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Get categories for the dashboard
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function _buildQueryByBlogger($bloggerId = 0 , $ordering = '', $search = '', $limit = null)
	{
		$db = EB::db();
		$config = EB::config();
		$sortConfig = $config->get('layout_sorting_category','latest');

		$limit  = ($limit == 0) ? $this->getState('limit') : $limit;

		// Reset the limit
		if ($limit) {
			$this->setState('limit', $limit);
		}

		$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');
		$limitSQL = ' LIMIT ' . $limitstart . ',' . $limit;

		$query  =   'select SQL_CALC_FOUND_ROWS a.*, count(cp.`id`) as `post_count`';
		$query  .=  ' from `#__easyblog_category` as a';
		$query  .=  '    left join `#__easyblog_post_category` as cp on a.`id` = cp.`category_id`';

		$where = array();

		if ($bloggerId) {
			$where[] =  ' a.`created_by` = ' . $db->Quote($bloggerId);
		}

		if ($search) {
			$where[] = ' LOWER(a.' . $db->quoteName('title') . ') LIKE \'%' . $search . '%\' ';
		}

		$query .= (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		$query .=  ' group by (a.`id`)';

		if (!empty($ordering)) {
			$sortConfig = $ordering;
		}

		switch($sortConfig) {
			case 'count':
				$orderBy = ' ORDER BY `post_count` DESC';
				break;
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`ordering` ASC';
				break;
			case 'latest' :
			default :
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query .= $orderBy;
		$query .= $limitSQL;

		return $query;
	}

	/**
	 * Retrieves a list of categories created by a specific author.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getCategoriesByBlogger($id = 0, $ordering = '', $search = '', $limit = null)
	{
		$db = EB::db();

		$query = $this->_buildQueryByBlogger($id, $ordering, $search, $limit);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$categories = array();

		foreach ($result as $row) {

			$category = EB::table('Category');
			$category->bind($row);

			$categories[] = $category;
		}

		// Get categories
		$cntQuery = 'select FOUND_ROWS()';
		$db->setQuery($cntQuery);

		$this->_total = $db->loadResult();

		return $categories;
	}


	/**
	 * Retrieves a list of categories created by a specific author.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getCategoriesUsedByBlogger($userId)
	{
		$db = EB::db();

		$query = "select distinct a.* from `#__easyblog_category` as a";
		$query .= " inner join `#__easyblog_post_category` as b on a.`id` = b.`category_id`";
		$query .= " inner join `#__easyblog_post` as c on b.`post_id` = c.`id`";
		$query .= " where c.`created_by` = " . $db->Quote($userId);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Retrieves the pagination by blogger
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getPaginationByBlogger($bloggerId = 0, $search = '')
	{
		jimport('joomla.html.pagination');
		$this->_pagination  = EB::pagination($this->getTotalByBlogger($bloggerId, $search), $this->getState('limitstart'), $this->getState('limit'));
		return $this->_pagination;
	}

	/**
	 * Retrieves the total category by blogger
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getTotalByBlogger($bloggerId = 0, $search = '')
	{
		// Lets load the content if it doesn't already exist
		$query = $this->_buildQueryByBlogger($bloggerId, $search);
		$total = $this->_getListCount($query);

		return $total;
	}

	/**
	 * Retrieves the category params
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getParams()
	{
		static $result = array();

		if (!isset($result[$this->id])) {
			$result[$this->id] = new JRegistry($this->params);
		}

		return $result[$this->id];
	}

	/**
	 * Retrieves a list of parent categories from the site
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getParentCategories($contentId, $type = 'all', $isPublishedOnly = false, $isFrontendWrite = false , $exclusion = array())
	{
		$config = EB::config();
		$db = EB::db();
		$my = JFactory::getUser();

		// Get the sorting options
		$sortConfig = $this->config->get('layout_sorting_category','latest');

		$query = 'SELECT a.' . $db->quoteName('id') . ', a.' . $db->quoteName('title') . ', a.' . $db->quoteName('alias');
		$query .= ', a.' . $db->quoteName('private') . ', a.' . $db->quoteName('default') . ', a.`lft`, a.`rgt`, a.`params`';
		$query .= ' FROM ' . $db->quoteName('#__easyblog_category') . ' AS a';
		$query .= ' WHERE a.' . $db->quoteName('parent_id') . '=' . $db->Quote(0);

		if ($type == 'blogger') {
			$query .= ' AND a.' . $db->quoteName('created_by') . '=' . $db->Quote($contentId);

		} else if($type == 'category') {

			$query .= ' AND a.' . $db->quoteName('id') . '=' . $db->Quote($contentId);
		}

		if ($isPublishedOnly) {
			$query .= ' AND a.' . $db->quoteName('published') . '=' . $db->Quote(1);
		}

		if ($isFrontendWrite) {
			$gid = EB::getUserGids();
			$gids = '';
			if (count($gid) > 0) {
				foreach ($gid as $id) {
					$gids .= (empty($gids)) ? $db->Quote($id) : ',' . $db->Quote($id);
				}
			}

			$query .= ' and a.id not in (';
			$query .= ' select id from `#__easyblog_category` as c';
			$query .= ' where not exists (';
			$query .= '     select b.category_id from `#__easyblog_category_acl` as b';
			$query .= '         where b.category_id = c.id and b.`acl_id` = '. $db->Quote(CATEGORY_ACL_ACTION_SELECT);
			$query .= '         and b.type = ' . $db->Quote('group');
			$query .= '         and b.content_id IN (' . $gids . ')';
			$query .= '     )';
			$query .= ' and c.`private` = ' . $db->Quote(CATEGORY_PRIVACY_ACL);
			$query .= ' and c.`parent_id` = ' . $db->Quote('0');
			$query .= ')';

			if (! JFactory::getApplication()->isAdmin()) {
				$filterLanguage     = JFactory::getApplication()->getLanguageFilter();
				if ($filterLanguage && !EB::isSiteAdmin() && $config->get('layout_composer_category_language', 0)) {
					$query .= EBR::getLanguageQuery('AND', 'a.language');
				}
			}
		}

		// @task: Process exclusion list.
		if (!empty($exclusion)) {

			$excludeQuery = 'AND a.`id` NOT IN (';
			for ($i = 0 ; $i < count($exclusion); $i++) {
				$id = $exclusion[$i];

				$excludeQuery .= $db->Quote($id);

				if (next($exclusion) !== false) {
					$excludeQuery .= ',';
				}
			}

			$excludeQuery .= ')';
			$query .= $excludeQuery;
		}

		switch ($sortConfig)
		{
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ASC';
				break;
			case 'latest' :
			default :
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query .= $orderBy;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves the child categories of a parent category
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getChildCategories($parentId , $isPublishedOnly = false, $isFrontendWrite = false , $exclusion = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config = EB::config();

		$sortConfig = $config->get('layout_sorting_category','latest');

		$category = EB::table('Category');

		if (EB::cache()->exists($parentId, 'category')) {
			$category = EB::cache()->get($parentId, 'category');
		} else {
			$category->load($parentId);
		}

		$query = 'select a.`id`, a.`title`, a.`alias`, a.`private`, a.`parent_id`, a.`avatar`, a.`description`';
		$query .= ' from `#__easyblog_category` as a';
		$query .= ' WHERE a.`lft` > ' . $db->Quote($category->lft);
		$query .= ' AND a.`lft` < ' . $db->Quote($category->rgt);

		if ($isPublishedOnly) {
			$query .= ' and a.`published` = ' . $db->Quote('1');
		}

		$acl = CATEGORY_ACL_ACTION_VIEW;
		if ($isFrontendWrite) {
			$acl = CATEGORY_ACL_ACTION_SELECT;
		}

		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccess = $catLib::genCatAccessSQL('a.`private`', 'a.`id`', $acl);
			$query .= ' AND (' . $catAccess . ')';
		}


		// @task: Process exclusion list.
		if (!empty($exclusion)) {
			$excludeQuery   = 'AND a.`id` NOT IN (';
			for ($i = 0 ; $i < count($exclusion); $i++) {
				$id = $exclusion[$i];

				$excludeQuery .= $db->Quote($id);

				if (next($exclusion) !== false) {
					$excludeQuery .= ',';
				}
			}

			$excludeQuery .= ')';
			$query .= $excludeQuery;
		}

		switch ($sortConfig) {
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ASC';
				break;
			case 'latest' :
			default :
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query .= $orderBy;

		// echo $query;
		// echo '<br /><br />';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves the primary category for the blog post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getPrimaryCategory($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT b.* FROM ' . $db->qn('#__easyblog_post_category') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_category') . ' AS b';
		$query[] = 'ON a.' . $db->qn('category_id') . ' = b.' . $db->qn('id');
		$query[] = 'WHERE a.' . $db->qn('primary') . '=' . $db->Quote(1);
		$query[] = 'AND a.' . $db->qn('post_id') . '=' . $db->Quote($id);

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result = $db->loadObject();

		if (!$result) {
			return false;
		}

		$category = EB::table('Category');
		$category->bind($result);

		return $category;
	}

	/**
	 * Retrieve all private categories.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getPrivateCategories()
	{
		$db = EB::db();

		$query = 'select a.`id`';
		$query .= ' from `#__easyblog_category` as a';
		$query .= ' where a.`private` = ' . $db->Quote('1');

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieve category child count.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getChildCount($categoryId , $published = false)
	{
		$db = EB::db();

		$query  = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_category') . ' '
				. 'WHERE ' . $db->nameQuote('parent_id') . '=' . $db->Quote($categoryId);

		if ($published) {
			$query  .= ' AND ' . $db->nameQuote('published') . '=' . $db->Quote(1);
		}

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Search category
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function searchCategories($keyword, $options = array())
	{
		$db = EB::db();

		if (! $keyword) {
			return array();
		}

		$ignoreParent = isset($options['ignoreParent']) ? $options['ignoreParent'] : false;

		$gid = EB::getUserGids();
		$gids = '';

		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= (empty($gids)) ? $db->Quote($id) : ',' . $db->Quote($id);
			}
		}

		$query  = 'SELECT a.`id`, a.`lft`, a.`rgt`, a.`parent_id`';
		$query .= ' FROM `#__easyblog_category` AS a';
		$query .= ' WHERE a.`published` = ' . $db->Quote('1');
		if ($ignoreParent) {
			$query .= ' AND a.`parent_id` > ' . $db->Quote('0');
		}

		$query .= ' AND a.`title` LIKE ' . $db->Quote('%' . $keyword . '%');
		$query .= ' and a.id not in (';
		$query .= ' select id from `#__easyblog_category` as c';
		$query .= ' where not exists (';
		$query .= '     select b.category_id from `#__easyblog_category_acl` as b';
		$query .= '         where b.category_id = c.id and b.`acl_id` = '. $db->Quote(CATEGORY_ACL_ACTION_SELECT);
		$query .= '         and b.type = ' . $db->Quote('group');
		$query .= '         and b.content_id IN (' . $gids . ')';
		$query .= '     )';
		$query .= ' and c.`private` = ' . $db->Quote(CATEGORY_PRIVACY_ACL);
		$query .= ')';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (! $results) {
			return array();
		}


		$config = EB::config();
		$sortConfig = $config->get('layout_sorting_category','latest');
		$categories = array();


		// now for each matched categories, we will have to fetch the hierachy tree.
		foreach ($results as $row) {

			$query = 'select a.`id`, a.`title`, a.`alias`, a.`private`, a.`parent_id`, a.`lft`, a.`rgt`, a.`params`, ';

			$query  .= ' (SELECT COUNT(id) FROM ' . $db->nameQuote('#__easyblog_category') . ' as c';
			$query  .= ' WHERE c.`parent_id` = a.`id`) AS childs';

			$query .= ' from `#__easyblog_category` as a';

			$query .= " inner join `#__easyblog_category` as b on a.`lft` >= b.`lft` and a.`rgt` <= b.`rgt`";

			// we do not want the parent category.
			$query .= ' WHERE a.`published` = ' . $db->Quote('1');
			if ($ignoreParent) {
				$query .= ' AND a.`parent_id` > ' . $db->Quote('0');
			}

			$acl = CATEGORY_ACL_ACTION_SELECT;

			$catLib = EB::category();
			if ($config->get('main_category_privacy')) {
				$catAccess = $catLib::genCatAccessSQL('a.`private`', 'a.`id`', $acl);
				$query .= ' AND (' . $catAccess . ')';
			}


			$query .= ' and b.`lft` <= ' . $db->Quote($row->lft);
			$query .= ' and b.`rgt` >= ' . $db->Quote($row->rgt);
			$query .= ' and b.`parent_id` = ' . $db->Quote('0');

			switch ($sortConfig) {
				case 'alphabet' :
					$orderBy = ' ORDER BY a.`title` ASC';
					break;
				case 'ordering' :
					$orderBy = ' ORDER BY a.`lft` ASC';
					break;
				case 'latest' :
				default :
					$orderBy = ' ORDER BY a.`created` DESC';
					break;
			}

			$query .= $orderBy;

			$db->setQuery($query);

			$childs = $db->loadObjectList();

			if ($childs) {
				foreach ($childs as $item) {
					$categories[] = $item;
				}
			}

		}

		return $categories;

	}

	/**
	 * Retrieve category hierachy
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getCategoriesHierarchy($useLimit = true)
	{
		$db = EB::db();
		$config = EB::config();

		$limit = '10';
		$limitstart = $this->getState('limitstart');

		$search = $this->input->get('search', '');

		$gid = EB::getUserGids();
		$gids = '';
		if (count($gid) > 0) {
			foreach ($gid as $id) {
				$gids .= (empty($gids)) ? $db->Quote($id) : ',' . $db->Quote($id);
			}
		}

		$query  = 'SELECT a.*, (SELECT COUNT(id) FROM `#__easyblog_category` WHERE `lft` < a.`lft` AND `rgt` > a.`rgt`) AS depth';
		$query  .= ' FROM `#__easyblog_category` AS a';
		$query  .= ' WHERE a.`published` = ' . $db->Quote('1');
		if(!empty($search))
			$query  .= ' AND a.`title` LIKE ' . $db->Quote('%' . $search . '%');

		$query .= ' and a.id not in (';
		$query .= ' select id from `#__easyblog_category` as c';
		$query .= ' where not exists (';
		$query .= '     select b.category_id from `#__easyblog_category_acl` as b';
		$query .= '         where b.category_id = c.id and b.`acl_id` = '. $db->Quote(CATEGORY_ACL_ACTION_SELECT);
		$query .= '         and b.type = ' . $db->Quote('group');
		$query .= '         and b.content_id IN (' . $gids . ')';
		$query .= '     )';
		$query .= ' and c.`private` = ' . $db->Quote(CATEGORY_PRIVACY_ACL);
		$query .= ')';


		if (! JFactory::getApplication()->isAdmin()) {
			$filterLanguage = JFactory::getApplication()->getLanguageFilter();
			if ($filterLanguage && !EB::isSiteAdmin() && $config->get('layout_composer_category_language', 0)) {
				$query .= EBR::getLanguageQuery('AND', 'a.language');
			}
		}


		$query  .= ' AND a.`parent_id` NOT IN (SELECT `id` FROM `#__easyblog_category` AS e WHERE e.`published` = ' . $db->Quote('0') . ' AND e.`parent_id` = ' . $db->Quote('0') . ')';


		$query  .= ' ORDER BY a.`lft`';

		if ($useLimit) {
			$this->_total = $this->_getListCount($query);

			jimport('joomla.html.pagination');
			$this->_pagination  = EB::pagination($this->_total , $limitstart , $limit);
			$query  .= ' LIMIT ' . $limitstart . ', ' . $limit;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Removes any association of a blog post with a category
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function deleteAssociation($blogId)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_post_category');
		$query .= ' WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($blogId);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * This method reduces the number of query hit on the server
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function preloadUserSubscription($catIds, $email)
	{
		$db = EB::db();

		$query  = 'SELECT `uid` FROM `#__easyblog_subscriptions`';
		$query .= ' WHERE `uid` IN (' . implode(',', $catIds) . ')';
		$query .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY);
		$query .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$results = $db->loadColumn();

		$cats = array();

		if ($results) {
			foreach($results as $cat) {
				$cats[$cat] = '1';
			}
		}

		return $cats;
	}

	/**
	 * This method reduces the number of query hit on the server
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function preloadPosts($catIds, $limit = null)
	{
		$db = EB::db();
		$config = EB::config();

		if (is_null($limit)) {
			$limit = EB::call('Pagination', 'getLimit');
		}

		$gids = EB::getUserGids('', true);

		// Determines if this is currently on blogger mode
		$isBloggerMode = EBR::isBloggerMode();

		$sort = $config->get('layout_postsort', 'DESC');
		$ordering = $config->get('layout_categorypostorder', 'created');

		// Ordering column should be publish_up if the ordering is configured to be publishing date
		if ($ordering == 'published') {
			$ordering = 'publish_up';
		}

		$query = array();

		$i = 1;
		foreach ($catIds as $cid => $cIds) {

			$p = 'p'.$i;
			$a = 'a'.$i;
			$f = 'f'.$i;

			$isJSGrpPluginInstalled = false;
			$isJSGrpPluginInstalled = JPluginHelper::isEnabled('system', 'groupeasyblog');
			$isEventPluginInstalled = JPluginHelper::isEnabled('system' , 'eventeasyblog');
			$isJSInstalled  = false; // need to check if the site installed jomsocial.

			if (EB::jomsocial()->exists()) {
				$isJSInstalled = true;
			}

			$includeJSGrp = ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
			$includeJSEvent = ($isEventPluginInstalled && $isJSInstalled) ? true : false;

			// contribution type sql
			$contributor = EB::contributor();
			$contributeSQL = " AND (($p.`source_type` = " . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ") ";
			if ($config->get('main_includeteamblogpost')) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, $p);
			}
			if ($includeJSEvent) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT, $p);
			}
			if ($includeJSGrp) {
				$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP, $p);
			}

			// Test if easysocial exists on the site
			if (EB::easysocial()->exists()) {
				if (EB::easysocial()->isBlogAppInstalled('group')) {
					$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP, $p);
				}

				if (EB::easysocial()->isBlogAppInstalled('page')) {
					$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE, $p);
				}

				if (EB::easysocial()->isBlogAppInstalled('event')) {
					$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT, $p);
				}
			}

			$contributeSQL .= ")";



			$tmp = "(select distinct $p.*, " . $db->Quote($cid) . " as `parent_category_id`, IFNULL($f.`id`, 0) as `featured`";
			$tmp .= "   from `#__easyblog_post` as $p";
			$tmp .= "       inner join `#__easyblog_post_category` as $a on $p.`id` = $a.`post_id`";
			$tmp .= " LEFT JOIN `#__easyblog_featured` AS $f";
			$tmp .= "   ON $p.`id` = $f.`content_id` AND $f.`type` = " . $db->Quote('post');
			if (count($cIds) == 1) {
				$tmp .= " where $a.`category_id` = " . $db->Quote($cIds[0]);
			} else {
				$tmp .= " where $a.`category_id` IN (" . implode(',', $cIds) . ")";
			}
			$tmp .= " and $p.`published` = " . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$tmp .= " and $p.`state` = " . $db->Quote(EASYBLOG_POST_NORMAL);

			if ($isBloggerMode !== false) {
				$tmp .= " AND $p." . $db->qn('created_by') . " = " . $db->Quote($isBloggerMode);
			} else {

				// Get the author id based on the category menu
				$authorId = EB::getCategoryMenuBloggerId();

				if ($authorId) {
					$tmp .= " AND $p." . $db->qn('created_by') . " = " . $db->Quote($authorId);
				}
			}

			// If user is a guest, ensure that they can really view the blog post
			if ($this->my->guest) {
				$tmp .= " AND $p." . $db->qn('access') . " = " . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}

			// Ensure that the blog posts is available site wide
			$tmp .= $contributeSQL;
			// $tmp .= " AND $p." . $db->qn('source_id') . " = " . $db->Quote("0");

			// Filter by language
			$language = EB::getCurrentLanguage();

			if ($language) {
				$tmp .= " AND ($p." . $db->qn('language') . "=" . $db->Quote($language) . " OR $p." . $db->qn('language') . "=" . $db->Quote('*') . " OR $p." . $db->qn('language') . "=" . $db->Quote('') . ")";
			}


			if ($config->get('main_category_privacy')) {
				// if a post has multiple categories and one of the category is not accessible, we should exclude this post. #790
				$acp = 'acp' . $i;
				$cat = 'cat' . $i;

				$tmp .= " and not exists (";
				$tmp .= "   select $acp.post_id from `#__easyblog_post_category` as $acp";
				$tmp .= "       inner join  `#__easyblog_category` as $cat on $acp.`category_id` = $cat.`id`";
				$tmp .= "   where $acp.`post_id` = $a.`post_id`";
				$tmp .= "   and (";
				$tmp .= "       ($cat.`private` = 1 and (" . $this->my->id . " = 0)) OR ";
				$tmp .= "       ($cat.`private` = 2 and (select count(1) from `#__easyblog_category_acl` as cacl2 where cacl2.`category_id` = $cat.`id` and cacl2.`acl_id` = " . CATEGORY_ACL_ACTION_VIEW . " and cacl2.`content_id` IN ($gids)) = 0)";
				$tmp .= "  )";
				$tmp .= ")";
			}

			$tmp .= " ORDER BY $p." . $db->quoteName($ordering) . " " . $sort;

			$tmp .= " limit " . $limit . ")";

			// echo $tmp;
			// echo '<br><br>';
			// exit;

			$query[] = $tmp;

			$i++;
		}

		$query = implode(' UNION ALL ', $query);

		// here we sort the results. #1050
		$query = "select * from (" . $query . ") as xx";
		$query .= " ORDER BY xx." . $db->quoteName($ordering) . " " . $sort;


		// echo $query;exit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$posts = array();

		if ($results) {
			foreach($results as $row) {
				$posts[$row->parent_category_id][] = $row;
			}
		}

		return $posts;
	}

	/**
	 * Preload authors count
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function preloadActiveAuthorsCount($catIds)
	{
		$db = EB::db();

		$query = "select b.`category_id`, count(distinct u.`id`) as `cnt`";
		$query .= " from `#__easyblog_users` as u";
		$query .= "   inner join `#__easyblog_post` as a";
		$query .= "     on u.`id` = a.`created_by`";
		$query .= "   inner join `#__easyblog_post_category` as b";
		$query .= "     on a.`id` = b.`post_id`";
		$query .= " where b.`category_id` in (" . implode(',', $catIds) . ")";
		$query .= " group by b.category_id";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$membersCnt = array();

		if ($results) {
			foreach ($results as $row) {
				$membersCnt[$row->category_id] = $row->cnt;
			}
		}

		return $membersCnt;
	}

	/**
	 * Preload authors
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function preloadActiveAuthors($catIds)
	{
		$db = EB::db();

		$query = "select distinct b.`category_id`, u.id";
		$query .= " from `#__easyblog_users` as u";
		$query .= "   inner join `#__easyblog_post` as a";
		$query .= "     on u.`id` = a.`created_by`";
		$query .= "   inner join `#__easyblog_post_category` as b";
		$query .= "     on a.`id` = b.`post_id`";
		$query .= " where b.`category_id` in (" . implode(',', $catIds) . ")";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$members = array();

		if ($results) {

			//preload users
			$ids = array();
			foreach($results as $row) {
				$ids[] = $row->id;
			}

			// unique the arrays;
			$ids = array_unique($ids);

			EB::user($ids);

			foreach($results as $row) {
				$tbl = EB::user($row->id);
				$members[$row->category_id][] = $tbl;
			}
		}

		return $members;
	}

	/**
	 * This method reduces the number of query hit on the server
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function preloadCategoryChilds($catIds)
	{
		$db = EB::db();
		$language = EB::getCurrentLanguage();

		$config = EB::config();
		$sortConfig = $config->get('layout_sorting_category','latest');

		$query = "select a.id as category_id, b.`id`, b.`title`, b.`alias`, b.`private`, b.`parent_id`";
		$query .= " from `#__easyblog_category` as a";
		$query .= " inner join `#__easyblog_category` as b on a.`lft` < b.`lft` and a.`rgt` > b.`lft`";
		$query .= " where a.`id` in (" . implode(',', $catIds) . ")";
		$query .=  " and a.`published` = " . $db->Quote('1');

		if ($language) {
			$query .= ' AND(';
			$query .= ' b.' . $db->quoteName('language') . '=' . $db->Quote($language);
			$query .= ' OR';
			$query .= ' b.' . $db->quoteName('language') . '=' . $db->Quote('');
			$query .= ' OR';
			$query .= ' b.' . $db->quoteName('language') . '=' . $db->Quote('*');
			$query .= ')';
		}

		if ($config->get('main_category_privacy')) {
			$catLib = EB::category();
			$catAccess = $catLib::genCatAccessSQL('b.`private`', 'b.`id`');

			$query .= " AND ($catAccess)";
		}

		switch ($sortConfig) {
			case 'alphabet' :
				$orderBy = ' ORDER BY b.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY b.`lft` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY b.`created` DESC';
				break;
		}

		$query .= $orderBy;

		$db->setQuery($query);

		$results = $db->loadObjectList();

		$childs = array();

		if ($results) {
			foreach($results as $child) {
				$childs[$child->category_id][] = $child;
			}
		}

		return $childs;
	}


	/**
	 * This method reduces the number of query hit on the server
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function preload($postIds = array())
	{
		if (!$postIds) {
			return $postIds;
		}

		$db = EB::db();

		$query = array();
		$query = array();
		$query[] = 'SELECT a.`post_id`, b.* FROM ' . $db->qn('#__easyblog_post_category') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_category') . ' AS b';
		$query[] = 'ON a.' . $db->qn('category_id') . ' = b.' . $db->qn('id');
		$query[] = 'WHERE a.' . $db->qn('primary') . '=' . $db->Quote(1);
		$query[] = 'AND a.' . $db->qn('post_id') . ' IN(' . implode(',', $postIds) . ')';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$primaryCategories = array();


		foreach ($result as $item) {
			$category = EB::table('Category');
			$category->bind($item);

			$primaryCategories[$item->post_id] = $category;
		}

		return $primaryCategories;
	}

	/**
	 * Retrieve categories hierary
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getCategoryTree($sort = 'latest')
	{
		$db = EB::db();
		$my = JFactory::getUser();

		$config = EB::config();

		$queryExclude = '';
		$excludeCats = array();

		$query = 'SELECT a.*, ';
		$query .= ' (SELECT COUNT(id) FROM ' . $db->nameQuote('#__easyblog_category');
		$query .= ' WHERE lft < a.lft AND rgt > a.rgt AND a.lft != ' . $db->Quote(0) . ') AS depth ';
		$query .= ' FROM ' . $db->nameQuote('#__easyblog_category') . ' AS a ';
		$query .= ' WHERE a.`published`=' . $db->Quote('1');

		if ($config->get('main_category_privacy')) {
			// category access here
			$catLib = EB::category();
			$catAccess = $catLib::genCatAccessSQL('a.`private`', 'a.`id`');

			$query .= ' AND (' . $catAccess . ')';
		}

		switch ($sort) {
			case 'ordering':
				$query  .= ' ORDER BY `lft`, `ordering`';
				break;
			case 'alphabet':
				$query  .= ' ORDER BY `title`, `lft`';
				break;
			case 'latest':
			default:
				$query  .= ' ORDER BY `rgt` DESC';
				break;
		}

		// echo $query;

		$db->setQuery($query);

		$rows = $db->loadObjectList();
		$total = count($rows);
		$categories = array();

		for ($i = 0; $i < $total; $i++) {
			$category = EB::table('Category');
			$category->bind($rows[$i]);
			$category->depth = $rows[$i]->depth;
			$categories[] = $category;
		}
		return $categories;
	}

	/**
	 * Retrieve the immediate category hierary of a parent category
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getImmediateChildCategories($parentId, $options = array())
	{
		$db = EB::db();
		$my = JFactory::getUser();
		$config = EB::config();

		$sortConfig = $config->get('layout_sorting_category','latest');

		$category = EB::table('Category');
		$category->load($parentId);

		$query = 'select a.`id`, a.`title`, a.`alias`, a.`private`, a.`parent_id`, a.`lft`, a.`rgt`, a.`params`, ';
		$query .= ' (SELECT COUNT(id) FROM ' . $db->nameQuote('#__easyblog_category') . ' as c';
		$query .= ' WHERE c.parent_id = a.id) AS childs';
		$query .= ' from `#__easyblog_category` as a';
		$query .= ' WHERE a.`parent_id` = ' . $db->Quote($parentId);
		$query .=  ' and a.`published` = ' . $db->Quote('1');

		if ($config->get('main_category_privacy')) {
			$acl = CATEGORY_ACL_ACTION_SELECT;

			$catLib = EB::category();
			$catAccess = $catLib::genCatAccessSQL('a.`private`', 'a.`id`', $acl);
			$query .= ' AND (' . $catAccess . ')';
		}

		if (! JFactory::getApplication()->isAdmin()) {
			$filterLanguage = JFactory::getApplication()->getLanguageFilter();
			if ($filterLanguage && !EB::isSiteAdmin() && $config->get('layout_composer_category_language', 0)) {
				$query .= EBR::getLanguageQuery('AND', 'a.language');
			}
		}

		switch ($sortConfig) {
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ASC';
				break;
			case 'latest' :
			default :
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query .= $orderBy;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
