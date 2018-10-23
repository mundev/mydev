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
<div class="eb-composer-fieldset eb-image-url-fieldset" data-eb-image-url-fieldset data-name="image-url">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_URL'); ?></strong>
	</div>
	
	<div class="eb-composer-fieldset-content">
		<div class="o-form-group eb-image-url-field" data-eb-image-url-field>
			<div style="margin: 0 auto;" class="o-input-group">
				<input type="text" value="" class="o-form-control" data-eb-image-url-field-text />
				<span class="o-input-group__btn">
					<a href="javascript:void(0);" class="btn btn-eb-default-o" data-eb-image-url-field-update-button><?php echo JText::_('COM_EASYBLOG_UPDATE_BUTTON'); ?></a>
				</span>
			</div>
		</div>
	</div>
</div>


<div class="eb-composer-fieldset eb-image-source-fieldset" data-eb-image-source-fieldset data-name="image-source">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_SOURCE'); ?></strong>
	</div>
	<div class="eb-composer-fieldset-content">
		<div class="o-form-group o-form-group--eb-style-bordered eb-image-source-field" data-eb-image-source-field>
			<div class="o-form-group eb-image-source-header">
				<div class="eb-image-source-thumbnail" data-eb-image-source-thumbnail></div>
				<div class="row-table">
					<div class="col-cell cell-ellipse eb-image-source-info">
						<div class="row-table">
							<div class="col-cell cell-ellipse eb-image-source-title" data-eb-image-source-title></div>
							<div class="col-cell cell-tight eb-image-source-size" data-eb-image-source-size></div>
						</div>
						<div class="eb-image-source-url" data-eb-image-source-url></div>
					</div>
					<div class="col-cell cell-tight">
						<button type="button" class="btn btn--sm btn-eb-default-o eb-image-source-change-button"
							data-eb-image-source-change-button
							data-eb-mm-browse-button
							data-eb-mm-start-uri="_cG9zdA--"
							data-eb-mm-filter="image"
						><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CHANGE'); ?></button>
					</div>
				</div>
			</div>

			<div class="o-form-group eb-image-variation-field can-create can-delete" data-eb-image-variation-field>
				<div class="eb-image-variation-list-container" data-eb-image-variation-list-container></div>
				<div class="eb-image-variation-create-container o-form-horizontal">
					<div class="">
						<div class="o-form-group">
							<div class="o-control-label"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_NAME'); ?></div>
							<div class="o-control-input">
								<input type="text" class="o-form-control eb-image-variation-name" data-eb-image-variation-name placeholder="<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_NAME_PLACEHOLDER'); ?>">
							</div>
						</div>
					</div>
					<div class=" eb-image-variation-size-field">
						<div class="o-form-group">
							<div class="o-control-label"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_WIDTH'); ?></div>
							<div class="o-control-input"><input type="text" class="o-form-control eb-image-variation-width" data-eb-image-variation-width></div>
						</div>
						<div class="o-form-group">
							<div class="o-control-label"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_HEIGHT'); ?></div>
							<div class="o-control-input"><input type="text" class="o-form-control eb-image-variation-height" data-eb-image-variation-height></div>
						</div>

					</div>
				</div>
				<div class="eb-image-variation-action">
					<button type="button" class="eb-image-variation-new-button btn btn--sm btn-eb-primary-o" data-eb-image-variation-new-button>
						<i class="fa fa-plus-circle"></i> <?php echo JText::_('COM_EASYBLOG_MM_NEW_SIZE'); ?>
					</button>
					
					<button type="button" class="eb-image-variation-rebuild-button btn btn--sm btn-eb-default-o" data-eb-image-variation-rebuild-button>
						<i class="fa fa-undo"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_REBUILD'); ?>
					</button>

					<button type="button" class="eb-image-variation-delete-button btn btn--sm btn-eb-default-o" data-eb-image-variation-delete-button>
						<i class="fa fa-trash"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_DELETE'); ?>
					</button>

					<button type="button" class="eb-image-variation-cancel-button btn btn--sm btn-eb-default-o" data-eb-image-variation-cancel-button>
						<i class="fa fa-close"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CANCEL'); ?>
					</button>
					<button type="button" class="eb-image-variation-create-button btn btn--sm btn-eb-primary-o" data-eb-image-variation-create-button>
						<i class="fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CREATE'); ?>
					</button>
				</div>

				<div class="eb-hint hint-creating-variation layout-overlay style-gray size-sm">
					<div>
						<i class="eb-hint-icon"><span class="eb-loader-o size-sm"></span></i>
						<span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CREATING_IMAGE_SIZE'); ?></span>
					</div>
				</div>

				<div class="eb-hint hint-failed-variation layout-overlay style-gray size-sm">
					<div>
						<i class="eb-hint-icon fa fa-warning"></i>
						<span class="eb-hint-text">
							<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CREATING_IMAGE_SIZE_ERROR'); ?>
							<span class="eb-image-source-failed-action">
								<button type="button" class="btn btn-eb-primary btn--sm " data-eb-image-variation-cancel-failed-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CANCEL'); ?></button>
							</span>
						</span>
					</div>
				</div>
			</div>

			<div class="eb-hint hint-loading layout-overlay style-gray size-sm">
				<div>
					<i class="eb-hint-icon"><span class="eb-loader-o size-sm"></span></i>
					<span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_LOADING_VARIATIONS'); ?></span>
				</div>
			</div>

			<div class="eb-hint hint-failed layout-overlay style-gray size-sm">
				<div>
					<i class="eb-hint-icon fa fa-warning"></i>
					<span class="eb-hint-text">
						<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_LOADING_VARIATIONS_ERROR'); ?>
						<span class="eb-image-source-failed-action">
							<button type="button" class="btn btn-sm btn-default" data-eb-image-source-change-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CHANGE_IMAGE'); ?></button>
							<button type="button" class="btn btn-sm btn-primary" data-eb-image-source-retry-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_RETRY'); ?></button>
						</span>
					</span>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-size-fieldset" data-eb-image-fieldset data-eb-image-fieldset-size data-type="simple">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_SIZE'); ?></strong>
	</div>

	<div class="o-loader"></div>
	
	<div class="eb-composer-fieldset-content">
		<div class="o-grid o-grid--gutters">
			<div class="o-grid__cell">
				<div class="eb-image-size-input">
					<div class="eb-image-size-input__field">
						<label for="" class="eb-image-size-input__label">
							<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_WIDTH');?>
						</label>
						<div class="eb-image-size-input__input">
							<input type="text" name="image-width" class="o-form-control" data-eb-image-width />
						</div>
						
					</div>
					<div class="eb-image-size-input__unit">px</div>
				</div>
			</div>
			<div class="o-grid__cell">
				<div class="eb-image-size-input">
					<div class="eb-image-size-input__field">
						<label for="" class="eb-image-size-input__label">
							<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_HEIGHT');?>
						</label>
						<div class="eb-image-size-input__input">
							<input type="text" name="image-height" class="o-form-control" data-eb-image-height />
						</div>
						
					</div>
					<div class="eb-image-size-input__unit">px</div>
				</div>
			</div>
			<div class="o-grid__cell o-grid__cell--auto-size">
				<?php echo $this->html('composer.checkbox', 'image-lock-ratio', '<i class="fa fa-lock"></i>', true, array('data-eb-image-lock-ratio')); ?>
			</div>
		</div>
		
		<?php echo $this->html('composer.checkbox', 'image-responsive-option', 'COM_EASYBLOG_EXPAND_TO_FULL_WIDTH_ON_MOBILE', true, array('data-eb-image-responsive')); ?>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-alignment-fieldset" data-eb-image-fieldset data-type="simple" data-eb-image-alignment>
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_ALIGNMENT'); ?></strong>
	</div>

	<div class="eb-composer-fieldset-content">
		<select class="o-form-control eb-composer-fieldrow-select" data-eb-image-alignment-selection>
			<option value="left"><?php echo JText::_('COM_EASYBLOG_COMPOSER_ALIGNMENT_LEFT');?></option>
			<option value="center" selected=""><?php echo JText::_('COM_EASYBLOG_COMPOSER_ALIGNMENT_CENTER');?></option>
			<option value="right"><?php echo JText::_('COM_EASYBLOG_COMPOSER_ALIGNMENT_RIGHT');?></option>
		</select>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-caption-fieldset"  data-eb-image-fieldset data-eb-image-caption-fieldset data-name="image-caption">
	<label class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CAPTION'); ?></strong>
	</label>
	<div class="eb-composer-fieldset-content">
		<textarea class="o-form-control eb-image-caption-text-field" placeholder="<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_ENTER_CAPTION_HERE', true);?>" data-eb-image-caption-text-field></textarea>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-alt-fieldset"  data-eb-image-fieldset data-type="standard" data-eb-image-alt-fieldset data-name="image-alt">
	<label class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_ALT'); ?></strong>
	</label>
	<div class="eb-composer-fieldset-content">
		<textarea class="o-form-control eb-image-alt-text-field" placeholder="<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_ENTER_ALT_HERE', true);?>" data-eb-image-alt-text-field></textarea>
	</div>
