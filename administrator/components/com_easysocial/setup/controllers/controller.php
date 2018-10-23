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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
jimport('joomla.database.driver');
jimport('joomla.installer.helper');

class EasySocialSetupController
{
	private $result = array();

	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
	}

	protected function data($key, $value)
	{
		$obj = new stdClass();
		$obj->$key = $value;

		$this->result[] = $obj;
	}

	/**
	 * Renders a response with proper headers
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function output($data = array())
	{
		header('Content-Type: application/json; UTF-8');

		if (empty($data)) {
			$data = $this->result;
		}

		echo json_encode($data);
		exit;
	}

	/**
	 * Generates a result object that can be json encoded
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function getResultObj($message, $state, $stateMessage = '')
	{
		$obj = new stdClass();
		$obj->state = $state;
		$obj->stateMessage = $stateMessage;
		$obj->message = JText::_($message);

		return $obj;
	}

	/**
	 * Get's the version of this launcher so we know which to install
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getVersion()
	{
		static $version = null;

		// Get the version from the manifest file
		if (is_null($version)) {
			$contents 	= JFile::read( JPATH_ROOT . '/administrator/components/com_easysocial/easysocial.xml' );
			$parser 	= simplexml_load_string( $contents );
			$version 	= $parser->xpath( 'version' );
			$version 	= (string) $version[ 0 ];
		}

		return $version;
	}

	/**
	 * Retrieve the Joomla Version
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function getJoomlaVersion()
	{
		$jVerArr = explode('.', JVERSION);
		$jVersion = $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	/**
	 * Retrieves the current site's domain information
	 *
	 * @since	2.0.9
	 * @access	public
	 */
	public function getDomain()
	{
		static $domain = null;

		if (is_null($domain)) {
			$domain = JURI::root();
			$domain = str_ireplace(array('http://', 'https://'), '', $domain);
			$domain = rtrim($domain, '/');
		}

		return $domain;
	}

	/**
	 * Retrieves the information about the latest version
	 *
	 * @since	2.0.9
	 * @access	public
	 */
	public function getInfo()
	{
		// Get the md5 hash from the server.
		$resource = curl_init();

		$version = $this->getVersion();

		// We need to pass the api keys to the server
		curl_setopt($resource, CURLOPT_URL, ES_MANIFEST);
		curl_setopt($resource, CURLOPT_POST, true);
		curl_setopt($resource, CURLOPT_POSTFIELDS, 'apikey=' . ES_KEY . '&from=' . $version);
		curl_setopt($resource, CURLOPT_TIMEOUT, 120);
		curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($resource);

		curl_close($resource);

		if (!$result) {
			return false;
		}

		$obj = json_decode($result);

		return $obj;
	}

	/**
	 * Requires the EasySocial core library
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function engine()
	{
		$lib = JPATH_ROOT . '/administrator/components/com_easysocial/includes/easysocial.php';

		if (!JFile::exists($lib)) {
			return false;
		}

		require_once($lib);
	}

	/**
	 * Loads the previous version that was installed
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getInstalledVersion()
	{
		$this->engine();

		$path 	= JPATH_ADMINISTRATOR . '/components/com_easysocial/easysocial.xml';

		$parser	= FD::get( 'Parser' );
		$parser->load( $path );

		$version	= $parser->xpath( 'version' );
		$version	= (string) $version[ 0 ];

		return $version;
	}

	/**
	 * get a configuration item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getPreviousVersion($versionType)
	{
		$this->engine();

		$config = ES::table('Config');
		$config->load(array('type' => $versionType));

		return $config->value;
	}

	/**
	 * Determines if we are in development mode
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function isDevelopment()
	{
		$session = JFactory::getSession();
		$developer = $session->get('easysocial.developer');

		return $developer;
	}

	/**
	 * Verifies the api key
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function verifyApiKey($key)
	{
		$post = array('apikey' => $key, 'product' => 'easysocial');
		$resource = curl_init();

		curl_setopt($resource, CURLOPT_URL, ES_VERIFIER);
		curl_setopt($resource, CURLOPT_POST , true);
		curl_setopt($resource, CURLOPT_TIMEOUT, 120);
		curl_setopt($resource, CURLOPT_POSTFIELDS, $post);
		curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($resource);
		curl_close($resource);

		if (!$result) {
			return false;
		}

		$result = json_decode($result);

		return $result;
	}

	/**
	 * install verified column in user table if not exists
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function installUserVerifiedColumn()
	{
		$db = JFactory::getDBO();
		$column = 'verified';

		$query = "SHOW FIELDS FROM `#__social_users`";
		$db->setQuery($query);

		$rows = $db->loadObjectList();
		$fields	= array();

		foreach ($rows as $row) {
			$fields[] = $row->Field;
		}

		$columnExist = in_array($column, $fields);

		// if not exists, lets add this column.
		if (!$columnExist) {
			$query = "ALTER TABLE `#__social_users` ADD `verified` TINYINT(3) NOT NULL DEFAULT '0'";
			$db->setQuery($query);
			$db->query();
		}

		return true;
	}

	/**
	 * install social_params column in user table if not exists
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function installUserSocialParamColumn()
	{
		$db = JFactory::getDBO();
		$column = 'social_params';

		$query = "SHOW FIELDS FROM `#__social_users`";
		$db->setQuery($query);

		$rows = $db->loadObjectList();
		$fields	= array();

		foreach ($rows as $row) {
			$fields[] = $row->Field;
		}

		$columnExist = in_array($column, $fields);

		// if not exists, lets add this column.
		if (!$columnExist) {
			$query = "ALTER TABLE `#__social_users` ADD `social_params` LONGTEXT NOT NULL";
			$db->setQuery($query);
			$db->query();
		}

		return true;
	}
}
