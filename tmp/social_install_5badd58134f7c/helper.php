<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/apps/apps');

class TwitterAppHelper extends SocialAppItem
{
	public static function getClient($userId = null)
	{
		$table = self::getTwitterTable($userId);

		if (!$table->id) {
			return false;
		}
		
		$oauth = ES::oauth('twitter');	
		$oauth->setAccess($table->token, $table->secret);

		return $oauth;
	}

	public static function getTwitterTable($userId = null)
	{
		static $table = null;

		if (is_null($table)) {
			$my = ES::user($userId);

			// Get the user's oauth record
			$table = ES::table('OAuth');
			$table->load(array('uid' => $my->id, 'type' => SOCIAL_TYPE_USER, 'client' => 'twitter'));
		}

		return $table;
	}
}
