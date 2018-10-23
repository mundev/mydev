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
<form method="post" action="<?php echo JRoute::_('index.php');?>" class=" <?php echo !$templates ? 'is-empty' : '';?>" data-eb-dashboard-templates>
	<div class="eb-dashboard-header-wrapper">
		<div class="eb-dashboard-sticky-header" data-eb-spy="affix" data-offset-top="240" style="<?php echo 'top:' . $this->config->get('layout_dashboard_header_offset') . 'px'; ?>">
			<div class="eb-dashboard-entry-header">
				<?php echo $this->html('dashboard.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_POST_TEMPLATES', 'fa fa-file-text-o', EB::isSiteAdmin() || $this->acl->get('create_post_templates') ? array('text' => 'COM_EASYBLOG_NEW_TEMPLATE', 'icon' => 'fa fa-file-text-o', 'link' => EBR::_('index.php?option=com_easyblog&view=templates&tmpl=component')) : null); ?>
				
				<div class="eb-table-filter">
					<div class="eb-table-filter__cell">
						<div class="eb-table-filter__action hide" data-eb-table-actions>
							<div class="eb-filter-select-group eb-filter-select-group--inline mr-5">
								<select class="form-control" data-eb-table-task>
									<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
									<option value="templates.publish"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></option>
									<option value="templates.unpublish"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></option>
									<option value="templates.delete" data-confirmation="site/views/templates/confirmDeleteTemplates"><?php echo JText::_('COM_EASYBLOG_DELETE');?></option>
								</select>

								<div class="eb-filter-select-group__drop"></div>
							</div>

							<a href="javascript:void(0);" class="btn btn-default" data-eb-table-apply><?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON');?></a>
						</div>
					</div>

					<div class="eb-table-filter__cell eb-table-filter__cell--filter">
						<div class="eb-table-filter__search-field">
							<div class="input-group">
								<input type="text" class="form-control" name="search" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEARCH_POSTS_TEMPLATES');?>" value="<?php echo $this->html('string.escape', $search);?>" />
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
				<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_EMPTY');?></b>
				<p>
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_EMPTY_HINT'); ?>
				</p>
				<?php if (EB::isSiteAdmin() || $this->acl->get('create_post_templates')) { ?>
					<div>
						<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=templates&tmpl=component');?>" class="btn btn-primary">
							<?php echo JText::_('COM_EASYBLOG_NEW_TEMPLATE');?>
						</a>
					</div>
				<?php } ?>	
			</div>
		</div>
	</div>

	<table class="eb-table table table-striped">
		<thead>
			<tr>
				<td width="1%">
					<?php echo $this->html('dashboard.checkall', $disabled); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>
				<td width="10%" class="text-center">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE');?>
				</td>
				<?php if (EB::isSiteAdmin()) { ?>
				<td width="20%" class="text-center">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_AUTHOR');?>
				</td>

				<td width="10%" class="text-center narrow-hide">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_GLOBAL');?>
				</td>
				<?php } ?>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($templates as $template) { ?>
			<tr data-eb-templates-item data-id="<?php echo $template->id;?>">
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $template->id, array('disabled' => $disabled)); ?>
				</td>
				<td>
					<a href="<?php echo $template->getEditLink();?>" class="post-title"><?php echo JText::_($template->title);?></a>

					<ul class="post-actions mt-5" data-eb-actions data-id="<?php echo $template->id;?>">
						<?php if ($this->acl->get('create_post_templates') && !$template->isBlank()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="templates.copy" data-type="form">
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_DUPLICATE'); ?>
							</a>
						</li>
						<?php } ?>
						
						<?php if ($template->canPublish()) { ?>
							<?php if ($template->published) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="templates.unpublish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_UNPUBLISH'); ?>
								</a>
							</li>
							<?php } else { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="templates.publish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_PUBLISH'); ?>
								</a>
							</li>
							<?php } ?>
							<?php if (!$template->isBlank()) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="site/views/templates/confirmDeleteTemplates" data-type="dialog" class="text-danger">
									<i class="fa fa-trash"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATE_DELETE'); ?>
								</a>
							</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</td>
				<td class="text-center">
					<?php if ($template->published) { ?>
						<span class="text-success"><?php echo JText::_('COM_EASYBLOG_STATE_PUBLISHED'); ?></span>
					<?php } else { ?>
						<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_STATE_UNPUBLISHED'); ?></span>
					<?php } ?>
				</td>
				<?php if (EB::isSiteAdmin()) { ?>
				<td class="text-center">
					<?php echo $template->getAuthor()->getName();?>
				</td>
				<td class="text-center narrow-hide">
					<?php echo $template->system;?>
				</td>
				<?php } ?>
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

	<input type="hidden" name="ids[]" value="" data-table-grid-id />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="templates" />
	<?php echo $this->html('form.action'); ?>
</form>
