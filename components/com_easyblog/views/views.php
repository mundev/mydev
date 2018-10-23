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

jimport('joomla.application.component.view');
jimport('joomla.filesystem.folder');

class EasyBlogView extends JViewLegacy
{
	protected $app = null;
	protected $my = null;
	protected $customTheme = null;
	protected $props = array();
	public $paramsPrefix = 'listing';

	public function __construct()
	{
		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->my = JFactory::getUser();
		$this->config = EB::config();
		$this->info = EB::info();
		$this->jconfig = EB::jconfig();
		$this->acl = EB::acl();

		// If this is a dashboard theme, we need to let the theme object know
		$options = array('paramsPrefix' => $this->paramsPrefix);

		// If this is an ajax document, we should pass the $ajax library to the client
		if ($this->doc->getType() == 'ajax') {

			// We need to load frontend language from here incase it was called from backend.
			EB::loadLanguages();

			$this->ajax = EB::ajax();
		}

		// Create an instance of the theme so child can start setting variables to it.
		$this->theme = EB::template(null, $options);

		// Set the input object
		$this->input = EB::request();
	}

	/**
	 * Allows child to set variables
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function set($key, $value = '')
	{
		if ($this->doc->getType() == 'json') {
			$this->props[$key] = $value;

			return;
		}

		$this->theme->set($key, $value);
	}

	/**
	 * Allows children to check for acl
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function checkAcl($rule, $default = null)
	{
		$allowed = $this->acl->get($rule, $default);

		if (!$allowed) {
			JError::raiseError(500, JText::_('COM_EASYBLOG_NOT_ALLOWED_ACCESS_IN_THIS_SECTION'));
			return;
		}

		return true;
	}

	/**
	 * Responsible to render the css files on the head
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function renderHeaders()
	{
		// Load js stuffs
		$view = $this->input->get('view', '', 'cmd');

		// Determines which js section to initialize
		$section = 'site';

		if ($view == 'composer' || $view == 'templates') {
			$section = 'composer';
		}

		EB::init($section);

		// Get the theme on the site
		$theme = $this->config->get('theme_site');

		if ($this->customTheme) {
			$theme = $this->customTheme;
		}

		// Attach the theme's css
		$stylesheet = EB::stylesheet($section, $theme);

		// Allow caller to invoke recompiling of the entire css
		if ($this->input->get('compileCss') && EB::isSiteAdmin()) {
			$result = $stylesheet->build('full');

			header('Content-type: text/x-json; UTF-8');
			echo json_encode($result);
			exit;
		}

		$stylesheet->attach(true, true, $this->customTheme);

		// Render the custom styles
		$theme = EB::themes();
		$customCss = $theme->output('site/structure/css');

		// This custom css doesn't need to render on the composer page
		if ($view != 'composer') {
			// Compress custom css
			$customCss = EB::minifyCSS($customCss);

			$this->doc->addCustomTag($customCss);
		}
	}

	/**
	 * Allows caller to set a custom theme
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function setTheme($theme)
	{
		$this->customTheme = $theme;

		$this->theme->setCategoryTheme($theme);
	}

	/**
	 * Responsible to display the entire component output
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function display($tpl = null)
	{
		// Response for json calls
		if ($this->doc->getType() == 'json') {

			$callback = $this->input->get('callback', '', 'cmd');
			$output = json_encode($this->props);

			if ($callback) {
				$output = $callback . '(' . $output . ')';
			}

			header('Content-type: text/x-json; UTF-8');
			echo $output;
			exit;
		}

		// Standard html response
		if ($this->doc->getType() == 'html') {

			$this->renderHeaders();

			// Get the contents from the view
			$namespace  = 'site/' . $tpl;

			$contents = $this->theme->output($namespace);

			// Get menu suffix
			$suffix = $this->getMenuSuffix();

			// Get the current view.
			$view = $this->getName();

			// Get the current task
			$layout = $this->getLayout();

			// If this is a dashboard theme, we need to let the theme object know
			$options = array();

			if ($this->getName() == 'dashboard') {
				$options['dashboard'] = true;
			}

			// We need to append the contents back into the main structure
			$theme = EB::template(null, $options);

			$tmpl = $this->input->get('tmpl');

			// Get the toolbar
			$toolbar = '';
			$contributionHeader = false;

			// Render EasyBlog's toolbar
			if ($tmpl != 'component') {
				$toolbar = EB::toolbar()->html();
			}

			if ($view == 'entry' && $layout != 'preview') {

				$id = $this->input->get('id', 0, 'int');
				$post = EB::post($id);

				if (!$post->isStandardSource()) {
					$contribution = $post->getBlogContribution();

					$contributionHeader = $contribution->getHeader();

					if ($contributionHeader) {
						$toolbar = '';
					}
				}
			}

			// Get the theme name
			$themeName = $theme->getName();

			// Push notifications
			if (EB::push()->isEnabled()) {
				EB::push()->generateScripts();
			}

			// We attach the script tags on the bottom of the page
			$scripts = EB::helper('Scripts')->getScripts();

			// Jomsocial toolbar
			$jsToolbar = EB::jomsocial()->getToolbar();
			$theme->set('jsToolbar', $jsToolbar);

			$lang = JFactory::getLanguage();
			$rtl = $lang->isRTL();

			// Load easysocial headers when viewing posts of another person
			$miniheader = '';

			$showMiniHeader = $this->config->get('integrations_easysocial_miniheader');

			// Only work for Easysocial 2.0. Only display if there is no contribution header.
			if ($showMiniHeader && $view == 'entry' && EB::easysocial()->exists() && !EB::easysocial()->isLegacy() && !$contributionHeader && $layout != 'preview') {
				ES::initialize();

				if (!isset($post)) {
					$id = $this->input->get('id', 0, 'int');
					$post = EB::post($id);
				}

				$user = ES::user($post->getAuthor()->id);

				$miniheader = ES::themes()->html('html.miniheader', $user);
			}

			// For image popups and container
			$loadImageTemplates = $view == 'composer' ? false : true;

			$theme->set('loadImageTemplates', $loadImageTemplates);
			$theme->set('miniheader', $miniheader);
			$theme->set('rtl', $rtl);
			$theme->set('bootstrap', '');
			$theme->set('themeName', $themeName);
			$theme->set('jscripts', $scripts);
			$theme->set('toolbar', $toolbar);
			$theme->set('contents', $contents);
			$theme->set('suffix', $suffix);
			$theme->set('layout', $layout);
			$theme->set('view', $view);

			$output = $theme->output('site/structure/default');

			echo $output;
			return;
		}
	}

	/**
	 * Sets view in breadcrumbs
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function setViewBreadcrumb($view = null)
	{
		if (is_null($view)) {
			$view = $this->getName();
		}

		if (!EBR::isCurrentActiveMenu($view)) {
			$this->setPathway(JText::_('COM_EASYBLOG_BREADCRUMB_' . strtoupper($view)));

			return true;
		}

		return false;
	}

	/**
	 * Retrieve the menu suffix for a page
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getMenuSuffix()
	{
		$menu = $this->app->getMenu()->getActive();
		$suffix = '';

		if ($menu) {
			$suffix = $menu->params->get('pageclass_sfx', '');
		}

		return $suffix;
	}

	/**
	 * Generate a canonical tag on the header of the page
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function canonical($url, $route = true, $external = true)
	{
		if ($route) {
			$url = EBR::getRoutedUrl($url, true, $external);
		}

		$this->doc->addHeadLink($this->escape($url), 'canonical');
	}

	/**
	 * Generate a rel tag on the header of the page
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function amp($url, $route = true)
	{
		if ($route) {
			$url = EBR::_($url, false, null, false, true);
		}

		$this->doc->addHeadLink($this->escape($url), 'amphtml');
	}

	/**
	 * Retrieves the active menu
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getActiveMenu()
	{
		return $this->app->getMenu()->getActive();
	}

	/**
	 * Retrieve any queued messages from the system
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getMessages()
	{
		$messages = EB::getMessageQueue();

		return $messages;
	}

	/**
	 * Adds the breadcrumbs on the site
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function setPathway($title, $link ='')
	{
		// Get the pathway
		$pathway = $this->app->getPathway();

		// set this option to true if the breadcrumb didn't show the EasyBlog root menu.
		$showRootMenuItem = false;

		// Translate the pathway item
		$title = JText::_($title);
		$state = $pathway->addItem($title, $link);

		return $state;
	}

	/**
	 * Renders JSON output on the page
	 *
	 * @since	5.1
	 * @access	public
	 */
	protected function outputJSON($output = null)
	{
		echo '<script type="text/json" id="ajaxResponse">' . json_encode($output) . '</script>';
		exit;
	}

