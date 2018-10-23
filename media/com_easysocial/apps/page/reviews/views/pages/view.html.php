<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class ReviewsViewPages extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display($pageId = null, $docType = null)
	{
		// Load up the page
		$page = ES::page($pageId);

		$this->setTitle('APP_PAGE_REVIEWS_TITLE_REVIEWS');

		$params = $this->app->getParams();

		// Set the max length of the item
		$options = array('limit' => (int) $params->get('total', 10), 'isAdmin' => $page->isAdmin());

		// Get a list of reviews
		$model = ES::model('Reviews');
		$items = $model->getReviews($page->id, SOCIAL_TYPE_PAGE, $options);
		$pagination = $model->getPagination();

		// Format the item's content.
		$this->format($items, $params);

		$pagination->setVar('option', 'com_easysocial');
		$pagination->setVar('view', 'pages');
		$pagination->setVar('layout', 'item');
		$pagination->setVar('id', $page->getAlias());
		$pagination->setVar('appId', $this->app->getAlias());

		$this->set('params', $params);
		$this->set('pagination', $pagination);
		$this->set('cluster', $page);
		$this->set('items', $items);

		echo parent::display('themes:/site/reviews/default/default');
	}

	private function format(&$items, $params)
	{
		$length	= $params->get('content_length');

		if ($length == 0) {
			return;
		}

		foreach ($items as &$item) {
			$item->message = JString::substr(strip_tags($item->message), 0, $length) . ' ' . JText::_('COM_EASYSOCIAL_ELLIPSES');
		}
	}
}
