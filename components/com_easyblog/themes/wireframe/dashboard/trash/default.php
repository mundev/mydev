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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=entries');?>" class="eb-dashboard-entries" data-eb-dashboard-posts>
	<div class="eb-dashboard-header-wrapper">
		<div class="eb-dashboard-sticky-header" data-eb-spy="affix" data-offset-top="240" style="<?php echo 'top:' . $this->config->get('layout_dashboard_header_offset') . 'px'; ?>">
			<div class="eb-dashboard-entry-header">

				<?php echo $this->html('dashboard.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_TRASHED_POSTS', 'fa fa-trash'); ?>

				<div class="eb-table-filter">
					<div class="eb-table-filter__cell">
						<div class="eb-table-filter__action hide" data-eb-table-actions>
							<div class="eb-filter-select-group eb-filter-select-group--inline mr-5">
								<select class="form-control" data-eb-table-task>
									<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
									<option value="posts.restore"><?php echo JText::_('COM_EASYBLOG_RESTORE');?></option>

									<?php if( $this->acl->get('delete_entry') ){ ?>
									<option value="posts.deletePermanent" data-confirmation="site/views/dashboard/confirmPermanentDelete"><?php echo JText::_('COM_EASYBLOG_DELETE_PERMANENT');?></option>
									<?php } ?>
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
								<input type="text" class="form-control" name="post-search" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_FOR_POSTS');?>" value="<?php echo $this->html('string.escape', $search);?>" />
								<span class="input-group-btn">
									<a class="btn btn-default" href="javascript:void(0);" data-eb-form-search title="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SEARCH_POSTS');?>">
										<i class="fa fa-search"></i>
									</a>
								</span>
							</div>
						</div>
						<div class="eb-filter-select-groups">
							<?php echo $this->html('dashboard.filters', $state); ?>

							<div class="eb-filter-select-group eb-filter-select-group--inline">
								<?php echo $categoryDropdown; ?>
								<div class="eb-filter-select-group__drop"></div>
							</div>
						</div>
					</div>
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
				<td class="text-center" width="5%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_HITS');?>
				</td>
				<td class="text-center" width="15%">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE');?>
				</td>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($posts as $post) { ?>
			<tr data-eb-post-item data-id="<?php echo $post->id;?>">
				<td width="1%">
					<?php echo $this->html('dashboard.checkbox', 'ids[]', $post->id); ?>
				</td>
				<td>
					<a href="<?php echo $post->getEditLink();?>" class="post-title"><?php echo $post->getTitle();?></a>

					<div class="post-meta">
						<span>
							<a href="<?php echo $post->getAuthorPermalink();?>"><?php echo $post->getAuthorName();?></a>
						</span>

						<span>
							<?php foreach ($post->getCategories() as $category) { ?>
								<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
							<?php } ?>
						</span>

						<?php if ($post->language != '*' && $post->language) { ?>
						<span>
							<i class="fa fa-language"></i>&nbsp; <?php echo $post->language;?>
						</span>
						<?php } ?>
					</div>

					<ul class="post-actions" data-eb-actions data-id="<?php echo $post->id;?>">

						<li>
							<a href="javascript:void(0);" data-eb-action="posts.restore" data-type="form">
								<?php echo JText::_('COM_EASYBLOG_RESTORE'); ?>
							</a>
						</li>

						<?php if ($this->acl->get('delete_entry')) { ?>
						<li>
							<a href="javascript:void(0);" class="text-danger" data-eb-action="site/views/dashboard/confirmPermanentDelete" data-type="dialog">
								<?php echo JText::_('COM_EASYBLOG_DELETE_PERMANENT'); ?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</td>

				<td class="text-center">
					<?php echo $post->hits;?>
				</td>

				<td class="text-center">
					<?php echo $post->getCreationDate()->format(JText::_('Y-m-d H:i'));?>
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

	<div class="eb-box empty text-center<?php echo !$posts ? ' is-empty' : '';?>">
		<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TRASH_EMPTY'); ?></b>
	</div>

	<input type="hidden" name="return" value="<?php echo $return;?>" data-table-grid-return />
	<input type="hidden" name="view" value="dashboard" />
	<input type="hidden" name="layout" value="entries" />
	<input type="hidden" name="id" value="" data-table-grid-id />
	<?php echo $this->html('form.action'); ?>
</form>
