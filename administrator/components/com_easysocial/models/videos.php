<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.application.component.model');

FD::import('admin:/includes/model');

class EasySocialModelVideos extends EasySocialModel
{
	public function __construct($config = array())
	{
		parent::__construct('videos', $config);
	}

	/**
	 * Initializes all the generic states from the form
	 *
	 * @since   1.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function initStates()
	{
		$filter = $this->getUserStateFromRequest('filter', 'all');
		$ordering = $this->getUserStateFromRequest('ordering', 'id');
		$direction = $this->getUserStateFromRequest('direction', 'ASC');

		$this->setState('filter', $filter);

		parent::initStates();

		// Override the ordering behavior
		$this->setState('ordering', $ordering);
		$this->setState('direction', $direction);
	}

	/**
	 * Retrieves a list of profiles that has access to a category
	 *
	 * @since   1.4
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getCategoryAccess($categoryId, $type = 'create')
	{
		$db = ES::db();

		$sql = $db->sql();
		$sql->select('#__social_videos_categories_access');
		$sql->column('profile_id');
		$sql->where('category_id', $categoryId);
		$sql->where('type', $type);

		$db->setQuery($sql);

		$ids = $db->loadColumn();

		return $ids;
	}

	/**
	 * Inserts new access for a cluster category
	 *
	 * @since   1.4
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function insertCategoryAccess($categoryId, $type = 'create', $profiles = array())
	{
		$db = FD::db();

		// Delete all existing access type first
		$sql = $db->sql();
		$sql->delete('#__social_videos_categories_access');
		$sql->where('category_id', $categoryId);
		$sql->where('type', $type);

		$db->setQuery($sql);
		$db->Query();

		if (!$profiles) {
			return;
		}

		foreach ($profiles as $id) {
			$sql->clear();
			$sql->insert('#__social_videos_categories_access');
			$sql->values('category_id', $categoryId);
			$sql->values('type', $type);
			$sql->values('profile_id', $id);

			$db->setQuery($sql);
			$db->Query();
		}

		return true;
	}

	/**
	 * Retrieves the total featured videos available on site
	 *
	 * @since   1.4
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getTotalUserVideos($userId = null)
	{
		$user = ES::user($userId);
		$userId = $user->id;

		$sql = $this->db->sql();

		$query = "select count(1) from `#__social_videos` as a";
		$query .= " where a.state = " . $this->db->Quote(SOCIAL_VIDEO_PUBLISHED);
		$query .= " and a.user_id = " . $this->db->Quote($userId);
		$query .= " and a.`type` = " . $this->db->Quote('user');

		// $query .= " and (a.`type` = " . $this->db->Quote('user');
		// $query .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = " . $this->db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) . ") > 0))";

	// echo $query;exit;

		$sql->raw($query);
		$this->db->setQuery($sql);
		$total = (int) $this->db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the total featured videos available on site
	 *
	 * @since   1.4
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getTotalPendingVideos($userId = null)
	{
		$user = ES::user($userId);
		$userId = $user->id;

		$sql = $this->db->sql();

		// $sql->select('#__social_videos', 'a');
		// $sql->column('COUNT(1)');
		// $sql->where('state', SOCIAL_VIDEO_PENDING);
		// $sql->where('user_id', $userId);

		$query = "select count(1) from `#__social_videos` as a";
		$query .= " where a.state = " . $this->db->Quote(SOCIAL_VIDEO_PENDING);
		$query .= " and a.user_id = " . $this->db->Quote($userId);
		$query .= " and a.`type` = " . $this->db->Quote('user');

		// $query .= " and (a.`type` = " . $this->db->Quote('user');
		// $query .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = " . $this->db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) . ") > 0))";

		$sql->raw($query);
		$this->db->setQuery($sql);
		$total = (int) $this->db->loadResult();

		return $total;
	}


	/**
	 * Retrieves the total featured videos available on site
	 *
	 * @since   1.4
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getTotalFeaturedVideos($options = array())
	{
		$db = $this->db;
		$sql = $this->db->sql();
		$config = ES::config();

		$uid = $this->normalize($options, 'uid', null);
		$type = $this->normalize($options, 'type', null);
		$userid = $this->normalize($options, 'userid', null);
		$privacy = $this->normalize($options, 'privacy', true);


		$query = "select count(1) from `#__social_videos` as a";

		if ($privacy) {
			$tmpTable = $this->genCounterTableWithPrivacy();
			$query = "select count(1) from $tmpTable as a";
		}


		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$query .= ' LEFT JOIN `#__social_block_users` AS `bus`';
			$query .= ' ON (a.`user_id` = bus.`user_id`';
			$query .= ' AND bus.`target_id` = ' . $db->Quote(JFactory::getUser()->id);
			$query .= ' OR a.`user_id` = bus.`target_id`';
			$query .= ' AND bus.`user_id` = ' . $db->Quote(JFactory::getUser()->id) . ')';
		}

		$query .= " where a.state = " . $this->db->Quote(SOCIAL_VIDEO_PUBLISHED);
		$query .= " and a.featured = " . $this->db->Quote(SOCIAL_VIDEO_FEATURED);

		if ($userid) {
			$query .= " and a.user_id = " . $this->db->Quote($userid);
		}

		if ($uid && $type) {
			$query .= " and a.uid = " . $this->db->Quote($uid);
			$query .= " and a.type = " . $this->db->Quote($type);
		} else {
			// $query .= " and (a.`type` = " . $this->db->Quote('user');
			// $query .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = " . $this->db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) . ") > 0))";

			$query .= " and a.`type` = " . $this->db->Quote('user');
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$query .= " and `bus`.`id` IS NULL";
		}

		$sql->raw($query);
		$this->db->setQuery($sql);
		$total = (int) $this->db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the total videos available on site
	 *
	 * @since   1.4
	 * @access  public
	 */
	public function getTotalVideos($options = array())
	{
		$db = $this->db;
		$sql = $this->db->sql();
		$config = ES::config();

		$uid = $this->normalize($options, 'uid', null);
		$type = $this->normalize($options, 'type', null);
		$userid = $this->normalize($options, 'userid', null);
		$state = $this->normalize($options, 'state', SOCIAL_VIDEO_PUBLISHED);
		$privacy = $this->normalize($options, 'privacy', true);
		$day = $this->normalize($options, 'day', false);

		$viewer = ES::user()->id;

		$cond = array();

		$query = "select count(1) from `#__social_videos` as a";

		if (!FD::user()->isSiteAdmin() && $privacy) {
			if ($type == 'user' || is_null($type)) {
				$tmpTable = $this->genCounterTableWithPrivacy();
				$query = "select count(1) from $tmpTable as a";
			} else {
				$query .= " inner join `#__social_clusters` as cls on a.`uid` = cls.`id` and a.`type` = cls.`cluster_type`";
			}
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$query .= ' LEFT JOIN `#__social_block_users` AS `bus`';
			$query .= ' ON (a.`user_id` = bus.`user_id`';
			$query .= ' AND bus.`target_id` = ' . $db->Quote(JFactory::getUser()->id);
			$query .= ' OR a.`user_id` = bus.`target_id`';
			$query .= ' AND bus.`user_id` = ' . $db->Quote(JFactory::getUser()->id) . ')';
		}

		if ($state != 'all') {
			$cond[] = "a.state = " . $this->db->Quote($state);
		}


		if ($userid) {
			$cond[] = "a.user_id = " . $this->db->Quote($userid);
		}

		if ($uid && $type) {
			$cond[] = "a.uid = " . $this->db->Quote($uid);
			$cond[] = " a.type = " . $this->db->Quote($type);
		} else {
			// $tmp = "(a.`type` = " . $this->db->Quote('user');
			// $tmp .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = " . $this->db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) . ") > 0))";

			$cond[] = "a.`type` = " . $this->db->Quote('user');
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$cond[] = "`bus`.`id` IS NULL";
		}

		if ($day) {
			$start 	= $day . ' 00:00:01';
			$end 	= $day . ' 23:59:59';
			$cond[] = '(a.`created` >= ' . $this->db->Quote( $start ) . ' and a.`created` <= ' . $this->db->Quote( $end ) . ')';
		}

		if (!FD::user()->isSiteAdmin() && $privacy && $type != 'user' && !is_null($type)) {
			$tmp = "(";
			$tmp .= " (cls.`type` = 1) OR";
			$tmp .= " (cls.`type` > 1) AND " . $this->db->Quote($viewer) . " IN ( select scn.`uid` from `#__social_clusters_nodes` as scn where scn.`cluster_id` = a.`uid` and scn.`type` = " . $this->db->Quote(SOCIAL_TYPE_USER) . " and scn.`state` = 1)";
			$tmp .= ")";

			$cond[] = $tmp;
		}


		if ($cond) {
			if (count($cond) == 1) {
				$query .= " where " . $cond[0];
			} else if (count($cond) > 1) {

				$whereCond = array_shift($cond);

				$query .= " where " . $whereCond;
				$query .= " and " . implode(" and ", $cond);
			}
		}

		$sql->raw($query);

		$this->db->setQuery($sql);
		$total = (int) $this->db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the list of videos for the back end
	 *
	 * @since   1.4
	 * @access  public
	 */
	public function getItems()
	{
		$sql = $this->db->sql();

		$filter = $this->getState('filter');
		$search = $this->getState('search');

		$sql->select('#__social_videos');

		if ($filter != 'all') {
			$sql->where('state', $filter);
		}

		if ($search) {
			$sql->where('title', '%' . $search . '%', 'LIKE');
		}

		// Determine the ordering
		$ordering = $this->getState('ordering');

		if ($ordering) {
			$direction = $this->getState('direction');
			$sql->order($ordering , $direction);
		}

		// Set the total records for pagination.
		$this->setTotal($sql->getTotalSql());

		$result = $this->getData($sql);

		if (!$result) {
			return $result;
		}

		$videos = array();

		foreach ($result as $row) {

			$tmp = (array) $row;

			$row = ES::table('Video');
			$row->bind($tmp);

			$video = ES::video($row);

			$videos[] = $video;
		}

		return $videos;
	}

	private function genCounterTableWithPrivacy()
	{
		$db = ES::db();
		$config = ES::config();
		$viewer = FD::user()->id;

		$accessColumn = $this->getAccessColumn('access', 'ct');
		$accessCustomColumn = $this->getAccessColumn('customaccess', 'ct');
		$accessFieldColumn = $this->getAccessColumn('fieldaccess', 'ct');

		$table = "(select * from (select ct.*,";
		$table .= " $accessColumn, $accessCustomColumn";

		if ($config->get('users.privacy.field')) {
			$table .= ", $accessFieldColumn";
		}

		$table .= " from `#__social_videos` as ct) as x";
		// privacy here.
		$table .= " WHERE (";

		//public
		$table .= " (x.`access` = " . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ") OR";

		//member
		$table .= " ( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ") AND (" . $viewer . " > 0 ) ) OR ";

