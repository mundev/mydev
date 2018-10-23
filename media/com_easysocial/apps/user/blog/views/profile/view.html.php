<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class BlogViewProfile extends SocialAppsView
{
	public function display($userId = null , $docType = null)
	{
		// Check if EasyBlog really exists on the site
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		if (!JFile::exists($file)) {
			echo JText::_('APP_BLOG_EASYBLOG_NOT_INSTALLED');
			return;
		}

		require_once($file);

		// Get the user params
		$params = $this->getUserParams($userId);

		// Get the app params
		$appParams = $this->app->getParams();

		$config = EB::config();

		// Get the blog model
		$total = (int) $params->get('total', $appParams->get('total', 5));
		$includeTeam = $params->get('include_team', $appParams->get('include_team', true));

		$options = array('limit' => $total, 'includeTeam' => $includeTeam);

		$model = $this->getModel('Blog');
		$posts = $model->getItems($userId, $options);

		$maxLength = $appParams->get('profile_maxlength');

		$useEBTruncation = !$maxLength ? null : false;

		if ($posts) {
			foreach ($posts as $post) {
				$post->content = $post->getIntro(false, $useEBTruncation, 'intro', $maxLength, array('triggerPlugins' => $appParams->get('activate_plugin', false)));

				if (!is_null($useEBTruncation)) {
					$post->content = $this->truncateStreamContent($post->content, $maxLength);
				}

				$post->image = $post->getImage('medium', $appParams->get('show_placeholder', true));
			}
		}

		$user = ES::user($userId);

		// Generate the return url
		$return = ESR::profile(array('id' => $user->getAlias(), 'appId' => $this->app->getAlias()));
		$return = base64_encode($return);

		$composeLink = EB::composer()->getComposeUrl(array('returnUrl' => $return, 'return' => $return));

		$pagination = $model->getPagination();

		$pagination->setVar('option', 'com_easysocial');
		$pagination->setVar('view', 'profile');
		$pagination->setVar('id', $user->getAlias());
		$pagination->setVar('appId', $this->app->getAlias());

		$this->set('return', $return);
		$this->set('composeLink', $composeLink);
		$this->set('user', $user);
		$this->set('posts', $posts);
		$this->set('appParams', $appParams);
		$this->set('pagination', $pagination);

		echo parent::display('profile/default');
	}

	/**
	 * Truncate the stream item
	 *
	 * @since   1.0
	 * @access  public
	 */
	public function truncateStreamContent($content, $contentLength)
	{
		// Get the app params
		$params = $this->getParams();
		$truncateType = $params->get('truncation');

		if ($truncateType == 'chars') {

			// Remove uneccessary html tags to avoid unclosed html tags
			$content = strip_tags($content);

			// Remove blank spaces since the word calculation should not include new lines or blanks.
			$content = trim($content);

			// Truncate the content
			$content = EBString::substr(strip_tags($content), 0, $contentLength) . JText::_('COM_EASYSOCIAL_ELLIPSES');

		} else {

			$tag = false;
			$count = 0;
			$output = '';

			// Remove uneccessary html tags to avoid unclosed html tags
			$content = strip_tags($content);

			$chunks = preg_split("/([\s]+)/", $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

			foreach($chunks as $piece) {

				if (!$tag || stripos($piece, '>') !== false) {
					$tag = (bool) (strripos($piece, '>') < strripos($piece, '<'));
				}

				if (!$tag && trim($piece) == '') {
					$count++;
				}

				if ($count > $contentLength && !$tag) {
					break;
				}

				$output .= $piece;
			}

			unset($chunks);
			$content = $output . JText::_('COM_EASYSOCIAL_ELLIPSES');
		}

		return $content;
	}
}
