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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewComposer extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// Handles backedn
		if ($this->app->isAdmin() && method_exists($this, $this->getLayout())) {
			$layoutName = $this->getLayout();
			return $this->$layoutName();
		}

		// If there is no tmpl=component, we will enforce this
		$tmpl = $this->input->get('tmpl', '');

		if ($tmpl != 'component') {
			$this->input->setVar('tmpl', 'component');
		}

		// Ensure that the user is logged in.
		EB::requireLogin();

		// null = new post
		// 63   = post 63 from post table
		// 63.2 = post 63, revision 2 from history table
		$uid = $this->input->getVar('uid', null);

		// If no id given, create a new post.
		$post = EB::post($uid);

		// If there is a uid, ensure that it is loadable
		if ($uid && !$post->id) {
			$redirect = EBR::_('index.php?option=com_easyblog', false);
			return $this->app->redirect($redirect);
		}

		// Do not allow normal user to edit the existing post if there is already another revision that was submitted for approvals.
		if (!EB::isSiteAdmin() && !$this->acl->get('moderate_entry') && (!$this->acl->get('manage_pending') || !$this->acl->get('publish_entry')) && $post->hasRevisionWaitingForApproval()) {
			JError::raiseError(500, JText::_('COM_EASYBLOG_NOT_ALLOWED_TO_EDIT_ANOTHER_REVISION_PENDING_APPROVAL'));
			return;
		}

		// we need to set the jdoc base to root so that images with src = images/xxxx/yyy.jpg can render properly inside editor. #955
		$this->doc->setBase(JURI::root());

		// Prevent zooming
		$this->doc->setMetaData('viewport', 'width=device-width, initial-scale=1, maximum-scale=1');

		// If this is a compare, we need to directly load the compare windows
		$compareId = $this->input->get('compareid', '');

		// Do not allow user to access this page if he doesn't have access
		// Verify access (see function manager())
		if (!$post->canCreate() && !$post->canEdit()) {
			return $this->app->redirect(EBR::_('index.php?option=com_easyblog', false));
		}

		// If there's no id provided, we will need to create the initial revision for the post.
		if (!$uid) {
			$post->create();
			$uid = $post->uid;
		}

		if (!$post->title) {
			$this->doc->setTitle(JText::_('COM_EASYBLOG_COMPOSER_POST_UNTITLED'));
		}

		$composer = EB::composer();

		// Detect the keep alive interval that we should use
		$keepAlive = (int) $this->jconfig->get('lifetime');
		$keepAlive = round($keepAlive / 2);
		$keepAlive = $keepAlive > 16 ? 16 : $keepAlive;
		$keepAlive = $keepAlive * 10000;

		$this->set('keepAlive', $keepAlive);
		$this->set('composer', $composer);
		$this->set('post', $post);
		$this->set('compareId', $compareId);

		return parent::display('composer/default');
	}

	/**
	 * Retrieves a list of posts created by the user or on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts($tmpl = null)
	{
		// Ensure that the user is logged in.
		EB::requireLogin();

		$lang = $this->input->getVar('code', null);
		$langid = $this->input->getVar('codeid', null);
		$search = $this->input->getVar('query', '');

		// Admin might want to display the featured blogs on all pages.
		$start = $this->input->get('start', 0, 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		// conditions
		$options = array();

		$options['langcode'] = $lang;

		if ($search) {
			$options['search'] = $search;
		}

		if (!EB::isSiteAdmin()) {
			$options['userid'] = $this->my->id;
		}

		$model = EB::model('Blog');

		// get results
		$data = $model->getAssociationPosts($options);

		// Get the pagination
		$pagination = $model->getPagination();

		if ($data) {
			EB::cache()->insert($data);
		}

		if ($this->app->isAdmin()) {
			EB::loadLanguages();

			$pagination->setAdditionalUrlParam('option', 'com_easyblog');
			$pagination->setAdditionalUrlParam('view', 'composer');
			$pagination->setAdditionalUrlParam('layout', 'getPosts');
			$pagination->setAdditionalUrlParam('code', $lang);
			$pagination->setAdditionalUrlParam('codeid', $langid);
			$pagination->setAdditionalUrlParam('query', $search);
			$pagination->setAdditionalUrlParam('browse', true);
			$pagination->setAdditionalUrlParam('tmpl', 'component');
		}

		$posts = EB::formatter('list', $data, false);

		$this->set('posts', $posts);
		$this->set('langcode', $lang);
		$this->set('langid', $langid);
		$this->set('search', $search);
		$this->set('pagination', $pagination);

		parent::display('composer/posts/listing');
	}
}
