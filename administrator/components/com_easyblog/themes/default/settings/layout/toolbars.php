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
		<div class="panel" data-layout-toolbar-items>

			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_TOOLBAR_FRONTEND', 'COM_EASYBLOG_SETTINGS_TOOLBAR_FRONTEND_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'layout_toolbar', 'COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOG_TOOLBAR'); ?>

				<?php echo $this->html('settings.toggle', 'layout_latest', 'COM_EB_DISPLAY_HOME_ICON'); ?>

				<?php echo $this->html('settings.toggle', 'layout_categories', 'COM_EB_DISPLAY_CATEGORIES'); ?>

				<?php echo $this->html('settings.toggle', 'layout_tags', 'COM_EB_DISPLAY_TAGS'); ?>

				<?php echo $this->html('settings.toggle', 'layout_bloggers', 'COM_EB_DISPLAY_AUTHORS'); ?>

				<?php echo $this->html('settings.toggle', 'layout_teamblog', 'COM_EB_DISPLAY_TEAMS'); ?>

				<?php echo $this->html('settings.toggle', 'layout_archives', 'COM_EB_DISPLAY_ARCHIVES'); ?>

				<?php echo $this->html('settings.toggle', 'layout_calendar', 'COM_EB_DISPLAY_CALENDAR'); ?>

				<?php echo $this->html('settings.toggle', 'layout_search', 'COM_EB_DISPLAY_SEARCH'); ?>

				<?php echo $this->html('settings.toggle', 'layout_login', 'COM_EB_DISPLAY_LOGIN'); ?>

			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EB_TOOLBAR_STYLES'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EB_TOOLBAR_COLOR', 'layout_toolbarcolor'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'layout_toolbarcolor', $this->config->get('layout_toolbarcolor'), '#333333'); ?>
					</div>
				</div>

				<div class="form-group">

					<?php echo $this->html('form.label', 'COM_EB_TOOLBAR_ACTIVE_COLOR', 'layout_toolbaractivecolor'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'layout_toolbaractivecolor', $this->config->get('layout_toolbaractivecolor'), '#5C5C5C'); ?>
					</div>
				</div>

				<div class="form-group">

					<?php echo $this->html('form.label', 'COM_EB_TOOLBAR_TEXT_COLOR', 'layout_toolbartextcolor'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'layout_toolbartextcolor', $this->config->get('layout_toolbartextcolor'), '#FFFFFF'); ?>
					</div>
				</div>

				<div class="form-group">

					<?php echo $this->html('form.label', 'COM_EB_TOOLBAR_BORDER_COLOR', 'layout_toolbarbordercolor'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'layout_toolbarbordercolor', $this->config->get('layout_toolbarbordercolor'), '#333333'); ?>
					</div>
				</div>
				
				<div class="form-group">

					<?php echo $this->html('form.label', 'COM_EB_TOOLBAR_COMPOSER_BACKGROUND_COLOR', 'layout_toolbarcomposerbackgroundcolor'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'layout_toolbarcomposerbackgroundcolor', $this->config->get('layout_toolbarcomposerbackgroundcolor'), '#428bca'); ?>
					</div>
				</div>							
			</div>
		</div>
	</div>
</div>
