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
<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container es-media-browser layout-album <?php echo !$albums ? '' : ' has-albums'; ?> is-<?php echo $lib->type;?>" data-albums data-es-container>

	<div class="es-sidebar" data-sidebar>

		<?php echo $this->render('module', 'es-albums-all-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>

		<?php if ($lib->canCreateAlbums() && !$lib->exceededLimits()) { ?>
			<a href="<?php echo $lib->getCreateLink();?>" class="btn btn-es-primary btn-create btn-block t-lg-mb--xl"><?php echo JText::_('COM_EASYSOCIAL_ALBUMS_CREATE_ALBUM'); ?></a>
		<?php } ?>

		<div class="es-side-widget">

			<?php echo $this->html('widget.title', JText::_('COM_EASYSOCIAL_PAGE_TITLE_ALBUMS')); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked" data-es-albums-filters>
					<li class="o-tabs__item<?php echo $filter == '' || $filter == 'all' ? ' active' : ''; ?>" data-es-albums-filters-type="all">
						<a href="<?php echo ESR::albums();?>" title="<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_FILTER_ALL_ALBUMS', true);?>" class="o-tabs__link">
							<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_FILTER_ALL_ALBUMS');?>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<?php if ($this->my->id) { ?>
						<li class="o-tabs__item" data-es-albums-filters-type="me">
							<a href="<?php echo ESR::albums(array('layout' => 'mine'));?>" title="<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_FILTER_MY_ALBUMS', true);?>" class="o-tabs__link">
								<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_FILTER_MY_ALBUMS');?>
							</a>
							<div class="o-loader o-loader--sm"></div>
						</li>
						<li class="o-tabs__item<?php echo $filter == 'favourite' ? ' active' : ''; ?>" data-es-albums-filters-type="favourite">
							<a href="<?php echo ESR::albums(array('layout' => 'favourite'));?>" title="<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_FILTER_FAVOURITE_ALBUMS', true);?>" class="o-tabs__link">
								<?php echo JText::_('COM_EASYSOCIAL_ALBUMS_FILTER_FAVOURITE_ALBUMS');?>
							</a>
							<div class="o-loader o-loader--sm"></div>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<?php echo $this->render('module', 'es-albums-all-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
	</div>

	<div class="es-content">

		<div class="es-list-sorting">
			<?php echo $this->html('form.popdown', 'sorting', $sorting, array(
				$this->html('form.popdownOption', 'latest', 'COM_ES_SORT_BY_LATEST', '', false, $sortItems->latest->attributes, $sortItems->latest->url),
				$this->html('form.popdownOption', 'alphabetical', 'COM_ES_SORT_ALPHABETICALLY', '', false, $sortItems->alphabetical->attributes, $sortItems->alphabetical->url),
				$this->html('form.popdownOption', 'popular', 'COM_ES_SORT_BY_MOST_VIEWS', '', false, $sortItems->popular->attributes, $sortItems->popular->url),
				$this->html('form.popdownOption', 'likes', 'COM_ES_SORT_BY_MOST_LIKES', '', false, $sortItems->likes->attributes, $sortItems->likes->url)
			)); ?>
		</div>

		<div class="es-snackbar">
			<h1 class="es-snackbar__title"><?php echo JText::_('COM_EASYSOCIAL_ALBUMS_PHOTO_ALBUMS'); ?></h1>
		</div>

		<div class="">

			<?php echo $this->render('module', 'es-albums-before-contents'); ?>

			<div class="es-list-result" data-wrapper>
				<?php echo $this->html('html.loading'); ?>

				<div class="es-albums" data-contents>
					<?php echo $this->includeTemplate('site/albums/items/default'); ?>
				</div>

			</div>

			<?php echo $this->render('module', 'es-albums-after-contents'); ?>
		</div>
	</div>



</div>



