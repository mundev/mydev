<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/apps/apps');

require_once(__DIR__ . '/helper.php');

class SocialUserAppTwitter extends SocialAppItem
{
	/**
	 * Performs the auto posting when the user submits the story
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function onAfterStorySave($stream , $streamItem , $template)
	{
		if (!$this->canAutopost()) {
			return;
		}

		$tweet = $this->input->get('twitter', false, 'bool');

		// Currently we only support contents
		if (!$tweet) {
			return;
		}
		
		$client = TwitterAppHelper::getClient();

		// Possible that user didn't authenticate with Twitter yet.
		if (!$client) {
			return;
		}

		$photos = JRequest::getVar('photos');
		$photo	= null;

		if ($photos) {
			// Get the first picture
			$photoId = $photos[0];

			$photo = ES::table('Photo');
			$photo->load($photoId);
		}

		// Get the single stream object
		$streamObj = ES::stream()->getItem($streamItem->uid , true);

		// We don't want to process anything if there's a privacy issue.
		if ($streamObj === true) {
			return;
		}

		$streamObj = $streamObj[0];

		// Check stream access.
		if ($streamObj->access > 30) {
			return;
		}

		$content = strip_tags($streamObj->content_raw);

		$link = false;

		// Check if the stream type is a link type
		if ($streamObj->type == 'links') {
			$link = $streamObj->getAssets();
			$link = $link[0]->get('link');
		}

		$client->push($content, $photo, $link);
	}

	/**
	 * Generates the auto posting form on the story form
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onPrepareStoryAutopost()
	{
		if (!$this->canAutopost()) {
			return;
		}

		$theme = ES::themes();
		$output = $theme->output('themes:/apps/user/twitter/autopost');

		return $output;
	}

	private function canAutopost()
	{
		$params = $this->getParams();

		if (!$params->get('autopost', true)) {
			return false;
		}
		
		// Check for twitter association with current logged in user.
		$isAssociated = $this->my->getOAuth('twitter');

		if (!$isAssociated) {
			return false;
		}

		return true;
	}
}
