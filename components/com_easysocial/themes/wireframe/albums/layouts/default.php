<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<?php if ($album->id && !$album->finalized && $this->my->id == $album->getCreator()->id) { ?>
<div class="o-alert o-alert--warning o-alert--icon" data-album-unfinalized-label>
	<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_UNFINALIZES_NOTICE_MESSAGE'); ?>
</div>
<?php } ?>

<div class="es-album-item es-media-group  es-island <?php echo $photos ? 'has-photos' : ''; ?> <?php echo 'layout-' . $options['layout']; ?>"
	data-album-item="<?php echo $album->uuid(); ?>"
	data-album-id="<?php echo $album->id; ?>"
	data-album-nextstart="<?php echo isset($nextStart) ? $nextStart : '-1' ; ?>"
	data-album-layout="<?php echo $options['layout']; ?>"
	data-album-uid="<?php echo $lib->uid;?>"
	data-album-type="<?php echo $lib->type;?>">

	<div data-album-header class="es-media-header es-album-header">
		<?php if ($options['showToolbar']) { ?>
		<div class="o-media es-album-header__o-media">
			<div class="o-media__image">
				<?php $albumCreator = $album->getCreator(); ?>
				<?php if ($albumCreator instanceof SocialPage) { ?>
				<?php echo $this->html('avatar.page', $albumCreator); ?>
				<?php } else {  ?>
				<?php echo $this->html('avatar.user', $albumCreator); ?>
				<?php } ?>
			</div>
			<div class="o-media__body">
				<div data-album-owner class="es-album-owner">
					<?php echo JText::_("COM_EASYSOCIAL_ALBUMS_UPLOADED_BY"); ?> <?php echo $this->html('html.user', $album->getCreator()); ?>
				</div>
				<?php echo $this->includeTemplate('site/albums/layouts/menu'); ?>
			</div>
		</div>
		<?php } ?>

		<?php echo $this->render('module', 'es-albums-before-info'); ?>

		<?php if ($options['showInfo']) { ?>
			<?php echo $this->includeTemplate('site/albums/layouts/info'); ?>
		<?php } ?>

		<?php if ($options['showForm'] && $lib->editable()) { ?>
			<?php echo $this->includeTemplate('site/albums/layouts/form'); ?>
		<?php } ?>
	</div>

	<div data-album-content class="es-album-content" data-es-photo-group="album:<?php echo $album->id; ?>">
		<?php echo $this->render('module', 'es-albums-before-photos'); ?>
		<?php if ($options['showPhotos']) { ?>
		<?php echo $this->includeTemplate('site/albums/layouts/photos'); ?>
		<?php } ?>
		<?php echo $this->render( 'module' , 'es-albums-after-photos' ); ?>
	</div>

	<?php if ($options['view'] != 'all') { ?>
	<div data-album-footer class="es-album-footer">
		<?php if ($options['showStats']) { ?>
			<?php echo $this->includeTemplate('site/albums/layouts/stats'); ?>
		<?php } ?>

		<div class="es-album-interaction o-row">

			<div class="es-album-showresponse o-col--8 o-col--top t-lg-pr--md t-xs-mb--lg t-xs-pr--no">
			<?php if ($options['showResponse']) { ?>
				<?php echo $this->includeTemplate('site/albums/layouts/response'); ?>
			<?php } ?>
			</div>

			<?php if($options['showTags']){ ?>
			<div class="es-album-showtag o-col--4 o-col--top">
				<?php echo $this->includeTemplate('site/albums/layouts/tags'); ?>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<?php if ($options['showForm'] && $lib->editable()) { ?>
	<div class="t-hidden" data-uploader-template>
		<div id="" data-wrapper class="es-photo-upload-item es-photo-item">
			<div>
				<div>
					<table>
						<tr class="upload-status">
							<td>
								<div class="upload-title">
									<span class="upload-title-pending"><?php echo JText::_('COM_EASYSOCIAL_UPLOAD_PENDING'); ?></span>
									<span class="upload-title-preparing"><?php echo JText::_('COM_EASYSOCIAL_UPLOAD_PREPARING'); ?></span>
									<span class="upload-title-uploading"><?php echo JText::_('COM_EASYSOCIAL_UPLOAD_UPLOADING'); ?></span>
									<span class="upload-title-failed"><?php echo JText::_('COM_EASYSOCIAL_UPLOAD_FAILED'); ?> <span class="upload-details-button" data-upload-failed-link>(<?php echo JText::_('COM_EASYSOCIAL_UPLOAD_SEE_DETAILS'); ?>)</span></span>
									<span class="upload-title-done"><?php echo JText::_('COM_EASYSOCIAL_UPLOAD_DONE'); ?></span>
								</div>

								<div class="upload-filename" data-file-name></div>

								<div class="upload-progress progress progress-striped active">
									<div class="upload-progress-bar bar progress-bar-info" style="width: 0%"><span class="upload-percentage"></span></div>
								</div>

								<div class="upload-filesize"><span class="upload-filesize-total"></span> (<span class="upload-filesize-left"></span> <?php echo JText::_('COM_EASYSOCIAL_UPLOAD_LEFT'); ?>)</div>

								<div class="upload-remove-button"><i class="fa fa-remove"></i></div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="es-media-loader"></div>
</div>

