<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/linkedin.php');

class SocialConsumerLinkedIn extends LinkedIn
{
	protected $key = null;
	protected $secret = null;
	protected $callback = null;

	public function __construct($key, $secret, $callback)
	{
		$this->key = $key;
		$this->secret = $secret;
		
		$url = JRoute::_('index.php?option=com_easysocial&view=registration&layout=oauthDialog&client=linkedin', false);
		$url = ltrim($url, '/');

		// We need to use $uri->toString() because JURI::root() may contain a subfolder which will be duplicated
		// since $url already has the subfolder.
		$uri = JURI::getInstance();
		$callback = $uri->toString(array('scheme', 'host', 'port')) . '/' . $url;
		
		$this->callback = $callback;

		$options = array('appKey' => $key, 'appSecret' => $secret, 'callbackUrl' => $this->callback);
		parent::__construct($options);
	}

	/**
	 * Return client type
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function getType()
	{
		return 'linkedin';
	}

	/**
	 * Renders the revoke access button
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function getRevokeButton($callback)
	{
		$theme = ES::themes();
		$theme->set('callback', $callback);
		$output = $theme->output('site/linkedin/revoke');

		return $output;
	}

	/**
	 * Returns the verifier option. Since Facebook does not have oauth_verifier,
	 * The only way to validate this is through the 'code' query
	 *
	 * @since	2.1.0
	 * @access	public
	 **/
	public function getVerifier()
	{
		$verifier	= JRequest::getVar( 'oauth_verifier' , '' );
		return $verifier;
	}

	/**
	 * Retrieves the authorization url for Twitter
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function getAuthorizationURL($callback = '')
	{
		$data = parent::retrieveTokenRequest($callback);
		$token = $data['linkedin']['oauth_token'];
		$secret = $data['linkedin']['oauth_token_secret'];

		// We need to store the secret token on the session
		$session = JFactory::getSession();
		$session->set('linkedin.oauth_secret', $secret, SOCIAL_SESSION_NAMESPACE);

		return parent::_URL_AUTH . $token;
	}

	/**
	 * Determines if the current twitter user is already registered
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function isRegistered()
	{
		$table = ES::table('OAuth');
		$options = array('oauth_id' => $this->getUserId(), 'client' => 'linkedin');
		$state = $table->load($options);

		return $state;
	}

	/**
	 * Retrieves the user's unique id on Twitter
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getUserId()
	{
		$user = $this->getUser();

		return $user->id;
	}

	/**
	 * Refreshes the stored token
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function updateToken()
	{
		// We need to update with the new access token here.
		$session = JFactory::getSession();
		$accessToken = $session->get('linkedin.access', '', SOCIAL_SESSION_NAMESPACE);

		$user = $this->getUser();

		$table = ES::table('OAuth');
		$exists = $table->load(array('oauth_id' => $user->id, 'client' => 'linkedin'));

		if (!$exists) {
			return false;
		}

		// Try to update with the new token
		$table->token = $accessToken->token;
		$table->secret = $accessToken->secret;

		$state = $table->store();

		return $state;
	}

	/**
	 * Once the user has already granted access, we can now exchange the token with the access token
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function getAccess($verifier = '')
	{
		$token = $this->input->get('oauth_token', '', 'default');
		$session = JFactory::getSession();
		$secret = $session->get('linkedin.oauth_secret', '', SOCIAL_SESSION_NAMESPACE);

		// Try to retrieve the access token now
		$access = parent::retrieveTokenAccess($token, $secret, $verifier);

		$obj = new stdClass();
		$obj->token = $access['linkedin']['oauth_token'];
		$obj->secret = $access['linkedin']['oauth_token_secret'];
		$obj->expires = $access['linkedin']['oauth_expires_in'];

		return $obj;
	}

	/**
	 * Retrieves the person's profile picture
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getAvatar($meta = array(), $size = 'normal')
	{
		$avatar = false;

		if (isset($meta->pictureUrls)) {
			if (isset($meta->pictureUrls->_total) && $meta->pictureUrls->_total > 0) {
				$avatar = $meta->pictureUrls->values[0];
			}
		} else if (isset($meta->pictureUrl)) {
			$avatar = $meta->pictureUrl;
		}

		return $avatar;
	}

	/**
	 * Retrieves user's linkedin profile
	 *
	 * @since	2.1
	 * @access	public
	 */
	private function getUser()
	{
		// We want the results using JSON format
		$this->setResponseFormat('JSON');

		// Get the information needed from Linkedin
		$details = parent::profile('~:(id,formatted-name,first-name,last-name,picture-url,picture-urls::(original),email-address)');
		$result = json_decode($details['linkedin']);

		return $result;
	}

