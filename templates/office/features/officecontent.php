<?php
/**
* @package      Office Template
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class OfficeHelix3FeatureOfficecontent 
{
	private $helix3;
	public $position;

	public function __construct($helix3)
	{
		$this->helix3 = $helix3;
		$this->position = 'officecontent';

		$this->user = JFactory::getUser();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
	}

	public function renderFeature()
	{
		// We'll need to make sure the special layout only applicable to EasySocial.
		// Other component will use the default 3 column.
		$option = $this->input->get('option','');
		$easysocial = $option == 'com_easysocial' ? true : false;
		$html = '<jdoc:include type="message" />';
		$html .= '<div class="office-container">';
		
		// Component section.
		$html .= '<div class="office-container__content">';
		

		// Only available on other than EasySocial page.
		if (!$easysocial) {
			$html .= '<div class="office-container__content-component">';

			$html .= '<div class="office-container__content-menu">';
			$html .= '<jdoc:include type="modules" name="left" style="sp_xhtml" />';
			$html .= '</div>';
			$html .= '<div class="office-container__content-component-wrapper">';
		}

		if ($this->helix3->countModules('officecontenttop')) {
			$html .= '<div class="officecontenttop"><jdoc:include type="modules" name="officecontenttop" style="sp_xhtml" /></div>';
		}

		$html .= '<jdoc:include type="component" />';

		if ($this->helix3->countModules('officecontentbottom')) {
			$html .= '<div class="officecontentbottom"><jdoc:include type="modules" name="officecontentbottom" style="sp_xhtml" /></div>';
		}

		if (!$easysocial) {
			$html .= '</div>';
			$html .= '</div>';
		}

		$html .= '</div>';

		// Right module position.
		$html .= '
			<div class="office-container__right">
				<jdoc:include type="modules" name="right" style="sp_xhtml" />
			</div>
			';
		$html .= '</div>';

		return $html;
	}    
}
