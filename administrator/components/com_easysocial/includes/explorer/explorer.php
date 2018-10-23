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

class SocialExplorer extends EasySocial
{
	protected $uid = null;
	protected $type = null;
	protected $storage = null;
	protected $adapter = null;


	public function __construct($uid, $type)
	{
		parent::__construct();

		$this->uid = $uid;
		$this->type = $type;

		require_once(__DIR__ . '/hooks/' . $this->type . '.php');

		$class = 'SocialExplorerHook' . ucfirst($this->type);
		$this->adapter = new $class($this->uid, $this->type);
	}

	public static function getInstance($uid, $type)
	{
		static $instance = null;

		if (!$instance) {
			$instance = new self( $uid , $type );
		}

		return $instance;
	}

	/**
	 * Processes the hook
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function hook($hook)
	{
		$result = $this->adapter->$hook();

		return $result;
	}

	/**
	 * Renders the html output for explorer
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function render($url, $options=array())
	{
		// Get the cluster id
		$cluster = ES::cluster($this->type, $this->uid);

		$uuid  = uniqid();
		$theme = ES::themes();

		// Standard options
		$showUse = $this->normalize($options, 'showUse', true);
		$showClose = $this->normalize($options, 'showClose', true);
		$showUpload = false;

		// We need to normalize the uploadlimit
		if (isset($options['uploadLimit'])) {
			$options['uploadLimit'] = (int) $options['uploadLimit'];
		}

		if (!isset($options['allowUpload']) || $options['allowUpload']) {
			$showUpload = true;
		}

		// Set default extensions. #1485
		if (!isset($options['allowedExtensions'])) {
			$options['allowedExtensions'] = 'zip,txt,pdf,gz,php,doc,docx,ppt,xls,jpg,png,gif';
		}

		$theme->set('options', $options);
		$theme->set('uuid', $uuid);
		$theme->set('uid', $this->uid);
		$theme->set('type', $this->type);
		$theme->set('url', $url);
		$theme->set('showUse', $showUse);
		$theme->set('showClose', $showClose);
		$theme->set('showUpload', $showUpload);
		$theme->set('cluster', $cluster);

		$html = $theme->output('site/explorer/default');

		return $html;
	}
}
