<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/abstract.php');

class EasyBlogBlockHandlerModule extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fa fa-cube';
	public $element = 'none';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;
		$meta->properties['textpanel'] = false;

		// Set the template for the module
		$theme = EB::template();
		$meta->preview = $theme->output('site/composer/blocks/handlers/module/preview');

		$installedModules = $this->getInstalledModules();

		$theme = EB::themes();
		$theme->set('installedModules', $installedModules);

		// HTML & Block
		$meta->html = $theme->output('site/composer/blocks/handlers/' . $this->type . '/html');

		return $meta;
	}

	public function data()
	{
		$data = (object) array();

		//for fieldset
		$data->installedModules = $this->getInstalledModules();

		return $data;
	}

	/**
	 * Retrieving available modules on the site.
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getInstalledModules()
	{
		$db = EB::db();

		$query = 'SELECT id, title, module, position FROM ' . $db->quoteName('#__modules')
				. ' WHERE ' . $db->nameQuote('published') . '=' . $db->Quote(1)
				. ' AND ' . $db->nameQuote('client_id') . '=' . $db->Quote(0);

		$db->setQuery($query);
		$modules = $db->loadObjectList();

		return $modules;
	}

	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function validate($block)
	{
		// if no url specified, return false.
		if (!isset($block->data->module) || !$block->data->module) {
			return false;
		}

		return true;
	}

	/**
	 * Displays the html output for a module preview block
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false)
	{
		// For RSS documents, we just want to display nothing
		$doc = JFactory::getDocument();

		if ($doc->getType() == 'feed') {
			return;
		}

		// If configured to display text only, nothing should appear at all for this block.
		if ($textOnly) {
			return;
		}

		// Need to ensure that we have the "source"
		if (!isset($block->data->module) || !$block->data->module) {
			return;
		}

		$template = EB::template();
		$template->set('data', $block->data);
		$contents = $template->output('site/blogs/blocks/module');

		return $contents;
	}
}
