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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_GENERAL_TITLE', 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_GENERAL_INFO'); ?>

			<div class="panel-body">

				<?php echo $this->html('settings.toggle', 'layout_headers', 'COM_EB_DISPLAY_BLOG_HEADERS', '', 'data-blog-headers'); ?>

				<div class="form-group <?php echo !$this->config->get('layout_headers') ? 'hide' : '';?>" data-blog-headers-options>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_TITLE', 'main_title'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'main_title', $this->html('string.escape', $this->config->get('main_title'))); ?>
					</div>
				</div>

				<div class="form-group <?php echo !$this->config->get('layout_headers') ? 'hide' : '';?>" data-blog-headers-options>
					<?php echo $this->html('form.label', 'COM_EB_DISPLAY_HEADER_DESCRIPTION', 'layout_header_description'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'layout_header_description', $this->config->get('layout_header_description'), '', 'data-blog-description'); ?>
					</div>
				</div>

				<div class="form-group <?php echo !$this->config->get('layout_headers') ? 'hide' : '';?> <?php echo !$this->config->get('layout_header_description') ? 'hide' : '';?>" data-blog-headers-options data-blog-description-options>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_BLOG_DESCRIPTION', 'main_description'); ?>

					<div class="col-md-7">
						<textarea name="main_description" id="main_description" rows="5" class="form-control" cols="35"><?php echo $this->config->get('main_description');?></textarea>
					</div>
				</div>

				<div class="form-group <?php echo !$this->config->get('layout_headers') ? 'hide' : '';?>" data-blog-headers-options>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_HEADER_RESPECT_AUTHOR', 'layout_headers_respect_author'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'layout_headers_respect_author', $this->config->get('layout_headers_respect_author')); ?>
					</div>
				</div>

				<div class="form-group <?php echo !$this->config->get('layout_headers') ? 'hide' : '';?>" data-blog-headers-options>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_HEADER_RESPECT_TEAMBLOG', 'layout_headers_respect_teamblog'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'layout_headers_respect_teamblog', $this->config->get('layout_headers_respect_teamblog')); ?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_login_read', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REQUIRE_LOGIN_TO_READ_FULL'); ?>

				<?php echo $this->html('settings.toggle', 'main_multi_language', 'COM_EASYBLOG_SETTINGS_GENERAL_ENABLE_MULTI_LANGUAGE_POSTS'); ?>

				<?php echo $this->html('settings.toggle', 'main_category_privacy', 'COM_EB_ENABLE_CATEGORY_PRIVACY'); ?>
				
				<?php echo $this->html('settings.toggle', 'main_microblog', 'COM_EASYBLOG_SETTINGS_MICROBLOG_ENABLE_MICROBLOG'); ?>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_xmlrpc', 'COM_EASYBLOG_SETTINGS_REMOTE_PUBLISHING_ENABLE'); ?>

				<?php echo $this->html('settings.toggle', 'main_favourite_post', 'COM_EB_SETTINGS_FAVOURITE_POST_ENABLE'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_CALENDAR_START_OF_WEEK', 'main_start_of_week'); ?>

					<div class="col-md-7">
						<select name="main_start_of_week" id="main_start_of_week" class="form-control">
							<option value="monday"<?php echo $this->config->get('main_start_of_week') == 'monday' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_CALENDAR_MONDAY'); ?></option>
							<option value="sunday"<?php echo $this->config->get('main_start_of_week') == 'sunday' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_CALENDAR_SUNDAY'); ?></option>
						</select>
					</div>
				</div>

			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EB_GDPR'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'gdpr_enabled', 'COM_EB_ENABLE_GDPR'); ?>

				<?php echo $this->html('settings.smalltext', 'gdpr_archive_expiry', 'COM_EB_GDPR_ARCHIVE_EXPIRY', '', 'COM_EB_GDPR_ARCHIVE_EXPIRY_DAYS'); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EB_SETTINGS_READING_PROGRESS'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_show_reading_progress', 'COM_EB_SETTINGS_SHOW_READING_PROGRESS'); ?>
				<?php echo $this->html('settings.smalltext', 'main_reading_progress_offset', 'COM_EB_SETTINGS_READING_PROGRESS_OFFSET', '', 'COM_EASYBLOG_ELEMENTS_PX'); ?>

				<div class="form-group">

					<?php echo $this->html('form.label', 'COM_EB_READING_PROGRESS_BACKGROUND', 'main_reading_background'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'main_reading_background', $this->config->get('main_reading_background'), '#f5f5f5'); ?>
					</div>
				</div>

				<div class="form-group">

					<?php echo $this->html('form.label', 'COM_EB_READING_PROGRESS_FOREGROUND', 'main_reading_foreground'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'main_reading_foreground', $this->config->get('main_reading_foreground'), '#57B4FC'); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_REACTIONS_GENERAL'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'reactions_enabled', 'COM_EASYBLOG_SETTINGS_REACTIONS_ENABLE_REACTIONS');?>

				<?php echo $this->html('settings.toggle', 'reactions_guests', 'COM_EASYBLOG_SETTINGS_REACTIONS_GUEST');?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_RATINGS_TITLE', 'COM_EASYBLOG_SETTINGS_WORKFLOW_RATINGS_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_ratings', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RATINGS'); ?>

				<?php echo $this->html('settings.toggle', 'main_ratings_frontpage_locked', 'COM_EASYBLOG_SETTINGS_WORKFLOW_LOCKED_ON_FRONTPAGE'); ?>

				<?php echo $this->html('settings.toggle', 'main_ratings_guests', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_RATING'); ?>

				<?php echo $this->html('settings.toggle', 'main_ratings_allow_author', 'COM_EASYBLOG_SETTINGS_WORKFLOW_RATINGS_ALLOW_AUTHOR'); ?>

				<?php echo $this->html('settings.toggle', 'main_ratings_display_raters', 'COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_PEOPLE_RATED'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_TEAMBLOGS'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_includeteamblogpost', 'COM_EASYBLOG_SETTINGS_WORKFLOW_TEAMBLOG_INCLUDE_TEAMBLOG_POSTS'); ?>

				<?php echo $this->html('settings.toggle', 'main_includeteamblogdescription', 'COM_EASYBLOG_SETTINGS_WORKFLOW_TEAMBLOG_INCLUDE_TEAMBLOG_DESCRIPTIONS'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REPORTING', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REPORTING_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_reporting', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_REPORTING'); ?>

				<?php echo $this->html('settings.toggle', 'main_reporting_guests', 'COM_EASYBLOG_REPORTS_ALLOW_GUEST_TO_REPORT'); ?>

				<?php echo $this->html('settings.smalltext', 'main_reporting_maxip', 'COM_EASYBLOG_REPORTS_MAX_REPORTS_PER_IP', '', 'COM_EASYBLOG_REPORTS_REPORTS'); ?>
			</div>
		</div>
	</div>
</div>
