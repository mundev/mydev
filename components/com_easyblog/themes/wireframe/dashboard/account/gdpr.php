<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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
	<?php echo $this->html('dashboard.miniHeading', 'COM_EB_GDPR_DOWNLOAD_INFORMATION', 'fa fa-download'); ?>

	<div class="eb-box-body">
		<div class="form-horizontal">
			<div class="form-group">
				<div class="gdpr-download-link center">
					<a class="btn btn-primary-o" href="javascript:void(0);" data-gdpr-download-link><?php echo JText::_('COM_EB_GDPR_DOWNLOAD_INFORMATION');?></a>
				</div>
				<div class="gdpr-description">
					<div><?php echo JText::_('COM_EB_GDPR_INCLUDED_INFORMATION');?></div>
					<div><?php echo JText::_('COM_EB_GDPR_INCLUDED_INFORMATION_DESC');?></div>
				</div>
				<div>
					<?php echo JText::_('COM_EB_GDPR_EXTRA_INFO');?>
				</div>
			</div>
		</div>
	</div>
</div>