		if ($config->get('friends.enabled')) {
			//friends
			$table .= " ( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ") AND ( (" . $this->generateIsFriendSQL( 'x.`user_id`', $viewer ) . ") > 0 ) ) OR ";
		} else {
			// fall back to member
			$table .= " ( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ") AND (" . $viewer . " > 0 ) ) OR ";
		}

		//only me
		$table .= " ( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ") AND ( x.`user_id` = " . $viewer . " ) ) OR ";

		// custom
		$table .= " ( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_CUSTOM) . ") AND ( x.`custom_access` LIKE " . $db->Quote( '%,' . $viewer . ',%' ) . "    ) ) OR ";

		// field
		if ($config->get('users.privacy.field')) {
			// field
			$fieldPrivacyQuery = '(select count(1) from `#__social_privacy_items_field` as fa';
			$fieldPrivacyQuery .= ' inner join `#__social_privacy_items` as fi on fi.`id` = fa.`uid` and fa.utype = ' . $db->Quote('item');
			$fieldPrivacyQuery .= ' inner join `#__social_fields` as ff on fa.`unique_key` = ff.`unique_key`';
			$fieldPrivacyQuery .= ' inner join `#__social_fields_data` as fd on ff.`id` = fd.`field_id`';
			$fieldPrivacyQuery .= ' where fi.`uid` = x.`id`';
			$fieldPrivacyQuery .= ' and fi.`type` = ' . $db->Quote('videos');
			$fieldPrivacyQuery .= ' and fd.`uid` = ' . $db->Quote($viewer);
			$fieldPrivacyQuery .= ' and fd.`type` = ' . $db->Quote('user');
			$fieldPrivacyQuery .= ' and fd.`raw` LIKE concat(' . $db->Quote('%') . ',fa.`value`,' . $db->Quote('%') . '))';

			$table .= " ((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FIELD) . ") AND (x.`field_access` <= " . $fieldPrivacyQuery . ")) OR ";
		} else {
			$table .= " ((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FIELD) . ") AND (" . $viewer . " > 0)) OR ";
		}

		// my own items.
		$table .= " (x.`user_id` = " . $viewer . ")";

		// privacy checking end here.
		$table .= " ))";

		return $table;
	}


	private function getAccessColumn($type = 'access', $prefix = 'a')
	{
		$column = '';
		if ($type == 'access') {
			$column = "(select pri.value as `access` from `#__social_privacy_items` as pri";
			$column .= " left join `#__social_privacy_customize` as prc on pri.id = prc.uid and prc.utype = 'item' where pri.uid = " . $prefix . ".id and pri.`type` = 'videos'";
			$column .= " UNION ALL ";
			$column .= " select prm.value as `access`";
			$column .= " from `#__social_privacy_map` as prm";
			$column .= "  inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
			$column .= "  left join `#__social_privacy_customize` as prc on prm.id = prc.uid and prc.utype = 'user'";
			$column .= " where prm.uid = " . $prefix . ".user_id and prm.utype = 'user'";
			$column .= "  and pp.type = 'videos' and pp.rule = 'view'";
			$column .= " union all ";
			$column .= " select prm.value as `access`";
			$column .= " from `#__social_privacy_map` as prm";
			$column .= "  inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
			$column .= "  inner join `#__social_profiles_maps` pmp on prm.uid = pmp.profile_id";
			$column .= " where prm.utype = 'profiles' and pmp.user_id = " . $prefix . ".user_id";
			$column .= "  and pp.type = 'videos' and pp.rule = 'view'";
			$column .= " limit 1";
			$column .= ") as access";

		} else if ($type == 'customaccess') {

			$column = "(select concat(',', group_concat(prc.user_id SEPARATOR ','), ',') as `custom_access` from `#__social_privacy_items` as pri";
			$column .= " left join `#__social_privacy_customize` as prc on pri.id = prc.uid and prc.utype = 'item' where pri.uid = " . $prefix . ".id and pri.`type` = 'videos'";
			$column .= " UNION ALL ";
			$column .= " select concat(',', group_concat(prc.user_id SEPARATOR ','), ',') as `custom_access`";
			$column .= " from `#__social_privacy_map` as prm";
			$column .= "    inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
			$column .= "    left join `#__social_privacy_customize` as prc on prm.id = prc.uid and prc.utype = 'user'";
			$column .= " where prm.uid = " . $prefix . ".user_id and prm.utype = 'user'";
			$column .= "    and pp.type = 'videos' and pp.rule = 'view'";
			$column .= " limit 1";
			$column .= ") as custom_access";

		} else if ($type == 'fieldaccess') {
			$column = "(select `field_access` from `#__social_privacy_items` as pri where pri.`uid`= " . $prefix .".`id` and pri.`type` = 'videos' limit 1) as field_access";
		}

		return $column;
	}

	/**
	 * Retrieves a list of videos from the site
	 *
	 * @since   1.4
	 * @access  public
	 */
	public function getVideosForCron($options = array())
	{
		// search criteria
		$filter = $this->normalize($options, 'filter', '');
		$sort = $this->normalize($options, 'sort', 'latest');
		$limit = $this->normalize($options, 'limit', false);

		$db = ES::db();
		$sql = $db->sql();

		$query[] = "select a.* from `#__social_videos` as a";

		if ($filter == 'processing') {
			$query[] = 'WHERE a.`state`=' . $db->Quote(SOCIAL_VIDEO_PROCESSING);
		} else {
			$query[] = "where a.`state` = " . $db->Quote(SOCIAL_VIDEO_PENDING);
		}

		if ($sort) {
			switch ($sort) {
				case 'popular':
					$query[] = "order by a.hits desc";
					break;

				case 'alphabetical':
					$query[] = "order by a.title asc";
					break;

				case 'random':
					$query[] = "order by RAND()";
					break;

				case 'likes':
					$query[] = "order by likes desc";
					break;

				case 'commented':
					$query[] = "order by totalcomments desc";
					break;

				case 'latest':
				default:
					$query[] = "order by a.created desc";
					break;
			}
		}

		if ($limit) {
			$query[] = "limit $limit";
		}

		$query = implode(' ', $query);
		$sql->raw($query);

		$db->setQuery($sql);
		$results = $db->loadObjectList();

		$videos = array();

		if ($results) {
			foreach ($results as $row) {
				$video = ES::video($row->uid, $row->type);
				$video->load($row);

				$videos[] = $video;
			}
		}

		return $videos;
	}

	/**
	 * Retrieves the list of items which stored in Amazon
	 *
	 * @since   1.4.6
	 * @access  public
	 */
	public function getVideosStoredExternally($storageType = 'amazon')
	{
		// Get the number of files to process at a time
		$config = ES::config();
		$limit = $config->get('storage.amazon.limit', 10);

		$db = FD::db();
		$sql = $db->sql();
		$sql->select('#__social_videos');
		$sql->where('storage', $storageType);
		$sql->limit($limit);

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves a list of videos from the site
	 *
	 * @since   1.4
	 * @access  public
	 */
	public function getVideos($options = array())
	{
		$db = ES::db();
		$sql = $db->sql();
		$config = ES::config();

		$accessColumn = $this->getAccessColumn('access', 'a');
		$accessCustomColumn = $this->getAccessColumn('customaccess', 'a');
		$accessFieldColumn = $this->getAccessColumn('fieldaccess', 'a');

		$likeCountColumn = "(select count(1) from `#__social_likes` as exb where exb.uid = a.id and exb.type = 'videos.user.create') as likes";
		$commentCountColumn = "(select count(1) from `#__social_comments` as exb where exb.uid = a.id and exb.element = 'videos.user.create') as totalcomments";

		// search criteria
		$privacy = $this->normalize($options, 'privacy', true);

		// If privacy has been disabled, we should always disable privacy checks
		// if (!$config->get('privacy.enabled')) {
		// 	$privacy = false;
		// }

		$filter = $this->normalize($options, 'filter', '');
		$featured = $this->normalize($options, 'featured', null);
		$category = $this->normalize($options, 'category', '');
		$sort = $this->normalize($options, 'sort', 'latest');
		$maxlimit = $this->normalize($options, 'maxlimit', 0);
		$limit = $this->normalize($options, 'limit', false);
		$includeFeatured = $this->normalize($options, 'includeFeatured', null);

		$storage = $this->normalize($options, 'storage', false);
		$uid = $this->normalize($options, 'uid', null);
		$type = $this->normalize($options, 'type', null);
		$source = $this->normalize($options, 'source', false);

		$userid = $this->normalize($options, 'userid', null);

		$hashtags = $this->normalize($options, 'hashtags', null);

		$useLimit = true;

		$query = array();

		$isSiteAdmin = ES::user()->isSiteAdmin();

		if (!$isSiteAdmin && $privacy) {
			$query[] = "select * from (";
		}

		$query[] = "select a.*";

		if (!$isSiteAdmin && $privacy) {
			if ($type == 'user' || is_null($type)) {
				$query[] = ", $accessColumn, $accessCustomColumn";

				if ($config->get('users.privacy.field')) {
					$query[] = ", $accessFieldColumn";
				}

			} else {
				$query[] = ", cls.`type` as `access`";
			}
		}

		if ($sort == 'likes') {
			$query[] = ", $likeCountColumn";
		}

		if ($sort == 'commented') {
			$query[] = ", $commentCountColumn";
		}


		$orderTblPrefix = 'a';

		$query[] = "from `#__social_videos` as a";

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$query[] = 'LEFT JOIN `#__social_block_users` AS `bus`';
			$query[] = 'ON (a.`user_id` = bus.`user_id`';
			$query[] = 'AND bus.`target_id` = ' . $db->Quote(JFactory::getUser()->id);
			$query[] = 'OR a.`user_id` = bus.`target_id`';
			$query[] = 'AND bus.`user_id` = ' . $db->Quote(JFactory::getUser()->id) . ')';
		}


		if ($type != 'user' && !is_null($type)) {
			$query[] = " inner join `#__social_clusters` as cls on a.`uid` = cls.`id` and a.`type` = cls.`cluster_type`";
		}

		if ($hashtags) {
			$query[] = " inner join`#__social_tags` as hashtag on a.`id` = hashtag.`target_id`";
		}

		if ($filter == 'pending') {
			$query[] = "where a.`state` = " . $db->Quote(SOCIAL_VIDEO_PENDING);
		} else if ($filter == 'processing') {
			$query[] = 'WHERE a.`state`=' . $db->Quote(SOCIAL_VIDEO_PROCESSING);
		} else {
			$query[] = "where a.`state` = " . $db->Quote(SOCIAL_VIDEO_PUBLISHED);
		}

		if ($uid && $type) {
			$query[] = 'AND a.`uid`=' . $db->Quote($uid);
			$query[] = 'AND a.`type`=' . $db->Quote($type);
		} else {
			// $query[] = 'and (a.`type` = ' . $db->Quote('user');
			// $query[] = '    or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = '. $db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) .') > 0))';

			$query[] = 'and a.`type` = ' . $db->Quote('user');
		}

		if ($filter == 'mine') {
			$my = ES::user();
			$query[] = "and a.`user_id` = " . $db->Quote($my->id);
		}

		if ($filter == 'pending' && $userid) {
			$query[] = "and a.`user_id` = " . $db->Quote($userid);
		}

		if ($filter == SOCIAL_TYPE_USER) {
			$query[] = "and a.`user_id` = " . $db->Quote($userid);
		}

		if ($category) {
			$query[] = "and a.`category_id` = " . $db->Quote($category);
		}

		if ($hashtags) {
			$hashtagQuery = array();

			$tags = explode(',', $hashtags);

			if ($tags) {
				if (count($tags) == 1) {
					$query[] = 'AND hashtag.`title` =' . $db->Quote($tags[0]);
				} else {
					$totalTags = count($tags);
					$tagQuery = '';

					for ($t = 0; $t < $totalTags; $t++) {
						$tagQuery .= ($t < $totalTags - 1) ? ' ( hashtag.`title` = ' . $db->Quote($tags[$t]) . ') OR ' : ' ( hashtag.`title` = ' . $db->Quote($tags[$t]) . ')';
					}

					$query[] = 'AND ( ' . $tagQuery . ' )';
				}

				$query[] = 'AND hashtag.`target_type` = ' . $db->Quote('video');
			}
		}

		$exclusion = $this->normalize($options, 'exclusion', null);

		if ($exclusion) {

			$exclusion = ES::makeArray($exclusion);
			$exclusionIds = array();

			foreach ($exclusion as $exclusionId) {
				$exclusionIds[] = $db->Quote($exclusionId);
			}

			$exclusionIds = implode(',', $exclusionIds);

			$query[] = 'AND a.' . $db->qn('id') . ' NOT IN (' . $exclusionIds . ')';
		}

		// featured filtering
		if ($filter == 'featured') {
			$query[] = "and a.`featured` = " . $db->Quote(SOCIAL_VIDEO_FEATURED);
		}

		// featured video only
		if (!$includeFeatured && !is_null($featured) && $featured !== '') {
			$query[] = "and a.`featured` = " . $db->Quote((int) $featured);
		}

		if ($storage !== false) {
			$query[] = 'AND a.`storage` = ' . $db->Quote($storage);
		}

		if ($source !== false) {
			$query[] = 'AND a.`source`=' . $db->Quote($source);
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$query[] = "AND `bus`.`id` IS NULL";
		}


		if (!$isSiteAdmin && $privacy) {

			$orderTblPrefix = 'x';

			$viewer = FD::user()->id;

			$query[] = ") as x";

			if ($type != 'user' && !is_null($type)) {
				// cluster privacy
				$query[] = " WHERE (";
				$query[] = " (x.`access` = 1) OR";
				$query[] = " (x.`access` > 1) AND " . $db->Quote($viewer) . " IN ( select scn.`uid` from `#__social_clusters_nodes` as scn where scn.`cluster_id` = x.`uid` and scn.`type` = " . $db->Quote(SOCIAL_TYPE_USER) . " and scn.`state` = 1)";
				$query[] = ")";

			} else if ($config->get('privacy.enabled')) {

				// privacy here.
				$query[] = " WHERE (";

				//public
				$query[] = "(x.`access` = " . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ") OR";

				//member
				$query[] = "( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ") AND (" . $viewer . " > 0 ) ) OR ";

				if ($config->get('friends.enabled')) {
					//friends
					$query[] = "( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ") AND ( (" . $this->generateIsFriendSQL( 'x.`user_id`', $viewer ) . ") > 0 ) ) OR ";
				} else {
					// fall back to member
					$query[] = "( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ") AND (" . $viewer . " > 0 ) ) OR ";
				}


				//only me
				$query[] = "( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ") AND ( x.`user_id` = " . $viewer . " ) ) OR ";

				// custom
				$query[] = "( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_CUSTOM) . ") AND ( x.`custom_access` LIKE " . $db->Quote( '%,' . $viewer . ',%' ) . "    ) ) OR ";

				// field
				if ($config->get('users.privacy.field')) {
					// field
					$fieldPrivacyQuery = '(select count(1) from `#__social_privacy_items_field` as fa';
					$fieldPrivacyQuery .= ' inner join `#__social_privacy_items` as fi on fi.`id` = fa.`uid` and fa.utype = ' . $db->Quote('item');
					$fieldPrivacyQuery .= ' inner join `#__social_fields` as ff on fa.`unique_key` = ff.`unique_key`';
					$fieldPrivacyQuery .= ' inner join `#__social_fields_data` as fd on ff.`id` = fd.`field_id`';
					$fieldPrivacyQuery .= ' where fi.`uid` = x.`id`';
					$fieldPrivacyQuery .= ' and fi.`type` = ' . $db->Quote('videos');
					$fieldPrivacyQuery .= ' and fd.`uid` = ' . $db->Quote($viewer);
					$fieldPrivacyQuery .= ' and fd.`type` = ' . $db->Quote('user');
					$fieldPrivacyQuery .= ' and fd.`raw` LIKE concat(' . $db->Quote('%') . ',fa.`value`,' . $db->Quote('%') . '))';

					$query[] = "((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FIELD) . ") AND (x.`field_access` <= " . $fieldPrivacyQuery . ")) OR ";
				} else {
					$query[] = " ((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FIELD) . ") AND (" . $viewer . " > 0)) OR ";
				}

				// my own items.
				$query[] = "(x.`user_id` = " . $viewer . ")";

				// privacy checking end here.
				$query[] = ")";
			}
		}

		// var_dump($maxlimit, $limit);

		if (!$maxlimit && $limit) {

			$totalQuery = implode(' ', $query);

			// Set the total number of items.
			$this->setTotal( $totalQuery , true );
		}

		// the sorting must come after the privacy checking to have better sql performance.
		if ($sort) {
			switch ($sort) {
				case 'popular':
					$query[] = "order by $orderTblPrefix.hits desc";
					break;

				case 'alphabetical':
					$query[] = "order by $orderTblPrefix.title asc";
					break;

				case 'random':
					$query[] = "order by RAND()";
					break;

				case 'likes':
					$query[] = "order by likes desc";
					break;

				case 'commented':
					$query[] = "order by totalcomments desc";
					break;

				case 'latest':
				default:
					$query[] = "order by $orderTblPrefix.created desc";
					break;
			}
		}

		if ($maxlimit) {
			$useLimit = false;
			$query[] = "limit $maxlimit";
		} else if ($limit) {

			// Get the limitstart.
			$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
			$limitstart = ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState('limit', $limit);
			$this->setState('limitstart', $limitstart);

			$query[] = "limit $limitstart, $limit";
		}


		$query = implode(' ', $query);

		// echo $query;exit;

		$sql->clear();
		$sql->raw($query);

		// echo $sql;exit;


		$this->db->setQuery($sql);
		$result = $this->db->loadObjectList();


		if (!$result) {
			return $result;
		}

		$videos = array();

		foreach ($result as $row) {
			$video = ES::video($row->uid, $row->type);
			$video->load($row);

			$cluster = $video->getCluster();

			$video->creator = $video->getVideoCreator($cluster);

			$videos[] = $video;
		}

		return $videos;
	}

	/**
	 * Retrieves a list of videos from a particular user for GDPR
	 *
	 * @since   2.2
	 * @access  public
	 */
	public function getVideosGDPR($options = array())
	{
		$db = ES::db();
		$sql = $db->sql();
		$config = ES::config();

		$filter = $this->normalize($options, 'filter', '');
		$limit = $this->normalize($options, 'limit', false);
		$userid = $this->normalize($options, 'userid', null);
		$useLimit = true;

		$query = array();

		$query[] = "select a.*";
		$query[] = "from `#__social_videos` as a";
		$query[] = "where a.`user_id` = " . $db->Quote($userid);


		$exclusion = $this->normalize($options, 'exclusion', null);

		if ($exclusion) {

			$exclusion = ES::makeArray($exclusion);
			$exclusionIds = array();

			foreach ($exclusion as $exclusionId) {
				$exclusionIds[] = $db->Quote($exclusionId);
			}

			$exclusionIds = implode(',', $exclusionIds);

			$query[] = 'AND a.' . $db->qn('id') . ' NOT IN (' . $exclusionIds . ')';
		}

		if ($limit) {
			$totalQuery = implode(' ', $query);

			// Set the total number of items.
			$this->setTotal($totalQuery, true);
		}

		// Get the limitstart.
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		$limitstart = ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$query[] = "limit $limitstart, $limit";

		$query = implode(' ', $query);

		$sql->clear();
		$sql->raw($query);

		$this->db->setQuery($sql);
		$result = $this->db->loadObjectList();


		if (!$result) {
			return $result;
		}

		$videos = array();

		foreach ($result as $row) {
			$video = ES::video($row->uid, $row->type);
			$video->load($row);

			$cluster = $video->getCluster();

			$video->creator = $video->getVideoCreator($cluster);

			$videos[] = $video;
		}

		return $videos;
	}

	/**
	 * Overriding parent getData method so that we can specify if we need the limit or not.
	 *
	 * If using the pagination query, child needs to use this method.
	 *
	 * @since   1.4
	 * @access  public
	 */
	protected function getData($query , $useLimit = true)
	{
		if ($useLimit) {
			return parent::getData($query);
		} else {
			$this->db->setQuery($query);
		}

		return $this->db->loadObjectList();
	}


	public function generateIsFriendSQL( $source, $target )
	{
		$query = "select count(1) from `#__social_friends` where ( `actor_id` = $source and `target_id` = $target) OR (`target_id` = $source and `actor_id` = $target) and `state` = 1";
		return $query;
	}


	/**
	 * Retrieves the default category
	 *
	 * @since   1.4
	 * @access  public
	 */
	public function getDefaultCategory()
	{
		$db = $this->db;
		$sql = $db->sql();

		$sql->select('#__social_videos_categories');
		$sql->where('default', 1);

		$db->setQuery($sql);

		$result = $db->loadObject();

		if (!$result) {
			return false;
		}

		$category = ES::table('VideoCategory');
		$category->bind($result);

		return $category;
	}

	/**
	 * Retrieves a list of video categories from the site
	 *
	 * @since   1.4
	 * @access  public
	 */
	public function getCategories($options = array())
	{
		$db = ES::db();
		$sql = $db->sql();

		$query = array();
		$query[] = 'SELECT a.* FROM ' . $db->qn('#__social_videos_categories') . ' AS a';

		// Filter for respecting creation access
		$respectAccess = $this->normalize($options, 'respectAccess', false);
		$profileId = $this->normalize($options, 'profileId', 0);

		if ($respectAccess && $profileId) {
			$query[] = 'LEFT JOIN ' . $db->qn('#__social_videos_categories_access') . ' AS b';
			$query[] = 'ON a.id = b.category_id';
		}

		$query[] = 'WHERE 1 ';

		// Filter for searching categories
		$search = $this->normalize($options, 'search', '');

		if ($search) {
			$query[] = 'AND ';
			$query[] = $db->qn('title') . ' LIKE ' . $db->Quote('%' . $search . '%');
		}

		// Respect category creation access
		if ($respectAccess && $profileId) {
			$query[] = 'AND (';
			$query[] = '(b.`profile_id`=' . $db->Quote($profileId) . ')';
			$query[] = 'OR';
			$query[] = '(a.' . $db->qn('id') . ' NOT IN (SELECT `category_id` FROM `#__social_videos_categories_access`))';
			$query[] = ')';
		}

		// Ensure that the videos are published
		$state = $this->normalize($options, 'state', true);

		// Ensure that all the categories are listed in backend
		$adminView = $this->normalize($options, 'administrator', false);

		if (!$adminView) {
			$query[] = 'AND ' . $db->qn('state') . '=' . $db->Quote($state);
		}

		$ordering = $this->normalize($options, 'ordering', '');
		$direction = $this->normalize($options, 'direction', '');

		if ($ordering) {
			$query[] = ' ORDER BY ' . $db->qn($ordering) . ' ' . $direction;
		}

		$query = implode(' ', $query);

		// Determines if we need to paginate the result
		$paginate = $this->normalize($options, 'pagination', true);

		if ($paginate) {
			// Set the total records for pagination.
			$totalSql = str_ireplace('a.*', 'COUNT(1)', $query);
			$this->setTotal($totalSql);
		}

		// We need to go through our paginated library
		$result = $this->getData($query, $paginate);

		if (!$result) {
			return $result;
		}

		$categories = array();

		foreach ($result as $row) {
			$category = ES::table('VideoCategory');
			$category->bind($row);

			$categories[] = $category;
		}

		return $categories;
	}

	/**
	 * Retrieves the total number of videos from a category
	 *
	 * @since   1.4
	 * @access  public
	 */
	public function getTotalVideosFromCategory($categoryId, $cluster = false, $uid = null, $type = null)
	{
		$db = $this->db;
		$sql = $this->db->sql();
		$config = ES::config();

		$query = "select count(1) from `#__social_videos` as a";

		if (!ES::user()->isSiteAdmin() && $config->get('privacy.enabled')) {
			$tmpTable = $this->genCounterTableWithPrivacy();
			$query = "select count(1) from $tmpTable as a";
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$query .= ' LEFT JOIN `#__social_block_users` AS `bus`';
			$query .= ' ON (a.`user_id` = bus.`user_id`';
			$query .= ' AND bus.`target_id` = ' . $db->Quote(JFactory::getUser()->id);
			$query .= ' OR a.`user_id` = bus.`target_id`';
			$query .= ' AND bus.`user_id` = ' . $db->Quote(JFactory::getUser()->id) . ')';
		}


		$query .= " where a.state = " . $this->db->Quote(SOCIAL_VIDEO_PUBLISHED);
		$query .= " and a.category_id = " . $this->db->Quote($categoryId);

		if (!is_null($uid) && !is_null($type)) {
			if ($type == SOCIAL_TYPE_USER) {
				$query .= " and a.user_id = " . $db->Quote($uid);
			}

			if ($cluster && !($cluster instanceof SocialUser)) {
				$query .= " and a.uid = " . $db->Quote($cluster->id);
				$query .= " and a.type = " . $db->Quote($cluster->getType());
			} else {
				// $query .= " and (a.`type` = " . $this->db->Quote('user');
				// $query .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = " . $this->db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) . ") > 0))";

				$query .= " and a.`type` = " . $this->db->Quote('user');
			}
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$query .= " and `bus`.`id` IS NULL";
		}

		// echo $query;exit;

		$sql->raw($query);
		$this->db->setQuery($sql);
		$total = $this->db->loadResult();

		return $total;
	}

	/**
	 * Determines if the video should be associated with the stream item
	 *
	 * @since   1.4
	 * @access  public
	 */
	public function getStreamId($videoId, $verb)
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_stream_item', 'a');
		$sql->column('a.uid');
		$sql->where('a.context_type', SOCIAL_TYPE_VIDEOS);
		$sql->where('a.context_id', $videoId);
		$sql->where('a.verb', $verb);

		$db->setQuery($sql);

		$uid    = (int) $db->loadResult();

		return $uid;
	}

	/**
	 * Update videos categories ordering
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function updateCategoriesOrdering($id, $order)
	{
		$db = ES::db();
		$sql = $db->sql();

		$query = "update `#__social_videos_categories` set ordering = " . $db->Quote($order);
		$query .= " where id = " . $db->Quote($id);

		$sql->raw($query);

		$db->setQuery($sql);
		$state = $db->query();

		return $state;
	}

	/**
	 * Get video from stream
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getStreamVideo($streamId)
	{
		$db = ES::db();

		$query = "select a.* from `#__social_videos` as a";
		$query .= " inner join `#__social_stream_item` as b on a.`id` = b.`context_id`";
		$query .= " where b.`uid` = " . $db->Quote($streamId);
		$query .= " and a.`state` = " . $db->Quote(SOCIAL_STATE_PUBLISHED);

		$db->setQuery($query);
		$row = $db->loadObject();

		$video = ES::video($row->uid, $row->type);
		$video->load($row);

		return $video;
	}

	/**
	 * update video from stream
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function updateStreamVideo($streamId, $data)
	{
		$db = ES::db();
		$config = ES::config();

		$streamItem = ES::table('StreamItem');
		$streamItem->load(array('uid' => $streamId));

		// video id before we update
		$existingId = $streamItem->context_id;
		$oldVideo = ES::table('video');
		$oldVideo->load($existingId);

		// determine if this is a new video
		$isNewVideo = false;
		if ($oldVideo->source == $data['source']) {
			$isNewVideo = $oldVideo->source == 'link' ? $oldVideo->path != $data['link'] : $existingId != $data['id'];
		} else {
			$isNewVideo = true;
		}

		if ($isNewVideo) {

			// 1. update new video state.
			// 2. update existing stream item with new video id.
			// 3. remove old video.

			$video = ES::video();

			// Save options for the video library
			$saveOptions = array();

			// If this is a link source, we just load up a new video library
			if ($data['source'] == 'link') {
				$data['link'] = $video->format($data['link']);
			}

			// If this is a video upload, the id should be provided because videos are created first.
			if ($data['source'] == 'upload') {
				$id = $data['id'];

				$video = ES::video();
				$video->load($id);

				// Video library needs to know that we're storing this from the story
				$saveOptions['story'] = true;

				// We cannot publish the video if auto encoding is disabled
				if ($config->get('video.autoencode')) {
					$data['state'] = SOCIAL_VIDEO_PUBLISHED;
				}
			}

			$data['uid'] = $oldVideo->uid;
			$data['type'] = $oldVideo->type;
			$data['user_id'] = $oldVideo->user_id;

			// update isnew flag
			$video->table->isnew = 0;

			// saving new video
			unset($data['id']);
			$video->save($data, array(), $saveOptions);

			// update existing stream item
			$streamItem->context_id = $video->id;
			$streamItem->store();

			// delete existing video
			$oldVideo->delete();

		} else {
			// 1. update title, description and category for existing video.

			$oldVideo->title = $data['title'];
			$oldVideo->description = $data['description'];
			$oldVideo->category_id = $data['category_id'];

			$oldVideo->store();
		}

		return true;
	}

}
