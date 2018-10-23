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
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_USERS_SETTINGS_GENERAL'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_PERMALINK_FORMAT'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.aliasName', $this->config->get('users.aliasName'), array(
							array('value' => 'username', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_USERNAME'),
							array('value' => 'realname', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_REAL_NAME')
						));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_ACCOUNT_DELETION_WORKFLOW'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.deleteLogic', $this->config->get('users.deleteLogic'), array(
								array('value' => 'delete', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_DELETE_IMMEDIATELY_AND_NOTIFY_ADMIN'),
								array('value' => 'unpublish', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_UNPUBLISH_ACCOUNT_AND_NOTIFY_ADMIN')
						)); ?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'users.appenduserid', 'COM_ES_USERS_APPEND_ID'); ?>
				<?php echo $this->html('settings.toggle', 'friends.autofollow', 'COM_ES_FRIENDS_AUTOFOLLOW'); ?>
				<?php echo $this->html('settings.toggle', 'users.blocking.enabled', 'COM_EASYSOCIAL_USERS_SETTINGS_ALLOW_USER_BLOCKING'); ?>
				<?php echo $this->html('settings.toggle', 'users.avatarWebcam', 'COM_EASYSOCIAL_USERS_SETTINGS_ALLOW_WEBCAM_AVATAR'); ?>
				<?php echo $this->html('settings.toggle', 'users.download.enabled', 'COM_ES_USERS_SETTINGS_ALLOW_USER_DOWNLOAD'); ?>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_USERS_SETTINGS_ALLOW_USER_DOWNLOAD_EXPIRY'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.inputbox', 'users.download.expiry', $this->config->get('users.download.expiry'), '', array('class' => 'input-short text-center'));?>
						<?php echo JText::_('COM_EASYSOCIAL_DAYS'); ?>
					</div>
				</div>

			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_ES_USERS_VERIFIED_ACCOUNTS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_USERS_ENABLE_VERIFIED_ACCOUNTS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.verification.enabled', $this->config->get('users.verification.enabled')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_USERS_ALLOW_VERIFICATION_REQUESTS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.verification.requests', $this->config->get('users.verification.requests')); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_USERS_SETTINGS_PROFILE_COMPLETION'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_CHECK_FOR_PROFILE_COMPLETION'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'user.completeprofile.required', $this->config->get('user.completeprofile.required')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_INCLUDE_OPTIONAL_FIELD'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'user.completeprofile.strict', $this->config->get('user.completeprofile.strict')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_ACTION_ON_INCOMPLETE_PROFILE'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'user.completeprofile.action', $this->config->get('user.completeprofile.action'), array(
							array('value' => 'info', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_SHOW_MESSAGE_ON_SITE_WIDE'),
							array('value' => 'infoprofile', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_SHOW_MESSAGE_ON_PROFILE_PAGE'),
							array('value' => 'redirect', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_REDIRECT_TO_EDIT_PAGE')
						)); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_ES_USERS_SETTINGS_REMINDER'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_REMINDER_FOR_INACTIVE_USERS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.reminder.enabled', $this->config->get('users.reminder.enabled')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DURATION_FOR_INACTIVITY'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.reminder.duration', $this->config->get('users.reminder.duration'), array(
							array('value' => '14', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_2_WEEKS'),
							array('value' => '30', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_1_MONTH'),
							array('value' => '60', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_2_MONTHS'),
							array('value' => '90', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_3_MONTHS'),
							array('value' => '180', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_6_MONTHS')
						)); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_USERS_SETTINGS_USER_INDEXING'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_NAME_INDEXING_FORMAT'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.indexer.name', $this->config->get('users.indexer.name'), array(
							array('value' => 'username', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_USERNAME'),
							array('value' => 'realname', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_REAL_NAME')
						)); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_INDEX_EMAIL'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.indexer.email', $this->config->get('users.indexer.email')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_PRIVACY_VALIDATION'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.indexer.privacy', $this->config->get('users.indexer.privacy')); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_USERS_SETTINGS_USER_PROFILE_SWITCHING'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_USER_PROFILE_GROUP_SWITCH'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.profile.switchgroup', $this->config->get('users.profile.switchgroup')); ?>
					</div>
				</div>

			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_ES_USERS_SETTINGS_USER_PRIVACY'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_USERS_SETTINGS_USER_PRIVACY_FIELD'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.privacy.field', $this->config->get('users.privacy.field')); ?>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
