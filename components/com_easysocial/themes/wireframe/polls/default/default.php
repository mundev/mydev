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
<?php if ($user) { ?>
<?php echo $this->html('cover.user', $user, 'polls'); ?>
<?php } ?>

<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container" data-es-polls data-es-container>
	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render('module', 'es-polls-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>

		<?php if ($showCreateButton) { ?>
		<a href="<?php echo $createButton;?>" class="btn btn-es-primary btn-block t-lg-mb--xl"><?php echo JText::_('COM_EASYSOCIAL_NEW_POLL');?></a>
		<?php } ?>

		<?php if (isset($showStatistic) && $showStatistic) { ?>
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_STATISTICS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-nav o-nav--stacked">
					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted">
							<i class="es-side-widget__icon fa fa-pie-chart t-lg-mr--md"></i>
							<b><?php echo $total;?></b> <?php echo JText::_('COM_EASYSOCIAL_POLLS');?>
						</span>
					</li>
				</ul>
			</div>
		</div>
		<?php } else { ?>
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_POLLS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<li class="o-tabs__item <?php echo $filter == 'all' ? 'active' : '';?>" data-filter="all">
						<a href="<?php echo $filterAllLink; ?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_POLLS_ALL_POLLS');?>"><?php echo JText::_('COM_EASYSOCIAL_POLLS_ALL_POLLS');?></a>
					</li>

					<?php if ($this->my->id) { ?>
					<li class="o-tabs__item <?php echo $filter == 'mine' ? 'active' : '';?>" data-filter="mine">
						<a href="<?php echo $filterMineLink; ?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_POLLS_MY_POLLS');?>"><?php echo JText::_('COM_EASYSOCIAL_POLLS_MY_POLLS');?></a>
							<div class="o-loader o-loader--sm"></div>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>

		<?php echo $this->render('module', 'es-polls-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
	</div>

	<div class="es-content">
		<?php echo $this->render('module', 'es-polls-before-contents'); ?>

		<div data-contents>
			<?php echo $this->includeTemplate('site/polls/default/wrapper'); ?>

			<?php echo $this->html('html.loading'); ?>
		</div>

		<?php echo $this->render('module', 'es-polls-after-contents'); ?>
	</div>
</div>
