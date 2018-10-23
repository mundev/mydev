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

$app = JFactory::getApplication();
$task = $app->input->get('task', '', 'cmd');
$config = EB::config();
$result = array();

$cron = $app->input->get('cron', false, 'bool');

if ($task == 'cron' || $cron) {

	// Check for secure cron key
	$requireSecure = $config->get('main_cron_secure');
	$secureKey = $config->get('main_cron_secure_key');
	$key = $app->input->get('phrase', '', 'cmd');

	if ($requireSecure && empty($key) || ($requireSecure && $secureKey != $key)) {
		$output = new stdClass();
		$output->status = 200;
		$output->contents = JText::_('COM_EB_CRONJOB_PASSPHRASE_INVALID');
		$output->time = EB::date()->toMySQL();

		header('Content-type: text/x-json; UTF-8');
		echo json_encode($output);
		exit;
	}

	// Process emails that is pending to be dispatched
	$result['emails'] = EB::mailer()->dispatch();

	// Import from mailbox for posts
	$result['email_import'] = EB::mailbox()->import('post');

	// Process twitter remote posts
	$result['twitter_import'] = EB::twitter()->import();

	// Process scheduled posts
	$result['scheduler_publish'] = EB::scheduler()->publish();

	// Process scheduled unpublish posts
	$result['scheduler_unpublish'] = EB::scheduler()->unpublish();

	// Process the garbage collector. Remove the records from #__easyblog_uploader_tmp which exceed 120 minutes.
	$result['scheduler_remove_tmp_files'] = EB::scheduler()->removeTmpFiles();

	// Process the garbage collector. Remove BLANK post from from #__easyblog_post which exceed 3 days.
	$result['scheduler_remove_blank_posts'] = EB::scheduler()->removeBlankPosts();

	// Process the facebook's token expiry notification.
	$result['token_expiry_notification'] = EB::oauth()->notifyAdminTokenExpiry();

	// Process scheduled unpublish posts
	$result['scheduler_autopost'] = EB::scheduler()->autopost();

	// Process reposting
	$result['scheduler_reposting'] = EB::scheduler()->reposting();

	// Process expired gdpr download request
	$result['scheduler_expiry_download'] = EB::scheduler()->purgeExpiredDownload();


	header('Content-type: text/x-json; UTF-8');
	echo json_encode( cronOutPut($result) );
	exit;
}

// If there's a task to execute cron feeds, execute it here
if ($task == 'cronfeed') {

	$result['feeds'] = EB::feeds()->cron();

	header('Content-type: text/x-json; UTF-8');
	echo json_encode(cronOutPut($result));
	exit;
}


// If there's a task to execute cron for data download, execute it here
if ($task == 'crondata') {

	$result['data'] = EB::gdpr()->cron();

	header('Content-type: text/x-json; UTF-8');
	echo json_encode(cronOutPut($result));
	exit;
}


function cronOutPut($results) {

	$output = array();

	foreach( $results as $key => $data) {

		$newdata = new stdClass();

		$newdata->status = '';
		$newdata->type = '';
		$newdata->message = '';

		if ($data instanceof EasyBlogException) {

			$item = $data->toArray();

			$newdata->status = $item['code'];
			$newdata->type = $item['type'];
			$newdata->message = ($item['message']) ? $item['message'] : $item['html'];
		} else if (is_string($data)) {
			$newdata->message = $data;
		}

		$output[$key] = $newdata;
	}

	return $output;

}
