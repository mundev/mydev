<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php foreach ($posts as $post) { ?>
<div class="es-apps-item es-island<?php echo $post->isResolved() ? ' is-resolved' : '';?><?php echo $post->isLocked() ? ' is-locked' : '';?><?php echo !$post->hasReplies() ? ' is-unanswered' : '';?>">
	<div class="es-apps-item__bd">
		<a href="<?php echo $post->getPermalink(false, true, false, true);?>" class="es-apps-item__title"><?php echo $post->getTitle();?></a>

		<div class="es-apps-item__desc">
			<?php echo $post->getIntro();?>
		</div>
		
		<div class="es-apps-item__item-action">
			<a href="<?php echo $post->getPermalink(false, true, false, true);?>" class="btn btn-es-default-o btn-sm"><?php echo JText::_('COM_ES_VIEW_POST');?></a>
		</div>
	</div>
	<div class="es-apps-item__ft">
		<div class="o-grid">
			<div class="o-grid__cell">
				<div class="es-apps-item__meta">
					<div class="es-apps-item__meta-item">
						<ol class="g-list-inline g-list-inline--dashed">
							<li>
								<i class="fa fa-calendar"></i>&nbsp; <?php echo JText::sprintf('APP_EASYDISCUSS_DISCUSSIONS_STARTED_BY_ON', ES::date($post->created)->format(JText::_('DATE_FORMAT_LC1'))); ?>
							</li>
						</ol>
					</div>
				</div>		
			</div>
			<div class="o-grid__cell o-grid__cell--auto-size o-grid__cell--right">
				<div class="es-apps-item__state">
					<?php if ($post->isResolved()) { ?>
						<span class="o-label o-label--success-o label-resolved"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_RESOLVED'); ?></span>
					<?php } ?>

					<?php if ($post->isLocked()) { ?>
						<span class="o-label o-label--warning-o label-locked"><i class="fa fa-lock locked-icon"></i> <?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_LOCKED'); ?></span>
					<?php } ?>

					<?php if (!$post->getTotalReplies()) { ?>
						<span class="o-label o-label--danger-o label-unanswered"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_UNANSWERED'); ?></span>
					<?php } ?>
				</div>		
			</div>
		</div>
	</div>
</div>
<?php } ?>

<div data-pagination>
	<?php echo $pagination->getPagesLinks('profile', array(), false); ?>
</div>