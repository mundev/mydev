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

class EasySocialViewTasks extends EasySocialSiteView
{
	/**
	 * Confirmation to delete a milestone
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function confirmDeleteMilestone()
	{
		$id = $this->input->get('id', 0, 'int');
		$return = $this->input->get('return', '', 'default');
		
		$theme = ES::themes();
		$theme->set('id', $id);
		$theme->set('return', $return);

		$output = $theme->output('site/tasks/dialogs/delete.milestone');

		return $this->ajax->resolve($output);
	}

	/**
	 * Post process after milestone is deleted
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function delete($milestone, $cluster)
	{
		return $this->ajax->resolve();
	}

	/**
	 * Post process after task is deleted
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function deleteTask($milestone, $cluster, $task)
	{
		return $this->ajax->resolve();
	}

	/**
	 * Post process after milestone is resolved
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function resolve($milestone, $cluster)
	{
		return $this->ajax->resolve();
	}

	/**
	 * Post process after milestone is unresolved
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function unresolve($milestone, $cluster)
	{
		return $this->ajax->resolve();
	}

	/**
	 * Post process after task is resolved
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function resolveTask($milestone, $cluster, $task)
	{
		return $this->ajax->resolve();
	}

	/**
	 * Post process after task is unresolved
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function unresolveTask($milestone, $cluster, $task)
	{
		return $this->ajax->resolve();
	}

	/**
	 * Post processing after saving a task
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function saveTask($milestone, $task, $cluster)
	{
		// Get the assignee
		$assignee = ES::user($task->user_id);

		// Get the contents
		$theme = ES::themes();
		$theme->set('milestone', $milestone);
		$theme->set('cluster', $cluster);
		$theme->set('task', $task);
		$theme->set('assignee', $assignee);

		$output = $theme->output('site/tasks/item/task');
		
		return $this->ajax->resolve($output);
	}
}
