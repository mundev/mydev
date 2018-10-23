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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_TITLE', 'COM_EASYBLOG_SETTINGS_WORKFLOW_REPORTING_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_facebook_ogauthor', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_USE_AUTHOR_URL'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID', 'main_facebook_like_admin'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" name="main_facebook_like_admin" id="main_facebook_like_admin" class="form-control" value="<?php echo $this->config->get('main_facebook_like_admin');?>" />
							<span class="input-group-btn">
								<a href="https://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-facebook-autoposting" target="_blank" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID', 'main_facebook_like_appid'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" name="main_facebook_like_appid" id="main_facebook_like_appid" class="form-control" value="<?php echo $this->config->get('main_facebook_like_appid');?>" />

							<span class="input-group-btn">
								<a href="https://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-facebook-autoposting" target="_blank" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_facebook_scripts', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS'); ?>

				<?php echo $this->html('settings.toggle', 'main_facebook_opengraph', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APPEND_OPENGRAPH_HEADERS'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS', 'COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_facebook_analytics', 'COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_ENABLE'); ?>

				<div>
					<span class="label label-danger"><?php echo JText::_('COM_EASYBLOG_NOTE');?></span><br />
					<?php echo JText::_('COM_EASYBLOG_SOCIAL_INTEGRATIONS_ANALYTICS_NOTE');?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_BUTTON_STYLING'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_facebook_like_faces', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES'); ?>

				<?php echo $this->html('settings.toggle', 'main_facebook_like_send', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB', 'main_facebook_like_verb'); ?>
					<div class="col-md-7">
						<select id="main_facebook_like_verb" name="main_facebook_like_verb" class="form-control" onchange="switchFBPosition();">
							<option<?php echo $this->config->get( 'main_facebook_like_verb' ) == 'like' ? ' selected="selected"' : ''; ?> value="like"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_LIKES');?></option>
							<option<?php echo $this->config->get( 'main_facebook_like_verb' ) == 'recommend' ? ' selected="selected"' : ''; ?> value="recommend"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_RECOMMENDS');?></option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES', 'main_facebook_like_theme'); ?>

					<div class="col-md-7">
						<select name="main_facebook_like_theme" id="main_facebook_like_theme" class="form-control">
							<option<?php echo $this->config->get('main_facebook_like_theme') == 'light' ? ' selected="selected"' : ''; ?> value="light"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT');?></option>
							<option<?php echo $this->config->get('main_facebook_like_theme') == 'dark' ? ' selected="selected"' : ''; ?> value="dark"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK');?></option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_FACEBOOK_INSTANT_ARTICLE'); ?>

			<div class="panel-body">
				<p><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_FACEBOOK_INSTANT_ARTICLE_INSTRUCTIONS', 'https://stackideas.com/docs/easyblog/administrators/configuration/instant-article-configuration');?></p>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_FACEBOOK_INSTANT_ARTICLE_ID', 'facebook_instant_article_id'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" name="facebook_instant_article_id" id="facebook_instant_article_id" class="form-control" value="<?php echo $this->config->get('facebook_instant_article_id');?>" />
							<span class="input-group-btn">
								<a href="https://stackideas.com/docs/easyblog/administrators/configuration/instant-article-configuration" target="_blank" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_FACEBOOK_INSTANT_ARTICLE_URL', 'facebook_instant_article_url'); ?>

					<div class="col-md-7">
						<div class="form-control-static"><?php echo JURI::root();?>index.php?option=com_easyblog&view=latest&format=instant</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_FACEBOOK_ADS_PLACEMENT_ID', 'facebook_ads_placement_id'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" name="facebook_ads_placement_id" id="facebook_ads_placement_id" class="form-control" value="<?php echo $this->config->get('facebook_ads_placement_id');?>" />
							<span class="input-group-btn">
								<a href="https://developers.facebook.com/docs/instant-articles/monetization/audience-network" target="_blank" class="btn btn-default">
									<i class="fa fa-life-ring"></i>
								</a>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_FB_ADS_DENSITY', 'fb_ads_density'); ?>

					<div class="col-md-7">
						<select name="fb_ads_density" id="fb_ads_density" class="form-control">
							<option<?php echo $this->config->get('fb_ads_density') == 'default' ? ' selected="selected"' : ''; ?> value="default"><?php echo JText::_('COM_EASYBLOG_SETTINGS_FB_ADS_DENSITY_DEFAULT');?></option>
							<option<?php echo $this->config->get('fb_ads_density') == 'medium' ? ' selected="selected"' : ''; ?> value="medium"><?php echo JText::_('COM_EASYBLOG_SETTINGS_FB_ADS_DENSITY_MEDIUM');?></option>
							<option<?php echo $this->config->get('fb_ads_density') == 'low' ? ' selected="selected"' : ''; ?> value="low"><?php echo JText::_('COM_EASYBLOG_SETTINGS_FB_ADS_DENSITY_LOW');?></option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_FACEBOOK_ADS_WIDTH', 'facebook_ads_width'); ?>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="facebook_ads_width" id="facebook_ads_width" class="form-control" value="<?php echo $this->config->get('facebook_ads_width', 300);?>" />
									<span class="input-group-addon">px</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_FACEBOOK_ADS_HEIGHT', 'facebook_ads_height'); ?>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="facebook_ads_height" id="facebook_ads_height" class="form-control" value="<?php echo $this->config->get('facebook_ads_height', 250);?>" />
									<span class="input-group-addon">px</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
