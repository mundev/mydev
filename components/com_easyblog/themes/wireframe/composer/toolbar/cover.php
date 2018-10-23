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
<button type="button" class="btn eb-comp-toolbar__nav-btn dropdown-toggle_" data-bp-toggle="dropdown">
	<i class="fa fa-photo"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_POST_COVER');?>
</button>

<div class="dropdown-menu eb-comp-toolbar-dropdown-menu eb-comp-toolbar-dropdown-menu--cover" data-cover-container>
	<div class="eb-comp-toolbar-dropdown-menu__hd">
		<?php echo JText::_('COM_EASYBLOG_POST_COVER');?>
		<div class="eb-comp-toolbar-dropdown-menu__hd-action">
			<a href="javascript:void(0);" class="eb-comp-toolbar-dropdown-menu__close" data-toolbar-dropdown-close>
				<i class="fa fa-times-circle"></i>
			</a>
		</div>
	</div>
	<div class="eb-comp-toolbar-dropdown-menu__bd">
		<div class="<?php echo !empty($post->image) ? " has-image" : ""; ?>"
			data-cover-placeholder
			data-eb-composer-art
			data-id="cover"
			data-key="_cG9zdA--"
			data-type="image"
			data-plupload-multi-selection="0"
		>
			<div class="o-progress-radial" data-eb-mm-upload-progress-bar>
				<div class="o-progress-radial__overlay" data-eb-mm-upload-progress-value></div>
			</div>

			<div class="eb-comp-cover-area" data-cover-workarea data-plupload-drop-element>
				<div class="eb-comp-cover-area__remove hide" data-cover-remove>
					<i class="fa fa-close"></i> <?php echo JText::_('COM_EASYBLOG_REMOVE');?>
				</div>
				<div class="eb-comp-cover-area__embed">
					<div class="eb-comp-cover-area__embed-item" data-cover-preview style="<?php echo $post->image ? 'background-image: url(\'' . $post->getImage() . '\');' : '';?>"></div>
				</div>

				<div class="eb-comp-cover-area__empty">
					<i class="fa fa-photo"></i>
					<div class="t-lg-mt--md t-lg-mb--lg"><?php echo JText::_('COM_EASYBLOG_POST_COVER_DROP_IMAGE_TO_UPLOAD');?></div>	

					<div class="t-lg-mt--lg" style="margin-top: 35px !important;">
						<div class="o-grid-sm">
							<div class="o-grid-sm__cell t-text--right">
								<a href="javascript:void(0);" class="btn btn-eb-default btn--sm btn-browse" 
									data-cover-browse
									data-eb-mm-browse-button
									data-eb-mm-start-uri="_cG9zdA--"
									data-eb-mm-filter="image"
									data-eb-mm-browse-place="local"
									data-eb-mm-browse-type="cover"
								>
									<?php echo JText::_('COM_EASYBLOG_MM_BROWSE_MEDIA');?>
								</a>
							</div>
							<div class="o-grid-sm__cell o-grid-sm__cell--auto-size">
								<div class="t-lg-ml--sm t-lg-mr--sm t-lg-mt--sm"><?php echo JText::_('COM_EASYBLOG_OR');?></div>
							</div>
							<div class="o-grid-sm__cell t-text--left">
								<a href="javascript:void(0);" class="btn btn-eb-primary btn--sm btn-primary" 
									data-cover-upload
									data-plupload-browse-button
									data-eb-composer-blogimage-browse-button
								>
									<?php echo JText::_('COM_EASYBLOG_UPLOAD_BUTTON');?>
								</a>
							</div>
						</div>
						<div class="eb-nmm-media__info">
							<div class="eb-nmm-media__failed-txt text-danger">
								<i class="fa fa-exclamation-circle" data-eb-mm-failed-message></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_MM_UPLOAD_FAILED');?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="eb-comp-cover-field t-lg-mt--md">
				<div class="o-grid o-grid--gutters t-lg-mb--no">
					<div class="o-grid__cell">
						<div class="">
							<input type="text" name="params[image_cover_title]" value="<?php echo $post->getParams()->get('image_cover_title'); ?>" placeholder="<?php echo JText::_('COM_EASYBLOG_POST_COVER_TITLE_PLACEHOLDER', true);?>" 
							class="o-form-control" data-cover-title />
						</div>		
					</div>
					<div class="o-grid__cell">
						<div class="">
							<input type="text" name="params[image_cover_caption]" value="<?php echo $post->getParams()->get('image_cover_caption'); ?>" placeholder="<?php echo JText::_('COM_EASYBLOG_POST_COVER_CAPTION_PLACEHOLDER', true);?>" 
								class="o-form-control" data-cover-caption />
						</div>
					</div>
				</div>
				

				
			</div>

			<input type="hidden" name="image" value="<?php echo $post->image;?>" data-cover-value />
		</div>
	</div>
</div>