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

class ReviewsWidgetsGroups extends SocialAppsWidgets
{
	/**
	 * Display admin actions for the group
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function groupAdminStart($group)
	{
		if (!$this->app->hasAccess($group->category_id) || !$group->getParams()->get('reviews', true)) {
		    return;
		}

		$theme = ES::themes();
		$theme->set('app', $this->app);
		$theme->set('cluster', $group);

		echo $theme->output('themes:/site/reviews/widgets/menu');
	}

	/**
     * Display additional meta in header of the group
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return
     */
	public function headerMeta($group)
	{
		if (!$this->app->hasAccess($group->category_id) || !$group->getParams()->get('reviews', true)) {
		    return;
		}

		$theme = ES::themes();
		$theme->set('cluster', $group);

		echo $theme->output('themes:/site/reviews/widgets/header');
	}

	/**
	 * Display user photos on the side bar
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom($groupId)
	{
		// Set the max length of the item
		$params = $this->app->getParams();
		$enabled = $params->get('widget', true);
		$group = ES::group($groupId);

		if (!$enabled || !$this->app->hasAccess($group->category_id)) {
			return;
		}

		$theme = ES::themes();

		$options = array('limit' => (int) $params->get('widgets_total', 5));

		$model = ES::model('Reviews');
		$items = $model->getReviews($group->id, SOCIAL_TYPE_GROUP, $options);
		$total = $model->getTotalReviews($group->id, SOCIAL_TYPE_GROUP);

		if (!$items) {
			return;
		}
		
		$theme->set('total', $total);
		$theme->set('cluster', $group);
		$theme->set('app', $this->app);
		$theme->set('items', $items);

		echo $theme->output('themes:/site/reviews/widgets/reviews');
	}
}
