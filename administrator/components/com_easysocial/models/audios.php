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

jimport('joomla.application.component.model');

ES::import('admin:/includes/model');

class EasySocialModelAudios extends EasySocialModel
{
	public function __construct($config = array())
	{
		parent::__construct('audios', $config);
	}

	/**
	 * Initializes all the generic states from the form
	 *
	 * @since   2.1
	 * @access  public
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
	 * Retrieves a list of profiles that has access to a genre
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getGenreAccess($genreId, $type = 'create')
	{
		$db = ES::db();

		$sql = $db->sql();
		$sql->select('#__social_audios_genres_access');
		$sql->column('profile_id');
		$sql->where('genre_id', $genreId);
		$sql->where('type', $type);

		$db->setQuery($sql);

		$ids = $db->loadColumn();

		return $ids;
	}

	/**
	 * Determines if the genre is already exists
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function isGenreExists($genre)
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_audios_genres');
		$sql->where('alias', strtolower($genre));

		$db->setQuery($sql->getTotalSql());

		$result = $db->loadResult();

		return !empty($result);
	}

	/**
	 * Inserts new access for a cluster genre
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function insertGenreAccess($genreId, $type = 'create', $profiles = array())
	{
		$db = ES::db();

		// Delete all existing access type first
		$sql = $db->sql();
		$sql->delete('#__social_audios_genres_access');
		$sql->where('genre_id', $genreId);
		$sql->where('type', $type);

		$db->setQuery($sql);
		$db->Query();

		if (!$profiles) {
			return;
		}

		foreach ($profiles as $id) {
			$sql->clear();
			$sql->insert('#__social_audios_genres_access');
			$sql->values('genre_id', $genreId);
			$sql->values('type', $type);
			$sql->values('profile_id', $id);

			$db->setQuery($sql);
			$db->Query();
		}

		return true;
	}

	/**
	 * Retrieves the total user's audios available on site
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getTotalUserAudios($userId = null)
	{
		$user = ES::user($userId);
		$userId = $user->id;

		$sql = $this->db->sql();

		$query = "select count(1) from `#__social_audios` as a";
		$query .= " where a.state = " . $this->db->Quote(SOCIAL_AUDIO_PUBLISHED);
		$query .= " and a.user_id = " . $this->db->Quote($userId);
		$query .= " and a.`type` = " . $this->db->Quote('user');

		$sql->raw($query);
		$this->db->setQuery($sql);
		$total = (int) $this->db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the total pending audios available on site
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getTotalPendingAudios($userId = null)
	{
		$user = ES::user($userId);
		$userId = $user->id;

		$sql = $this->db->sql();

		$query = "select count(1) from `#__social_audios` as a";
		$query .= " where a.state = " . $this->db->Quote(SOCIAL_AUDIO_PENDING);
		$query .= " and a.user_id = " . $this->db->Quote($userId);
		$query .= " and a.`type` = " . $this->db->Quote('user');

		$sql->raw($query);
		$this->db->setQuery($sql);
		$total = (int) $this->db->loadResult();

		return $total;
	}


	/**
	 * Retrieves the total featured audios available on site
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getTotalFeaturedAudios($options = array())
	{
		$db = $this->db;
		$sql = $this->db->sql();
		$config = ES::config();


		$uid = $this->normalize($options, 'uid', null);
		$type = $this->normalize($options, 'type', null);
		$userid = $this->normalize($options, 'userid', null);
		$privacy = $this->normalize($options, 'privacy', true);

		if (!$config->get('privacy.enabled') && $type == 'user') {
			$privacy = false;
		}

		$query = "select count(1) from `#__social_audios` as a";

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


		$query .= " where a.state = " . $this->db->Quote(SOCIAL_AUDIO_PUBLISHED);
		$query .= " and a.featured = " . $this->db->Quote(SOCIAL_AUDIO_FEATURED);

		if ($userid) {
			$query .= " and a.user_id = " . $this->db->Quote($userid);
		}

		if ($uid && $type) {
			$query .= " and a.uid = " . $this->db->Quote($uid);
			$query .= " and a.type = " . $this->db->Quote($type);
		} else {
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
	 * Retrieves the total audios available on site
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getTotalAudios($options = array())
	{
		$db = $this->db;
		$sql = $this->db->sql();
		$config = ES::config();

		$uid = $this->normalize($options, 'uid', null);
		$type = $this->normalize($options, 'type', null);
		$userid = $this->normalize($options, 'userid', null);
		$state = $this->normalize($options, 'state', SOCIAL_AUDIO_PUBLISHED);
		$privacy = $this->normalize($options, 'privacy', true);
		$day = $this->normalize($options, 'day', false);

		// if (!$config->get('privacy.enabled') && $type == 'user') {
		// 	$privacy = false;
		// }

		$viewer = ES::user()->id;

		$cond = array();

		$query = "select count(1) from `#__social_audios` as a";

		if (!ES::user()->isSiteAdmin() && $privacy) {
			if (($type == 'user' || is_null($type)) && $config->get('privacy.enabled')) {
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

			$cond[] = "a.`type` = " . $this->db->Quote('user');;
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$cond[] = "`bus`.`id` IS NULL";
		}

		if ($day) {
			$start = $day . ' 00:00:01';
			$end = $day . ' 23:59:59';
			$cond[] = '(a.`created` >= ' . $this->db->Quote($start) . ' and a.`created` <= ' . $this->db->Quote($end) . ')';
		}

		if (!ES::user()->isSiteAdmin() && $privacy && $type != 'user' && !is_null($type)) {
			$tmp = "(";
			$tmp .= " (cls.`type` = 1) OR";
			$tmp .= " (cls.`type` > 1) AND " . $this->db->Quote($viewer) . " IN (select scn.`uid` from `#__social_clusters_nodes` as scn where scn.`cluster_id` = a.`uid` and scn.`type` = " . $this->db->Quote(SOCIAL_TYPE_USER) . " and scn.`state` = 1)";
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
	 * Retrieves the list of audios for the back end
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getItems()
	{
		$sql = $this->db->sql();

		$filter = $this->getState('filter');
		$search = $this->getState('search');

		$sql->select('#__social_audios');

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
			$sql->order($ordering, $direction);
		}

		// Set the total records for pagination.
		$this->setTotal($sql->getTotalSql());

		$result = $this->getData($sql);

		if (!$result) {
			return $result;
		}

		$audios = array();

		foreach ($result as $row) {

			$tmp = (array) $row;

			$row = ES::table('Audio');
			$row->bind($tmp);

			$audio = ES::audio($row);

			$audios[] = $audio;
		}

		return $audios;
	}

	private function genCounterTableWithPrivacy()
	{
		$db = ES::db();
		$config = ES::config();
		$viewer = ES::user()->id;

		$accessColumn = $this->getAccessColumn('access', 'ct');
		$accessCustomColumn = $this->getAccessColumn('customaccess', 'ct');
		$accessFieldColumn = $this->getAccessColumn('fieldaccess', 'ct');

		// $table = "(select * from (select ct.*, $accessColumn, $accessCustomColumn from `#__social_audios` as ct) as x";

		$table = "(select * from (select ct.*,";
		$table .= " $accessColumn, $accessCustomColumn";

		if ($config->get('users.privacy.field')) {
			$table .= ", $accessFieldColumn";
		}

		$table .= " from `#__social_audios` as ct) as x";

		// privacy here.
		$table .= " WHERE (";

		//public
		$table .= " (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_PUBLIC) . ") OR";

		//member
		$table .= " ((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ") AND (" . $viewer . " > 0)) OR ";

		//friends
		$table .= " ((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ") AND ((" . $this->generateIsFriendSQL('x.`user_id`', $viewer) . ") > 0)) OR ";

		//only me
		$table .= " ((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ") AND (x.`user_id` = " . $viewer . ")) OR ";

		// custom
		$table .= " ((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_CUSTOM) . ") AND (x.`custom_access` LIKE " . $db->Quote('%,' . $viewer . ',%') . "  )) OR ";

		// field
		if ($config->get('users.privacy.field')) {
			// field
			$fieldPrivacyQuery = '(select count(1) from `#__social_privacy_items_field` as fa';
			$fieldPrivacyQuery .= ' inner join `#__social_privacy_items` as fi on fi.`id` = fa.`uid` and fa.utype = ' . $db->Quote('item');
			$fieldPrivacyQuery .= ' inner join `#__social_fields` as ff on fa.`unique_key` = ff.`unique_key`';
			$fieldPrivacyQuery .= ' inner join `#__social_fields_data` as fd on ff.`id` = fd.`field_id`';
			$fieldPrivacyQuery .= ' where fi.`uid` = x.`id`';
			$fieldPrivacyQuery .= ' and fi.`type` = ' . $db->Quote('audios');
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
		$table .= "))";

		return $table;
	}

	/**
	 * Build a access SQL for audio
	 *
	 * @since   2.1
	 * @access  private
	 */
	private function getAccessColumn($type = 'access', $prefix = 'a')
	{
		$column = '';
		if ($type == 'access') {
			$column = "(select pri.value as `access` from `#__social_privacy_items` as pri";
			$column .= " left join `#__social_privacy_customize` as prc on pri.id = prc.uid and prc.utype = 'item' where pri.uid = " . $prefix . ".id and pri.`type` = 'audios'";
			$column .= " UNION ALL ";
			$column .= " select prm.value as `access`";
			$column .= " from `#__social_privacy_map` as prm";
			$column .= "  inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
			$column .= "  left join `#__social_privacy_customize` as prc on prm.id = prc.uid and prc.utype = 'user'";
			$column .= " where prm.uid = " . $prefix . ".user_id and prm.utype = 'user'";
			$column .= "  and pp.type = 'audios' and pp.rule = 'view'";
			$column .= " union all ";
			$column .= " select prm.value as `access`";
			$column .= " from `#__social_privacy_map` as prm";
			$column .= "  inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
			$column .= "  inner join `#__social_profiles_maps` pmp on prm.uid = pmp.profile_id";
			$column .= " where prm.utype = 'profiles' and pmp.user_id = " . $prefix . ".user_id";
			$column .= "  and pp.type = 'audios' and pp.rule = 'view'";
			$column .= " limit 1";
			$column .= ") as access";

		} else if ($type == 'customaccess') {

			$column = "(select concat(',', group_concat(prc.user_id SEPARATOR ','), ',') as `custom_access` from `#__social_privacy_items` as pri";
			$column .= " left join `#__social_privacy_customize` as prc on pri.id = prc.uid and prc.utype = 'item' where pri.uid = " . $prefix . ".id and pri.`type` = 'audios'";
			$column .= " UNION ALL ";
			$column .= " select concat(',', group_concat(prc.user_id SEPARATOR ','), ',') as `custom_access`";
			$column .= " from `#__social_privacy_map` as prm";
			$column .= "    inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
			$column .= "    left join `#__social_privacy_customize` as prc on prm.id = prc.uid and prc.utype = 'user'";
			$column .= " where prm.uid = " . $prefix . ".user_id and prm.utype = 'user'";
			$column .= "    and pp.type = 'audios' and pp.rule = 'view'";
			$column .= " limit 1";
			$column .= ") as custom_access";

		} else if ($type == 'fieldaccess') {
			$column = "(select `field_access` from `#__social_privacy_items` as pri where pri.`uid`= " . $prefix .".`id` and pri.`type` = 'audios') as field_access";
		}

		return $column;
	}

