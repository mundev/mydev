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

class TasksViewProfile extends SocialAppsView
{
	public function display($userId = null, $docType = null)
	{
		$user = ES::user($userId);

		$params = $this->getParams();
		$hidePersonal = $params->get('hide_personal', true);

		if ($this->my->id == $userId) {
			$hidePersonal = false;
		}

		$model = ES::model('Tasks');
		$result = $model->getItems($userId, $hidePersonal);
		$counters = $model->getUserTaskCounters($userId);

		// If there are tasks, we need to bind them with the table.
		$tasks = array();

		if ($result) {
			
			foreach ($result as $row) {
				$task = ES::table('Task');
				$task->bind($row);
				$task->cluster = '';

				if ($task->uid && $task->type) {
					$cluster = ES::cluster($task->type, $task->uid);

					// Check for cluster privacy
					if (!$cluster->canViewItem()) {

						// Re-adjust the counters
						$counters[$task->type]--;
						$counters['total']--;

						if ($task->isResolved()) {
							$counters['resolved']--;
						} else {
							$counters['unresolved']--;
						}

						continue;
					}

					$task->cluster = $cluster;
				}

				$tasks[] = $task;
			}
		}

		$this->set('hidePersonal', $hidePersonal);
		$this->set('user', $user);
		$this->set('counters', $counters);
		$this->set('tasks', $tasks);

		echo parent::display('profile/default');
	}
}
