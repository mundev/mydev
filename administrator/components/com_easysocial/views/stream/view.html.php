<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasySocialViewStream extends EasySocialAdminView
{
	public function display($tpl = null)
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_STREAM', 'COM_EASYSOCIAL_DESCRIPTION_STREAM');

		// Check if this is from after execution
		$success = JRequest::getInt('success');

		$model = FD::model('stream', array('initState' => true, 'namespace' => 'stream.listing'));
		$items = $model->getItemsWithState();

		$ordering = $model->getState('ordering');
		$direction = $model->getState('direction');
		$limit = $model->getState('limit');
		$state = $model->getState('state');
		$pagination = $model->getPagination();

		// Add button
		$this->toolbar($state);


		// set variable into themes.
		$this->set('pagination', $pagination);
		$this->set('ordering', $ordering);
		$this->set('direction', $direction);
		$this->set('limit', $limit);
		$this->set('state', $state);
		$this->set('items', $items);

		parent::display('admin/stream/default/default');
	}

	/**
	 * Standard redirection after performing actions
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function standardRedirection()
	{
		return $this->redirect('index.php?option=com_easysocial&view=stream');
	}

	private function toolbar($state)
	{
		if ($state == SOCIAL_STREAM_STATE_TRASHED) {
			JToolbarHelper::custom('purge', 'trash', '', JText::_('COM_EASYSOCIAL_STREAM_DELETE'), true);
		}

		if ($state == 'all' || $state == SOCIAL_STREAM_STATE_RESTORED) {
			JToolbarHelper::custom('trash', 'trash' , '' , JText::_('COM_EASYSOCIAL_STREAM_TRASH'), true);
		}

		if ($state == SOCIAL_STREAM_STATE_TRASHED) {
			JToolbarHelper::custom('restoreTrash', 'refresh', '', JText::_('COM_EASYSOCIAL_STREAM_RESTORE'), true);
		}

		if ($state == SOCIAL_STREAM_STATE_ARCHIVED) {
			JToolbarHelper::custom('restore', 'refresh', '', JText::_('COM_EASYSOCIAL_STREAM_RESTORE'), true);
		}

		if ($state == 'all' || $state == SOCIAL_STREAM_STATE_RESTORED) {
			JToolbarHelper::custom('archive', '', '', JText::_('COM_EASYSOCIAL_STREAM_ARCHIVE'), true);
		}
	}
}