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
<form action="index.php?option=com_easyblog" method="post" name="adminForm" id="adminForm" data-grid-eb>

	<div class="app-filter-bar">
		<div class="app-filter-bar__cell">
			<?php echo $this->html('filter.search', $search, 'search', 'COM_EB_SEARCH_TOOLTIP_POSTS'); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.custom', $this->getFilterState($filter_state)); ?>
			</div>
		</div>

		<?php if ($filter_state == 'date') { ?>
		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-date">
				<a href="javascript:void(0);" class="dropdown-toggle_ app-filter-bar__btn-date pull-right" data-date-range>
					<span>
						<i class="fa fa-calendar"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SELECT_DATE');?>
					</span>
				</a>
			</div>
		</div>
		<?php } ?>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.custom', $this->getFilterCategory($filter_category)); ?>
			</div>
		</div>

		<?php if ($filter_state != 'date') { ?>
		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.custom', $this->getFilterBlogger($filterBlogger)); ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.custom', $this->getFilterLanguage($filterLanguage)); ?>
			</div>
		</div>
		<?php } ?>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.custom', $this->getFilterPosttype($filter_posttype)); ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<?php if ($filter_state != 'date') { ?>
		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left t-text--center">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.custom', $this->getFilterSortBy($filterSortBy)); ?>
			</div>
		</div>
		<?php } ?>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.limit', $limit); ?>
			</div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle table table-striped table-eb table-hover" data-table-grid>
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>

					<th>
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_BLOGS_BLOG_TITLE'), 'a.title', $orderDirection, $order); ?>
					</th>

					<?php if (!$browse) { ?>

						<th class="nowrap hidden-phone text-center" width="15%">
							<?php echo JText::_('COM_EASYBLOG_BLOGS_ACTIONS'); ?>
						</th>

						<th class="nowrap hidden-phone center" width="5%">
							<?php echo JText::_('COM_EASYBLOG_BLOGS_FEATURED'); ?>
						</th>

						<th class="nowrap hidden-phone center" width="5%">
							<?php echo JText::_('COM_EASYBLOG_STATUS'); ?>
						</th>

						<th class="nowrap hidden-phone center" width="5%">
							<?php echo JText::_('COM_EASYBLOG_BLOGS_FRONTPAGE'); ?>
						</th>

						<th class="center" width="10%">
							<?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_AUTHOR', 'a.created_by', $orderDirection, $order); ?>
						</th>

						<th width="5%" class="nowrap center hidden-phone">
							<?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_DATE', 'a.created', $orderDirection, $order); ?>
						</th>

					<?php } ?>

					<?php if (!$browse) { ?>
					
						<th class="nowrap hidden-phone center" width="5%">
							<?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_BLOGS_HITS', 'a.hits', $orderDirection, $order); ?>
						</th>

						<th width="1%" nowrap="nowrap center">
							<?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_ID', 'a.id', $orderDirection, $order); ?>
						</th>
					
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php if ($blogs) { ?>
					<?php $i = 0; ?>

					<?php foreach ($blogs as $row) { ?>
					<tr
						data-item
						data-id="<?php echo $row->id;?>"
						data-title="<?php echo $row->title;?>"
					>
						<td class="center hidden-iphone" valign="top">
							<?php echo $this->html('grid.id', $i++ , $row->id); ?>
						</td>

						<td class="nowrap has-context">

							<div style="max-width: 280px; overflow: hidden; white-space: nowrap;text-overflow: ellipsis;">
								<?php if ($row->isFromFeed()) { ?>
									<i class="fa fa-rss-square" data-eb-provide="tooltip" data-title="<?php echo JText::_('COM_EASYBLOG_BLOG_POST_IS_IMPORTED_FROM_FEEDS', true);?>" data-placement="bottom"></i>&nbsp;
								<?php } ?>

								<?php if ($browse) { ?>
									<a href="javascript:void(0);" data-post-title><?php echo $row->title;?></a>
								<?php } else { ?>
									<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $row->id . '.' . $row->revision_id)); ?>"><?php echo $row->title; ?></a>
								<?php } ?>
							</div>

							<div class="mt-5 eb-table-meta">

								<?php foreach ($row->getCategories() as $category) { ?>
								<span class="mr-10">
									<i class="fa fa-folder text-muted"></i>&nbsp; <?php echo $category->getTitle(); ?>
								</span>
								<?php }?>

								<span class="mr-10">
									<i class="fa fa-flag text-muted"></i>&nbsp;
									<?php if ($row->language=='*' || empty( $row->language) ){ ?>
										<?php echo JText::alt('JALL', 'language'); ?>
									<?php } else { ?>
										<?php echo $this->escape($this->getLanguageTitle($row->language)); ?>
									<?php } ?>
								</span>

								<span class="mr-10">
									<i class="fa fa-sitemap"></i> <?php echo $row->contributionDisplay;?>
								</span>

								<?php if ($row->locked) { ?>
								<span>
									<i class="fa fa-lock text-muted" data-eb-provide="tooltip" data-title="<?php echo JText::_('COM_EASYBLOG_BLOG_POST_IS_LOCKED');?>" data-placement="bottom"></i> <?php echo JText::_('COM_EASYBLOG_POST_LOCKED');?>
								</span>
								<?php } ?>
							</div>

							<!-- Only render this on the mobile device -->
							<div class="mt-5 hidden-desktop">
							    <div class="btn-group">
									<a class="btn btn-default btn-sm" data-notify-item data-blog-id="<?php echo $row->id;?>" data-eb-provide="tooltip" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGS_NOTIFY_TOOLTIP');?>">
										<i class="fa fa-envelope fa-14"></i>
									</a>

									<?php if ($row->isPublished() && $centralizedConfigured) { ?>
										<?php foreach ($consumers as $consumer) { ?>
											<?php if ($this->acl->get('update_' . $consumer->type)) { ?>
											<a class="btn btn-social btn-<?php echo $consumer->type;?> btn-sm text-center <?php echo $consumer->isShared($row->id) ? ' is-sent' : '';?>"
												href="javascript:void(0);"
												data-post-autopost
												data-id="<?php echo $row->id;?>"
												data-type="<?php echo $consumer->type;?>"
												data-eb-provide="tooltip"
												data-original-title="<?php echo $consumer->isShared($row->id) ? JText::sprintf('COM_EASYBLOG_AUTOPOST_SHARED', $consumer->type) : JText::sprintf('COM_EASYBLOG_AUTOPOST_NOT_SHARED_YET', $consumer->type);?>"
											>
												<i class="fa fa-<?php echo $consumer->type;?> fa-14"></i>
											</a>
											<?php } ?>
										<?php } ?>
									<?php } ?>
							    </div>
							</div>
						</td>

						<td class="center hidden-phone small">
							<div class="btn-group">
								<a class="btn btn-default btn-sm" data-notify-item data-blog-id="<?php echo $row->id;?>" data-eb-provide="tooltip" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGS_NOTIFY_TOOLTIP');?>">
									<i class="fa fa-envelope fa-14"></i>
								</a>

								<?php if ($row->isPublished() && $centralizedConfigured) { ?>
									<?php foreach ($consumers as $consumer) { ?>
										<?php if ($this->acl->get('update_' . $consumer->type)) { ?>
										<a class="btn btn-social btn-<?php echo $consumer->type;?> btn-sm text-center <?php echo $consumer->isShared($row->id) ? ' is-sent' : '';?>"
											href="javascript:void(0);"
											data-post-autopost
											data-id="<?php echo $row->id;?>"
											data-type="<?php echo $consumer->type;?>"
											data-eb-provide="tooltip"
											data-original-title="<?php echo $consumer->isShared($row->id) ? JText::sprintf('COM_EASYBLOG_AUTOPOST_SHARED', $consumer->type) : JText::sprintf('COM_EASYBLOG_AUTOPOST_NOT_SHARED_YET', $consumer->type);?>"
										>
											<i class="fa fa-<?php echo $consumer->type;?> fa-14"></i>
										</a>
										<?php } ?>
									<?php } ?>
								<?php } ?>
							</div>
						</td>

						<?php if (!$browse) { ?>
						<td class="nowrap hidden-phone center">
							<?php echo $this->html('grid.featured', $row, 'blogs', 'featured', array('blogs.feature', 'blogs.unfeature')); ?>
						</td>

						<td class="nowrap hidden-phone center">
							<?php echo $this->html('grid.published', $row, 'blogs', 'published'); ?>
						</td>

						<td class="nowrap hidden-phone center">
							<?php echo $this->html('grid.published', $row, 'blogs', 'frontpage', array('blogs.setFrontpage', 'blogs.removeFrontpage')); ?>
						</td>
						<?php } ?>

						<td class="center">
							<?php if (!$browse) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo JFactory::getUser($row->created_by)->name;?></a>
							<?php } else { ?>
								<?php echo JFactory::getUser($row->created_by)->name;?>
							<?php } ?>
						</td>

						<?php if (!$browse) { ?>
						<td class="text-center">
							<?php echo EB::date($row->created, true)->format(JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						
						<td class="nowrap hidden-phone text-center">
							<?php echo $row->hits;?>
						</td>

						<td class="text-center">
							<?php echo $row->id; ?>
						</td>
						<?php } ?>
					</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
				<tr>
					<td colspan="15" class="empty">
						<?php echo JText::_('COM_EASYBLOG_BLOGS_NO_ENTRIES');?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="15" class="text-center">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php if( $browse ){ ?>
	<input type="hidden" name="tmpl" value="component" />
	<?php } ?>

	<input type="hidden" name="filter_start_date" value="<?php echo $startDate;?>" data-date-start />
	<input type="hidden" name="filter_end_date" value="<?php echo $endDate;?>" data-date-end />

	<input type="hidden" name="autopost_type" value="" />
	<input type="hidden" name="autopost_selected" value="" />
	<input type="hidden" name="move_category_id" value="" data-move-category />
	<input type="hidden" name="move_author_id" value="" data-move-author />
	<input type="hidden" name="mass_assign_tags" value="" data-assign-tags />
	<input type="hidden" name="browse" value="<?php echo $browse;?>" />
	<input type="hidden" name="browseFunction" value="<?php echo $browseFunction;?>" />
	<input type="hidden" name="view" value="blogs" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $orderDirection; ?>" />
	
	<?php echo $this->html('form.action'); ?>
</form>

<?php if ($filter_state != 'T') { ?>
	<?php echo $this->output('admin/blogs/actions'); ?>
<?php } ?>