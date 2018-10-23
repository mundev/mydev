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

class OfficeHelix3FeatureOfficeHeader 
{
	private $helix3;
	public $position;

	public function __construct($helix3)
	{
		$this->helix3 = $helix3;
		$this->position = 'officeheader';
	}

	public function renderFeature()
	{
		$user = JFactory::getUser();

		$html = '<div class="office-header">';
		$html .= $this->renderLogo();
		
		if (!$user->guest) {
			
			$html .= '
					<div class="office-header__content">

						<div id="sp-search" class=" ">
						<jdoc:include type="modules" name="search" style="none" />
						<a href="javascript:void(0);" class="sp-search-toggle" data-office-search-toggle>
							<span class="sp-search-toggle__icons">
								<i class="fa fa-times"></i>
							</span>
						</a>
						</div>
						<a href="javascript:void(0);" class="sp-search-toggle" data-office-search-toggle>
							<span class="sp-search-toggle__icons">
								<i class="fa fa-search"></i>
							</span>
						</a>
					</div>
					';
			
			$html .= '
					<div class="office-header__right">
						<div class="es-top-mod-group">
							<div id="sp-topnotification" class="es-top-mod-group__item">

								<jdoc:include type="modules" name="topnotification" style="none" />
								<a href="javascript:void(0);" class="sp-topnotification-toggle" data-office-noti-toggle>
									<span class="sp-topnotification-toggle__icons">
										<i class="fa"></i>
									</span>
									<span class="sp-topnotification-toggle__bubble">
									</span>
								</a>
							</div>
							<div id="sp-topesmenu" class="es-top-mod-group__item">
								<jdoc:include type="modules" name="topesmenu" style="none" />
							</div>
						</div>
					</div>
					';
			
		} else {
			$html .= '
					<div class="office-header__content">
						<div id="sp-search" class=" ">
						</div>
					</div>
					';
			
			$html .= '
					<div class="office-header__right">
						<div class="es-top-mod-group">
							<div id="sp-topesmenu" class="es-top-mod-group__item">
								<jdoc:include type="modules" name="topesmenu" style="none" />
							</div>
						</div>
					</div>
					';
		}

		$html .= '</div>';
		

		return $html;
	}

	/**
	 * Responsible to render logo.
	 *
	 * @since   1.0
	 * @access  public
	 */
	public function renderLogo()
	{
		// Retina logo
		if ($this->helix3->getParam('logo_type') == 'image') {
			jimport('joomla.image.image');

			if ($this->helix3->getParam('logo_image')) {
				$path = JPATH_ROOT . '/' . $this->helix3->getParam('logo_image');
			} else {
				$path = JPATH_ROOT . '/templates/' . $this->helix3->getTemplate() . '/images/presets/' . $this->helix3->Preset() . '/logo.png';
			}

			if(file_exists($path)) {
				$image = new JImage( $path );
				$width 	= $image->getWidth();
				$height = $image->getHeight();
			} else {
				$width 	= '';
				$height = '';
			}
		}

		$html  = '';
		$custom_logo_class = '';
		$sitename = JFactory::getApplication()->get('sitename');

		if ($this->helix3->getParam('mobile_logo')) {
			$custom_logo_class = ' hidden-xs';
		}

		$html .= '<div class="office-header__left"><div id="sp-logo" class=" "><div class="office-logo-section">';

		// Sidebar toggle
		$html .= '<div class="office-logo-section__item"><a id="sidebar-toggler" href="#"><i class="fa fa-remove"></i><i class="fa fa-bars"></i></a></div>';

		$html .= '<div class="office-logo-section__item">';

		if ($this->helix3->getParam('logo_type') == 'image') {

			$html .= '<div class="office-logo">';
			$html .= '<a href="' . JURI::base(true) . '/">';

			// $html .= '<span class="office-logo-img" style="background-image:url('. $this->helix3->getParam('logo_image') .');">';

			if ($this->helix3->getParam('logo_image')) {
				
				// $html .= '<img class="sp-default-logo'. $custom_logo_class .'" src="' . $this->helix3->getParam('logo_image') . '" alt="'. $sitename .'">';
				$html .= '<span class="office-logo-img " style="background-image:url('. $this->helix3->getParam('logo_image') . ');">';

				if ($this->helix3->getParam('logo_image_2x')) {
					// $html .= '<img class="sp-retina-logo'. $custom_logo_class .'" src="' . $this->helix3->getParam('logo_image_2x') . '" alt="'. $sitename .'" width="' . $width . '" height="' . $height . '">';
				}

				if ($this->helix3->getParam('mobile_logo')) {
					// $html .= '<img class="sp-default-logo visible-xs" src="' . $this->helix3->getParam('mobile_logo') . '" alt="'. $sitename .'">';
				}

			} 
			// Default logo
			else {
				$html .= '<span class="office-logo-img" style="background-image:url('. $this->helix3->getTemplateUri() . '/images/presets/' . $this->helix3->Preset() . '/logo.png);">';

				// $html .= '<span class="office-logo-img is-retina" style="background-image:url('. $this->helix3->getTemplateUri() . '/images/presets/' . $this->helix3->Preset() . '/logo@2x.png);">';
				// $html .= '<img class="sp-default-logo'. $custom_logo_class .'" src="' . $this->helix3->getTemplateUri() . '/images/presets/' . $this->helix3->Preset() . '/logo.png" alt="'. $sitename .'">';
				// $html .= '<img class="sp-retina-logo'. $custom_logo_class .'" src="' . $this->helix3->getTemplateUri() . '/images/presets/' . $this->helix3->Preset() . '/logo@2x.png" alt="'. $sitename .'" width="' . $width . '" height="' . $height . '">';

				if ($this->helix3->getParam('mobile_logo')) {
					// $html .= '<img class="sp-default-logo visible-xs" src="' . $this->helix3->getParam('mobile_logo') . '" alt="'. $sitename .'">';
				}
			}

			$html .= '</span></a>';
			$html .= '</div>';
		} 
		// Text logo
		else {

			$html .= '<div class="office-logo">';
			$html .= '<a href="' . JURI::base(true) . '/">';

			if ($this->helix3->getParam('logo_text')) {
				$html .= $this->helix3->getParam('logo_text');
			} else {
				$html .= $sitename;
			}

			$html .= '</a>';
			$html .= '</div>';
		}

		$html .= '</div></div></div></div>';

		return $html;
	}
}
















