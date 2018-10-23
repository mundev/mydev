<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

if (EB::isJoomla40()) {
	class EBStringBase extends Joomla\String\StringHelper
	{
	}
} else {
	class EBStringBase extends JString
	{
	}
}

class EBString extends EBStringBase
{
}

class EBFactory
{
	/**
	 * Returns a query variable by name.
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function getURI($requestPath = false)
	{
		$uri = JUri::getInstance();

		// Gets the full request path.
		if ($requestPath) {
			$uri = $uri->toString(array('path', 'query'));
		}

		return $uri;
	}

	/**
	 * Render Joomla editor.
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function getEditor($editorType = null)
	{
		if (!$editorType) {
			
			$config = EB::config();
			$jConfig = EB::jConfig();

			// If use system editor, we should check if the configured editor exists or enabled.
			$editorType = $config->get('layout_editor');

			// if use build-in composer, we should check from the global configuration setting
			if ($editorType == 'composer') {
				$editorType = $jConfig->get('editor');
			}			
		}

		if (EB::isJoomla40()) {
			$editor = Joomla\CMS\Editor\Editor::getInstance($editorType);
		} else {
			$editor = JFactory::getEditor($editorType);

			if ($editorType == 'none') {
				JHtml::_('behavior.core');	
			}
		}

		return $editor;
	}
}

class EBUserModel
{
	/**
	 * Load joomla's user forms
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function loadUserModel()
	{
		if (EB::isJoomla31()) {
			require_once(JPATH_ADMINISTRATOR . '/components/com_users/models/user.php');
			$userModel = new UsersModelUser();
		}

		if (EB::isJoomla40()) {
			$userModel = new Joomla\Component\Users\Administrator\Model\UserModel();
		}

		return $userModel;
	}
}

class EBArrayHelper
{
	/**
	 * Utility function to map an object to an array
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function fromObject($data)
	 {
		if (EB::isJoomla31()) {
			$data = JArrayHelper::fromObject($data);
		}

		if (EB::isJoomla40()) {
			$data = Joomla\Utilities\ArrayHelper::fromObject($data);
		}

		return $data;
	 }

	/**
	 * Utility function to return a value from a named array or a specified default
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function getValue($array, $name, $default = null, $type = '')
	{
		if (EB::isJoomla31()) {
			$data = JArrayHelper::getValue($array, $name, $default, $type);
		}

		if (EB::isJoomla40()) {
			$data = Joomla\Utilities\ArrayHelper::getValue($array, $name, $default, $type);
		}

		return $data;
	}
}

class EBRouter
{
	/**
	 * Determine whether the site enable SEF.
	 *
	 * @since   5.2
	 * @access  public
	 */
	public static function getMode()
	{
		$jConfig = EB::jConfig();

		$app = JFactory::getApplication();

		if ($app->isAdmin()) {
			$isSef = false;
		}

		if ($app->isSite()) {
			$isSef = $jConfig->get('sef');
		}

		return $isSef;
	}
}
