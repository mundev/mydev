<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/tables/table');

class SocialTableStream extends SocialTable
{
	public $id = null;
	public $actor_id = null;
	public $actor_type = null;
	public $post_as	= null;
	public $alias = null;
	public $created	= null;
	public $modified = null;
	public $edited = null;
	public $title = null;
	public $content = null;
	public $sitewide = null;
	public $target_id = null;
	public $context_type = null;
	public $verb = null;
	public $stream_type = null;
	public $with = null;
	public $location_id = null;
	public $anywhere_id	= null;
	public $mood_id	= null;
	public $ispublic = null;
	public $params = null;
	public $cluster_id = null;
	public $cluster_type = null;
	public $cluster_access = null;
	public $state = null;
	public $privacy_id = null;
	public $access = null;
	public $custom_access = null;
	public $field_access = null;
	public $last_action = null;
	public $last_userid = null;
	public $last_action_date = null;
	public $sticky_id = null;

	static $_streams = array();

	public function __construct(&$db)
	{
		parent::__construct('#__social_stream', 'id', $db);
	}

	/**
	 * Overrides parent's load implementation
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function load($keys = null, $reset = true)
	{
		if (is_array($keys)) {
			return parent::load($keys, $reset);
		}

		if (!isset(self::$_streams[$keys])) {
			$state = parent::load($keys);
			self::$_streams[$keys] = $this;
			
			return $state;
		}

		return parent::bind(self::$_streams[$keys]);
	}

	public function loadByBatch($ids)
	{
		$db = ES::db();
		$sql = $db->sql();

		$streamIds = array();

		foreach ($ids as $pid) {
			
			if (!isset(self::$_streams[$pid])) {
				$streamIds[] = $pid;
			}
		}

		if ($streamIds) {
			
			foreach ($streamIds as $pid) {
				self::$_streams[$pid] = false;
			}

			$query = '';
			$idSegments = array_chunk( $streamIds, 5 );
			//$idSegments = array_chunk( $streamIds, count($streamIds) );

			for ($i = 0; $i < count($idSegments); $i++) {
				$segment = $idSegments[$i];

				$ids = implode(',', $segment);
				$query .= 'select * from `#__social_stream` where `id` IN ( ' . $ids . ')';

				if (($i + 1)  < count($idSegments)) {
					$query .= ' UNION ';
				}
			}

			$sql->raw($query);
			$db->setQuery($sql);

			$results = $db->loadObjectList();

			if ($results) {

				foreach ($results as $row) {
					self::$_streams[$row->id] = $row;
				}
			}
		}
	}

	/**
	 * Override the parent's store behavior
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function store($updateNulls = false)
	{
		if (is_null($this->modified)) {
			$date = ES::date();
			$this->modified = $date->toSql();
		}

		return parent::store();
	}

	/**
	 * Retrieves a list of #__social_stream_items for the stream object
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function getItems()
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_stream_item');
		$sql->where('uid', $this->id);

		$db->setQuery($sql);

		$items = $db->loadObjectList();

		return $items;
	}

	public function toJSON()
	{
		return array('id' => $this->id ,
					 'actor_id' => $this->actor_id ,
					 'actor_type' => $this->actor_type,
					 'post_as' => $this->post_as,
					 'alias' => $this->alias,
					 'created' => $this->created,
					 'modified' => $this->modified,
					 'title' => $this->title,
					 'content' => $this->content,
					 'sitewide' => $this->sitewide,
					 'target_id' => $this->target_id,
					 'location_id' => $htis->location_id,
					 'ispublic'	=> $this->ispublic,
					 'params'	=> $this->params,
					 'cluster_id' => $this->cluster_id,
					 'cluster_type' => $this->cluster_type,
					 'cluster_access' => $this->cluster_access,
					 'verb' => $this->verb,
					 'mood_id' => $this->mood_id,
					 'privacy_id' => $this->privacy_id,
					 'access' => $this->access,
					 'custom_access' => $this->custom_access,
					 'field_access' => $this->field_access,
		 );
	}

	/**
	 * Get the uid association to this stream
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getUID()
	{
		$db = ES::db();
		$sql = $db->sql();
		$sql->select('#__social_stream_item', 'a');
		$sql->column('a.id');
		$sql->where('a.uid', $this->id);

		$db->setQuery($sql);

		$id = $db->loadResult();

		return $id;
	}

	/**
	 * Loads a stream item by uid
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function loadByUID($uid)
	{
		$db = ES::db();
		$sql = $db->sql();
		$sql->select('#__social_stream', 'a');
		$sql->column('a.*');
		$sql->join('#__social_stream_item', 'b');
		$sql->on('b.uid', 'a.id');
		$sql->where('b.id', $uid);

		$db->setQuery($sql);

		$obj = $db->loadObject();

		return parent::bind($obj);
	}

	/**
	 * Returns the stream's permalink
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getPermalink($xhtml = true, $external = false, $sef = true)
	{
		return ESR::stream(array('id' => $this->id, 'layout' => 'item', 'external' => $external, 'sef' => $sef), $xhtml);
	}

	/**
	 * Retrieves the cluster object for this stream
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getCluster()
	{
		$cluster = ES::cluster($this->cluster_type, $this->cluster_id);

		return $cluster;
	}

	/**
	 * Checks if the provided user is allowed to hide this item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function hideable($id = null)
	{
		return true;
	}

	/**
	 * Determines whether the stream is moderated
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function isModerated()
	{
		return $this->state == SOCIAL_STREAM_STATE_MODERATE;
	}

	/**
	 * Checks if the provided user is the owner of this item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function isAdmin( $id = null )
	{
		$user 	= FD::user( $id );

		if( $user->isSiteAdmin() )
		{
			return true;
		}

		return false;
	}

	/**
	 * Checks if the provided user is the owner of this item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isOwner( $id = null )
	{
		$user 	= FD::user( $id );

		if( $this->actor_id == $user->id )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if this item is posted in a cluster
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function isCluster()
	{
		if ($this->cluster_id) {
			return true;
		}

		return false;
	}

	/**
	 * Delete this stream and its associated stream_items
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function delete($pk = null)
	{
		// Cleanup related items to the stream
		$this->deleteNotifications();
		$this->deleteRepost();
		$this->deleteComments();
		$this->deleteReactions();
		$this->deleteHashtags();

		// Clean up files attached to this stream
		if ($this->context_type == 'files') {
			$this->deleteFiles();
		}

		$db = ES::db();
		$sql = $db->sql();

		$query = 'DELETE FROM `#__social_stream_item` WHERE `uid` = ' . $db->Quote($this->id);
		$sql->raw($query);

		$db->setQuery($sql);
		$db->query();

		$state = parent::delete();

		// Deduct point from the author when the story is deleted.
		ES::points()->assign('story.delete', 'com_easysocial', $this->actor_id);

		return $state;
	}

	/**
	 * Delete any file added to this stream item
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function deleteFiles()
	{
		$streamItem = ES::table('StreamItem');
		$streamItem->load(array('uid' => $this->id));
		$params = $streamItem->getParams();

		$files = $params->get('files');
		
		if ($this->isCluster()) {
			$files = $params->get('file');
		}

		if (empty($files)) {
			return;
		}
		
		foreach ($files as $fileId) {
			$file = ES::table('File');
			$file->load($fileId);

			$file->delete(null, '', false);
		}
	}

	/**
	 * Deletes comments related to the stream item
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function deleteComments()
	{
		$context = $this->getContext();

		$model = ES::model('Comments');
		return $model->deleteComments($this->id, $context);
	}

	/**
	 * Delete repost that are associated with the stream
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function deleteRepost()
	{
		if ($this->context_type != 'shares') {
			return;
		}

		$db = ES::db();
		$sql = $db->sql();

		$query = 'DELETE FROM `#__social_shares` WHERE ' . $db->nameQuote('uid') . ' = ' . $db->Quote($this->target_id);
		$query .= ' AND ' . $db->nameQuote('user_id') . ' = ' . $db->Quote($this->actor_id);
		$sql->raw($query);

		$db->setQuery($sql);
		$db->query();

		return;
	}

	/**
	 * Deletes reactions related to the stream item
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function deleteReactions()
	{
		$context = $this->getContext();

		$model = ES::model('Likes');
		return $model->delete($this->id, $context);
	}

	/**
	 * Deletes reactions related to the stream item
	 *
	 * @since	2.2.3
	 * @access	public
	 */
	public function deleteHashtags()
	{
		$model = ES::model('Hashtags');
		return $model->delete($this->id);
	}

