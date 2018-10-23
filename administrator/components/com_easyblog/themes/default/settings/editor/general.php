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
<div class="row form-horizontal">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMPOSER', 'COM_EASYBLOG_SETTINGS_COMPOSER_INFO'); ?>

			<div class="panel-body">

				<div class="form-group" data-composer-editors>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_SELECT_DEFAULT_EDITOR', 'layout_editor'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.editors', 'layout_editor', $this->config->get('layout_editor'), true); ?>
						<div class="small mt-10">
							<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SELECT_DEFAULT_EDITOR_NOTE');?>
						</div>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'layout_composer_permalink', 'COM_EASYBLOG_SETTINGS_LAYOUT_COMPOSER_ALLOW_EDITING_PERMALINK'); ?>

				<?php echo $this->html('settings.toggle', 'layout_composer_fields', 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_FIELDS'); ?>

				<?php echo $this->html('settings.toggle', 'layout_dashboardseo', 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_SEO'); ?>

				<?php echo $this->html('settings.toggle', 'main_copyrights', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_COPYRIGHTS'); ?>

				<?php echo $this->html('settings.toggle', 'composer_templates', 'COM_EASYBLOG_SETTINGS_COMPOSER_ENABLE_TEMPLATES'); ?>

				<?php echo $this->html('settings.toggle', 'layout_composer_tags', 'COM_EASYBLOG_SETTINGS_LAYOUT_COMPOSER_ENABLE_TAGS'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_LAYOUT_DASHBOARD_MAX_TAGS_ALLOWED', 'max_tags_allowed'); ?>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="max_tags_allowed" id="max_tags_allowed" class="form-control text-center" value="<?php echo $this->config->get('max_tags_allowed', '' );?>" />
									<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_TAGS');?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMPOER_MAX_TAGS_SUGGESTION', 'composer_max_tag_suggest'); ?>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="composer_max_tag_suggest" id="composer_max_tag_suggest" class="form-control text-center" value="<?php echo $this->config->get('composer_max_tag_suggest', '' );?>" />
									<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMPOSER_SUGGESTED_TAGS');?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'layout_composer_customcss', 'COM_EASYBLOG_SETTINGS_COMPOSER_CUSTOM_CSS'); ?>
				
				<?php echo $this->html('settings.toggle', 'layout_composer_author_alias', 'COM_EASYBLOG_SETTINGS_LAYOUT_COMPOSER_ENABLE_AUTHOR_ALIAS'); ?>

				<?php echo $this->html('settings.toggle', 'main_password_protect', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_PASSWORD_PROTECTION'); ?>

				<?php echo $this->html('settings.toggle', 'main_composer_exit_alert', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_ALERT_DURING_EXIT'); ?>

				<?php echo $this->html('settings.toggle', 'layout_composer_history', 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ENABLE_REVISIONS'); ?>

				<?php $hiddenClass = $this->config->get('layout_composer_history') ? '' : 'hidden'; ?>
				<div class="form-group <?php echo $hiddenClass; ?>" data-revision-limit>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_REVISIONS_LIMIT', 'layout_composer_history_limit'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'layout_composer_history_limit', $this->config->get('layout_composer_history_limit'), 'layout_composer_history_limit');?>
					</div>
				</div>

				<div class="form-group <?php echo $hiddenClass; ?>" data-revision-limit-max>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_REVISIONS_MAX', 'layout_composer_history_limit_max'); ?>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="layout_composer_history_limit_max" id="layout_composer_history_limit_max" class="form-control text-center" value="<?php echo $this->config->get('layout_composer_history_limit_max', '5' );?>" />
									<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_REVISIONS');?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_OPTIONS', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_OPTIONS_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'layout_composer_frontpage', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_OPTIONS_FRONTPAGE'); ?>

				<?php echo $this->html('settings.toggle', 'layout_composer_language', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_OPTIONS_LANGUAGE_SELECTION'); ?>

				<?php echo $this->html('settings.toggle', 'layout_composer_creationdate', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_OPTIONS_CREATION_DATE'); ?>

				<?php echo $this->html('settings.toggle', 'layout_composer_publishingdate', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_OPTIONS_PUBLISHING_DATE'); ?>

				<?php echo $this->html('settings.toggle', 'layout_composer_unpublishdate', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_OPTIONS_UNPUBLISHING_DATE'); ?>

				<?php echo $this->html('settings.toggle', 'layout_composer_autopostdate', 'COM_EB_SETTINGS_COMPOSER_POST_OPTIONS_AUTOPOSTING_DATE'); ?>
				
				<?php echo $this->html('settings.toggle', 'main_sendemailnotifications', 'COM_EB_DEFAULT_NOTIFY_SUBSCRIBERS'); ?>


				<?php echo $this->html('settings.toggle', 'layout_composer_privacy', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_OPTIONS_PRIVACY_SECTION', ''); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_BLOG_PRIVACY', 'main_blogprivacy'); ?>

					<div class="col-md-7">
						<?php
							$nameFormat = EB::privacy()->getOptions();
							$showdet = JHTML::_('select.genericlist', $nameFormat, 'main_blogprivacy', 'class="form-control"', 'value', 'text', $this->config->get('main_blogprivacy'));
							echo $showdet;
						?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'layout_composer_comment', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_OPTIONS_COMMENT', '', 'data-comment-option'); ?>

				<div class="form-group <?php echo $this->config->get('layout_composer_comment') ? '' : 'hide';?>" data-comment-option-default>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_DEFAULT_ALLOW_COMMENT', 'main_defaultallowcomment'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'main_defaultallowcomment', $this->config->get('main_defaultallowcomment')); ?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'layout_composer_feature', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_OPTIONS_FEATURE_SECTION'); ?>

				<?php echo $this->html('settings.toggle', 'layout_composer_category_language', 'COM_EASYBLOG_SETTINGS_COMPOSER_POST_CATEGORY_RESPECT_LANGUAGE'); ?>
			</div>
		</div>
	</div>
</div>
