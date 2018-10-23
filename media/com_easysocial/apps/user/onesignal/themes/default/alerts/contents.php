<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="notification-content-push t-hidden" data-es-alert-contents="push">
	<legend><?php echo JText::_('APP_USER_ONESIGNAL_ALERTS_HEADING');?></legend>

	<table width="100%">
		<tr>
			<td width="55%">&nbsp;</td>
			<td width="25%">&nbsp;</td>
			<td width="20%" style="text-align:center;">&nbsp;</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_('APP_USER_ONESIGNAL_ALERTS_RECEIVE_BROWSER_NOTIFICATIONS');?>
			</td>
			<td class="t-text--right">
				<i class="fa fa-question-circle" <?php echo $this->html('bootstrap.popover', 'APP_USER_ONESIGNAL_ALERTS_RECEIVE_BROWSER_NOTIFICATIONS'); ?>></i>
			</td>
			<td class="t-lg-p--md t-text--center">	
				<?php echo $this->html('form.toggler', 'push', false, 'push', 'data-alerts-onesignal-push');?>
			</td>
		</tr>
	</table>
</div>