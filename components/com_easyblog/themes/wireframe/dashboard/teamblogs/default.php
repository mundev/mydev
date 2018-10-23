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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs');?>" class="<?php echo !$teams ? 'is-empty' : '';?>" data-eb-dashboard-teams>
	<div class="eb-dashboard-header-wrapper">	
		<div class="eb-dashboard-sticky-header" data-eb-spy="affix" data-offset-top="240" style="<?php echo 'top:' . $this->config->get('layout_dashboard_header_offset') . 'px'; ?>">
			<div class="eb-dashboard-entry-header">
				<?php echo $this->html('dashboard.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_TEAMBLOGS', 'fa fa-users', $action); ?>

				<div class="eb-table-filter">
					<div class="eb-table-filter__cell">
						<div class="eb-table-filter__action hide" data-eb-table-actions>
							<div class="eb-filter-select-group eb-filter-select-group--inline mr-5">
								<select class="form-control" data-eb-table-task>
									<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
									<?php if (EB::isSiteAdmin()) { ?>
									<option value="teamblogs.publish"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></option>
									<option value="teamblogs.unpublish"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></option>
									<option value="teamblogs.delete" data-confirmation="site/views/teamblog/confirmDelete"><?php echo JText::_('COM_EASYBLOG_DELETE');?></option>
									<?php } else { ?>
									<option value="teamblogs.leave" data-confirmation="site/views/teamblog/confirmLeave"><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_LEAVE_TEAM');?></option>
									<?php } ?>
								</select>
								<div class="eb-filter-select-group__drop"></div>
							</div>

							<a href="javascript:void(0);" class="btn btn-default" data-eb-table-apply><?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?></a>
						</div>
					</div>

					<div class="eb-table-filter__cell eb-table-filter__cell--filter">
						<div class="eb-table-filter__search-field">
							<div class="input-group">
								<input type="text" class="form-control" name="search" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEARCH_TEAMBLOG');?>" value="<?php echo $this->html('string.escape', $search);?>" />
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
			<i class="eb-dashboard-empty__icon fa fa-users"></i>
			<div class="eb-dashboard-empty__text">
				<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TEAMS_AVAILABLE');?></b>
				<p>
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TEAMS_AVAILABLE_HINT');?>
				</p>
			</div>
		</div>
	</div>

	<table class="eb-table table table-striped ">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall'); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>
				<td width="10%" class="text-center">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE');?>
				</td>
				<td width="10%" class="text-center narrow-hide">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_MEMBERS');?>
				</td>
				<td width="20%" class="text-center narrow-hide">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ACCESS');?>
				</td>
			</tr>
		</thead>

		<?php if ($teams) { ?>
		<tbody>
			<?php foreach ($teams as $team) { ?>
			<tr data-eb-teamblogs-item data-id="<?php echo $team->id;?>">
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $team->id); ?>
				</td>
				<td>
					<a href="<?php echo $team->getEditPermalink();?>" class="post-title"><?php echo $team->getTitle();?></a>

					<ul class="post-actions" data-eb-actions data-id="<?php echo $team->id;?>">
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/teamblog/viewMembers" data-type="dialog">
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOG_VIEW_MEMBERS'); ?>
							</a>
						</li>
						
						<?php if ($team->isTeamAdmin()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/teamblog/inviteMembers" data-type="dialog">
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEABMLOG_INVITE_MEMBERS'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if (EB::isSiteAdmin()) { ?>
							<?php if ($team->isPublished()) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="teamblogs.unpublish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_UNPUBLISH'); ?>
								</a>
							</li>
							<?php } else { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="teamblogs.publish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_PUBLISH'); ?>
								</a>
							</li>
							<?php } ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/teamblog/confirmDelete" data-type="dialog" class="text-danger">
									<?php echo JText::_('COM_EASYBLOG_DELETE'); ?>
								</a>
							</li>
						<?php } else { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/teamblog/confirmLeave" data-type="dialog" class="text-danger">
									<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_LEAVE_TEAM'); ?>
								</a>
							</li>
						<?php } ?>
					</ul>
				</td>
				<td class="text-center">
					<?php if ($team->isPublished()) { ?>
						<span class="text-success"><?php echo JText::_('COM_EASYBLOG_STATE_PUBLISHED'); ?></span>
					<?php } else { ?>
						<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_STATE_UNPUBLISHED'); ?></span>
					<?php } ?>
				</td>
				<td class="text-center narrow-hide">
					<?php echo $team->getMembersCount();?>
				</td>
				<td class="text-center narrow-hide">
					<?php echo $team->getAccess();?>
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

	<input type="hidden" name="ids[]" value="" data-table-grid-id />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="teamblogs" />
	<?php echo $this->html('form.action'); ?>
</form>
