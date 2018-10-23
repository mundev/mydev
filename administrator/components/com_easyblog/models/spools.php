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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelSpools extends EasyBlogAdminModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	protected $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	protected $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	protected $_data = null;

	public function __construct()
	{
		parent::__construct();
		$mainframe	= JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest('com_easyblog.categories.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery(false, true);

			$db = EB::db();
			$db->setQuery($query);

			$this->_total = $db->loadResult();

			// $this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	public function _buildQuery($publishedOnly = false, $isCount = false)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere($publishedOnly);
		$orderby = $this->_buildQueryOrderBy();
		$db = EB::db();

		$query = 'SELECT * ';

		if ($isCount) {
			$query = 'SELECT COUNT(1) ';
		}

		$query .= 'FROM ' . $db->nameQuote('#__easyblog_mailq');
		$query .= $where;

		$query .= $orderby;

		return $query;
	}

	public function _buildQueryWhere()
	{
		$mainframe = JFactory::getApplication();
		$db = EB::db();

		$filter_state = $mainframe->getUserStateFromRequest('com_easyblog.spools.filter_state', 'filter_state', '', 'word');
		$search = $mainframe->getUserStateFromRequest('com_easyblog.spools.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EBString::strtolower($search)));

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote('status') . '=' . $db->Quote('1');
			} else if ($filter_state == 'U') {
				$where[] = $db->nameQuote('status') . '=' . $db->Quote('0');
			}
		}

		if ($search) {
			$where[] = ' LOWER(subject) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	public function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		// we need to order by id so that the same date value will not messed up the sorting. #1047
		$orderby = ' ORDER BY `id` DESC';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData($usePagination = true)
	{
		$limit = $this->getState('limit', 0);
		$limitstart = $this->getState('limitstart', 0);

		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			if ($usePagination && $limit) {
				// $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));


				$db = EB::db();

				$query .= ' LIMIT ' . $limitstart . ', ' . $limit;
				$db->setQuery($query);

				$this->_data = $db->loadObjectList();
			} else {
				$this->_data = $this->_getList($query);
			}
		}

		return $this->_data;
	}

	/**
	 * Purges all emails from the system
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function purge($type = '')
	{
		$db = EB::db();
		$query = array();
		$query[] = 'DELETE FROM ' . $db->qn('#__easyblog_mailq');

		if ($type == 'sent') {
			$query[] = 'WHERE ' . $db->qn('status') . '=' . $db->Quote(1);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Retrieves a list of email template files
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFiles()
	{
		$folder = $this->getFolder();

		// Retrieve the list of files
		$rows = JFolder::files($folder, '.', true, true);
		$files = array();

		// Get the current site template
		$currentTemplate = $this->getCurrentTemplate();

		foreach ($rows as $row) {

			$row = EB::normalizeSeparator($row);
			$fileName = basename($row);

			if ($fileName == 'index.html' || stristr($fileName, '.orig') !== false) {
				continue;
			}

			// Get the file object
			$file = $this->getTemplate($row);
			$files[] = $file;
		}

		return $files;
	}

	/**
	 * Generates the path to an email template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFolder()
	{
		$folder = EBLOG_ROOT . '/themes/wireframe/emails/html';
		$folder = EB::normalizeSeparator($folder);

		return $folder;
	}

	/**
	 * Generates the path to the overriden folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getOverrideFolder($file)
	{
		$path = JPATH_ROOT . '/templates/' . $this->getCurrentTemplate() . '/html/com_easyblog/emails/html/' . ltrim($file, '/');

		return $path;
	}

	/**
	 * Retrieves a list of email templates
	 *
	 * @since	5.1
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTemplate($absolutePath, $contents = false)
	{
		$file = new stdClass();
		$file->name = basename($absolutePath);

		$file->desc = str_ireplace('.php', '', $file->name);
		$file->desc = strtoupper(str_ireplace(array('.', '-'), '_', $file->desc));
		$file->desc = JText::_('COM_EASYBLOG_EMAILS' . $file->desc);
		$file->path = $absolutePath;
		$file->relative = str_ireplace($this->getFolder(), '', $file->path);

		// Get the current site template
		$currentTemplate = $this->getCurrentTemplate();

		// Determine if the email template file has already been overriden.
		$overridePath = $this->getOverrideFolder($file->relative);

		$file->override = JFile::exists($overridePath) ? 1 : 0;
		$file->overridePath = $overridePath;
		$file->contents = '';

		if ($contents) {
			if ($file->override) {
				$file->contents = JFile::read($file->overridePath);
			} else {
				$file->contents = JFile::read($file->path);
			}
		}

		return $file;
	}

	/**
	 * Retrieves the current site template
	 *
	 * @since	5.1
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCurrentTemplate()
	{
		$db = EB::db();

		$query = 'SELECT ' . $db->nameQuote('template') . ' FROM ' . $db->nameQuote('#__template_styles');
		$query .= ' WHERE ' . $db->nameQuote('home') . '=' . $db->Quote(1);
		$query .= ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$template = $db->loadResult();
		return $template;
	}

	/**
	 * Saves contents
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function write($path, $contents)
	{
		$state = JFile::write($path, $contents);

		return $state;
	}

	/**
	 * Delete particular user email activities 
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function removeUserEmailActivities($recipientEmail = '')
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_mailq');
		$query .= ' WHERE ' . $db->nameQuote('recipient') . '=' . $db->Quote($recipientEmail);

		$db->setQuery($query);
 		$state = $db->Query();

		if (!$state) {
			return false;
		}

		return true;
	}	
}
