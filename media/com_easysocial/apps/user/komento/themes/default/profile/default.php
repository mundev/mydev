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
<div class="es-komento">
	<div class=" app-contents<?php echo !$comments ? ' is-empty' : '';?>" data-reviews-wrapper>
		<?php echo $this->html('html.emptyBlock', 'APP_USER_KOMENTO_VIEW_PROFILE_NO_COMMENS_YET', 'fa-database'); ?>

		<div data-reviews-contents>
			<div class="es-reviews-list">
					
				<?php foreach ($comments as $comment) { ?>
				<div class="es-reviews-item">
					<div class="es-reviews-item__title">
						<?php echo JText::sprintf('APP_USER_KOMENTO_VIEW_PROFILE_COMMENT_TITLE', '<a href="' . $comment->getItemPermalink() . '">' . $comment->getItemTitle() . '</a>'); ?>
					</div>
					<div class="t-fs--sm t-text--muted">
						<i class="fa fa-clock-o"></i>&nbsp; <?php echo $comment->getCreatedDate()->toLapsed();?>
					</div>
					<div class="es-reviews-item__desp t-lg-mt--md">
						<?php echo $comment->comment; ?>
					</div>
					<div>
						<a href="<?php echo $comment->getPermalink();?>" class="btn btn-es-default-o btn-sm"><?php echo JText::_('APP_USER_KOMENTO_VIEW_COMMENT');?></a>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
