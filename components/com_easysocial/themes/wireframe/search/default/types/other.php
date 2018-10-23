<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-users-item es-island">
	<div class="o-grid">
		<div class="o-grid__cell">
			<div class="o-flag">
				<div class="o-flag__image o-flag--top">
					<a href="<?php echo JRoute::_($item->link); ?>" class="o-avatar">
						<img src="<?php echo $item->image; ?>" title="<?php echo $this->html('string.escape', strip_tags($item->title)); ?>" />
					</a>
				</div>

				<div class="o-flag__body">
					<a href="<?php echo JRoute::_($item->link);?>" class="es-search-group__item-title">
						<?php echo strip_tags($item->title); ?>
					</a>

					<div class="es-search-group__meta">
						<?php echo JString::substr(strip_tags($item->content), 0, 120); ?>
					</div>

					<ul class="g-list-inline g-list-inline--dashed t-text--muted">
						<li>
							<i class="fa fa-search"></i>&nbsp; <?php echo $item->groupTitle;?>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>