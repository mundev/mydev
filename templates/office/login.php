<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

defined('_JEXEC') or die;
$doc = JFactory::getDocument();
$app = JFactory::getApplication();

//Load Helix
$helix3_path = JPATH_PLUGINS . '/system/officehelix3/core/officehelix3.php';
if (file_exists($helix3_path)) {
	require_once($helix3_path);
	$this->helix3 = officehelix3::getInstance();
} else {
	die('Please install and activate office helix3 plugin');
}

//custom css file
$custom_css_path = JPATH_ROOT . '/templates/' . $this->template . '/css/custom.css';

//Comingsoon Logo
if ($logo_image = $this->params->get('comingsoon_logo')) {
	 $logo = JURI::root() . '/' .  $logo_image;
	 $path = JPATH_ROOT . '/' .  $logo_image;
} else {
	$logo 		= $this->baseurl . '/templates/' . $this->template . '/images/presets/preset1/logo.png';
	$path 		= JPATH_ROOT . '/templates/' . $this->template . '/images/presets/preset1/logo.png';
	$ratlogo 	= $this->baseurl . '/templates/' . $this->template . '/images/presets/preset1/logo@2x.png';
}

if(file_exists($path)) {
	$image 		 = new JImage( $path );
	$logo_width  = $image->getWidth();
	$logo_height = $image->getHeight();
} else {
	$logo_width 	= '';
	$logo_height = '';
}

//Load jQuery
JHtml::_('jquery.framework');

?>
<!DOCTYPE html>
<html class="si-login-page" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>

	<?php
	if($favicon = $this->helix3->getParam('favicon')) {
		$doc->addFavicon( JURI::base(true) . '/' .  $favicon);
	} else {
		$doc->addFavicon( $this->helix3->getTemplateUri() . '/images/favicon.ico' );
	}
	?>
	<jdoc:include type="head" />
	<?php
	$this->helix3->addCSS('bootstrap.min.css, font-awesome.min.css')
		->lessInit()->setLessVariables(array(
			'preset'=>$this->helix3->Preset(),
			// 'bg_color'=> $this->helix3->PresetParam('_bg'),
			// 'text_color'=> $this->helix3->PresetParam('_text'),
			'active_color' => $this->helix3->PresetParam('_active'),
			'major_color'=> $this->helix3->PresetParam('_major')
			// 'preloader_bg' => $preloader_bg,
			// 'preloader_tx' => $preloader_tx
			))
		->addLess('master', 'template')
		->addLess('presets',  'presets/'.$this->helix3->Preset());
		// ->addJS('jquery.countdown.min.js');
		// has exist custom.css then load it
		if (file_exists($custom_css_path)) {
			 $this->helix3->addCSS('custom.css');
		}

		//background image
		// $comingsoon_bg = '';
		// $hascs_bg = '';
		// if ($cs_bg = $this->params->get('comingsoon_bg')) {
		// 	$comingsoon_bg 	= JURI::root() . $cs_bg;
		// 	$hascs_bg 		= 'has-background';
		// }
	?>
</head>
<body>
	<!-- <div class="container"> -->
		<jdoc:include type="modules" name="login" style="none" />
	<!-- </div> -->

</body>
</html>