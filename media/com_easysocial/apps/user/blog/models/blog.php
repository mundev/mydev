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

ES::import('admin:/includes/model');

class BlogModel extends EasySocialModel
{
	public function exists()
	{
		jimport('joomla.filesystem.file');

		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		if (JFile::exists($file)) {
			require_once($file);
			return true;
		}

		return false;
	}

	/**
	 * Retrieves a list of blog posts created in a page
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getItems($userId = null, $options = array())
	{
		if (!$this->exists()) {
			return array();
		}

		$db = ES::db();
		$query = array();
		$contributor = EB::contributor();

		$includeTeam = isset($options['includeTeam']) ? $options['includeTeam'] : false;

		$contributeSQL = ' AND ((a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';

		if ($includeTeam) {
			$contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
		}

		$contributeSQL .= ')';

		$query[] = 'SELECT * FROM ' . $db->qn('#__easyblog_post') . ' as a';
		$query[] = 'WHERE a.' . $db->qn('created_by') . '=' . $db->Quote($userId);

		$query[] = $contributeSQL;

		$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query[] = 'ORDER BY ' . $db->qn('created') . ' DESC';

		if (isset($options['limit'])) {
			// Set total
			$this->setTotal(implode(' ', $query), true);
			$this->setState('limit', $options['limit']);
			$this->setState('limitstart', JFactory::getApplication()->input->get('limitstart', 0, 'int'));
		}
		
		$query = implode(' ', $query);

		$result = $this->getData($query);

		if (!$result) {
			return $result;
		}

		// Format the blog post
		$result = EB::formatter('list', $result);

		return $result;
	}
}