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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_CONTENT', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_CONTENT_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_ENABLE', 'composer_truncation_enabled'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'composer_truncation_enabled', $this->config->get('composer_truncation_enabled')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_TRUNCATION_COMPOSER_DISPLAY_READMORE_WHEN_NECESSARY', 'composer_truncation_readmore'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'composer_truncation_readmore', $this->config->get('composer_truncation_readmore'));?>
					</div>
				</div>

				<?php $mediaTypes = array('image', 'video', 'audio', 'gallery'); ?>

				<?php foreach ($mediaTypes as $media) { ?>
					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_POSITIONS', 'composer_truncate_' . $media . '_position'); ?>

						<div class="col-md-7">
							<select name="composer_truncate_<?php echo $media; ?>_position" id="composer_truncate_<?php echo $media; ?>_position" class="form-control" data-composer-truncate-option="<?php echo $media;?>">
								<option value="top"<?php echo $this->config->get('composer_truncate_' . $media  . '_position' ) == 'top' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_TOP_OPTION'); ?></option>
								<option value="bottom"<?php echo $this->config->get('composer_truncate_' . $media  . '_position' ) == 'bottom' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_BOTTOM_OPTION');?></option>
								<option value="hidden"<?php echo $this->config->get('composer_truncate_' . $media  . '_position' ) == 'hidden' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_DO_NOT_SHOW_OPTION');?></option>
							</select>
						</div>
					</div>

					<?php if ($media != 'gallery') { ?>
					<div data-composer-truncate-items-<?php echo $media;?> class="form-group <?php echo $this->config->get('composer_truncate_' . $media . '_position') == 'hidden' ? 'hide' : '';?>">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_LIMITS', 'composer_truncate_' . $media . '_limit'); ?>

						<div class="col-md-7">
							<input type="text" name="composer_truncate_<?php echo $media; ?>_limit" id="composer_truncate_<?php echo $media; ?>_limit"
								class="form-control input-mini text-center" value="<?php echo $this->config->get('composer_truncate_'.$media.'_limit' , '0');?>" />
						</div>
					</div>
					<?php } ?>

				<?php } ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_CONTENT', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_CONTENT_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_TRUNCATION_COMPOSER_MAX_CHARS', 'composer_truncation_chars'); ?>

					<div class="col-md-7">
						<input type="text" name="composer_truncation_chars" id="composer_truncation_chars" class="input-mini form-control text-center" value="<?php echo $this->config->get('composer_truncation_chars' , '350');?>" />
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_NORMAL_CONTENT', 'COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_NORMAL_CONTENT_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_TYPE', 'main_truncate_type'); ?>

					<div class="col-md-7">
						<select name="main_truncate_type" id="main_truncate_type" class="form-control" data-truncate-type>
							<option value="chars"<?php echo $this->config->get( 'main_truncate_type' ) == 'chars' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_CHARACTERS' ); ?></option>
							<option value="words"<?php echo $this->config->get( 'main_truncate_type' ) == 'words' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_WORDS' ); ?></option>
							<option value="paragraph"<?php echo $this->config->get( 'main_truncate_type' ) == 'paragraph' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_PARAGRAPH' ); ?></option>
							<option value="break"<?php echo $this->config->get( 'main_truncate_type' ) == 'break' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_BREAK' );?></option>
						</select>
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('main_truncate_type') == 'chars' || $this->config->get('main_truncate_type') == 'words' ? '' : 'hide';?>" data-max-chars>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_OF_BLOG_CONTENT_AS_INTROTEXT', 'layout_maxlengthasintrotext'); ?>

					<div class="col-md-7">
						<input type="text" name="layout_maxlengthasintrotext" id="layout_maxlengthasintrotext" class="input-mini form-control text-center" value="<?php echo $this->config->get('layout_maxlengthasintrotext' , '150');?>" />
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('main_truncate_type') == 'break' || $this->config->get('main_truncate_type') == 'paragraph' ? '' : 'hide';?>" data-max-tag>
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_TAGS', 'main_truncate_maxtag'); ?>

					<div class="col-md-7">
						<input type="text" name="main_truncate_maxtag" id="main_truncate_maxtag" class="input-mini form-control text-center" value="<?php echo $this->config->get('main_truncate_maxtag' , '150');?>" />
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
