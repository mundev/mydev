<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container" data-es-container data-es-tasks>
	<div class="es-sidebar" data-sidebar>

		<?php if ($user->isViewer()) { ?>
		<a class="btn btn-es-primary btn-block t-lg-mb--xl" href="javascript:void(0);" data-create><?php echo JText::_('APP_USER_TASKS_NEW_TASK_BUTTON'); ?></a>
		<?php } ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_STATISTICS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-nav o-nav--stacked">
					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted">
							<i class="es-side-widget__icon fa fa-tasks t-lg-mr--md"></i>
							<b><?php echo $counters['total'];?></b> <?php echo JText::_('COM_ES_TASKS'); ?>
						</span>
					</li>
					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted">
							<i class="es-side-widget__icon fa fa-users t-lg-mr--md"></i>
							<b><?php echo $counters['group'];?></b> <?php echo JText::_('COM_ES_GROUP_TASKS'); ?>
						</span>
					</li>
					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted">
							<i class="es-side-widget__icon fa fa-calendar t-lg-mr--md"></i>
							<b><?php echo $counters['event'];?></b> <?php echo JText::_('COM_ES_EVENT_TASKS'); ?>
						</span>
					</li>
				</ul>
			</div>
		</div>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_FILTERS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked feed-items">
					<li class="o-tabs__item has-notice active" data-tasks-filter="all">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_USER_TASKS_FILTER_ALL');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['total'];?></div>
					</li>

					<li class="o-tabs__item has-notice" data-tasks-filter="is-resolved">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_USER_TASKS_FILTER_RESOLVED');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['resolved'];?></div>
					</li>

					<li class="o-tabs__item has-notice" data-tasks-filter="is-unresolved">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_USER_TASKS_FILTER_UNRESOLVED');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['unresolved'];?></div>
					</li>

					<?php if (!$hidePersonal) { ?>
					<li class="o-tabs__item has-notice" data-tasks-filter="task-user">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_USER_TASKS_FILTER_USER');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['user'];?></div>
					</li>
					<?php } ?>

					<li class="o-tabs__item has-notice" data-tasks-filter="task-group">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_USER_TASKS_FILTER_GROUPS');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['group'];?></div>
					</li>

					<li class="o-tabs__item has-notice" data-tasks-filter="task-event">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_USER_TASKS_FILTER_EVENTS');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['event'];?></div>
					</li>
				</ul>
			</div>
		</div>

	</div>

	<div class="es-content">
		<div class="app-tasks">
			<div class="app-contents<?php echo !$tasks ? ' is-empty' : '';?>" data-app-contents>
				<div class="app-contents-data">
					<div class="form-item t-hidden" data-form>
						<div class="o-form-group es-island">
							<div class="o-input-group">
								<input type="text" class="o-form-control" value="" placeholder="<?php echo JText::_('APP_USER_TASKS_PLACEHOLDER', true);?>" data-form-title />

								<span class="o-input-group__btn">
									<a href="javascript:void(0);" class="btn btn-es-default-o" data-form-save>
										<i class="fa fa-check"></i>
									</a>

									<a href="javascript:void(0);" class="btn btn-es-danger-o" data-form-cancel>
										<i class="fa fa-remove"></i>
									</a>
								</span>
							</div>
						</div>
					</div>

					<div data-lists>
						<?php if ($tasks) { ?>
							<?php foreach ($tasks as $task) { ?>
								<?php echo $this->loadTemplate('themes:/apps/user/tasks/profile/item', array('task' => $task, 'user' => $user)); ?>
							<?php } ?>
						<?php } ?>
					</div>
				</div>

				<?php echo $this->html('html.emptyBlock', 'APP_USER_TASKS_NO_TASKS_YET', 'fa-checkbox'); ?>
			</div>
		</div>
	</div>
</div>
