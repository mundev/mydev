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

ES::import('admin:/includes/model');

class NotesModel extends EasySocialModel
{
	/**
	 * Retrieves a list of notes created by a particular user.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getItems($userId)
	{
		$db = ES::db();

		$query = 'SELECT * FROM ' . $db->nameQuote('#__social_notes');
		$query .= ' WHERE ' . $db->nameQuote('user_id') . ' = ' . $db->Quote($userId);
		$query .= ' ORDER BY ' . $db->nameQuote('created') . ' DESC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves the total number of notes created by the user
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function getTotalNotes($userId)
	{
		$db = ES::db();

		// Get sql helper.
		$sql = $db->sql();

		$sql->column('COUNT(1)');
		$sql->select('#__social_notes');
		$sql->where('user_id', $userId);

		$db->setQuery($sql);
		$result = (int) $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves a list of notes created by a particular user for gdpr
	 *
	 * @since	2.2
	 * @access	public
	 */
	public function getNotesGDPR($options = array())
	{
		$db = ES::db();
		$sql = $db->sql();
		$query = array();

		$limit = $this->normalize($options, 'limit', false);
		$userId = $this->normalize($options, 'userid', null);
		$exclusion = $this->normalize($options, 'exclusion', null);

		$query[] = 'SELECT * FROM ' . $db->nameQuote('#__social_notes');
		$query[] = ' WHERE ' . $db->nameQuote('user_id') . ' = ' . $db->Quote($userId);

		if ($exclusion) {

			$exclusion = ES::makeArray($exclusion);
			$exclusionIds = array();

			foreach ($exclusion as $exclusionId) {
				$exclusionIds[] = $db->Quote($exclusionId);
			}

			$exclusionIds = implode(',', $exclusionIds);

			$query[] = 'AND ' . $db->nameQuote('id') . ' NOT IN (' . $exclusionIds . ')';
		}

		if ($limit) {
			$totalQuery = implode(' ', $query);

			// Set the total number of items.
			$this->setTotal($totalQuery, true);
		}

		// Get the limitstart.
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$query[] = "limit $limitstart, $limit";

		$query = implode(' ', $query);

		$sql->clear();
		$sql->raw($query);

		$this->db->setQuery($sql);
		$result = $this->db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$table = ES::table('app');
		$table->load(array('group' => 'user', 'element' => 'notes'));

		$apps = ES::apps()->getApp($table);
		$notes = array();

		foreach ($result as $row) {

			$noteTbl = $apps->table('Note');
			$noteTbl->load($row->id);

			$noteTbl->content = $noteTbl->getContent();

			$notes[] = $noteTbl;
		}

		return $notes;
	}	
}
