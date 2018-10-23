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

ES::import('admin:/tables/table');

class SocialTableTagsFilter extends SocialTable
{
	/**
	 * The unique id.
	 * @var	int
	 */
	public $id = null;

	/**
	 * Uid - user id
	 * @var	int
	 */
	public $cid = null;

	/**
	 * Type
	 * @var	string
	 */
	public $filter_type = null;

	/**
	 * Type
	 * @var	string
	 */
	public $cluster_type = null;

	/**
	 * Title
	 * @var	int
	 */
	public $title = null;

	/**
	 * The alias of the tag filter
	 * @var	string
	 */
	public $alias = null;

	/**
	 * Class Constructor.
	 *
	 * @since	1.1
	 * @access	public
	 */
	public function __construct($db)
	{
		parent::__construct('#__social_tags_filter', 'id', $db);
	}

	/**
	 * Override parent's store function
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($updateNulls = false)
	{
		// Generate an alias for this filter if it is empty.
		if (empty($this->alias)) {
			$alias = $this->title;
			$alias = JFilterOutput::stringURLSafe($alias);
			$tmp = $alias;

			$i = 1;

			while ($this->aliasExists($alias)) {
				$alias = $tmp . '-' . $i;
				$i++;
			}

			$this->alias = $alias;
		}

		$state = parent::store($updateNulls);
	}

	/**
	 * Checks the database to see if there are any same alias
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function aliasExists($alias)
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_tags_filter');
		$sql->column('COUNT(1)', 'total');
		$sql->where('alias', $alias);

		$db->setQuery($sql);

		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Retrieves the alias of this filter
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		$alias = $this->id . '-' . $this->alias;

		return $alias;
	}

	public function getHashTag($display = false)
	{
		if (! $this->id) {
			return '';
		}

		$filterItem = ES::table('TagsFilterItem');
		$filterItem->load(array('filter_id' => $this->id, 'type' => 'hashtag'));

		if ($display) {
			//for display
			$filterItem->content = str_replace(',', ', #', $filterItem->content);
			$filterItem->content = '#' . $filterItem->content;

			return $filterItem->content;
		} else {
			return $filterItem->content;
		}
	}

	/**
	 * Function to get hashtags display link
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getHashtagsLink($hashtags, $type, $clusterId = null, $clusterType = null)
	{
		$hashtags = explode(',', $hashtags);

		$linkOptions = array();

		if ($hashtags) {

			// Cluster link
			if ($clusterId && $clusterType) {
				$cluster = ES::cluster($clusterType, $clusterId);
				$uid = $cluster->getAlias();

				if ($uid && $clusterType) {
					$linkOptions['uid'] = $uid;
					$linkOptions['type'] = $clusterType;
				}
			}

			if (count($hashtags) == 1) {
				$linkOptions['hashtag'] = $hashtags[0];
				$tagLink = "<a href='" . ESR::$type($linkOptions) . "'>" . $hashtags[0] . "</a>";

				return JText::sprintf('COM_EASYSOCIAL_VIDEOS_TAGGED_WITH', $tagLink);	
			} else {
				$text = JText::_('COM_EASYSOCIAL_VIDEOS_TAGGED_WITH_MULTIPLE');

				foreach ($hashtags as $hashtag) {
					// Re-assign hashtag properties.
					$linkOptions['hashtag'] = $hashtag;

					$link = ESR::$type($linkOptions);
					$text .= ' <a href="' . $link . '">#' . $hashtag . '</a>';
					$text .= ',';
				}

				$text = rtrim($text, ',');
			}
		}

		return $text;
	}

	/**
	 * Get edit filter permalink
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getEditLink()
	{	
		$options = array('filter' => 'filterForm', 'id' => $this->id);

		$filterType = $this->filter_type;

		if ($this->cid) {

			$options['uid'] = $this->cid;

			// Check for cluster
			$cluster = array('group', 'event', 'page');

			if (in_array($this->cluster_type, $cluster)) {
				$cluster = ES::cluster($this->cluster_type, $this->cid);

				$options['uid'] = $cluster->getAlias();
				$options['type'] = $cluster->cluster_type;
			}
		}

		$editLink = ESR::$filterType($options);

		return $editLink;
	}

	public function deleteItem($type = '')
	{
		if (! $this->id)
			return;

		$db = ES::db();
		$sql = $db->sql();

		$query = 'delete from `#__social_tags_filter_item` where `filter_id` = ' . $db->Quote($this->id);

		if ($type) {
			$query .= ' and `type` = ' . $db->Quote($type);
		}

		$sql->raw($query);
		$db->setQuery($sql);

		$db->query();

		return true;
	}
}