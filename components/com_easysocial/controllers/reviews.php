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

class EasySocialControllerReviews extends EasySocialController
{
	/**
	 * Allows caller to delete an review
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function delete()
	{
		ES::requireLogin();
		ES::checkToken();

		$id = $this->input->get('id', 0, 'int');

		$review = ES::table('Reviews');
		$review->load($id);

		if (!$review->id || !$id) {
			return $this->view->exception('APP_REVIEWS_INVALID_REVIEWS_ID');
		}

		// Get the cluster
		$cluster = ES::cluster($review->uid);

		if (!$cluster->isAdmin() && $review->created_by != $this->my->id) {
			return $this->view->exception('APP_REVIEWS_NOT_ALLOWED_TO_DELETE');
		}

		$review->delete();

		$this->view->setMessage('APP_REVIEWS_DELETED_SUCCESS', SOCIAL_MSG_SUCCESS);

		return $this->view->call(__FUNCTION__, $review, $cluster);
	}

	/**
	 * Saves a review
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function saveReview()
	{
		ES::requireLogin();
		ES::checkToken();

		// Id of the review data
		$id = $this->input->get('id', 0, 'int');
		$ratings = $this->input->get('score', 0, 'int');
		$title = trim($this->input->get('title', '', 'default'));
		$reviewMessage = trim($this->input->get('message', '', 'default'));

		// Get the uid and type
		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', SOCIAL_TYPE_USER, 'string');

		$cluster = ES::cluster($type, $uid);

		$app = $cluster->getApp('reviews');
		$params = $app->getParams();

		$moderation = $params->get('enable_moderation', false);
		$needModeration = (!$cluster->isAdmin() && $moderation) ? true : false;

		// Load the filter table
		$review = ES::table('Reviews');

		$isNew = true;

		if ($id) {
			$isNew = false;
			$review->load($id);
		}

		if (!$title) {
			$this->view->setMessage('APP_REVIEWS_NO_TITLE', ES_ERROR);
			return $this->view->call(__FUNCTION__, $uid);
		}

		if (!$ratings) {
			$this->view->setMessage('APP_REVIEWS_NO_RATINGS', ES_ERROR);
			return $this->view->call(__FUNCTION__, $uid);
		}

		$message = 'APP_REVIEWS_UPDATED_SUCCESSFULLY';

		if ($isNew) {
			$message = 'APP_REVIEWS_SUBMITTED_SUCCESSFULLY';
			$review->created_by = $this->my->id;
		}

		// Set the filter attributes
		$review->uid = $uid;
		$review->type = $type;
		$review->value = $ratings;
		$review->published = $needModeration ? SOCIAL_REVIEW_STATE_PENDING : SOCIAL_REVIEW_STATE_PUBLISHED;
		$review->title = $title;
		$review->message = $reviewMessage;

		$state = $review->store();

		if (!$state) {
			$this->view->setMessage('APP_REVIEWS_SAVE_FAILED', ES_ERROR);
			return $this->view->call(__FUNCTION__, $uid);
		}

		if ($needModeration) {
			$message = 'APP_REVIEWS_SUBMITTED_FOR_MODERATION';

			// We need to notify cluster admin
			$cluster->notifyAdmins('moderate.review', array('userId' => $this->my->id, 'reviewId' => $review->id, 'message' => $review->message, 'permalink' => $review->getPermalink(), 'title' => $review->title));
		} else {
			ES::points()->assign($cluster->getTypePlural() . '.review.added', 'com_easysocial', $this->my->id);
		}

		$this->view->setMessage($message);

		return $this->view->call(__FUNCTION__, $uid);
	}

	/**
	 * Allows caller to approve a review
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function approve()
	{
		ES::requireLogin();

		$id = $this->input->get('id', 0, 'int');

		$review = ES::table('Reviews');
		$review->load($id);

		if (!$review->id || !$id) {
			return $this->view->exception('APP_REVIEWS_INVALID_REVIEWS_ID');
		}

		// check if review is currently under pending moderation or not.
		// if not, do not process further.
		if (!$review->isPending()) {
			return $this->view->exception('APP_REVIEWS_INVALID_REVIEWS_ID');
		}

		// Get the cluster
		$cluster = ES::cluster($review->uid);

		if (!$cluster->isAdmin()) {
			return $this->view->exception('APP_REVIEWS_NOT_ALLOWED_TO_APPROVE');
		}

		// First we removed any existing stream for this review id
		$review->removeStream();

		$review->publish();

		ES::points()->assign($cluster->getTypePlural() . '.review.added', 'com_easysocial', $review->created_by);

		$this->view->setMessage('APP_REVIEWS_APPROVED_SUCCESS');

		return $this->view->call(__FUNCTION__, $review, $cluster);
	}

	/**
	 * Allows caller to reject a review
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function reject()
	{
		ES::requireLogin();

		$id = $this->input->get('id', 0, 'int');

		$review = ES::table('Reviews');
		$review->load($id);

		if (!$review->id || !$id) {
			return $this->view->exception('APP_REVIEWS_INVALID_REVIEWS_ID');
		}

		// check if review is currently under pending moderation or not.
		// if not, do not process further.
		if (!$review->isPending()) {
			return $this->view->exception('APP_REVIEWS_INVALID_REVIEWS_ID');
		}

		// Get the cluster
		$cluster = ES::cluster($review->uid);

		if (!$cluster->isAdmin()) {
			return $this->view->exception('APP_REVIEWS_NOT_ALLOWED_TO_REJECT');
		}

		$review->delete();

		$this->view->setMessage('APP_REVIEWS_REJECTED_SUCCESS');

		return $this->view->call(__FUNCTION__, $review, $cluster);
	}

	/**
	 * Retrieve reviews for provided cluster id
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getReviews()
	{
		$id = $this->input->get('id', 0, 'int');
		$cluster = ES::cluster($id);

		if (!$cluster->canViewItem()) {
			return $this->view->exception('APP_REVIEWS_NOT_ALLOWED_TO_VIEW');
		}

		$filter = $this->input->get('filter', 'all', 'cmd');
		$options = array();

		if ($filter != 'all') {
			$options[$filter] = true;
		}

		$app = $cluster->getApp('reviews');
		$params = $app->getParams();

		$options['limit'] = $params->get('total', ES::getLimit());

		$model = ES::model('Reviews');
		$reviews = $model->getReviews($cluster->id, $cluster->cluster_type, $options);
		$pagination = $model->getPagination();

		$pagination->setVar('view', $cluster->getTypePlural());
		$pagination->setVar('layout', 'item');
		$pagination->setVar('id', $cluster->getAlias());
		$pagination->setVar('appId', $app->id);
		$pagination->setVar('filter', $filter);

		return $this->view->call(__FUNCTION__, $cluster, $reviews, $pagination, $app);

	}
}
