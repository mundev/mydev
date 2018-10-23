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
<?php echo $this->html('cover.user', $user, 'friends'); ?>

<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container" data-es-friends-wrapper data-userid="<?php echo $user->id;?>" data-es-container>

	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render('module', 'es-friends-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_FRIENDS_SIDEBAR_TITLE'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<li class="o-tabs__item has-notice <?php echo !$activeList->id && (!$filter || $filter == 'all' ) ? ' active' : '';?>" data-filter-item data-type="all">
						<a href="<?php echo ESR::friends(array('userid' => $userAlias));?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS', true);?>">
							<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_ALL_FRIENDS_FILTER');?>
						</a>
						<span class="o-tabs__bubble" data-counter><?php echo $totalFriends;?></span>
						<div class="o-loader o-loader--sm"></div>
					</li>


					<?php if (!$user->isViewer()) { ?>
					<li class="o-tabs__item has-notice <?php echo !$activeList->id && $filter == 'mutual' ? ' active' : '';?>" data-filter-item data-type="mutual">
						<a href="<?php echo ESR::friends(array('filter' => 'mutual', 'userid' => $userAlias));?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_MUTUAL_FRIENDS', true);?>">
							<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_MUTUAL_FRIENDS_FILTER');?>
						</a>
						<span class="o-tabs__bubble" data-counter><?php echo $totalMutualFriends;?></span>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<?php } ?>

					<?php if ($user->isViewer()) { ?>
						<li class="o-tabs__item has-notice <?php echo !$activeList->id && $filter == 'suggest' ? ' active' : '';?>" data-filter-item data-type="suggest">
							<a href="<?php echo ESR::friends(array('filter' => 'suggest', 'userid' => $userAlias));?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_SUGGESTIONS', true);?>">
								<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_SUGGEST_FRIENDS_FILTER');?>
							</a>
							<span class="o-tabs__bubble" data-counter><?php echo $totalFriendSuggest;?></span>
							<div class="o-loader o-loader--sm"></div>
						</li>

						<li class="o-tabs__item has-notice <?php echo !$activeList->id && $filter == 'pending' ? ' active' : '';?>" data-filter-item data-type="pending">
							<a href="<?php echo ESR::friends(array('filter' => 'pending', 'userid' => $userAlias));?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_PENDING_APPROVAL', true);?>">
								<?php echo JText::_( 'COM_EASYSOCIAL_FRIENDS_PENDING_APPROVAL_FILTER' );?>
							</a>
							<span class="o-tabs__bubble" data-counter><?php echo $totalPendingFriends;?></span>
							<div class="o-loader o-loader--sm"></div>
						</li>

						<li class="o-tabs__item has-notice <?php echo !$activeList->id && $filter == 'request' ? ' active' : '';?>" data-filter-item data-type="request">
							<a href="<?php echo ESR::friends(array('filter' => 'request', 'userid' => $this->my->id == $user->id ? '' : $user->getAlias()));?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_FRIENDS_REQUESTS', true);?>">
								<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_REQUEST_SENT_FILTER');?>
							</a>
							<span class="o-tabs__bubble" data-counter><?php echo $totalRequestSent;?></span>
							<div class="o-loader o-loader--sm"></div>
						</li>

						<?php if ($this->config->get('friends.invites.enabled')) { ?>
						<li class="o-tabs__item has-notice <?php echo !$activeList->id && $filter == 'invites' ? ' active' : '';?>" data-filter-item data-type="invites">
							<a href="<?php echo ESR::friends(array('filter' => 'invites', 'userid' => $userAlias));?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_INVITED_FRIENDS', true);?>">
								<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_INVITED_FRIENDS');?>
							</a>
							<span class="o-tabs__bubble" data-counter><?php echo $totalInvites;?></span>
							<div class="o-loader o-loader--sm"></div>
						</li>
						<?php }?>
					<?php } ?>
				</ul>
			</div>
		</div>

		<?php if ($user->isViewer() && $this->access->allowed('friends.list')) { ?>
			<hr class="es-hr" />
			
			<?php if ($user->isViewer() && ES::lists()->canCreateList()) { ?>
			<a href="<?php echo ESR::friends(array('layout' => 'listForm'));?>" class="btn btn-es-primary btn-block btn-sm t-lg-mb--xl">
				<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_NEW_LIST'); ?>
			</a>
			<?php } ?>

			<div class="es-side-widget">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_FRIENDS_YOUR_LIST'); ?>

				<div class="es-side-widget__bd" data-friends-list>
					<?php if ($lists) { ?>
					<ul class="o-tabs o-tabs--stacked" data-friends-listItems>
						<?php foreach ($lists as $list) { ?>
							<li class="o-tabs__item has-notice item-<?php echo $list->id;?><?php echo $activeList->id == $list->id ? ' active' : '';?>" data-filter-item data-type="list" data-id="<?php echo $list->id;?>">
								<a href="<?php echo ESR::friends(array('listId' => $list->id));?>" class="o-tabs__link" title="<?php echo $this->html('string.escape' , $list->get('title'));?>">
									<?php echo $this->html('string.escape', $list->get('title')); ?>
								</a>
								<span class="o-tabs__bubble" data-counter><?php echo $list->getCount();?></span>
								<div class="o-loader o-loader--sm"></div>
							</li>
						<?php } ?>
					</ul>
					<?php } else { ?>
					<div class="t-text--muted">
						<?php echo JText::_('COM_EASYSOCIAL_FRIENDS_NO_LIST_CREATED_YET'); ?>
					</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>

		<?php echo $this->render('module', 'es-friends-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
	</div>


	<div class="es-content" data-wrapper>
		<?php echo $this->render('module', 'es-friends-before-contents'); ?>

		<?php echo $this->html('html.loading'); ?>

		<div data-contents>
		<?php if ($filter == 'invites') { ?>
			<?php echo $this->includeTemplate('site/friends/default/invites', array('user' => $user, 'pagination' => $pagination)); ?>
		<?php } else { ?>
			<?php echo $this->includeTemplate('site/friends/default/items', array('user' => $user, 'pagination' => $pagination)); ?>
		<?php } ?>
		</div>

		<?php echo $this->render('module', 'es-friends-after-contents'); ?>
	</div>
</div>
