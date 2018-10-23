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

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerBlocks extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
		$this->registerTask('apply', 'save');
	}

	/**
	 * Saves the block settings
	 *
	 * @since	5.2.6
	 * @access	public
	 */
	public function save()
	{
		$id = $this->input->get('id', 0, 'int');

		$table = EB::table('Block');
		$table->load($id);

		$published = $this->input->get('published', '', 'bool');

		// System block published state cannot be change
		if ($table->id && $table->published == EASYBLOG_COMPOSER_BLOCKS_NOT_VISIBLE) {
			$published = EASYBLOG_COMPOSER_BLOCKS_NOT_VISIBLE;
		}

		$table->published = $published;

		$params = $this->input->get('params', '', 'array');

		$table->params = json_encode($params);

		$table->store();

		$task = $this->getTask();

		$message = 'Block saved successfully';
		$redirect = 'index.php?option=com_easyblog&view=blocks';

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_easyblog&view=blocks&layout=form&id=' . $table->id;
		}

		$this->info->set($message, 'success');
		return $this->app->redirect($redirect);
	}

	/**
	 * Toggles the publishing state of a block
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function togglePublish()
	{
		// Check for request forgeries
		EB::checkToken();

		// Default redirection url
		$redirect = 'index.php?option=com_easyblog&view=blocks';

		// Get the items to be published / unpublished
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_BLOCKS_INVALID_ID_PROVIDED', 'error');
			return $this->app->redirect($redirect);
		}

		// Get the current task
		$task = $this->getTask();

		foreach ($ids as $id) {
			$block = EB::table('Block');
			$block->load((int) $id);

			$block->$task();
		}

		$message = 'COM_EASYBLOG_BLOCKS_PUBLISHED_SUCCESSFULLY';

		if ($task == 'unpublish') {
			$message = 'COM_EASYBLOG_BLOCKS_UNPUBLISHED_SUCCESSFULLY';
		}

		$this->info->set(JText::_($message), 'success');
		return $this->app->redirect($redirect);
	}

	/**
	 * Process blocks installation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function upload()
	{
		EB::checkToken();

		$redirect = 'index.php?option=com_easyblog&view=blocks&layout=install';

		// Get the zip file
		$file = $this->input->files->get('package', array(), 'raw');

		$blocks = EB::blocks();
		$state = $blocks->install($file);

		if (!$state || $state === false) {
			$this->info->set($blocks->getError(), 'error');
		} else {
			$this->info->set(JText::_('COM_EASYBLOG_BLOCKS_INSTALL_SUCCESS'), 'success');
		}

		return $this->app->redirect($redirect);
	}
}
