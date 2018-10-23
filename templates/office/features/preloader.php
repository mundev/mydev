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

class OfficeHelix3FeaturePreloader {

	private $helix3;

	public function __construct($helix){
		$this->helix3 = $helix;
		$this->position = 'helixpreloader';
	}

	public function renderFeature() {

		$app = JFactory::getApplication();
		//Load Helix
		$helix3_path = JPATH_PLUGINS . '/system/officehelix3/core/officehelix3.php';
		if (file_exists($helix3_path)) {
			require_once($helix3_path);
			$getHelix3 = officehelix3::getInstance();
		} else {
			die('Please install and activate office helix3 plugin');
		}

		$output = '';
		if ($getHelix3->getParam('preloader')) {
			//Pre-loader -->
			$output .= '<div class="sp-pre-loader">';
				if ($getHelix3->getParam('preloader_animation') == 'double-loop') {
					// Bubble Loop loader
					$output .= '<div class="sp-loader-bubble-loop"></div>';
				} elseif ($getHelix3->getParam('preloader_animation') == 'wave-two') {
					// Audio Wave 2 loader
					$output .= '<div class="wave-two-wrap">';
						$output .= '<ul class="wave-two">';
							$output .= '<li></li>';
							$output .= '<li></li>';
							$output .= '<li></li>';
							$output .= '<li></li>';
							$output .= '<li></li>';
							$output .= '<li></li>';
						$output .= '</ul>'; //<!-- /.Audio Wave 2 loader -->
					$output .= '</div> >'; // <!-- /.wave-two-wrap -->

				} elseif ($getHelix3->getParam('preloader_animation') == 'audio-wave') {
					// Audio Wave loader
					$output .= '<div class="sp-loader-audio-wave"> </div>';
				} elseif ($getHelix3->getParam('preloader_animation') == 'circle-two') {
					// Circle two Loader
					$output .= '<div class="circle-two">';
						$output .= '<span></span>';
					$output .= '</div>'; // /.Circle two loader
				} elseif ($getHelix3->getParam('preloader_animation') == 'clock') {
					//Clock loader
					$output .= '<div class="sp-loader-clock"></div>';
				} elseif ($getHelix3->getParam('preloader_animation') == 'logo') {

					if ($getHelix3->getParam('logo_image')) {
						$logo = JUri::root() . '/' . $getHelix3->getParam('logo_image');
					} else {
						$logo = JUri::root() . '/templates/' . $app->getTemplate() . '/images/presets/' . $getHelix3->Preset() . '/logo.png';
					}

					// Line loader with logo
					$output .= '<div class="sp-loader-with-logo">';
						$output .= '<div class="logo">';
							$output .= '<img src="' . $logo . '" alt="">';
						$output .= '</div>';
						$output .= '<div class="line" id="line-load"></div>';
					$output .= '</div>'; // /.Line loader with logo

				} else {
					// Circle loader
					$output .= '<div class="sp-loader-circle"></div>'; // /.Circular loader
				}
			$output .= '</div>'; // /.Pre-loader

		} // if enable preloader

		echo $output;
	} //renderFeature
}
