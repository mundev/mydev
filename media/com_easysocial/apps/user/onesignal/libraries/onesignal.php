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

class OneSignal
{
	private $app_id = null;
	private $rest_key = null;

	public function __construct($params)
	{
		$this->params = $params;
		$this->app_id = $params->get('app_id');
		$this->rest_key = $params->get('rest_key');
	}

	/**
	 * Determines if cleantalk is enabled
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isEnabled()
	{
		if ($this->app_id && $this->rest_key) {
			return true;
		}
		
		return false;
	}

	/**
	 * Create a filter rule
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function createFilter($field, $key, $relation, $value)
	{
		$filter = new stdClass();
		$filter->field = $field;
		$filter->key = $key;
		$filter->relation = $relation;
		$filter->value = $value;

		return $filter;
	}

	/**
	 * Create an operator rule
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function createOperator($operator)
	{
		$filter = new stdClass();
		$filter->operator = $operator;

		return $filter;
	}

	/**
	 * Notifies the push api (onesignal)
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function notify(SocialUser $user, $options = array())
	{
		$title = $options['title'];
		$contents = $options['contents'];
		$permalink = $options['permalink'];
		$icon = $options['icon'];

		// Prepare the contents to be pushed
		$heading = array("en" => $title);
		$content = array("en" => $contents);
		
		$filters = array();
		$filters[] = $this->createFilter('tag', 'id', '=', $user->id);

		$fields = array(
						'app_id' => $this->app_id,
						'headings' => $heading,
						'contents' => $content,
						'url' => $permalink,
						'chrome_web_icon' => $icon
				);

		// Send to all
		// $fields['included_segments'] = 'All';

		// Send to segments		
		$fields['filters'] = $filters;

		$fields = json_encode($fields);

		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic ' . $this->rest_key));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);

		curl_close($ch);
		
		return $response;
	}

}
