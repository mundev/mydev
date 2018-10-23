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
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_MEDIA_MANAGER'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_media_manager_place_shared_media', 'COM_EASYBLOG_SETTINGS_MEDIA_ENABLE_SHARED_MEDIA'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_MEDIA_ALLOWED_EXTENSIONS', 'main_media_extensions'); ?>

					<div class="col-md-7">
						<div class="input-group">
							<input type="text" class="form-control" value="<?php echo $this->config->get('main_media_extensions');?>" id="media_extensions" name="main_media_extensions" data-media-extensions />
							<span class="input-group-btn">
								<button type="button" class="btn btn-default" data-reset-extensions><?php echo JText::_('COM_EASYBLOG_RESET_DEFAULT');?></button>
							</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_MAX_FILESIZE', 'main_upload_image_size'); ?>

					<div class="col-md-7">
						<div class="row">
							<div class="col-sm-6">
								<div class="input-group">
									<input type="text" name="main_upload_image_size" class="form-control text-center" value="<?php echo $this->config->get('main_upload_image_size', '0' );?>" />
									<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_MEGABYTES');?></span>
								</div>
							</div>
						</div>


						<div><?php echo JText::sprintf( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_UPLOAD_PHP_MAXSIZE' , ini_get( 'upload_max_filesize') ); ?></div>
						<div><?php echo JText::sprintf( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_UPLOAD_PHP_POSTMAXSIZE' , ini_get( 'post_max_size') ); ?></div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_MEDIA_QUALITY', 'main_image_quality'); ?>

					<div class="col-md-7">
						<?php
							$options = array();

							for ($i = 0; $i <= 100; $i += 10) {
								$message = $i;
								$message = $i == 0 ? JText::sprintf( 'COM_EASYBLOG_LOWEST_QUALITY_OPTION' , $i ) : $message;
								$message = $i == 50 ? JText::sprintf( 'COM_EASYBLOG_MEDIUM_QUALITY_OPTION' , $i ) : $message;
								$message = $i == 100 ? JText::sprintf( 'COM_EASYBLOG_HIGHEST_QUALITY_OPTION' , $i ) : $message;
								$options[] = JHTML::_('select.option', $i , $message );
							}

							echo JHTML::_('select.genericlist', $options, 'main_image_quality', 'class="form-control"', 'value', 'text', $this->config->get('main_image_quality' ) );
						?>
						<div class="help-block">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_IMAGE_UPLOAD_QUALITY_HINTS' );?>
						</div>
					</div>
				</div>

				<?php echo $this->html('settings.toggle', 'main_media_relative_path', 'COM_EASYBLOG_SETTINGS_MEDIA_USE_RELATIVE_PATH'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EB_SETTINGS_MEDIA_DEFAULT_VARIATION', 'main_default_variation'); ?>

					<div class="col-md-7">
						<select name="main_media_variation" class="form-control">
							<option value="system/large"<?php echo $this->config->get('main_media_variation') == 'system/large' ? ' selected="selected"' : '';?>><?php echo JText::_('Large'); ?></option>
							<option value="system/original"<?php echo $this->config->get('main_media_variation') == 'system/original' ? ' selected="selected"' : '';?>><?php echo JText::_('Original'); ?></option>
						</select>
					</div>
				</div>

			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_MEDIA_STORAGE_TITLE', 'COM_EASYBLOG_SETTINGS_MEDIA_STORAGE_INFO'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.text', 'main_articles_path', 'COM_EASYBLOG_SETTINGS_MEDIA_ARTICLE_PATH'); ?>

				<?php echo $this->html('settings.text', 'main_image_path', 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_PATH'); ?>

				<?php echo $this->html('settings.text', 'main_shared_path', 'COM_EASYBLOG_SETTINGS_MEDIA_SHARED_PATH'); ?>

				<?php echo $this->html('settings.text', 'main_avatarpath', 'COM_EASYBLOG_SETTINGS_MEDIA_AVATAR_PATH'); ?>

				<?php echo $this->html('settings.text', 'main_categoryavatarpath', 'COM_EASYBLOG_SETTINGS_MEDIA_CATEGORY_PATH'); ?>

				<?php echo $this->html('settings.text', 'main_teamavatarpath', 'COM_EASYBLOG_SETTINGS_MEDIA_TEAMBLOG_PATH'); ?>

			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_MEDIA_VIDEOS_TITLE'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.text', 'max_video_width', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH', '', array('postfix' => 'COM_EASYBLOG_PIXELS', 'size' => 5), '', 'text-center'); ?>

				<?php echo $this->html('settings.text', 'max_video_height', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT', '', array('postfix' => 'COM_EASYBLOG_PIXELS', 'size' => 5), '', 'text-center'); ?>

			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_MEDIA_ORIGINAL_IMAGE_TITLE', 'COM_EASYBLOG_SETTINGS_MEDIA_ORIGINAL_IMAGE_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_resize_original_image', 'COM_EASYBLOG_SETTINGS_MEDIA_RESIZE_ORIGINAL_IMAGE'); ?>

				<?php echo $this->html('settings.text', 'main_original_image_width', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH', '', array('postfix' => 'COM_EASYBLOG_PIXELS', 'size' => 5), '', 'text-center'); ?>

				<?php echo $this->html('settings.text', 'main_original_image_height', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT', '', array('postfix' => 'COM_EASYBLOG_PIXELS', 'size' => 5), '', 'text-center'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_MEDIA_QUALITY', 'main_original_image_quality'); ?>

					<div class="col-md-7">
						<?php
							$options = array();

							for( $i = 0; $i <= 100; $i += 10 )
							{
								$message	= $i;
								$message	= $i == 0 ? JText::sprintf( 'COM_EASYBLOG_LOWEST_QUALITY_OPTION' , $i ) : $message;
								$message	= $i == 50 ? JText::sprintf( 'COM_EASYBLOG_MEDIUM_QUALITY_OPTION' , $i ) : $message;
								$message	= $i == 100 ? JText::sprintf( 'COM_EASYBLOG_HIGHEST_QUALITY_OPTION' , $i ) : $message;
								$options[]	= JHTML::_('select.option', $i , $message );
							}

							echo JHTML::_('select.genericlist', $options, 'main_original_image_quality', 'class="form-control"', 'value', 'text', $this->config->get('main_original_image_quality' ) );
						?>
						<div class="help-block">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_IMAGE_UPLOAD_QUALITY_HINTS' );?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_SETTINGS_MEDIA_THUMBNAILS_TITLE', 'COM_EASYBLOG_SETTINGS_MEDIA_THUMBNAILS_DESC'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.text', 'main_image_thumbnail_width', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH', '', array('postfix' => 'COM_EASYBLOG_PIXELS', 'size' => 5), '', 'text-center'); ?>

				<?php echo $this->html('settings.text', 'main_image_thumbnail_height', 'COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT', '', array('postfix' => 'COM_EASYBLOG_PIXELS', 'size' => 5), '', 'text-center'); ?>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_SETTINGS_MEDIA_QUALITY', 'main_image_thumbnail_quality'); ?>

					<div class="col-md-7">
						<?php
							$options = array();

							for( $i = 0; $i <= 100; $i += 10 )
							{
								$message	= $i;
								$message	= $i == 0 ? JText::sprintf( 'COM_EASYBLOG_LOWEST_QUALITY_OPTION' , $i ) : $message;
								$message	= $i == 50 ? JText::sprintf( 'COM_EASYBLOG_MEDIUM_QUALITY_OPTION' , $i ) : $message;
								$message	= $i == 100 ? JText::sprintf( 'COM_EASYBLOG_HIGHEST_QUALITY_OPTION' , $i ) : $message;
								$options[]	= JHTML::_('select.option', $i , $message );
							}

							echo JHTML::_('select.genericlist', $options, 'main_image_thumbnail_quality', 'class="form-control"', 'value', 'text', $this->config->get('main_image_thumbnail_quality' ) );
						?>
						<div class="help-block">
						<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_IMAGE_UPLOAD_QUALITY_HINTS' );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
