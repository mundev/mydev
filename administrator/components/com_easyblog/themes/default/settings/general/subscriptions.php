<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_TITLE', 'COM_EASYBLOG_SETTINGS_WORKFLOW_SUBSCRIPTIONS_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_sitesubscription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_SITE_SUBSCRIPTIONS'); ?>

				<?php echo $this->html('settings.toggle', 'main_subscription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOG_SUBSCRIPTIONS'); ?>

				<?php echo $this->html('settings.toggle', 'main_bloggersubscription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_BLOGGER_SUBSCRIPTIONS'); ?>

				<?php echo $this->html('settings.toggle', 'main_categorysubscription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_CATEGORY_SUBSCRIPTIONS'); ?>

				<?php echo $this->html('settings.toggle', 'main_teamsubscription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_TEAM_SUBSCRIPTIONS'); ?>

				<?php echo $this->html('settings.toggle', 'main_allowguestsubscribe', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_TO_SUBSCRIBE'); ?>

				<?php echo $this->html('settings.toggle', 'main_registeronsubscribe', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_REGISTRATION_DURING_SUBSCRIBE'); ?>

				<?php echo $this->html('settings.toggle', 'main_subscription_confirmation', 'COM_EASYBLOG_SETTINGS_NOTIFY_USER_SUBSCRIPTIONS_CONFIRMATION'); ?>

				<?php echo $this->html('settings.toggle', 'main_subscription_admin_notification', 'COM_EASYBLOG_SETTINGS_NOTIFY_ADMIN_NEW_SUBSCRIPTIONS'); ?>

				<?php echo $this->html('settings.toggle', 'main_subscription_author_notification', 'COM_EASYBLOG_SETTINGS_NOTIFY_AUTHOR_NEW_SUBSCRIPTIONS'); ?>

				<?php echo $this->html('settings.toggle', 'main_subscription_author_post_notification', 'COM_EASYBLOG_SETTINGS_NOTIFY_AUTHOR_POST_NEW_SUBSCRIPTIONS'); ?>				
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SUBSCRIPTIONS_AGREEMENT', 'COM_EASYBLOG_SETTINGS_SUBSCRIPTIONS_AGREEMENT_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_subscription_agreement', 'COM_EASYBLOG_SETTINGS_SUBSCRIPTIONS_REQUIRE_USER_TO_AGREE'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SUBSCRIPTIONS_AGREEMENT_MESSAGE', 'main_subscription_agreement_message'); ?>

					<div class="col-md-7">
						<textarea name="main_subscription_agreement_message" id="main_subscription_agreement_message" class="form-control"><?php echo $this->config->get('main_subscription_agreement_message');?></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>