<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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

<div class="es-container" data-profile-edit data-es-container>
	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render('module' , 'es-profile-edit-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

		<?php if ($this->config->get('users.layout.profiletitle', true) && $this->my->hasCommunityAccess()) { ?>
			<div class="es-side-widget">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_PROFILE_SIDEBAR_YOUR_PROFILE'); ?>

				<div class="es-side-widget__bd">
					<div class="es-side-profile-info">
						<?php echo JText::sprintf('COM_EASYSOCIAL_PROFILE_SIDEBAR_YOUR_PROFILE_INFO', '<a href="' . $profile->getPermalink() . '">' . $profile->getTitle() . '</a>');?>
					</div>

					<?php if ($profilesCount > 1 && $this->my->canSwitchProfile()) { ?>
					<a href="<?php echo ESR::profile(array('layout' => 'switchProfile'));?>" class="btn btn-es-default-o btn-sm btn-block t-lg-mt--md">
						<?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_SWITCH_PROFILE');?>
					</a>
					<?php } ?>
				</div>
			</div>
			<hr class="es-hr" />
		<?php } ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_PROFILE_SIDEBAR_ABOUT'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<?php $i = 0; ?>
					<?php foreach ($steps as $step) { ?>
						<li class="o-tabs__item<?php echo ($i == 0 && !$activeStep) || ($activeStep && $activeStep == $step->id) ? ' active' :'';?>" data-profile-edit-fields-step data-for="<?php echo $step->id;?>" data-actions="1">
							<a class="o-tabs__link" href="javascript:void(0);"><?php echo $step->get('title'); ?></a>
						</li>
						<?php $i++; ?>
					<?php } ?>
				</ul>
			</div>
		</div>

		<?php if ($oauthClients) { ?>
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_PROFILE_SIDEBAR_SOCIALIZE'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<?php foreach ($oauthClients as $client) { ?>
					<li class="o-tabs__item" data-profile-edit-fields-step data-for="oauth-<?php echo $client->getType();?>" data-actions="0">
						<a class="o-tabs__link" href="javascript:void(0);"><?php echo $client->getTitle();?></a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>

		<hr class="es-hr" />
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_OTHER_LINKS');?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<?php if ($this->config->get('privacy.enabled') && $this->my->hasCommunityAccess()) { ?>
					<li class="o-tabs__item">
						<a href="<?php echo ESR::profile(array('layout' => 'editPrivacy'));?>" class="o-tabs__link"><?php echo JText::_('COM_EASYSOCIAL_MANAGE_PRIVACY');?></a>
					</li>
					<?php } ?>

					<?php if ($this->my->hasCommunityAccess()) { ?>
					<li class="o-tabs__item">
						<a href="<?php echo ESR::profile(array('layout' => 'editNotifications'));?>" class="o-tabs__link"><?php echo JText::_('COM_EASYSOCIAL_MANAGE_ALERTS');?></a>
					</li>
					<?php } ?>

					<?php if ($showVerificationLink && $this->my->hasCommunityAccess()) { ?>
					<li class="o-tabs__item">
						<a href="<?php echo ESR::profile(array('layout' => 'submitVerification'));?>" class="o-tabs__link"><?php echo JText::_('COM_ES_SUBMIT_VERIFICATION');?></a>
					</li>
					<?php } ?>
					<?php if ($this->config->get('users.download.enabled')) { ?>
					<li class="o-tabs__item">
						<a href="<?php echo ESR::profile(array('layout' => 'download')); ?>" class="o-tabs__link"><?php echo JText::_('COM_ES_GDPR_DOWNLOAD_YOUR_INFORMATION'); ?></a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<?php if ($this->my->deleteable()) { ?>
		<hr class="es-hr" />
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_PROFILE_SIDEBAR_DELETE'); ?>

			<div class="es-side-widget__bd">
				<a href="javascript:void(0);" class="t-text--danger" data-profile-edit-delete>
					<?php echo JText::_('COM_EASYSOCIAL_DELETE_YOUR_PROFILE_BUTTON');?>
				</a>
			</div>
		</div>
		<?php } ?>

		<?php echo $this->render('module' , 'es-profile-edit-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper'); ?>
	</div>

	<div class="es-content">
		<form method="post" action="<?php echo JRoute::_('index.php'); ?>" class="es-forms" data-profile-fields-form autocomplete="off">

			<?php echo $this->render('module' , 'es-profile-edit-before-contents'); ?>

			<div class="tab-content">
				<?php $i = 0; ?>
				<?php foreach ($steps as $step) { ?>
				<div class="tab-content__item step-content step-<?php echo $step->id;?><?php echo ($i == 0 && !$activeStep) || ($activeStep && $activeStep == $step->id) ? ' active' :'';?>" 
					 data-profile-edit-fields-content data-id="<?php echo $step->id; ?>"
				>
					<?php if ($step->fields){ ?>
					<div class="es-forms__group">
						<div class="es-forms__content">
							<div class="o-form-horizontal">
								<?php foreach ($step->fields as $field) { ?>
									<?php echo $this->loadTemplate('site/registration/steps/field', array('field' => $field, 'errors' => '')); ?>

									<?php if (!$field->getApp()->id) { ?>
									<div class="o-alert o-alert--danger"><?php echo JText::_('COM_EASYSOCIAL_FIELDS_INVALID_APP'); ?></div>
									<?php } ?>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php $i++; ?>
				<?php } ?>

				<?php foreach ($oauthClients as $client) { ?>
					<?php echo $this->loadTemplate('site/profile/edit/' . $client->getType(), array('client' => $client)); ?>
				<?php } ?>
			</div>

			<div class="es-forms__actions">
				<div class="o-form-actions" data-profile-actions>

					<?php if ($this->my->hasCommunityAccess()) { ?>
					<div class="t-pull-left">
						<a href="<?php echo $this->my->getPermalink();?>" class="btn btn-es-default-o"><?php echo JText::_('COM_ES_CANCEL'); ?></a>
					</div>
					<?php } ?>

					<div class="t-pull-right">
						<button type="button" class="btn btn-es-primary-o" data-profile-fields-save-close><?php echo JText::_('COM_ES_UPDATE');?></button>

						<?php if (!$this->my->hasCommunityAccess()) { ?>
						<button type="button" class="btn btn-es-primary" data-profile-fields-save><?php echo JText::_('COM_ES_UPDATE');?></button>
						<?php } ?>
					</div>
				</div>
			</div>

			<?php echo $this->render('module' , 'es-profile-edit-after-contents'); ?>

			<input type="hidden" name="conditionalRequired" value="<?php echo ES::string()->escape($conditionalFields); ?>" data-conditional-check/>
			<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid');?>" />
			<input type="hidden" name="profileId" value="<?php echo $profile->id; ?>" />
			<input type="hidden" name="workflowId" value="<?php echo $workflow->id; ?>" />
			<input type="hidden" name="option" value="com_easysocial" />
			<input type="hidden" name="controller" value="profile" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="<?php echo ES::token();?>" value="1" />
			<input type="hidden" name="userId" value="<?php echo (int) $user->id;?>" />
		</form>	
	</div>
</div>
