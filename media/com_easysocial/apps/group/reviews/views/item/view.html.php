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

class ReviewsViewItem extends SocialAppsView
{
	/**
	 * Renders the Review item view
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($uid = null, $docType = null )
	{
		$group = ES::group($uid);

		if (!$group->canViewItem()) {
			return $this->redirect($group->getPermalink(false));
		}

		// Load up the app params
		$params = $this->app->getParams();

		// Get the review item
		$id = $this->input->get('reviewId', 0, 'int');
		$reviews = ES::table('Reviews');
		$reviews->load($id);

		// Get the author of the article
		$author = $reviews->getAuthor();

		// Get the url for the article
		$url = $reviews->getPermalink();

		// Set the page title
		$this->page->title($reviews->title);

		$this->set('params', $params);
		$this->set('reviews', $reviews);
		$this->set('cluster', $group);
		$this->set('author', $author);

		echo parent::display('themes:/site/reviews/item/default');
	}
}
