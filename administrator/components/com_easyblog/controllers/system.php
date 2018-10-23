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

jimport('joomla.installer.helper');
jimport('joomla.installer.installer');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class EasyBlogControllerSystem extends EasyBlogController
{
	/**
	 * Process EasyBlog upgrades
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function upgrade()
	{
		$model = EB::model('System');
		$state = $model->update();

		if ($state === false) {
			$this->info->set($model->getError(), 'error');
			return $this->app->redirect('index.php?option=com_easyblog');
		}

		$this->info->set('EasyBlog updated to the latest version successfully', 'success');
		return $this->app->redirect('index.php?option=com_easyblog');
	}
}
