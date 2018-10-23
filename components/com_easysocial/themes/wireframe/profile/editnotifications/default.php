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

<div class="es-container" data-es-container>
	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render('module', 'es-profile-editnotifications-sidebar-top'); ?>

		<?php $i = 0; ?>

		<?php foreach ($groups as $group) { ?>
			<?php if (isset($alerts[$group]) && $alerts[$group] ) { ?>
			<div class="es-side-widget">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_PROFILE_SIDEBAR_NOTIFICATIONS_GROUP_' . strtoupper($group)); ?>

				<div class="es-side-widget__bd">
					<ul class="o-tabs o-tabs--stacked">
						<?php foreach ($alerts[$group] as $element => $alert) { ?>
							<li class="o-tabs__item <?php echo ($i == 0 && !$activeTab) || ($activeTab == $element) ? 'active' : ''; ?>" data-es-alert-item data-type="<?php echo $element; ?>">
								<a href="javascript:void(0);" class="o-tabs__link"><?php echo $alert['title']; ?></a>
							</li>
							<?php $i++; ?>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>
		<?php } ?>

		<?php if ($customAlerts) { ?>
			<?php foreach ($customAlerts as $customAlert) { ?>
				<?php echo $customAlert->sidebar;?>
			<?php } ?>
		<?php } ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_OTHER_LINKS');?>
			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<li class="o-tabs__item">
						<a href="<?php echo ESR::profile(array('layout' => 'edit'));?>" class="o-tabs__link"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_EDIT_PROFILE');?></a>
					</li>
					<li class="o-tabs__item">
						<a href="<?php echo ESR::profile(array('layout' => 'editPrivacy'));?>" class="o-tabs__link"><?php echo JText::_('COM_EASYSOCIAL_MANAGE_PRIVACY');?></a>
					</li>
				</ul>
			</div>
		</div>

		<?php echo $this->render('module', 'es-profile-editnotifications-sidebar-bottom'); ?>
	</div>

	<div class="es-content">
		<?php echo $this->render( 'module' , 'es-profile-editnotifications-before-contents' ); ?>

		<form method="post" action="<?php echo JRoute::_('index.php');?>" class="es-forms">
			<div class="tab-content notification-content form-notifications">
				<?php $i = 0; ?>
				<?php foreach ($groups as $group) { ?>
					<?php if (isset($alerts[$group])) { ?>
						<?php foreach ($alerts[$group] as $element => $alert) { ?>
						<div class="tab-content__item notification-content-<?php echo $element; ?> <?php echo ($i == 0 && !$activeTab) || ($activeTab == $element) ? 'is-active' : '';?>" data-es-alert-contents="<?php echo $element;?>">
							<div class="es-forms__group">
								<div class="es-forms__title">
									<?php echo $this->html('form.title', $alert['title']); ?>
								</div>
								
								<div class="es-forms__content">
									<table width="100%">
										<tr>
											<td width="55%">&nbsp;</td>
											<td width="5%">&nbsp;</td>
											<td width="20%"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_NOTIFICATION_SYSTEM'); ?></td>
											<td width="20%"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_NOTIFICATION_EMAIL'); ?></td>
										</tr>
										<?php foreach( $alert[ 'data' ] as $rule ) { ?>
										<tr>
											<td><span class=""><?php echo $rule->getTitle(); ?></span></td>
											<td>
												<i class="fa fa-question-circle" <?php echo $this->html( 'bootstrap.popover' , $rule->getTitle() , $rule->getDescription()  , 'bottom' ); ?>></i>
											</td>
											<td class="t-lg-p--md t-text--center">
												<?php if ($rule->system_published) { ?>
													<?php echo $rule->system >= 0 ? $this->html('form.toggler', 'system[' . $rule->id . ']', $rule->system) : JText::_('COM_EASYSOCIAL_PROFILE_NOTIFICATION_NOT_APPLICABLE'); ?>
												<?php } else { ?>
												-
												<?php } ?>
											</td>
											<td class="t-lg-p--md t-text--center">
												<?php if ($rule->email_published) { ?>
													<?php echo $rule->email >= 0 ? $this->html('form.toggler', 'email[' . $rule->id .']', $rule->email) : JText::_('COM_EASYSOCIAL_PROFILE_NOTIFICATION_NOT_APPLICABLE'); ?>
												<?php } else { ?>
												-
												<?php } ?>
											</td>
										</tr>
										<?php } ?>
									</table>
								</div>
							</div>
						</div>
						<?php $i++; ?>
						<?php } ?>
					<?php } ?>
				<?php } ?>

				<?php if ($customAlerts) { ?>
					<?php foreach ($customAlerts as $customAlert) { ?>
						<?php echo $customAlert->contents; ?>
					<?php } ?>
				<?php } ?>
			</div>

			<div class="es-forms__actions">
				<div class="o-form-actions" data-es-form-action>
					<div class="pull-right">
						<button class="btn btn-es-primary-o"><?php echo JText::_('COM_EASYSOCIAL_SAVE_BUTTON');?></button>
					</div>
				</div>
			</div>

			<input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-alert-active />
			<?php echo $this->html('form.action', 'profile', 'saveNotification'); ?>
		</form>

		<?php echo $this->render('module', 'es-profile-editnotifications-after-contents'); ?>
	</div>
</div>



