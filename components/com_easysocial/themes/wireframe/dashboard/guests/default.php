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
<div>
	<?php echo $this->html('html.login', $return); ?>
</div>

<?php if ($this->config->get('users.dashboard.guest', true)) { ?>
	<div class="es-dashboard">

		<?php if ($hasSidebarModules) { ?>
			<?php echo $this->html('responsive.toggle'); ?>
		<?php } ?>

		<div class="es-container" data-es-container>
			<?php if ($hasSidebarModules) { ?>
			<div class="es-sidebar" data-sidebar>
				<?php echo $this->render('module', 'es-dashboard-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>

				<div class="es-side-widget">
					<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS'); ?>

					<div class="es-side-widget__bd">
						<ul class="o-tabs o-tabs--stacked">
							<li class="o-tabs__item <?php echo $filter == 'everyone' ? ' active' : '';?>">
								<a href="javascript:void(0);" class="o-tabs__link">
									<?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS_EVERYONE');?>
								</a>
							</li>
						</ul>
					</div>

				</div>

				<?php echo $this->render('module', 'es-dashboard-sidebar-after-newsfeeds', 'site/dashboard/sidebar.module.wrapper'); ?>
				<?php echo $this->render('module', 'es-dashboard-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
			</div>

			<?php } ?>

			<div class="es-content">

				<?php echo $this->render('module', 'es-dashboard-before-contents'); ?>

				<div class="es-snackbar">
					<div class="es-snackbar__cell">
						<h1 class="es-snackbar__title">
						<?php if ($filter == 'hashtg') { ?>
							#<?php echo $hashtag;?>
						<?php } else { ?>
							<?php echo JText::_('COM_EASYSOCIAL_RECENT_UPDATES');?>
						<?php } ?>
						</h1>
					</div>

					<?php if ($this->config->get('stream.rss.enabled', true)) { ?>
					<div class="es-snackbar__cell">
						<a href="<?php echo $rssLink;?>" class=" pull-right btn-rss" target="_blank">
							<i class="fa fa-rss-square"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_SUBSCRIBE_VIA_RSS');?>
						</a>
					</div>
					<?php } ?>
				</div>

				<div>
					<?php echo $stream->html(false, JText::_('COM_EASYSOCIAL_UNITY_STREAM_LOGIN_TO_VIEW')); ?>
				</div>

				<?php echo $this->render('module', 'es-dashboard-after-contents'); ?>
			</div>
		</div>
	</div>
<?php } ?>
