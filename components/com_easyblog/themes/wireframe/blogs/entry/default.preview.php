<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-blog-post>
	<div id="entry-<?php echo $blog->id; ?>" class="eb-entry fd-cf" data-blog-posts-item data-id="<?php echo $blog->id;?>">

		<?php if (!$ispreview) { ?>
			<?php echo $this->output('site/blogs/admin.tools', array('blog' => $blog, 'return' => EB::_('index.php?option=com_easyblog'))); ?>
		<?php } ?>

		<!-- @module: easyblog-before-entry -->
		<?php echo EB::renderModule('easyblog-before-entry'); ?>

		<div class="eb-entry-head">
			<?php if ($category->getParam('show_title', true)) { ?>
			<h1 id="title-<?php echo $blog->id; ?>" class="eb-entry-title reset-heading <?php echo ($blog->isFeatured()) ? ' featured-item' : '';?> "><?php echo $blog->title; ?></h1>
			<?php } ?>

			<?php echo $blog->event->afterDisplayTitle; ?>

			<div class="eb-entry-meta text-muted">
				<?php if ($blog->isFeatured()) { ?>
				<div class="eb-entry-featured">
					<i class="fa fa-star text-muted"></i>
					<span><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?></span>
				</div>
				<?php } ?>

				<?php if ($category->getParam('show_date', true)) { ?>
				<div class="eb-entry-date">
					<i class="fa fa-clock-o"></i>
					<time class="eb-meta-date" content="<?php echo $blog->getCreationDate($blog->category->getParam('date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
						<?php echo $blog->getDisplayDate($blog->category->getParam('date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
					</time>
				</div>
				<?php } ?>

				<?php if ($category->getParam('show_author', true)) { ?>
				<div class="eb-meta-author">
					<i class="fa fa-pencil"></i>
					<span>
						<a href="<?php echo $blog->getAuthorPermalink();?>" rel="author"><?php echo $blog->getAuthorName();?></a>
					</span>
				</div>
				<?php } ?>

				<?php if ($category->getParam('show_category', true)) { ?>
					<div class="eb-meta-category comma-seperator">
						<i class="fa fa-folder-open"></i>
						<?php foreach ($blog->categories as $cat) { ?>
						<span><a href="<?php echo $cat->getPermalink();?>"><?php echo $cat->getTitle();?></a></span>
						<?php } ?>
					</div>
				<?php } ?>

				<?php if ($category->getParam('show_hits', true)) { ?>
				<div class="eb-meta-views">
					<i class="fa fa-eye"></i>
					<span><?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $blog->hits);?></span>
				</div>
				<?php } ?>

				<?php if ($this->config->get('main_comment') && $blog->totalComments !== false && $category->getParam('allow_comments', true) && $blog->allowcomment) { ?>
				<div class="eb-meta-comments">
					<?php if ($this->config->get('comment_disqus')) { ?>
						<i class="fa fa-comment"></i>
						<span><?php echo $blog->totalComments; ?></span>
					<?php } else { ?>
						<i class="fa fa-comment"></i>
						<span><a href="#comments"><?php echo $this->getNouns('COM_EASYBLOG_COMMENT_COUNT', $blog->totalComments, true); ?></a></span>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
		</div>

		<div class="eb-entry-body clearfix">
			<div class="eb-entry-article clearfix" data-blog-content>
				<?php if (($blog->image && $category->getParam('show_blogimage', true)) || $blog->category->getParam('show_imageplaceholder', true)) { ?>
					<div class="eb-image eb-entry-image">
						<a class="easyblog-thumb-preview eb-image-popup-button"
						   href="<?php echo $post->getImage('original');?>"
						   title="<?php echo $this->escape($post->getImageTitle());?>"
						   target="_blank"
						>
							<img src="<?php echo $post->getImage('large');?>" alt="<?php echo $post->getImageTitle(); ?>">
							<i class="fa fa-search"></i>
						</a>
					</div>
				<?php } ?>

				<?php echo $blog->event->beforeDisplayContent; ?>

				<?php if(!empty($blog->toc)){ echo $blog->toc; } ?>

				<?php echo EB::renderModule('easyblog-before-content'); ?>

				<?php echo $blog->text; ?>

				<?php echo EB::renderModule('easyblog-after-content'); ?>

			</div>

			<?php if ($blog->fields && $category->getParam('show_fields', true)) { ?>
				<?php echo $this->output('site/blogs/entry/fields', array('fields' => $blog->fields)); ?>
			<?php } ?>

			<!-- Location Service -->
			<?php echo $this->output('site/blogs/entry/location', array('blog' => $blog, 'category' => $category)); ?>

			<!-- Copyright -->
			<?php echo $this->output('site/blogs/entry/copyright', array('blog' => $blog, 'category' => $category)); ?>

			<?php if ($category->getParam('show_tags', true)) { ?>
			<div class="eb-entry-tags">
				<?php echo $this->output('site/blogs/tags/item', array('tags' => $tags)); ?>
			</div>
			<?php } ?>

		</div>

		<?php if ($category->getParam('show_author_box', true) && !$blog->hasAuthorAlias()) { ?>
		<?php echo $this->output('site/blogs/entry/author', array('blog' => $blog)); ?>
		<?php } ?>
	</div>

	<?php echo $adsenseHTML; ?>

	<!-- @Trigger: onAfterDisplayContent -->
	<?php echo $blog->event->afterDisplayContent; ?>
</div>
