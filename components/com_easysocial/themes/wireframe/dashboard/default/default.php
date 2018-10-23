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
<div class="es-dashboard" data-es-dashboard>

	<?php if ($this->config->get('users.dashboard.sidebar') != 'hidden') { ?>
		<?php echo $this->html('responsive.toggle'); ?>
	<?php } ?>

	<div class="es-container <?php echo $this->config->get('users.dashboard.sidebar') == 'right' ? 'es-sidebar-right' : '';?>" data-es-container>

		<?php if ($this->config->get('users.dashboard.sidebar') != 'hidden') { ?>
		<div class="es-sidebar" data-sidebar>

			<?php echo $this->render('module', 'es-dashboard-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>

			<?php echo $this->render('widgets', SOCIAL_TYPE_USER, 'dashboard', 'sidebarTop'); ?>

			<div class="es-side-widget" data-type="feeds">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS', $createCustomFilter); ?>

				<div class="es-side-widget__bd">
					<ul class="o-tabs o-tabs--stacked feed-items" data-dashboard-feeds>

						<?php if ($this->config->get('users.dashboard.everyone')) { ?>
						<li class="o-tabs__item <?php echo $filter == 'everyone' ? ' active' : '';?>" data-filter-item data-type="everyone" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
							<a href="<?php echo ESR::dashboard(array('type' => 'everyone'));?>" class="o-tabs__link">
								<?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NEWSFEEDS_EVERYONE');?>
								<div class="o-tabs__bubble" data-counter>0</div>
							</a>
							<div class="o-loader o-loader--sm"></div>
						</li>
						<?php } else if ($this->config->get('users.dashboard.start') == 'everyone') { ?>
							<li class="o-tabs__item <?php echo $filter == 'everyone' ? ' active' : '';?> t-hidden" data-filter-item data-type="everyone" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<a href="<?php echo ESR::dashboard(array('type' => 'everyone'));?>" class="o-tabs__link"></a>
							</li>
						<?php } ?>

						<li class="o-tabs__item <?php echo (empty($filter) || $filter == 'me') ? 'active' : '';?>" data-filter-item data-type="me" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
							<a href="<?php echo ESR::dashboard(array('type' => 'me'));?>" class="o-tabs__link">
								<?php if ($this->config->get('friends.enabled')) { ?>
								<?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_SIDEBAR_ME_AND_FRIENDS');?>
								<?php } else {  ?>
								<?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_SIDEBAR_MY_UPDATES');?>
								<?php } ?>
								<div class="o-tabs__bubble" data-counter>0</div>
							</a>
							<div class="o-loader o-loader--sm"></div>
						</li>

						<?php if ($this->config->get('followers.enabled')) { ?>
						<li class="o-tabs__item <?php echo $filter == 'following' ? ' active' : '';?>" data-filter-item data-type="following" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
							<a href="<?php echo ESR::dashboard(array('type' => 'following'));?>" class="o-tabs__link">
								<?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_FEEDS_FOLLOWING');?>
								<div class="o-tabs__bubble" data-counter>0</div>
							</a>
							<div class="o-loader o-loader--sm"></div>
						</li>
						<?php } ?>

						<?php if ($this->config->get('stream.bookmarks.enabled')) { ?>
							<li class="o-tabs__item <?php echo $filter == 'bookmarks' ? ' active' : '';?>" data-filter-item data-type="bookmarks" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<a href="<?php echo ESR::dashboard(array('type' => 'bookmarks'));?>" class="o-tabs__link"><?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_FEEDS_BOOKMARKS'); ?></a>
								<div class="o-loader o-loader--sm"></div>
							</li>
						<?php } ?>

						<?php if ($this->config->get('stream.pin.enabled')) { ?>
							<li class="o-tabs__item <?php echo $filter == 'sticky' ? ' active' : '';?>" data-filter-item data-type="sticky" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<a href="<?php echo ESR::dashboard(array('type' => 'sticky'));?>" class="o-tabs__link"><?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_FEEDS_STICKY'); ?></a>
								<div class="o-loader o-loader--sm"></div>
							</li>
						<?php } ?>

						<?php if ($friendLists && count($friendLists) > 0) { ?>
							<?php foreach ($friendLists as $friendList) { ?>
							<li class="o-tabs__item <?php echo $listId == $friendList->id ? ' active' : '';?>" data-filter-item data-type="list" data-id="<?php echo $friendList->id;?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<a href="<?php echo ESR::dashboard(array('type' => 'list', 'listId' => $friendList->id));?>" class="o-tabs__link">
									<?php echo $friendList->_('title'); ?>
									<div class="o-tabs__bubble" data-counter>0</div>
								</a>
								<div class="o-loader o-loader--sm"></div>
							</li>
							<?php } ?>
						<?php } ?>

						<?php if ($filter == 'hashtag' && isset($hashtag) && $hashtag) { ?>
							<li class="o-tabs__item active" style="display:none;" data-filter-item data-type="hashtag" data-tag="<?php echo $hashtag ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<div class="o-loader o-loader--sm"></div>
							</li>
						<?php } ?>

						<?php if ($this->config->get('users.dashboard.customfilters') && ($filterList && count($filterList) > 0)) { ?>
							<?php foreach ($filterList as $filter) { ?>
							<li class="o-tabs__item <?php echo $filterId == $filter->id ? ' active' : '';?>" class="o-tabs__item" data-filter-item data-type="custom" data-id="<?php echo $filter->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<a href="<?php echo ESR::dashboard(array('type' => 'filter', 'filterid' => $filter->getAlias()));?>" class="o-tabs__link">
									<?php echo $filter->_('title'); ?>
								</a>
								<div class="o-loader o-loader--sm"></div>
							</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</div>
			</div>

			<?php echo $this->render('module', 'es-dashboard-sidebar-before-newsfeeds', 'site/dashboard/sidebar.module.wrapper'); ?>

			<?php if ($appFilters && $this->config->get('users.dashboard.appfilters')) { ?>
			<div class="es-side-widget" data-section data-type="appfilters">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_FILTER_BY_APPS'); ?>

				<div class="es-side-widget__bd">
					<ul class="o-tabs o-tabs--stacked feed-items">
						<?php $i = 1; ?>
						<?php foreach ($appFilters as $appFilter) { ?>
							<li class="o-tabs__item <?php echo $i > 5 ? ' t-hidden' : '';?><?php echo $filterId == $appFilter->alias ? ' active' : '';?>"
								data-filter-item
								data-type="appFilter"
								data-id="<?php echo $appFilter->alias;?>"
								data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<a href="<?php echo ESR::dashboard(array('type' => 'appFilter', 'filterid' => $appFilter->alias));?>" class="o-tabs__link">
									<?php echo $appFilter->title;?>
								</a>
								<div class="o-loader o-loader--sm"></div>
							</li>
							<?php $i++; ?>
						<?php } ?>
					</ul>
				</div>

				<?php if (count($appFilters) > 5) { ?>
				<div class="es-side-widget__ft">
					<a href="javascript:void(0);" data-section-showall>
						<?php echo JText::_('COM_ES_VIEW_ALL');?>
					</a>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<?php echo $this->render('module', 'es-dashboard-sidebar-after-newsfeeds', 'site/dashboard/sidebar.module.wrapper'); ?>

			<?php if ($groups && $this->config->get('users.dashboard.groups') && $this->config->get('groups.enabled')) { ?>
			<div class="es-side-widget" data-section-clusters data-type="groups">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_GROUPS'); ?>

				<div class="es-side-widget__bd">
					<?php echo $this->includeTemplate('site/dashboard/default/filter.groups', array('groups' => $groups)); ?>
				</div>

				<?php if ($showMoreGroups) { ?>
				<div class="es-side-widget__ft">
					<a href="javascript:void(0);" data-clusters-showall>
						<?php echo JText::_('COM_ES_VIEW_ALL');?>
					</a>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<?php if ($events && $this->config->get('users.dashboard.events') && $this->config->get('events.enabled')) { ?>
			<div class="es-side-widget" data-section-clusters data-type="events">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_EVENTS'); ?>

				<div class="es-side-widget__bd">
					<?php echo $this->includeTemplate('site/dashboard/default/filter.events', array('events' => $events)); ?>
				</div>

				<?php if ($showMoreEvents) { ?>
				<div class="es-side-widget__ft">
					<a href="javascript:void(0);" data-clusters-showall>
						<?php echo JText::_('COM_ES_VIEW_ALL');?>
					</a>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<?php if ($pages && $this->config->get('users.dashboard.pages', true) && $this->config->get('pages.enabled')) { ?>
			<div class="es-side-widget" data-section-clusters data-type="pages">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_PAGES'); ?>

				<div class="es-side-widget__bd">
					<?php echo $this->includeTemplate('site/dashboard/default/filter.pages', array('pages' => $pages)); ?>
				</div>

				<?php if ($showMorePages) { ?>
				<div class="es-side-widget__ft">
					<a href="javascript:void(0);" data-clusters-showall>
						<?php echo JText::_('COM_ES_VIEW_ALL');?>
					</a>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<div class="widgets-wrapper">
				<?php echo $this->render('widgets' , SOCIAL_TYPE_USER , 'dashboard' , 'sidebarBottom'); ?>
			</div>

			<?php echo $this->render('module', 'es-dashboard-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>
		<?php } else { ?>
			<input type="hidden" class="active" data-filter-item="feeds" data-type="<?php echo $filter ?>"<?php echo ($filter == 'appFilter') ? ' data-id="' . $filterId . '"' : ''; ?> data-stream-identifier="<?php echo $stream->getIdentifier(); ?>" />
		<?php } ?>

		<div class="es-content" data-wrapper>
			<?php echo $this->html('html.loading'); ?>

			<?php echo $this->render('module', 'es-dashboard-before-contents'); ?>

			<div data-contents>

				<?php if ($contents) { ?>
					<?php echo $contents; ?>
				<?php } else { ?>
					<?php echo $this->includeTemplate('site/dashboard/default/feeds'); ?>
				<?php } ?>
			</div>

			<?php echo $this->render('module', 'es-dashboard-after-contents'); ?>
		</div>
	</div>
</div>
