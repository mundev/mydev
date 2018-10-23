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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_BLOGGERS_FORM_AUTHOR_DETAILS', 'COM_EASYBLOG_BLOGGERS_FORM_AUTHOR_DETAILS_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_NAME', 'name'); ?>
					<div class="col-md-7">
						<?php echo $this->html('form.text', 'name', $this->html('string.escape', $user->name), 'name'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_USERNAME', 'username'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'username', $this->html('string.escape', $user->username), 'username'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_EMAIL', 'email'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'email', $this->html('string.escape', $user->email), 'email'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_NEW_PASSWORD', 'password'); ?>

					<div class="col-md-7">
						<input id="password" name="password" class="form-control" type="password" value="<?php echo isset( $this->post['password'] ) ?  $this->post['password'] : '' ;?>" />
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_VERIFY_PASSWORD', 'password2'); ?>

					<div class="col-md-7">
						<input id="password2" name="password2" class="form-control" type="password" value="" />
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_USER_GROUP', 'gid'); ?>

					<div class="col-md-7">
						<?php echo $this->html('tree.groups', 'gid', $user->groups); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_BLOCK_USER', 'block'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'block', $user->block); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_RECEIVE_SYSTEM_EMAILS', 'sendEmail'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'sendEmail', $user->get('sendEmail')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_BLOGGERS_FORM_BASIC_SETTINGS', 'COM_EASYBLOG_BLOGGERS_FORM_BASIC_SETTINGS_INFO'); ?>

			<div class="panel-body">
				<?php foreach ($form->getFieldset('settings') as $field) {
					if ($field->type == 'Plugins' && $field->id == 'jform_params_editor') {
						continue;
					}
				?>
				<div class="form-group">
					<label class="col-md-5">
						<?php echo $field->label; ?>
					</label>

					<div class="col-md-7">
						<?php echo $field->input;?>
					</div>
				</div>
				<?php }?>
			</div>
		</div>
	</div>
</div>
