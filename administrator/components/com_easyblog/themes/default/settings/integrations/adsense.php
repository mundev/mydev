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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'integration_google_adsense_enable', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE'); ?>
				<?php echo $this->html('settings.toggle', 'integration_google_adsense_script', 'COM_EB_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_LOAD_SCRIPT'); ?>
				<?php echo $this->html('settings.toggle', 'integration_google_adsense_centralized', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_USE_CENTRALIZED'); ?>
				<?php echo $this->html('settings.toggle', 'integrations_google_adsense_blogger', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_ALLOW_BLOGGER_UPDATE'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY', 'integration_google_adsense_display'); ?>

					<div class="col-md-7">
						<select id="integration_google_adsense_display" name="integration_google_adsense_display" class="form-control" onchange="switchFBPosition();">
							<option <?php echo $this->config->get( 'integration_google_adsense_display' ) == 'both' ? ' selected="selected"' : ''; ?> value="both"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_BOTH');?></option>
							<option <?php echo $this->config->get( 'integration_google_adsense_display' ) == 'header' ? ' selected="selected"' : ''; ?> value="header"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_HEADER');?></option>
							<option <?php echo $this->config->get( 'integration_google_adsense_display' ) == 'footer' ? ' selected="selected"' : ''; ?> value="footer"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_FOOTER');?></option>
							<option <?php echo $this->config->get( 'integration_google_adsense_display' ) == 'beforecomments' ? ' selected="selected"' : ''; ?> value="beforecomments"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_BEFORE_COMMENT');?></option>
							<option <?php echo $this->config->get( 'integration_google_adsense_display' ) == 'userspecified' ? ' selected="selected"' : ''; ?> value="userspecified"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_USER_SPECIFIED');?></option>
						</select>

						<div class="mt-10">
							<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY_NOTE');?>:<br />
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS', 'integration_google_adsense_display_access'); ?>

					<div class="col-md-7">
						<select id="integration_google_adsense_display_access" name="integration_google_adsense_display_access" class="form-control" onchange="switchFBPosition();">
							<option<?php echo $this->config->get( 'integration_google_adsense_display_access' ) == 'both' ? ' selected="selected"' : ''; ?> value="both"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_ALL');?></option>
							<option<?php echo $this->config->get( 'integration_google_adsense_display_access' ) == 'members' ? ' selected="selected"' : ''; ?> value="members"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_MEMBERS');?></option>
							<option<?php echo $this->config->get( 'integration_google_adsense_display_access' ) == 'guests' ? ' selected="selected"' : ''; ?> value="guests"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_ADSENSE_DISPLAY_GUESTS');?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODES', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODES_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'integration_google_adsense_responsive', 'COM_EASYBLOG_SETTINGS_ADSENSE_RESPONSIVE', '', 'data-adsense-responsive'); ?>

				<div class="form-group form-responsive<?php echo !$this->config->get('integration_google_adsense_responsive') ? ' hide' : '';?>" data-responsive-form>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_ADSENSE_RESPONSIVE_CODES', 'integration_google_adsense_responsive_code'); ?>

					<div class="col-md-7">
						<textarea name="integration_google_adsense_responsive_code" id="integration_google_adsense_responsive_code" rows="5" class="form-control" cols="35"><?php echo $this->html('string.escape', $this->config->get('integration_google_adsense_responsive_code'));?></textarea>

						<div class="mt-10">
							<?php echo JText::_('COM_EASYBLOG_SETTINGS_ADSENSE_ONLY_CODES_BELOW');?><br />

							<pre><?php echo $this->html('string.escape', '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-XXXXXXXXXXXX" data-ad-slot="xxxx" data-ad-format="auto"></ins>');?></pre>
						</div>
					</div>
				</div>

				<div class="form-group form-standard<?php echo $this->config->get('integration_google_adsense_responsive') ? ' hide' : '';?>" data-code-form>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODE', 'integration_google_adsense_code'); ?>

					<div class="col-md-7">
						<textarea name="integration_google_adsense_code" id="integration_google_adsense_code" rows="5" class="form-control" cols="35"><?php echo $this->config->get('integration_google_adsense_code');?></textarea>

						<div class="mt-10">
							<?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_EXAMPLE');?>:<br />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
