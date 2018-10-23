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

<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container app-members app-groups" data-es-container data-es-group-members data-id="<?php echo $group->id;?>" data-return="<?php echo $returnUrl;?>">
	<div class="es-sidebar" data-sidebar>
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_FILTERS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked feed-items">
					<li class="o-tabs__item has-notice <?php echo $active == '' ? ' active' : '';?>" data-filter data-type="all">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_GROUP_MEMBERS_FILTER_ALL');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['total'];?></div>
					</li>

					<li class="o-tabs__item has-notice <?php echo $active == 'members' ? ' active' : '';?>" data-filter data-type="members">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_GROUP_MEMBERS_FILTER_MEMBERS');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['members'];?></div>
					</li>

					<li class="o-tabs__item has-notice <?php echo $active == 'admin' ? ' active' : '';?>" data-filter data-type="admin">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_GROUP_MEMBERS_FILTER_ADMINS');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['admins'];?></div>
					</li>

					<?php if (($group->isClosed() || $group->isSemiOpen()) && ($group->isAdmin($this->my->id) || $group->isOwner($this->my->id))) { ?>
					<li class="o-tabs__item <?php echo $active == 'pending' ? ' active' : '';?> <?php echo $counters['pending'] ? 'has-notice' : '';?>" data-filter data-type="pending">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_GROUP_MEMBERS_FILTER_PENDING');?>
							<div class="o-tabs__bubble" data-counter><?php echo $counters['pending'];?></div>
						</a>
					</li>
					<?php } ?>

					<?php if ($group->isInviteOnly() && ($group->isAdmin($this->my->id) || $group->isOwner($this->my->id))) { ?>
						<li class="o-tabs__item <?php echo $active == 'invited' ? ' active' : '';?> <?php echo $counters['invited'] ? 'has-notice' : '';?>" data-filter data-type="invited">
							<a href="javascript:void(0);" class="o-tabs__link">
								<?php echo JText::_('APP_GROUP_MEMBERS_FILTER_INVITED');?>
								<div class="o-tabs__bubble" data-counter><?php echo $counters['invited'];?></div>
							</a>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-content">
		<div class="app-contents-wrap">
			<div class="o-input-group o-input-group">
				<input type="text" class="o-form-control" data-search-input placeholder="<?php echo JText::_('APP_GROUP_MEMBERS_SEARCH_MEMBERS'); ?>" />
			</div>
			
			<div class="t-lg-mt--xl" data-wrapper>
				<?php echo $this->html('html.loading'); ?>

				<div data-result>
					<?php echo $this->includeTemplate('apps/group/members/groups/list'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
