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
$ssoFlag = false;

$helix3 = officehelix3::getInstance();
$loginlogo = $helix3->getParam('login_logo') ? $helix3->getParam('login_logo') : $helix3->getParam('logo_image');

if (!file_exists($loginlogo)) {
	$loginlogo = $helix3->getTemplateUri() . '/images/presets/preset1/logo-login.png';
}

$loginBackground = $helix3->getParam('login_background');

if (!file_exists($loginBackground) || is_null($loginBackground) || !$loginBackground) {
	$loginBackground = $helix3->getTemplateUri() . '/images/bg-login.jpg';
}

$loginPretext = $helix3->getParam('login_pretext_slogan');
$loginPosttext = $helix3->getParam('login_posttext_slogan');
?>
<div class="si-login-wrapper <?php echo ES::config()->get('general.site.lockdown.enabled') ? 'is-full-width': ''; ?>">
	<?php if ($helix3->getParam('login_layout') == 'minimalist') { ?>
	<div class="si-login" data-dashboard-guest-login>
		<div class="si-login__hd">
			<div class="si-login__logo">
				<!-- <div class="si-login__logo-img" style="background-image: url('<?php echo $loginlogo; ?>');"></div> -->
			</div>
			<div class="si-login__desc"><?php echo JText::_($loginPretext); ?></div>
		</div>
		<div class="si-login__bd">
			<form name="loginbox" id="loginbox" method="post" action="<?php echo JRoute::_('index.php');?>" class="si-login__form">

				<?php if ($sso->isEnabled('facebook') || $sso->isEnabled('twitter') || $sso->isEnabled('linkedin')) { ?>
				<?php $ssoFlag = true; ?>
				<div class="si-login__social">
					<div class="si-login__social-txt">
						<?php echo JText::_('OFFICE_LOGIN_WITH_SOCIAL_ACCOUNT'); ?>
					</div>
					<div class="es-login-social-container">

						<?php if ($sso->isEnabled('facebook')) { ?>
						<div class="es-login-social-container__cell">
							<?php echo $facebook->getLoginButton(ESR::registration(array('layout' => 'oauthDialog', 'client' => 'facebook', 'external' => true), false)); ?>
						</div>
						<?php } ?>

						<?php if ($sso->isEnabled('twitter')) { ?>
						<div class="es-login-social-container__cell">
							<?php echo $sso->getLoginButton('twitter'); ?>
						</div>
						<?php } ?>

						<?php if ($sso->isEnabled('linkedin')) { ?>
						<div class="es-login-social-container__cell">
							<?php echo $sso->getLoginButton('linkedin'); ?>
						</div>
						<?php } ?>

					</div>
				</div>
				<?php } ?>
				
				<div class="si-login__fields">
					<div class="si-login__form-title">
						<?php if ($ssoFlag) { ?>
						<?php echo JText::_('OFFICE_OR_LOGIN_WITH_YOUR_EMAIL'); ?>
						<?php } else { ?>
						<?php echo JText::_('OFFICE_LOGIN_WITH_YOUR_EMAIL'); ?>
						<?php } ?>
					</div>

					<fieldset class="t-lg-mt--lg">
						<div class="o-form-group">
							<label for="es-form-name"><?php echo $usernamePlaceholder; ?></label>
							<input id="es-form-name" type="text" class="o-form-control" name="username"
								placeholder="<?php echo $usernamePlaceholder; ?>" />
							<a href="<?php echo ESR::account(array('layout' => 'forgetUsername'));?>" class="si-login__forgot"><?php echo JText::_('OFFICE_FORGOT'); ?></a>
						</div>
						<div class="o-form-group">
							<label for="es-form-password"><?php echo JText::_('COM_EASYSOCIAL_LOGIN_PASSWORD_PLACEHOLDER', true);?></label>
							<input id="es-form-password" type="password" class="o-form-control" name="password" placeholder="<?php echo JText::_('COM_EASYSOCIAL_LOGIN_PASSWORD_PLACEHOLDER', true);?>" />
							<a href="<?php echo ESR::account(array('layout' => 'forgetPassword'));?>" class="si-login__forgot"><?php echo JText::_('OFFICE_FORGOT'); ?></a>
						</div>

						<?php if ($this->config->get('general.site.twofactor')) { ?>
						<div class="form-group">
							<input type="text" class="o-form-control " name="secretkey" placeholder="<?php echo JText::_('COM_EASYSOCIAL_LOGIN_TWOFACTOR_SECRET', true);?>" />
						</div>
						<?php } ?>

						<div class="o-grid-sm">
							<div class="o-grid-sm__cell">
								<div class="xes-login-box__rmb">
									<div class="o-checkbox">
										<input type="checkbox" id="es-quick-remember" type="checkbox" name="remember" value="1" />
										<label for="es-quick-remember">
											<?php echo JText::_('COM_EASYSOCIAL_LOGIN_REMEMBER_YOU');?>
										</label>
									</div>
								</div>
							</div>
							<div class="o-grid-sm__cell o-grid-sm__cell--right">
								
							</div>
						</div>
						<div class="">
							<button type="submit" class="btn btn-es-primary btn-block"><i class="fa fa-lock"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_LOGIN_BUTTON');?></button>
						</div>
	
				</div>

				<input type="hidden" name="option" value="com_easysocial" />
				<input type="hidden" name="controller" value="account" />
				<input type="hidden" name="task" value="login" />
				<input type="hidden" name="return" value="<?php echo $returnUrl; ?>" />
				<input type="hidden" name="returnFailed" value="<?php echo base64_encode(JRequest::getURI()); ?>" />
				<?php echo $this->html('form.token');?>
			</form>
		</div>
		<div class="si-login__ft">
			<div class="t-lg-mb--md"><?php echo JText::_($loginPosttext); ?></div>
			<a href="<?php echo ESR::registration();?>" class="btn btn-es-default-o"><?php echo JText::_('OFFICE_REGISTER_NEW_ACCOUNT'); ?></a>
		</div>
	</div>	
	<?php } else { ?>
	<div class="es-login" data-dashboard-guest-login>
		<div class="es-login-box">
			<div class="si-login-bg" style="background-image: url('<?php echo $loginBackground; ?>');"></div>
			<div class="o-grid o-grid--si-login">
				<div class="o-grid__cell">

					<div class="es-login-box__msg">
						<div class="si-login-box-msg">
							<div class="si-login-box-msg__hd">
								<div class="si-login-box-msg__hd-title"><?php echo ucfirst($this->jConfig->getValue('sitename')); ?></div>
								<div class="si-login-box-msg__hd-desc"><?php echo JText::_($loginPretext); ?></div>
							</div>
							<div class="si-login-box-msg__bd">
								<div class="si-login-box-msg__bd-title"><?php echo JText::_('COM_EASYSOCIAL_LOGIN_NO_ACCOUNT');?></div>
								<div class="si-login-box-msg__bd-desc"><?php echo JText::_('COM_EASYSOCIAL_LOGIN_REGISTER_NOW');?></div>
								<div>
									<a href="<?php echo ESR::registration();?>" class="btn btn-es-default btn-block"><i class="fa fa-globe"></i>&nbsp; <?php echo JText::_('OFFICE_REGISTER_NEW_ACCOUNT'); ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="o-grid__cell">
					<div class="es-login-box__form-wrap">
						<form name="loginbox" id="loginbox" method="post" action="<?php echo JRoute::_( 'index.php' );?>" class="es-login-box__form">
							<div class="es-login-box__form-title">
								<?php echo JText::_('COM_EASYSOCIAL_LOGIN_ALREADY_HAVE_ACCOUNT');?>
							</div>

							<fieldset class="t-lg-mt--lg">
								<div class="o-form-group">
									<input type="text" class="o-form-control" name="username" placeholder="<?php echo $usernamePlaceholder; ?>" />
								</div>
								<div class="o-form-group">
									<input type="password" class="o-form-control" name="password" placeholder="<?php echo JText::_('COM_EASYSOCIAL_LOGIN_PASSWORD_PLACEHOLDER', true);?>" />
								</div>

								<?php if ($this->config->get('general.site.twofactor')) { ?>
								<div class="form-group">
									<input type="text" class="o-form-control " name="secretkey" placeholder="<?php echo JText::_('COM_EASYSOCIAL_LOGIN_TWOFACTOR_SECRET', true);?>" />
								</div>
								<?php } ?>

								<div class="o-grid-sm">
									<div class="o-grid-sm__cell">
										<div class="xes-login-box__rmb">
											<div class="o-checkbox">
												<input type="checkbox" id="es-quick-remember" type="checkbox" name="remember" value="1" />
												<label for="es-quick-remember">
													<?php echo JText::_('COM_EASYSOCIAL_LOGIN_REMEMBER_YOU');?>
												</label>
											</div>
										</div>
									</div>
									<div class="o-grid-sm__cell o-grid-sm__cell--right">
										<button type="submit" class="btn btn-es-primary-o"><i class="fa fa-lock"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_LOGIN_BUTTON');?></button>
									</div>
								</div>

								<hr />

								<div class="t-text--center t-lg-mb--md">
									<?php if ($this->config->get('registrations.emailasusername')) { ?>
										<a href="<?php echo ESR::account(array('layout' => 'forgetPassword')); ?>"> <?php echo JText::_('COM_EASYSOCIAL_LOGIN_FORGOT_PASSWORD_FULL'); ?></a>
									<?php } else { ?>
										<a href="<?php echo ESR::account(array('layout' => 'forgetUsername'));?>"> <?php echo JText::_('COM_EASYSOCIAL_LOGIN_FORGOT_USERNAME');?></a> /
										<a href="<?php echo ESR::account(array('layout' => 'forgetPassword'));?>"> <?php echo JText::_('COM_EASYSOCIAL_LOGIN_FORGOT_PASSWORD');?></a>
									<?php } ?>
								</div>

								<?php if ($sso->hasSocialButtons()) { ?>
									<hr class="es-hr" />

									<div class="t-text--center t-lg-mb--md">
										<?php echo JText::_('OFFICE_LOGIN_WITH_SOCIAL_ACCOUNT');?>
									</div>

									<div class="es-login-social-container">

										<?php if ($sso->isEnabled('facebook')) { ?>
										<div class="es-login-social-container__cell">
											<?php echo $facebook->getLoginButton(ESR::registration(array('layout' => 'oauthDialog', 'client' => 'facebook', 'external' => true), false)); ?>
										</div>
										<?php } ?>

										<?php if ($sso->isEnabled('twitter')) { ?>
										<div class="es-login-social-container__cell">
											<?php echo $sso->getLoginButton('twitter'); ?>
										</div>
										<?php } ?>

										<?php if ($sso->isEnabled('linkedin')) { ?>
										<div class="es-login-social-container__cell">
											<?php echo $sso->getLoginButton('linkedin'); ?>
										</div>
										<?php } ?>

									</div>
								<?php } ?>
							</fieldset>

							<input type="hidden" name="option" value="com_easysocial" />
							<input type="hidden" name="controller" value="account" />
							<input type="hidden" name="task" value="login" />
							<input type="hidden" name="return" value="<?php echo $returnUrl; ?>" />
							<input type="hidden" name="returnFailed" value="<?php echo base64_encode(JRequest::getURI()); ?>" />
							<?php echo $this->html('form.token');?>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</div>