	/**
	 * Delete notification items that related with this stream
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function deleteNotifications()
	{
		// Since the url is no longer valid, we can safely use the url as identifier to delete the notifications
		$url = $this->getPermalink(true, false, false);

		$db = ES::db();
		$sql = $db->sql();

		$query = 'DELETE FROM `#__social_notifications`';
		$query .= ' WHERE `url` = ' . $db->Quote($url);

		$sql->raw($query);

		$db->setQuery($sql);
		$db->query();

		return true;
	}

	/**
	 * Publishes a stream on the site.
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$this->state = SOCIAL_STREAM_STATE_PUBLISHED;

		return $this->store();
	}

	/**
	 * Retrieves the context for the stream
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getContext()
	{
		$context = $this->context_type . '.' . $this->actor_type . '.' . $this->verb;

		return $context;
	}

	/**
	 * Get a list of tags for the stream
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTags($types = array())
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_stream_tags');
		$sql->where('stream_id', $this->id);

		$sql->where('utype', $types, 'IN');
		$db->setQuery($sql);

		$tags 	= $db->loadObjectList();

		return $tags;
	}

	/**
	 * Determines if the viewer can edit this stream item
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function canEdit($userId = null)
	{
		$my = ES::user($userId);
		$access = $my->getAccess();

		if ($my->isSiteAdmin()) {
			return true;
		}

		if ($this->isCluster()) {
			$cluster = $this->getCluster();
			$access = $cluster->getAccess();

			// Instead of only allow admin to edit this stream, we should add an acl for cluster 
			if ($cluster->isAdmin()) { 
				return true;
			}

			if ($access->get('stream.edit', 'admins') == 'members' && $cluster->isMember($my->id) && $this->actor_id == $my->id) {
				return true;
			}

			$this->setError('COM_EASYSOCIAL_STREAM_NOT_ALLOWED_TO_DELETE');

			return false;
		}

		// check if this stream is belogn to user or not.
		if ($this->actor_type == 'user' && $this->actor_id == $my->id) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can delete this stream item
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function canDelete($userId = null, $joins = null)
	{
		$my = ES::user($userId);
		$access = $my->getAccess();

		if ($my->isSiteAdmin()) {
			return true;
		}

		if ($access->allowed('stream.delete')) {
			return true;
		}

		if ($this->isCluster()) {
			$cluster = $this->getCluster();

			if ($cluster->isAdmin()) {
				return true;
			}

			$this->setError('COM_EASYSOCIAL_STREAM_NOT_ALLOWED_TO_DELETE');

			return false;
		}

		return false;
	}

	/**
	 * Determines if the viewer can sticky this stream item
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function canSticky()
	{
		$config = ES::config();

		if (!$config->get('stream.pin.enabled')) {
			return false;
		}

		// If the stream is moderated, it shouldn't be allowed to be stickied
		if ($this->isModerated()) {
			return false;
		}

		if ($this->isCluster()) {
			$cluster = ES::cluster($this->cluster_type, $this->cluster_id);

			// if user is not the cluster owner or the admin, then dont alllow to sticky
			if (!$cluster->isOwner() && !$cluster->isAdmin()) {
				return false;
			}

			return true;
		} 

		if (!$this->isOwner() && !ES::isSiteAdmin()) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the viewer can view this stream item
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function canViewItem($userId = null)
	{
		$user = ES::user($userId);

		if ($user->isSiteAdmin()) {
			return true;
		}

		$lib = ES::stream();
		$stream = $lib->getItem($this->id, $this->cluster_id, $this->cluster_type);

		if ($stream === false) {
			return false;
		}

		return true;
	}

	/**
	 * Get assets related to this stream
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function getAssets($type)
	{
		// Get the link object
		$model      = FD::model( 'Stream' );
		$assets		= $model->getAssets($this->id, $type);

		return $assets;
	}
}
