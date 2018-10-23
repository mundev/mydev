<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php $column = $this->params->get('post_nickel_column', 2); ?>
<?php $grid_size = 99 / $column; ?>
<div class="eb-masonry-post <?php echo $this->isMobile() ? 'is-mobile' : '';?>" data-blog-posts style="width: <?php echo $grid_size; ?>%">
	<div class="eb-post" data-blog-posts-item data-id="<?php echo $post->id;?>">

		<div class="eb-post-top row-table<?php echo !$this->config->get('layout_avatar') || !$this->params->get('post_author_avatar', true) ? ' no-avatar' : '';?>">
			<?php if ($this->config->get('layout_avatar') && $this->params->get('post_author_avatar', true)) { ?>
			<div class="col-cell cell-tight">
				
				<div class="eb-post-author-avatar single">
					<a href="<?php echo $post->getAuthorPermalink(); ?>" class="eb-avatar">
						<img src="<?php echo $post->creator->getAvatar();?>" width="40" height="40" alt="<?php echo $post->getAuthorName();?>" />
					</a>
				</div>
			</div>
			<?php } ?>

			<div class="col-cell">
				<?php if ($this->params->get('post_author', true)) { ?>
				<div class="eb-post-author">
					<span>
						<a href="<?php echo $post->getAuthorPermalink();?>" rel="author"><?php echo $post->getAuthorName();?></a>
					</span>
				</div>
				<?php } ?>

				<?php if ($this->params->get('post_date', true)) { ?>
				<div class="eb-post-date text-muted">
					<time class="eb-meta-date" content="<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
						<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
					</time>
				</div>
				<?php } ?>
			</div>

			<div class="col-cell cell-tight text-right">
				<?php echo $this->output('site/blogs/admin.tools', array('post' => $post, 'return' => $return)); ?>
			</div>
	
		</div>

		<div class="eb-post-content">
			<div class="eb-post-head">
				<?php if ($post->getType() == 'quote') { ?>
				<div class="eb-post-headline">
					<h2 class="eb-post-title reset-heading">
						<a href="<?php echo $post->getPermalink();?>" class="text-inherit"><?php echo nl2br($post->title);?></a>
					</h2>

					<div class="eb-post-headline-source">
						<?php echo $post->getContent(); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($post->getType() == 'link') { ?>
				<div class="eb-post-headline">
					<h2 class="eb-placeholder-link-title eb-post-title reset-heading">
						<a href="<?php echo $post->getPermalink();?>"><?php echo nl2br($post->title);?></a>
					</h2>

					<div class="eb-post-headline-source">
						<a href="<?php echo $post->getAsset('link')->getValue(); ?>" target="_blank"><?php echo $post->getAsset('link')->getValue();?></a>
					</div>
				</div>
				<?php } ?>

				<?php if ($post->getType() == 'twitter') { ?>
				<?php $screen_name = $post->getAsset('screen_name')->getValue();
					  $created_at = EB::date($post->getAsset('created_at')->getValue(), true)->format(JText::_('DATE_FORMAT_LC'));
				?>
				<div class="eb-post-headline">
					<h2 class="eb-post-title-tweet reset-heading">
						<?php echo $post->content;?>
					</h2>

					<?php if (!empty($screen_name) && !empty($created_at)) { ?>
					<div class="eb-post-headline-source">
							<?php echo '@'.$screen_name.' - '.$created_at; ?>
							&middot;
							<a href="<?php echo $post->getPermalink();?>">
								<?php echo JText::_('COM_EASYBLOG_LINK_TO_POST'); ?>
							</a>
					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ((in_array($post->posttype, array('photo', 'standard', 'video', 'email'))) && $this->params->get('post_title', true)) { ?>
					<h2 class="eb-post-title reset-heading">
						<a href="<?php echo $post->getPermalink();?>" class="text-inherit"><?php echo $post->title;?></a>
					</h2>
				<?php } ?>

				<?php if ($this->params->get('post_date', true) || $this->params->get('post_author', true) || $this->params->get('post_category', true)) { ?>
				<div class="eb-post-meta text-muted">

					<?php if ($this->params->get('post_type', true)) { ?>
					<div class="hide eb-post-type">
						<?php echo $post->getIcon(); ?>
					</div>
					<?php } ?>

					<?php if ($post->isTeamBlog() && $this->config->get('layout_teamavatar')) { ?>
					<div class="eb-post-meta-team">
						<a href="<?php echo $post->getBlogContribution()->getPermalink(); ?>" class="">
							<img src="<?php echo $post->getBlogContribution()->getAvatar();?>" width="16" height="16" alt="<?php echo $post->getBlogContribution()->getTitle();?>" />
						</a>
						<span>
							<a href="<?php echo $post->getBlogContribution()->getPermalink(); ?>" class="">
								<?php echo $post->getBlogContribution()->getTitle();?>
							</a>
						</span>
					</div>
					<?php } ?>
					
					<?php if ($this->params->get('post_category', true) && $post->categories) { ?>
					<div class="">
						<div class="eb-post-category comma-seperator">
							<i class="fa fa-folder-open"></i>
							<?php foreach ($post->categories as $category) { ?>
							<span>
								<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
							</span>
							<?php } ?>
						</div>
					</div>
					<?php } ?>

					<?php if ($post->isFeatured) { ?>
					<div>
						<div class="eb-post-featured">
							<?php if ($post->isFeatured) { ?>
							<i class="fa fa-star" data-original-title="<?php echo JText::_('COM_EASYBLOG_POST_IS_FEATURED');?>" data-placement="bottom" data-eb-provide="tooltip"></i>
							<?php echo JText::_('COM_EASYBLOG_FEATURED');?>&nbsp;
							<?php } ?>
						</div>
					</div>
					<?php } ?>
					
				</div>
				<?php } ?>
			</div>

			<div class="eb-post-protected">
				<?php echo $this->output('site/blogs/tools/protected.form', array('post' => $post)); ?>
			</div>
		</div>
	</div>
	<?php echo $this->output('site/blogs/post.schema', array('post' => $post)); ?>
</div>
