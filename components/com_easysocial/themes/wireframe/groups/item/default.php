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
<div class="es-profile" data-es-group data-id="<?php echo $group->id;?>">

	<?php echo $this->html('cover.group', $group, $layout); ?>

	<?php echo $this->html('responsive.toggle'); ?>

	<div class="es-container" data-es-container>

		<?php if ($layout == 'timeline' || $layout == 'moderation' || $layout == 'filterForm') { ?>
		<div class="es-sidebar" data-sidebar>

			<?php echo $this->render('module', 'es-groups-item-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>
			<?php echo $this->render('widgets', SOCIAL_TYPE_GROUP, 'groups', 'sidebarTop', array('uid' => $group->id, 'group' => $group)); ?>

			<div class="es-side-widget">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_GROUPS_INTRO'); ?>

				<div class="es-side-widget__bd">
					<?php if (!$this->isMobile() && $this->config->get('groups.layout.description')) { ?>
						<?php echo $this->html('string.truncate', $group->getDescription(), 120, '', false, false, false, true);?>
						<a href="<?php echo $aboutPermalink;?>"><?php echo JText::_('COM_EASYSOCIAL_READMORE');?></a>
					<?php } ?>

					<ul class="o-nav o-nav--stacked t-lg-mt--sm">
						<li class="o-nav__item t-text--muted t-lg-mb--sm">
							<a class="o-nav__link t-text--muted" href="<?php echo $group->getCreator()->getPermalink();?>">
								<i class="es-side-widget__icon fa fa-user t-lg-mr--md"></i>
								<?php echo $group->getCreator()->getName();?>
							</a>
						</li>

						<li class="o-nav__item t-text--muted t-lg-mb--sm">
							<a class="o-nav__link t-text--muted" href="<?php echo $group->getAppPermalink('members');?>">
								<i class="es-side-widget__icon fa fa-users t-lg-mr--md"></i>
								<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_MEMBERS_MINI', $group->getTotalMembers()), $group->getTotalMembers()); ?>
							</a>
						</li>
					</ul>
				</div>
				<?php if (!$displayFeedsFilter) { ?>
					<input type="hidden" class="active" data-filter-item="feeds" data-type="feeds" data-id="<?php echo $group->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>" />
				<?php } ?>
			</div>

			<?php if ($displayFeedsFilter) { ?>
			<div class="es-side-widget">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_NEWS_FEED', $customFilterActions); ?>

				<div class="es-side-widget__bd">
					<ul class="o-tabs o-tabs--stacked">
						<li class="o-tabs__item <?php echo empty($contents) && empty($context) && empty($filterId) && ($layout != 'moderation' && $layout != 'hashtag') ? 'active' : '';?>" data-filter-item="feeds" data-type="feeds" data-id="<?php echo $group->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
							<a href="<?php echo ESR::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'timeline')); ?>" class="o-tabs__link">
								<?php echo JText::_('COM_EASYSOCIAL_GROUP_TIMELINE'); ?>
								<div class="o-tabs__bubble" data-counter>0</div>
							</a>
						</li>

						<?php if (($group->isAdmin() || $group->isOwner() || $this->my->isSiteAdmin()) && $showPendingPostFilter) { ?>
						<li class="o-tabs__item <?php echo $type == 'moderation' ? ' active' : '';?> <?php echo $totalPendingPost ? 'has-notice' : '' ?>" data-filter-item="moderation" data-type="moderation">
							<a href="<?php echo ESR::groups(array('layout' => 'item', 'id' => $group->getAlias(), 'type' => 'moderation'));?>" class="o-tabs__link">
								<?php echo JText::_('COM_EASYSOCIAL_GROUP_SIDEBAR_PENDING_POSTS'); ?>
								<div class="o-tabs__bubble" data-counter><?php echo $totalPendingPost; ?></div>
							</a>
						</li>
						<?php } ?>

						<?php if (isset($hashtag) && $hashtag) { ?>
							<li class="o-tabs__item active" style="display:none;" data-filter-item="hashtag" data-type="hashtag" data-tag="<?php echo $hashtag ?>" data-id="<?php echo $group->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
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



			<?php echo $this->render('widgets', SOCIAL_TYPE_GROUP, 'groups', 'sidebarMiddle', array('uid' => $group->id, 'group' => $group)); ?>

			<?php echo $this->render('widgets', SOCIAL_TYPE_GROUP, 'groups', 'sidebarBottom', array('uid' => $group->id, 'group' => $group)); ?>

			<?php echo $this->render('module', 'es-groups-item-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>
		<?php } ?>

		<div class="es-content">

			<?php echo $this->render('module', 'es-groups-before-contents'); ?>

			<?php if ($layout != 'info') { ?>
			<div class="es-content-wrap" data-wrapper>
				<?php echo $this->html('html.loading'); ?>

				<div data-contents>
					<?php if ($contents){ ?>
						<?php echo $contents; ?>
					<?php } else { ?>
						<?php echo $this->includeTemplate('site/groups/item/content'); ?>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

			<?php if ($layout == 'info') { ?>
			<div class="es-profile-info">
				<?php if ($steps) { ?>
					<?php echo $this->output('site/fields/about/default', array('steps' => $steps, 'canEdit' => $group->isAdmin(), 'objectId' => $group->id, 'routerType' => 'groups')); ?>
				<?php } ?>
			</div>
			<?php } ?>

			<?php echo $this->render('module', 'es-groups-after-contents'); ?>
		</div>
	</div>
</div>
