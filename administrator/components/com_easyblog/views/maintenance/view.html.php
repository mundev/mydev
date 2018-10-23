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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewMaintenance extends EasyBlogAdminView
{
	/**
	 * Displays the theme listings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.maintenance');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		if ($this->input->get('success', 0, 'int')) {
			$this->info->set(JText::_('COM_EASYBLOG_MAINTENANCE_SUCCESSFULLY_EXECUTED_SCRIPT'), EASYBLOG_MSG_SUCCESS);
		}

		// Set heading text
		$this->setHeading('COM_EASYBLOG_MAINTENANCE_TITLE_SCRIPTS', '', 'fa-flask');

		// Set the buttons
		JToolbarHelper::custom('maintenance.form', 'refresh', '', JText::_('COM_EASYBLOG_MAINTENANCE_EXECUTE_SCRIPTS'));

		// filters
		$version = $this->app->getUserStateFromRequest('com_easyblog.maintenance.filter_version', 'filter_version', 'all', 'cmd');

		$order = $this->app->getUserStateFromRequest('com_easyblog.maintenance.filter_order', 'filter_order', 'version', 'cmd');
		$orderDirection	= $this->app->getUserStateFromRequest('com_easyblog.maintenance.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$versions = array();

		$model = EB::model('Maintenance');
		$model->setState('version', $version);
		$model->setState('ordering', $order);
		$model->setState('direction', $orderDirection);

		$scripts = $model->getItems();
		$pagination = $model->getPagination();

		$versions = $model->getVersions();

		$limit = $model->getState('limit');

		$this->set('limit', $limit);
		$this->set('version', $version);
		$this->set('scripts', $scripts);
		$this->set('versions', $versions);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('pagination', $pagination);

		parent::display('maintenance/default');
	}

	public function form($tpl = null)
	{
		$cids = $this->input->get('cid', array(), 'var');

		$scripts = EB::model('Maintenance')->getItemByKeys($cids);

		$this->set('scripts', $scripts);

		parent::display('maintenance/form');
	}

	/**
	 * Displays the theme installer form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function database($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.maintenance');

		// Set heading text
		$this->setHeading('COM_EASYBLOG_MAINTENANCE_TITLE_DATABASE', '', 'fa-flask');

		parent::display('maintenance/database');
	}
}
