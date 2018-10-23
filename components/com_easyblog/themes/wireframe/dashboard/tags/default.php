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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=tags');?>" class="<?php echo !$tags ? 'is-empty' : '';?>" data-eb-dashboard-tags>
	<div class="eb-dashboard-header-wrapper">
		<div class="eb-dashboard-sticky-header" data-eb-spy="affix" data-offset-top="240" style="<?php echo 'top:' . $this->config->get('layout_dashboard_header_offset') . 'px'; ?>">
			<div class="eb-dashboard-entry-header">
				<?php echo $this->html('dashboard.heading', 'COM_EASYBLOG_DASHBOARD_TAGS', 'fa fa-tags', $this->acl->get('create_tag') || EB::isSiteAdmin() ? array('link' => EBR::_('index.php?option=com_easyblog&view=dashboard&layout=tagForm', false), 'text' => 'COM_EASYBLOG_ADD_TAG_BUTTON', 'icon' => 'fa fa-tag') : null); ?>

				<div class="eb-table-filter">
					<div class="eb-table-filter__cell">
						<div class="eb-table-filter__action hide" data-eb-table-actions>
							<div class="eb-filter-select-group eb-filter-select-group--inline mr-5">
								<select class="form-control" data-eb-table-task>
									<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
									<option value="tags.delete" data-confirmation="site/views/dashboard/confirmDeleteTag"><?php echo JText::_('COM_EASYBLOG_DELETE');?></option>
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
								<input type="text" class="form-control" name="tag-search" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEARCH_TAG');?>" value="<?php echo $this->html('string.escape', $search);?>" />
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
			<i class="eb-dashboard-empty__icon fa fa-tag"></i>
			<div class="eb-dashboard-empty__text">
				<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE');?></b>
				<p>
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE_HINT');?>
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
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>

				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_LANGUAGE');?>
				</td>

				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_POSTS');?>
				</td>
			</tr>
		</thead>

		<?php if ($tags) { ?>
		<tbody>
			<?php foreach ($tags as $tag) { ?>
			<tr>
				<td>
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $tag->id); ?>
				</td>
				<td>
					<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=tagForm&id=' . $tag->id, true);?>" class="post-title"><?php echo $tag->title;?></a>

					<ul class="post-actions" data-eb-actions data-id="<?php echo $tag->id;?>">
						<li>
							<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->id); ?>" target="_blank">
								<?php echo JText::_('COM_EASYBLOG_VIEW'); ?>
							</a>
						</li>
						<li>
							<a href="javascript:void(0);" class="text-danger" data-eb-action="site/views/dashboard/confirmDeleteTag" data-type="dialog">
								<i class="fa fa-trash"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DELETE'); ?>
							</a>
						</li>
					</ul>
				</td>

				<td class="text-center">
					<?php echo $tag->language == '' ? 'All' : $tag->language;?>
				</td>

				<td class="text-center">
					<?php echo $tag->post_count;?>
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

	<input type="hidden" name="return" value="<?php echo base64_encode(EBFactory::getURI(true));?>" data-table-grid-return />
	<input type="hidden" name="id" value="" data-table-grid-id />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="tags" />
	<?php echo $this->html('form.action'); ?>
</form>
