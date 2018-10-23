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

class EasyBlogControllerBlogs extends EasyBlogController
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask( 'saveApply' , 'savePublish' );

		// Need to explicitly define this in Joomla 3.0
		$this->registerTask( 'unpublish' , 'unpublish' );

		// Restoring a blog post is the same as publishing it
		$this->registerTask('restore', 'publish');

		// Need to explicitly define trash
		$this->registerTask( 'trash' , 'trash' );

		// Register lock / unlock
		$this->registerTask('lock', 'toggleLock');
		$this->registerTask('unlock', 'toggleLock');

		// Featuring / Unfeaturing
		$this->registerTask('unfeature', 'toggleFeatured');
		$this->registerTask('feature', 'toggleFeatured');

		// Toggling frontpage
		$this->registerTask('setFrontpage', 'toggleFrontpage');
		$this->registerTask('removeFrontpage', 'toggleFrontpage');

		// Toggle global template
		$this->registerTask('setGlobalTemplate', 'toggleGlobalTemplate');
		$this->registerTask('removeGlobalTemplate', 'toggleGlobalTemplate');

		// Toggle publish
		$this->registerTask('publishTemplate', 'toggleStateTemplate');
		$this->registerTask('unpublishTemplate', 'toggleStateTemplate');

		$this->registerTask('changeAuthor', 'changeAuthor');
	}

	/**
	 * Allows caller to empty the trashed posts
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function emptyTrash()
	{
		EB::checkToken();

		$this->checkAccess('blog');

		$model = EB::model('Blogs');
		$model->emptyTrash();

		$this->info->set(JText::_('COM_EASYBLOG_BLOGS_TRASHED_EMPTIED'), 'success');

		$return = 'index.php?option=com_easyblog&view=blogs';

		return $this->app->redirect($return);
	}

	/**
	 * Archives a blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function archive()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check access for blog
		$this->checkAccess('blog');

		// Get the id's
		$ids = $this->input->get('cid', array(), 'array');

		$return = 'index.php?option=com_easyblog&view=blogs';

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_BLOG_ID'), 'error');
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);
			$post->archive();
		}

		$this->info->set(JText::_('COM_EASYBLOG_BLOGS_ARCHIVED_SUCCESSFULLY'), 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Unarchives a blog post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function unarchive()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check access for blog
		$this->checkAccess('blog');

		// Get the id's
		$ids = $this->input->get('cid', array(), 'array');

		$return = 'index.php?option=com_easyblog&view=blogs';

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_BLOG_ID'), 'error');
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);
			$post->unarchive();
		}

		$this->info->set(JText::_('COM_EASYBLOG_BLOGS_UNARCHIVED_SUCCESSFULLY'), 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Ability to lock a blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function toggleLock()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check access for blog
		$this->checkAccess('blog');

		// It could be lock / unlock
		$task = $this->getTask();

		$ids = $this->input->get('cid', '', 'array');

		foreach ($ids as $id) {

			$post = EB::post($id);

			// Lock the blog post
			if ($task == 'lock') {
				$post->lock();
			} else {
				$post->unlock();
			}
		}

		$msg = $task == 'lock' ? 'COM_EASYBLOG_BLOGS_LOCKED_SUCCESSFULLY' : 'COM_EASYBLOG_BLOGS_UNLOCKED_SUCCESSFULLY';

		$this->info->set($msg, 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
	}

	/**
	 * Allows caller to autopost to social network sites
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function autopost()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl rules.
		$this->checkAccess('blog');

		// Get the autoposting type
		$type = $this->input->get('type', '', 'cmd');
		$ids = $this->input->get('cid', array(), 'cid');

		// Load the oauth library
		$oauth = EB::table('OAuth');
		$oauth->load(array('system' => 1, 'type' => $type));

		// Default return url
		$return = 'index.php?option=com_easyblog&view=blogs';

		if (!$oauth->id) {
			$this->info->set('COM_EASYBLOG_AUTOPOST_UNABLE_TO_LOAD_TYPE', 'error');
			return $this->app->redirect($return);
		}

		// Ensure that they are enabled
		if (!$this->config->get('integrations_' . $oauth->type)) {
			$this->info->set(JText::sprintf('COM_EASYBLOG_AUTOPOST_SITE_IS_NOT_ENABLED', ucfirst($type)), 'error');
			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$post = EB::post($id);

			if ($oauth->isShared($post->id)) {
				$this->info->set(JText::sprintf('COM_EB_AUTOPOST_HAS_BEEN_SHARED', $post->id, ucfirst($oauth->type)), 'info');
				return $this->app->redirect($return);
			}

			// Check the author's acl
			$acl = EB::acl($post->created_by);
			$rule = 'update_' . $oauth->type;

			if (!$acl->get($rule) && !EB::isSiteAdmin($post->created_by)) {
				$this->info->set(JText::sprintf('COM_EB_AUTOPOST_AUTHOR_NO_PERMISSION', $post->id, ucfirst($oauth->type)), 'error');
				return $this->app->redirect($return);
			}
			
			$post->autopost($oauth->type, true);
		}

		$this->info->set(JText::sprintf('COM_EASYBLOG_AUTOPOST_SUBMIT_SUCCESS', ucfirst($oauth->type)), 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Toggles the front page status
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function toggleFrontpage()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl rules.
		$this->checkAccess('blog');

		// Get the list of id's
		$ids = $this->input->get('cid', array(), 'array');

		// Default redirect url
		$return = 'index.php?option=com_easyblog&view=blogs';

		foreach ($ids as $id) {
			$post = EB::post($id);

			$task = $this->getTask();

			if ($post->frontpage) {
				$post->removeFrontpage();
				$message = JText::sprintf('COM_EASYBLOG_BLOGS_REMOVED_FROM_FRONTPAGE_SUCCESS', $blog->title);
			} else {
				$post->setFrontpage();
				$message = JText::sprintf('COM_EASYBLOG_BLOGS_SET_AS_FRONTPAGE_SUCCESS', $blog->title);
			}
		}

		$this->info->set($message, 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Toggles the featured status of the blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function toggleFeatured()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl rules.
		$this->checkAccess('blog');

		// Get the list of items to toggle
		$ids = $this->input->get('cid', array(), 'default');
		$task = $this->getTask();

		if (empty($ids)) {
			EB::info()->set(JText::_('COM_EASYBLOG_BLOGS_INVALID_ID'), 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
		}

		foreach ($ids as $id) {

			$post = EB::post($id);

			if ($task == 'unfeature') {
				$post->removeFeatured();
			}

			if ($task == 'feature') {
				$post->setFeatured();
			}
		}

		$message = JText::_('COM_EASYBLOG_BLOGS_FEATURED_SUCCESSFULLY');

		if ($task == 'unfeature') {
			$message = JText::_('COM_EASYBLOG_BLOGS_UNFEATURED_SUCCESSFULLY');
		}

		EB::info()->set($message, 'success');

		$this->app->redirect('index.php?option=com_easyblog&view=blogs');
	}

	/**
	 * Re-sends notification for a specific blog post
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function notify()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('blog');

		$return = 'index.php?option=com_easyblog&view=blogs';

		// Get the blog post id
		$id = $this->input->get('id', 0, 'int');

		$post = EB::post($id);

		if (!$id || !$post->id) {
			$this->info->set(JText::_('COM_EASYBLOG_BLOGS_INVALID_ID'), 'error');
			return $this->app->redirect($return);
		}

		// Notify users
		$post->notify();

		$message = JText::_('COM_EASYBLOG_BLOGS_NOTIFY_SUBSCRIBERS');

		$this->info->set($message, 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Trashes blog posts from the site.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function trash()
	{
		// Check for request forgeries
		EB::checkToken();

		// Default redirection url
		$return = 'index.php?option=com_easyblog&view=blogs';

		// Check for acl rules.
		$this->checkAccess('blog');

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_BLOGS_INVALID_ID', 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);
			$post->trash();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_TRASHED', 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Deletes a post template from the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deletePostTemplates()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl access
		$this->checkAccess('blog');

		$return = 'index.php?option=com_easyblog&view=blogs&layout=templates';

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_INVALID_BLOG_ID', 'error');

			return $this->app->redirect($return);
		}


		foreach ($ids as $id) {
			$template = EB::table('PostTemplate');
			$template->load($id);

			if ($template->isCore() || $template->isBlank()) {
				$this->info->set('COM_EASYBLOG_POST_TEMPLATES_DELETED_CORE_FAILED', 'error');
				return $this->app->redirect($return);
			}

			$template->delete();
		}

		$this->info->set('COM_EASYBLOG_POST_TEMPLATES_DELETED_SUCCESSFULLY', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Deletes a blog post from the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function remove()
	{
		// Check for request forgeries
		EB::checkToken();

		// Check for acl access
		$this->checkAccess('blog');

		$return = 'index.php?option=com_easyblog&view=blogs&filter_state=T';

		// Get list of blog post id's.
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_INVALID_BLOG_ID', 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);
			$post->delete();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_DELETED_SUCCESSFULLY', 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Publishes blog posts
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function publish()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('blog');

		// Get a list of blog id's
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_BLOG_ID'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
		}

		// Get the model
		$model = EB::model('Blogs');

		// We should notify if the blog post is under moderation
		foreach ($ids as $id) {
			$post = EB::post($id);

			$post->publish();
		}

		$message = JText::_('COM_EASYBLOG_BLOGS_PUBLISHED_SUCCESSFULLY');

		if ($this->getTask() == 'restore') {
			$message = JText::_('COM_EASYBLOG_BLOGS_RESTORED_SUCCESSFULLY');
		}

		$this->info->set($message, 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
	}

	/**
	 * Unpublishes a blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unpublish()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('blog');

		// Get any return urls
		$return = $this->input->get('return', '', 'default');
		$return = $return ? base64_decode($return) : 'index.php?option=com_easyblog&view=blogs';

		// Get the list of blog ids
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_INVALID_BLOG_ID', 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$post = EB::post($id);

			$post->unpublish();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_UNPUBLISHED_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
	}

	/**
	 * Toggles the global template status
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toggleGlobalTemplate()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that the user really has access to this section
		$this->checkAccess('blog');

		// Default redirection
		$return = 'index.php?option=com_easyblog&view=blogs&layout=templates';

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_BLOG_ID'), 'error');

			return $this->app->redirect($return);
		}

		$system = $this->getTask() == 'setGlobalTemplate' ? true : false;

		foreach ($ids as $id) {
			$template = EB::table('PostTemplate');
			$template->load($id);

			$template->system = $system;

			$template->store();
		}

		$message = JText::_('COM_EASYBLOG_POST_TEMPLATES_SUCCESSFULLY_SET_AS_GLOBAL_TEMPLATE');

		if ($task == 'removeGlobalTemplate') {
			$message = JText::_('COM_EASYBLOG_POST_TEMPLATES_SUCCESSFULLY_REMOVED_FROM_GLOBAL_TEMPLATE');
		}

		$this->info->set($message, 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Duplicate the post template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function copyTemplate()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that the user really has access to this section
		$this->checkAccess('blog');

		// Default redirection
		$return = 'index.php?option=com_easyblog&view=blogs&layout=templates';

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$template = EB::table('PostTemplate');
			$template->load($id);
			$template->duplicate();
		}


		$message = JText::_('COM_EASYBLOG_POST_TEMPLATES_SUCCESSFULLY_DUPLICATED');

		$this->info->set($message, 'success');
		return $this->app->redirect($return);

	}



	/**
	 * Toggles the template publishing state
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toggleStateTemplate()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that the user really has access to this section
		$this->checkAccess('blog');

		// Default redirection
		$return = 'index.php?option=com_easyblog&view=blogs&layout=templates';

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_EASYBLOG_INVALID_ID_PROVIDED'), 'error');

			return $this->app->redirect($return);
		}

		$task = $this->getTask();

		$published = $task == 'publishTemplate' ? 1 : 0;

		foreach ($ids as $id) {
			$template = EB::table('PostTemplate');
			$template->load($id);

			$template->published = $published;

			$template->store();
		}

		$message = JText::_('COM_EASYBLOG_POST_TEMPLATES_SUCCESSFULLY_PUBLISHED');

		if ($task == 'unpublishTemplate') {
			$message = JText::_('COM_EASYBLOG_POST_TEMPLATES_SUCCESSFULLY_UNPUBLISHED');
		}

		$this->info->set($message, 'success');
		return $this->app->redirect($return);
	}

	/**
	 * Moves blog post into category
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function move()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('blog');

		// Default redirection
		$return = 'index.php?option=com_easyblog&view=blogs';

		// Get list of blog posts
		$ids = $this->input->get('cid', array(), 'array');

		// Get the new category to move to
		$newCategory = $this->input->get('move_category_id', 0, 'int');

		if (!$ids || !$newCategory){
			$this->info->set(JText::_('COM_EASYBLOG_BLOGS_MOVED_ERROR'), 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$post = EB::post($id);
			$post->move($newCategory);
		}

		$this->info->set(JText::sprintf('COM_EASYBLOG_BLOGS_MOVED_SUCCESSFULLY', count($ids)), 'success');

		return $this->app->redirect($return);
	}

	/**
	 * Duplicates a blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function copy()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('blog');

		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info()->set(JText::_('COM_EASYBLOG_BLOGS_COPY_ERROR'), 'error');

			return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
		}

		foreach ($ids as $id) {
			$post = EB::post($id);

			$post->duplicate();
		}

		$this->info->set(JText::sprintf('COM_EASYBLOG_BLOGS_COPIED_SUCCESSFULLY', count($ids)), 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
	}


	/**
	 * Mass author change.
	 *
	 * @since	5.0.17
	 * @access	public
	 */
	public function changeAuthor()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('blog');

		$ids = $this->input->get('cid', array(), 'array');
		$authorId = $this->input->get('move_author_id', 0, 'int');

		if (!$ids || !$authorId) {
			$this->info()->set(JText::_('COM_EASYBLOG_BLOGS_CHANGE_AUTHOR_ERROR'), 'error');
			return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
		}

		foreach ($ids as $id) {
			$post = EB::post($id);
			$post->reassignAuthor($authorId);
		}

		$this->info->set(JText::_('COM_EASYBLOG_BLOGS_CHANGE_AUTHOR_SUCCESSFULLY'), 'success');
		return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
	}

	/**
	 * Reset post hits
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function resetHits()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('blog');

		// Get any return urls
		$return = $this->input->get('return', '', 'default');
		$return = $return ? base64_decode($return) : 'index.php?option=com_easyblog&view=blogs';

		// Get the list of blog ids
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_INVALID_BLOG_ID', 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$post = EB::post($id);

			$post->resetHits();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_RESET_HITS_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
	}


	/**
	 * Reset post ratings
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function resetRatings()
	{
		// Check for request forgeries
		EB::checkToken();

		// @task: Check for acl rules.
		$this->checkAccess('blog');

		// Get any return urls
		$return = $this->input->get('return', '', 'default');
		$return = $return ? base64_decode($return) : 'index.php?option=com_easyblog&view=blogs';

		// Get the list of blog ids
		$ids = $this->input->get('cid', array(), 'array');

		if (!$ids) {
			$this->info->set('COM_EASYBLOG_INVALID_BLOG_ID', 'error');

			return $this->app->redirect($return);
		}

		foreach ($ids as $id) {
			$post = EB::post($id);

			$post->deleteRatings();
		}

		$this->info->set('COM_EASYBLOG_BLOGS_RESET_RATINGS_SUCCESSFULLY', 'success');

		return $this->app->redirect('index.php?option=com_easyblog&view=blogs');
	}

	/**
	 * Remove override post templates image
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteTemplateThumbnails()
	{
		$id = $this->input->get('id', 0, 'int');

		// Load template
		$template = EB::table('PostTemplate');
		$template->load($id);

		if (!$template->id) {
			return $this->ajax->reject();
		}

		// Remove thumbnails
		$template->removeOverrideThumbnails();

		return $this->ajax->resolve();
	}

	/**
	 * Allows tagging suggestion
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function suggest()
	{
		// Check for request forgeries
		EB::checkToken();

		$inputName = $this->input->get('inputName', '', 'default');
		$keyword = $this->input->get('search', '', 'default');
		$limit = 10;

		$model = EB::model('Tags');
		$results = $model->suggest($keyword, $limit);

		if (!$results) {
			return $this->ajax->resolve(array());
		}

		$suggestions = array();		

		foreach ($results as $tag) {

			$title = $tag->title;
			$tagId = $tag->id;

			$template = EB::template();
			$template->set('title', $title);
			$template->set('tagId', $tagId);
			$template->set('inputName', $inputName);

			$suggestions[] = $template->output('admin/tags/suggest/item');
		}

		return $this->ajax->resolve($suggestions);
	}

	/**
	 * Mass assign tags for the blog
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function massAssignTags()
	{
		// check for request forgeries
		EB::checkToken();

		// Check for acl rules.
		$this->checkAccess('blog');

		// Default redirection
		$return = 'index.php?option=com_easyblog&view=blogs';

		// Get list of blog posts
		$postIds = $this->input->get('cid', array(), 'array');

		// Get the new tags to assign into the selected posts
		$tags = $this->input->get('mass_assign_tags', '', 'default');

		// check for the post id and tag id exist or not 
		if (!$postIds || !$tags) {
			$this->info->set('COM_EB_MASS_ASSIGN_TAGS_ERROR', 'error');
			return $this->app->redirect($return);
		}

		// make it as array
		$tags = explode(',', $tags);

		// Ensure that the tags are unique
		$tags = array_unique($tags);

		foreach ($postIds as $postId) {

			foreach ($tags as $tag) {

				$table = EB::table('Tag');
				$exists = $table->exists($tag, true);

				// if the selected tag already exist, just assign to the associated post.
				if ($exists) {
					$table->load($tag, true);

				} else {
					$table->created_by = $this->my->id;
					$table->title = $tag;
					$table->created = EB::date()->toSql();
					$table->published = true;
					$table->status = 0;

					// For now temporary store it as all language
					$table->language = '*';

					$state = $table->store();

					if (!$state) {
						$this->info->set('COM_EB_MASS_ASSIGN_TAGS_ERROR', 'error');
						return $this->app->redirect($return);
					}					
				}

				$postTagModel = EB::model('PostTag');

				// Add the association tags for these blog posts
				$postTagModel->add($table->id, $postId, EB::date()->toSql());
			}

		}

		$this->info->set('COM_EB_MASS_ASSIGN_TAGS_SUCCESS', 'success');

		return $this->app->redirect($return);
	}
}
