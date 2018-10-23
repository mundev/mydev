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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SOCIAL_BUTTONS', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SOCIAL_BUTTONS_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIAL_BUTTONS_TYPE', 'social_button_type'); ?>

					<div class="col-md-7">
						<select name="social_button_type" id="social_button_type" class="form-control" data-social-button-type>
							<option value="disabled"<?php echo $this->config->get('social_button_type') == 'disabled' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TYPE_NONE');?></option>
							<option value="internal"<?php echo $this->config->get('social_button_type') == 'internal' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TYPE_SIMPLE');?></option>
							<option value="external"<?php echo $this->config->get('social_button_type') == 'external' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TYPE_RESPECT_SOCIAL_SITE');?></option>
							<option value="addthis"<?php echo $this->config->get('social_button_type') == 'addthis' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TYPE_ADDTHIS');?></option>
							<option value="sharethis"<?php echo $this->config->get('social_button_type') == 'sharethis' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TYPE_SHARETHIS');?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel <?php echo $this->config->get('social_button_type') == 'internal' ? '' : 'hidden';?>" data-social-group="internal">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_INTERNAL_BUTTONS'); ?>

			<div class="panel-body">

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIAL_BUTTONS_BUTTON_SIZE', 'social_button_internal_size'); ?>

					<div class="col-md-7">
						<select name="social_button_internal_size" id="social_button_internal_size" class="form-control">
							<option value="large"<?php echo $this->config->get('social_button_internal_size') == 'large' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SOCIAL_BUTTONS_SIZE_LARGE');?></option>
							<option value="small"<?php echo $this->config->get('social_button_internal_size') == 'small' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SOCIAL_BUTTONS_SIZE_SMALL');?></option>
						</select>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'social_button_facebook', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'social_button_twitter', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'social_button_linkedin', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_LINKEDIN_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'social_button_google', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_ENABLE'); ?>
				<?php echo $this->html('settings.toggle', 'social_button_pinterest', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_PINIT_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'social_button_pocket', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_POCKET_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'social_button_reddit', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_REDDIT_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'social_button_vk', 'COM_EASYBLOG_SETTINGS_VK_ENABLE'); ?>
				<?php echo $this->html('settings.toggle', 'social_button_xing', 'COM_EASYBLOG_SETTINGS_XING_ENABLE'); ?>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_button_type') == 'external' ? '' : 'hidden';?>" data-social-group="external">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_BUTTONS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIAL_BUTTONS_BUTTON_SIZE', 'social_button_size'); ?>

					<div class="col-md-7">
						<select name="social_button_size" class="form-control">
							<option value="large"<?php echo $this->config->get('social_button_size') == 'large' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SOCIAL_BUTTONS_SIZE_LARGE');?></option>
							<option value="small"<?php echo $this->config->get('social_button_size') == 'small' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SOCIAL_BUTTONS_SIZE_SMALL');?></option>
						</select>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_facebook_like', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES'); ?>
				<?php echo $this->html('settings.toggle', 'main_twitter_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'main_linkedin_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_LINKEDIN_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'main_googleone', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_ENABLE'); ?>
				<?php echo $this->html('settings.toggle', 'main_pinit_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_PINIT_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'main_pocket_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_POCKET_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'main_reddit_button', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_USE_REDDIT_BUTTON'); ?>
				<?php echo $this->html('settings.toggle', 'main_vk', 'COM_EASYBLOG_SETTINGS_VK_ENABLE'); ?>
				<?php echo $this->html('settings.text', 'main_vk_api', 'COM_EASYBLOG_SETTINGS_VK_API_ID'); ?>
				<?php echo $this->html('settings.toggle', 'main_xing_button', 'COM_EASYBLOG_SETTINGS_XING_ENABLE'); ?>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_button_type') == 'sharethis' ? '' : 'hidden';?>" data-social-group="sharethis">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_TITLE'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_PROPERTY_ID', 'social_sharethis_property'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'social_sharethis_property', $this->config->get('social_sharethis_property'), 'social_sharethis_property'); ?>

						<div class="notice full-width"><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_PUBLISHERS_INSTRUCTIONS', 'http://easyblog.io/administrators/configuration/sharethis_configuration');?></div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_button_type') == 'addthis' ? '' : 'hidden';?>" data-social-group="addthis">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_ADDTHIS_CODE', 'social_addthis_customcode'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'social_addthis_customcode', $this->config->get('social_addthis_customcode'), 'social_addthis_customcode'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
