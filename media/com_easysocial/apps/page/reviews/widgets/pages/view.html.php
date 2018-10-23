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

class ReviewsWidgetsPages extends SocialAppsWidgets
{
	/**
	 * Display admin actions for the page
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function pageAdminStart($page)
	{
		if (!$this->app->hasAccess($page->category_id) || !$page->getParams()->get('reviews', true)) {
			return;
		}

		$theme = ES::themes();
		$theme->set('app', $this->app);
		$theme->set('cluster', $page);

		echo $theme->output('themes:/site/reviews/widgets/menu');
	}

	/**
	 * Display additional meta in header of the page
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function headerMeta($page)
	{
		if (!$this->app->hasAccess($page->category_id) || !$page->getParams()->get('reviews', true)) {
			return;
		}

		$theme = ES::themes();
		$theme->set('cluster', $page);

		echo $theme->output('themes:/site/reviews/widgets/header');
	}

	/**
	 * Display user photos on the side bar
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function sidebarBottom($pageId)
	{
		// Set the max length of the item
		$params = $this->app->getParams();
		$enabled = $params->get('widget', true);
		$page = ES::page($pageId);

		if (!$enabled || !$this->app->hasAccess($page->category_id)) {
			return;
		}

		$theme = ES::themes();

		$options = array('limit' => (int) $params->get('widgets_total', 5));

		$model = ES::model('Reviews');
		$items = $model->getReviews($page->id, SOCIAL_TYPE_PAGE, $options);
		$total = $model->getTotalReviews($page->id, SOCIAL_TYPE_PAGE);

		if (!$items) {
			return;
		}
		
		$theme->set('total', $total);
		$theme->set('cluster', $page);
		$theme->set('app', $this->app);
		$theme->set('items', $items);

		echo $theme->output('themes:/site/reviews/widgets/reviews');
	}
}
