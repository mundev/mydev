<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access'); 
?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA_IMAGE'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_captcha_registered', 'COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA_REGISTERED'); ?>
				
				<?php echo $this->html('settings.dropdown', 'comment_captcha_type', 'COM_EASYBLOG_SETTINGS_COMMENTS_CAPTCHA_TYPE',
						array('none' => 'COM_EASYBLOG_CAPTCHA_DISABLED', 'builtin' => 'COM_EASYBLOG_CAPTCHA_BUILTIN', 'recaptcha' => 'COM_EASYBLOG_CAPTCHA_RECAPTCHA'),
						'',
						'data-captcha-type'
					); ?>

				<div class="form-group <?php echo $this->config->get('comment_captcha_type') != 'recaptcha' ? 'hidden' : '';?>" data-captcha="recaptcha">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PUBLIC_KEY', 'comment_recaptcha_public'); ?>

					<div class="col-md-7">
						<input type="text" class="form-control" name="comment_recaptcha_public" id="comment_recaptcha_public" value="<?php echo $this->config->get('comment_recaptcha_public');?>" size="60" />
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('comment_captcha_type') != 'recaptcha' ? 'hidden' : '';?>" data-captcha="recaptcha">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_PRIVATE_KEY', 'comment_recaptcha_private'); ?>

					<div class="col-md-7">
						<input type="text" class="form-control" name="comment_recaptcha_private" id="comment_recaptcha_private" value="<?php echo $this->config->get('comment_recaptcha_private');?>" size="60" />
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('comment_captcha_type') != 'recaptcha' ? 'hidden' : '';?>" data-captcha="recaptcha">
					<?php echo $this->html('form.label', 'COM_EB_RECAPTCHA_ENABL_INVISIBLE', 'comment_recaptcha_invisible'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'comment_recaptcha_invisible', $this->config->get('comment_recaptcha_invisible'));?>
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('comment_captcha_type') != 'recaptcha' ? 'hidden' : '';?>" data-captcha="recaptcha">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME', 'comment_recaptcha_theme'); ?>

					<div class="col-md-7">
						<select name="comment_recaptcha_theme" id="comment_recaptcha_theme" class="form-control">
							<option value="light"<?php echo $this->config->get('comment_recaptcha_theme') == 'light' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME_LIGHT');?></option>
							<option value="dark"<?php echo $this->config->get('comment_recaptcha_theme') == 'dark' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_THEME_DARK');?></option>
						</select>
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('comment_captcha_type') != 'recaptcha' ? 'hidden' : '';?>" data-captcha="recaptcha">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_RECAPTCHA_LANGUAGE', 'comment_recaptcha_lang'); ?>

					<div class="col-md-7">
						<select name="comment_recaptcha_lang" id="comment_recaptcha_lang" class="form-control">
							<?php $languages = EB::captcha()->getRecaptchaLanguages(); ?>
							<?php foreach ($languages as $language) { ?>
								<option value="<?php echo $language->value; ?>"<?php echo $this->config->get('comment_recaptcha_lang') == $language->value ? ' selected="selected"' : ''; ?>><?php echo $language->language;?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="alert alert-warning mt-20 <?php echo $this->config->get('comment_captcha_type') != 'recaptcha' ? 'hidden' : '';?>" data-captcha="recaptcha">
					<?php echo JText::_('COM_EB_SETTINGS_COMMENTS_RECAPTCHA_NOTE');?>
				</div>				
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_INTEGRATIONS_TITLE', 'COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_INTEGRATIONS_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_akismet', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AKISMET'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_AKISMET_API_KEY', 'comment_akismet_key'); ?>

					<div class="col-md-7">
						<input type="text" class="form-control" name="comment_akismet_key" id="comment_akismet_key" value="<?php echo $this->config->get('comment_akismet_key');?>" size="60" />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>