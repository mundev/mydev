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
<div class="eb-nmm-info-panel__viewport">
	<div class="eb-nmm-info-panel__content">
		<form data-mm-form>
			<div class="eb-nmm-image-preview is-image" data-mm-info-preview>
				<div style="background-image:url(<?php echo $preview;?>);"></div>
			</div>

			<div class="eb-nmm-panel-block">
				<?php echo $this->html('media.field', 'media.textbox', 'title', 'COM_EASYBLOG_IMAGE_MANAGER_TITLE', $file->title, 'data-mm-panel-title data-mm-panel-input'); ?>

				<?php if ($this->config->get('layout_editor') != 'composer' || $isLegacyPost) { ?>
					<?php echo $this->html('media.field', 'media.textbox', 'width', 'COM_EASYBLOG_MM_WIDTH', $params->get('width', $preferredVariation->width), 'data-mm-image-width data-mm-panel-input'); ?>
					<?php echo $this->html('media.field', 'media.textbox', 'height', 'COM_EASYBLOG_MM_HEIGHT', $params->get('height', $preferredVariation->height), 'data-mm-image-height data-mm-panel-input'); ?>
				<?php } ?>

				<?php echo $this->html('media.field', 'media.textarea', 'caption_text', 'COM_EASYBLOG_MM_CAPTION', $params->get('caption_text', ''), 'data-mm-panel-input', JText::_('COM_EASYBLOG_MM_CAPTIONS_PLACEHOLDER')); ?>

				<?php echo $this->html('media.field', 'media.textbox', 'alt_text', 'COM_EASYBLOG_MM_ALTERNATE_TEXT', $params->get('alt_text', ''), 'data-mm-panel-input', JText::_('COM_EASYBLOG_MM_ALT_PLACEHOLDER')); ?>



				<div class="o-form-group">
					<label class="o-control-label" for="mm-preview-size"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_SOURCE');?></label>
					<select name="variation" id="mm-preview-size" class="o-form-control input-sm" data-mm-variation data-mm-panel-input>
						<?php foreach ($variations as $variation) { ?>
						<option value="<?php echo $variation->key;?>" <?php echo $preferredVariation->key == $variation->key ? 'selected="selected"' : '';?>
							data-url="<?php echo $variation->url;?>"
							data-width="<?php echo $variation->width;?>"
							data-height="<?php echo $variation->height;?>"
						>
							<?php echo ucfirst($variation->name);?> &ndash; (<?php echo $variation->width;?> x <?php echo $variation->height;?>)
						</option>
						<?php } ?>
					</select>
				</div>

				<div class="o-form-group">
					<label class="o-control-label" for="mm-preview-alt"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE');?></label>
					<select name="style" class="o-form-control input-sm" data-mm-panel-input>
						<option value="clear" <?php echo $params->get('style', 'clear') == 'clear' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_CLEAR');?></option>
						<option value="gray" <?php echo $params->get('style', 'clear') == 'gray' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_GRAY');?></option>
						<option value="polaroid" <?php echo $params->get('style', 'clear') == 'polaroid' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_POLAROID');?></option>
						<option value="solid" <?php echo $params->get('style', 'clear') == 'solid' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_SOLID');?></option>
						<option value="dashed" <?php echo $params->get('style', 'clear') == 'dashed' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_DASHED');?></option>
						<option value="dotted" <?php echo $params->get('style', 'clear') == 'dotted' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_STYLE_DOTTED');?></option>
					</select>
				</div>

				<div class="o-form-group">
					<label class="o-control-label" for="mm-preview-alt"><?php echo JText::_('COM_EASYBLOG_COMPOSER_ALIGNMENT');?></label>
					<select name="alignment" class="o-form-control input-sm" data-mm-panel-input>
						<option value="left" <?php echo $params->get('alignment', 'center') == 'left' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_COMPOSER_ALIGNMENT_LEFT');?></option>
						<option value="center" <?php echo $params->get('alignment', 'center') == 'center' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_COMPOSER_ALIGNMENT_CENTER');?></option>
						<option value="right" <?php echo $params->get('alignment', 'center') == 'right' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_COMPOSER_ALIGNMENT_RIGHT');?></option>
					</select>
				</div>
				<div class="o-form-group">
					<label class="o-control-label" for="mm-preview-alt"><?php echo JText::_('COM_EASYBLOG_MM_LINK_TO');?></label>
					<select name="link" class="o-form-control input-sm" data-mm-preview-link data-mm-panel-input>
						<option value="none" selected="selected"><?php echo JText::_('COM_EASYBLOG_MM_IMAGE_LINK_NONE');?></option>
						<option value="lightbox"><?php echo JText::_('COM_EASYBLOG_MM_IMAGE_LINK_POPUP');?></option>
						<option value="custom"><?php echo JText::_('COM_EASYBLOG_MM_IMAGE_LINK_CUSTOM_URL_SAME_WINDOW');?></option>
						<option value="custom_new"><?php echo JText::_('COM_EASYBLOG_MM_IMAGE_LINK_CUSTOM_URL_NEW_WINDOW');?></option>
					</select>

					<input type="text" name="link_url" class="o-form-control input-sm t-lg-mt--md t-hidden" placeholder="http://site.com" data-mm-preview-custom-url/>
				</div>
			</div>

			<input type="hidden" name="ratio" value="<?php echo $preferredVariation->width / $preferredVariation->height;?>" data-mm-image-ratio />
			<input type="hidden" name="natural_ratio" value="<?php echo $preferredVariation->width / $preferredVariation->height;?>" data-mm-image-ratio-natural />
			<input type="hidden" name="url" value="<?php echo $preferredVariation->url;?>" data-mm-image-url />
			<input type="hidden" name="uri" value="<?php echo $file->uri;?>" />
			<input type="hidden" name="ratio_lock" value="1" />
		</form>
	</div>
</div>
