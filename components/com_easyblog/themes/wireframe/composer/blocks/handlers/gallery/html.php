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
<div class="eb-composer-placeholder eb-gallery-upload-placeholder text-center" data-key="_cG9zdA--" data-type="image" contenteditable="false">
	<div class="eb-gallery is-empty">
		<div class="eb-gallery-stage" data-plupload-drop-element>
			<div class="eb-gallery-viewport">
				<div class="eb-gallery-item is-placeholder">
					<div class="eb-composer-placeholder-content" >
						<i class="eb-composer-placeholder-icon fa fa-image"></i>
						<b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_BLOCKS_GALLERY_PREVIEW_TITLE');?></b>
						<p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_BLOCKS_GALLERY_PREVIEW_INFO');?></p>
						<p data-eb-file-error class="hide eb-composer-placeholder-error text-error"><?php echo JText::_('COM_EASYBLOG_INVALID_FILE');?></p>
						<span class="eb-plupload-btn">
							<a href="javascript:void(0);" class="btn btn-eb-default-o btn--sm btn-browse" 
								data-eb-gallery-browse
								data-eb-mm-browse-button
								data-eb-mm-start-uri="_cG9zdA--"
								data-eb-mm-filter="image"
								data-eb-mm-browse-place="local"
								data-eb-mm-browse-type="gallery"
							>
								<?php echo JText::_('COM_EASYBLOG_MM_BROWSE_MEDIA');?>
							</a>

							<?php if ($this->acl->get('upload_image')) { ?>
							<button type="button" class="btn btn--sm btn-eb-primary" data-plupload-browse-button>
								<i class="fa fa-upload"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_UPLOAD_IMAGE');?>
							</button>
							<?php } ?>
						</span>
					</div>
				</div>
			</div>
			<div class="eb-gallery-button eb-gallery-next-button">
				<i class="fa fa-chevron-right"></i>
			</div>
			<div class="eb-gallery-button eb-gallery-prev-button">
				<i class="fa fa-chevron-left"></i>
			</div>
		</div>

		<div class="eb-gallery-menu">
			<div class="eb-gallery-menu-item is-placeholder active" data-id="placeholder">
				<div></div>
			</div>
		</div>

	</div>

	<?php echo $this->output('site/composer/progress'); ?>
</div>
