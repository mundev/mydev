<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-side-widget is-module">
	<div class="es-side-widget__hd">
		<div class="es-side-widget__title">
			<?php echo JText::_('APP_USER_EVENTS_WIDGET_EVENTS'); ?>
			<span class="widget-label">(<?php echo $total; ?>)</span>
		</div>
	</div>
	<div class="es-side-widget__bd">
		<?php if ($total > 0) { ?>
		<ul class="g-list-inline g-list-inline--dashed t-lg-mb--md">
			<li class="active">
				<a href="#es-attending" role="tab" data-bs-toggle="tab">
					<span class="widget-label"><?php echo JText::_('APP_USER_EVENTS_WIDGET_ATTENDING_EVENTS'); ?></span>
				</a>
			</li>
			<li>
				<a href="#es-created" role="tab" data-bs-toggle="tab">
					<?php if (!empty($createdTotal) && $allowCreate) { ?>
					<span class="widget-label"><?php echo JText::_('APP_USER_EVENTS_WIDGET_CREATED_EVENTS'); ?></span>
					<?php } ?>
				</a>
			</li>
		</ul>
		<?php } ?>

		<div class="tab-content">
			<div class="tab-pane active" id="es-attending">
				<?php echo $this->html('widget.events', $attendingEvents); ?>
			</div>
			<div class="tab-pane" id="es-created">
				<?php if (!empty($createdTotal) && $allowCreate) { ?>
					<?php echo $this->html('widget.events', $createdEvents); ?>
				<?php } ?>
			</div>
		</div>

	</div>

	<?php if ($total > 0) { ?>
	<div class="es-side-widget__ft">
		<?php echo $this->html('widget.viewAll', 'COM_ES_VIEW_ALL', $viewAll); ?>
	</div>
	<?php } ?>
</div>
