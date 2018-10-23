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

class ReviewsViewForm extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($uid = null, $docType = null)
	{
		ES::requireLogin();
		
		$page = ES::page($uid);

		$this->setTitle('APP_PAGE_REVIEWS_TITLE_SUBMIT_REVIEW');

		$id = $this->input->get('reviewsId', 0, 'int');
		$reviews = ES::table('Reviews');
		$reviews->load($id);

		$this->page->title(JText::_('APP_PAGE_REVIEWS_FORM_UPDATE_PAGE_TITLE'));

		// Determine if this is a new record or not
		if (!$id) {
			$this->page->title(JText::_('APP_PAGE_REVIEWS_FORM_CREATE_PAGE_TITLE'));
		}

		// Get app params
		$params = $this->app->getParams();

		$redirectOptions = array('layout' => 'canvas', 'customView' => 'form', 'uid' => $page->getAlias(), 'type' => $page->getType(), 'id' => $this->app->getAlias());

		$returnUrl = ESR::apps($redirectOptions);
		$returnUrl = base64_encode($returnUrl);

		$this->set('returnUrl', $returnUrl);
		$this->set('params', $params);
		$this->set('reviews', $reviews);
		$this->set('cluster', $page);

		echo parent::display('themes:/site/reviews/form/default');
	}
}
