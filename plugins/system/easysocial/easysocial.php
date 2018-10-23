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

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/plugins.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

class PlgSystemEasySocial extends EasySocialPlugins
{
	/**
	 * Triggered upon Joomla application initialization
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function onAfterInitialise()
	{
		// We only process on the front end.
		if ($this->app->isAdmin()) {
			return;
		}

		return true;
	}

	/**
	 * Executes before the router
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onAfterRoute()
	{
		// We only process on the front end.
		if ($this->app->isAdmin()) {
			return;
		}

		// If the site is offline, just use Joomla login.
		if ($this->jConfig->getValue('offline')) {
			return;
		}

		$doc = JFactory::getDocument();

		// Set the current url for non registration pages so that we can respect the profile type settings registration_success
		// and later redirect the user after their successful signup.
		$view = $this->input->get('view', '', 'cmd');
		$task = $this->input->get('task', '', 'cmd');

		if ($doc->getType() == 'html' && !$this->my->id && $view != 'registration' && $view != 'login' && $task != 'saveStep') {
			$currentUri = JRequest::getUri();
			$currentUri = base64_encode($currentUri);


			$session = JFactory::getSession();
			$session->set('easysocial.before_registration', $currentUri, SOCIAL_SESSION_NAMESPACE);
		}

		// Process redirection
		$this->processUsersRedirection();
	}

	/**
	 * Redirects users view to easysocial
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function processUsersRedirection()
	{
		$cron = $this->input->get('cron', false, 'bool');
		$doc = JFactory::getDocument();

		if ($cron) {
			return;
		}

		if ($doc->getType() != 'html') {
			return;
		}

		// Check if the admin wants to enable this
		if (!$this->params->get('redirection', false)) {
			return;
		}

		// If this is registration from com_users, redirect to the appropriate page.
		if ($this->isUserRegistration()) {
			$url = ESR::registration(array(), false);

			return $this->app->redirect($url);
		}

		// If this is username reminder, redirect to the appropriate page.
		if ($this->isUserRemind()) {
			$url = ESR::account(array('layout' => 'forgetUsername'), false);

			return $this->app->redirect($url);
		}

		// If this is password reset, redirect to the appropriate page.
		if ($this->isUserReset()) {
			$url = ESR::account(array('layout' => 'forgetPassword'), false);

			return $this->app->redirect($url);
		}

		// If this is logout, we should respect this.
		if ($this->isUserLogout()) {
			return;
		}

		// If this is password reset, redirect to the appropriate page.
		if ($this->isUserLogin()) {

			$return = $this->app->input->get('return', '', 'default');

			if ($return) {
				ES::setCallback(base64_decode($return));
			}

			// Redirect to EasySocial's registration
			$url = ESR::login(array(), false);

			return $this->app->redirect($url);
		}

		// If this is password reset, redirect to the appropriate page.
		if ($this->isUserProfile()) {
			$url = ESR::profile(array(), false);

			return $this->app->redirect($url);
		}
	}

	/**
	 * Determines if the current access is for profile
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function isUserProfile()
	{
		$option = $this->input->get('option', '', 'default');
		$view = $this->input->get('view', '', 'cmd');

		if ($option == 'com_users' && $view == 'profile') {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for logout
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function isUserLogout()
	{
		$option = $this->input->get('option', '', 'default');
		$view = $this->input->get('view', '', 'cmd');
		$layout = $this->input->get('layout', '', 'cmd');

		if ($option == 'com_users' && $view == 'login' && $layout == 'logout') {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for login
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function isUserLogin()
	{
		$option = $this->input->get('option', '', 'default');
		$view = $this->input->get('view', '', 'cmd');

		if ($option == 'com_users' && $view == 'login') {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for reset password
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function isUserReset()
	{
		$option = $this->input->get('option', '', 'default');
		$view = $this->input->get('view', '', 'cmd');

		if ($option == 'com_users' && $view == 'reset') {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for remind username
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function isUserRemind()
	{
		$option = $this->input->get('option', '', 'default');
		$view = $this->input->get('view', '', 'cmd');

		if ($option == 'com_users' && $view == 'remind') {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for registration
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function isUserRegistration()
	{
		$option = $this->input->get('option', '', 'default');
		$view = $this->input->get('view', '', 'cmd');

		if ($option == 'com_users' && $view == 'registration') {
			return true;
		}

		return false;
	}


	/**
	 * Method to handle any logout logic and report back to the subject.
	 *
	 * @param   array  $user     Holds the user data.
	 * @param   array  $options  Array holding options (client, ...).
	 *
	 * @return  boolean  Always returns true.
	 *
	 * @since   2.0
	 */
	public function onUserLogout($user, $options = array())
	{

		$pluginEnabled = JPluginHelper::isEnabled('system', 'languagefilter');
		$mode_sef = $this->app->get('sef', 0);
		$isMultilang = JLanguageMultilang::isEnabled();

		if ($this->app->isSite() && $mode_sef && $isMultilang && $pluginEnabled) {

			$menu = $this->app->getMenu();

			$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
			$params = new JRegistry();
			$params->loadString(empty($plugin) ? '' : $plugin->params);
			$langFilterAutomaticUrlChange = is_null($params) ? 'null' : $params->get('automatic_change', 'null');

			// Try to get association from the current active menu item
			$active = $menu->getActive();


			if ($active && $langFilterAutomaticUrlChange) {

				$config = ES::config();

				$logoutMenuItemId = $config->get('general.site.logout');

				// If lagout redirection is pointing to a menu item, we need to check if there is any menu item association.
				if ($logoutMenuItemId != 'null') {

					$default_lang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
					$assoc = JLanguageAssociations::isEnabled();

					$logoutMenuItem = $menu->getItem($logoutMenuItemId);

					$lang_code = $logoutMenuItem->language;
					$current_lang = $active->language;

					if (!$lang_code || $lang_code == '*') {
						$lang_code = $default_lang;
					}

					if (!$current_lang || $current_lang == '*') {
						$current_lang = JFactory::getLanguage()->getTag();
					}

					if ($assoc && $lang_code != $current_lang) {

						// // lets get the associated menu item.
						$associations = MenusHelper::getAssociations($logoutMenuItem->id);

						if (isset($associations[$current_lang]) && $menu->getItem($associations[$current_lang])) {
							$associationItemid = $associations[$current_lang];

							// reset the return url
							$returnUrl = 'index.php?Itemid=' . $associationItemid;
							$returnUrl = base64_encode($returnUrl);
							$this->app->input->set('return', $returnUrl);
						}

					}
				}
			}
		}

		return true;
	}


}
