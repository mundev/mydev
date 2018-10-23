<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-profile" data-es-page data-id="<?php echo $page->id;?>">

	<?php echo $this->html('responsive.toggle'); ?>

	<div class="es-container" data-es-container>

		<?php if ($layout == 'timeline' || $layout == 'moderation' || $layout == 'filterForm') { ?>
		<div class="es-sidebar" data-sidebar>

			<?php echo $this->render('module', 'es-pages-item-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>
			<?php echo $this->render('widgets', SOCIAL_TYPE_PAGE, 'pages', 'sidebarTop', array('uid' => $page->id, 'page' => $page)); ?>

			<div class="es-side-widget">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_PAGES_INTRO'); ?>

				<div class="es-side-widget__bd">
					<?php if (!$this->isMobile() && $this->config->get('pages.layout.description')) { ?>
						<?php echo $this->html('string.truncate', $page->getDescription(), 120, '', false, false, false, true);?>
						<a href="<?php echo $aboutPermalink;?>"><?php echo JText::_('COM_EASYSOCIAL_READMORE');?></a>
					<?php } ?>

					<ul class="o-nav o-nav--stacked t-lg-mt--sm">
						<li class="o-nav__item t-text--muted t-lg-mb--sm">
							<a class="o-nav__link t-text--muted" href="<?php echo $page->getCreator()->getPermalink();?>">
								<i class="es-side-widget__icon fa fa-user t-lg-mr--md"></i>
								<?php echo $page->getCreator()->getName();?>
							</a>
						</li>

						<li class="o-nav__item t-text--muted t-lg-mb--sm">
							<a class="o-nav__link t-text--muted" href="<?php echo $page->getAppPermalink('followers');?>">
								<i class="es-side-widget__icon fa fa-thumbs-o-up t-lg-mr--md"></i>
								<?php echo JText::sprintf(ES::string()->computeNoun('COM_ES_LIKES_COUNT', $page->getTotalMembers()), $page->getTotalMembers()); ?>
							</a>
						</li>
					</ul>
				</div>
				<?php if (!$displayFeedsFilter) { ?>
					<input type="hidden" class="active" data-filter-item="feeds" data-type="feeds" data-id="<?php echo $page->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>" />
				<?php } ?>
			</div>

			<?php echo $this->trigger('fields', 'page', 'item', 'sidebarTop', $page); ?>

			<?php if ($displayFeedsFilter) { ?>
			<div class="es-side-widget">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_NEWS_FEED', $customFilterActions); ?>

				<div class="es-side-widget__bd">
					<ul class="o-tabs o-tabs--stacked">
						<li class="o-tabs__item <?php echo empty($contents) && empty($filterId) && $layout == 'timeline' ? 'active' : '';?>" data-filter-item="feeds" data-type="feeds" data-id="<?php echo $page->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
							<a href="<?php echo ESR::pages(array('layout' => 'item', 'id' => $page->getAlias(), 'type' => 'timeline')); ?>" class="o-tabs__link">
								<?php echo JText::_('COM_EASYSOCIAL_PAGE_TIMELINE'); ?>
								<div class="o-tabs__bubble" data-counter>0</div>
							</a>
						</li>

						<?php if ($page->isAdmin() && $showPendingPostFilter) { ?>
						<li class="o-tabs__item <?php echo $layout == 'moderation' ? ' active' : '';?> <?php echo $totalPendingPost ? 'has-notice' : '' ?>" data-filter-item="moderation" data-type="moderation">
							<a href="<?php echo ESR::pages(array('layout' => 'item', 'id' => $page->getAlias(), 'type' => 'moderation'));?>" class="o-tabs__link">
								<?php echo JText::_('COM_EASYSOCIAL_PAGE_SIDEBAR_PENDING_POSTS'); ?>
								<div class="o-tabs__bubble" data-counter><?php echo $totalPendingPost; ?></div>
							</a>
						</li>
						<?php } ?>

						<?php if (isset($hashtag) && $hashtag) { ?>
							<li class="o-tabs__item active" style="display:none;" data-filter-item="hashtag" data-type="hashtag" data-tag="<?php echo $hashtag ?>" data-id="<?php echo $page->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<div class="o-loader o-loader--sm"></div>
							</li>
						<?php } ?>

						<?php if ($filters && count($filters) > 0) { ?>

							<?php foreach ($filters as $filter) { ?>
							<li class="o-tabs__item <?php echo $filterId == $filter->id ? ' active' : '';?>" class="o-tabs__item" data-filter-item="filters" data-type="filters" data-id="<?php echo $filter->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<a href="<?php echo $filter->permalink;?>" class="o-tabs__link"><?php echo $filter->_('title'); ?></a>
							</li>

							<?php } ?>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>

			<?php echo $this->render('widgets', SOCIAL_TYPE_PAGE, 'pages', 'sidebarMiddle', array('uid' => $page->id, 'page' => $page)); ?>

			<?php echo $this->render('widgets', SOCIAL_TYPE_PAGE, 'pages', 'sidebarBottom', array('uid' => $page->id, 'page' => $page)); ?>

			<?php echo $this->render('module', 'es-pages-item-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>
		<?php } ?>

		<div class="es-content">

			<?php echo $this->html('cover.page', $page, $layout); ?>

			<?php echo $this->render('module', 'es-pages-before-contents'); ?>

			<?php if ($layout != 'info') { ?>
			<div class="es-content-wrap" data-wrapper>
				<?php echo $this->html('html.loading'); ?>

				<div data-contents>
					<?php if ($contents) { ?>
						<?php echo $contents; ?>
					<?php } else { ?>
						<?php echo $this->includeTemplate('site/pages/item/content'); ?>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

			<?php if ($layout == 'info') { ?>
			<div class="es-profile-info">
				<?php if ($steps) { ?>
					<?php echo $this->output('site/fields/about/default', array('steps' => $steps, 'canEdit' => $page->isAdmin(), 'objectId' => $page->id, 'routerType' => 'pages')); ?>
				<?php } ?>
			</div>
			<?php } ?>

			<?php echo $this->render('module', 'es-pages-after-contents'); ?>
		</div>
	</div>
</div>
