<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-stream-apps type-discuss">
	<div class="es-stream-apps__hd">
		<a href="<?php echo $post->getPermalink(false, true, false, true);?>" class="es-stream-apps__title">
			<?php echo $post->title; ?>
		</a>

		<div class="es-stream-apps__meta t-fs--sm">
			<ul class="g-list-inline g-list-inline--dashed">
				<li>
					<i class="fa fa-folder"></i>&nbsp; <a href="<?php echo $post->getCategory()->getPermalink();?>"><?php echo JText::_($post->getCategory()->title);?></a>
				</li>
				<li>
					<i class="fa fa-calendar"></i>&nbsp; <?php echo ED::date($post->created)->display(ED::config()->get('layout_dateformat', JText::_('DATE_FORMAT_LC1')));?>
				</li>
			</ul>
		</div>
	</div>

	<div class="es-stream-apps__bd es-stream-apps--border">
		<div class="es-stream-apps__desc">
			<?php echo $content;?>
		</div>

		<ol class="g-list--horizontal has-dividers--right">
			<li class="g-list__item">
				<a href="<?php echo $post->getPermalink(false, true, false, true);?>">
					<?php echo JText::_('APP_EASYDISCUSS_STREAM_VIEW_POST'); ?>
				</a>
			</li>
		</ol>
	</div>
</div>