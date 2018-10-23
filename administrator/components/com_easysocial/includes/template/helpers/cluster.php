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

class ThemesHelperCluster extends ThemesHelperAbstract
{
	/**
	 * Renders the page stream object
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function approvalHistory($data)
	{
		$theme = ES::themes();
		$theme->set('data', $data);

		$content = $theme->output('site/clusters/history/default');

		return $content;
	}

	/**
	 * Renders sidebar categories menu for clusters
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function categoriesSidebar($type, $activeCategory, $categories = array())
	{

		// Get a list of group categories
		if (!$categories) {
			$categories = ES::populateClustersCategoriesTree($type, array(), array('state' => SOCIAL_STATE_PUBLISHED));
		}

		$cluster = false;

		// If the caller is from cluster(page/group)
		if ($type == SOCIAL_TYPE_EVENT) {
			$eventCluster = $this->input->get('type', '', 'string');
			$uid = $this->input->get('uid', null, 'int');

			if ($eventCluster == SOCIAL_TYPE_PAGE || $eventCluster == SOCIAL_TYPE_GROUP) {
				$cluster = ES::cluster($eventCluster, $uid);
			}
		}

		$theme = ES::themes();
		$theme->set('cluster', $cluster);
		$theme->set('categories', $categories);
		$theme->set('type', $type);
		$theme->set('activeCategory', $activeCategory);

		$content = $theme->output('site/clusters/categories/menu');

		return $content;
	}
}