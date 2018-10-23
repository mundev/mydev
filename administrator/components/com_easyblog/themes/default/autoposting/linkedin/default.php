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
<form name="adminForm" action="index.php" method="post" class="adminForm" id="adminForm">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->html('panel.heading', 'COM_EASYBLOG_AUTOPOST_LINKEDIN_APP_SETTINGS', 'COM_EASYBLOG_AUTOPOST_LINKEDIN_APP_SETTINGS_INFO'); ?>

				<div class="panel-body">
					<?php echo $this->html('settings.toggle', 'integrations_linkedin', 'COM_EASYBLOG_AUTOPOST_LINKEDIN_ENABLE'); ?>

					<div class="form-group" data-facebook-api>
						<?php echo $this->html('form.label', 'COM_EB_SETTINGS_SOCIALSHARE_LINKEDIN_OAUTH_REDIRECT_URI', 'integrations_linkedin_oauth_redirect_uri'); ?>

						<div class="col-md-7">
							<p>Effective <b>May 18th, 2018</b>, LinkedIn has <a href="https://developer.linkedin.com/docs/guide/v2/compliance/release-notes#2018-04-11" target="_blank">announced</a> that V1 Company Pages API will no longer work with OAuth 1.0. It is recommended for all apps to switchover OAuth 2.0 authentication before then. You will need to copy the links below and add it under the Authorized Redirect URLs section of the LinkedIn app.</p>
							<?php
							if (EBR::isSefEnabled()) {
								$i = 1;
								foreach ($oauthURIs as $oauthURI) { ?>
								<div class="input-group mb-10">
									<input type="text" id="oauth-uri-<?php echo $i?>" data-oauthuri-input name="integrations_linkedin_oauth_redirect_uri" class="form-control" value="<?php echo $oauthURI;?>" size="60" style="pointer-events:none;" />
									<span class="input-group-btn" 
										data-oauthuri-button
										data-original-title="<?php echo JText::_('COM_EB_COPY_TOOLTIP')?>"
										data-placement="bottom"
										data-eb-provide="tooltip"
									>
										<a href="javascript:void(0);" class="btn btn-default">
											<i class="fa fa-copy"></i>
										</a>
									</span>
								</div>
								<?php $i++; } ?>
							<?php } else { ?>
							<div style="margin:15px 0 8px 0;border: 1px dashed #FC595B;padding: 20px;color: #FC595B;">
								<b>Note:</b> It seems like your site Search Engine Friendly (SEF) is disabled. In order to use LinkedIn as autoposting medium, you must first enable the SEF of your site.
							</div>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_API_KEY', 'integrations_linkedin_api_key'); ?>

						<div class="col-md-7">
							<div class="input-group">
								<input type="text" name="integrations_linkedin_api_key" id="integrations_linkedin_api_key" class="form-control" value="<?php echo $this->config->get('integrations_linkedin_api_key');?>" />
								<span class="input-group-btn">
									<a href="https://stackideas.com/docs/easyblog/administrators/autoposting/linkedin-autoposting" target="_blank" class="btn btn-default">
										<i class="fa fa-life-ring"></i>
									</a>
								</span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_SECRET_KEY', 'integrations_linkedin_secret_key'); ?>

						<div class="col-md-7">
							<div class="input-group">
								<input type="text" name="integrations_linkedin_secret_key" class="form-control" value="<?php echo $this->config->get('integrations_linkedin_secret_key');?>" size="60" />
								<span class="input-group-btn">
									<a href="https://stackideas.com/docs/easyblog/administrators/autoposting/linkedin-autoposting" target="_blank" class="btn btn-default">
										<i class="fa fa-life-ring"></i>
									</a>
								</span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_AUTOPOSTING_LINKEDIN_ACCESS', 'linkedin_access'); ?>

						<div class="col-md-7">
							<?php if ($associated) { ?>
								<div>
									<div style="margin-top:5px;">
										<?php echo $client->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=linkedin', true);?>
									</div>
								</div>
							<?php } else { ?>
								<?php if ($this->config->get('integrations_linkedin_api_key') && $this->config->get('integrations_linkedin_secret_key')) { ?>
									<?php echo $client->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=linkedin', true);?>

									<div class="mt-10">
										<?php echo JText::_('COM_EB_INTEGRATIONS_LINKEDIN_ACCESS_DESC');?>
									</div>
								<?php } else { ?>
									<?php echo JText::_('COM_EB_AUTOPOSTING_SAVE_SETTING_NOTICE');?>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>

			<div class="panel">
				<?php echo $this->html('panel.heading', 'COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES', 'COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_INFO'); ?>

				<div class="panel-body">
					<?php if ($companies && !empty($companies)){ ?>
					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_SELECT_COMPANY', 'integrations_linkedin_company'); ?>

						<div class="col-md-7">
							<select name="integrations_linkedin_company[]" id="integrations_linkedin_company" multiple style="height: 150px;">
								<?php foreach ($companies as $company) { ?>
								<option value="<?php echo $company->id;?>"<?php echo in_array($company->id , $storedCompanies ) ? ' selected="selected"' : '';?>><?php echo $company->title;?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<?php } else { ?>
						<div>
							<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_UNAVAILABLE');?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->html('panel.heading', 'COM_EASYBLOG_AUTOPOSTING_GENERAL_TITLE', 'COM_EASYBLOG_AUTOPOSTING_GENERAL_DESC'); ?>

				<div class="panel-body">
					<?php echo $this->html('settings.toggle', 'integrations_linkedin_centralized', 'COM_EASYBLOG_AUTOPOSTING_CENTRALIZED'); ?>

					<?php echo $this->html('settings.toggle', 'integrations_linkedin_centralized_auto_post', 'COM_EASYBLOG_AUTOPOST_ON_NEW_POST'); ?>

					<?php echo $this->html('settings.toggle', 'integrations_linkedin_centralized_send_updates', 'COM_EASYBLOG_AUTOPOST_ON_UPDATES'); ?>

					<?php echo $this->html('settings.toggle', 'integrations_linkedin_centralized_and_own', 'COM_EASYBLOG_LINKEDIN_ALLOW_AUTHOR_USE_OWN_ACCOUNT'); ?>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_DEFAULT_MESSAGE', 'main_linkedin_message'); ?>

						<div class="col-md-7">
							<textarea name="main_linkedin_message" id="main_linkedin_message" class="form-control" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('main_linkedin_message', JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE_STRING'));?></textarea>
							<div class="mt-10"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_MESSAGE_DESC');?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action'); ?>
</form>
