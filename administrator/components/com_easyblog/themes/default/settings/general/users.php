<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_USERS', 'COM_EASYBLOG_SETTINGS_WORKFLOW_USERS_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_joomlauserparams', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_JOOMLA_USER_PARAMETERS'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOGIN_PROVIDER', 'main_login_provider'); ?>

					<div class="col-md-7">
						<select class="form-control" name="main_login_provider" id="main_login_provider">
							<option value="easyblog"<?php echo $this->config->get( 'main_login_provider' ) == 'easyblog' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG' );?></option>
							<option value="easysocial"<?php echo $this->config->get( 'main_login_provider' ) == 'easysocial' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_EASYSOCIAL' );?></option>
							<option value="joomla"<?php echo $this->config->get( 'main_login_provider' ) == 'joomla' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_JOOMLA' );?></option>
							<option value="cb"<?php echo $this->config->get( 'main_login_provider' ) == 'cb' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_CB' );?></option>
							<option value="jomsocial"<?php echo $this->config->get( 'main_login_provider' ) == 'jomsocial' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_JOMSOCIAL' );?></option>
						</select>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_autofeatured', 'COM_EASYBLOG_SETTINGS_WORKFLOW_AUTOMATIC_FEATURE_BLOG_POST'); ?>

				<?php echo $this->html('settings.toggle', 'main_bloggerlistingoption', 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_LISTINGS_OPTION'); ?>

				<?php echo $this->html('settings.text', 'layout_exclude_bloggers', 'COM_EASYBLOG_SETTINGS_LAYOUT_EXCLUDE_USERS_FROM_BLOGGER_LISTINGS'); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
	</div>
</div>