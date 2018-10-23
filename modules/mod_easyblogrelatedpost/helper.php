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

class modRelatedPostHelper extends EasyBlog
{
	public function __construct($modules)
	{
		parent::__construct();

		$this->lib = $modules;
		$this->params = $this->lib->params;
	}

	/**
	 * Retrieves a list of related posts against the current post being viewed
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPosts($id, $count = 0)
	{
		$model = EB::model('Blog');
		$posts = $model->getRelatedPosts($id, $count);
		$posts = $this->lib->processItems($posts);

		return $posts;
	}
}
