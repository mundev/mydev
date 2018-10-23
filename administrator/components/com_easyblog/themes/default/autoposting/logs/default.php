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
<form action="index.php?option=com_easyblog" method="post" name="adminForm" id="adminForm" data-grid-eb>

	<div class="app-filter-bar">
		<div class="app-filter-bar__cell">
			<select name="status" class="form-control" data-table-grid-filter>
				<option value="" <?php echo $status == "" ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_AUTOPOST_STATUS'); ?></option>
				<option value="success" <?php echo $status === 'success' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SUCCESSFUL'); ?></option>
				<option value="fail" <?php echo $status === 'fail' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FAILED'); ?></option>
			</select>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.limit', $limit); ?>
			</div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle table table-striped table-eb" data-table-grid>
			<thead>
				<tr>
					<th width="1%" class="nowrap hidden-phone text-center">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_EASYBLOG_BLOGS_BLOG_TITLE'); ?>
					</th>
					<th width="10%" class="text-center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATUS'); ?>
					</th>
					<th width="10%" class="text-center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_CLIENT'); ?>
					</th>
					<th width="20%" class="text-center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE'); ?>
					</th>
					<th width="1%" class="text-center">
						<?php echo JText::_('COM_EASYBLOG_ID'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($logs) { ?>
					<?php $i = 0; ?>
					<?php foreach ($logs as $log) { ?>
					<tr>
						<td class="text-center">
							<?php echo $this->html('grid.id', $i++, $log->id); ?>
						</td>
						<td>
							<?php echo $log->post->title;?>

							<?php if (!$log->status) { ?>
							<div class="small">
								<?php if ($log->response) { ?>
									<?php $response = json_decode($log->response); ?>

									<?php if (isset($response->error->message)) { ?>
										<?php echo $response->error->message;?>
									<?php } ?>
								<?php } ?>
							</div>
							<?php } ?>
						</td>
						<td class="text-center">
							<?php if ($log->status) { ?>
							<label class="label label-success"><?php echo JText::_('COM_EASYBLOG_SUCCESSFUL');?></label>
							<?php } ?>

							<?php if (!$log->status) { ?>
							<label class="label label-danger"><?php echo JText::_('COM_EASYBLOG_FAILED');?></label>
							<?php } ?>
						</td>
						<td class="text-center">
							<?php echo ucfirst($log->oauth->type); ?>
						</td>
						<td class="text-center">
							<?php echo $log->created;?>
						</td>
						<td class="text-center">
							<?php echo $log->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="6" class="empty">
						<div><?php echo JText::_('COM_EASYBLOG_AUTOPOST_LOGS_EMPTY'); ?>
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

	<input type="hidden" name="view" value="autoposting" />
	<input type="hidden" name="layout" value="logs" />
</form>
