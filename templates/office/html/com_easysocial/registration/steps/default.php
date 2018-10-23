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
<div class="es-container">
	<div class="es-sidebar">
		<?php echo $this->render('module', 'es-module-left' , 'site/dashboard/sidebar.module.wrapper'); ?>
	</div>
	
	<div class="es-content">

		<?php if ($this->config->get('registrations.steps.progress')) { ?>
			<?php echo $this->html('html.steps', $steps, $currentStep, 
							array('link' => ESR::registration(array('profile_id' => '0')), 'tooltip' => 'COM_EASYSOCIAL_REGISTRATIONS_SELECT_A_PROFILE'), 
							array('tooltip' => 'COM_EASYSOCIAL_REGISTRATIONS_REGISTRATION_COMPLETE')
						); ?>
		<?php } ?>

		<?php if ($totalProfiles > 1) { ?>
		<div class="profile-selected es-bg-shade es-island t-lg-p--lg t-lg-mb--lg">
			<i class="fa fa-users t-lg-mr--sm"></i> <?php echo JText::_('COM_EASYSOCIAL_REGISTRATION_REGISTERING_UNDER_PROFILE'); ?> <strong><?php echo $profile->get('title'); ?></strong>.
			<a href="<?php echo ESR::registration(array('profile_id' => '0'));?>"><?php echo JText::_('COM_EASYSOCIAL_REGISTRATION_SWITCH_PROFILE');?></a>
		</div>
		<?php } ?>

		<form action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" class="es-forms has-privacy" data-registration-form>

			<div class="es-forms__group">
				<div class="es-forms__content">
					<div class="o-form-horizontal">
						<?php if ($fields) { ?>
							<?php foreach ($fields as $field) { ?>
								<?php echo $this->loadTemplate('site/registration/steps/field', array('field' => $field, 'errors' => $errors)); ?>
							<?php } ?>
						<?php } ?>

						<div class="o-form-group">
							<div class="o-row">
								<div class="o-col-sm--8">
									<?php echo JText::_('COM_EASYSOCIAL_REGISTRATIONS_REQUIRED');?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="es-forms__actions">
				<div class="o-form-actions">
					<?php if ($currentStep != 1) { ?>
					<button class="btn btn-es-default-o pull-left" type="button" data-registration-previous><?php echo JText::_('COM_EASYSOCIAL_PREVIOUS_BUTTON'); ?></button>
					<?php } ?>
					<button class="btn btn-es-primary-o pull-right" type="button" data-registration-submit>
						<div class="o-loader o-loader--sm"></div> <?php echo $currentIndex === $totalSteps || $totalSteps < 2 ? JText::_('COM_EASYSOCIAL_SUBMIT_BUTTON') : JText::_('COM_EASYSOCIAL_CONTINUE_BUTTON');?>
					</button>
				</div>
			</div>

			<?php echo JHTML::_('form.token'); ?>
			<input type="hidden" name="conditionalRequired" value="<?php echo ES::string()->escape($conditionalFields); ?>" data-conditional-check />
			<input type="hidden" name="currentStep" value="<?php echo $currentIndex; ?>" />
			<input type="hidden" name="controller" value="registration" />
			<input type="hidden" name="task" value="saveStep" />
			<input type="hidden" name="option" value="com_easysocial" />
			<input type="hidden" name="profileId" value="<?php echo $profile->id; ?>" />
			<input type="hidden" name="workflowId" value="<?php echo $workflow->id; ?>" />
		</form>

	</div>
</div>