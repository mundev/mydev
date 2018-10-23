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

class SocialFieldsUserJoomlaUsernameHelper
{
	/**
	 * Determines if the username is allowed
	 *
	 * @since   1.0
	 * @access  public
	 */
	public static function allowed($username, &$params, $current = '')
	{
		// Exception for current
		if (!empty($current) && $username === $current) {
			return true;
		}

		$disallowed = trim($params->get('disallowed', ''));

		// If nothing is defined as allowed
		if (empty($disallowed)) {
			return true;
		}

		$disallowed = JString::strtoupper($disallowed);
		$disallowed = ES::makeArray($disallowed, ',');

		if (empty($disallowed)) {
			return true;
		}

		// Standardize case sensitivity
		if (!in_array(JString::strtoupper($username), $disallowed)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if a username already exist in the system.
	 *
	 * @since   1.0
	 * @access  public
	 */
	public static function exists($username, $current = '')
	{
		if (!empty($current) && $username === $current) {
			return false;
		}

		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__users')
			->where('username', $username);

		$db->setQuery($sql->getTotalSql());
		$exists = $db->loadResult();

		return (bool) $exists;
	}

	/**
	 * Validates a username for proper syntax.
	 *
	 * @since   1.0
	 * @access  public
	 */
	public static function isValid($username, $params)
	{
		// Somehow, Joomla allows spaces back in username again
		// This regex pattern is retrieved from /libraries/joomla/table/user.php check()
		$pattern = '#[<>"\'%;()&\\\\]|\\.\\./#';

		if (empty($username) || preg_match($pattern, $username)) {
			return false;
		}

		if ($params->get('regex_validate')) {
			$format = $params->get('regex_format');
			$modifier = $params->get('regex_modifier');

			// Reformat the modifier to ensure that it is valid
			$modifier = self::formatModifier($modifier);

			$pattern = '/' . $format . '/' . $modifier;
			$result = preg_match($pattern, $username);

			if (empty($result)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Ensure that the modifier is valid
	 *
	 * @since	2.1.10
	 * @access	public
	 */
	public static function formatModifier($modifier)
	{
		// Known modifier for preg_match,
		// http://php.net/manual/en/reference.pcre.pattern.modifiers.php
		$knownModifier = array(
				'A' => 'A',
				'D' => 'D',
				'i' => 'i',
				'm' => 'm',
				's' => 's',
				'S' => 'S',
				'u' => 'u',
				'U' => 'U',
				'x' => 'x',
				'X' => 'X'
		);

		if ($modifier) {
			$chars = str_split($modifier);
			$modifier = '';

			foreach ($chars as $char) {
				if (in_array($char, $knownModifier)) {
					$modifier .= $char;

					unset($knownModifier[$char]);
				}
			}
		}

		return $modifier;
	}
}
