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
<div class="es-stream-apps type-blog">
	<div class="es-stream-apps__hd">
		<a href="<?php echo $post->getPermalink();?>" class="es-stream-apps__title">
			<?php echo $post->title; ?>
		</a>

		<div class="es-stream-apps__meta t-fs--sm">
			<ul class="g-list-inline g-list-inline--dashed">
				<li>
					<i class="fa fa-folder"></i>&nbsp; <a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo JText::_($post->getPrimaryCategory()->title);?></a>
				</li>
				<li>
					<i class="fa fa-calendar"></i>&nbsp; <?php echo $post->getDisplayDate(EB::config()->get('integrations_easysocial_stream_date_source'))->format(JText::_('DATE_FORMAT_LC1'));?>
				</li>
				<?php if (isset($tagsOutput) && $tagsOutput) { ?>
					<?php echo $tagsOutput; ?>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="es-stream-apps__bd es-stream-apps--border">
		<div class="es-stream-apps__desc">
			<?php if ($post->image) { ?>
				<a href="<?php echo $post->getPermalink();?>" class="blog-image <?php echo $alignment;?>">
					<img src="<?php echo $post->getImage('thumbnail');?>" align="right" width="96" alt="<?php echo $this->html('string.escape', $post->getImageTitle());?>" />
				</a>
			<?php } ?>

			<?php echo strip_tags($content);?>
		</div>

		<ol class="g-list--horizontal has-dividers--right">
			<li class="g-list__item">
				<a href="<?php echo $post->getPermalink();?>">
					<?php echo JText::_('APP_USER_BLOG_CONTINUE_READING'); ?>
				</a>
			</li>
		</ol>
	</div>
</div>