	/**
	 * Responsible to modify the title whenever necessary. Inherited classes should always use this method to set the title
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setPageTitle($title, $pagination = null , $addSitePrefix = false )
	{
		$page = null;

		if ($addSitePrefix) {
			$addTitle = $this->jconfig->get('sitename_pagetitles');
			$sitenameOrdering = $this->config->get('sitename_position', 'default');

			if ($sitenameOrdering == 'after' && $addTitle == 2) {
				// Only apply if the joomla site name setting is using 'after'
				$titleTmp = explode(" - ", $title);
				$title = $titleTmp[0] . ' - ' . JText::_($this->config->get('main_title')) . ' - ' . $titleTmp[1];
			} else {
				// Normal ordering
				$title .= ' - ' . JText::_($this->config->get('main_title'));
			}
		}

		if ($pagination && is_object($pagination)) {
			$page = $pagination->get('pages.current');

			// Append the current page if necessary.
			$title .= $page == 1 ? '' : ' - ' . JText::sprintf('COM_EASYBLOG_PAGE_NUMBER', $page);
		}

		$this->doc->setTitle($title);
	}

	/**
	 * Sets the rss author email
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getRssEmail($author)
	{
		if ($this->jconfig->get('feed_email') == 'none') {
			return;
		}

		if ($this->jconfig->get('feed_email') == 'author') {
			return $author->user->email;
		}

		return $this->jconfig->get('mailfrom');
	}
}
