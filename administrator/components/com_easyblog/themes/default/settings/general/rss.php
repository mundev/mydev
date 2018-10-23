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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_RSS', 'COM_EASYBLOG_SETTINGS_RSS_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_rss', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RSS'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT', 'main_rss_content'); ?>

					<div class="col-md-7">
						<select name="main_rss_content" id="main_rss_content" class="form-control">
							<option value="introtext"<?php echo $this->config->get( 'main_rss_content' ) == 'introtext' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_INTROTEXT' ); ?></option>
							<option value="fulltext"<?php echo $this->config->get( 'main_rss_content' ) == 'fulltext' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_RSS_CONTENT_FULLTEXT' ); ?></option>
						</select>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_FEEDBURNER', 'COM_EASYBLOG_SETTINGS_FEEDBURNER_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_FEEDBURNER_RSS_URL', 'rss_url'); ?>

					<div class="col-md-7">
						<div class="form-control-static"><?php echo JURI::root();?>index.php?option=com_easyblog&view=latest&format=feed&type=rss</div>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_feedburner', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ENABLE_FEEDBURNER_INTEGRATIONS'); ?>

				<?php echo $this->html('settings.toggle', 'main_feedburnerblogger', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ALLOW_BLOGGERS_TO_USE_FEEDBURNER'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_FEEDBURNER_URL', 'main_feedburner_url'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'main_feedburner_url', $this->config->get('main_feedburner_url')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>