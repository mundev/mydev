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
		<label><?php echo JText::_('COM_EB_GDPR_DELETE_INFO_HEADER');?></label>
	</div>

	<p><?php echo JText::_('COM_EB_GDPR_DELETE_INFO_DESCRIPTION');?></p>

	<ul style="">
		<?php if ($userId) { ?>
			<li><?php echo JText::_('COM_EB_GDPR_TAB_POST_TITLE'); ?></li>
			<li><?php echo JText::_('COM_EB_GDPR_TAB_TAG_TITLE'); ?></li>
		<?php } ?>

		<li><?php echo JText::_('COM_EB_GDPR_TAB_COMMENT_TITLE'); ?></li>
		<li><?php echo JText::_('COM_EB_GDPR_TAB_SUBSCRIPTION_TITLE'); ?></li>
	</ul>

	<div class="alert alert-danger" role="alert">
		<?php echo JText::_('COM_EB_GDPR_DELETE_INFO_CONFIRMATION_NOTICE');?>
	</div>

	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="download">

		<?php if ($userId) { ?>
		<div id="form-login-password" style="padding-bottom: 5px;">
			<label for="password"><?php echo JText::_('COM_EB_GDPR_DELETE_INFO_PASSWORD') ?></label><br />
			<input id="password" type="password" name="password" class="form-control half" size="18" alt="password" />
		</div>
		<?php } ?>

		<br />
		<input type="submit" name="Submit" class="btn btn-danger" value="<?php echo JText::_('COM_EASYBLOG_DELETE') ?>" />
		<input type="hidden" value="com_easyblog"  name="option">
		<input type="hidden" value="profile.deleteInfo" name="task">
		<input type="hidden" name="key" value="<?php echo $data; ?>" />

		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
