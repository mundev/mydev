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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_MAILBOX_PUBLISHING_OPTIONS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT', 'main_remotepublishing_mailbox_format'); ?>

					<div class="col-md-7">
						<?php
							$contentType = array();
							$contentType[] = JHTML::_('select.option', 'html', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT_HTML_OPTION' ) );
							$contentType[] = JHTML::_('select.option', 'plain', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FORMAT_PLAINTEXT_OPTION' ) );

							$showdet = JHTML::_('select.genericlist', $contentType, 'main_remotepublishing_mailbox_format', 'class="form-control"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_format' ) );
							echo $showdet;
						?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_syncuser', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_MAP_USERS_EMAIL'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_SELECT_USER', 'main_remotepublishing_mailbox_userid'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.author', 'main_remotepublishing_mailbox_userid', $this->config->get('main_remotepublishing_mailbox_userid')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTYPE', 'main_remotepublishing_mailbox_type'); ?>
					<div class="col-md-7">
						<?php
							$contentType = array();
							$contentType[] = JHTML::_('select.option', 'intro', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTTYPE_INTRO' ) );
							$contentType[] = JHTML::_('select.option', 'content', JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_INSERTTYPE_CONTENT' ) );

							$showdet = JHTML::_('select.genericlist', $contentType, 'main_remotepublishing_mailbox_type', 'class="form-control"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_type' ) );
							echo $showdet;
						?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_CATEGORY', 'main_remotepublishing_mailbox_categoryid'); ?>
					
					<div class="col-md-7">
						<?php echo $this->html('form.browseCategory', 'main_remotepublishing_mailbox_categoryid', $this->config->get('main_remotepublishing_mailbox_categoryid')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_STATE', 'main_remotepublishing_mailbox_publish'); ?>

					<div class="col-md-7">
						<?php
							$publishFormat = array();
							$publishFormat[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_UNPUBLISHED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_PUBLISHED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_SCHEDULED_OPTION' ) );
							$publishFormat[] = JHTML::_('select.option', '3', JText::_( 'COM_EASYBLOG_DRAFT_OPTION' ) );

							$showdet = JHTML::_('select.genericlist', $publishFormat, 'main_remotepublishing_mailbox_publish', 'class="form-control"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_publish' , '1' ) );
							echo $showdet;
						?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_PUBLISH_PRIVACY', 'main_remotepublishing_mailbox_privacy'); ?>
					<div class="col-md-7">
						<?php
							$privacies = array();
							$privacies[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_PRIVACY_ALL_OPTION' ) );
							$privacies[] = JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_PRIVACY_REGISTERED_OPTION' ) );

							$showdet = JHTML::_('select.genericlist', $privacies, 'main_remotepublishing_mailbox_privacy', 'class="form-control"', 'value', 'text', $this->config->get('main_remotepublishing_mailbox_privacy' , '0' ) );
							echo $showdet;
						?>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_frontpage', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_FRONTPAGE'); ?>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_image_attachment', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_ENABLE_ATTACHMENT'); ?>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_mailbox_blogimage', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_MAILBOX_ENABLE_BLOGIMAGE'); ?>

				<?php echo $this->html('settings.toggle', 'main_remotepublishing_autoposting', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REMOTE_PUBLISHING_AUTOPOST'); ?>
			</div>
		</div>
	</div>
</div>
