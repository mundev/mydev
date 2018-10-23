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
<form method="post" action="<?php echo JRoute::_('index.php');?>" class="<?php echo !$categories ? 'is-empty' : ''; ?>" data-eb-dashboard-categories>
	<div class="eb-dashboard-header-wrapper">
		<div class="eb-dashboard-sticky-header" data-eb-spy="affix" data-offset-top="240" style="<?php echo 'top:' . $this->config->get('layout_dashboard_header_offset') . 'px'; ?>">
			<div class="eb-dashboard-entry-header">
				<?php echo $this->html('dashboard.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_CATEGORIES', 'fa fa-folder-open-o', array('icon' => 'fa fa-folder-o', 'text' => 'COM_EASYBLOG_DASHBOARD_CATEGORIES_CREATE', 'link' => EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categoryForm', false) )); ?>

				<div class="eb-table-filter">
					<div class="eb-table-filter__cell" >
						<div class="eb-table-filter__action hide" data-eb-table-actions>
							<div class="eb-filter-select-group eb-filter-select-group--inline mr-5">
								<select class="form-control" data-eb-table-task>
									<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
									<option value="categories.publish" data-confirmation="site/views/categories/confirmPublishCategory"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></option>
									<option value="categories.unpublish" data-confirmation="site/views/categories/confirmUnpublishCategory"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></option>
									<?php if ($this->acl->get('delete_category') || EB::isSiteAdmin()) { ?>
									<option value="categories.delete" data-confirmation="site/views/categories/confirmDeleteCategory"><?php echo JText::_('COM_EASYBLOG_DELETE');?></option>
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
								<input type="text" class="form-control" name="search" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEARCH_CATEGORY');?>" value="<?php echo $this->html('string.escape', $search);?>" />
								<span class="input-group-btn">
									<a class="btn btn-default" href="javascript:void(0);" data-eb-form-search>
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
				<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_EMPTY');?></b>
				<p>
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_EMPTY_HINT'); ?>
				</p>
				<div>
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categoryForm', false);?>" class="btn btn-primary">
						<?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_CREATE');?>
					</a>
				</div>
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
				<td class="text-center" width="10%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DEFAULT');?>
				</td>				
				<td class="text-center" width="10%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE');?>
				</td>
				<td class="text-center narrow-hide" width="10%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_POSTS');?>
				</td>
				<td class="text-center narrow-hide" width="10%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_SUBCATEGORIES');?>
				</td>
			</tr>
		</thead>

		<?php if ($categories) { ?>
		<tbody>
			<?php foreach ($categories as $category) { ?>
			<tr data-eb-actions data-id="<?php echo $category->id; ?>">
				<td>
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $category->id); ?>
				</td>
				<td>
					<a href="<?php echo $category->getEditPermalink();?>" class="post-title"><?php echo $category->getTitle();?></a>

					<ul class="post-actions mt-5">
						<li>
							<a href="<?php echo $category->getPermalink();?>" target="_blank">
								<?php echo JText::_('COM_EASYBLOG_VIEW'); ?>
							</a>
						</li>
						<?php if (!$category->isDefault()) { ?>
						<li>
							<?php if ($category->published) { ?>
							<a href="javascript:void(0)" data-eb-action="site/views/categories/confirmUnpublishCategory" data-type="dialog"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH'); ?></a>
							<?php } else { ?>
							<a href="javascript:void(0)" data-eb-action="site/views/categories/confirmPublishCategory" data-type="dialog"><?php echo JText::_('COM_EASYBLOG_PUBLISH'); ?></a>
							<?php } ?>
						</li>
						<?php } ?>
						<?php if ($category->canDelete()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/categories/confirmDeleteCategory" data-type="dialog" class="text-danger">
								<i class="fa fa-trash"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DELETE'); ?>
							</a>						
						</li>
						<?php } ?>
					</ul>
				</td>
				<td class="text-center">
					<?php if (EB::isSiteAdmin()) { ?>
					<a class="eb-star-<?php echo $category->isDefault() ? 'featured' : 'default'; ?>" href="javascript:void(0)" title="<?php echo JText::_('COM_EASYBLOG_MAKE_DEFAULT_BUTTON'); ?>" data-eb-action="site/views/categories/confirmSetDefault" data-type="dialog">
						<i class="fa fa-star"></i>
					</a>
					<?php } else { ?>
					<span class="eb-star-<?php echo $category->isDefault() ? 'featured' : 'default'; ?>">
						<i class="fa fa-star"></i>
					</span>
					<?php } ?>
				</td>
				<td class="text-center">
					<?php if ($category->published) { ?>
						<span class="text-success"><?php echo JText::_('COM_EASYBLOG_STATE_PUBLISHED'); ?></span>
					<?php } else { ?>
						<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_STATE_UNPUBLISHED'); ?></span>
					<?php } ?>
				</td>
				<td class="text-center narrow-hide">
					<?php echo $category->getPostCount();?>
				</td>
				<td class="text-center narrow-hide">
					<?php echo $category->getChildCount();?>
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

	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="categories" />
	<?php echo $this->html('form.action'); ?>
</form>
