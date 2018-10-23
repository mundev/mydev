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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewDownload extends EasyBlogView
{
	/**
	 * Displays the calendar layout
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		if (! $this->config->get('gdpr_enabled')) {
			return JError::raiseError(404, JText::_('COM_EB_GDPR_DOWNLOAD_DISABLED'));
		}

		// Get the composite keys
		$data = $this->input->get('id', '', 'raw');
		$redirect = EB::_( 'index.php?option=com_easyblog&view=latest', false);

		if (!$data) {
			return JError::raiseError(404, JText::_('COM_EB_INVALID_TOKEN_PROVIDED'));
		}


		$return = JUri::getInstance()->toString();
		$return = base64_encode($return);

		$keys = base64_decode($data);

		$key = explode('|', $keys);

		$id = $key[0];
		$userId = $key[1];
		$created = $key[2];

		$download = EB::table('download');
		$download->load($id);

		if (!$download->id || $download->state != EASYBLOG_DOWNLOAD_REQ_READY || $download->userid != $userId || $download->created != $created) {
			return JError::raiseError(404, JText::_('COM_EB_INVALID_TOKEN_PROVIDED'));
		}

		// okay all passed. lets display a password form to verify the user.
		$this->set('data', $data);
		$this->set('return', $return);

		return parent::display('download/default');
	}

	/**
	 * Display delete info page
	 *
	 * @since   5.2.0
	 * @access  public
	 */
	public function deleteinfo()
	{
		if (!$this->config->get('gdpr_enabled')) {
			return JError::raiseError(404, JText::_('COM_EB_GDPR_DOWNLOAD_DISABLED'));
		}

		// Get the composite keys
		$data = $this->input->get('key', '', 'raw');
		$redirect = EB::_('index.php?option=com_easyblog&view=latest', false);

		if (!$data) {
			return JError::raiseError(404, JText::_('COM_EB_INVALID_TOKEN_PROVIDED'));
		}

		$keys = base64_decode($data);
		$key = explode('|', $keys);

		$userId = $key[0];
		$email = $key[1];

		// okay all passed. lets display a password form to verify the user.
		$this->set('data', $data);
		$this->set('userId', $userId);

		return parent::display('gdpr/delete.info');
	}
}
