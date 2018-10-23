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
<tr>
	<td bgcolor="#ffffff">
		<table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="padding: 20px 20px 0px; font-family: sans-serif; font-size: 13px; line-height: 20px; color: #999999; text-align: left;">
					<p style="margin: 0;"><?php echo JText::_('COM_EASYBLOG_MAIL_TEMPLATE_TEAM_APPROVED_SUBHEADING'); ?></p>
				</td>
			</tr>
			<tr>
				<td style="padding: 0px 20px 20px; text-align: left;">
					<h1 style="margin: 0; font-family: sans-serif; font-size: 22px; line-height: 27px; color: #666666; font-weight: normal;"><?php echo JText::sprintf('COM_EASYBLOG_MAIL_TEMPLATE_TEAM_APPROVED_HEADING');?></h1>
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
						<p style="margin: 0;"><?php echo JText::sprintf('COM_EASYBLOG_NOTIFICATION_TEAM_REQUEST_APPROVED', '<b>Preview Team</b>');?></p>
					<?php } else { ?>
						<p style="margin: 0;"><?php echo JText::sprintf('COM_EASYBLOG_NOTIFICATION_TEAM_REQUEST_APPROVED', '<b>' . $teamName . '</b>');?></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td style="padding: 0 20px 40px; font-family: sans-serif; font-size: 15px; color: #555555;">
					<table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="left" >
						<tr>
							<td style="border-radius: 3px; background: #54C063; text-align: center;" class="button-td">
								<a href="<?php echo $templatePreview ? 'javascript:void(0);' : $teamLink;?>" style="background: #54C063; border: 15px solid #54C063; font-family: sans-serif; font-size: 13px; line-height: 1.1; text-align: center; text-decoration: none; display: block; border-radius: 3px; font-weight: bold;" class="button-a">
									<span style="color:#ffffff;" class="button-link">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('COM_EASYBLOG_MAIL_TEMPLATE_VIEW_TEAM');?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
								</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>