	/**
	 * Retrieve details of user from LinkedIn
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function getUserMeta()
	{
		// Empty user meta data
		$data = array();

		// Get the default profile
		$profile = $this->getDefaultProfile();

		// Assign the profileId first
		$data['profileId'] = $profile->id;

		// We need the basic id from LinkedIn
		$linkedinFields = array('id');

		// We let field decide which fields they want from facebook
		$fields = $profile->getCustomFields();
		$args = array(&$linkedinFields, &$this);
		$fieldsLib = ES::fields();
		$fieldsLib->trigger('onOAuthGetMetaFields', SOCIAL_FIELDS_GROUP_USER, $fields, $args);

		// Unique it to prevent multiple same fields request
		$linkedinFields = array_unique((array) $linkedinFields);

		// Implode it into a string for request
		$linkedinFields = implode(',', $linkedinFields);

		// Get the information needed from Linkedin
		$result = $this->getUser();

		$details = array();
		$details['email'] = $result->emailAddress;
		$details['name'] = $result->formattedName;
		$details['first_name'] = $result->firstName;
		$details['last_name'] = $result->lastName;

		if ($this->config->get('oauth.linkedin.registration.avatar')) {
			$details['avatar'] = $this->getAvatar($result);
		}
	
		// Give fields the ability to decorate user meta as well
		// This way fields can do extended api calls if the fields need it
		$args = array(&$details, &$this);
		$fieldsLib->trigger('onOAuthGetUserMeta', SOCIAL_FIELDS_GROUP_USER, $fields, $args);

		// We remap the id to oauth_id key
		$details['oauth_id'] = $result->id;

		// Merge Facebook details into data array
		$data = array_merge($data, $details);

		// Generate a random password for the user.
		$data['password'] = JUserHelper::genRandomPassword();

		return $data;
	}

	/**
	 * Allows caller to set the access
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function setAccess($access, $secret)
	{
		$this->token = array('oauth_token' => $access, 'oauth_token_secret' => $secret);
	}

	public function getUserName()
	{
		$result     = parent::api( '/me' );
		$data       = array( 'first_name' => $result[ 'first_name' ] , 'last_name' => $result[ 'last_name' ] );
		return $data;
	}

	/**
	 * Gets the login credentials for the Joomla site.
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function getLoginCredentials()
	{
		$table = ES::table("OAuth");
		$user = $this->getUser();
		
		$state = $table->load(array('oauth_id' => $user->id, 'client' => $this->getType()));

		if (!$state) {
			return false;
		}

		// Get the user object.
		$user = ES::user($table->uid);
		$credentials = array('username' => $user->username, 'password' => JUserHelper::genRandomPassword());

		return $credentials;
	}

	/**
	 * Renders the login button for LinkedIn
	 *
	 * @since	2.1
	 * @access	public
	 */
	public function getLoginButton($callback , $permissions = array() , $display = 'popup', $text = '', $size = 'btn-sm btn-block')
	{
		$config = ES::config();
		$jfbconnect = ES::jfbconnect();

		// Check if JFBConnect is enabled
		if ($jfbconnect->isEnabled()) {

			// We only return false here since the button already created through facebook library
			return;
		}		

		if (!$config->get('oauth.linkedin.registration.enabled')) {
			return;
		}
		
		$url = JRoute::_('index.php?option=com_easysocial&view=registration&layout=oauthRequestToken&client=linkedin&callback=' . base64_encode($callback), false);

		if (!$text) {
			$text = 'COM_EASYSOCIAL_SIGN_IN_WITH_LINKEDIN';
		}

		// only display icon without text
		if ($text == 'icon') {
			$text = '';
		}

		$theme = ES::themes();
		$theme->set('url', $url);
		$theme->set('size', $size);
		$theme->set('text', $text);

		$output = $theme->output('site/linkedin/button');

		return $output;
	}

	/**
	 * Get the default assigned Facebook profile
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getDefaultProfile()
	{
		$profileId = $this->config->get('oauth.linkedin.profile');

		$profile = FD::table('profile');
		$state = $profile->load( $profileId );

		// Test if profile id is set
		if( !$state )
		{
			// Try to get the default profile on the site.
			$profile 	= FD::table( 'Profile' );
			$state = $profile->load( array( 'default' => 1 ) );

			// If the profile id still cannot be found, just fetch the first item from the database
			if( !$state )
			{
				$model		= FD::model( 'Profiles' );
				$profile	= $model->setLimit( 1 )->getProfiles();
			}
		}

		return $profile;
	}

	/**
	 * Pushes data to LinkedIn
	 *
	 * @since	2.1.0
	 * @access	public
	 */
	public function push($message, $placeId = null, $photo = null, $link = null)
	{
		$options = array(
						'title' => $message,
						'comment' => $message,
						'submitted-url' => $link->get('link'),
						'description' => $message,
						'visibility' => 'anyone'
					);

		if (is_object($link)) {
			$options['submitted-url'] = $link->get('link');

			if ($photo) {
				$photoUrl = $photo->getSource('thumbnail');

				// If there is a cdn url, we need to replace it
				$cdn = ES::getCdnUrl();

				if ($cdn) {
					$photoUrl = str_ireplace($cdn, JURI::root(), $photoUrl);
				}
				
				$options['submitted-image-url'] = $photoUrl;
			}
		}

		// Satisfy linkedin's criteria
		$options['description'] = trim(htmlspecialchars(strip_tags(stripslashes($options['description']))));
		$options['comment'] = htmlspecialchars(trim(strip_tags(stripslashes($options['comment']))));
		$options['title'] = htmlspecialchars(trim(strip_tags(stripslashes($options['title']))));

		// Linkedin now restricts the message and text size.
		// To be safe, we'll use 380 characters instead of 400.
		$options['description'] = trim(JString::substr($options['description'], 0, 380));
		$options['comment'] = JString::substr($options['comment'], 0, 250);
		$options['title'] = JString::substr($options['title'], 0, 180);

		// Share to their account now
		$response = parent::share('new', $options, true, false);
		$state = isset($response['success']) && $response['success'] ? true : false;

		if (!$state) {
			return false;
		}

		return $state;
	}
}
