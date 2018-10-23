<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-side-widget">
	<?php echo $this->html('widget.title', 'APP_USER_ONESIGNAL_ALERTS_HEADING'); ?>

	<div class="es-side-widget__bd">
		<ul class="o-tabs o-tabs--stacked">
			<li class="o-tabs__item" data-es-alert-item data-type="push">
				<a href="javascript:void(0);" class="o-tabs__link"><?php echo JText::_('APP_USER_ONESIGNAL_ALERTS_HEADING');?></a>
			</li>
		</ul>
	</div>
</div>