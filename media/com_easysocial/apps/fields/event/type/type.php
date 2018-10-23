<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/fields/dependencies');

class SocialFieldsEventType extends SocialFieldItem
{
	/**
	 * Displays the field for creation.
	 *
	 * @since   1.3
	 * @access  public
	 */
	public function onRegister(&$post, &$session)
	{
		// Support for group event
		// If this is a group event, we do not allow user to change the type as the type follows the group
		$reg = FD::registry();
		$reg->load($session->values);

		if ($reg->exists('group_id')) {
			return;
		}

		if ($reg->exists('page_id')) {
			return;
		}

		$value = isset($post['event_type']) ? $post['event_type'] : $this->params->get('default');

		$this->set('value', $value);

		return $this->display();
	}

	/**
	 * Displays the field for edit.
	 *
	 * @since   1.3
	 * @access  public
	 */
	public function onEdit(&$post, &$cluster, $error)
	{
		// Support for group/page event
		// If this is a group/page event, we do not allow user to change the type as the type follows the group/page
		if ($cluster->isClusterEvent()) {
			return;
		}

		$value = isset($post['event_type']) ? $post['event_type'] : $cluster->type;

		$this->set('value', $value);

		return $this->display();
	}

	/**
	 * Executes before the event is created
	 *
	 * @since   1.3
	 * @access  public
	 */
	public function onRegisterBeforeSave(&$post, &$event)
	{   
		// Currently, the type always follow group/page type
		// There is a separate checking where user must be group/page member to join the event
		if ($event->isClusterEvent()) {
			$event->type = $event->getCluster()->type;
			unset($post['event_type']);

			return;
		}

		$type = isset($post['event_type']) ? $post['event_type'] : $this->params->get('default');

		$event->type = $type;

		unset($post['event_type']);
	}

	/**
	 * Executes before the event is created.
	 *
	 * @since   1.3
	 * @access  public
	 */
	public function onEditBeforeSave(&$post, &$event)
	{
		// Currently, the type always follow group/page type
		// There is a separate checking where user must be group/page member to join the event
		if ($event->isClusterEvent()) {
			$event->type = $event->getCluster()->type;
			unset($post['event_type']);

			return;
		}

		$type = isset($post['event_type']) ? $post['event_type'] : $event->type;

		$event->type = $type;

		unset($post['event_type']);
	}
}
