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
					<p style="margin: 0;"><?php echo JText::_('COM_EB_MAIL_TEMPLATE_GDPR_DOWNLOAD_SUBHEADING'); ?></p>
				</td>
			</tr>
			<tr>
				<td style="padding: 0px 20px 20px; text-align: left;">
					<h1 style="margin: 0; font-family: sans-serif; font-size: 22px; line-height: 27px; color: #666666; font-weight: normal;"><?php echo JText::sprintf('COM_EB_MAIL_TEMPLATE_GDPR_DOWNLOAD_HEADING');?></h1>
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
					<p style="margin: 0;">
						<?php if ($templatePreview) { ?>
							Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s
						<?php } else { ?>
							<?php echo JText::sprintf('COM_EB_MAIL_TEMPLATE_GDPR_DOWNLOAD_DESCRIPTION', $downloadLink); ?>
						<?php } ?>
					</p>
				</td>
			</tr>
		</table>
	</td>
</tr>
