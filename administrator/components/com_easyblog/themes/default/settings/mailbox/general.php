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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING'); ?>

			<div class="panel-body">
				<?php echo $this->html('panel.info', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ROMOTE_PUBLISHING_MAILBOX_INSTRUCTION', 'https://stackideas.com/docs/easyblog/administrators/cronjobs', 'COM_EASYBLOG_SETTINGS_HELP_CRON'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST', 'test-button'); ?>

					<div class="col-md-7">
						<button type="button" class="btn btn-default btn-sm" data-test-mailbox><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_TEST_BUTTON');?></button>
						<span data-mailbox-test-result></span>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING'); ?>

				<?php echo $this->html('settings.text', 'main_remotepublishing_mailbox_prefix', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PREFIX'); ?>

				<?php echo $this->html('settings.text', 'main_remotepublishing_mailbox_run_interval', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_RUN_INTERVAL', '', array('postfix' => 'COM_EASYBLOG_MINUTES', 'size' => 5), '', 'text-center'); ?>

				<?php echo $this->html('settings.text', 'main_remotepublishing_mailbox_fetch_limit', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FETCH_LIMIT', '', array('postfix' => 'COM_EASYBLOG_EMAILS', 'size' => 5), '', 'text-center'); ?>
			</div>
		</div>

	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_SERVER_SETTINGS'); ?>

			<div class="panel-body">
				<?php echo $this->html('panel.info', 'COM_EASYBLOG_SETTINGS_MAILBOX_PUBLISHING_SETUP_INFORMATION', 'https://stackideas.com/docs/easyblog/administrators/remote-publishing/email-publishing', 'COM_EASYBLOG_SETTING_UP_MAILBOX_PUBLISHING'); ?>
				
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PROVIDER', 'main_remotepublishing_mailbox_provider'); ?>

					<div class="col-md-7">
						<select name="main_remotepublishing_mailbox_provider" id="main_remotepublishing_mailbox_provider" class="form-control" data-mail-provider>
							<option value=""><?php echo JText::_('COM_EASYBLOG_MAILBOX_PROVIDER_SELECT_PROVIDER');?></option>
							<option value="gmail"<?php echo ($this->config->get('main_remotepublishing_mailbox_provider') == 'gmail') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_MAILBOX_PROVIDER_GMAIL');?></option>
							<option value="hotmail"<?php echo ($this->config->get('main_remotepublishing_mailbox_provider') == 'hotmail') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_MAILBOX_PROVIDER_HOTMAIL');?></option>
							<option value="others"<?php echo ($this->config->get('main_remotepublishing_mailbox_provider') == 'others') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_MAILBOX_PROVIDER_OTHERS');?></option>
						</select>
					</div>
				</div>

				<?php echo $this->html('settings.text', 'main_remotepublishing_mailbox_username', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_USERNAME', '', array('attributes' => 'data-mailbox-username')); ?>

				<?php echo $this->html('settings.password', 'main_remotepublishing_mailbox_password', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PASSWORD', '', array('attributes' => 'data-mailbox-password')); ?>

				<?php echo $this->html('settings.text', 'main_remotepublishing_mailbox_remotesystemname', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SYSTEM_NAME', '', array('attributes' => 'data-mailbox-address')); ?>

				<?php echo $this->html('settings.text', 'main_remotepublishing_mailbox_port', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PORT', '', array('attributes' => 'data-mailbox-port', 'size' => ''), '', ''); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE', 'main_remotepublishing_mailbox_service'); ?>

					<div class="col-md-7">
						<?php
							$services = array();
							$services[] = JHTML::_('select.option', 'imap', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE_IMAP' ) );
							$services[] = JHTML::_('select.option', 'pop3', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SERVICE_POP3' ) );
							echo JHTML::_('select.genericlist', $services, 'main_remotepublishing_mailbox_service', 'class="form-control" data-mailbox-type', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_service') );
						?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_ssl', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SSL', '', 'data-mailbox-ssl'); ?>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_validate_cert', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_VALIDATE_CERT', '', 'data-mailbox-validate-ssl'); ?>

				<?php echo $this->html('settings.text', 'main_remotepublishing_mailbox_mailboxname', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAILBOX_NAME', '', array('attributes' => 'data-mailbox-name', 'size' => ''), '', ''); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FROM_WHITE_LIST', 'main_remotepublishing_mailbox_from_whitelist'); ?>

					<div class="col-md-7">
						<textarea class="form-control" id="main_remotepublishing_mailbox_from_whitelist" name="main_remotepublishing_mailbox_from_whitelist" data-mailbox-whitelist><?php echo $this->config->get('main_remotepublishing_mailbox_from_whitelist');?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
