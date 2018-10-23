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

class NotesViewProfile extends SocialAppsView
{
	public function display($userId = null, $docType = null)
	{
		// Get Notes model library
		$model = $this->getModel('Notes');

		$this->setTitle('APP_NOTES_APP_TITLE');

		// Retrieve list of notes created by user
		$result = $model->getItems($userId);
		$total = $model->getTotalNotes($userId);

		// Get the profile
		$user = ES::user($userId);

		// Format the notes
		$notes = $this->format($result, $user);

		$this->set('total', $total);
		$this->set('user', $user);
		$this->set('notes', $notes);

		echo parent::display('themes:/site/notes/profile/default');
	}

	/**
	 * Formats the result of notes
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function format($result = array(), $owner)
	{
		if (!$result) {
			return $result;
		}

		$notes = array();

		foreach ($result as $row) {
			$note = $this->getTable('Note');
			$note->bind($row);
			$note->permalink = ESR::apps(array('layout' => 'canvas', 'id' => $this->app->getAlias(), 'cid' => $note->id, 'uid' => $owner->getAlias(), 'type' => SOCIAL_TYPE_USER));

			$notes[] = $note;
		}

		return $notes;
	}
}
