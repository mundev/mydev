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

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.lists', $filterType, 'filter_type', $type); ?>
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
		<table class="app-table table table-striped table-eb table-hover" data-table-grid>
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_EASYBLOG_META_TITLE'); ?>
					</th>
					<th class="center" width="5%">
						<?php echo JText::_('COM_EASYBLOG_META_INDEXING'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TYPE');?>
					</th>
					<th width="1%" class="text-center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ID');?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($metas) { ?>
					<?php $i = 0; ?>

					<?php foreach ($metas as $row) { ?>
					<tr>
						<td class="center">
							<?php
								$checkedOut = ( $row->type == 'view') ? true : false;
								echo $this->html('grid.id', $i , $row->id, $checkedOut);
							?>
						</td>

						<td>
							<a href="index.php?option=com_easyblog&view=metas&layout=form&id=<?php echo $row->id;?>"><?php echo $row->title; ?></a>
						</td>

						<td class="nowrap hidden-phone center">
							<?php echo $this->html('grid.published', $row, 'meta', 'indexing', array('meta.addIndexing', 'meta.removeIndexing')); ?>
						</td>

						<td class="center">
							<?php echo ucfirst($row->type); ?>
						</td>

						<td class="center">
							<?php echo $row->id;?>
						</td>

					</tr>
						<?php $i++; ?>
					<?php }?>

				<?php } else { ?>
					<tr>
						<td colspan="5" class="text-center">
							<?php echo JText::_('COM_EASYBLOG_NO_META_TAGS_INDEXED_YET');?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5" class="text-center">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<?php echo $this->html('form.action'); ?>
	<input type="hidden" name="view" value="metas" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>
