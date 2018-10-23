<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-sidebar class="es-sidebar">
	<?php echo $this->render('module', 'es-audios-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>

	<?php if ($allowCreation) { ?>
		<a class="btn btn-es-primary btn-block t-lg-mb--xl" href="<?php echo $createLink;?>">
			<?php echo JText::_('COM_ES_AUDIO_ADD_AUDIO');?>
		</a>
	<?php } ?>

	<div class="es-side-widget">
		<div class="es-side-widget__hd">
			<div class="es-side-widget__title"><?php echo JText::_('COM_ES_AUDIO');?></div>
		</div>

		<div class="es-side-widget__bd">
			<ul data-es-audios-filters="" class="o-tabs o-tabs--stacked">
				<li class="o-tabs__item has-notice <?php echo ($filter == '' || $filter == 'all') ? 'active' : '';?>">
					<a href="<?php echo $adapter->getAllAudiosLink();?>"
						data-audios-filter
						data-type="all"
						title="<?php echo $allAudiosPageTitle;?>"
						class="o-tabs__link">
						<span><?php echo JText::_('COM_ES_AUDIO_FILTERS_ALL_AUDIOS');?></span>
					</a>
					<span class="o-tabs__bubble" data-counter><?php echo $total;?></span>
					<div class="o-loader o-loader--sm"></div>
				</li>

				<li class="o-tabs__item has-notice <?php echo $filter == 'featured' ? 'active' : '';?>">
					<a href="<?php echo $adapter->getAllAudiosLink('featured');?>"
						data-audios-filter
						data-type="featured"
						title="<?php echo $featuredAudiosPageTitle;?>"
						class="o-tabs__link">
						<span><?php echo JText::_('COM_ES_AUDIO_FILTERS_FEATURED_AUDIOS');?></span>
					</a>
					<span class="o-tabs__bubble" data-counter data-total-featured><?php echo $totalFeatured;?></span>
					<div class="o-loader o-loader--sm"></div>
				</li>

				<?php if ($showMyAudios) { ?>
					<li class="o-tabs__item has-notice <?php echo $filter == 'mine' ? 'active' : '';?>">
						<a href="<?php echo FRoute::audios(array('filter' => 'mine'));?>"
							data-audios-filter
							data-type="mine"
							title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_AUDIO_FILTER_MINE');?>"
							class="o-tabs__link">
							<span><?php echo JText::_('COM_ES_AUDIO_FILTERS_MY_AUDIOS');?></span>
						</a>
						<span class="o-tabs__bubble" data-counter data-total-created><?php echo $totalUserAudios;?></span>
						<div class="o-loader o-loader--sm"></div>
					</li>
				<?php } ?>

				<?php if ($showPendingAudios) { ?>
					<li class="o-tabs__item has-notice <?php echo $filter == 'pending' ? 'active' : '';?>">
						<a href="<?php echo $adapter->getAllAudiosLink('pending');?>"
							data-audios-filter
							data-type="pending"
							title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_AUDIO_FILTER_PENDING');?>"
							class="o-tabs__link">
							<span><?php echo JText::_('COM_ES_AUDIO_FILTERS_PENDING_AUDIOS');?></span>
						</a>
						<span class="o-tabs__bubble" data-counter data-total-pending><?php echo $totalPending;?></span>
						<div class="o-loader o-loader--sm"></div>
					</li>
				<?php } ?>

			</ul>
		</div>
	</div>

	<?php if ($browseView) { ?>
	<hr class="es-hr" />
	<div class="es-side-widget">
		<div class="es-side-widget__hd">
			<div class="es-side-widget__title"><?php echo JText::_('COM_ES_AUDIO_GENRES');?></div>
		</div>
		<div class="es-side-widget__bd">
			<?php if ($genres) { ?>
				<ul data-es-audios-genres class="o-tabs o-tabs--stacked">
					<?php foreach ($genres as $genre) { ?>
					<li class="o-tabs__item has-notice<?php echo $currentGenre == $genre->id ? ' active' : '';?>">
						<a href="<?php echo $genre->getPermalink(true, $uid, $type);?>"
							data-audios-filter
							data-type="genre"
							data-id="<?php echo $genre->id;?>"
							title="<?php echo JText::_($genre->pageTitle, true);?>"
							class="o-tabs__link">
							<span><?php echo JText::_($genre->title);?></span>
						</a>

						<?php $totalGenreAudios = $genre->getTotalAudios($cluster, $uid, $type); ?>
						<span class="o-tabs__bubble" data-counter data-total-audios="<?php echo $totalGenreAudios; ?>"><?php echo $totalGenreAudios; ?></span>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<?php if ($canCreatePlaylist) { ?>
		<hr class="es-hr" />

		<a href="<?php echo ESR::audios(array('layout' => 'playlistform'));?>" class="btn btn-es-primary btn-block btn-sm t-lg-mb--xl">
			<?php echo JText::_('COM_ES_AUDIO_NEW_PLAYLIST'); ?>
		</a>
	<?php } ?>


	<div class="es-side-widget">
		<?php echo $this->html('widget.title', 'COM_ES_AUDIO_PLAYLIST'); ?>

		<div class="es-side-widget__bd" data-audios-list>
			<?php if ($lists) { ?>
			<ul class="o-tabs o-tabs--stacked" data-audios-listItems>
				<?php foreach ($lists as $list) { ?>
					<li class="o-tabs__item has-notice item-<?php echo $list->id;?> <?php echo $activeList->id == $list->id ? ' active' : '';?>" data-id="<?php echo $list->id;?>">
						<a href="<?php echo $adapter->getPlaylistLink($list->id);?>"
							data-audios-filter
							data-type="list"
							title="<?php echo $this->html('string.escape' , $list->get('title'));?>"
							data-id="<?php echo $list->id;?>"
							class="o-tabs__link">
							<?php echo $this->html('string.escape', $list->get('title')); ?>
						</a>
						<span class="o-tabs__bubble" data-counter><?php echo $list->getCount();?></span>
						<div class="o-loader o-loader--sm"></div>
					</li>
				<?php } ?>
			</ul>
			<?php } else { ?>
			<div class="t-text--muted">
				<?php echo JText::_('COM_ES_AUDIO_NO_PLAYLIST_CREATED_YET'); ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php echo $this->render('module', 'es-audios-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>

</div>
