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

$app = JFactory::getApplication();
$input = $app->input;

// Ensure that the Joomla sections don't appear.
$input->set('tmpl', 'component');

// Determines if we are now in developer mode.
$developer = $input->get('developer', false, 'bool');

if ($developer) {
	$session = JFactory::getSession();
	$session->set('easysocial.developer', true);
}

############################################################
#### Constants
############################################################
$path = __DIR__;
define('ES_PACKAGES', $path . '/packages');
define('ES_CONFIG', $path . '/config');
define('ES_THEMES', $path . '/themes');
define('ES_LIB', $path . '/libraries');
define('ES_CONTROLLERS', $path . '/controllers');
define('ES_TMP', $path . '/tmp');
define('ES_VERIFIER', 'https://stackideas.com/updater/verify');
define('ES_DOWNLOADER', 'https://stackideas.com/updater/services/download/easysocial');
define('ES_MANIFEST', 'https://stackideas.com/updater/manifests/easysocial');
define('ES_BETA', false);
define('ES_SETUP_URL', rtrim(JURI::root(), '/') . '/administrator/components/com_easysocial/setup');
define('ES_KEY', '44e6ff7f498cd26dc9682d9e938bc03f');
define('ES_INSTALLER', 'launcher');
define('ES_PACKAGE', '');

############################################################
#### Process ajax calls
############################################################
if ($input->get('ajax', false, 'bool')) {

	$controller = $input->get('controller', '', 'cmd');
	$task = $input->get('task', '', 'cmd');

	$controllerFile = ES_CONTROLLERS . '/' . strtolower( $controller ) . '.php';

	require_once($controllerFile);

	$controllerName = 'EasySocialController' . ucfirst( $controller );
	$controller = new $controllerName();

	return $controller->$task();
}

############################################################
#### Process controller
############################################################
$controller = $input->get('controller', '', 'cmd');

if (!empty($controller)) {
	$controllerFile = ES_CONTROLLERS . '/' . strtolower($controller) . '.php';

	require_once($controllerFile);

	$controllerName = 'EasySocialController' . ucfirst( $controller );
	$controller = new $controllerName();
	return $controller->execute();
}

############################################################
#### Initialization
############################################################
$contents = JFile::read(ES_CONFIG . '/installation.json');
$steps = json_decode($contents);

############################################################
#### Workflow
############################################################
$active = $input->get('active', 0, 'int');

if ($active == 0) {
	$active = 1;
	$stepIndex = 0;
} else {
	$active += 1;
	$stepIndex = $active - 1;
}

if ($active > count($steps)) {
	$active = 'complete';
	$activeStep = new stdClass();

	$activeStep->title = JText::_('Installation Completed');
	$activeStep->template = 'complete';

	// Assign class names to the step items.
	if ($steps) {
		foreach ($steps as $step) {
			$step->className = ' current done';
		}
	}
} else {
	// Get the active step object.
	$activeStep = $steps[$stepIndex];

	// Assign class names to the step items.
	foreach ($steps as $step) {
		$step->className = $step->index == $active || $step->index < $active ? ' current' : '';
		$step->className .= $step->index < $active ? ' done' : '';
	}
}

require(ES_THEMES . '/default.php');
