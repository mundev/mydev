<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-label o-label--<?php echo $open || $checkTime ? 'success' : 'danger';?>-o ">

	<?php if ($alwaysOpen) { ?>
		<span class="es-profile-header__label-wrap-txt">
			<?php echo JText::_('COM_EASYSOCIAL_PAGE_FIELD_HOURS_ALWAYS_OPEN');?>
		</span>
	<?php } ?>

	<?php if (!$alwaysOpen && $fullDetails) { ?>
		<a href="javascript:void(0);"
			data-popbox=""
			data-popbox-position="bottom-right"
			data-popbox-id="es"
			data-popbox-type="operation-hr"
			data-popbox-toggle="click"
			data-popbox-target="[data-hour-popbox-dropdown]"
			data-popbox-offset="8"
			class="es-profile-header__label-wrap-txt"
		><?php echo JText::_('COM_EASYSOCIAL_PAGE_FIELD_HOURS_CHECKTIME'); ?>&nbsp;<i class="fa fa-caret-down"></i></a>
		<div class="t-hidden" data-hour-popbox-dropdown>
			<div class="popbox-dropdown">
				<div class="popbox-dropdown__bd">
					<div class="es-widget-operation-data">
					<?php foreach ($fullDetails as $day => $details) { ?>
						<div class="es-widget-operation-data__item">
							<div class="es-widget-operation-data__day <?php echo $details->class; ?>"><?php echo $day; ?></div>
							<div class="es-widget-operation-data__hr <?php echo $details->class; ?>"><?php echo $details->string; ?></div>
						</div>
					<?php } ?>
					</div>
				</div>
			</div>
		</div>

	<?php } ?>

</div>