	/**
	 * Retrieves a list of audios from the site
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getAudiosForCron($options = array())
	{
		// search criteria
		$filter = $this->normalize($options, 'filter', '');
		$sort = $this->normalize($options, 'sort', 'latest');
		$limit = $this->normalize($options, 'limit', false);

		$db = ES::db();
		$sql = $db->sql();

		$query[] = "select a.* from `#__social_audios` as a";

		if ($filter == 'processing') {
			$query[] = 'WHERE a.`state`=' . $db->Quote(SOCIAL_AUDIO_PROCESSING);
		} else {
			$query[] = "where a.`state` = " . $db->Quote(SOCIAL_AUDIO_PENDING);
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

		$audios = array();

		if ($results) {
			foreach ($results as $row) {
				$audio = ES::audio($row->uid, $row->type);
				$audio->load($row);

				$audios[] = $audio;
			}
		}

		return $audios;
	}

	/**
	 * Retrieves the list of items which stored in Amazon
	 *
	 * @since   2.1.6
	 * @access  public
	 */
	public function getAudiosStoredExternally($storageType = 'amazon')
	{
		// Get the number of files to process at a time
		$config = ES::config();
		$limit = $config->get('storage.amazon.limit', 10);

		$db = ES::db();
		$sql = $db->sql();
		$sql->select('#__social_audios');
		$sql->where('storage', $storageType);
		$sql->limit($limit);

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieves a list of audios from the site
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getAudios($options = array())
	{
		$db = ES::db();
		$sql = $db->sql();
		$config = ES::config();

		$accessColumn = $this->getAccessColumn('access', 'a');
		$accessCustomColumn = $this->getAccessColumn('customaccess', 'a');
		$accessFieldColumn = $this->getAccessColumn('fieldaccess', 'a');

		$likeCountColumn = "(select count(1) from `#__social_likes` as exb where exb.uid = a.id and exb.type = 'audios.user.create') as likes";
		$commentCountColumn = "(select count(1) from `#__social_comments` as exb where exb.uid = a.id and exb.element = 'audios.user.create') as totalcomments";

		// search criteria
		$privacy = $this->normalize($options, 'privacy', true);

		// If privacy has been disabled, we should always disable privacy checks
		if (!$config->get('privacy.enabled')) {
			$privacy = false;
		}

		$filter = $this->normalize($options, 'filter', '');
		$featured = $this->normalize($options, 'featured', null);
		$genre = $this->normalize($options, 'genre', '');
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

		$query[] = "from `#__social_audios` as a";

		if ($type != 'user' && !is_null($type)) {
			$query[] = " inner join `#__social_clusters` as cls on a.`uid` = cls.`id` and a.`type` = cls.`cluster_type`";
		}

		if ($hashtags) {
			$query[] = " inner join`#__social_tags` as hashtag on a.`id` = hashtag.`target_id`";
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$query[] = 'LEFT JOIN `#__social_block_users` AS `bus`';
			$query[] = 'ON (a.`user_id` = bus.`user_id`';
			$query[] = 'AND bus.`target_id` = ' . $db->Quote(JFactory::getUser()->id);
			$query[] = 'OR a.`user_id` = bus.`target_id`';
			$query[] = 'AND bus.`user_id` = ' . $db->Quote(JFactory::getUser()->id) . ')';
		}

		if ($filter == 'pending') {
			$query[] = "where a.`state` = " . $db->Quote(SOCIAL_AUDIO_PENDING);
		} else if ($filter == 'processing') {
			$query[] = 'WHERE a.`state`=' . $db->Quote(SOCIAL_AUDIO_PROCESSING);
		} else {
			$query[] = "where a.`state` = " . $db->Quote(SOCIAL_AUDIO_PUBLISHED);
		}

		if ($uid && $type) {
			$query[] = 'AND a.`uid`=' . $db->Quote($uid);
			$query[] = 'AND a.`type`=' . $db->Quote($type);
		} else {
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

		if ($genre) {
			$query[] = "and a.`genre_id` = " . $db->Quote($genre);
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
						$tagQuery .= ($t < $totalTags - 1) ? ' (hashtag.`title` = ' . $db->Quote($tags[$t]) . ') OR ' : ' (hashtag.`title` = ' . $db->Quote($tags[$t]) . ')';
					}

					$query[] = 'AND (' . $tagQuery . ')';
				}

				$query[] = 'AND hashtag.`target_type` = ' . $db->Quote('audio');
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
			$query[] = "and a.`featured` = " . $db->Quote(SOCIAL_AUDIO_FEATURED);
		}

		// featured audio only
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

			$viewer = ES::user()->id;

			$query[] = ") as x";

			if ($type != 'user' && !is_null($type)) {
				// cluster privacy
				$query[] = " WHERE (";
				$query[] = " (x.`access` = 1) OR";
				$query[] = " (x.`access` > 1) AND " . $db->Quote($viewer) . " IN (select scn.`uid` from `#__social_clusters_nodes` as scn where scn.`cluster_id` = x.`uid` and scn.`type` = " . $db->Quote(SOCIAL_TYPE_USER) . " and scn.`state` = 1)";
				$query[] = ")";

			} else {

				// privacy here.
				$query[] = " WHERE (";

				//public
				$query[] = "(x.`access` = " . $db->Quote(SOCIAL_PRIVACY_PUBLIC) . ") OR";

				//member
				$query[] = "((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ") AND (" . $viewer . " > 0)) OR ";

