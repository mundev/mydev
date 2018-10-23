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
jimport('joomla.filesystem.folder');

// Get application
$app = JFactory::getApplication();
$input = $app->input;

// Ensure that the Joomla sections don't appear.
$input->set('tmpl', 'component');

// Determines if the current mode is re-install
$reinstall = $input->get('reinstall', false, 'bool') || $input->get('install', false, 'bool');

// If the mode is update, we need to get the latest version
$update = $input->get('update', false, 'bool');

// Determines if we are now in developer mode.
$developer = $input->get('developer', false, 'bool');

// If this is in developer mode, we need to set the session
if ($developer) {
	$session = JFactory::getSession();
	$session->set('easyblog.developer', true);
}

if (!function_exists('dump')) {

	function isDevelopment()
	{
		$session = JFactory::getSession();
		$developer = $session->get('easyblog.developer');

		return $developer;
	}

	function dump()
	{
		$args = func_get_args();

		echo '<pre>';
		
		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo '</pre>';

		exit;
	}
}

############################################################
#### Constants
############################################################
$path = dirname(__FILE__);
define('EB_PACKAGES', $path . '/packages');
define('EB_CONFIG', $path . '/config');
define('EB_THEMES', $path . '/themes');
define('EB_LIB', $path . '/libraries');
define('EB_CONTROLLERS', $path . '/controllers');
define('EB_SERVER', 'https://stackideas.com');
define('EB_VERIFIER', 'https://stackideas.com/updater/verify');
define('EB_MANIFEST', 'https://stackideas.com/updater/manifests/easyblog');
define('EB_SETUP_URL', JURI::base() . 'components/com_easyblog/setup');
define('EB_TMP', $path . '/tmp');
define('EB_BETA', false);
define('EB_KEY', '44e6ff7f498cd26dc9682d9e938bc03f');
define('EB_INSTALLER', 'launcher');

// Only when EB_INSTALLER is running on full package, the EB_PACKAGE should contain the zip's filename
define('EB_PACKAGE', '');

// Process controller
$controller = $input->get('controller', '', 'cmd');
$task = $input->get('task', '');

if (!empty($controller)) {

	$file = strtolower($controller) . '.' . strtolower($task) . '.php';
	$file = EB_CONTROLLERS . '/' . $file;

	require_once($file);

	$className = 'EasyBlogController' . ucfirst($controller) . ucfirst($task);
	$controller = new $className();
	return $controller->execute();
}

// Get the current version
$contents = JFile::read(JPATH_ROOT. '/administrator/components/com_easyblog/easyblog.xml');
$parser = simplexml_load_string($contents);

$version = $parser->xpath('version');
$version = (string) $version[0];

define('EB_HASH', md5($version));

//Initialize steps
$contents = JFile::read(EB_CONFIG . '/install.json');
$steps = json_decode($contents);

// Workflow
$active = $input->get('active', 0, 'default');

if ($active === 'complete') {
	$activeStep = new stdClass();

	$activeStep->title = JText::_('COM_EASYBLOG_INSTALLER_INSTALLATION_COMPLETED');
	$activeStep->template = 'complete';

	// Assign class names to the step items.
	if ($steps) {
		foreach ($steps as $step) {
			$step->className = ' done';
		}
	}
} else {

	if ($active == 0) {
		$active = 1;
		$stepIndex = 0;
	} else {
		$active += 1;
		$stepIndex = $active - 1;
	}

	// Get the active step object.
	$activeStep = $steps[$stepIndex];

	// Assign class names to the step items.
	foreach ($steps as $step) {
		$step->className = $step->index == $active || $step->index < $active ? ' current' : '';
		$step->className .= $step->index < $active ? ' done' : '';
	}

	// If this site meets all requirement, we skip the requirement page
	if ($stepIndex == 0) {

		$gd = function_exists('gd_info');
		$curl = is_callable('curl_init');

		// MySQL info
		$db = JFactory::getDBO();
		$mysqlVersion = $db->getVersion();

		// PHP info
		$phpVersion = phpversion();
		$uploadLimit = ini_get('upload_max_filesize');
		$memoryLimit = ini_get('memory_limit');
		$postSize = ini_get('post_max_size');
		$magicQuotes = get_magic_quotes_gpc() && JVERSION > 3;

		if (stripos($memoryLimit, 'G') !== false) {

			list($memoryLimit) = explode('G', $memoryLimit);

			$memoryLimit = $memoryLimit * 1024;
		}

		$postSize = 4;
		$hasErrors = false;

		if (!$gd || !$curl || $magicQuotes) {
			$hasErrors = true;
		}

		$files = array();

		$files['admin'] = new stdClass();
		$files['admin']->path = JPATH_ROOT . '/administrator/components';
		$files['site'] = new stdClass();
		$files['site']->path = JPATH_ROOT . '/components';
		$files['tmp'] = new stdClass();
		$files['tmp']->path = JPATH_ROOT . '/tmp';
		$files['media'] = new stdClass();
		$files['media']->path = JPATH_ROOT . '/media';
		$files['user'] = new stdClass();
		$files['user']->path = JPATH_ROOT . '/plugins/user';
		$files['module'] = new stdClass();
		$files['module']->path = JPATH_ROOT . '/modules';

		// Debugging
		$posixExists = function_exists('posix_getpwuid');

		if ($posixExists) {
			$owners = array();
		}

		// If until here no errors, we don't display the setting section
		$showSettingsSection = $hasErrors;

		// Determines write permission on folders
		$showDirectorySection = false;

		foreach ($files as $file) {

			// The only proper way to test this is to not use is_writable
			$contents = "<body></body>";
			$state = JFile::write($file->path . '/tmp.html', $contents);

			// Initialize this to false by default
			$file->writable = false;

			if ($state) {
				JFile::delete($file->path . '/tmp.html');

				$file->writable = true;
			}

			if (!$file->writable) {
				$showDirectorySection = true;
				$hasErrors = true;
			}

			if ($posixExists) {
				$owner = posix_getpwuid(fileowner($file->path));
				$group = posix_getpwuid(filegroup($file->path));

				$file->owner = $owner['name'];
				$file->group = $group['name'];
				$file->permissions = substr(decoct(fileperms($file->path)), 1);
			}
		}

		if ($hasErrors) {
			$errorStep = new stdCLass;
			$errorStep->index = 0;
			$errorStep->title = 'COM_EB_INSTALLATION_REQUIREMENTS_ERROR';
			$errorStep->desc = 'COM_EB_INSTALLATION_REQUIREMENTS_ERROR_DESC';
			$errorStep->template = 'requirements';
			$activeStep = $errorStep;

			require(EB_THEMES . '/default.php');
			return;
		}
	}
}

require(EB_THEMES . '/default.php');