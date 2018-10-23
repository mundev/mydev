<?php
/**
* @package		Helix3 Framework
* @author		JoomShaper http://www.joomshaper.com
* @copyright	Copyright (c) 2010 - 2017 JoomShaper
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*
* @package      Office Template
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Office Template is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filter.filteroutput');

class OfficeHelix3
{
	private static $_instance;
	private $document;
	private $importedFiles = array();
	private $_less;
	private $loadPos;
	private $inPositions = array();

	public $loadFeture = array();
	public $app = null;
	public $input = null;
	public $params = null;
	public $template = null;
	public $templatePath = null;
	public $user = null;
	public $cachePath = null;
	public $cacheTime = null;

	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->template = $this->app->getTemplate();
		$this->templatePath = JPATH_THEMES . '/' . $this->template;
		$this->params = $this->app->getTemplate(true)->params;
		$this->user = JFactory::getUser();
		$this->cachePath = JPATH_CACHE . '/com_templates/templates/' . $this->template;
		$this->cacheTime = $this->app->get('cachetime', 15);
	}

	/**
	* Object for singleton.
	* 
	*/
	public final static function getInstance()
	{
		if (!self::$_instance) {
			self::$_instance = new self();
			self::$_instance->getDocument();
		}

		return self::$_instance;
	}

	/**
	* Get Document
	* 
	*/
	public static function getDocument($key = false)
	{
		self::getInstance()->document = JFactory::getDocument();
		$doc = self::getInstance()->document;

		if (is_string($key)) {
			return $doc->$key;
		}

		return $doc;
	}

	/**
	* Get param
	* 
	*/
	public static function getParam($key)
	{
		return self::getInstance()->params->get($key);
	}

	/**
	* Generate body class
	* 
	*/
	public static function bodyClass($class = '')
	{
		$language = self::getInstance()->document->language;
		$direction = self::getInstance()->document->direction;
		$option = str_replace('_', '-', self::getInstance()->input->getCmd('option', ''));
		$view = self::getInstance()->input->getCmd('view', '');
		$layout = self::getInstance()->input->getCmd('layout', '');
		$task = self::getInstance()->input->getCmd('task', '');
		$itemid = self::getInstance()->input->getCmd('Itemid', '');
		$sitename = self::getInstance()->app->get('sitename');

		$menu = self::getInstance()->app->getMenu()->getActive();

		if ($view == 'modules') {
			$layout = 'edit';
		}

		$class = 'site ' . $option . ' view-' . $view;
		$class .= $layout ? ' layout-' . $layout : ' no-layout';
		$class .= $task ? ' task-' . $task : ' no-task';
		$class .= $itemid ? ' itemid-' . $itemid : '';
		$class .= $language ? ' ' . $language : '';
		$class .= $direction ? ' ' . $direction : '';
		$class .= $class ? ' ' . $class : '';

		$class .= ' layout-fluid';
		$class .= ' slide-menu-left sidebar-init';
		$class .= Office::isMobile() ? ' is-mobile' : '';
		$class .= Office::isTablet() ? ' is-tablet' : '';

		if (self::getParam("sticky_header")) {
			$class .= ' sticky_header';
		}
		if (isset($menu) && $menu) {
			if ($menu->params->get('pageclass_sfx')) {
				$class .= ' ' . $menu->params->get('pageclass_sfx');
			}
		}

		if (JFactory::getUser()->guest) {
			if ($option == 'com-easysocial' && in_array($view, array( '', 'login', 'dashboard', 'account'))) {

				if (ES::config()->get('general.site.lockdown.enabled')) {
					$class .= ' si-login-page';
				}

				$class .= ' is-login-' . self::getParam('login_layout');
			}
		}


		return $class;
	}

	/**
	* Get Template URI
	* 
	*/
	public static function view($class = '')
	{
		$view = self::getInstance()->input->getCmd('view', '');
		$layout = self::getInstance()->input->getCmd('layout', '');

		if ($view == 'modules') {
			$layout = 'edit';
		}

		return $layout;
	}

	/**
	* Get Template URI
	* 
	*/
	public static function getTemplateUri()
	{
		return JUri::base(true) . '/templates/' . self::getTemplate();
	}

	/**
	* Get Template name
	* 
	*/
	public static function getTemplate()
	{
		return self::getInstance()->template;
	}

	/**
	* Get or set Template param. If value not setted params get and return, else set params.
	* 
	*/
	public static function param($name = true, $value = null)
	{
		if (is_bool($name) && $name) {
			return self::getInstance()->params;
		}

		if (is_null($value)) {
			return self::getInstance()->params->get($name);
		}

		$data = self::getInstance()->params->get($name);
		if (is_null($data) || !isset($data)) {
			self::getInstance()->params->set($name, $value);

			return self::getInstance()->params->get($name);
		} 
		else {
			return $data;
		}
	}

	/**
	* Importing features
	* 
	*/
	private static function importFeatures()
	{
		$path = self::getInstance()->templatePath . '/features';

		if (file_exists($path)) {
			$files = JFolder::files($path, '.php');

			if (count($files)) {
				foreach ($files as $key => $file) {
					include_once $path . '/' . $file;
					$name = JFile::stripExt($file);

					$class = 'OfficeHelix3Feature' . ucfirst($name);
					$class = new $class(self::getInstance());

					$position = $class->position;
					$loadPos = '';

					if (isset($class->load_pos) && $class->load_pos) {
						$loadPos = $class->load_pos;
					}

					self::getInstance()->inPositions[] = $position;

					if (!empty($position)) {
						self::getInstance()->loadFeature[$position][$key]['feature'] = $class->renderFeature();
						self::getInstance()->loadFeature[$position][$key]['load_pos'] = $loadPos;
					}
				}
			}
		}

		return self::getInstance();
	}

	/**
	* Get number from col-xs
	* 
	*/
	public static function getColXsNo($colName)
	{
		$classRemove = array('layout-column', 'column-active', 'col-sm-');

		$colXsNo = trim(str_replace($classRemove, '', $colName));
		$col = 0;

		if ($colXsNo != '') {
			$col = (int) $colXsNo;
		}

		return $col;
	}

	/**
	* Generate layout
	* 
	*/
	public static function generateLayout()
	{
		self::getInstance()->addCSS('custom.css');
		self::getInstance()->addJS('custom.js');

		$option = self::getInstance()->input->getCmd('option', '');
		$view = self::getInstance()->input->getCmd('view', '');
		$pageBuilder = false;

		if ($option == 'com_sppagebuilder') {
			self::getInstance()->document->addStylesheet(JUri::base(true) . '/plugins/system/officehelix3/assets/css/pagebuilder.css');
			$pageBuilder = true;
		}

		self::importFeatures();

		$layout = json_decode(self::getInstance()->params->get('layout'));

		// Remove layout from DB. We'll render layout from file.
		$layout = '';

		if (empty($layout)) {
			$layoutFile = JPATH_SITE . '/templates/' . self::getInstance()->template . '/layout/default.json';

			if (self::getInstance()->user->guest && in_array($view, array('', 'login', 'dashboard', 'account')) && $option == 'com_easysocial') {
				if (ES::config()->get('general.site.lockdown.enabled')) {
					$layoutFile = JPATH_SITE . '/templates/' . self::getInstance()->template . '/layout/login.json';
				}
			}

			if (!JFile::exists($layoutFile)) {
				die('Layout files are missing. Please contact support at https://stackideas.com/forums');
			}

			$rows = json_decode(JFile::read($layoutFile));

			// Hack to fix the footer issue for login page.
			// We know that (2) is used for login.json.
			if (count($rows) == 2) {
				// Get footer from default.json
				$defaultRows = json_decode(JFile::read(JPATH_SITE . '/templates/' . self::getTemplate() . '/layout/default.json'));

				$rows[count($rows)] = $defaultRows[count($defaultRows)-1];
			}
		}

		$output = '';

		foreach ($rows as $key => $row) {

			$rowColumns = self::rowColumns($row->attr);

			if (!empty($rowColumns)) {
				$componentArea = self::hasComponent($rowColumns) ? true : false;

				$fluidRow = false;
				if (!empty($row->settings->fluidrow)) {
					$fluidRow = $row->settings->fluidrow;
				}

				$id = (empty($row->settings->name)) ? 'sp-section-' . ($key + 1) : 'sp-' . JFilterOutput::stringURLSafe($row->settings->name);

				$rowClass = '';

				if (!empty($row->settings->custom_class)) {
					$rowClass .= $row->settings->custom_class;
				}

				if (!empty($row->settings->hidden_xs)) {
					$rowClass .= ' hidden-xs';
				}

				if (!empty($row->settings->hidden_sm)) {
					$rowClass .= ' hidden-sm';
				}

				if (!empty($row->settings->hidden_md)) {
					$rowClass .= ' hidden-md';
				}

				if ($rowClass) {
					$rowClass = ' class="' . $rowClass . '"';
				}

				$rowCss = '';

				if (!empty($row->settings->background_image)) {
					$rowCss .= 'background-image:url("' . JURI::base(true) . '/' . $row->settings->background_image . '");';

					if (!empty($row->settings->background_repeat)) {
						$rowCss .= 'background-repeat:' . $row->settings->background_repeat . ';';
					}

					if (!empty($row->settings->background_size)) {
						$rowCss .= 'background-size:' . $row->settings->background_size . ';';
					}

					if (!empty($row->settings->background_attachment)) {
						$rowCss .= 'background-attachment:' . $row->settings->background_attachment . ';';
					}

					if (!empty($row->settings->background_position)) {
						$rowCss .= 'background-position:' . $row->settings->background_position . ';';
					}
				}

				if (!empty($row->setings->background_color)) {
					$rowCss .= 'background-color:' . $row->settings->background_color . ';';
				}

				if (!empty($row->settings->color)) {
					$rowCss .= 'color:' . $row->settings->color . ';';
				}

				if (!empty($row->settings->padding)) {
					$rowCss .= 'padding:' . $row->settings->padding . ';';
				}

				if (!empty($row->settings->margin)) {
					$rowCss .= 'margin:' . $row->settings->margin . ';';
				}

				if ($rowCss) {
					self::getInstance()->document->addStyleDeclaration('#' . $id . '{' . $rowCss . '}');
				}

				if (!empty($row->settings->link_color)) {
					self::getInstance()->document->addStyleDeclaration('#' . $id . 'a{color:' . $row->settings->link_color . ';}');
				}

				if (!empty($row->settings->link_hover_color)) {
					self::getInstance()->document->addStyleDeclaration('#' . $id . 'a:hover{color:' . $row->settings->link_hover_color . ';}');
				}

				// Set html5 structure
				$semantic = 'section';

				if (!empty($row->settings->name)) {
					$semantic = strtolower($row->settings->name);

					if (!in_array($semantic, array('header', 'footer'))) {
						$semantic = 'section';
					}
				}

				$layoutData = array(
					'sematic' => $semantic,
					'id' => $id,
					'row_class' => $rowClass,
					'componentArea' => $componentArea,
					'pagebuilder' => $pageBuilder,
					'fluidrow' => $fluidRow,
					'rowColumns' => $rowColumns,
				);

				$generateFile = self::getInstance()->templatePath . '/html/layouts/officehelix3/frontend/generate.php';

				$layoutPath = JPATH_ROOT .'/plugins/system/officehelix3/layouts';
				if (file_exists($generateFile)) {
					$layoutPath = self::getInstance()->templatePath . '/html/layouts/officehelix3/';
				}

				$generate = new JLayoutFile('frontend.generate', $layoutPath);
				$output .= $generate->render($layoutData);
			}
		}

		echo $output;
	}

	/**
	* Get component
	* 
	*/
	private static function hasComponent($rowColumns) 
	{
		$hasComponent = false;

		foreach ($rowColumns as $key => $column) {
			if ($column->settings->column_type) {
				$hasComponent = true;
			}
		}

		return $hasComponent;
	}

	/**
	* Get Active Columns
	* 
	*/
	private static function rowColumns($columns)
	{
		$cols = array();

		// absence span
		$absspan = 0;
		$col_i = 1;

		// total publish children
		$totalPublished = count($columns);
		$hasComponent = false;

		foreach ($columns as &$column) {
			$column->settings->name = (!empty($column->settings->name)) ? $column->settings->name : 'none_empty';
			$column->settings->column_type = (!empty($column->settings->column_type)) ? $column->settings->column_type : 0;
			$column->settings->custom_class = (!empty($column->settings->custom_class)) ? $column->settings->custom_class : '';

			if (!$column->settings->column_type) {
				if (!self::countModules($column->settings->name)) {
					$col_xs_no = self::getColXsNo($column->className);
					$absspan += $col_xs_no;
					$totalPublished--;
				}
			}
			else {
				$hasComponent = true;
			}
		}

		foreach ($columns as &$column) {
			if ($column->settings->column_type) {
				$column->className = 'col-sm-' . (self::getColXsNo($column->className) + $absspan) . ' col-md-' . (self::getColXsNo($column->className) + $absspan);
				$cols[] = $column;
				$col_i++;
			}
			else {
				if (self::countModules($column->settings->name)) {
					$last_col = ($totalPublished == $col_i) ? $absspan : 0;
					
					if ($hasComponent) {
						$column->className = 'col-sm-' . self::getColXsNo($column->className) . ' col-md-' . self::getColXsNo($column->className);
					}
					else {
						$column->className = 'col-sm-' . (self::getColXsNo($column->className) + $last_col) . ' col-md-' . (self::getColXsNo($column->className) + $last_col);
					}

					$cols[] = $column;
					$col_i++;
				}
			}
		}

		return $cols;
	}

	/**
	* Count Modules
	* 
	*/
	public static function countModules($position)
	{
		return (self::getInstance()->document->countModules($position) || self::hasFeature($position));
	}

	/**
	* Has feature
	* 
	*/
	public static function hasFeature($position)
	{
		if (in_array($position, self::getInstance()->inPositions)) {
			return true;
		}

		return false;
	}

	/**
	* Add stylesheet
	* 
	*/
	public static function addCSS($sources, $attr = array())
	{
		$path = self::getInstance()->templatePath . '/css/';

		if (is_string($sources)) {
			$sources = explode(',', $sources);
		}

		if (!is_array($sources)) {
			$sources = array($sources);
		}

		foreach ((array) $sources as &$source) {
			$source = trim($source);

			if (file_exists($path . $source)) {
				self::getInstance()->document->addStylesheet(JUri::base(true) . '/templates/' . self::getInstance()->template . '/css/' . $source, 'text/css', null, $attr);
			}
			else if ($source != 'custom.css') {
				self::getInstance()->document->addStylesheet($source, 'text/css', null, $attr);
			}
		}

		return self::getInstance();
	}

	/**
	* Add javascript
	* 
	*/
	public static function addJS($sources, $separator = ',')
	{
		$path = self::getInstance()->templatePath . '/js/';

		if (is_string($sources)) {
			$sources = explode($separator, $sources);
		}

		if (!is_array($sources)) {
			$sources = array($sources);
		}

		foreach ((array) $sources as &$source) {
			$source = trim($source);

			if (file_exists($path . $source)) {
				self::getInstance()->document->addScript(JUri::base(true) . '/templates/' . self::getInstance()->template . '/js/' . $source);
			}
			else if ($source != 'custom.js') {
				self::getInstance()->document->addScript($source);
			}
		}

		return self::getInstance();
	}

	/**
	* Add Inline Javascript
	* 
	*/
	public static function addInlineJS($code)
	{
		self::getInstance()->document->addscriptDeclaration($code);

		return self::getInstance();
	}

	/**
	* Add Inline CSS
	* 
	*/
	public static function addInlineCSS($code)
	{
		self::getInstance()->document->addStyleDeclaration($code);

		return self::getInstance();
	}

	/**
	* Less Init
	*
	*/
	public static function lessInit()
	{
		require_once __DIR__ . '/classes/lessc.inc.php';

		self::getInstance()->_less = new helix3_lessc();

		return self::getInstance();
	}

	/**
	* Get Less instance
	*
	*/
	public static function less()
	{
		return self::getInstance()->_less;
	}

	/**
	* Set Less Variables using array key and value
	*
	*/
	public static function setLessVariables($array)
	{
		self::getInstance()->less()->setVariables($array);

		return self::getInstance();
	}

	/**
	* Set less variable using name and value
	*
	*/
	public static function setLessVariable($name, $value)
	{
		self::getInstance()->less()->setVariables(array($name => $value));

		return self::getInstance();
	}

	/**
	* Compile less to css when less modified or css not exist
	*
	*/
	private static function autoCompileLess($less, $css)
	{
		$cacheFile = self::getInstance()->cachePath . '/' . basename($css . '.cache');

		if (file_exists($cacheFile)) {
			$cache = unserialize(JFile::read($cacheFile));

			// If root changed, then do not re-compile.
			if (isset($cache['root']) && $cache['root']) {
				if ($cache['root'] != $less) {
					return self::getInstance();
				}
			}
		}
		else {
			$cache = $less;
		}

		$lessInit = self::getInstance()->less();
		$newCache = $lessInit->cachedCompile($cache);

		if (!is_array($cache) || $newCache['updated'] > $cache['updated']) {
			if (!file_exists(self::getInstance()->cachePath)) {
				JFolder::create(self::getInstance()->cachePath, 0755);
			}

			file_put_contents($cacheFile, serialize($newCache));
			file_put_contents($css, $newCache['compiled']);
		}

		return self::getInstance();
	}

	/**
	* Add less
	*
	*/
	public static function addLess($less, $css, $attr = array())
	{
		if (self::getParam('lessoption') && self::getParam('lessoption') == '1') {
			if (file_exists(self::getInstance()->templatePath . '/less/' . $less . '.less')) {
				self::getInstance()->autoCompileLess(self::getInstance()->templatePath . '/less/' . $less . '.less', self::getInstance()->templatePath . '/css/' . $css . '.css');
			}
		}

		self::getInstance()->addCSS($css . '.css', $attr);

		return self::getInstance();
	}

	/**
	* Add less files
	*
	*/
	private static function addLessFiles($less, $css)
	{
		$less = self::getInstance()->file('less/' . $less . '.less');
		$css = self::getInstance()->file('css/' . $css . '.css');
		self::getInstance()->less()->compileFile($less, $css);

		echo $less;
		die;

		return self::getInstance();
	}

	/**
	* Reset cookie
	*
	*/
	private static function resetCookie($name)
	{
		if (self::getInstance()->input->get('reset', '', 'get') == 1) {
			setCookie($name, '', time() - 3600, '/');
		}
	}

	/**
	* Preset
	*
	*/
	public static function preset()
	{
		$name = self::getInstance()->template . '_preset';

		if (isset($_COOKIE[$name])) {
			$current = $_COOKIE[$name];
		}
		else {
			$current = self::getParam('preset');
		}

		return $current;
	}

	/**
	* Get preset
	*
	*/
	public static function presetParam($name)
	{
		return self::getParam(self::getInstance()->preset() . $name);
	}

	/**
	* Load Menu
	*
	*/
	public static function loadMegaMenu($class = '', $name = '')
	{
		require_once __DIR__ . '/classes/menu.php';

		return new OfficeHelix3Menu($class, $name);
	}

	/**
	* Add Google Fonts
	*
	*/
	public static function addGoogleFont($fonts)
	{
		$templateFontPath = self::getInstance()->templatePath . '/webfonts/webfonts.json';
		$pluginFontPath = JPATH_BASE . '/plugins/system/officehelix3/assets/webfonts/webfonts.json';

		$webfonts = JFile::read($pluginFontPath);
		if (file_exists($templateFontPath)) {
			$webfonts = JFile::read($templateFontPath);
		}

		$families = array();
		$selectors = array();

		foreach ($fonts as $key => $value) {
			$value = json_decode($value);

			// Families
			if (isset($value->fontWeight) && $value->fontWeight) {
				$families[$value->fontFamily]['weight'][] = $value->fontWeight;
			}

			if (isset($value->fontSubset) && $value->fontSubset) {
				$families[$value->fontFamily]['subset'][] = $value->fontSubset;
			}

			// Selectors
			if (isset($value->fontFamily) && $value->fontFamily) {
				$selectors[$key]['family'] = $value->fontFamily;
			}

			if (isset($value->fontSize) && $value->fontSize) {
				$selectors[$key]['size'] = $value->fontSize;
			}

			if (isset($value->fontWeight) && $value->fontWeight) {
				$selectors[$key]['weight'] = $value->fontWeight;
			}
		}

		// Add Google Font URL
		foreach ($families as $key => $value) {
			$output = str_replace(' ', '+', $key);

			if ($webfonts) {
				$fontArray = Utils::object_to_array(json_decode($webfonts));
				$fontKey = Utils::font_key_search($key, $fontArray['items']);
				$weightArray = $fontArray['items'][$fontKey]['variants'];
				$output .= ':' . implode(',', $weightArray);
			}
			else {
				$weight = array_unique($value['weight']);

				if (isset($weight) && $weight) {
					$output .= ':' . implode(',', $weight);
				}
			}

			// Subset
			$subset = array_unique($value['subset']);
			if (isset($subset) && $subset) {
				$output .= '&amp;subset=' . implode(',', $subset);
			}

			self::getInstance()->document->addStylesheet('//fonts.googleapis.com/css?family=' . $output);
		}

		// Add font to selector
		foreach ($selectors as $key => $value) {
			if (isset($value['family']) && $value['family']) {
				$output = 'font-family:' . $value['family'] . ', sans-serif; ';

				if (isset($value['size']) && $value['size']) {
					$output .= 'font-size:' . $value['size'] . 'px; ';
				}

				if (isset($value['weight']) && $value['weight']) {
					$output .= 'font-weight:' . str_replace('regular', 'normal', $value['weight']) . '; ';
				}

				$selectors = explode(',', $key);
				foreach ($selectors as $selector) {
					$style = $selector . '{' . $output . '}';
					self::getInstance()->document->addStyleDeclaration($style);
				}
			}
		}
	}

	/**
	* Exclude js and return others js
	*
	*/
	private static function excludeJS($key, $excludes)
	{
		if ($excludes) {
			$excludes = explode(',', $excludes);

			foreach ($excludes as $exclude) {
				if (JFile::getName($key) == trim($exclude)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	* function to compress js files
	*
	*/
	public static function compressJS($excludes = '')
	{
		require_once __DIR__ . '/classes/Minifier.php';

		$allScripts = self::getInstance()->document->_scripts;
		$cachePath = self::getInstance()->cachePath;
		$cacheTime = self::getInstance()->cacheTime;

		$scripts = array();
		$rootUrl = JUri::root(true);
		$minifiedCode = '';
		$md5sum = '';

		$tinyMCE = false;

		foreach ($allScripts as $key => $value) {
			$jsFile = str_replace($rootUrl, JPATH_ROOT, $key);

			if (strpos($jsFile, JPATH_ROOT) === false) {
				$jsFile = JPATH_ROOT . $key;
			}

			if (JFile::exists($jsFile))  {

				if (strpos($key, '/media/editors/tinymce/tinymce.min.js') !== false 
					|| strpos($key, '/media/editors/tinymce/js/tinymce.min.js') !== false 
					|| strpos($key, '/media/editors/tinymce/js/tiny-close.min.js') !== false) {
					$tinymce = true;
					unset(self::getInstance()->document->_scripts[$key]);
					continue;
				}

				if (!self::excludeJS($key, $excludes)) {
					$scripts[] = $key;
					$md5sum .= md5($key);
					$compressed = \JShrink\Minifier::minify(JFile::read($jsFile), array('flaggedComments' => false));

					//add file name to compressed JS
					$minifiedCode .= "/*------ " . JFile::getName($jsFile) . " ------*/\n" . $compressed . "\n\n";

					unset(self::getInstance()->document->_scripts[$key]);
				}
			}
		}

		if ($minifiedCode) {
			if (!JFolder::exists($cachePath)) {
				JFolder::create($cachePath, 0755);
			} else {
				$file = $cachePath . '/' . md5($md5sum) . '.js';

				if (!JFile::exists($file)) {
					JFile::write($file, $minifiedCode);
				} else {
					if (filesize($file) == 0 || ((filemtime($file) + $cacheTime * 60) < time())) {
						JFile::write($file, $minifiedCode);
					}
				}

				self::getInstance()->document->addScript(JUri::base(true) . '/cache/com_templates/templates/' . self::getInstance()->template . '/' . md5($md5sum) . '.js');
			}
		}

		if ($tinyMCE) {
			self::getInstance()->document->addScript(JURI::base(true) . '/media/editors/tinymce/tinymce.min.js');
			self::getInstance()->document->addScript(JURI::base(true) . '/media/editors/tinymce/js/tinymce.min.js');
			self::getInstance()->document->addScript(JURI::base(true) . '/media/editors/tinymce/js/tiny-close.min.js');
		}

		return;
	}

	/**
	* function to compress js files
	*
	*/
	public static function compressCSS()
	{
		require_once __DIR__ . '/classes/cssmin.php';

		$allStylesheet = self::getInstance()->document->_styleSheets;
		$cachePath = self::getInstance()->cachePath;
		$cacheTime = self::getInstance()->cacheTime;

		$stylesheets = array();
		$rootUrl = JUri::root(true);
		$minifiedCode = '';
		$md5sum = '';

		foreach ($allStylesheet as $key => $value) {
			$cssFile = str_replace($rootUrl, JPATH_ROOT, $key);

			if (strpos($cssFile, JPATH_ROOT) === false) {
				$cssFile = JPATH_ROOT . $key;
			}

			if (JFile::exists($cssFile)) {
				$stylesheets[] = $key;
				$md5sum .= md5($key);
				$compressed = CSSMinify::process(JFile::read($cssFile));

				$url = Utils::fixCssUrl($compressed, $key);

				// add file name to compressed css
				$minifiedCode .= "/*------ " . JFile::getName($cssFile) . " ------*/\n" . $url . "\n\n";

				unset(self::getInstance()->document->_styleSheets[$key]);
			}
		}

		if ($minifiedCode) {
			if (!JFolder::exists($cachePath)) {
				JFolder::create($cachePath, 0755);
			} else {
				$file = $cachePath . '/' . md5($md5sum) . '.css';

				if (!JFile::exists($file)) {
					JFile::write($file, $minifiedCode);
				} else {
					if (filesize($file) == 0 || ((filemtime($file) + $cacheTime * 60) < time())) {
						JFile::write($file, $minifiedCode);
					}
				}

				self::getInstance()->document->addStylesheet(JUri::base(true) . '/cache/com_templates/templates/' . self::getInstance()->template . '/' . md5($md5sum) . '.css');
			}
		}

		return;
	}

}

class Utils
{	
	/**
	* Convert object to array
	*
	*/
	public static function fixCssUrl($subject, $absUrl)
	{
		return preg_replace_callback('/url\(([^\)]*)\)/',
			function ($matches) {
				$url = str_replace(array('"', '\''), '', $matches[1]);

				global $absUrl;

				$base = dirname($absUrl);

				while (preg_match('/^\.\.\//', $url)) {
					$base = dirname($base);
					$url = substr($url, 3);
				}

				$url = $base .'/' . $url;

				return "url('$url')";

			}, $subject);
	}

	/**
	* Convert object to array
	*
	*/
	public static function object_to_array($obj)
	{
		if (is_object($obj)) {
			$obj = (array) $obj;
		}

		if (is_array($obj)) {
			$new = array();

			foreach($obj as $key => $val) {
				$new[$key] = self::object_to_array($val);
			}
		}
		else {
			$new = $obj;
		}

		return $new;
	}

	/**
	* font_key_search
	*
	*/
	public static function font_key_search($font, $fonts)
	{
		foreach ($fonts as $key => $value) {
			if ($value['family'] == $font) {
				return $key;
			}
		}

		return 0;
	}
}