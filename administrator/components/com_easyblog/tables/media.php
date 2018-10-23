<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTableMedia extends EasyBlogTable
{
	public $id = null;
	public $filename = null;
	public $title = null;
	public $created_by = null;
	public $type = null;
	public $icon = null;
	public $preview = null;
	public $url = null;
	public $key = null;
	public $uri = null;
	public $place = null;
	public $parent = null;
	public $params = null;
	public $created = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_media', 'id', $db);
	}

	/**
	 * Retrieves the preview link
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPreview()
	{
		// Only images has preview
		if ($this->type != 'image') {
			return $this->icon;
		}

		return $this->preview;
	}

	/**
	 * Renders a list of variations for a media item.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getVariations()
	{
		// Only images has variations
		if ($this->type != 'image') {
			return array();
		}

		$params = $this->getParams();
		$variations = (array) $params->get('variations');

		return $variations;
	}

	/**
	 * Allows caller to pass in a list of variations to be updated
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function updateVariations($variations)
	{
		$params = json_decode($this->params);
		$params->variations = $variations;

		$this->params = json_encode($params);
		
		return $this->store();
	}
}
