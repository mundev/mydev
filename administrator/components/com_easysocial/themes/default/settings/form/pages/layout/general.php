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
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_GENERAL_SETTINGS_FEATURES'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'zeroasplural.enabled', 'COM_ES_GENERAL_SETTINGS_ZERO_AS_PLURAL'); ?>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_SETTINGS_LOGO'); ?>

					<div class="col-md-7">
						<div class="mb-20">
							<div class="es-img-holder">
								<div class="es-img-holder__remove <?php echo !ES::hasOverride('email_logo') ? 't-hidden' : '';?>">
									<a href="javascript:void(0);" data-image-restore data-type="email_logo">
										<i class="fa fa-times"></i>&nbsp; <?php echo JText::_('COM_ES_REMOVE'); ?>
									</a>
								</div>
								<img src="<?php echo ES::getLogo(); ?>" width="120" data-image-source data-default="<?php echo ES::getLogo(true);?>" />
							</div>
						</div>
						<div style="clear:both;" class="t-lg-mb--xl">
							<input type="file" name="email_logo" id="email_logo" class="input" style="width:265px;" data-uniform />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_ES_MOBILE_SHORTCUT_SETTINGS'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'mobileshortcut.enabled', 'COM_ES_RENDER_MOBILE_SHORTCUT'); ?>
				
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_MOBILE_SHORTCUT_SETTINGS_ICON'); ?>

					<div class="col-md-7">
						<div class="mb-20">
							<div class="es-img-holder">
								<div class="es-img-holder__remove <?php echo !ES::hasOverride('mobile_icon') ? 't-hidden' : '';?>">
									<a href="javascript:void(0);" data-image-restore data-type='mobile_icon'>
										<i class="fa fa-times"></i>&nbsp; <?php echo JText::_('COM_ES_REMOVE'); ?>
									</a>
								</div>
								<img src="<?php echo ES::getMobileIcon(); ?>" width="120" data-image-source data-default="<?php echo ES::getMobileIcon(true);?>" />
							</div>
						</div>
						<div style="clear:both;" class="t-lg-mb--xl">
							<input type="file" name="mobile_icon" id="mobile_icon" class="input" style="width:265px;" data-uniform />
						</div>
					</div>
				</div>

				<?php echo $this->html('settings.textbox', 'mobileshortcut.name', 'COM_ES_MOBILE_SHORTCUT_SETTINGS_NAME'); ?>
				<?php echo $this->html('settings.textbox', 'mobileshortcut.shortname', 'COM_ES_MOBILE_SHORTCUT_SETTINGS_SHORTNAME'); ?>
			</div>
		</div>
	</div>

	<div class="col-md-6">

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_GENERAL_SETTINGS_LOGIN_LAYOUT'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_LOGIN_SETTINGS_USE_CUSTOM_IMAGE'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'login.custom.image', $this->config->get('login.custom.image'),'', array('data-toggle-upload')); ?>
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('login.custom.image') ? '' : 't-hidden';?>" data-login-image data-has-image="<?php echo ES::login()->hasLoginImage(); ?>" data-default-login-image="<?php echo ES::login()->getDefaultImage(); ?>">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_LOGIN_SETTINGS_IMAGE'); ?>

					<div class="col-md-7">
						<div>
							<img src="<?php echo ES::login()->getLoginImage(false, true);?>" class="" data-login-override-image />
						</div>

						<div>
							<input type="file" name="login_image" data-uniform data-login-image-upload />
							<span class="t-lg-ml--md" data-login-image-remove-wrap <?php if (!ES::login()->hasLoginImage()) { ?>style="display: none;"<?php } ?>> <?php echo JText::_( 'COM_EASYSOCIAL_OR' ); ?>
								<a href="javascript:void(0);" class="btn btn-sm btn-es-danger t-lg-ml--sm" data-login-image-remove-button>
									<?php echo JText::_('COM_EASYSOCIAL_REMOVE_LOGIN_IMAGE'); ?>
								</a>
							</span>
						</div>

						<div>
							<a href="https://www.123rf.com/#stackideas" class="btn btn-es-primary-o btn-sm t-lg-ml--xl" target="_blank">Download Hi-Quality Images</a>
						</div>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>
