<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($this->params->get('authors_search', true) || $this->params->get('authors_sorting', true)) { ?>
<form name="authors" method="post" action="<?php echo JRoute::_('index.php'); ?>" class="eb-author-filter form-horizontal row-table <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<?php if ($this->params->get('authors_search', true)) { ?>
	<div class="col-cell">
		<div class="eb-authors-finder input-group">
			<input type="text" class="form-control" name="search" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_BLOGGERS', true);?>" value="<?php echo $this->html('string.escape', $search);?>" />
			<i class="fa fa-user"></i>
			<div class="input-group-btn">
				<button type="submit" class="btn btn-default">
					<?php echo JText::_('COM_EASYBLOG_SEARCH_BUTTON', true);?>
				</button>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php if ($this->params->get('authors_sorting', true)) { ?>
	<div class="col-cell">
		<div class="eb-authors-sorter eb-filter-select-group pull-right">
			<select class="form-control pull-right" data-authors-sorting>
				<option value="default" data-url="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger');?>"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_SORT_BY');?></option>
				<option value="alphabet" <?php echo $sort == 'alphabet' ? 'selected="selected"' : '';?> data-url="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger&sort=alphabet', false); ?>">
					<?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_NAME');?>
				</option>
				<option value="mostactive" <?php echo $sort == 'active' ? 'selected="selected"' : '';?> data-url="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=active', false); ?>">
					<?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_ACTIVE');?>
				</option>
				<option value="latestblogger" <?php echo $sort == 'latest' ? 'selected="selected"' : '';?> data-url="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=latest', false); ?>">
					<?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_LATEST');?>
				</option>
				<option value="latestpost" <?php echo $sort == 'latestpost' ? 'selected="selected"' : '';?> data-url="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=latestpost', false); ?>">
					<?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_LATEST_POST');?>
				</option>
				<option value="ordering" <?php echo $sort == 'ordering' ? 'selected="selected"' : '';?> data-url="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=ordering', false); ?>">
					<?php echo JText::_('COM_EB_BLOGGERS_ORDER_BY_COLUMN_ORDERING');?>
				</option>
			</select>
			<div class="eb-filter-select-group__drop"></div>
		</div>
	</div>
	<?php } ?>

	<?php echo $this->html('form.action', 'search.blogger'); ?>
</form>
<?php } ?>

<div class="eb-authors" data-authors>
	<?php if ($authors) { ?>
		<?php foreach ($authors as $author) { ?>

			<div class="eb-author <?php echo $this->isMobile() ? 'is-mobile' : '';?>" data-author-item data-id="<?php echo $author->id;?>">

				<?php echo $this->html('headers.author', $author, array(
																			'avatar' => $this->params->get('author_avatar', true),
																			'rss' => $this->params->get('author_subscribe_rss', true),
																			'subscription' => $this->params->get('author_subscribe_email', true),
																			'twitter' => $this->params->get('author_twitter', true),
																			'website' => $this->params->get('author_website', true),
																			'biography' => $this->params->get('author_bio', true),
																			'featureAction' => true
																	)
				); ?>

				<?php if ($this->params->get('author_posts', true) || $this->params->get('author_categories', true) || $this->params->get('author_tags', true)) { ?>
				<div class="eb-authors-stats">
					<ul class="eb-stats-nav reset-list">
						<?php if ($this->params->get('author_posts', true)) { ?>
						<li class="active">
							<a class="btn btn-default btn-block" href="#posts-<?php echo $author->id;?>" data-bp-toggle="tab">
								<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TOTAL_POSTS');?>
							</a>
						</li>
						<?php } ?>

						<?php if ($this->params->get('author_categories', true)) { ?>
						<li>
							<a class="btn btn-default btn-block" href="#categories-<?php echo $author->id;?>"
								data-bp-toggle="tab"
								data-author-id="<?php echo $author->id; ?>"
								<?php echo ($author->categoryCount) ? 'data-tab-category' : ''; ?>
							>
								<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TOTAL_CATEGORIES');?>
							</a>
						</li>
						<?php } ?>

						<?php if ($this->params->get('author_tags', true)) { ?>
						<li>
							<a class="btn btn-default btn-block" href="#tags-<?php echo $author->id;?>"
								data-bp-toggle="tab"
								data-author-id="<?php echo $author->id; ?>"
								<?php echo ($author->tagCount) ? 'data-tab-tag' : ''; ?>
							>
								<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TAGS');?>
							</a>
						</li>
						<?php } ?>
					</ul>

					<div class="eb-stats-content">
						<?php if ($this->params->get('author_posts', true)) { ?>
						<div class="tab-pane eb-stats-posts active" id="posts-<?php echo $author->id;?>">
							<?php if ($author->blogs) { ?>
							<?php $authorp = 1; ?>
								<?php foreach ($author->blogs as $post) { ?>
									<?php if ($authorp <= $limitPreviewPost) { ?>
										<div>
											<time><?php echo $post->getDisplayDate($this->config->get('blogger_post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC3'));?></time>
											<?php echo $post->getIcon('eb-post-type'); ?>
											<a href="<?php echo EB::_('index.php?option=com_easyblog&view=entry&id=' . $post->id);?>"><?php echo $post->title;?></a>
										</div>
									<?php } ?>
								<?php $authorp++; ?>
								<?php } ?>

								<a href="<?php echo $author->getPermalink();?>" class="btn btn-show-all">
									<?php echo JText::_('COM_EASYBLOG_VIEW_ALL_POSTS');?>
								</a>
							<?php } else { ?>
								<div class="eb-empty">
									<?php echo JText::_('COM_EASYBLOG_NO_RECORDS_FOUND');?>
								</div>
							<?php } ?>
						</div>
						<?php } ?>

						<?php if ($this->params->get('author_categories', true)) { ?>
						<div class="tab-pane eb-labels eb-stats-categories <?php echo !$this->params->get('author_posts', true) ? 'active' : '';?>" data-category-container id="categories-<?php echo $author->id;?>">
							<?php if ($author->categoryCount > 0) { ?>
								<div class="center">
									<i class="eb-loader-o"></i>
								</div>
							<?php } else { ?>
								<div class="eb-empty"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_DID_NOT_CREATE_CATEGORY'); ?></div>
							<?php } ?>
						</div>
						<?php } ?>

						<?php if ($this->params->get('author_tags', true)) { ?>
						<div class="tab-pane eb-labels eb-stats-tags <?php echo (!$this->params->get('author_posts', true) && !$this->params->get('author_categories', true)) ? 'active' : '';?>" data-tag-container id="tags-<?php echo $author->id;?>">
							<?php if ($author->tagCount > 0) { ?>
								<div class="center">
									<i class="eb-loader-o"></i>
								</div>
							<?php } else { ?>
								<div class="eb-empty"><?php echo JText::_('COM_EASYBLOG_AUTHOR_DID_NOT_USE_ANY_TAGS_YET'); ?></div>
							<?php } ?>
						</div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fa fa-users"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_AUTHORS_CURRENTLY'); ?>
		</div>
	<?php } ?>

	<?php if ($pagination) { ?>
	<div class="eb-pagination clearfix">
		<?php echo $pagination; ?>
	</div>
	<?php } ?>
</div>
