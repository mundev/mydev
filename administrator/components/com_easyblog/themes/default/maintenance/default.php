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
			<div class="form-group form-inline">
				<?php echo $this->html('filter.lists', $versions, 'filter_version', $version, JText::_('COM_EASYBLOG_MAINTENANCE_ALL_VERSIONS'), 'all'); ?>
			</div>
		</div>
		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="form-group">
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
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE') , 'title', $orderDirection, $order ); ?>
					</th>

					<th width="5%" class="center nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE') , 'version', $orderDirection, $order ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if( $scripts ){ ?>
					<?php $i = 0; ?>
					<?php foreach($scripts as $script) { ?>
						<tr
							data-item
							data-id="<?php echo $script->key;?>"
							data-title="<?php echo $script->title;?>"
						>
							<td class="center hidden-iphone" valign="top">

								<?php echo $this->html('grid.id', $i, $script->key); ?>
							</td>
							<td>
								<div><b><?php echo $script->title; ?></b></div>
								<div class="fd-small"><?php echo $script->description; ?></div>
							</td>
							<td class="center"><?php echo $script->version; ?></td>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="3" align="center">
						<?php echo JText::_('No Scripts found.');?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.action'); ?>
	<input type="hidden" name="view" value="maintenance" />
	<input type="hidden" name="layout" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>
