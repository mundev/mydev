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

class GuestsViewEvents extends SocialAppsView
{
	public function display($eventId = null, $docType = null)
	{
		// Load up the event
		$event = ES::event($eventId);
		$params = $event->getParams();
		$appParam = $this->app->getParams();

		// Load up the events model
		$model = ES::model('Events');
		$type = $this->input->get('type', 'going', 'string');

		$this->setTitle('APP_ATTENDEES_APP_TITLE');

		$options = array();

		$limit = (int) $appParam->get('guests.limit');

		$options['limit'] = $limit; 

		if ($type === 'going') {
			$options['state'] = SOCIAL_EVENT_GUEST_GOING;
		}

		if ($params->get('allowmaybe') && $type === 'maybe') {
			$options['state'] = SOCIAL_EVENT_GUEST_MAYBE;
		}

		if ($params->get('allownotgoingguest') && $type === 'notgoing') {
			$options['state'] = SOCIAL_EVENT_GUEST_NOT_GOING;
		}

		if ($event->isClosed() && $type === 'pending') {
			$options['state'] = SOCIAL_EVENT_GUEST_PENDING;
		}

		if ($type === 'admin') {
			$options['admin'] = 1;
		}

		$guests  = $model->getGuests($event->id, $options);
		
		// Set pagination properties
		$pagination = $model->getPagination();
		$pagination->setVar('view', 'events');
		$pagination->setVar('layout', 'item');
		$pagination->setVar('id', $event->getAlias());
		$pagination->setVar('appId', $this->app->getAlias());
		$pagination->setVar('Itemid', ESR::getItemId('events', 'item', $event->id));

		if ($pagination && $type) {
			$pagination->setVar('filter', $type);
		}

		if ($guests) {
			foreach ($guests as $guest) {
				$guest->user = ES::user($guest->uid);
			}
		}

		$emptyText = 'APP_EVENT_GUESTS_EMPTY_SEARCH';
		
		$myGuest = $event->getGuest();

		$counters = array();
		$counters['going'] = $event->getTotalGoing();
		$counters['maybe'] = $event->getTotalMaybe();
		$counters['notgoing'] = $event->getTotalNotGoing();
		$counters['admins'] = $event->getTotalAdmins();
		
		$counters['pending'] = 0;

		if ($event->isAdmin()) {
			$counters['pending'] = $event->getTotalPendingGuests();
		}

		$this->set('counters', $counters);
		$this->set('active', $type);
		$this->set('event', $event);
		$this->set('guests', $guests);
		$this->set('returnUrl', '');
		$this->set('myGuest', $myGuest);
		$this->set('pagination', $pagination);
		$this->set('emptyText', $emptyText);

		echo parent::display('events/default');
	}
}