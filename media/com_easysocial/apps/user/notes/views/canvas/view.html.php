<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class NotesViewCanvas extends SocialAppsView
{
	public function display($userId = null, $docType = null)
	{
		$id = $this->input->get('cid', 0, 'int');

		// Get the current owner of this app canvas
		$user = ES::user($userId);

		$note = $this->getTable('Note');
		$note->load($id);

		if (!$id || !$note->id) {
			ES::info()->set(false, JText::_('APP_USER_NOTES_INVALID_NOTE_ID_PROVIDED'), SOCIAL_MSG_ERROR);
			$redirect = ESR::profile(array('id' => $user->getAlias()), false);

			return $this->redirect($redirect);
		}

		// Set the page attributes
		$this->page->title($note->title);

		// Load up actions
		$likes = ES::likes($note->id, 'notes', 'create', SOCIAL_APPS_GROUP_USER);
		$comments = ES::comments($note->id, 'notes', 'create', SOCIAL_APPS_GROUP_USER, array('url' => $note->getPermalink()));

		$backLink = $this->app->getUserPermalink($user->getAlias());

		$this->set('backLink', $backLink);
		$this->set('likes', $likes);
		$this->set('comments', $comments);
		$this->set('note', $note);
		$this->set('app', $this->app);
		$this->set('user', $user);

		echo parent::display('themes:/site/notes/item/default');
	}
}
