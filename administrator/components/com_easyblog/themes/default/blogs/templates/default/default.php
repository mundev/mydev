<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-grid-eb>
	<div class="app-filter-bar">
		<div class="app-filter-bar__cell">
			<?php echo $this->html('filter.search', $search); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.limit', $limit); ?>
			</div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table table-striped table-eb table-hover">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_TITLE'); ?>
					</th>
					<th width="10%" class="center">
						&nbsp;
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATUS'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_GLOBAL'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_CORE'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JHTML::_('grid.sort', 'Order', 'ordering', $orderDirection, $order); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_AUTHOR'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_HEADING_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($templates) { ?>
					<?php $i = 0; ?>
					<?php foreach ($templates as $template) { ?>
					<tr>
						<td>
							<?php echo $this->html('grid.id', $i, $template->id); ?>
						</td>
						<td>
							<a href="<?php echo rtrim(JURI::root(), '/'); ?>/administrator/index.php?option=com_easyblog&view=blogs&layout=editTemplate&id=<?php echo $template->id;?>"><?php echo JText::_($template->title);?></a>
						</td>
						<td class="center">
							<?php if (!$template->isBlank()) { ?>
							<a href="<?php echo rtrim(JURI::root(), '/'); ?>/administrator/index.php?option=com_easyblog&view=templates&layout=form&tmpl=component&id=<?php echo $template->id;?>" class="btn btn-default btn-xs">
								<i class="fa fa-edit"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_EDIT_LAYOUT');?>
							</a>
							<?php } ?>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $template, 'templates', 'published', array('blogs.publishTemplate', 'blogs.unpublishTemplate'), array()); ?>
						</td>
						<td class="center">
							<?php $disabled = ($template->isBlank() || $template->isCore()) ? true : false; ?>
							<?php echo $this->html('grid.published', $template, 'templates', 'system', array('blogs.setGlobalTemplate', 'blogs.removeGlobalTemplate'), array(JText::_('COM_EASYBLOG_GRID_TOOLTIP_UNSET_AS_GLOBAL'), JText::_('COM_EASYBLOG_GRID_TOOLTIP_SET_AS_GLOBAL')), $disabled); ?>
						</td>
						<td class="center">
							<?php echo $this->html('grid.core', $template, 'core', array(JText::_('COM_EASYBLOG_GRID_TOOLTIP_IS_NOT_CORE'), JText::_('COM_EASYBLOG_GRID_TOOLTIP_IS_CORE'))); ?>
						</td>

						<td class="order center">
							<?php $orderkey = array_search($template->id, $ordering); ?>

							<?php $disabled = 'disabled="disabled"'; ?>
							<input type="text" name="order[]" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="order-value input-xsmall"/>
							<?php $originalOrders[] = $orderkey + 1; ?>
							
							<?php if ($saveOrder) { ?>
								<span class="order-up"><?php echo $pagination->orderUpIcon($i, isset($ordering[$orderkey - 1]), 'templates.orderup', 'Move Up', $ordering); ?></span>
								<span class="order-down">
									<?php echo $pagination->orderDownIcon($i, $pagination->total, isset($ordering[$orderkey + 1]), 'templates.orderdown', 'Move Down', $ordering); ?>
								</span>
							<?php } ?>
						</td>

						
						<td class="center">
							<?php echo $template->getAuthor()->getName();?>
						</td>
						<td class="center">
							<?php echo $template->id;?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="7" class="empty">
						<?php echo JText::_('COM_EASYBLOG_PENDING_EMPTY'); ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.action'); ?>
	<input type="hidden" name="view" value="blogs" />
	<input type="hidden" name="layout" value="templates" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
</form>
