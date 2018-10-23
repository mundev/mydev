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

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerModerate extends EasyBlogController
{
	/**
	 * Approves a blog post that is currently in moderation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function approve()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that user is logged in
		EB::requireLogin();

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Check if the user is privileged enough
		if (!$this->acl->get('add_entry') && !$this->acl->get('manage_pending') ) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NO_PERMISSION_TO_MODERATE_BLOG'));
		}

		// Load the draft
		$ids = $this->input->get('ids', array(), 'array');

		foreach ($ids as $id) {
			$post = EB::post($id);

			// Ensure that the current user is really allowed to reject this post
			if (!$post->canApprove()) {
				return JError::raiseError(500, JText::_('COM_EASYBLOG_NO_PERMISSION_TO_MODERATE_BLOG'));		
			}

			$post->approve();
		}

		$this->info->set('COM_EASYBLOG_MODERATE_BLOG_POSTS_APPROVED_SUCCESSFULLY', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Allows caller to reject posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function reject()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that user is logged in
		EB::requireLogin();

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');

		if ($this->getReturnURL()) {
			$return = $this->getReturnURL();
		}

		// Check if the user is privileged enough
		if (!$this->acl->get('add_entry') && !$this->acl->get('manage_pending') ) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NO_PERMISSION_TO_MODERATE_BLOG'));
		}

		// Get a list of ids
		$ids = $this->input->get('ids', array(), 'array');
		$message = $this->input->get('message', '', 'default');

		foreach ($ids as $id) {
			$post = EB::post($id);

			// Ensure that the current user is really allowed to reject this post
			if (!$post->canModerate()) {
				return JError::raiseError(500, JText::_('COM_EASYBLOG_NO_PERMISSION_TO_MODERATE_BLOG'));		
			}

			$post->reject($message);
		}

		$this->info->set('COM_EASYBLOG_BLOGS_BLOG_SAVE_REJECTED', 'success');

		return $this->app->redirect($return);
	}
}
