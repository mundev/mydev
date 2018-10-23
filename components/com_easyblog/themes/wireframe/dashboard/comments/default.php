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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=comments');?>" class="eb-dashboard-comments <?php echo !$comments ? 'is-empty' : '';?>" data-eb-dashboard-comments>
	<div class="eb-dashboard-header-wrapper">
		<div class="eb-dashboard-sticky-header" data-eb-spy="affix" data-offset-top="240" style="<?php echo 'top:' . $this->config->get('layout_dashboard_header_offset') . 'px'; ?>">
			<div class="eb-dashboard-entry-header">
				<?php echo $this->html('dashboard.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_COMMENTS', 'fa fa-comments'); ?>

				<div class="eb-table-filter">
					<div class="eb-table-filter__cell">
						<div class="b-table-filter__action hide" data-eb-table-actions>
							<div class="eb-filter-select-group eb-filter-select-group--inline mr-5">
								<select class="form-control" data-eb-table-task>
									<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
									<?php if ($this->acl->get('manage_comment')) { ?>
									<option value="comments.publish"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></option>
									<option value="comments.unpublish"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></option>
									<?php } ?>

									<?php if ($this->acl->get('delete_comment')) { ?>
									<option value="comments.delete" data-confirmation="site/views/dashboard/confirmDeleteComment"><?php echo JText::_('COM_EASYBLOG_DELETE');?></option>
									<?php } ?>
								</select>
								<div class="eb-filter-select-group__drop"></div>
							</div>

							<a href="javascript:void(0);" class="btn btn-default pull-left" data-eb-table-apply><?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?></a>
						</div>
					</div>

					<div class="eb-table-filter__cell eb-table-filter__cell--filter">
						<div class="eb-table-filter__search-field">
							<div class="input-group">
								<input type="text" class="form-control" name="post-search" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEARCH_COMMENTS');?>" value="<?php echo $this->html('string.escape', $search);?>" 
									data-eb-filter-input />
								<span class="input-group-btn">
									<a class="btn btn-default" href="javascript:void(0);" data-eb-filter-button>
										<i class="fa fa-search"></i>
									</a>
								</span>
							</div>
						</div>
						<div class="eb-filter-select-group eb-filter-select-group--inline mr-5">
							<select class="form-control" name="filter" data-eb-filter-dropdown>
								<option value="all"<?php echo $filter == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_SELECT_FILTER');?></option>
								<option value="published"<?php echo $filter == 'published' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_PUBLISHED');?></option>
								<option value="unpublished"<?php echo $filter == 'unpublished' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_UNPUBLISHED');?></option>
								<option value="moderate"<?php echo $filter == 'moderate' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_PENDING');?></option>
							</select>
							<div class="eb-filter-select-group__drop"></div>
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
				<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_COMMENTS_EMPTY');?></b>
				<p>
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_COMMENTS_EMPTY_HINT');?>
				</p>
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
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_COMMENT');?>
				</td>
				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE');?>
				</td>
				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_AUTHOR');?>
				</td>
				<td class="text-center narrow-hide" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE');?>
				</td>
			</tr>
		</thead>
		
		<?php if ($comments) { ?>
		<tbody>
			<?php foreach ($comments as $comment) { ?>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $comment->id); ?>
				</td>
				<td>
					<div>
						<?php echo $comment->getContent();?>
					</div>

					<ul class="post-actions mt-5" data-id="<?php echo $comment->id;?>" data-eb-actions>
						<?php if ($this->acl->get('manage_comment') && $comment->isPublished()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmUnpublishComment" data-type="dialog"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></a>
						</li>
						<?php } ?>

						<?php if ($this->acl->get('manage_comment') && $comment->isUnpublished()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmPublishComment" data-type="dialog"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></a>
						</li>
						<?php } ?>

						<?php if ($comment->isPublished()) { ?>
						<li>
							<a href="<?php echo $comment->getPermalink();?>" target="_blank"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_VIEW_COMMENT');?></a>
						</li>
						<?php } ?>

						<?php if ($this->acl->get('delete_comment') ) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmDeleteComment" data-type="dialog" class="text-danger">
								<i class="fa fa-trash"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DELETE');?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</td>
				<td class="text-center">
					<?php if ($comment->isPublished()) { ?>
						<span class="text-success"><?php echo JText::_('COM_EASYBLOG_STATE_PUBLISHED'); ?></span>
					<?php } ?>

					<?php if ($comment->isModerated()) { ?>
						<span class="text-info"><?php echo JText::_('COM_EASYBLOG_STATE_PENDING'); ?></span>
					<?php } ?>

					<?php if (!$comment->isPublished() && !$comment->isModerated()) { ?>
						<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_STATE_UNPUBLISHED'); ?></span>
					<?php } ?>
				</td>
				<td class="text-center">
					<?php echo $comment->getAuthorName();?>
				</td>
				
				<td class="text-center narrow-hide">
					<?php echo $this->html('string.date', $comment->created, 'DATE_FORMAT_LC4'); ?>
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
	
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="comments" />

	<?php echo $this->html('form.action', ''); ?>
</form>
