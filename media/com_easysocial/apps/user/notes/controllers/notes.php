<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class NotesControllerNotes extends SocialAppsController
{
	/**
	 * Renders the form dialog for notes
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function form()
	{
		ES::checkToken();
		ES::requireLogin();

		$id = $this->input->get('id', 0, 'int');

		$table = $this->getTable('Note');
		$table->load($id);

		// Check if the user is allowed to edit this note
		if ($id && $table->user_id != $this->my->id) {
			return $this->ajax->reject();
		}

		// Set the params
		$params = $this->getParams();

		// Load the contents
		$theme = ES::themes();
		$theme->set('note', $table);
		$theme->set('params', $params);

		$contents = $theme->output('themes:/site/notes/dialogs/form');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays a delete confirmation dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		ES::checkToken();
		ES::requireLogin();

		$theme = ES::themes();
		$contents = $theme->output('themes:/site/notes/dialogs/delete');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Deletes a note from the site
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function delete()
	{
		ES::checkToken();
		ES::requireLogin();

		$id = $this->input->get('id', 0, 'int');

		$table = $this->getTable('Note');
		$table->load($id);

		// Throw error when the id not valid
		if (!$id || !$table->id) {
			return $this->ajax->reject();
		}

		if ($table->user_id != $this->my->id) {
			return $this->ajax->reject();
		}

		if (!$table->delete()) {
			return $this->ajax->reject($note->getError());
		}

		return $this->ajax->resolve();
	}

	/**
	 * Creates a new note.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store()
	{
		ES::checkToken();
		ES::requireLogin();

		// Get the app id.
		$appId = JRequest::getInt('appId');

		// Get the title from request
		$title = $this->input->get('title', '', 'string');
		$content = $this->input->get('content', '', 'default');
		$stream = $this->input->get('stream', false, 'bool');
		$id = $this->input->get('id', 0, 'int');

		$table = $this->getTable('Note');
		$state = $table->load($id);

		if ($id && $state && $table->user_id != $this->my->id) {
			return $this->ajax->reject();
		}

		$table->title = $title;
		$table->content = $content;
		$table->user_id = $this->my->id;

		$state = $table->store();

		$table->permalink = ESR::_('index.php?option=com_easysocial&view=apps&layout=canvas&id=' . $appId . '&cid=' . $table->id . '&userId=' . $this->my->id);

		if (!$state) {
			return $this->ajax->reject($table->getError());
		}

		// Create a stream record
		if ($stream) {
			$verb = $id ? 'update' : 'create';
			$table->createStream($verb);
		}

		$app = $this->getApp();
		$theme = FD::themes();
		$theme->set('app', $app);
		$theme->set('user', $this->my);
		$theme->set('appId', $appId);
		$theme->set('note', $table);

		$content = $theme->output('themes:/site/notes/profile/item');

		return $this->ajax->resolve($content);
	}
}
