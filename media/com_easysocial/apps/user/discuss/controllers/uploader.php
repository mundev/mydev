<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

class DiscussControllerUploader extends SocialAppsController
{
	/**
	 * Process file uploads from the story
	 *
	 * @since	2.0.6
	 * @access	public
	 */
	public function upload()
	{
		// Ensure that the user has access to upload

		// Ensure that this is an image only
		$options = array('name' => 'file', 'maxsize' => '100M');

		// Get uploaded file
		$uploader = ES::uploader($options);
		$file = $uploader->getFile(null, '');

		// If there was an error getting uploaded file, stop.
		if ($file instanceof SocialException) {
			dump($file->toArray());
			die('Invalid file');
		}

		// Save the file into the temporary path
		$tmpName = md5(JFactory::getDate()->toSql() . uniqid() . $file['name']);
		$source = $file['tmp_name'];
		$destination = JPATH_ROOT . '/tmp/' . $tmpName;

		JFile::copy($source, $destination);

		// Store this in the uploader temporary table
		$table = ED::table('AttachmentsTmp');
		$table->path = $destination;
		$table->title = $file['name'];
		$table->mime = $file['type'];
		$table->userid = $this->my->id;

		$table->store();
		
		header('Content-type: text/x-json; UTF-8');
		echo json_encode($table);
		exit;
	}
}
