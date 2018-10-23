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

class EasyBlogCompiler extends EasyBlog
{
	static $instance = null;
	public $version;
	public $cli = false;

	// These script files should be rendered externally and not compiled together
	// Because they are either too large or only used in very minimal locations.
	public $exclusions = array(
								"ace",
								"audiojs",
								"moment",
								"plupload2",
								"ui",
								"videojs",
								"ace.js",
								"audiojs.js",
								"datetimepicker.js",
								"imgareaselect.js",
								"moment.js",
								"plupload2.js",
								"redactor.js",
								"videojs.js"
						);

	// Exclusions based on sections
	public $sectionExclusionsFilters = array();

	public function __construct()
	{
		$this->version = (string) EB::getLocalVersion();

		// Manually insert folders which we would like to exclude
		$this->sectionExclusionsFilters['admin'] = array('vendors\/*');
	}

	/**
	 * Allows caller to compile a script file on the site, given the section
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function compile($section = 'admin', $minify = true, $jquery = true)
	{
		// Get the file name that should be used after compiling the scripts
		$fileName = EB::scripts()->getFileName($section, $jquery);

		$files = $this->getFiles($section, $jquery);

		// Include the bootloader
		$contents = $this->compileBootloader();

		// 1. Core file contents needs to be placed at the top
		$contents .= $this->compileCoreFiles($files->core);

		// 2. Libraries should be appended next
		$contents .= $this->compileLibraries($files->libraries);

		// 3. Compile the normal scripts
		$contents .= $this->compileScripts($files->scripts);

		$result = new stdClass();
		$result->section = $section;
		$result->minify = $minify;

		// Store the uncompressed version
		$standardPath = EBLOG_SCRIPTS . '/' . $fileName . '.js';

		$this->write($standardPath, $contents);

		$result->standard = $standardPath;
		$result->minified = false;

		// Compress the script and minify it
		if ($minify) {
			$closure = $this->getClosure();

			// 1. Minify the main library
			$contents = $closure->minify($contents);
			
			// Store the minified version
			$minifiedPath = EBLOG_SCRIPTS . '/' . $fileName . '.min.js';
			$this->write($minifiedPath, $contents);

			$result->minified = $minifiedPath;
		}

		if (defined('EASYBLOG_CLI')) {
			return $result;
		}

		return $result;
	}

	/**
	 * Compile excluded files
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function compileExcludedFiles()
	{
		$files = $this->getExcludedFiles();

		$closure = $this->getClosure();

		foreach ($files as $file) {
			$extension = JFile::getExt($file);

			// Do not try to repack any .min.js
			$name = basename($file);
			$isMinified = stristr($name, '.min.js') !== false;

			if ($isMinified || $extension != 'js') {
				continue;
			}

			$contents = JFile::read($file);
			$contents = $closure->minify($contents);

			// Create the .min.js file
			$target = str_ireplace('.js', '.min.js', $file);
			$this->write($target, $contents);
		}
	}

	/**
	 * Retrieves a list of excluded script files from the compiler
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getExcludedFiles()
	{
		$path = EBLOG_SCRIPTS . '/vendors';

		if (!$this->exclusions) {
			return array();
		}
		
		// Do not include these type of files	
		$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX', '.jpg', '.png', '.swf', '.gif');

		$pattern = implode('|^', $this->exclusions);
		$files = JFolder::files($path, $pattern, false, true);

		return $files;
	}

	/**
	 * Retrieves contents from the bootloader file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function compileBootloader()
	{
		$file = EBLOG_SCRIPTS . '/bootloader.js';

		$contents = JFile::read($file);

		return $contents;
	}

	/**
	 * Compiles core files used in EasyBlog
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function compileCoreFiles($files)
	{
		$contents = '';

		foreach ($files as $file) {
			$contents .= JFile::read($file);
		}

		return $contents;
	}

	/**
	 * Compiles all libraries
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function compileLibraries($files)
	{
		$modules = array();

		// Get the prefix so that we can get the proper namespace
		$prefix = EBLOG_SCRIPTS . '/vendors';

		foreach ($files as $file) {
			$fileName = ltrim(str_ireplace($prefix, '', $file), '/');
			$modules[] = str_ireplace('.js', '', $fileName);
		}

		$modules = json_encode($modules);

ob_start();
?>
FD50.plugin("static", function($) {
	$.module(<?php echo $modules;?>);

	// Now we need to retrieve the contents of each files
	<?php foreach ($files as $file) { ?>
		<?php echo $this->getContents($file); ?>
	<?php } ?>
});
<?php
$contents = ob_get_contents();
ob_end_clean();

		return $contents;
	}

	/**
	 * Compiles script files
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function compileScripts($files)
	{
		$modules = array();

		foreach ($files as $file) {
			$namespace = str_ireplace(EBLOG_SCRIPTS, 'easyblog', $file);

			$modules[] = str_ireplace('.js', '', $namespace);
		}

		$modules = json_encode($modules);
ob_start();
?>
// Prepare the script definitions
FD50.installer('EasyBlog', 'definitions', function($) {
	$.module(<?php echo $modules;?>);
});

// Prepare the contents of all the scripts
FD50.installer('EasyBlog', 'scripts', function($) {
	<?php foreach ($files as $file) { ?>
		<?php echo $this->getContents($file); ?>
	<?php } ?>
});
<?php
$contents = ob_get_contents();
ob_end_clean();
	
		return $contents;
	}


	/**
	 * Only creates this instance once
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Retrieves the contents of a particular file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getContents($file)
	{
		$contents = JFile::read($file);

		return $contents;
	}

	/**
	 * Retrieves the closure compiler
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getClosure()
	{
		require_once(__DIR__ . '/closure.php');
		$closure = new EasyBlogCompilerClosure();

		return $closure;
	}

	/**
	 * Retrieves a list of files for specific sections
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFiles($section, $jquery = true)
	{
		$files = new stdClass();

		// Get a list of core files
		$coreFiles = EB::scripts()->getDependencies(true, $jquery);
		$files->core = $coreFiles;

		// Get a list of libraries
		$files->libraries = $this->getLibraryFiles();

		// Get a list of shared scripts that is used across sections
		$scriptFiles = array();
		$scriptFiles = array_merge($scriptFiles, $this->getSharedFiles());

		// Get script files from the particular section
		$scriptFiles = array_merge($scriptFiles, $this->getScriptFiles($section));
		$files->scripts = $scriptFiles;

		return $files;
	}

	/**
	 * Retrieves a list of library files used on the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getLibraryFiles()
	{
		// Retrieve core dependencies
		$excludes = array('moment', 'jquery.js');

		// Add exclusion files
		foreach ($this->exclusions as $exclusion) {
			$excludes[] = $exclusion;

			// Excluded files may also contain a .min.js
			$excludes[] = str_ireplace('.js', '.min.js', $exclusion);
		}

		// Exclude dependencies since these dependencies are stored in the core
		$dependencies = EB::scripts()->getDependencies();
		$excludes = array_merge($excludes, $dependencies);

		$path = EBLOG_SCRIPTS . '/vendors';
		$files = JFolder::files($path, '.js$', true, true, $excludes);

		return $files;
	}

	/**
	 * Retrieves list of shared files that is used across all sections
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getSharedFiles()
	{
		// Retrieve core dependencies
		$dependencies = EB::scripts()->getDependencies();

		// Get shared scripts
		$files = JFolder::files(EBLOG_SCRIPTS . '/shared', '.js$', true, true, $this->exclusions);

		return $files;
	}

	/**
	 * Retrieves list of scripts that is only used in the particular section
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getScriptFiles($section)
	{
		// Check if we have any exclusion filters defined
		$exclusionFilters = array('^\..*', '.*~');

		if (isset($this->sectionExclusionsFilters[$section])) {
			$exclusionFilters = array_merge($exclusionFilters, $this->sectionExclusionsFilters[$section]);
		}

		$path = EBLOG_SCRIPTS . '/' . $section;
		$files = JFolder::files($path, '.js$', true, true, $this->exclusions, $exclusionFilters);

		return $files;
	}

	/**
	 * Saves the contents into a file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function write($path, $contents)
	{
		if (JFile::exists($path)) {
			JFile::delete($path);
		}

		return JFile::write($path, $contents);
	}
}