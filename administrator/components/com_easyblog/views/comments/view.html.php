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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewComments extends EasyBlogAdminView
{
	/**
	 * Displays a list of comments
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.comment');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}
		// Set the page heading
		$this->setHeading('COM_EASYBLOG_TITLE_COMMENTS', '', 'fa-comments');

		JToolbarHelper::publishList('comment.publish');
		JToolbarHelper::unpublishList('comment.unpublish');
		JToolBarHelper::divider();
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_COMMENTS_CONFIRM_DELETE', true), 'comment.delete');

		$filter_state = $this->app->getUserStateFromRequest('com_easyblog.comments.filter_state', 'filter_state', 	'*', 'word');
		$search = $this->app->getUserStateFromRequest('com_easyblog.comments.search', 'search', '', 'string');
		$search = EBString::trim(EBString::strtolower($search));
		
		$order = $this->app->getUserStateFromRequest('com_easyblog.comments.filter_order', 'filter_order', 'a.ordering', 'cmd');
		$orderDirection	= $this->app->getUserStateFromRequest('com_easyblog.comments.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word');

		//Get data from the model
		$model  = EB::model('Comments');
		$result = $model->getData();
		$pagination = $model->getPagination();
		$comments = array();
		$limit = $model->getState('limit');

		//convert the status.
		if ($result) {

			foreach ($result as $row) {
				$comment = EB::table('Comment');
				$comment->bind($row);

				$comment->blog_name = $row->blog_name;
				$comment->isModerate = false;

				if ($comment->published == 2) {
					$comment->isModerate = true;
					$comment->published = 0;
				}

				$comments[] = $comment;
			}
		}

		// Get the filter state
		$filterState = $this->getFilterState($filter_state);

		$this->set('limit', $limit);
		$this->set('filterState', $filterState);
		$this->set('search', $search);
		$this->set('comments', $comments);
		$this->set('pagination', $pagination);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('comments/default');
	}

	/**
	 * Allows site admin to edit a comment from the back end
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->setHeading('COM_EASYBLOG_COMMENTS_FORM_TITLE', '', 'fa-comments');

		JToolbarHelper::apply('comment.apply');
		JToolBarHelper::save('comment.save');
		JToolBarHelper::cancel('comment.cancel');

		// Get the comment item
		$id = $this->input->get('id', '', 'int');

		$comment = EB::table('Comment');
		$comment->load($id);

		// Set default values for new entries.
		if (!$comment->id) {
			$comment->created	= EB::date()->format();
			$comment->published	= true;
		}

		// If this comment was posted by a user on the site, pre-fill in the values
		if ($comment->created_by) {
			$author = $comment->getAuthor();
			$comment->name  = $author->getName();
			$comment->email = $author->user->email;
			$comment->website = $author->getWebsite();
		}

		$this->set('comment', $comment);
		$this->set('post', $comment->getBlog());

		parent::display('comments/form');
	}

	public function getFilterState($filter_state='*')
	{
		$state[] = JHTML::_('select.option',  '', JText::_('COM_EASYBLOG_SELECT_STATE'));
		$state[] = JHTML::_('select.option',  'P', JText::_('COM_EASYBLOG_PUBLISHED'));
		$state[] = JHTML::_('select.option',  'U', JText::_('COM_EASYBLOG_UNPUBLISHED'));
		$state[] = JHTML::_('select.option',  'M', JText::sprintf('COM_EASYBLOG_AWAITING_MODERATION' , $this->get('TotalPending')));

		return JHTML::_('select.genericlist',   $state, 'filter_state', 'class="form-control" data-table-grid-filter', 'value', 'text', $filter_state );
	}
}
