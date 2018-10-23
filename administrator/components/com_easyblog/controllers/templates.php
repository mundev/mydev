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

class EasyBlogControllerTemplates extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('applyForm', 'saveForm');
	}

	/**
	 * Saves a post template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function saveForm()
	{
		// Check for request forgeries
		EB::checkToken();

		$this->checkAccess('blog');

		// Get any return urls
		$return = $this->input->get('return', '', 'default');
		$return = $return ? base64_decode($return) : 'index.php?option=com_easyblog&view=blogs&layout=templates';

		$id = $this->input->get('id', 0, 'int');

		$template = EB::table('PostTemplate');
		$template->load($id);

		$post = $this->input->post->getArray();

		// We need to save the thumbnail differently
		if (isset($post['screenshot'])) {
			unset($post['screenshot']);
		}

		$template->bind($post);
		
		// Save the template
		$template->store();

		// Get thumbnail
		$file = $this->input->files->get('screenshot', '');

		// Store thumbnail
		if (!empty($file['tmp_name'])) {
			$template->storeThumbnail($file);
		}		

		$this->info->set('COM_EASYBLOG_POST_TEMPLATE_SAVED_SUCCESS', 'success');

		$task = $this->getTask();

		if ($task == 'apply') {
			$return = 'index.php?option=com_easyblog&view=blogs&layout=editTemplate&id=' . $template->id;
		}

		return $this->app->redirect($return);
	}

	/**
	 * Save post templates
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function save()
	{
		EB::checkToken();
		EB::requireLogin();

		// We want to get the document data
		$document = $this->input->get('document', '', 'raw');
		$type = 'ebd';

		// Legacy templates
		if (!$document) {
			$document = $this->input->get('content', '', 'raw');
			$type = 'legacy';
		}

		$title = $this->input->get('template_title', '', 'default');

		// If there is a template id, we assume that the user want's to save the template
		$id = $this->input->get('template_id', 0, 'int');

		if (!$title) {

			$redirect = 'index.php?option=com_easyblog&view=templates&tmpl=component';

			if ($id) {
				$redirect .= '&layout=form&id=' . $id;
			}

			$data = array('data' => $document, 'doctype' => $type);

			EB::storeSession($data, 'EASYBLOG_COMPOSER_POST_TEMPLATES');

			$this->info->set('COM_EASYBLOG_SAVE_TEMPLATE_TITLE_EMPTY', 'error');
			return $this->app->redirect($redirect);
		}

		$template = EB::table('PostTemplate');
		$template->load($id);
		
		$template->title = $title;
		$template->user_id = $template->id ? $template->user_id : $this->my->id;
		$template->created = EB::date()->toSql();
		$template->system = $this->input->get('system', false, 'bool');
		$template->data = $document;

		// Joomla 4 compatibility:
		// To Ensure id column type is integer
		$template->system = (int) $template->system;
		$template->screenshot = '';

		$template->store();

		$message = 'COM_EASYBLOG_BLOG_TEMPLATE_SAVED_SUCCESS';

		if ($id) {
			$message = 'COM_EASYBLOG_BLOG_TEMPLATE_UPDATE_SUCCESS';
		}

		$this->info->set($message, 'success');

		$redirect = EBR::_('index.php?option=com_easyblog&view=blogs&layout=templates', false);

		return $this->app->redirect($redirect);
	}

	public function orderdown()
	{
		// Check for request forgeries
		EB::checkToken();

		$this->orderTemplates(1);
	}

	public function orderup()
	{
		// Check for request forgeries
		EB::checkToken();

		$this->orderTemplates(-1);
	}

	public function orderTemplates($direction)
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('blog');

		// Initialize variables
		$db = EB::db();
		$cid = $this->input->get('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$row = EB::table('PostTemplate');
			$row->load((int) $cid[0]);

			$row->move($direction);

			//now we need to update the ordering.
			$row->updateOrdering();
		}

		$this->app->redirect('index.php?option=com_easyblog&view=blogs&layout=templates');

	}
}