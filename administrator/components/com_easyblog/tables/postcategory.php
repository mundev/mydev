<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTablePostCategory extends EasyBlogTable
{
	public $id = null;
	public $post_id = null;
	public $category_id = null;
    public $primary = null;
    
	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_post_category', 'id', $db);
	}
}
