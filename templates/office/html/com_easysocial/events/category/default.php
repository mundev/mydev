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
<div class="es-events-category" data-events-category>
	<div class="es-container">

		<div class="es-sidebar" data-sidebar>

			<!-- do not remove this element. This element is needed for the stream loodmore to work properly -->
			<div data-filter-item data-type="eventcategory" data-id="<?php echo $category->id;?>" class="active" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>"></div>

			<?php echo $this->render('module', 'es-profiles-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

			<?php echo $this->includeTemplate('site/events/category/widgets/events'); ?>
			<?php echo $this->includeTemplate('site/events/category/widgets/albums'); ?>
			<?php echo $this->includeTemplate('site/events/category/widgets/guests'); ?>

			<?php echo $this->render('module', 'es-profiles-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>

		<div class="es-content">
			<div>
				<div class="es-snackbar pull-left">
					<h1 class="es-snackbar__title"><?php echo JText::_('COM_EASYSOCIAL_EVENTS_RECENT_UPDATES'); ?> - <?php echo $category->get('title'); ?></h1>
				</div>
				<div class="pull-right">
					<?php if ($this->access->allowed('events.create', true) && !$this->access->intervalExceeded('events.limit', $this->my->id) && $category->hasAccess('create', $this->my->profile_id) && !$category->container) { ?>
					<a href="<?php echo ESR::events(array('controller' => 'events', 'task' => 'selectCategory', 'category_id' => $category->id));?>" class="btn btn-es-primary btn-sm pull-right">
						<?php echo JText::_('COM_EASYSOCIAL_EVENTS_CREATE_EVENT'); ?>
					</a>
					<?php } ?>
				</div>
			</div>

			<div class="es-content-wrap" data-es-event-item-content>
				<?php echo $stream->html();?>
			</div>
		</div>
	</div>

</div>
