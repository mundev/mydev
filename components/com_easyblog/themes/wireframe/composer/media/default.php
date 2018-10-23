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
<div class="eb-nmm-popup t-hidden <?php echo $this->isMobile() ? '' : ' is-places-open';?>" data-mm-frame
	data-mm-uploader-url="<?php echo $uploadUrl; ?>"
	data-mm-uploader-max-file-size="<?php echo $this->config->get('main_upload_image_size'); ?>mb"
	data-mm-uploader-extensions="<?php echo $this->config->get('main_media_extensions'); ?>"
	data-uri="user:<?php echo $this->my->id;?>"
	data-acl-upload="<?php echo $this->acl->get('upload_image') ? '1' : '0';?>"
	data-mobile="<?php echo $this->isMobile() ? 1 : 0;?>"
>
	<div class="eb-nmm" data-plupload-drop-element>

		<div class="eb-nmm-places" data-scrolly="y">
			<div class="eb-nmm-places__actionbar">		
				<div class="eb-nmm-places__title">
					<b><?php echo JText::_('COM_EASYBLOG_MM_PLACES');?></b>
				</div>

				<a href="javascript:void(0);" class="eb-nmm-places__close" data-mm-mobile-close-places>
					<i class="fa fa-chevron-left"></i>
				</a>
			</div>

			<div class="eb-nmm-places-groups">
				<div class="eb-nmm-place__item is-active">
					<div class="eb-nmm-places-list">
						<?php foreach ($places as $place) { ?>
						<div class="eb-nmm-places-list__item" data-mm-place data-id="<?php echo $place->id;?>" data-key="<?php echo $place->key;?>">
							<a href="javascript:void(0);" class="eb-nmm-places-list__link">
								<i class="eb-nmm-places-list__icon <?php echo $place->icon;?>"></i>&nbsp; <?php echo $place->title;?>
							</a>
							<div class="o-loader o-loader--sm"></div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="eb-nmm-main">
			<div class="eb-nmm-main-header">
				<div class="eb-nmm-main-header__title"><?php echo JText::_('COM_EASYBLOG_COMPOSER_MEDIA_MANAGER');?></div>
				<a href="javascript:void(0);" class="eb-nmm-main-header__close" data-mm-close>
					<i class="fa fa-times-circle"></i>
				</a>
			</div>

			<div class="eb-nmm-mobile-actionbar">
				<div class="eb-nmm-mobile-actionbar__cell eb-nmm-mobile-actionbar__cell--toggle-places">
					<a href="javascript:void(0);" data-mm-mobile-open-places>
						<i class="fa fa-sitemap"></i>
					</a>
				</div>
				<div class="eb-nmm-mobile-actionbar__cell eb-nmm-mobile-actionbar__cell--places" data-mm-breadcrumbs>
				</div>
				<div class="eb-nmm-mobile-actionbar__cell eb-nmm-mobile-actionbar__cell--action">
					<a href="javascript:void(0);" class="btn btn-eb-danger-o btn-nmm-delete" data-mm-delete>
						<i class="fa fa-trash"></i>
					</a>
					
					<a href="javascript:void(0);" class="btn btn-eb-default-o btn-nmm-new-folder" data-mm-create-folder>
						<i class="fa fa-plus"></i>
					</a>

					<div class="t-lg-ml--md" data-mm-upload>
						<a href="javascript:void(0);" class="btn btn-eb-default-o btn-nmm-upload" data-plupload-browse-button>
							<i class="fa fa-upload"></i>
						</a>
					</div>
				</div>

			</div>

			<div class="eb-nmm-actionbar">
				
				<div class="eb-nmm-actionbar__cell eb-nmm-actionbar__cell--breadcrumb">
					<div class="eb-nmm-breadcrumb" data-mm-breadcrumbs>
					</div>
				</div>

				<div class="eb-nmm-actionbar__cell eb-nmm-actionbar__cell--action">
					<div class="eb-nmm-sub-action">

						<a href="javascript:void(0);" data-mm-layout data-type="list">
							<i class="fa fa-th-list"></i>
						</a>

						<a href="javascript:void(0);" class="is-active" data-mm-layout data-type="grid">
							<i class="fa fa-th-large"></i>
						</a>
					</div>
				</div>

				<?php if ($this->acl->get('upload_image')) { ?>
				<div class="eb-nmm-actionbar__cell eb-nmm-actionbar__cell--upload">
					<a href="javascript:void(0);" class="btn btn-eb-danger-o btn-nmm-delete" data-mm-delete>
						<i class="fa fa-trash"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DELETE_BUTTON');?>
					</a>
					
					<a href="javascript:void(0);" class="btn btn-eb-default-o btn-nmm-new-folder" data-mm-create-folder>
						<i class="fa fa-plus"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_MM_CREATE_FOLDER');?>
					</a>
				</div>

				<div class="eb-nmm-actionbar__cell eb-nmm-actionbar__cell--upload" data-mm-upload>
					<a href="javascript:void(0);" class="btn btn-eb-default-o btn-nmm-upload" data-plupload-browse-button>
						<i class="fa fa-upload"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_MM_UPLOAD');?>
					</a>
				</div>
				<?php } ?>
			</div>

			<div class="eb-nmm-main-body" data-dragzone-wrapper>
				<div class="o-loader"></div>

				<div class="eb-nmm-drag-zone t-hidden" data-dragzone>
					<div class="eb-nmm-drag-zone__title">
						<i class="fa fa-upload"></i>
						<div><?php echo JText::_('COM_EASYBLOG_MM_DROP_FILES_HERE');?></div>
					</div>
				</div>

				<div class="eb-nmm-info-panel" >
					<div class="eb-nmm-info-panel__mobile-bar">
						<div class="eb-nmm-info-panel__close">
							<a href="javascript:void(0);" data-mm-mobile-panel-hide>
								<i class="fa fa-chevron-right"></i>
							</a>
						</div>
						<div class="eb-nmm-info-panel__mobile-filename">
							<div data-mm-mobile-panel-title></div>
						</div>
					</div>
					<div class="eb-nmm-info-panel__hd">
						<div class="eb-nmm-info-panel__alert t-hidden" data-mm-info-state>
							<div class="o-alert o-alert--success o-alert--dismissible">
								<i class="fa fa-check-circle"></i>&nbsp; <strong><?php echo JText::_('COM_EASYBLOG_MM_CHANGES_SAVED');?></strong>
								<button type="button" class="o-alert__close" data-bp-dismiss="alert">
									<span aria-hidden="true">×</span>
								</button>
							</div>	
						</div>
					</div>

					<div class="eb-nmm-info-panel__bd" data-mm-info-panel data-uri>
					</div>

				</div>

				<div class="eb-nmm-content" data-mm-body>
					<div class="o-loader"></div>

					<div class="o-empty o-empty__flickr eb-nmm-content__empty">
						<div class="o-empty__content">
							<i class="o-empty__icon fa fa-flickr"></i>
							<div class="o-empty__text">
								<?php echo JText::_('COM_EASYBLOG_MM_FLICKR_NO_IMAGES');?>
							</div>
						</div>
					</div>

					<div class="o-empty o-empty__standard eb-nmm-content__empty">
						<div class="o-empty__content">
							<i class="o-empty__icon fa fa-hdd-o"></i>
							<div class="o-empty__text">
								<?php echo JText::_('COM_EASYBLOG_MM_NO_FILES_OR_FOLDERS');?>
									<?php if ($this->acl->get('upload_image')) { ?>
									<br /> 
									<?php echo JText::_('COM_EASYBLOG_MM_DROP_FILES_HERE_TO_UPLOAD');?>
								<?php } ?>
							</div>
						</div>
					</div>

					<div class="eb-nmm-group-listing <?php echo $this->isMobile() ? 'is-list' : ' is-grid';?>" data-mm-listing></div>

					<div class="eb-nmm-screens" data-mm-screens></div>
				</div>	
			</div>

			<div class="eb-nmm-main-footer" data-mm-footer>
				<div class="eb-nmm-main-footer__selection">
					<div class="eb-nmm-media-selection">
						<div class="eb-nmm-media-selection__info">
							<span class="eb-nmm-media-selection__counter" data-mm-selection-counter></span> <?php echo JText::_('COM_EASYBLOG_MM_SELECTED');?> &mdash; <a href="javascript:void(0);" data-mm-clear-selections><?php echo JText::_('COM_EASYBLOG_MM_CLEAR_SELECTIONS');?></a>
						</div>

						<div class="eb-nmm-media-selection__thumbs" data-mm-selection-list>
						</div>
					</div>	
				</div>
				<div class="eb-nmm-main-footer__action" data-mm-actions>
					<a href="javascript:void(0);" class="btn btn-eb-primary eb-nmm-main-footer__action-btn btn-nmm-gallery" data-mm-insert-gallery>
						<?php echo JText::_('COM_EASYBLOG_MM_INSERT_AS_GALLERY');?>
					</a>
					<a href="javascript:void(0);" class="btn btn-eb-primary eb-nmm-main-footer__action-btn btn-nmm-permalink" data-mm-insert-permalink>
						<?php echo JText::_('COM_EB_MM_INSERT_AS_PERMALINK');?>
					</a>					
					<a href="javascript:void(0);" class="btn btn-eb-primary eb-nmm-main-footer__action-btn btn-nmm-insert" data-mm-insert>
						<?php echo JText::_('COM_EASYBLOG_MM_INSERT');?>
					</a>
					<a href="javascript:void(0);" class="btn btn-eb-primary eb-nmm-main-footer__action-btn btn-nmm-select" data-mm-insert>
						<?php echo JText::_('COM_EASYBLOG_MM_SELECT');?>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="t-hidden" data-mm-selection-template>
		<div class="eb-nmm-media type-image ext-png" data-selection-wrapper>
			<div class="eb-nmm-media__cover">
				<div class="eb-nmm-media__embed">
					<div class="eb-nmm-media__embed-item" data-selection-cover></div>
				</div>
			</div>
		</div>
	</div>

	<div class="t-hidden" data-eb-mm-upload-template>
		<div class="eb-nmm-content-listing__item" data-eb--mm-file data-id="">
			<div class="eb-nmm-media" data-eb-mm-upload-type>
				<div class="eb-nmm-media__icon-wrapper">
					<i class=""></i>
				</div>

				<div class="eb-nmm-media__cover">
					<div class="eb-nmm-media__embed">
						<div class="eb-nmm-media__embed-item" data-eb-mm-upload-thumbnail></div>
					</div>

					<div class="eb-nmm-media__progress o-progress-radial" data-eb-mm-upload-progress-bar>
						<div class="eb-nmm-media__progress-overlay o-progress-radial__overlay" data-eb-mm-upload-progress-value></div>
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
</div>