<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/consumer.php');

class EasyBlogClientLinkedIn extends LinkedIn
{
	public function __construct($options = array())
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;

		$this->config = EB::config();
		$this->apiKey = $this->config->get('integrations_linkedin_api_key');
		$this->apiSecret = $this->config->get('integrations_linkedin_secret_key');

		// Determine the redirection url for both backend and frontend
		if (isset($options['backend']) && $options['backend']) {
			$this->redirect = JURI::root() . 'administrator/index.php?option=com_easyblog&task=linkedin.grant';
		} else {
			$this->redirect = JURI::root() . 'index.php?option=com_easyblog&view=auth&type=linkedin';
		}

		if ($this->input->get('system', false, 'bool')) {
			$this->redirect .= '&system=1';
		}

		$options = array('appKey' => $this->apiKey, 'appSecret' => $this->apiSecret, 'callbackUrl' => $this->redirect);

		parent::__construct($options);
	}

	/**
	 * Sets the callback url / redirection url
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setCallback($url)
	{
		return $this->setCallbackUrl($url);
	}

	/**
	 * Retrieves the request token from the query
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getRequestToken()
	{
		$request = $this->retrieveTokenRequest();

		$obj = new stdClass();
		$obj->token = $request['linkedin']['oauth_token'];
		$obj->secret = $request['linkedin']['oauth_token_secret'];

		return $obj;
	}

	/**
	 * Exchanges the request token with the access token
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAccess()
	{
		$access = parent::retrieveTokenAccess($this->auth_code);

		if (!$access) {
			return false;
		}

		$obj = new stdClass();

		// Convert to object
		if (is_string($access['linkedin'])) {
			$access['linkedin'] = json_decode($access['linkedin']);
		}

		$obj->token = $access['linkedin']->access_token;
		$obj->secret = true;
		$obj->params = '';
		$obj->expires = EB::date();

		// If the expiry date is given
		if (isset($access['linkedin']->expires_in)) {
			$expires = $access['linkedin']->expires_in;

			// Set the expiry date with proper date data
			$obj->expires = EB::date(strtotime('now') + $expires)->toSql();
		}

		return $obj;
	}

	/**
	 * Returns the verifier option. Since Facebook does not have oauth_verifier,
	 * The only way to validate this is through the 'code' query
	 *
	 * @return string	$verifier	Any string representation that we can verify it isn't empty.
	 **/
	public function getVerifier()
	{
		$verifier = $this->input->get('oauth_verifier', '', 'default');

		return $verifier;
	}

	public function getAuthorizeURL($redirect =  null)
	{
		$redirect = !is_null($redirect) ? $redirect : $this->redirect;

		$url = parent::_URL_AUTH_V2;
		$url .= '&client_id=' . $this->apiKey;
		$url .= '&redirect_uri=' . urlencode($redirect);
		$url .= '&state=' . $this->constructUserIdInState();

		return $url;
	}

	private function constructUserIdInState()
	{
		$user = EB::user();
		$state = parent::_USER_CONSTANT . $user->id;

		return $state;
	}

	public function getUserIdFromState($state)
	{
		$id = str_replace(parent::_USER_CONSTANT, '', $state);

		return $id;
	}

	/**
	 * Sets the request token
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setRequestToken($token, $secret)
	{
		$this->request_token = $token;
		$this->request_secret = $secret;
	}

	/**
	 * Set the authorization code
	 *
	 * @since	5.2.5
	 * @access	public
	 */
	public function setAuthCode($code)
	{
		$this->auth_code = $code;
	}

	/**
	 * Posts a message on linkedin
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function share(EasyBlogPost &$post, EasyBlogTableOAuth &$oauth, $system = true)
	{
		// Get the content
		$content = $post->getIntro(EASYBLOG_STRIP_TAGS);
		$content = strip_tags($content);

		// Get the blog image
		$image = $post->getImage('large', false, true);

		// If there's no blog image, try to get the image from the content
		if (!$image) {
			$fullcontent = $post->getContent('entry');
			$image = EB::string()->getImage($fullcontent);
		}

		// If there's still no image, just use the author's avatar
		if (!$image) {
			$image = $post->getAuthor()->getAvatar();
		}

		$options = array(
						'title' => $post->title,
						'comment' => $oauth->message,
						'submitted-url' => $post->getExternalPermalink(),
						'submitted-image-url' => $image,
						'description' => $content,
						'visibility' => 'anyone'
					);

		// Satisfy linkedin's criteria
		$options['description'] = trim(htmlspecialchars(strip_tags(stripslashes($options['description']))));
		$options['comment'] = htmlspecialchars(trim(strip_tags(stripslashes($options['comment']))));

		// Linkedin now restricts the message and text size.

		// To be safe, we'll use 380 characters instead of 400 due to multibyte characters from Joomla.
		$options['description'] = trim(EBString::substr($options['description'], 0, 380));
		$options['comment'] = EBString::substr($options['comment'], 0, 250);

		// Share to their account now
		$response = parent::sharePost('new', $options, true, false);
		$state = $response['success'] ? true : false;


		EB::oauth()->log($oauth, $post, $state, $response);

		// Determines if we should auto post to the company pages.
		if ($oauth->system && $this->config->get('integrations_linkedin_company')) {
			$companies = trim($this->config->get('integrations_linkedin_company'));

			if (!empty($companies)) {
				$companies = explode(',', $companies);

				foreach ($companies as $company) {
					$response = parent::sharePost('new', $options, true, false, array($company));

					$state = $response['success'] ? true : false;

					EB::oauth()->log($oauth, $post, $state, $response);
				}
			}
		}

		return true;
	}

	public function setAccess($access)
	{
		$access = EB::registry($access);
		return parent::setAccessToken($access->get('token'));
	}

	/**
	 * Revokes the linkedin access
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function revokeApp()
	{
		$result	= parent::revoke();

		return $result['success'] == true;
	}

	/**
	 * Retrieves the revoke access button
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getRevokeButton($return, $system = false, $userId = false)
	{
		$theme = EB::template();

		$uid = uniqid();

		// Generate the authorize url
		$url = JURI::root() . 'administrator/index.php?option=com_easyblog&task=linkedin.revoke';

		if ($system) {
			$url .= '&system=1';
		}

		$url .= '&return=' . base64_encode($return);

		if ($userId) {
			$url .= '&userId=' . $userId;
		}

		$theme->set('url', $url);
		$theme->set('system', $system);
		$theme->set('uid', $uid);

		$output = $theme->output('admin/oauth/linkedin/revoke');

		return $output;
	}

	/**
	 * Retrieves the loggin button for Facebook
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getLoginButton($return, $system = false, $userId = false)
	{
		$theme = EB::template();

		$uid = uniqid();

		// Generate the authorize url
		$url = JURI::root() . 'administrator/index.php?option=com_easyblog&task=linkedin.linkedinAuthorize';

		if ($system) {
			$url .= '&system=1';
		}

		if ($userId) {
			$url .= '&userId=' . $userId;
		}

		$theme->set('url', $url);
		$theme->set('system', $system);
		$theme->set('uid', $uid);
		$theme->set('return', $return);

		$output = $theme->output('admin/oauth/linkedin/button');

		return $output;
	}

	/**
	 * Process message
	 **/
	function processMessage( $message , $blog)
	{
		$config		= EB::config();
		$message 	= empty( $message ) ? $config->get( 'main_linkedin_message' ) : $message;
		$search		= array();
		$replace	= array();

		//replace title
		if (preg_match_all("/.*?(\\{title\\})/is", $message, $matches))
		{
			$search[] = '{title}';
			$replace[] = $blog->title;
		}

		//replace title
		if (preg_match_all("/.*?(\\{introtext\\})/is", $message, $matches))
		{
			$introtext	= empty($blog->intro)? '' : strip_tags( $blog->intro );
			$introtext	= EB::videos()->strip( $introtext );

			$search[] = '{introtext}';
			$replace[] = $introtext;
		}

		//replace category
		if (preg_match_all("/.*?(\\{category\\})/is", $message, $matches))
		{
			$category 	= EB::table('Category');
			$category->load($blog->category_id);

			$search[]	= '{category}';
			$replace[]	= $category->title;
		}

		//replace link
		if (preg_match_all("/.*?(\\{link\\})/is", $message, $matches))
		{
			$link = EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $blog->id, false, true);
			$search[]	= '{link}';
			$replace[]	= $link;
		}

		$message = EBString::str_ireplace($search, $replace, $message);

		return $message;
	}
}
