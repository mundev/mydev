<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-stream-apps type-blog">
	<div class="es-stream-apps__hd">
		<a href="<?php echo $comment->getPermalink();?>" class="es-stream-apps__title">
			<?php echo $comment->getItemTitle(); ?>
		</a>
	</div>

	<div class="es-stream-apps__bd es-stream-apps--border">
		<div class="es-stream-apps__desc">
			<blockquote><?php echo $this->html('string.truncater', $comment->getContent(), 300); ?></blockquote>
		</div>

		<?php if ($attachments) { ?>
		<h5><b><?php echo JText::_('COM_KOMENTO_COMMENT_ATTACHMENTS');?>:</b></h5>
		<hr />
			<ul class="comment-attachments" style="list-style: none;margin: 0;">
				<?php foreach ($attachments as $attachment) { ?>
				<li style="margin-bottom: 10px;">
					<a href="<?php echo $attachment->getLink();?>"><?php echo $attachment->filename;?></a> (<?php echo round($attachment->size / 1024);?> <?php echo JText::_('kb');?>) 
				</li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>
</div>