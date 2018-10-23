<?php
/**
* @package      Office Template
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$doc = JFactory::getDocument();
$params = JFactory::getApplication()->getTemplate('true')->params;

$helix3_path = JPATH_PLUGINS . '/system/officehelix3/core/officehelix3.php';

if (file_exists($helix3_path)) {
	require_once($helix3_path);
	$this->helix3 = officehelix3::getInstance();
} else {
	die('Please install and activate office helix3 plugin');
}

//Error Logo
if ($logo_image = $params->get('error_logo')) {
	 $logo = JURI::root() . '/' .  $logo_image;
	 $path = JPATH_ROOT . '/' .  $logo_image;
} else {
	$logo = $this->baseurl . '/templates/' . $this->template . '/images/presets/preset1/logo.png';
	$path = JPATH_ROOT . '/templates/' . $this->template . '/images/presets/preset1/logo.png';
	$ratlogo = $this->baseurl . '/templates/' . $this->template . '/images/presets/preset1/logo@2x.png';
}

//Favicon
if($favicon = $params->get('favicon')) {
	$doc->addFavicon(JURI::base(true) . '/' .  $favicon);
} else {
	$doc->addFavicon($this->baseurl . '/templates/' . $this->template . '/images/favicon.ico');
}

//Stylesheets
$custom_css_path = JPATH_ROOT . '/templates/' . $this->template . '/css/custom.css';
if (file_exists($custom_css_path)) {
	$doc->addStylesheet($this->baseurl . '/templates/' . $this->template . '/css/custom.css');
}

$doc->addStylesheet($this->baseurl . '/templates/' . $this->template . '/css/bootstrap.min.css');
$doc->addStylesheet($this->baseurl . '/templates/' . $this->template . '/css/font-awesome.min.css');
$doc->addStylesheet($this->baseurl . '/templates/' . $this->template . '/css/template.css');

$doc->setTitle($this->error->getCode() . ' - '.$this->title);

if (!class_exists('JDocumentRendererHead')) {
	require_once(JPATH_LIBRARIES.'/joomla/document/html/renderer/head.php');	
}

$header_renderer = new JDocumentRendererHead($doc);
$header_contents = $header_renderer->render(null);

//background image
$error_bg = '';
$hascs_bg = '';
if ($err_bg = $params->get('error_bg')) {
	$error_bg = JURI::root() . $err_bg;
	$hascs_bg = 'has-background';
}

?>
<!DOCTYPE html>
<html class="error-page" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
		<?php echo $header_contents; ?>
		<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/presets/<?php echo $this->helix3->Preset(); ?>.css" rel="stylesheet" type="text/css" class="preset">
	</head>
	<body>
		<div class="error-page-inner <?php echo $hascs_bg; ?>" style="background-image: url(<?php echo $error_bg; ?>);">
			<div>
				<div class="container">
					<?php if (isset($logo) && $logo) { ?>
						<div class="error-logo-wrap">
							<img class="error-logo" alt="logo" src="<?php echo $logo; ?>" />
						</div>
					<?php } else { ?>
						<p><i class="fa fa-exclamation-triangle"></i></p>
					<?php } ?>
					<h1 class="error-code"><?php echo $this->error->getCode(); ?></h1>
					<p class="error-message"><?php echo $this->error->getMessage(); ?></p>
					<a class="btn btn-primary btn-lg" href="<?php echo $this->baseurl; ?>/" title="<?php echo JText::_('HOME'); ?>"><i class="fa fa-chevron-left"></i> <?php echo JText::_('HELIX_GO_BACK'); ?></a>
					<?php echo $doc->getBuffer('modules', '404', array('style' => 'sp_xhtml')); ?>
				</div>
			</div>
		</div>
	</body>
</html>