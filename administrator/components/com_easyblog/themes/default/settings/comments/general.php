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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_TITLE', 'COM_EASYBLOG_SETTINGS_COMMENTS_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_comment', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_COMMENT'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_SORTING', 'comment_sort'); ?>

					<div class="col-md-7">
						<?php
							$listLength = array();
							$listLength[] = JHTML::_('select.option', 'desc', JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SORTING_OPTIONS_DESCENDING'));
							$listLength[] = JHTML::_('select.option', 'asc', JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_SORTING_OPTIONS_ASCENDING'));
							echo JHTML::_('select.genericlist', $listLength, 'comment_sort', 'class="form-control input-box"', 'value', 'text', $this->config->get('comment_sort' , 'desc'));
						?>
					</div>
				</div>                

				<?php echo $this->html('settings.toggle', 'comment_bbcode', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_BBCODE'); ?>

				<?php echo $this->html('settings.toggle', 'comment_likes', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_LIKES'); ?>

				<?php echo $this->html('settings.toggle', 'main_allowguestviewcomment', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_VIEW_COMMENT'); ?>

				<?php echo $this->html('settings.toggle', 'comment_registeroncomment', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_GUEST_REGISTRATION_WHEN_COMMENTING'); ?>

				<?php echo $this->html('settings.toggle', 'comment_autotitle', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTO_TITLE_IN_REPLY'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_THREADED_LEVEL', 'comment_maxthreadedlevel'); ?>

					<div class="col-md-7">
						<input type="text" name="comment_maxthreadedlevel" id="comment_maxthreadedlevel" class="form-control input-mini text-center" value="<?php echo $this->config->get('comment_maxthreadedlevel');?>" />
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'comment_showsubscribe', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_SHOWSUBSCRIBE'); ?>

				<?php echo $this->html('settings.toggle', 'comment_autosubscribe', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_AUTOSUBSCRIBE'); ?>

				<?php echo $this->html('settings.toggle', 'comment_allowlogin', 'COM_EASYBLOG_SETTINGS_COMMENTS_ALLOW_LOGIN_LINK'); ?>
			</div> 
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATION_TITLE', 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATION_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_moderatecomment', 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_NEW_COMMENT'); ?>

				<?php echo $this->html('settings.toggle', 'comment_moderateauthorcomment', 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_BLOG_AUTHORS'); ?>

				<?php echo $this->html('settings.toggle', 'comment_moderateguestcomment', 'COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_GUEST_COMMENTS'); ?>
			</div>
		</div>

	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIREMENTS_TITLE', 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIREMENTS_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_show_title', 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_TITLE'); ?>

				<?php echo $this->html('settings.toggle', 'comment_requiretitle', 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_TITLE'); ?>

				<?php echo $this->html('settings.toggle', 'comment_show_email', 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_EMAIL'); ?>

				<?php echo $this->html('settings.toggle', 'comment_require_email', 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_EMAIL'); ?>

				<?php echo $this->html('settings.toggle', 'comment_show_website', 'COM_EASYBLOG_SETTINGS_COMMENTS_SHOW_WEBSITE'); ?>

				<?php echo $this->html('settings.toggle', 'comment_require_website', 'COM_EASYBLOG_SETTINGS_COMMENTS_REQUIRE_WEBSITE'); ?>

				<?php echo $this->html('settings.toggle', 'comment_tnc', 'COM_EASYBLOG_SETTINGS_COMMENTS_ENABLE_TERMS'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_TERMS_TEXT', 'comment_tnctext'); ?>

					<div class="col-md-7">
						<textarea name="comment_tnctext" id="comment_tnctext" class="form-control" rows="15"><?php echo str_replace('<br />', "\n", $this->config->get('comment_tnctext')); ?></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
