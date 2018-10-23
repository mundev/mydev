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
<div id="download">
	<div class="">
		<label><?php echo JText::_('COM_EB_GDPR_DOWNLOAD_HEADER');?></label>
	</div>

	<p><?php echo JText::sprintf('COM_EB_GDPR_DOWNLOAD_DESCRIPTION', $this->config->get('gdpr_archive_expiry'));?></p>

	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="download">
		<div id="form-login-password" style="padding-bottom: 5px;">
			<label for="password"><?php echo JText::_('COM_EASYBLOG_PASSWORD') ?></label><br />
			<input id="password" type="password" name="password" class="form-control half" size="18" alt="password" />
		</div>

		<br />
		<input type="submit" name="Submit" class="btn btn-primary" value="<?php echo JText::_('COM_EB_GDPR_DOWNLOAD_VERIFY_BUTTON') ?>" />
		<input type="hidden" value="com_easyblog"  name="option">
		<input type="hidden" value="download.gdpr" name="task">
		<input type="hidden" name="id" value="<?php echo $data; ?>" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />

		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
