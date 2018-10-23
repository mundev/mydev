<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-box">
	<?php echo $this->html('dashboard.miniHeading', 'COM_EASYBLOG_DASHBOARD_FACEBOOK_SETTINGS', 'fa fa-facebook-square'); ?>

	<div class="eb-box-body">
		<div class="form-horizontal">
			<?php if ($this->config->get('main_facebook_ogauthor') && $this->acl->get('update_facebook')) { ?>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_FACEBOOK_PROFILE_URL'); ?>

				<div class="col-md-8">
					<?php echo $this->html('dashboard.text', 'facebook_profile_url', $params->get('facebook_profile_url')); ?>
					<p class="small">
						<?php echo JText::_('COM_EASYBLOG_FACEBOOK_PROFILE_URL_INFO'); ?>
					</p>
				</div>
			</div>
			<?php } ?>			
		</div>
	</div>
</div>