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

class EasyBlogDispatcher extends EasyBlog
{
	/**
	 * Dispatcher initialization as per joomla version
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		if (EB::isJoomla40()) {
			$this->dispatcher = new Joomla\Event\Dispatcher();
		}

		if (EB::isJoomla31()) {
			$this->dispatcher = JDispatcher::getInstance();
		}
	}

	public function trigger($eventName, $args = array())
	{
		if (EB::isJoomla40()) {
			return $this->dispatcher->triggerEvent($eventName, $args);
		}

		if (EB::isJoomla31()) {
			return $this->dispatcher->trigger($eventName, $args);
		}
	}
}