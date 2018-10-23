<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=reports');?>" class="eb-dashboard-entries <?php echo !$reports ? 'is-empty' : '';?>" data-eb-dashboard-posts>
	<div class="eb-dashboard-header-wrapper">
		<div class="eb-dashboard-sticky-header" data-eb-spy="affix" data-offset-top="240" style="<?php echo 'top:' . $this->config->get('layout_dashboard_header_offset') . 'px'; ?>">
			<div class="eb-dashboard-entry-header">
				<?php echo $this->html('dashboard.heading', 'COM_EB_REPORT_POSTS', 'fa fa-exclamation-triangle'); ?>

				<div class="eb-table-filter">
					<div class="eb-table-filter__cell">
						<div class="eb-table-filter__action hide" data-eb-table-actions>
							<div class="eb-filter-select-group eb-filter-select-group--inline mr-5">
								<select class="form-control" data-eb-table-task>
									<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
									<option value="reports.trash"><?php echo JText::_('COM_EASYBLOG_ADMIN_DELETE_ENTRY');?></option>
									<option value="reports.discard"><?php echo JText::_('COM_EB_DISCARD_BUTTON');?></option>
								</select>
								<div class="eb-filter-select-group__drop"></div>
							</div>

							<a class="btn btn-default pull-left" href="javascript:void(0);" data-eb-table-apply>
								<?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?>
							</a>
						</div>
					</div>

					<div class="eb-table-filter__cell eb-table-filter__cell--filter">
						<div class="eb-table-filter__search-field">
							<div class="input-group">
								<input type="text" class="form-control" name="post-search" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_FOR_POSTS');?>" value="<?php echo $this->html('string.escape', $search);?>" />
								<span class="input-group-btn">
									<a class="btn btn-default" href="javascript:void(0);" data-eb-form-search title="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEARCH_POSTS');?>">
										<i class="fa fa-search"></i>
									</a>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="eb-dashboard-empty">
		<div class="eb-dashboard-empty__content">
			<i class="eb-dashboard-empty__icon fa fa-align-left"></i>
			<div class="eb-dashboard-empty__text">
				<b><?php echo JText::_('COM_EB_EMPTY_REPORT_POSTS');?></b>
			</div>
		</div>
	</div>

	<table class="eb-table table table-striped table-hover">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EB_TABLE_COLUMN_REPORT_REASON');?>
				</td>
				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_REPORTED_BY');?>
				</td>
				<td class="text-center narrow-hide" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE');?>
				</td>				
			</tr>
		</thead>

		<tbody>
			<?php foreach ($reports as $report) { ?>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $report->id); ?>
				</td>

				<td>
					<div class="mb-10">
						<a href="<?php echo $report->getPermalink();?>" target="_blank"><?php echo $report->blog->title;?></a>
					</div>					
					<div>
						<?php echo $this->html('string.truncater', $report->reason, 250);?>
					</div>

					<ul class="post-actions mt-5" data-id="<?php echo $report->id;?>" data-eb-actions>

						<?php if ($report->blog->published) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmUnpublishPost" data-type="dialog"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></a>
							</li>
						<?php } ?>

						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmDeletePost" data-type="dialog" class="text-danger">
								<i class="fa fa-trash"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DELETE');?>
							</a>
						</li>
					</ul>
				</td>

				<td class="text-center">
					<?php if ($report->created_by == 0) { ?>
						<?php echo JText::_('COM_EASYBLOG_GUEST'); ?>
					<?php } else { ?>
						<?php echo $report->getAuthor()->getName();?>
					<?php } ?>
				</td>
				
				<td class="text-center narrow-hide">
					<?php echo $this->html('string.date', $report->created, 'DATE_FORMAT_LC4'); ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>

		<?php if ($pagination) { ?>
		<tfoot>
		<tr>
			<td colspan="6">
				<div class="eb-box-pagination pagination text-center">
					<?php echo $pagination->getPagesLinks(); ?>
				</div>
			</td>
		</tr>
		</tfoot>
		<?php } ?>
	</table>

	<input type="hidden" name="return" value="<?php echo base64_encode(EBFactory::getURI(true));?>" data-table-grid-return />
	<input type="hidden" name="ids[]" value="" data-table-grid-id />
	<input type="hidden" name="sort" value="" />
	<input type="hidden" name="ordering" value="" />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="reports" />
	<?php echo $this->html('form.action'); ?>
</form>
