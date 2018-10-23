<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($file)) {
	return;
}

// Load up our library
$modules = EB::modules($module);
	
// Render ticker's css
$url = rtrim(JURI::root(), '/');
$modules->doc->addStyleSheet($url . '/modules/mod_easyblogticker/assets/styles/ticker-style.css');

$config = EB::config();
$model = EB::model('Blog');
$categoryIds = $params->get('catid');
$count = $params->get('count');

if ($categoryIds) {
	$categories	= explode(',', $categoryIds);
	$posts = $model->getBlogsBy('category', $categories, 'latest', $count, EBLOG_FILTER_PUBLISHED, null, false, array(), false, false, false);
} else {
	$posts = $model->getBlogsBy('', '', 'latest', $count, EBLOG_FILTER_PUBLISHED, null, false, array(), false, false, false);
}

// If there's nothing to show at all, don't even display a box.
if (!$posts) {
	return;
}

$posts = $modules->processItems($posts);

require(JModuleHelper::getLayoutPath('mod_easyblogticker', $params->get('layout', 'default')));