</div>

<div class="eb-composer-fieldset eb-image-link-fieldset" data-eb-image-fieldset data-type="simple">
	<label class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_LINK'); ?></strong>
	</label>
	<div class="eb-composer-fieldset-content">
		<select class="o-form-control" data-eb-image-link>
			<option value="none"><?php echo JText::_('COM_EASYBLOG_MM_IMAGE_LINK_NONE');?></option>
			<option value="lightbox"><?php echo JText::_('COM_EASYBLOG_MM_IMAGE_LINK_POPUP');?></option>
			<option value="custom"><?php echo JText::_('COM_EASYBLOG_MM_IMAGE_LINK_CUSTOM_URL_SAME_WINDOW');?></option>
			<option value="custom_new"><?php echo JText::_('COM_EASYBLOG_MM_IMAGE_LINK_CUSTOM_URL_NEW_WINDOW');?></option>
		</select>

		<input type="text" name="popup_url" class="o-form-control t-lg-mt--md t-hidden" placeholder="http://site.com" data-eb-image-link-url />
	</div>
</div>

<div class="eb-composer-fieldset eb-image-style-fieldset" data-eb-image-fieldset data-type="simple" data-eb-image-style-fieldset data-name="image-style">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE'); ?></strong>
	</div>
	<div class="eb-composer-fieldset-content">
		<div class="">
			<div class="eb-swatch swatch-grid">
				<div class="row">
					<div class="col-xs-4">
						<div class="eb-swatch-item" data-eb-image-style-selection data-value="clear">
							<div class="eb-swatch-preview is-responsive">
								<div>
									<div class="eb-image style-simple">
										<div class="eb-image-figure"><div></div></div>
										<div class="eb-image-caption"><span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CAPTION_PREVIEW');?></span></div>
									</div>
								</div>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_CLEAR');?></span>
							</div>
						</div>
					</div>
					<div class="col-xs-4">
						<div class="eb-swatch-item" data-eb-image-style-selection data-value="gray">
							<div class="eb-swatch-preview is-responsive">
								<div>
									<div class="eb-image style-gray">
										<div class="eb-image-figure"><div></div></div>
										<div class="eb-image-caption"><span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CAPTION_PREVIEW');?></span></div>
									</div>
								</div>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_GRAY');?></span>
							</div>
						</div>
					</div>
					<div class="col-xs-4">
						<div class="eb-swatch-item" data-eb-image-style-selection data-value="polaroid">
							<div class="eb-swatch-preview is-responsive">
								<div>
									<div class="eb-image style-polaroid">
										<div class="eb-image-figure"><div></div></div>
										<div class="eb-image-caption"><span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CAPTION_PREVIEW');?></span></div>
									</div>
								</div>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_POLAROID');?></span>
							</div>
						</div>
					</div>

					<div class="col-xs-4">
						<div class="eb-swatch-item" data-eb-image-style-selection data-value="solid">
							<div class="eb-swatch-preview is-responsive">
								<div>
									<div class="eb-image style-solid">
										<div class="eb-image-figure"><div></div></div>
										<div class="eb-image-caption"><span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CAPTION_PREVIEW');?></span></div>
									</div>
								</div>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_SOLID');?></span>
							</div>
						</div>
					</div>

					<div class="col-xs-4">
						<div class="eb-swatch-item" data-eb-image-style-selection data-value="dashed">
							<div class="eb-swatch-preview is-responsive">
								<div>
									<div class="eb-image style-dashed">
										<div class="eb-image-figure"><div></div></div>
										<div class="eb-image-caption"><span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CAPTION_PREVIEW');?></span></div>
									</div>
								</div>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_DASHED');?></span>
							</div>
						</div>
					</div>

					<div class="col-xs-4">
						<div class="eb-swatch-item" data-eb-image-style-selection data-value="dotted">
							<div class="eb-swatch-preview is-responsive">
								<div>
									<div class="eb-image style-dotted">
										<div class="eb-image-figure"><div></div></div>
										<div class="eb-image-caption"><span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CAPTION_PREVIEW');?></span></div>
									</div>
								</div>
							</div>
							<div class="eb-swatch-label">
								<span><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_DOTTED');?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