				//friends
				$query[] = "((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ") AND ((" . $this->generateIsFriendSQL('x.`user_id`', $viewer) . ") > 0)) OR ";

				//only me
				$query[] = "((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ") AND (x.`user_id` = " . $viewer . ")) OR ";

				// custom
				$query[] = "((x.`access` = " . $db->Quote(SOCIAL_PRIVACY_CUSTOM) . ") AND (x.`custom_access` LIKE " . $db->Quote('%,' . $viewer . ',%') . "  )) OR ";

				// field
				if ($config->get('users.privacy.field')) {
					// field
					$fieldPrivacyQuery = '(select count(1) from `#__social_privacy_items_field` as fa';
					$fieldPrivacyQuery .= ' inner join `#__social_privacy_items` as fi on fi.`id` = fa.`uid` and fa.utype = ' . $db->Quote('item');
					$fieldPrivacyQuery .= ' inner join `#__social_fields` as ff on fa.`unique_key` = ff.`unique_key`';
					$fieldPrivacyQuery .= ' inner join `#__social_fields_data` as fd on ff.`id` = fd.`field_id`';
					$fieldPrivacyQuery .= ' where fi.`uid` = x.`id`';
					$fieldPrivacyQuery .= ' and fi.`type` = ' . $db->Quote('audios');
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

		if (!$maxlimit && $limit) {

			$totalQuery = implode(' ', $query);

			// Set the total number of items.
			$this->setTotal($totalQuery, true);
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
			$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

			$this->setState('limit', $limit);
			$this->setState('limitstart', $limitstart);

			$query[] = "limit $limitstart, $limit";
		}

		$query = implode(' ', $query);

		$sql->clear();
		$sql->raw($query);

		$this->db->setQuery($sql);
		$result = $this->db->loadObjectList();


		if (!$result) {
			return $result;
		}

		$audios = array();

		foreach ($result as $row) {
			$audio = ES::audio($row->uid, $row->type);
			$audio->load($row);

			$cluster = $audio->getCluster();

			$audio->creator = $audio->getAudioCreator($cluster);

			$audios[] = $audio;
		}

		return $audios;
	}

	/**
	 * Overriding parent getData method so that we can specify if we need the limit or not.
	 * If using the pagination query, child needs to use this method.
	 *
	 * @since   2.1
	 * @access  public
	 */
	protected function getData($query, $useLimit = true)
	{
		if ($useLimit) {
			return parent::getData($query);
		} else {
			$this->db->setQuery($query);
		}

		return $this->db->loadObjectList();
	}


	public function generateIsFriendSQL($source, $target)
	{
		$query = "select count(1) from `#__social_friends` where (`actor_id` = $source and `target_id` = $target) OR (`target_id` = $source and `actor_id` = $target) and `state` = 1";
		return $query;
	}


	/**
	 * Retrieves the default genre
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getDefaultGenre()
	{
		$db = $this->db;
		$sql = $db->sql();

		$sql->select('#__social_audios_genres');
		$sql->where('default', 1);

		$db->setQuery($sql);

		$result = $db->loadObject();

		if (!$result) {
			return false;
		}

		$genre = ES::table('AudioGenre');
		$genre->bind($result);

		return $genre;
	}

	/**
	 * Retrieves a list of audio genres from the site
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getGenres($options = array())
	{
		$db = ES::db();
		$sql = $db->sql();

		$query = array();
		$query[] = 'SELECT a.* FROM ' . $db->qn('#__social_audios_genres') . ' AS a';

		// Filter for respecting creation access
		$respectAccess = $this->normalize($options, 'respectAccess', false);
		$profileId = $this->normalize($options, 'profileId', 0);

		if ($respectAccess && $profileId) {
			$query[] = 'LEFT JOIN ' . $db->qn('#__social_audios_genres_access') . ' AS b';
			$query[] = 'ON a.id = b.genre_id';
		}

		$query[] = 'WHERE 1 ';

		// Filter for searching genres
		$search = $this->normalize($options, 'search', '');

		if ($search) {
			$query[] = 'AND ';
			$query[] = $db->qn('title') . ' LIKE ' . $db->Quote('%' . $search . '%');
		}

		// Respect genre creation access
		if ($respectAccess && $profileId) {
			$query[] = 'AND (';
			$query[] = '(b.`profile_id`=' . $db->Quote($profileId) . ')';
			$query[] = 'OR';
			$query[] = '(a.' . $db->qn('id') . ' NOT IN (SELECT `genre_id` FROM `#__social_audios_genres_access`))';
			$query[] = ')';
		}

		// Ensure that the audio are published
		$state = $this->normalize($options, 'state', true);

		// Ensure that all the genres are listed in backend
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

		$pagination = $this->normalize($options, 'pagination', true);

		// Set the total records for pagination.
		$totalSql = str_ireplace('a.*', 'COUNT(1)', $query);
		$this->setTotal($totalSql);

		// We need to go through our paginated library
		$result = $this->getData($query, $pagination);

		if (!$result) {
			return $result;
		}

		$genres = array();

		foreach ($result as $row) {
			$genre = ES::table('AudioGenre');
			$genre->bind($row);

			$genres[] = $genre;
		}

		return $genres;
	}

	/**
	 * Retrieves the total number of audios from a genre
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getTotalAudiosFromGenre($genreId, $cluster = false, $uid = null, $type = null)
	{
		$db = $this->db;
		$sql = $this->db->sql();
		$config = ES::config();

		$privacy = $config->get('privacy.enabled') ? true : false;

		$query = "select count(1) from `#__social_audios` as a";

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

		$query .= " where a.state = " . $this->db->Quote(SOCIAL_AUDIO_PUBLISHED);
		$query .= " and a.genre_id = " . $this->db->Quote($genreId);

		if (!is_null($uid) && !is_null($type)) {
			if ($type == SOCIAL_TYPE_USER) {
				$query .= " and a.user_id = " . $db->Quote($uid);
			}

			if ($cluster && !($cluster instanceof SocialUser)) {
				$query .= " and a.uid = " . $db->Quote($cluster->id);
				$query .= " and a.type = " . $db->Quote($cluster->getType());
			} else {
				$query .= " and a.`type` = " . $this->db->Quote('user');
			}
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$query .= " and `bus`.`id` IS NULL";
		}

		$sql->raw($query);
		$this->db->setQuery($sql);
		$total = $this->db->loadResult();

		return $total;
	}

	/**
	 * Determines if the audio should be associated with the stream item
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getStreamId($audioId, $verb)
	{
		$db     = ES::db();
		$sql    = $db->sql();

		$sql->select('#__social_stream_item', 'a');
		$sql->column('a.uid');
		$sql->where('a.context_type', SOCIAL_TYPE_AUDIOS);
		$sql->where('a.context_id', $audioId);
		$sql->where('a.verb', $verb);

		$db->setQuery($sql);

		$uid    = (int) $db->loadResult();

		return $uid;
	}

	/**
	 * Get audio from stream
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getStreamAudio($streamId)
	{
		$db = ES::db();

		$query = "select a.* from `#__social_audios` as a";
		$query .= " inner join `#__social_stream_item` as b on a.`id` = b.`context_id`";
		$query .= " where b.`uid` = " . $db->Quote($streamId);
		$query .= " and a.`state` = " . $db->Quote(SOCIAL_STATE_PUBLISHED);

		$db->setQuery($query);
		$row = $db->loadObject();

		$audio = ES::audio($row->uid, $row->type);
		$audio->load($row);

		return $audio;
	}

	/**
	 * Update audios genre ordering
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function updateGenresOrdering($id, $order)
	{
		$db = ES::db();
		$sql = $db->sql();

		$query = "update `#__social_audios_genres` set ordering = " . $db->Quote($order);
		$query .= " where id = " . $db->Quote($id);

		$sql->raw($query);

		$db->setQuery($sql);
		$state = $db->query();

		return $state;
	}

	/**
	 * update audio from stream
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function updateStreamAudio($streamId, $data)
	{
		$db = ES::db();
		$config = ES::config();

		$streamItem = ES::table('StreamItem');
		$streamItem->load(array('uid' => $streamId));

		// audio id before we update
		$existingId = $streamItem->context_id;
		$oldAudio = ES::table('audio');
		$oldAudio->load($existingId);

		// determine if this is a new audio
		$isNewAudio = false;
		if ($oldAudio->source == $data['source']) {
			$isNewAudio = $oldAudio->source == 'link' ? $oldAudio->path != $data['link'] : $existingId != $data['id'];
		} else {
			$isNewAudio = true;
		}

		if ($isNewAudio) {

			$audio = ES::audio();

			// Save options for the audio library
			$saveOptions = array();

			// If this is a link source, we just load up a new audio library
			if ($data['source'] == 'link') {
				$data['link'] = $audio->format($data['link']);
			}

			// If this is an audio upload, the id should be provided because audio are created first.
			if ($data['source'] == 'upload') {
				$id = $data['id'];

				$audio = ES::audio();
				$audio->load($id);

				// audio library needs to know that we're storing this from the story
				$saveOptions['story'] = true;

				// We cannot publish the audio if auto encoding is disabled
				if ($config->get('audio.autoencode')) {
					$data['state'] = SOCIAL_AUDIO_PUBLISHED;
				}
			}

			$data['uid'] = $oldAudio->uid;
			$data['type'] = $oldAudio->type;
			$data['user_id'] = $oldAudio->user_id;

			// update isnew flag
			$audio->table->isnew = 0;

			// saving new audio
			unset($data['id']);
			$audio->save($data, array(), $saveOptions);

			// update existing stream item
			$streamItem->context_id = $audio->id;
			$streamItem->store();

			// delete existing audio
			$oldAudio->delete();

		} else {
			// 1. update title, description and genre for existing audio.
			$oldAudio->title = $data['title'];
			$oldAudio->artist = $data['artist'];
			$oldAudio->album = $data['album'];
			$oldAudio->description = $data['description'];
			$oldAudio->genre_id = $data['genre_id'];

			$oldAudio->store();
		}

		return true;
	}

	/**
	 * Searches for a user's audio.
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function search($id, $term, $options = array())
	{
		$config = ES::config();
		$db = ES::db();

		$query = array();
		$query[] = 'SELECT a.' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__social_audios') . ' AS a';
		$query[] = 'WHERE a.' . $db->nameQuote('title') . ' LIKE ' . $db->Quote('%' . $term . '%');

		// If this is for playlist, we only allow uploaded audio
		if (isset($options['playlist']) && $options['playlist']) {
			$query[] = 'AND a.' . $db->nameQuote('source') . ' = ' . $db->Quote('upload');
			$query[] = 'AND a.' . $db->nameQuote('state') . ' = ' . $db->Quote(SOCIAL_STATE_PUBLISHED);
		}

		if (isset($options['exclude']) && $options['exclude']) {
			$excludeIds = '';

			if (!is_array($options['exclude'])) {
				$options['exclude'] = explode(',', $options['exclude']);
			}

			foreach ($options['exclude']  as $id) {
				$excludeIds .= (empty($excludeIds)) ? $db->Quote($id) : ', ' . $db->Quote($id);
			}

			$query[] = 'AND a.' . $db->nameQuote('id') . ' NOT IN (' . $excludeIds . ')';
		}

		// Glue back query.
		$query = implode(' ', $query);

		$limit = isset($options['limit']) ? $options['limit'] : false;
		$limitstart = isset($options['limitstart']) ? $options['limitstart'] : '10';

		if ($limit) {

			// get the total count.
			$replaceStr = 'SELECT b.' . $db->nameQuote('id') . ' FROM ';
			$totalSQL = str_replace($replaceStr, 'SELECT COUNT(1) FROM ', $query);

			$db->setQuery($totalSQL);
			$this->total = $db->loadResult();

			// now we append the limit
			$query .= " LIMIT $limitstart, $limit";
		}

		$db->setQuery($query);

		$results = $db->loadColumn();

		if (!$results) {
			return false;
		}
		$audios = array();

		foreach ($results as $result) {
			$table = ES::table('Audio');
			$table->load($result);
			$audios[] = ES::audio($table);
		}

		return $audios;
	}

	/**
	 * Retrieves a list of audios from a particular user for GDPR
	 *
	 * @since   2.2
	 * @access  public
	 */
	public function getAudiosGDPR($options = array())
	{
		$db = ES::db();
		$sql = $db->sql();

		$limit = $this->normalize($options, 'limit', false);
		$userid = $this->normalize($options, 'userid', null);

		$query = array();

		$query[] = "select *";
		$query[] = "from `#__social_audios`";
		$query[] = "where `user_id` = " . $db->Quote($userid);

		$exclusion = $this->normalize($options, 'exclusion', null);

		if ($exclusion) {

			$exclusion = ES::makeArray($exclusion);
			$exclusionIds = array();

			foreach ($exclusion as $exclusionId) {
				$exclusionIds[] = $db->Quote($exclusionId);
			}

			$exclusionIds = implode(',', $exclusionIds);

			$query[] = 'AND ' . $db->qn('id') . ' NOT IN (' . $exclusionIds . ')';
		}

		if ($limit) {
			$totalQuery = implode(' ', $query);

			// Set the total number of items.
			$this->setTotal($totalQuery, true);
		}

		// Get the limitstart.
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

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

		$audios = array();

		foreach ($result as $row) {
			$audio = ES::audio($row->uid, $row->type);
			$audio->load($row);

			$cluster = $audio->getCluster();

			$audio->creator = $audio->getAudioCreator($cluster);

			$audios[] = $audio;
		}

		return $audios;
	}

}
