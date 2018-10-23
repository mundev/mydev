<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogContributorEasySocialPage extends EasyBlogContributorAbstract
{
	public $page = null;

	public function __construct($id)
	{
		parent::__construct($id, 'page');

		if (!EB::easysocial()->exists()) {
			return;
		}

		$this->page = ES::page($id);
	}

	public function getHeader()
	{
		$output = EB::easysocial()->renderMiniHeader($this->page);
		echo $output;
		return $output;
	}

	public function getAvatar()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		return $this->page->getAvatar();
	}

	public function getTitle()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		return $this->page->getName();
	}

	public function getPermalink()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		return $this->page->getPermalink();
	}

	public function canDelete()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}

		$allowed = $this->page->isAdmin();

		return $allowed;
	}

	public function canView()
	{
		if (!EB::easysocial()->exists()) {
			return;
		}
		
		$canView = $this->page->canViewItem();

		return $canView;
	}
}