<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<tr>
	<td bgcolor="#ffffff">
		<table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="padding: 24px; text-align: left;">
					<h1 style="margin: 0; font-family: sans-serif; font-size: 22px; line-height: 27px; color: #666666; font-weight: normal;"><?php echo JText::sprintf('COM_EASYSOCIAL_EMAILS_NEW_FOLLOWER_HEADING'); ?></h1>
				</td>
			</tr>
		</table>
	</td>
</tr>

<tr>
	<td dir="ltr" bgcolor="#ffffff" height="100%" valign="top" width="100%" style="padding: 20px 24px 24px; font-family: sans-serif; font-size: 14px; color: #555555; text-align: center;">

		<!--[if mso]>
		<table role="presentation" aria-hidden="true" border="0" cellspacing="0" cellpadding="0" width="660" style="width: 660px;">
		<tr>
		<td valign="top" width="660" style="width: 660px;">
		<![endif]-->
		<table role="presentation" aria-hidden="true" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:660px;">
			<tr>
				<td bgcolor="#f6f9fb" align="center" style="padding: 24px;">
					<table role="presentation" aria-hidden="true" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:660px;">
						<tr>
							<td valign="top" width="64">
								<span style="display:block;width:64px;border-radius:50%; -moz-border-radius:50%; -webkit-border-radius:50%;background:#fff">
									<a href="<?php echo $actorLink;?>"><img src="<?php echo $actorAvatar;?>" alt="<?php echo $this->html( 'string.escape' , $actor );?>" style="border-radius:50%; -moz-border-radius:50%; -webkit-border-radius:50%;background:#fff;vertical-align: middle;" width="64" height="64"/></a>
								</span>
							</td>
							<td valign="middle" style="padding: 0 16px;">
								<table align="left" style="font-size: 14px;margin: 0 auto 10px 20px; text-align:left;color:#798796" align="">
									<tr>
										<td style="padding: 5px 0;">
											<a href="<?php echo $requesterLink;?>" style="color: #00aeef;text-decoration:none;"><strong><?php echo $actor;?></strong></a>
										</td>
									</tr>
									<tr>
										<td style="padding: 5px 0;">
											<?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FOLLOWING' );?>: <?php echo $totalFollowing;?>
											<span style="font-size:9px;margin:0 5px;">&bull;</span>
											<?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FOLLOWERS' );?>: <?php echo $totalFollowers;?>
										</td>
									</tr>
								</table>
								<table role="presentation" aria-hidden="true" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:660px;">
									<tr>
										<td style="padding: 20px 0 5px;">
											<a href="<?php echo $actorLink;?>" style="color:#00aeef;text-decoration:none;"><?php echo JText::_('COM_EASYSOCIAL_EMAILS_VIEW_PROFILE');?> &rarr;</a>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<!--[if mso]>
		</td>
		</tr>
		</table>
		<![endif]-->

		<!--[if mso]>
		<table role="presentation" aria-hidden="true" border="0" cellspacing="0" cellpadding="0" width="660" style="width: 660px;">
		<tr>
		<td valign="top" width="660" style="width: 660px;">
		<![endif]-->
		<p style="margin-top: 40px;">
			<?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FOLLOWER_CHECKOUT_ALL_YOUR_FOLLOWERS' );?> <a href="<?php echo FRoute::followers( array( 'external' => true ) );?>" style="color:#00aeef; text-decoration:none;"><?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FOLLOWER_SEE_ALL' ); ?></a>
		</p>
		<!--[if mso]>
		</td>
		</tr>
		</table>
		<![endif]-->
	</td>
</tr>