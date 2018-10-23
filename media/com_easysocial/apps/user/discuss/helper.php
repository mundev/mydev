<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class SocialDiscussHelper extends SocialAppItem
{
	public static function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

		if(!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	public static function getPermalink($postId)
	{
		if (!self::exists()) {
			return;
		}

		return EDR::getPostRoute($postId);
	}

	public static function getCategoryPermalink($catId)
	{
		if (!self::exists()) {
			return;
		}

		return EDR::getCategoryRoute($catId); 	
	}
}
