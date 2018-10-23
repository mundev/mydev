<?php
/**
* @package      Office Template
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Office Template is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$doc = JFactory::getDocument();
$app = JFactory::getApplication();

JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework'); //Force load Bootstrap

unset($doc->_scripts[$this->baseurl . '/media/jui/js/bootstrap.min.js']); // Remove joomla core bootstrap

//Load Helix
$helix3_path = JPATH_PLUGINS . '/system/officehelix3/core/officehelix3.php';

if (file_exists($helix3_path)) {
	require_once($helix3_path);
	$this->helix3 = officehelix3::getInstance();
} else {
	die('Please install and activate office helix3 plugin');
}

require_once(__DIR__ . '/helper.php');

// This template requires ES
ES::initialize();

//Coming Soon
if ($this->helix3->getParam('comingsoon_mode')) {
	header("Location: " . $this->baseUrl . "?tmpl=comingsoon");
}

//Body Background Image
if ($bg_image = $this->helix3->getParam('body_bg_image')) {

	$body_style = 'background-image: url(' . JURI::base(true) . '/' . $bg_image . ');';
	$body_style .= 'background-repeat: ' . $this->helix3->getParam('body_bg_repeat') . ';';
	$body_style .= 'background-size: ' . $this->helix3->getParam('body_bg_size') . ';';
	$body_style .= 'background-attachment: ' . $this->helix3->getParam('body_bg_attachment') . ';';
	$body_style .= 'background-position: ' . $this->helix3->getParam('body_bg_position') . ';';
	$body_style = 'body.site {' . $body_style . '}';

	$doc->addStyledeclaration($body_style);
}

//Body Font
$webfonts = array();

if ($this->params->get('enable_body_font')) {
	$webfonts['body'] = $this->params->get('body_font');
}

//Heading1 Font
if ($this->params->get('enable_h1_font')) {
	$webfonts['h1'] = $this->params->get('h1_font');
}

//Heading2 Font
if ($this->params->get('enable_h2_font')) {
	$webfonts['h2'] = $this->params->get('h2_font');
}

//Heading3 Font
if ($this->params->get('enable_h3_font')) {
	$webfonts['h3'] = $this->params->get('h3_font');
}

//Heading4 Font
if ($this->params->get('enable_h4_font')) {
	$webfonts['h4'] = $this->params->get('h4_font');
}

//Heading5 Font
if ($this->params->get('enable_h5_font')) {
	$webfonts['h5'] = $this->params->get('h5_font');
}

//Heading6 Font
if ($this->params->get('enable_h6_font')) {
	$webfonts['h6'] = $this->params->get('h6_font');
}

//Navigation Font
if ($this->params->get('enable_navigation_font')) {
	$webfonts['.sp-megamenu-parent'] = $this->params->get('navigation_font');
}

//Custom Font
if ($this->params->get('enable_custom_font') && $this->params->get('custom_font_selectors')) {
	$webfonts[$this->params->get('custom_font_selectors')] = $this->params->get('custom_font');
}

$this->helix3->addGoogleFont($webfonts);

//Custom CSS
if ($custom_css = $this->helix3->getParam('custom_css')) {
	$doc->addStyledeclaration($custom_css);
}

//Custom JS
if ($custom_js = $this->helix3->getParam('custom_js')) {
	$doc->addScriptdeclaration($custom_js);
}

JHtml::_('stylesheet', 'system/frontediting.css', array(), true);
JHtml::_('script', 'system/frontediting.js', false, true);

//preloader & goto top
$doc->addScriptdeclaration("\nvar sp_preloader = '" . $this->params->get('preloader') . "';\n");
$doc->addScriptdeclaration("\nvar sp_gotop = '" . $this->params->get('goto_top') . "';\n");
$doc->addScriptdeclaration("\nvar sp_offanimation = '" . $this->params->get('offcanvas_animation') . "';\n");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>

		<?php
			if ($favicon = $this->helix3->getParam('favicon')) {
				$doc->addFavicon(JURI::base(true) . '/' . $favicon);
			} else {
				$doc->addFavicon($this->helix3->getTemplateUri() . '/images/favicon.ico');
			}
			?>
		<!-- head -->
		<jdoc:include type="head" />
		<?php
			$megabgcolor = ($this->helix3->PresetParam('_megabg')) ? $this->helix3->PresetParam('_megabg') : '#ffffff';
			$megabgtx = ($this->helix3->PresetParam('_megatx')) ? $this->helix3->PresetParam('_megatx') : '#333333';
			
			$preloader_bg = ($this->helix3->getParam('preloader_bg')) ? $this->helix3->getParam('preloader_bg') : '#f5f5f5';
			$preloader_tx = ($this->helix3->getParam('preloader_tx')) ? $this->helix3->getParam('preloader_tx') : '#f5f5f5';
			
			// load css, less and js
			$scripts = 'bootstrap.min.js, jquery.sticky.js, main.js, frontend-edit.js';

			$this->helix3->addCSS('bootstrap.min.css, font-awesome.min.css')
					->addJS($scripts)
					->lessInit()->setLessVariables(array(
						'preset' => $this->helix3->Preset(),
						'active_color' => $this->helix3->PresetParam('_active'),
						'major_color' => $this->helix3->PresetParam('_major'),
						'preloader_bg' => $preloader_bg,
						'preloader_tx' => $preloader_tx,
					))
					->addLess('legacy/bootstrap', 'legacy')
					->addLess('master', 'template')
					->addLess('frontend-edit', 'frontend-edit');
			
			//RTL
			if ($this->direction == 'rtl') {
				$this->helix3->addCSS('bootstrap-rtl.min.css')
						->addLess('rtl', 'rtl');
			}
			
			$this->helix3->addLess('presets', 'presets/' . $this->helix3->Preset(), array('class' => 'preset'));
			
			//Before Head
			if ($before_head = $this->helix3->getParam('before_head')) {
				echo $before_head . "\n";
			}
			?>
	</head>
	<body class="<?php echo $this->helix3->bodyClass(); ?>" data-oid="<?php echo base64_encode(JFactory::getUser()->id); ?>">

		<div class="body-wrapper">
			<div class="body-innerwrapper">
			<?php $this->helix3->generatelayout(); ?>
			</div>
			<!-- /.body-innerwrapper -->
		</div>
		<div class="mobile-footer">
			<div class="mobile-footbar">
				<div class="mobile-footbar__item">
					<a href="javascript:void(0);" class="mobile-footbar__item-link" data-office-left-toggle>
						<i class="mobile-footbar__item-left-icon fa fa-align-left"></i>
					</a>
				</div>

				<div class="mobile-footbar__item mobile-footbar__item--center"></div>

				<div class="mobile-footbar__item">
					<a href="javascript:void(0);" class="mobile-footbar__item-link" data-office-right-toggle>
						<i class="mobile-footbar__item-right-icon fa fa-align-right"></i>
					</a>
				</div>
			</div>
		</div>
		<div class="sidebar-element" data-spy="affix" data-offset-top="1">
			<?php if ($this->helix3->countModules('sidebar')) { ?>
			  <jdoc:include type="modules" name="sidebar" style="sp_xhtml" />
			<?php } else { ?>
			  <p class="alert alert-warning">
				<?php echo JText::_('HELIX_NO_MODULE_OFFCANVAS'); ?>
			  </p>
			<?php } ?>
		</div>
		<?php if ($this->params->get('goto_top')) { ?>
		<a href="javascript:void(0)" class="scrollup">&nbsp;</a>
		<?php } ?>

		<?php
			if ($this->params->get('compress_css')) {
				$this->helix3->compressCSS();
			}
			
			$tempOption = $app->input->get('option');
			
			if ( $this->params->get('compress_js') && $tempOption != 'com_config' ) {
				$this->helix3->compressJS($this->params->get('exclude_js'));
			}
			
			//before body
			if ($before_body = $this->helix3->getParam('before_body')) {
				echo $before_body . "\n";
			} 
		?>
		<!-- /.body-innerwrapper -->
		<jdoc:include type="modules" name="debug" />
		<!-- Preloader -->
		<jdoc:include type="modules" name="helixpreloader" />
		<!-- Go to top -->
	</body>
</html>