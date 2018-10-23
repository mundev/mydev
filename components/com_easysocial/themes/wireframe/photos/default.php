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
<div class="wrapper-for-full-height">

<?php if (!isset($heading)) { ?>
	<?php echo $lib->heading(); ?>
<?php } ?>

<?php echo $this->html('responsive.toggle'); ?>

<div data-photo-browser="<?php echo $uuid; ?>" data-album-id="<?php echo $album->id; ?>" class="es-container es-photo-browser es-media-browser" data-es-container>
	<div data-photo-browser-sidebar class="es-sidebar" data-sidebar>
		<?php echo $this->render('module', 'es-photos-sidebar-top'); ?>

		<div class="es-side-widget">
			<div class="es-side-widget__hd">
				<a href="<?php echo $lib->getAlbumLink(); ?>" class="btn btn-es-default-o btn-block" data-photo-back-button>
					<i class="fa fa-caret-left"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_PHOTOS_BACK_TO_ALBUM'); ?>
				</a>
			</div>

			<hr class="es-hr" />

			<div class="es-side-widget__bd">
				<ul class="es-nav-thumbs" data-photo-list-item-group>
					<li class="es-thumb grid-sizer">
						<a></a>
					</li>
					
					<?php echo $this->output('site/photos/sidebar.photos', array('photos' => $photos, 'id' => $id)); ?>
				</ul>

				<?php if ($total > $limit) { ?>
				<div class="t-lg-mt--md">
					<a href="javascript:void(0);" class="btn btn-es-primary btn-block btn-sm" data-es-photos-loadmore data-id="<?php echo $album->id;?>">
						<?php echo JText::_('COM_EASYSOCIAL_LOAD_MORE');?>
						<div class="o-loader o-loader--sm"></div>	
					</a>
				</div>
				<?php } ?>
			</div>
		</div>

		<?php echo $this->render('module', 'es-photos-sidebar-bottom'); ?>
	</div>
	<div class="es-content" data-photo-browser-content>
		<?php echo $this->render('module', 'es-photos-before-contents'); ?>
		<?php echo $content; ?>
		<?php echo $this->render('module', 'es-photos-after-contents'); ?>
	</div>

</div>

</div>