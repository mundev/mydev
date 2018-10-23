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

jimport('joomla.application.component.model');

class EasySocialModelVerifications extends EasySocialModel
{
	private $data = null;

	public function __construct($config = array())
	{
		$this->displayOptions = array();
		parent::__construct('verifications', $config);
	}

	/**
	 * Populates the state
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function initStates()
	{

		// $this->setState( 'published' , $published );
		// $this->setState( 'group'	, $group );
		// $this->setState( 'profile'	, $profile );

		parent::initStates();
	}

	/**
	 * Retrieves a list of items that has submitted for verifications
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function getVerificationList($type = SOCIAL_TYPE_USER)
	{
		$db = $this->db;
		$sql = $db->sql();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__social_verification_requests');
		$query[] = 'WHERE ' . $db->qn('type') . '=' . $db->Quote($type);
		$query[] = 'AND ' . $db->qn('state') . '=' . $db->Quote(ES_VERIFICATION_REQUEST);

		$sql->raw($query);
		$db->setQuery($sql);

		$items = $db->loadObjectList();

		return $items;
	}
}
