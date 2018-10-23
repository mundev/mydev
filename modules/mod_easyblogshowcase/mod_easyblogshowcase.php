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

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);
require_once(__DIR__ . '/helper.php');

// Load up our library
$modules = EB::modules($module);

// Retrieve a list of posts
$helper = new modEasyBlogShowcaseHelper($modules);
$posts = $helper->getPosts($params);

if (!$posts) {
	return;
}

// @5.1
// Backward compatibility
$config = $modules->config;

// Should we display the ratings.
$disabled = $modules->params->get('enableratings') ? false : true;
$layout = $modules->params->get('layout', 'default');
$autoplay = $modules->params->get('autorotate', false) ? 1 : 0;
$autoplayInterval = $modules->params->get('autorotate_seconds', 30);

require(JModuleHelper::getLayoutPath('mod_easyblogshowcase', $layout));
