<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialKomentoHelper
{
	/**
	 * Determines if Komento exists on the site
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function exists()
	{
		jimport('joomla.filesystem.file');

		$file = JPATH_ADMINISTRATOR . '/components/com_komento/includes/komento.php';

		if (!JFile::exists($file)) {
			return false;
		}

		include_once($file);

		return true;
	}
}
