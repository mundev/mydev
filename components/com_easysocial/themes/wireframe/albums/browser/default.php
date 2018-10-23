<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="wrapper-for-full-height">

	<?php echo $lib->heading();?>

	<?php if ($this->isMobile() && $cluster) { ?>
	<a class="btn btn-es-default-o btn-sm t-lg-mb--lg" href="<?php echo $cluster->getPermalink();?>">&larr; <?php echo JText::sprintf('COM_EASYSOCIAL_BACK_TO_' . strtoupper($cluster->getType()));?></a>
	<?php } ?>	

	<?php echo $this->html('responsive.toggle'); ?>

	<div class="es-container es-media-browser layout-album" data-layout="album" data-album-browser="<?php echo $uuid; ?>" data-es-container>
		<?php echo $this->html('html.loading'); ?>

		<div class="es-sidebar" data-album-browser-sidebar data-sidebar>

			<?php echo $this->render('module', 'es-albums-sidebar-top'); ?>

			<?php if ($lib->canCreateAlbums() && !$lib->exceededLimits()) { ?>
				<a href="<?php echo $lib->getCreateLink();?>" class="btn btn-es-primary btn-create btn-block t-lg-mb--xl"><?php echo JText::_('COM_EASYSOCIAL_ALBUMS_CREATE_ALBUM'); ?></a>
			<?php } ?>

			<?php if ($coreAlbums) { ?>
			<div class="es-side-widget">

				<?php echo $this->html('widget.title', JText::_($lib->getCoreAlbumsTitle())); ?>

				<div class="es-side-widget__bd">
					<ul class="o-tabs o-tabs--stacked" data-album-list-item-group="core">
					<?php foreach( $coreAlbums as $album ){ ?>
						<li class="o-tabs__item has-notice<?php if ($album->id==$id) { ?> active<?php } ?>" data-album-list-item data-album-id="<?php echo $album->id; ?>">
							<a href="<?php echo $album->getPermalink();?>" title="<?php echo $album->get('title'); ?>" class="o-tabs__link">
								<span data-album-list-item-title><?php echo $album->get('title'); ?></span> 
								<div class="o-tabs__bubble" data-album-list-item-count><?php echo $album->getTotalPhotos(); ?></div>
							</a>
							<div class="o-loader o-loader--sm"></div>
						</li>
					<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>

			<?php if ($lib->showMyAlbums() && ($myAlbums || ($layout == 'form' && empty($id)))) { ?>
			<div class="es-side-widget">

				<?php echo $this->html('widget.title', JText::_($lib->getMyAlbumsTitle())); ?>

				<div class="es-side-widget__bd">
					<ul class="o-tabs o-tabs--stacked" data-album-list-item-group="regular">
						
						<?php if ($layout=="form" && empty($id)) { ?>
						<li class="o-tabs__item has-notice active new" data-album-list-item>
							<a href="javascript: void(0);" class="o-tabs__link">
								<i data-album-list-item-cover></i> 
								<span data-album-list-item-title><?php echo JText::_('COM_EASYSOCIAL_ALBUMS_NEW_ALBUM'); ?></span> 
								<div class="o-tabs__bubble" data-album-list-item-count>0</div>
							</a>
							<div class="o-loader o-loader--sm"></div>
						</li>
						<?php } ?>
						
						<?php foreach ($myAlbums as $album) { ?>
						<li class="o-tabs__item has-notice <?php if ($album->id==$id) { ?>active<?php } ?>"  data-album-list-item data-album-id="<?php echo $album->id; ?>">
							<a href="<?php echo $album->getPermalink(); ?>" title="<?php echo $this->html('string.escape', $album->get('title')); ?>" class="o-tabs__link">
								<i data-album-list-item-cover style="background-image: url(<?php echo $album->getCover(); ?>);"></i> 
								<span data-album-list-item-title><?php echo $album->get('title'); ?></span> 
								<div class="o-tabs__bubble" data-album-list-item-count><?php echo $album->getTotalPhotos(); ?></div>
							</a>
							<div class="o-loader o-loader--sm"></div>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>

			<?php if ($albums || ($layout=="form" && empty($id) && !$lib->showMyAlbums())) { ?>
			<div class="es-side-widget">

				<?php echo $this->html('widget.title', JText::_('COM_EASYSOCIAL_OTHER_ALBUMS')); ?>

				<div class="es-side-widget__bd">
					<ul class="o-tabs o-tabs--stacked" data-album-list-item-group="regular" data-album-list-item-container-regular>

						<?php if ($layout=="form" && empty($id) && !$lib->showMyAlbums()) { ?>
						<li class="o-tabs__item has-notice active new" data-album-list-item>
							<a href="javascript: void(0);" class="o-tabs__link">
								<i data-album-list-item-cover></i> 
								<span data-album-list-item-title><?php echo JText::_('COM_EASYSOCIAL_ALBUMS_NEW_ALBUM'); ?></span> 
								<div class="o-tabs__bubble" data-album-list-item-count>0</div>
							</a>
						</li>
						<?php } ?>
						
						<?php if ($albums) { ?>
							<?php foreach ($albums as $album) { ?>
							<li class="o-tabs__item has-notice <?php if ($album->id==$id) { ?>active<?php } ?>"  data-album-list-item data-album-id="<?php echo $album->id; ?>">
								<a href="<?php echo $album->getPermalink(); ?>" title="<?php echo $this->html('string.escape', $album->get('title')); ?>" class="o-tabs__link">
									<i data-album-list-item-cover style="background-image: url(<?php echo $album->getCover(); ?>);"></i> 
									<span data-album-list-item-title><?php echo $album->get('title'); ?></span> 
									<div class="o-tabs__bubble" data-album-list-item-count><?php echo $album->getTotalPhotos(); ?></div>
								</a>
							</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</div>

				<?php if ($totalAlbums > count($albums)) { ?>
				<div class="es-side-widget__bd">
					<a href="javascript:void(0);" data-album-showall>
						<?php echo JText::_('COM_ES_VIEW_ALL');?>
					</a>
				</div>
				<?php } ?>

			</div>
			<?php } ?>

			<?php echo $this->render('module', 'es-albums-sidebar-bottom'); ?>
		</div>

		<div class="es-content" data-wrapper>
			<?php echo $this->html('html.loading'); ?>

			<?php echo $this->render('module', 'es-albums-before-contents'); ?>

			<div class="es-album-browser-content" data-album-browser-content>
				<?php echo $content; ?>
			</div>

			<?php echo $this->render('module', 'es-albums-after-contents'); ?>
		</div>
	</div>
</div>
