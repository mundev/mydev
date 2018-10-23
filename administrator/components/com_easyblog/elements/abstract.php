<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

class EasyBlogFormField extends JFormField
{
	public function __construct()
	{
		EB::loadLanguages(JPATH_ADMINISTRATOR);

		// Load our own js library
		EB::init('admin');
		
		// Attach the admin's css
		$stylesheet = EB::stylesheet('admin', 'default');
		$stylesheet->attach();

		JHTML::_('behavior.modal');

		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->config = EB::config();
		$this->theme = EB::themes();
	}

	/**
	 * Proxy method to assist child elements to set variables to the theme
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function set($key, $value)
	{
		$this->theme->set($key, $value);
	}

	/**
	 * Proxy method to assist child elements to retrieve contents of a theme file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function output($namespace)
	{
		$contents = $this->theme->output($namespace);

		return $contents;
	}

    /**
     * Abstract method that should be implemented on child classes
     *
     * @since   5.1
     * @access  public
     */
    protected function getInput()
    {
    }
}
