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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

jimport('joomla.filesystem.file');

class plgSearchEasyblog extends JPlugin
{
	public function __construct(&$subject, $params)
	{
		$this->my = JFactory::getUser();

		if ($this->exists()) {
			$this->config = EB::config();
		}

		parent::__construct($subject, $params);
	}

	/**
	 * Retrieves the list of search areas
	 *
	 * @since	5.0.36
	 * @access	public
	 */
	public function onContentSearchAreas()
	{
		// Load site's language
		EB::loadLanguages();

		$areas = array('blogs' => JText::_('PLG_EASYBLOG_SEARCH_BLOGS'));

		return $areas;
	}

	/**
	 * When user performs a search
	 *
	 * @since	5.0.36
	 * @access	public
	 */
	public function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$plugin	= JPluginHelper::getPlugin('search', 'easyblog');
		$params = EB::registry($plugin->params);

		// Check if EasyBlog exists and is installed
		if (!self::exists()) {
			return array();
		}

		// Get the list of search areas
		$searchAreas = self::onContentSearchAreas();

		if (is_array($areas) && !array_intersect($areas, array_keys($searchAreas))) {
			return array();
		}

		$text = trim($text);

		if ($text == '') {
			return array();
		}

		// Get search results
		$result = $this->getResult($text, $phrase, $ordering);

		if (!$result) {
			return array();
		}

		$posts = array();

		foreach ($result as &$row) {

			$post = EB::post($row->id);

			$row->section = JText::sprintf('PLG_EASYBLOG_SEARCH_BLOGS_SECTION', $post->getPrimaryCategory()->title);
			$row->href = $post->getPermalink();
			$row->image = $post->getImage('large', false, true);
		}

		return $result;
	}

	/**
	 * Determines if EasyBlog exists on the site.
	 *
	 * @since	5.0.36
	 * @access	public
	 */
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
		$enabled = JComponentHelper::isEnabled('com_easyblog');

		if (!JFile::exists($file) || !$enabled) {
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Performs the real searching for blog posts here.
	 *
	 * @since	5.0.36
	 * @access	public
	 */
	public function getResult($text, $phrase, $ordering)
	{
		$db = EB::db();
		$where	= array();
		$where2	= array();

		// used for privacy
		$queryWhere = '';
		$queryExclude = '';
		$queryExcludePending = '';
		$excludeCats = array();

		// Exact matches
		if ($phrase == 'exact') {

			$searchText = $db->Quote('%' . $db->escape($text, true) . '%', false);

			$where[] = 'a.`title` LIKE ' . $searchText;
			$where[] = 'a.`content` LIKE ' . $searchText;
			$where[] = 'a.`intro` LIKE ' . $searchText;
			$where2 = '(t.`title` LIKE ' . $searchText . ')';
			$where = '(' . implode(') OR (', $where) . ')';

		} else {

			$words = explode(' ', $text);
			$wheres = array();
			$where2 = array();
			$wheres2 = array();

			foreach ($words as $word) {

				$word = $db->Quote( '%'. $db->escape($word, true) .'%', false );

				$where[] = 'a.`title` LIKE ' . $word;
				$where[] = 'a.`content` LIKE ' . $word;
				$where[] = 'a.`intro` LIKE ' . $word;

				$where2[] = 't.title LIKE ' . $word;

				$wheres[] = implode(' OR ', $where );
				$wheres2[] = implode(' OR ', $where2);
			}

			$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
			$where2	= '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres2 ) . ')';
		}

		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled('system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled('system', 'eventeasyblog');

		// Need to check if the site installed jomsocial.
		$isJSInstalled = EB::jomsocial()->exists();

		$includeJSGrp	= ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

		// Get teamblogs id.
		$query = '';

		// contribution type sql
		$contributor = EB::contributor();
		$contributeSQL = ' AND ( (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

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

		$query	= 'SELECT a.*, CONCAT(a.`content` , a.`intro`) AS text , "2" as browsernav';
		$query	.= ' FROM `#__easyblog_post` as a USE INDEX (`easyblog_post_searchnew`) ';

		$query	.= ' WHERE (' . $where;
		$query	.= ' OR a.`id` IN( ';
		$query	.= '		SELECT tp.`post_id` FROM `#__easyblog_tag` AS t ';
		$query	.= '		INNER JOIN `#__easyblog_post_tag` AS tp ON tp.`tag_id` = t.`id` ';
		$query	.= '		WHERE ' . $where2;
		$query	.= '))';

		// Guests should only see public post.
		if ($this->my->guest) {
			$query .= ' AND a.`access` = ' . $db->Quote('0');
		}

		// Do not render unpublished posts
		$query .= ' AND a.' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' AND a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query .= $queryWhere;

		if ($ordering == 'oldest') {
			$query .= ' ORDER BY a.`created` ASC';
		}

		if ($ordering == 'newest') {
			$query .= ' ORDER BY a.`created` DESC';
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

}
