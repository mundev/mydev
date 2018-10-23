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

class OfficeHelix3FeatureOfficeFooter 
{
	private $helix3;

	public function __construct($helix3)
	{
		$this->helix3 = $helix3;
		$this->position = 'officefooter';
	}

	public function renderFeature() 
	{
		$output = '';
		
		if ($this->helix3->getParam('enabled_copyright')) {
			$copyright = JText::_($this->helix3->getParam('copyright'));
			
			if( $this->helix3->getParam('copyright') ) {
				$output .= '<span class="sp-copyright">' . str_ireplace('{year}',date('Y'), $copyright) . '</span>';
			}
		}
		
		return $output;
	}    
}