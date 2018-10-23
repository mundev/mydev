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
					<?php if ($templatePreview) { ?>
						<p style="margin: 0;"><?php echo JText::sprintf('COM_EASYBLOG_MAIL_TEMPLATE_POST_SCHEDULED_SUBHEADING', '18th August 2017'); ?></p>
					<?php } else { ?>
						<p style="margin: 0;"><?php echo JText::sprintf('COM_EASYBLOG_MAIL_TEMPLATE_POST_SCHEDULED_SUBHEADING', $blogDate); ?></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td style="padding: 0px 20px 20px; text-align: left;">
					<h1 style="margin: 0; font-family: sans-serif; font-size: 22px; line-height: 27px; color: #666666; font-weight: normal;"><?php echo JText::sprintf('COM_EASYBLOG_MAIL_TEMPLATE_POST_SCHEDULED_HEADING');?></h1>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td dir="ltr" bgcolor="#ffffff" height="100%" valign="top" width="100%" style="padding: 10px 0;">

		<!--[if mso]>
		<table role="presentation" aria-hidden="true" border="0" cellspacing="0" cellpadding="0" width="660" style="width: 660px;">
		<tr>
		<td valign="top" width="660" style="width: 660px;">
		<![endif]-->
		<table role="presentation" aria-hidden="true" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:660px;">
			<tr>
				<td valign="top" style="font-size:0; padding: 10px 0;">
					<!--[if mso]>
					<table role="presentation" aria-hidden="true" border="0" cellspacing="0" cellpadding="0" width="660" style="width: 660px;">
					<tr>
					<td align="left" valign="top" width="84" style="width: 84px;">
					<![endif]-->
					<div style="display:inline-block; margin: 0 -2px; max-width: 84px; min-width:64px; vertical-align:top; width:100%;" class="stack-column">
						<table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td dir="ltr" style="padding: 0 10px 10px 10px;">
									<?php if ($templatePreview) { ?>
										<img src="<?php echo JURI::root();?>components/com_easyblog/assets/images/default_blogger.png" aria-hidden="true" width="64" height="64" border="0" alt="" class="center-on-narrow" style="width: 100%; max-width: 64px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
									<?php } else { ?>
										<img src="<?php echo $blogAuthorAvatar;?>" aria-hidden="true" width="64" height="64" border="0" alt="" class="center-on-narrow" style="width: 100%; max-width: 64px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
									<?php } ?>
								</td>
							</tr>
						</table>
					</div>
					<!--[if mso]>
					</td>
					<td align="left" valign="top" width="576" style="width: 576px;">
					<![endif]-->
					<div style="display:inline-block; margin: 0 -2px; max-width:86.66%; min-width:320px; vertical-align:top;" class="stack-column">
						<table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td dir="ltr" style="font-family: sans-serif; font-size: 20px; line-height: 28px; color: #555555; padding: 0px 10px 0; text-align: left;" class="center-on-narrow">
									<a href="<?php echo $templatePreview ? 'javascript:void(0);' : $blogLink;?>" target="_blank" style="color: #4E92DF; text-decoration: none; line-height: 28px;"><?php echo $templatePreview ? 'Blog title' : $blogTitle;?></a>

									<p style="margin:0px 0 10px 0;font-size: 14px; line-height: 28px;">
										<a href="<?php echo $templatePreview ? 'javascript:void(0);' : $blogAuthorLink;?>" target="_blank" style="color: #4E92DF; text-decoration: none;"><?php echo $templatePreview ? 'John Doe' : $blogAuthor;?></a> 
										<?php if ($templatePreview) { ?>
											<?php echo JText::sprintf('COM_EASYBLOG_MAIL_TEMPLATE_POSTED_ON', '13th August 2017');?>
										<?php } else { ?>
											<?php echo JText::sprintf('COM_EASYBLOG_MAIL_TEMPLATE_POSTED_ON', $blogDate);?>
										<?php } ?>
									</p>
								</td>
							</tr>
						</table>
					</div>
					<!--[if mso]>
					</td>
					</tr>
					</table>
					<![endif]-->
				</td>
			</tr>
		</table>
		<!--[if mso]>
		</td>
		</tr>
		</table>
		<![endif]-->
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
							<?php echo $blogIntro; ?>
						<?php } ?>
					</p>
				</td>
			</tr>
			<tr>
				<td style="padding: 0 20px 40px; font-family: sans-serif; font-size: 15px; color: #555555;">
					<table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="left" >
						<tr>
							<td style="border-radius: 3px; background: #54C063; text-align: center;" class="button-td">
								<a href="<?php echo $templatePreview ? 'javascript:void(0);' : $blogLink;?>" style="background: #54C063; border: 15px solid #54C063; font-family: sans-serif; font-size: 13px; line-height: 1.1; text-align: center; text-decoration: none; display: block; border-radius: 3px; font-weight: bold;" class="button-a">
									<span style="color:#ffffff;" class="button-link">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('COM_EASYBLOG_NOTIFICATION_READ_MORE');?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
								</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>