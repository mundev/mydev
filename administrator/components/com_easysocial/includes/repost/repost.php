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

class SocialRepost extends EasySocial
{
	public $uid = null;
	public $element 	= null;
	public $group 		= null;
	public $cluster_id = null;
	public $cluster_type = null;

	public function __construct($uid, $element, $group = SOCIAL_APPS_GROUP_USER, $clusterId = 0, $clusterType = '')
	{
		parent::__construct();

		$this->uid = $uid;
		$this->element = $element;
		$this->group = $group;
		$this->cluster_id = $clusterId;
		$this->cluster_type = $clusterType;
	}

	public static function factory($uid, $element, $group = SOCIAL_APPS_GROUP_USER, $clusterId = 0, $clusterType = '')
	{
		return new self($uid, $element, $group, $clusterId, $clusterType);
	}

	public function setCluster($clusterId, $clusterType)
	{
		$this->cluster_id 	= $clusterId;
		$this->cluster_type = $clusterType;
	}

	public function getCluster()
	{
		// Get the cluster object
        $cluster = ES::cluster($this->cluster_type, $this->cluster_id);

        return $cluster;
	}


	public function add($userId = null, $content = null)
	{
		if (empty($userId)) {
			$userId = ES::user()->id;
		}

		$model = ES::model('Repost');
		$state = $model->add($this->uid, $this->formKeys($this->element, $this->group), $userId, $content);

		return $state;
	}

	public function delete($userId = null)
	{
		if (empty($userId))
		{
			$userId = ES::user()->id;
		}

		$model = ES::model('Repost');
		$state = $model->delete($this->uid, $this->formKeys($this->element, $this->group), $userId);

		return $state;
	}

	public function isShared($userId)
	{
		$element = $this->formKeys($this->element, $this->group);

		$table = ES::table('Share');
		$table->load(array('uid' => $this->uid, 'element' => $element, 'user_id' => $userId));

		if ($table->id)
		{
			// already shared before. js return true.
			return true;
		}

		return false;
	}

	private function formKeys($element, $group)
	{
		return $element . '.' . $group;
	}

	public function getCount()
	{
		$model 	= ES::model('Repost');
		$cnt 	= $model->getCount($this->uid, $this->formKeys($this->element, $this->group));

		return $cnt;
	}

	/**
	 * Alias method for getButton
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function button($label = null)
	{
		return $this->getButton($label);
	}

	/**
	 * Retrieves the repost link
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getButton($label = null)
	{
		$my = ES::user();

		if (!$label) {
			$label = JText::_('COM_EASYSOCIAL_REPOST');
		}

		$themes = ES::get('Themes');
		$themes->set('text', $label);
		$themes->set('my', $my);
		$themes->set('uid', $this->uid);
		$themes->set('element', $this->element);
		$themes->set('group', $this->group);
		$themes->set('clusterId', $this->cluster_id);
		$themes->set('clusterType', $this->cluster_type);

 		$html = $themes->output('site/repost/action');
 		return $html;
	}

	/**
	 * Retrieves the html of the repost
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function counter()
	{
		// Get the count.
		$count = 0;
		$text = '';

		$count = $this->getCount();
		$text = JText::sprintf('COM_EASYSOCIAL_REPOST' . ES::language()->pluralize($count, true)->getString(), $count);

		$theme = ES::themes();
		$theme->set('text', $text);
		$theme->set('uid', $this->uid);
		$theme->set('element', $this->element);
		$theme->set('group', $this->group);
		$theme->set('count', $count);

		$output = $theme->output('site/repost/counter');

		return $output;
	}

	/**
	 * Deprecated. Use @counter instead
	 *
	 * @deprecated	2.0
	 * @access	public
	 */
	public function toHTML()
	{
		return $this->counter();
	}

	/**
	 * Displays the sharing code on the page.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getHTML()
	{
		// Get the count.
		$count = 0;
		$text = '';

		$count = $this->getCount();

		// $text = JText::sprintf('COM_EASYSOCIAL_REPOST_COUNT_SHARED', $count);
		$cntPluralize = ES::get('Language')->pluralize($count, true)->getString();
		$text = JText::sprintf('COM_EASYSOCIAL_REPOST' . $cntPluralize, $count);

		$themes = ES::get('Themes');
		$themes->set('text', $text);
		$themes->set('uid', $this->uid);
		$themes->set('element', $this->element);
		$themes->set('group', $this->group);
		$themes->set('count', $count);

 		$html = $themes->output('site/repost/item');
		return $html;
	}

	/**
	 * Retrieves the preview item
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function preview()
	{
		$file = dirname(__FILE__) . '/helpers/' . $this->element . '.php';

		if (JFile::exists($file)) {
			require_once($file);

			// Get class name.
			$className = 'SocialRepostHelper' . ucfirst($this->element);

			// Instantiate the helper object.
			$helper = new $className($this->uid, $this->group, $this->element);

			$content = $helper->getContent();
			$title = $helper->getTitle();

			$themes = ES::get('Themes');
			$themes->set('title', $title);
			$themes->set('content', $content);

	 		$html = $themes->output('site/repost/preview');
	 		return $html;
		}

		return false;
	}

}
