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
<div class="eb-pdf-viewer">
	<object data="<?php echo $block->data->url;?>" type="application/pdf" width="100%" height="<?php echo $block->data->height;?>" class="pdf-viewer-browser">
		<iframe src="<?php echo $block->data->url;?>" width="100%" height="<?php echo $block->data->height;?>" scrolling="no" frameborder="0" allowTransparency="true">
			<?php echo JText::_('COM_EASYBLOG_BROWSER_DOES_NOT_SUPPORT_PDF_VIEWER'); ?><br />
			<a href="<?php echo $block->data->url;?>"><?php echo JText::_('COM_EASYBLOG_DOWNLOAD_PDF_FILE');?></a>
		</iframe>
	</object>
	<div class="pdf-viewer-button">
		<a href="<?php echo $block->data->url;?>" target="_blank" class="btn btn-default"><i class="fa fa-download"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DOWNLOAD_PDF_FILE');?></a>
	</div>
</div>