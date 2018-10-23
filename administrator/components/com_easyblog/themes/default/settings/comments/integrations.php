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
<div class="row form-horizontal">
	<div class="col-lg-6">

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_OTHER_COMMENT_TITLE', 'COM_EASYBLOG_SETTINGS_COMMENTS_OTHER_COMMENT_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_easyblog', 'COM_EASYBLOG_SETTINGS_COMMENTS_BUILTIN_COMMENTS'); ?>

				<?php echo $this->html('settings.toggle', 'main_comment_multiple', 'COM_EASYBLOG_SETTINGS_COMMENTS_MULTIPLE_SYSTEM'); ?>

			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_komento', 'COM_EASYBLOG_SETTINGS_COMMENTS_KOMENTO'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYSOCIAL_COMMENTS'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_easysocial', 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYSOCIAL_COMMENTS'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_easydiscuss', 'COM_EASYBLOG_SETTINGS_COMMENTS_EASYDISCUSS'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_disqus', 'COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_DISQUS_CODE', 'comment_disqus_code'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" name="comment_disqus_code" id="comment_disqus_code" class="form-control" value="<?php echo $this->config->get('comment_disqus_code');?>" />
							<span class="input-group-btn">
								<a href="https://stackideas.com/docs/easyblog/administrators/comments/integrating-with-disqus" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_hypercomments', 'COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_HYPERCOMMENTS_WIDGETID', 'comment_hypercomments_widgetid'); ?>

					<div class="col-md-7">
						<input type="text" name="comment_hypercomments_widgetid" id="comment_hypercomments_widgetid" class="form-control" value="<?php echo $this->config->get('comment_hypercomments_widgetid');?>" />
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COMMENTS'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_facebook', 'COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COMMENTS'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_FACEBOOK_COLOUR_SCHEME', 'comment_facebook_colourscheme'); ?>

					<div class="col-md-7">
						<select name="comment_facebook_colourscheme" id="comment_facebook_colourscheme" class="form-control">
							<option<?php echo $this->config->get( 'comment_facebook_colourscheme' ) == 'light' ? ' selected="selected"' : ''; ?> value="light"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT');?></option>
							<option<?php echo $this->config->get( 'comment_facebook_colourscheme' ) == 'dark' ? ' selected="selected"' : ''; ?> value="dark"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK');?></option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_intensedebate', 'COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_COMMENTS_INTENSE_DEBATE_CODE', 'comment_intensedebate_code'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text"  name="comment_intensedebate_code" id="comment_intensedebate_code" class="form-control" value="<?php echo $this->config->get('comment_intensedebate_code');?>" />
							<span class="input-group-btn">
							   <a href="https://stackideas.com/docs/easyblog/administrators/comments/integrating-with-intense-debate" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
							   </a>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_jcomments', 'COM_EASYBLOG_SETTINGS_COMMENTS_JCOMMENT'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_COMMENTS_COMPOJOOM'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_compojoom', 'COM_EASYBLOG_SETTINGS_COMMENTS_COMPOJOOM'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EB_SETTINGS_COMMENTS_JLEX_COMMENTS'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'comment_jlex', 'COM_EB_SETTINGS_COMMENTS_JLEX_COMMENTS'); ?>
			</div>
		</div>
	</div>
</div>
