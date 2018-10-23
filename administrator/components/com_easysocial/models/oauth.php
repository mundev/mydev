<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

class EasySocialModelOAuth extends EasySocialModel
{
	public function __construct()
	{
		parent::__construct( 'people' );
	}

	/**
	 * Loads a record given the unique item id
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getRow($options = array())
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_oauth', 'a');
		$sql->column('a.*');
		$sql->join('#__users', 'b');
		$sql->on('b.id', 'a.uid');
		$sql->where('b.username', $options['username']);

		if (isset($options['client'])) {
			$sql->where('a.client', $options['client']);
		}

		$db->setQuery($sql);

		$result = $db->loadObject();

		return $result;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPullableClients()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_oauth' );
		$sql->where( 'pull' , 1 );

		$db->setQuery( $sql );

		$items 	= $db->loadObjectList();

		return $items;
	}

	/**
	 * Gets a list of oauth clients a user is associated with
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getOauthClients($userId = null)
	{
		$user = ES::user($userId);
		$db = ES::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__social_oauth');
		$query[] = 'WHERE ' . $db->qn('uid') . '=' . $db->Quote($user->id);
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote(SOCIAL_TYPE_USER);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return array();
		}

		$clients = array();

		foreach ($result as $row) {
			$table = ES::table('OAuth');
			$table->bind($row);

			$clients[] = $table;
		}

		return $clients;
	}
}
