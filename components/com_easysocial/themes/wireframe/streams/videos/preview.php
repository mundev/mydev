<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-stream-embed is-video">
	<div class="es-stream-embed__player">
		<div class="video-container<?php echo $video->isFacebookEmbed() ? ' ' . $video->getRatioString() : ''; ?>">
			<?php echo $video->getEmbedCodes();?>
		</div>
	</div>

	<a href="<?php echo $video->getPermalink();?>" class="es-stream-embed__title es-stream-embed--border"><?php echo $video->title;?></a>

	<div class="es-stream-embed__meta">
		<ul class="g-list-inline g-list-inline--space-right">
			<li>
				<a href="<?php echo $video->getCategory()->getPermalink();?>"><i class="fa fa-folder"></i>&nbsp; <?php echo JText::_($video->getCategory()->title);?></a>
			</li>
			<li>
				<i class="fa fa-calendar"></i>&nbsp; <?php echo $video->getCreatedDate()->format(JText::_('DATE_FORMAT_LC1'));?>
			</li>
			<?php if ($this->config->get('video.layout.item.hits')) { ?>
			<li>
				<i class="fa fa-eye"></i> <?php echo $video->getHits(); ?>
			</li>
			<?php } ?>
		</ul>
	</div>

	<div class="es-stream-embed__desc">
		<?php echo $this->html('string.truncate', $video->description, ES::config()->get('stream.content.truncatelength'), '', false, true);?>
	</div>
</div>
