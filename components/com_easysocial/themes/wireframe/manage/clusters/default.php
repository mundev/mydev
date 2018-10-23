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

<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container" data-es-cluster-wrapper data-es-container>

	<div class="es-sidebar" data-sidebar>
		
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_MANAGE_CLUSTER_SIDEBAR_TITLE'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<li class="o-tabs__item has-notice <?php echo (!$filter || $filter == 'all' ) ? ' active' : '';?>" data-filter-item data-type="all">
						<a href="<?php echo ESR::manage(array('layout' => 'clusters'));?>" class="o-tabs__link" title="<?php echo JText::_('COM_ES_MANAGE_CLUSTER_PENDING_ITEMS', true);?>">
							<?php echo JText::_('COM_ES_MANAGE_CLUSTER_PENDING_ITEMS');?>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>

					<li class="o-tabs__item has-notice <?php echo $filter == 'event' ? ' active' : '';?>" data-filter-item data-type="event">
						<a href="<?php echo ESR::manage(array('layout' => 'clusters', 'filter' => 'event'));?>" class="o-tabs__link" title="<?php echo JText::_('COM_ES_PAGE_TITLE_EVENTS_MODERATION', true);?>">
							<?php echo JText::_('COM_EASYSOCIAL_EVENTS');?>
						</a>
						<span class="o-tabs__bubble" data-counter><?php echo $pendingCounters['event'];?></span>
						<div class="o-loader o-loader--sm"></div>
					</li>

					<li class="o-tabs__item has-notice <?php echo $filter == 'group' ? ' active' : '';?>" data-filter-item data-type="group">
						<a href="<?php echo ESR::manage(array('layout' => 'clusters', 'filter' => 'group'));?>" class="o-tabs__link" title="<?php echo JText::_('COM_ES_PAGE_TITLE_GROUPS_MODERATION', true);?>">
							<?php echo JText::_('COM_EASYSOCIAL_GROUPS');?>
						</a>
						<span class="o-tabs__bubble" data-counter><?php echo $pendingCounters['group'];?></span>
						<div class="o-loader o-loader--sm"></div>
					</li>

					<li class="o-tabs__item has-notice <?php echo $filter == 'page' ? ' active' : '';?>" data-filter-item data-type="page">
						<a href="<?php echo ESR::manage(array('layout' => 'clusters', 'filter' => 'page'));?>" class="o-tabs__link" title="<?php echo JText::_('COM_ES_PAGE_TITLE_PAGES_MODERATION', true);?>">
							<?php echo JText::_('COM_EASYSOCIAL_PAGES');?>
						</a>
						<span class="o-tabs__bubble" data-counter><?php echo $pendingCounters['page'];?></span>
						<div class="o-loader o-loader--sm"></div>
					</li>

				</ul>
			</div>
		</div>
	</div>


	<div class="es-content" data-wrapper>

		<?php echo $this->html('html.loading'); ?>

		<div data-contents>
			<?php echo $this->includeTemplate('site/manage/clusters/items', array('clusters' => $clusters, 'pagination' => $pagination)); ?>
		</div>

	</div>
</div>
