<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_LISTING_TITLE', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_LISTING_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_SIZE', 'cover_size'); ?>

					<div class="col-md-7">
						<select name="cover_size" id="cover_size" class="form-control">
							<option value="small" <?php echo $this->config->get('cover_size') == 'small' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_SMALL');?></option>
							<option value="thumbnail" <?php echo $this->config->get('cover_size') == 'thumbnail' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_THUMBNAIL');?></option>
							<option value="medium" <?php echo $this->config->get('cover_size') == 'medium' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_MEDIUM');?></option>
							<option value="large" <?php echo $this->config->get('cover_size') == 'large' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_LARGE');?></option>
							<option value="original" <?php echo $this->config->get('cover_size') == 'original' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ORIGINAL');?></option>
						</select>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'cover_firstimage', 'COM_EASYBLOG_SETTINGS_POST_COVER_USE_FIRST_IMAGE'); ?>
				
				<?php echo $this->html('settings.toggle', 'cover_crop', 'COM_EASYBLOG_SETTINGS_POST_COVER_CROP_COVER', '', 'data-cover-crop'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_WIDTH', 'cover-width-full'); ?>

					<div class="col-md-7">
						<div class="checkbox" style="margin-top: 0;" data-cover-full-width-wrapper>
							<input type="checkbox" id="cover-width-full" value="1" name="cover_width_full" <?php echo $this->config->get('cover_width_full') ? ' checked="checked"' : '';?> data-cover-full-width />
							<label for="cover-width-full">
								<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_USE_FULL_WIDTH');?>
							</label>
						</div>

						<div class="row-table <?php echo $this->config->get('cover_width_full') ? 'hide' : '';?>" data-cover-width-input>
							<input type="text" class="form-control input-mini text-center" name="cover_width" value="<?php echo $this->config->get('cover_width', 260);?>" <?php echo $this->config->get('cover_width_full') ? ' disabled="disabled"' : '';?> data-cover-width />
							<span><?php echo JText::_('COM_EASYBLOG_PIXELS');?></span>
						</div>
					</div>
				</div>

				<div class="form-group<?php echo !$this->config->get('cover_crop') ? ' hide' : '';?>" data-cover-height>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_HEIGHT', 'cover_height'); ?>

					<div class="col-md-7">
						<input type="text" name="cover_height" id="cover_height" value="<?php echo $this->config->get('cover_height', 260);?>" class="form-control text-center input-mini" />
						<span><?php echo JText::_('COM_EASYBLOG_PIXELS');?></span>
					</div>
				</div>

				<div class="form-group<?php echo $this->config->get('cover_width_full') ? ' hide' : '' ?>" data-cover-alignment>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGNMENT', 'cover_alignment'); ?>

					<div class="col-md-7">
						<select name="cover_alignment" id="cover_alignment" class="form-control" style="width: 50%;">
							<option value="left"<?php echo $this->config->get('cover_alignment') == 'left' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_LEFT');?></option>
							<option value="right"<?php echo $this->config->get('cover_alignment') == 'right' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_RIGHT');?></option>
							<option value="center"<?php echo $this->config->get('cover_alignment') == 'center' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_CENTER');?></option>
							<option value="none"<?php echo $this->config->get('cover_alignment') == 'none' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_NONE');?></option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_ENTRY_TITLE', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_ENTRY_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_SIZE', 'cover_size_entry'); ?>

					<div class="col-md-7">
						<select name="cover_size_entry" id="cover_size_entry" class="form-control">
							<option value="small" <?php echo $this->config->get('cover_size_entry') == 'small' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_SMALL');?></option>
							<option value="thumbnail" <?php echo $this->config->get('cover_size_entry') == 'thumbnail' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_THUMBNAIL');?></option>
							<option value="medium" <?php echo $this->config->get('cover_size_entry') == 'medium' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_MEDIUM');?></option>
							<option value="large" <?php echo $this->config->get('cover_size_entry') == 'large' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_LARGE');?></option>
							<option value="original" <?php echo $this->config->get('cover_size_entry') == 'original' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ORIGINAL');?></option>
						</select>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'cover_crop_entry', 'COM_EASYBLOG_SETTINGS_POST_COVER_CROP_COVER', '', 'data-cover-crop-entry'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_WIDTH', 'cover-width-full-entry'); ?>

					<div class="col-md-7">
						<div class="checkbox" style="margin-top: 0;" data-cover-full-width-wrapper>
							<input type="checkbox" id="cover-width-full-entry" value="1" name="cover_width_entry_full" <?php echo $this->config->get('cover_width_entry_full') ? ' checked="checked"' : '';?> data-cover-full-width-entry />
							<label for="cover-width-full-entry">
								<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_USE_FULL_WIDTH');?>
							</label>
						</div>

						<div class="row-table <?php echo $this->config->get('cover_width_entry_full') ? 'hide' : '';?>" data-cover-width-input>
							<input type="text" class="form-control input-mini text-center" name="cover_width_entry" value="<?php echo $this->config->get('cover_width_entry', 260);?>" <?php echo $this->config->get('cover_width_entry_full') ? ' disabled="disabled"' : '';?> data-cover-width-entry />
							<span><?php echo JText::_('COM_EASYBLOG_PIXELS');?></span>
						</div>
					</div>
				</div>

				<div class="form-group<?php echo !$this->config->get('cover_crop_entry') ? ' hide' : '';?>" data-cover-height-entry>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_HEIGHT', 'cover_height_entry'); ?>

					<div class="col-md-7">
						<input type="text" class="form-control input-mini text-center" name="cover_height_entry" id="cover_height_entry" value="<?php echo $this->config->get('cover_height_entry', 260);?>" />
						<span><?php echo JText::_('COM_EASYBLOG_PIXELS');?></span>
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('cover_width_entry_full') ? 'hide' : '';?>" data-cover-alignment-entry>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGNMENT', 'cover_alignment_entry'); ?>

					<div class="col-md-7">
						<select name="cover_alignment_entry" id="cover_alignment_entry" class="form-control" style="width: 50%;">
							<option value="left"<?php echo $this->config->get('cover_alignment_entry') == 'left' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_LEFT');?></option>
							<option value="right"<?php echo $this->config->get('cover_alignment_entry') == 'right' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_RIGHT');?></option>
							<option value="center"<?php echo $this->config->get('cover_alignment_entry') == 'center' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_CENTER');?></option>
							<option value="none"<?php echo $this->config->get('cover_alignment_entry') == 'none' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_NONE');?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_FEATURED_TITLE', 'COM_EASYBLOG_SETTINGS_LAYOUT_COVER_FEATURED_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_SIZE', 'cover_featured_size'); ?>

					<div class="col-md-7">
						<select name="cover_featured_size" id="cover_featured_size" class="form-control">
							<option value="small" <?php echo $this->config->get('cover_featured_size') == 'small' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_SMALL');?></option>
							<option value="thumbnail" <?php echo $this->config->get('cover_featured_size') == 'thumbnail' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_THUMBNAIL');?></option>
							<option value="medium" <?php echo $this->config->get('cover_featured_size') == 'medium' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_MEDIUM');?></option>
							<option value="large" <?php echo $this->config->get('cover_featured_size') == 'large' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_LARGE');?></option>
							<option value="original" <?php echo $this->config->get('cover_featured_size') == 'original' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ORIGINAL');?></option>
						</select>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'cover_featured_crop', 'COM_EASYBLOG_SETTINGS_POST_COVER_CROP_COVER', '', 'data-cover-featured-crop'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_WIDTH', 'cover_featured_size'); ?>

					<div class="col-md-7">
						<input type="text" class="form-control input-mini text-center" name="cover_featured_width" id="cover_featured_width" value="<?php echo $this->config->get('cover_featured_width', 200);?>" data-cover-featured-width />
						<span><?php echo JText::_('COM_EASYBLOG_PIXELS');?></span>
					</div>
				</div>

				<div class="form-group<?php echo !$this->config->get('cover_featured_crop') ? ' hide' : '';?>" data-cover-featured-height>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_HEIGHT', 'cover_featured_height'); ?>

					<div class="col-md-7">
						<input type="text" class="form-control input-mini text-center" name="cover_featured_height" id="cover_featured_height" value="<?php echo $this->config->get('cover_featured_height', 200);?>" />
						<span><?php echo JText::_('COM_EASYBLOG_PIXELS');?></span>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_POST_COVER_ALIGNMENT', 'cover_featured_alignment'); ?>

					<div class="col-md-7">
						<select name="cover_featured_alignment" id="cover_featured_alignment" class="form-control" style="width: 50%;">
							<option value="left"<?php echo $this->config->get('cover_featured_alignment') == 'left' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_LEFT');?></option>
							<option value="right"<?php echo $this->config->get('cover_featured_alignment') == 'right' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_RIGHT');?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
