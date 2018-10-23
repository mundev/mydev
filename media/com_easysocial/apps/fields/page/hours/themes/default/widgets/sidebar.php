<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-side-widget">
	<?php echo $this->html('widget.title', $title); ?>

	<div class="es-side-widget__bd">

		<div class="es-side-operation-data ">
			<div class="o-flag">
				<div class="o-flag__image o-flag--top">
					<i class="fa fa-building-o es-side-operation-data__icons"></i>
				</div>
				<div class="o-flag__body">
					<div class="es-side-operation-data__item">
						<?php if ($alwaysOpen) { ?>
							<div class="t-text--success">
								<?php echo JText::_('COM_EASYSOCIAL_PAGE_FIELD_HOURS_ALWAYS_OPEN');?>
							</div>
						<?php } ?>

						<?php if ($open && !$alwaysOpen) { ?>
							<div class="es-side-operation-data__state t-text--success">
								<?php echo JText::_('COM_EASYSOCIAL_PAGE_FIELD_HOURS_OPENS_SIDEBAR');?>
							</div>
							<div class="es-side-operation-data__hr">
								<?php echo $timezone; ?>
							</div>
						<?php } ?>

						<?php if (!$open) { ?>
							<div class="es-side-operation-data__state<?php echo !$checkTime ? ' t-text--danger' : ''; ?>">
								<?php if ($checkTime) { ?>
								<?php echo JText::_('COM_EASYSOCIAL_PAGE_FIELD_HOURS_CHECKTIME_SIDEBAR'); ?>
								<?php } else { ?>
								<?php echo JText::_('COM_EASYSOCIAL_PAGE_FIELD_HOURS_CLOSED_SIDEBAR'); ?>
								<?php } ?>
							</div>
							<?php if ($nextOpenTime) { ?>
							<div class="es-side-operation-data__hr">
								<?php echo JText::sprintf('COM_EASYSOCIAL_PAGE_FIELD_HOURS_CLOSED_SIDEBAR_INFO', $nextOpenTime); ?>
							</div>
							<?php } ?>
							<?php if ($timezone) { ?>
								<div class="es-side-operation-data__hr">
									<?php echo $timezone; ?>
								</div>
							<?php } ?>
						<?php } ?>

						<?php if ($fullDetails && !$alwaysOpen) { ?>
						<div class="dropdown_ es-side-operation-data__dropdown">
							<a href="javascript:void(0);" class="es-side-operation-data__dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-caret-down"></i></a>

							<div class="dropdown-menu dropdown-menu-right">
								<?php foreach ($fullDetails as $day => $details) { ?>
									<div class="es-side-operation-data__item">
										<div class="es-side-operation-data__day <?php echo $details->class; ?>"><?php echo $day; ?></div>
										<div class="es-side-operation-data__hr <?php echo $details->class; ?>"><?php echo $details->string; ?></div>
									</div>
								<?php } ?>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
