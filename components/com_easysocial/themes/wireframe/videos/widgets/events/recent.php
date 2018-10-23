<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
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
			<?php echo JText::_('COM_EASYSOCIAL_EVENTS_WIDGET_TITLE_RECENT_VIDEOS'); ?>
		
			<span class="es-side-widget__label">(<?php echo $totalVideos;?>)</span>
		</div>
	</div>
	<div class="es-side-widget__bd">
		<?php echo $this->html('widget.videos', $videos, 'COM_EASYSOCIAL_EVENTS_WIDGET_NO_VIDEOS_UPLOADED_YET'); ?>

		<?php if ($videos) { ?>
		<div>
			<?php echo $this->html('widget.viewAll', 'COM_ES_VIEW_ALL', ESR::videos(array('uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT))); ?>
		</div>
		<?php } ?>
	</div>
</div>