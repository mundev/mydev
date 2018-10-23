<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/tables/table');

class SocialTableReviews extends SocialTable
{
	public $id = null;
	public $uid = null;
	public $type = null;
	public $title = null;
	public $created_by = null;
	public $value = null;
	public $created = null;
	public $published = null;
	public $message = null;

	public function __construct($db)
	{
		parent::__construct('#__social_reviews', 'id', $db);
	}

	/**
	 * Override parent's store behavior
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function store($updateNulls = array())
	{
		$isNew = !$this->id;
		$state = parent::store();

		// If it is a new item, we want to run some other stuffs here.
		if ($isNew && $state && $this->isPublished()) {
			$this->createStream();
		}

		return $state;
	}

	/**
	 * Allow caller to create a stream
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function createStream()
	{
		// Get the cluster
		$cluster = ES::cluster($this->uid);

		// Get the permalink of this news item
		$permalink = $this->getPermalink(false, true);

		// Create a new stream item for this discussion
		$stream = ES::stream();

		// Get the stream template
		$tpl = $stream->getTemplate();
		$tpl->setActor($this->created_by, SOCIAL_TYPE_USER);
		$tpl->setContext($this->id, 'reviews');
		$tpl->setCluster($this->uid, $cluster->getType(), $cluster->type);
		$tpl->setVerb('create');

		$registry = ES::registry();
		$registry->set('reviews', $this);

		// Set the params
		$tpl->setParams($registry);

		$tpl->setAccess('core.view');

		// Add the stream
		$stream->add($tpl);
	}

	/**
	 * Removes a stream item
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function removeStream()
	{
		$stream = ES::stream();
		$result = $stream->delete($this->id, 'reviews', $this->created_by, 'create');

		return $result;
	}

	/**
	 * Retrieve the author for this reviews
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function getAuthor()
	{	
		return ES::user($this->created_by);
	}

	/**
	 * Retrieves the permalink to the review
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getPermalink($xhtml = true, $external = false, $sef = true)
	{
		static $apps = array();

		$cluster = ES::cluster($this->type, $this->uid);

		if (!isset($apps[$this->type])) {
			$apps[$this->type] = $cluster->getApp('reviews');
		}

		$options = array();
		$options['layout'] = 'canvas';
		$options['customView'] = 'item';
		$options['uid'] = $cluster->getAlias();
		$options['type'] = $this->type;
		$options['id'] = $apps[$this->type]->getAlias();
		$options['reviewId'] = $this->id;
		$options['external'] = $external;
		$options['sef'] = $sef;

		$permalink = ESR::apps($options, $xhtml);
		
		return $permalink;
	}

	/**
	 * Retrieves the edit permalink to the reviews
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getEditPermalink($xhtml = true, $external = false, $sef = true)
	{
		static $apps = array();

		$cluster = ES::cluster($this->type, $this->uid);

		if (!isset($apps[$this->type])) {
			$apps[$this->type] = $cluster->getApp('reviews');
		}

		$options = array();
		$options['layout'] = 'canvas';
		$options['customView'] = 'edit';
		$options['uid'] = $cluster->getAlias();
		$options['type'] = $cluster->getType();
		$options['id'] = $apps[$this->type]->getAlias();
		$options['reviewId'] = $this->id;

		$permalink = ESR::apps($options, $xhtml);
		
		return $permalink;
	}

	/**
	 * Retrieves the created date
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getCreatedDate()
	{
		$date = ES::date($this->created);

		return $date;
	}

	/**
	 * Delete reviews
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function delete($pk = null)
	{
		$state = parent::delete($pk);

		if ($state) {
			// Remove the stream that belongs to this review.
			$model = ES::model('Reviews');
			$model->deleteReviewStreams($this->id);
		}

		return $state;
	}

	/**
	 * Publish reviews
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function publish($items = array(), $state = 1, $userId = 0)
	{
		$this->published = SOCIAL_REVIEW_STATE_PUBLISHED;
		
		$state = parent::store();

		// If stored, create a stream.
		if ($state) {
			$this->createStream();
		}
	}

	/**
	 * Determine if the review is in pending state
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function isPending()
	{
		return $this->published == SOCIAL_REVIEW_STATE_PENDING;
	}

	/**
	 * Determine if the review is in published state
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function isPublished()
	{
		return $this->published == SOCIAL_REVIEW_STATE_PUBLISHED;
	}
	
}
