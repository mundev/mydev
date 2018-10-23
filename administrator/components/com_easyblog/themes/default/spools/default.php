<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
				<?php echo $state; ?>
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
		<div class="alert alert-warning">
			<i class="fa fa-info-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SPOOLS_TIPS'); ?> 
			<a href="https://stackideas.com/docs/easyblog/administrators/cronjobs" target="_blank" class="btn btn-sm btn-default">
				<i class="fa fa-external-link"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SETUP_CRON');?>
			</a>
		</div>

		<table class="app-table table table-striped table-eb table-hover">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>

					<th><?php echo JText::_( 'COM_EASYBLOG_SUBJECT' ); ?></th>

					<th width="30%">
						<?php echo JText::_('COM_EASYBLOG_RECIPIENT'); ?>
					</th>

					<th width="5%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_STATE'); ?>
					</th>

					<th width="20%" class="center nowrap">
						<?php echo JText::_('COM_EASYBLOG_CREATED'); ?>
					</th>

					<th width="1%" class="center"><?php echo JText::_('COM_EASYBLOG_ID'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if( $mails ){ ?>
					<?php $i = 0; ?>
					<?php foreach ($mails as $row) {?>
					<tr>
						<td class="center">
							<?php echo $this->html('grid.id', $i++, $row->id); ?>
						</td>
						<td>
							<a href="javascript:void(0);" data-mailer-preview data-id="<?php echo $row->id;?>"><?php echo JText::_($row->subject);?></a>
						</td>
						<td>
							<?php echo $row->recipient;?>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $row, 'spools', 'status'); ?>
						</td>
						<td class="center">
							<?php echo $this->html('string.date', $row->created, '', true); ?>
						</td>
						<td class="center">
							<?php echo $row->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="7" align="center" class="empty">
							<?php echo JText::_('COM_EASYBLOG_NO_MAILS');?>
						</td>
					</tr>
				<?php } ?>
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
	<input type="hidden" name="view" value="spools" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>