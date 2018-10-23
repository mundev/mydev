<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

######################################## 
##### Configuration options.
########################################

// This should not contain http:// or https://
$host = 'site.com';

// If your site runs on https, please change this to true
$https = false;
########################################

// In case the host name is not configured.
if ($host == 'site.com') {
	echo "Please change the \$host value in the cron.php file to your correct url";
	return;
}

function connect($host, $url, $ssl = false)
{
	// check whether the curl function is exist or not.
	if (function_exists('curl_version')) {
		connectCurl($host, $url, $ssl);
		return;
	}

	// default
	connectFwrite($host, $url);
	return;
}

function connectCurl($host, $url, $ssl = false)
{
	$protocol = $ssl ? 'https://' : 'http://';
	$url = $protocol . $host . '/' . $url;

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));
	curl_setopt($ch, CURLOPT_TIMEOUT, 2); 
	$response = curl_exec($ch); 
	
	curl_close($ch);
}


function connectFwrite($host, $url)
{
	$fp = @fsockopen($host, 80, $errorNum, $errorStr);

	if (!$fp) {
		echo 'There was an error connecting to the site.';
		exit;
	}

	$request = "GET /" . $url . " HTTP/1.1\r\n";
	$request .= "Host: " . $host . "\r\n";
	$request .= "Connection: Close\r\n\r\n";

	fwrite($fp, $request);

	fclose($fp);
}

connect($host, 'index.php?option=com_easydiscuss&task=cron', $https);

echo "Cronjob processed.\r\n";
return;