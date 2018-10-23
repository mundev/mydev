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

$defaultType = $items[0]->getType() == SOCIAL_TYPE_USER ? 'user' : 'cluster';
?>
<div class="es-story-post-as">
	<div class="dropdown_">
		<button type="button" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown" data-postas-toggle>
			<div class="o-avatar o-avatar--sm" data-postas-avatar> 
				<?php echo $this->html('avatar.' . $defaultType, $items[0], 'default', false, false, '', false); ?>
			</div>
			<i class="i-chevron i-chevron--down" data-postas-icon></i>
		</button>

		<ul class="dropdown-menu dropdown-menu-right dropdown-menu--post-as" data-postas-menu>
			<?php foreach ($items as $item) { ?>
			<li data-item data-postas-<?php echo $item->getType(); ?> data-value="<?php echo $item->getType(); ?>" class="<?php echo $item->getType() == $default ? 'is-active' : ''; ?>" >
				<a href="javascript:void(0);">
					<span class="o-media">
						<span class="o-media__image">
							<span class="o-avatar o-avatar--sm" data-postas-avatar>
								<?php $type = $item->getType() == SOCIAL_TYPE_USER ? 'user' : 'cluster';?>
								<?php echo $this->html('avatar.' . $type, $item, 'sm', false, false, '', false); ?>
							</span>
						</span>
						<span class="o-media__body o-media__body--text-overflow">
							<?php echo $item->getName(); ?>	
						</span>
					</span>
				</a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<input type="hidden" name="postas" value="page" data-postas-hidden />
</div>