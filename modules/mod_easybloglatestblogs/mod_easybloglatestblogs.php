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

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);
require_once(__DIR__ . '/helper.php');

// Load up our library
$modules = EB::modules($module);

// Get the helper
$helper = new modLatestBlogsHelper($modules);

// @5.1
// Backward compatibility
$config = $modules->config;

// We need to normalize the filter type because of legacy issues
$filterType = $modules->params->get('type');
$filterType = $helper->normalizeFilterType($filterType);

// Other parameters
$layout = $params->get('alignment', 'vertical');

// EasyBlog 5.0.x backward compatible fixes
if (!in_array($layout, array('vertical', 'horizontal'))) {
	$layout = 'vertical';
}

$columnCount = $params->get('column', 3);
$enableRatings = $params->get('enableratings', false);
$excludeFeatured = $params->get('excludefeatured', false);

// Get photo layout and alignment settings
$photoLayout = $modules->getCoverLayout();
$photoAlignment = $modules->getCoverAlignment();

// Get post items
$posts = $helper->getItems($filterType);

if (!$posts) {
	if ($helper->hasErrors()) {
		echo JText::_($helper->getError());
	}
	return;
}

require(JModuleHelper::getLayoutPath('mod_easybloglatestblogs', $params->get('layout', 'default')));
