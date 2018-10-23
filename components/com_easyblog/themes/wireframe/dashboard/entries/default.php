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
<form method="post" action="<?php echo JRoute::_('index.php?option=com_easyblog&view=dashboard&layout=entries');?>" class="eb-dashboard-entries <?php echo !$posts ? 'is-empty' : '';?>" data-eb-dashboard-posts>
	<div class="eb-dashboard-header-wrapper">
		<div class="eb-dashboard-sticky-header" data-eb-spy="affix" data-offset-top="240" style="<?php echo 'top:' . $this->config->get('layout_dashboard_header_offset') . 'px'; ?>">
			<div class="eb-dashboard-entry-header">
				<?php echo $this->html('dashboard.heading', 'COM_EASYBLOG_DASHBOARD_HEADING_POSTS', 'fa fa-file-text-o', array('icon' => 'fa fa-pencil-square-o', 'text' => 'COM_EASYBLOG_NEW_POST', 'link' => EB::composer()->getComposeUrl())); ?>

				<div class="eb-table-filter">
					<div class="eb-table-filter__cell">
						<div class="eb-table-filter__action hide" data-eb-table-actions>
							<div class="eb-filter-select-group eb-filter-select-group--inline mr-5">
								<select class="form-control" data-eb-table-task>
									<option value=""><?php echo JText::_('COM_EASYBLOG_BULK_ACTIONS');?></option>
									<option value="posts.copy"><?php echo JText::_('COM_EASYBLOG_COPY_SELECTED');?></option>

									<?php if( $this->acl->get('publish_entry') ){ ?>
									<option value="posts.publish"><?php echo JText::_('COM_EASYBLOG_PUBLISH');?></option>
									<option value="posts.unpublish"><?php echo JText::_('COM_EASYBLOG_UNPUBLISH');?></option>
									<?php } ?>

									<?php if( $this->acl->get('delete_entry') ){ ?>
									<option value="posts.trash"><?php echo JText::_('COM_EASYBLOG_TRASH');?></option>
									<?php } ?>
								</select>
								<div class="eb-filter-select-group__drop"></div>
							</div>

							<a class="btn btn-default " href="javascript:void(0);" data-eb-table-apply>
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

	<div class="eb-dashboard-empty">
		<div class="eb-dashboard-empty__content">
			<i class="eb-dashboard-empty__icon fa fa-align-left"></i>
			<div class="eb-dashboard-empty__text">
				<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_EMPTY_POSTS');?></b>
				<p>
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_EMPTY_POSTS_HINT'); ?>
				</p>
				<div>
					<a href="<?php echo EB::composer()->getComposeUrl();?>" class="btn btn-primary">
						<?php echo JText::_('COM_EASYBLOG_NEW_POST');?>
					</a>
				</div>
			</div>
		</div>
	</div>

	<table class="eb-table table table-striped table-hover">
		<thead>
			<tr>
				<td width="1%">
					<?php
						$disabled = false;
						if ($isPendingState && !$isModerator) {
							$disabled = true;
						}
					?>
					<?php echo $this->html('dashboard.checkall', $disabled); ?>
				</td>
				<td>
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
				</td>
				<td width="15%" class="text-center center narrow-hide">
					<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_STATE');?>
				</td>
				<td width="10%" class="text-center narrow-hide">
					<?php echo $this->html('dashboard.sort', JText::_('COM_EASYBLOG_TABLE_COLUMN_HITS'), 'hits', $ordering, $sort); ?>
				</td>
				<td width="15%" class="text-center">
					<?php echo $this->html('dashboard.sort', JText::_('COM_EASYBLOG_TABLE_COLUMN_DATE'), 'created', $ordering, $sort); ?>
				</td>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($posts as $post) { ?>
			<tr data-eb-post-item data-id="<?php echo $post->id;?>" class="<?php echo $post->isPending() ? 'is-pending': ''; ?>">
				<td width="1%">
					<?php
						$disabled = false;
						if ($post->isPending() && !$isModerator) {
							$disabled = true;
						}
					?>

					<?php echo $this->html('dashboard.checkbox', 'ids[]', $post->id, array('disabled' => $disabled)); ?>
				</td>
				<td>
					<?php if (!$post->isPending() || EB::isSiteAdmin()) { ?>
						<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $post->id . '.' . $post->revision_id));?>" class="post-title"><?php echo $post->getTitle();?></a>
					<?php } else { ?>
						<label class="post-title"><?php echo $post->getTitle();?></label>
					<?php } ?>

					<?php if ($post->isScheduled()) { ?>
					<div class="post-meta">
						<i><?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_ENTRIES_POST_IS_SCHEDULED_DESC', $post->getPublishDate(true)->format(JText::_('DATE_FORMAT_LC2'))); ?></i>
					</div>
					<?php } ?>

					<div class="post-meta">

						<?php if ($post->isTeamBlog()) { ?>
						<span>
							<a href="<?php echo $post->getBlogContribution()->getPermalink();?>"><?php echo $post->getBlogContribution()->getTitle();?></a>
						</span>
						<?php } ?>

						<span>
							<a href="<?php echo $post->creator->getPermalink();?>"><?php echo $post->getAuthorName();?></a>
						</span>

						<span>
							<?php foreach ($post->categories as $category) { ?>
								<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
							<?php } ?>
						</span>

						<?php if ($post->language != '*' && $post->language) { ?>
						<span>
							<i class="fa fa-language"></i>&nbsp; <?php echo $post->language;?>
						</span>
						<?php } ?>

						<?php if ($post->isRejected) { ?>
						<span class="text-error">
							<?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED_TITLE'); ?>
						</span>
						<?php } ?>
					</div>

					<?php if ($post->isRejected) { ?>
					<div class="mt-5">
						<b><u><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED_REASON');?></u></b>: <?php echo $post->isRejected->message;?>
					</div>
					<?php } ?>

					<?php if ($this->isMobile()) { ?>
						<?php if ($post->isPublished()) { ?>
							<span class="text-success"><?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?></span>
						<?php } ?>

						<?php if ($post->isUnpublished()) { ?>
							<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_UNPUBLISHED'); ?></span>
						<?php } ?>

						<?php if ($post->isScheduled()) { ?>
							<span class="text-info"><?php echo JText::_('COM_EASYBLOG_SCHEDULED'); ?></span>
						<?php } ?>

						<?php if ($post->isPending()) { ?>
							<span class="text-warning"><?php echo JText::_('COM_EASYBLOG_UNDER_REVIEW'); ?></span>
						<?php } ?>

						<?php if ($post->isDraft()) { ?>
							<span><?php echo JText::_('COM_EASYBLOG_DRAFT'); ?></span>
						<?php } ?>

						<?php if ($post->isRejected) { ?>
								<p><span class="label label-danger"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED_TITLE');?></span></p>
								<b><u><?php echo JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_REJECTED_REASON');?></u></b>
								<div><?php echo $post->isRejected->message;?></div>
						<?php } ?>
					<?php } ?>

					<ul class="post-actions" data-eb-actions data-id="<?php echo $post->id;?>">
						<?php if ($post->isPublished()) { ?>
						<li>
							<a href="<?php echo $post->getPermalink();?>" target="_blank">
								<?php echo JText::_('COM_EASYBLOG_VIEW'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if ($post->isDraft() || $post->isPending()) { ?>
						<li>
							<a href="<?php echo $post->getPreviewLink();?>" target="_blank">
								<?php echo JText::_('COM_EASYBLOG_PREVIEW'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if ($post->isPublished() && ($this->acl->get('publish_entry') || EB::isSiteAdmin())) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="posts.unpublish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_UNPUBLISH'); ?>
								</a>
							</li>
						<?php } ?>

						<?php if ($post->isUnpublished() && ($this->acl->get('publish_entry') || EB::isSiteAdmin())) { ?>
							<li>
								<a href="javascript:void(0);" data-eb-action="posts.publish" data-type="form">
									<?php echo JText::_('COM_EASYBLOG_PUBLISH'); ?>
								</a>
							</li>
						<?php } ?>

						<?php if ($post->isFeatured() && $this->acl->get('feature_entry')) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="posts.unfeature" data-type="form">
								<?php echo JText::_('COM_EASYBLOG_FEATURED_UNFEATURE_POST'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if (!$post->isFeatured() && $this->acl->get('feature_entry')) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="posts.feature" data-type="form">
								<?php echo JText::_('COM_EASYBLOG_FEATURED_FEATURE_POST'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if ($this->acl->get('delete_entry') && !$post->isPending()) { ?>
						<li>
							<a href="javascript:void(0);" class="text-danger" data-eb-action="posts.trash" data-type="form">
								<i class="fa fa-trash"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_TRASH');?>
							</a>
						</li>
						<?php } ?>

						<?php if (EB::isSiteAdmin()) { ?>
						<li>
							<a href="javascript:void(0);" data-eb-action="site/views/dashboard/confirmNotify" data-type="dialog" class="btn btn-default btn-xs">
								<i class="fa fa-envelope"></i>
							</a>
						</li>
						<?php } ?>

						<?php if ($oauthClients) { ?>
							<?php foreach ($oauthClients as $oauth) { ?>
							<li class="dropdown-autopost">
								<a href="javascript:void(0);"
									class="<?php echo $oauth->isShared($post->id) ? ' active' : '';?>"
									data-eb-provide="tooltip"
									data-post-autopost
									data-autopost-type="<?php echo $oauth->type;?>"

									<?php if ($oauth->isShared($post->id)) { ?>
									data-original-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TOOLTIP_' . strtoupper($oauth->type) . '_POSTED');?>"
									<?php } else { ?>
									data-original-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TOOLTIP_' . strtoupper($oauth->type));?>"
									<?php } ?>
								>
									<i class="fa fa-check"></i>
									<?php echo $oauth->type;?>
								</a>
							</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</td>


				<td class="text-center narrow-hide">
					<?php if ($post->isPublished()) { ?>
						<span class="text-success"><?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?></span>
					<?php } ?>

					<?php if ($post->isUnpublished()) { ?>
						<span class="text-danger"><?php echo JText::_('COM_EASYBLOG_UNPUBLISHED'); ?></span>
					<?php } ?>

					<?php if ($post->isScheduled()) { ?>
						<span class="text-info"><?php echo JText::_('COM_EASYBLOG_SCHEDULED'); ?></span>
					<?php } ?>

					<?php if ($post->isPending()) { ?>
						<span class="text-warning"><?php echo JText::_('COM_EASYBLOG_UNDER_REVIEW'); ?></span>
					<?php } ?>

					<?php if ($post->isDraft()) { ?>
						<span><?php echo JText::_('COM_EASYBLOG_DRAFT'); ?></span>
					<?php } ?>
				</td>


				<td class="text-center narrow-hide">
					<?php echo $post->hits;?>
				</td>

				<td class="text-center">
					<?php echo $post->getCreationDate(true)->format(JText::_('Y-m-d H:i'));?>
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
	<input type="hidden" name="layout" value="entries" />
	<?php echo $this->html('form.action'); ?>
</form>
