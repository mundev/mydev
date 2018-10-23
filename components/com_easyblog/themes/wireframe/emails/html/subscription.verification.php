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
<tr>
	<td bgcolor="#ffffff">
		<table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="padding: 20px 20px 0px; font-family: sans-serif; font-size: 13px; line-height: 20px; color: #999999; text-align: left;">
					<p style="margin: 0;"><?php echo JText::_('COM_EB_MAIL_TEMPLATE_SUBSCRIBE_VERIFICATION_SUBHEADING'); ?></p>
				</td>
			</tr>			
			<tr>
				<td style="padding: 0px 20px 20px; text-align: left;">
					<h1 style="margin: 0; font-family: sans-serif; font-size: 22px; line-height: 27px; color: #666666; font-weight: normal;"><?php echo JText::sprintf('COM_EB_MAIL_TEMPLATE_SUBSCRIBE_VERIFICATION_HEADING');?></h1>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td bgcolor="#ffffff">
		<table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="padding: 0 20px 40px; font-family: sans-serif; font-size: 14px; color: #555555; text-align: left;">
					<?php if ($templatePreview) { ?>
						<?php echo '<a href="javascript:void(0);">' . JText::_('COM_EB_NOTIFICATION_SUBSCRIBE_VERIFICATION') . '</a> - Preview site'; ?>
					<?php } else { ?>
						<?php echo '<a href="' . $subscribeLink  . '">' . JText::_('COM_EB_NOTIFICATION_SUBSCRIBE_VERIFICATION') . '</a>' . JText::sprintf('COM_EB_NOTIFICATION_SUBSCRIBE_FROM', '<a href="' . $targetlink  . '">' . $target . '</a>') ; ?>
					<?php } ?>

					<div style="font-size:12px;margin-top:35px;">
						<?php echo JText::_('COM_EB_NOTIFICATION_SUBSCRIBE_VERIFICATION_NOTICE'); ?>
					</div>
				</td>
			</tr>
		</table>
	</td>
</tr>