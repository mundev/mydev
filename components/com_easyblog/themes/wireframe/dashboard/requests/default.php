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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=requests');?>" class="<?php echo !$requests ? 'is-empty' : '';?>" data-eb-dashboard-requests>
	<div class="eb-dashboard-header-wrapper">
		<div class="eb-dashboard-sticky-header" data-eb-spy="affix" data-offset-top="240" style="<?php echo 'top:' . $this->config->get('layout_dashboard_header_offset') . 'px'; ?>">
			<div class="eb-dashboard-entry-header">
				<?php echo $this->html('dashboard.heading', 'COM_EASYBLOG_DASHBOARD_TOOLBAR_TEAM_REQUESTS', 'fa fa-users'); ?>

				<div class="eb-table-filter">
					<div class="eb-table-filter__cell">
						<div class="eb-table-filter__action hide" data-eb-table-actions>
							<div class="eb-filter-select-group eb-filter-select-group--inline mr-5">
								<select class="form-control" data-eb-table-task>
									<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
									<option value="teamblogs.approve"><?php echo JText::_('COM_EASYBLOG_APPROVE');?></option>
									<option value="teamblogs.reject"><?php echo JText::_('COM_EASYBLOG_REJECT');?></option>
								</select>
								<div class="eb-filter-select-group__drop"></div>
							</div>

							<a class="btn btn-default" href="javascript:void(0);" data-eb-table-apply>
								<?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?>
							</a>
						</div>
					</div>

					<div class="eb-table-filter__cell eb-table-filter__cell--filter">
						<div class="eb-table-filter__search-field">
							<div class="input-group">
								<input type="text" class="form-control" name="requests-search" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH');?>" value="<?php echo $this->html('string.escape', $search);?>" />
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
			<i class="eb-dashboard-empty__icon fa fa-comments"></i>
			<div class="eb-dashboard-empty__text">
				<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_REQUESTS_EMPTY');?></b>
				<p>
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_REQUESTS_EMPTY_HINT');?>
				</p>
			</div>
		</div>
	</div>

	<table class="eb-table table table-striped table-hover mt-20">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_USER');?>
				</td>
				<td class="text-center" width="40%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TEAM');?>
				</td>
				<td class="text-center narrow-hide" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE');?>
				</td>
			</tr>
		</thead>

		<?php if ($requests) { ?>
		<tbody>
			<?php foreach ($requests as $request) { ?>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $request->id); ?>
				</td>
				<td>
					<div>
						<?php echo $request->user->getName();?>
					</div>

					<ul class="post-actions mt-5" data-id="<?php echo $request->id;?>" data-eb-actions>
						<li>
							<a href="javascript:void(0);" data-eb-action="teamblogs.approve" data-type="form"><?php echo JText::_('COM_EASYBLOG_APPROVE');?></a>
						</li>
						<li>
							<a href="javascript:void(0);" data-eb-action="teamblogs.reject" data-type="form" class="text-danger"><?php echo JText::_('COM_EASYBLOG_REJECT');?></a>
						</li>
					</ul>
				</td>
				<td class="text-center">
					<a href="<?php echo $request->team->getPermalink();?>"><?php echo $request->team->title;?></a>
				</td>
				<td class="text-center narrow-hide">
					<?php echo $request->date->format(JText::_('DATE_FORMAT_LC3')); ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
		<?php } ?>

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

	<?php echo $this->html('form.action'); ?>

	<input type="hidden" name="return" value="<?php echo base64_encode(EBFactory::getURI(true));?>" data-table-grid-return />
	<input type="hidden" name="ids[]" value="" data-table-grid-id />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="requests" />
</form>
