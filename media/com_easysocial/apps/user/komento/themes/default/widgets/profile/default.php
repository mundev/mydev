<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-side-widget">
	<div class="es-side-widget__hd">
		<div class="es-side-widget__title">
			<?php echo JText::_('APP_USER_KOMENTO_WIDGET_COMMENTS_TITLE'); ?>
			<span class="es-side-widget__label">(<?php echo $total; ?>)</span>
		</div>
	</div>

	<div class="es-side-widget__bd">
		<?php if ($comments) { ?>
		<ul class="o-nav o-nav--stacked">
			<?php foreach ($comments as $comment) { ?>
			<li class="o-nav__item t-lg-mb--lg">
				<a href="<?php echo $comment->getPermalink();?>"><?php echo $comment->getItemTitle();?></a>
				
				<div class="t-fs--sm t-text--muted">
					<i class="fa fa-clock-o"></i>&nbsp; <?php echo $comment->getCreatedDate()->toLapsed(); ?>
				</div>

				<div class="kt-excerpt">
					<?php echo $comment->getContent(50, true);?>
				</div>
			</li>
			<?php } ?>
		</ul>
		<?php } else { ?>
		<div class="t-text--muted">
			<?php echo JText::_('APP_USER_KOMENTO_NO_COMMENTS_POSTED_YET'); ?>
		</div>
		<?php } ?>
	</div>
</div>