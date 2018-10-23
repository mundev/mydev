<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class ThemesHelperListing extends ThemesHelperAbstract
{
	/**
	 * Renders the listing layout for album
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function album(SocialTableAlbum $album, $displayType = false)
	{
		$theme = ES::themes();
		$theme->set('displayType', $displayType);
		$theme->set('album', $album);
		$output = $theme->output('site/helpers/listing/album');

		return $output;
	}

	/**
	 * Renders the listing layout for pages
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function event(SocialEvent $event, $displayType = false)
	{
		$theme = ES::themes();
		$theme->set('displayType', $displayType);
		$theme->set('event', $event);
		$output = $theme->output('site/helpers/listing/event');

		return $output;
	}
	
	/**
	 * Renders the listing layout for users
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function user(SocialUser $user, $showRemoveFromList = false, $displayType = false)
	{
		$theme = ES::themes();

		$theme->set('displayType', $displayType);
		$theme->set('showRemoveFromList', $showRemoveFromList);
		$theme->set('user', $user);
		$output = $theme->output('site/helpers/listing/user');

		return $output;
	}

	/**
	 * Renders the listing layout for groups
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function group(SocialGroup $group, $displayType = false)
	{
		$theme = ES::themes();
		$theme->set('displayType', $displayType);
		$theme->set('group', $group);
		$output = $theme->output('site/helpers/listing/group');

		return $output;
	}

	/**
	 * Renders the listing layout for pages
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function page(SocialPage $page, $displayType = false)
	{
		$theme = ES::themes();
		$theme->set('displayType', $displayType);
		$theme->set('page', $page);
		$output = $theme->output('site/helpers/listing/page');

		return $output;
	}

	/**
	 * Renders the listing layout for photos
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function photo(SocialTablePhoto $photo, $displayType = false)
	{
		$theme = ES::themes();
		$theme->set('displayType', $displayType);
		$theme->set('photo', $photo);
		$output = $theme->output('site/helpers/listing/photo');

		return $output;
	}
}
