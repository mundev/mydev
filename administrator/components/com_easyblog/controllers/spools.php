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

require_once(JPATH_COMPONENT . '/controller.php');

class EasyBlogControllerSpools extends EasyBlogController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Purges all emails from the system
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function purge()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('mail');

		$model = EB::model('Spools');
		$model->purge();

		$this->info->set(JText::_('COM_EASYBLOG_MAILS_PURGED'), 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=spools');
	}

	/**
	 * Purge Sent items
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function purgeSent()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl
		$this->checkAccess('mail');

		$model = EB::model('Spools');
		$model->purge('sent');

		$this->info->set('COM_EASYBLOG_SENT_MAILS_PURGED', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=spools');
	}

	/**
	 * Deletes a mailer item
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('mail');

		$mails = $this->input->get('cid', array(), 'array');

		if (!$mails) {
			$message = JText::_('COM_EASYBLOG_NO_MAIL_ID_PROVIDED');

			$this->info->set($message, 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=spools');
		}

		foreach ($mails as $id) {
			$table = EB::table('MailQueue');
			$table->load((int) $id);

			$table->delete();
		}

		$this->info->set('COM_EASYBLOG_SPOOLS_DELETE_SUCCESS', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=spools');
	}

	/**
	 * Resets a list of email template files to it's original state
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function reset()
	{
		// Check for request forgeries
		EB::checkToken();

		$files = $this->input->get('cid', array(), 'default');
		$files = EB::makeArray($files);

		if (!$files) {
			// return $this->view->exception('COM_EASYBLOG_EMAIL_INVALID_ID_PROVIDED');

			$this->info->set('COM_EASYBLOG_EMAIL_INVALID_ID_PROVIDED', 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=spools&layout=editor');				
		}

		$model = EB::model("Spools");

		foreach ($files as $file) {

			$file = base64_decode($file);
			$path = $model->getOverrideFolder($file);

			JFile::delete($path);
		}

		$this->info->set('COM_EASYBLOG_EMAIL_DELETED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=spools&layout=editor');				
	}

	/**
	 * Store email template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function saveFile()
	{
		// Check for request forgeries
		EB::checkToken();

		// Get the contents of the email template
		$contents = $this->input->get('source', '', 'raw');
		
		$file = $this->input->get('file', '', 'default');
		$file = base64_decode($file);

		// Get the overriden path
		$model = EB::model("Spools");
		$path = $model->getOverrideFolder($file);

		$model->write($path, $contents);

		$this->info->set('COM_EASYBLOG_EMAILS_TEMPLATE_FILE_SAVED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=spools&layout=editor');		
	}
}
