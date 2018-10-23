<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-users-item es-island" data-item data-id="<?php echo $album->id;?>">
	<div class="o-grid">
		<div class="o-grid__cell">
			<div class="o-flag">
				<div class="o-flag__image o-flag--top">
					<a href="<?php echo $album->getPermalink(); ?>" class="o-avatar">
						<img src="<?php echo $album->image; ?>" title="<?php echo $this->html('string.escape', $album->title); ?>" class="avatar" />
					</a>
				</div>

				<div class="o-flag__body">
					<a href="<?php echo $album->getPermalink(); ?>" class="es-search-group__item-title">
						<?php echo $album->_('title');?>
					</a>

					<div class="es-search-group__meta">
						<?php echo JString::substr(strip_tags($album->_('caption')), 0, 120); ?>
					</div>

					<ul class="g-list-inline g-list-inline--dashed t-text--muted">
						<?php if ($displayType) { ?>
						<li>
							<i class="fa fa-picture-o"></i>&nbsp; <?php echo JText::_('COM_ES_ALBUMS');?>
						</li>
						<?php } ?>
						<li>
							<i class="fa fa-file-image-o"></i>&nbsp; <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_SEARCH_RESULT_ALBUMS_PHOTOS_COUNT', $album->getTotalPhotos()), $album->getTotalPhotos()); ?>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
