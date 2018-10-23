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
<span class="es-data-truncater" data-es-truncater>
	<span data-text class="fd-cf"><?php echo $truncated; ?></span>
	<span class="t-hidden" data-original><?php echo $original;?></span>

	<?php if ($showMore) { ?>
    <a href="javascript:void(0);" <?php echo $overrideReadmore ? 'data-filter-item="info"' : 'data-readmore'; ?>><?php echo JText::_('COM_EASYSOCIAL_READMORE'); ?></a>
	<?php } ?>

</span>