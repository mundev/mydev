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
	<?php echo $this->html('widget.title', 'APP_CALENDAR_UPCOMING_APPOINTMENTS'); ?>

	<div class="es-side-widget__bd">
		<?php if ($appointments) { ?>
			<ul class="o-nav o-nav--stacked">
				<?php foreach ($appointments as $appointment) { ?>
				<li class="o-nav__item">
					<div class="es-side-widget__event t-lg-mb--xl">
						<div class="o-flag">
							<div class="o-flag__image o-flag--top">
								<div class="es-side-calendar-date">
									<div class="es-side-calendar-date__date">
										<?php echo $appointment->getStartDate()->format('d'); ?>
									</div>
									<div class="es-side-calendar-date__mth">
										<?php echo $appointment->getStartDate()->format('M'); ?>
									</div>
								</div>  
							</div>
							<div class="o-flag__body">
								<a href="<?php echo $app->getCanvasUrl(array('schedule_id' => $appointment->id, 'customView' => 'item', 'uid' => $this->my->getAlias(), 'type' => SOCIAL_TYPE_USER));?>" class="es-side-calendar-date__title">
									<?php echo $appointment->_('title');?>
								</a>
								<div class="t-text--muted t-fs--sm">
									<?php if ($appointment->all_day) { ?>
										<?php echo JText::_('APP_CALENDAR_ALL_DAY'); ?>
									<?php } ?>

								</div>
							</div>
						</div>    
					</div>
				</li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>
</div>