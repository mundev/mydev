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

class OfficeHelix3FeatureOfficelogin
{
	private $helix3;
	public $position;

	public function __construct($helix3)
	{
		$this->helix3 = $helix3;
		$this->position = 'officelogin';
	}

	public function renderFeature()
	{
		$html = '';

		ob_start();
		?>
			<?php if (ES::config()->get('general.site.lockdown.enabled')) { ?>
				<jdoc:include type="message" />
				<jdoc:include type="component" />
			<?php } else { ?>
				<div class="office-container">
					<div class="office-container__content">
						<jdoc:include type="message" />
						<jdoc:include type="component" />
					</div>
					<div class="office-container__right">
						<jdoc:include type="modules" name="right" style="sp_xhtml" />
					</div>
				</div>
			<?php } ?>
		<?php
		$html = ob_get_clean();

		return $html;
	}    
}










