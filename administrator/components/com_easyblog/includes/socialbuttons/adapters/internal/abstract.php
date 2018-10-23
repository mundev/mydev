<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogInternalButtons extends EasyBlog
{
	protected $name = null;
	protected $post = null;
	protected $title = null;
	protected $permalink = null;

	public function __construct($name, EasyBlogPost $post)
	{
		$this->name = $name;
		$this->post = $post;
		$this->title = JText::_('COM_EASYBLOG_BUTTON_' . ucfirst($name));
		$this->permalink = $post->getExternalPermalink();

		parent::__construct();
	}

	/**
	 * Provides the button name identifier
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Provides the button title
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Renders the icon class for the button
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getIcon()
	{
		$icon = 'fa fa-' . $this->name;

		return $icon;
	}

	/**
	 * Retrieves the post title
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPostTitle()
	{
		$title = EBString::trim(urlencode($this->post->title));

		return $title;
	}	
}
