<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.lists', $groups, 'filter_group', $filterGroup, JText::_('COM_EASYBLOG_BLOCKS_FILTER_GROUPS'), ''); ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.published', 'filter_state', $filterState); ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.limit', $limit); ?>
			</div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table table-eb table-striped table-hover">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE'); ?>
					</th>
					<th width="5%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE'); ?>
					</th>
					<th class="center" width="15%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_GROUP'); ?>
					</th>
					<th class="center" width="15%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_CREATED'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($blocks) { ?>
					<?php $i = 0; ?>
					<?php foreach ($blocks as $block) { ?>
					<tr>
						<td>
							<?php echo $this->html('grid.id', $i, $block->id); ?>
						</td>

						<td align="left">
							<a href="index.php?option=com_easyblog&view=blocks&layout=form&id=<?php echo $block->id;?>"><?php echo $block->title;?></a>
						</td>
						<td class="center">
							<?php if ($block->published == 2) { ?>
								<a class="eb-state-scheduled badge" href="javascript:void(0);"
									data-eb-provide="tooltip"
									data-original-title="<?php echo JText::_('COM_EASYBLOG_BLOCKS_CORE_BLOCK');?>"
									data-placement="bottom"
									disabled="disabled"
								>
									<i class="fa fa-check"></i>
								</a>
							<?php } else { ?>
								<?php echo $this->html('grid.published', $block, 'blocks', 'published'); ?>
							<?php } ?>
						</td>
						<td class="center">
							<?php echo ucfirst($block->group);?>
						</td>
						<td class="center">
							<?php echo $block->getCreated()->toSql();?>
						</td>
						<td align="center">
							<?php echo $block->id;?>
						</td>
					</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="6" align="center" class="empty">
						<?php echo JText::_('COM_EASYBLOG_BLOCKS_NO_BLOCKS_INSTALLED');?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6" class="text-center">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.action'); ?>
	<input type="hidden" name="view" value="blocks" />
</form>
