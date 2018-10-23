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

ES::import('admin:/tables/table');
ES::import('admin:/includes/indexer/indexer');

class SocialTableAudio extends SocialTable implements ISocialIndexerTable
{
	public $id = null;
	public $artist = null;
	public $album = null;
	public $cover = null;
	public $title = null;
	public $description = null;
	public $duration = null;
	public $user_id = null;
	public $uid = null;
	public $type = null;
	public $created = null;
	public $state = null;
	public $isnew = null;
	public $featured = null;
	public $genre_id = null;
	public $hits = null;
	public $size = null;
	public $params = null;
	public $storage = 'joomla';
	public $path = null;
	public $original = null;
	public $file_title = null;
	public $source = 'link';
	public $post_as = null;
	public $playlist_id = null;
	public $albumart_source = 'upload';

	public function __construct($db)
	{
		parent::__construct('#__social_audios', 'id', $db);
	}

	public function syncIndex()
	{
	}

	public function deleteIndex()
	{
	}

	/**
	 * Determines if this audio is an upload source
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function isUpload()
	{
		return $this->source == SOCIAL_AUDIO_UPLOAD;
	}

	/**
	 * Determines if this audio is a link source
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function isLink()
	{
		return $this->source == SOCIAL_AUDIO_LINK;
	}

	/**
	 * Constructs the alias for this audio
	 *
	 * @since   2.1
	 * @access  public
	 */
	public function getAlias($withId = true)
	{
		$title = $this->title;
		$title = JFilterOutput::stringURLSafe($title);
		$alias = $title;

		if ($withId) {
			$alias = $this->id . ':' . $alias;
		}

		return $alias;
	}


	/**
	 * Retrieves the permalink of an audio
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function getPermalink($xhtml = true, $uid = null, $utype = null, $from = false)
	{
		$options = array('id' => $this->getAlias(), 'layout' => 'item');

		if ($this->uid && $this->type && $this->type != SOCIAL_TYPE_USER) {
			$cluster = ES::cluster($this->type, $this->uid);

			$options['uid'] = $cluster->getAlias();
			$options['type'] = $this->type;

		} else if ($uid && $utype) {
			$options['uid'] = $uid;
			$options['type'] = $utype;
		}

		if ($from !== false && $from) {
			$options['from'] = $from;
		}

		$url = FRoute::audios($options, $xhtml);

		return $url;
	}

	/**
	 * Retrieves the external permalink of an audio
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function getExternalPermalink()
	{
		$url = FRoute::audios(array('id' => $this->id, 'layout' => 'item', 'external' => true), false);

		return $url;
	}

	/**
	 * Allows caller to set the duration
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function setDuration(SocialAudioDuration $duration)
	{
		$this->duration = $duration->raw();

		// Save the audio object
		return $this->store();
	}

	/**
	 * Allows the caller to set the state to processing
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function processing()
	{
		$this->state = SOCIAL_AUDIO_PROCESSING;

		return $this->store();
	}

	/**
	 * Determines if the audio item is unfeatured
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function isUnfeatured()
	{
		return !$this->isFeatured();
	}

	/**
	 * Determines if the audio item is featured
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function isFeatured()
	{
		return $this->featured == SOCIAL_AUDIO_PUBLISHED;
	}
}
