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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_FEEDBURNER', 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_FEEDBURNER_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_FEEDBURNER_URL', 'feedburner_url'); ?>
					<div class="col-md-7">
						<?php echo $this->html('form.text', 'feedburner_url', $this->html('string.escape', $feedburner->url), 'feedburner_url'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">

		<?php if ($this->config->get('integration_google_adsense_enable')) { ?>
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_ADSENSE', 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_ADSENSE_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_ENABLE', 'adsense_published'); ?>
					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'adsense_published', $adsense->published); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_CODE', 'adsense_code'); ?>

					<div class="col-md-7">
						<textarea id="adsense_code" name="adsense_code" class="form-control"><?php echo $adsense->code; ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_BLOGGERS_EDIT_ADSENSE_DISPLAY_IN', 'adsense_display'); ?>

					<div class="col-md-7">
						<select name="adsense_display" id="adsense_display" class="form-control">
							<option value="both"<?php echo ($adsense->display == 'both')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_BOTH_HEADER_AND_FOOTER_OPTION'); ?></option>
							<option value="header"<?php echo ($adsense->display == 'header')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_HEADER_OPTION'); ?></option>
							<option value="footer"<?php echo ($adsense->display == 'footer')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_FOOTER_OPTION'); ?></option>
							<option value="beforecomments"<?php echo ($adsense->display == 'beforecomments')? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_BEFORE_COMMENTS_OPTION'); ?></option>
							<option value="userspecified"<?php echo ($adsense->display == 'userspecified')? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYBLOG_ADSENSE_USER_SPECIFIED'); ?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>