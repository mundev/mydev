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

<div class="es-container" data-es-container data-activities>

	<div class="es-sidebar" data-sidebar>

		<?php echo $this->render('module', 'es-activities-sidebar-top', 'site/activities/sidebar.module.wrapper'); ?>

		<?php echo $this->render('widgets', SOCIAL_TYPE_USER, 'activities', 'sidebarTop'); ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_ACTIVITY_SIDEBAR_FILTER'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked feed-items">
					<li class="o-tabs__item <?php echo $active == 'all' ? ' active' : '';?>"
						data-sidebar-item data-type="all">
						<a href="<?php echo FRoute::activities();?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_ALL_ACTIVITIES');?>">
							<?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_ALL_ACTIVITIES');?>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<li class="o-tabs__item <?php echo $active == 'hidden' ? ' active' : '';?>"
						data-sidebar-item data-type="hidden">
						<a href="<?php echo FRoute::activities(array('type' => 'hidden'));?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_ACTIVITIES');?>">
							<?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_ACTIVITIES' );?>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>

					<li class="o-tabs__item <?php echo $active == 'hiddenapp' ? ' active' : '';?>"
						data-sidebar-item data-type="hiddenapp">
						<a href="<?php echo FRoute::activities(array('type' => 'hiddenapp'));?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_APPS');?>">
							<?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_APPS' );?>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>

					<li class="o-tabs__item <?php echo $active == 'hiddenactor' ? ' active' : '';?>"
						data-sidebar-item
						data-type="hiddenactor">
						<a href="<?php echo FRoute::activities(array('type' => 'hiddenactor'));?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_ACTORS');?>">
							<?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_ACTORS' );?>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>

				</ul>
			</div>

		</div>

		<?php echo $this->render('widgets', SOCIAL_TYPE_USER, 'activities', 'sidebarMiddle'); ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_ACTIVITY_FILTER_BY_APPS'); ?>

			<div class="es-side-widget__bd">
				<?php if ($apps) { ?>
					<ul class="o-tabs o-tabs--stacked feed-items" data-activity-apps>
						<?php foreach ($apps as $app) { ?>
							<li class="o-tabs__item <?php echo $app->element == $active ? ' active' : '';?>"
								data-sidebar-item
								data-type="<?php echo $app->element; ?>"
							>
								<a href="<?php echo FRoute::activities(array('type' => $app->element));?>" class="o-tabs__link">
									<?php echo JText::_($app->title); ?>
								</a>
								<div class="o-loader o-loader--sm"></div>
							</li>
						<?php } ?>
					</ul>

				<?php } else { ?>
					<div class="t-text--muted"><?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_NO_APPS'); ?></div>
				<?php } ?>
			</div>

		</div>


		<?php echo $this->render('widgets', SOCIAL_TYPE_USER, 'activities', 'sidebarBottom'); ?>

		<?php echo $this->render('module', 'es-activities-sidebar-bottom', 'site/activities/sidebar.module.wrapper'); ?>
	</div>

	<div class="es-content" data-wrapper>
		<?php echo $this->html('html.loading'); ?>

		<?php echo $this->render('module', 'es-activities-before-contents'); ?>

		<div class="<?php echo !$activities ? 'is-empty': ''; ?>" data-contents>
			<?php echo $this->includeTemplate('site/activities/default/content', array('filtertype' => $filtertype)); ?>
		</div>

		<?php echo $this->render('module', 'es-activities-after-contents'); ?>
	</div>
</div>