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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SYSTEM_SETTINGS', 'COM_EASYBLOG_SETTINGS_SYSTEM_SETTINGS_INFO'); ?>

			<div class="panel-body">

				<?php echo $this->html('settings.toggle', 'show_outdated_message', 'COM_EASYBLOG_SETTINGS_SYSTEM_SHOW_SOFTWARE_UPDATE_NOTIFICATIONS'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SYSTEM_AJAX_URL', 'ajax_use_index'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'ajax_use_index', $this->config->get('ajax_use_index')); ?>
						
						<div class="mt-10">
							<p class="text-muted"><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_SYSTEM_AJAX_URL_INFO', rtrim(JURI::root(), '/'));?></p>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_CDN_URL', 'cdn_url'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'cdn_url', $this->config->get('cdn_url')); ?>
					</div>
				</div>
				
				<?php echo $this->html('settings.toggle', 'easyblog_jquery', 'COM_EASYBLOG_SETTINGS_SYSTEM_LOAD_EASYBLOG_JQUERY'); ?>

				<?php echo $this->html('settings.toggle', 'system_error_redirection', 'COM_EB_SETTINGS_SYSTEM_ENABLE_ERROR_REDIRECTION'); ?>
				
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHAN_TITLE', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHAN_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ORPHANED_ITEMS_OWNER', 'main_orphanitem_ownership'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.author', 'main_orphanitem_ownership', $this->config->get('main_orphanitem_ownership', EB::getDefaultSAIds())); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>