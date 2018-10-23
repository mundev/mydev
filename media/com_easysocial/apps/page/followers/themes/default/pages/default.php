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

<div class="es-container app-followers app-pages" data-es-container data-es-page-followers data-id="<?php echo $page->id;?>" data-return="<?php echo $returnUrl;?>">
	<?php if ($page->isAdmin()) { ?>
	<div class="es-sidebar" data-sidebar>
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_FILTERS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked feed-items">
					<li class="o-tabs__item has-notice <?php echo $active == '' ? ' active' : '';?>" data-filter data-type="all">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_PAGE_FOLLOWERS_FILTER_ALL');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['total'];?></div>
					</li>

					<li class="o-tabs__item has-notice <?php echo $active == 'followers' ? ' active' : '';?>" data-filter data-type="followers">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_PAGE_FOLLOWERS_FILTER_FOLLOWERS');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['followers'];?></div>
					</li>

					<li class="o-tabs__item has-notice <?php echo $active == 'admin' ? ' active' : '';?>" data-filter data-type="admin">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_PAGE_FOLLOWERS_FILTER_ADMINS');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['admins'];?></div>
					</li>

					<?php if ($page->isClosed() && ($page->isAdmin($this->my->id) || $page->isOwner($this->my->id))) { ?>
					<li class="o-tabs__item <?php echo $active == 'pending' ? ' active' : '';?> <?php echo $counters['pending'] ? 'has-notice' : '';?>" data-filter data-type="pending">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_PAGE_FOLLOWERS_FILTER_PENDING');?>
							<div class="o-tabs__bubble" data-counter><?php echo $counters['pending'];?></div>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="es-content">
		<div class="app-contents-wrap">
			<div class="o-input-group">
				<input type="text" class="o-form-control" data-search-input placeholder="<?php echo JText::_('COM_ES_SEARCH_PLACEHOLDER'); ?>" />
			</div>

			<div class="t-lg-mt--xl" data-wrapper>
				<?php echo $this->html('html.loading'); ?>
				<div data-result>
					<?php echo $this->includeTemplate('apps/page/followers/pages/list'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
