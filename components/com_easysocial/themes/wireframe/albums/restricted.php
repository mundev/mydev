<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<?php if ($showProfileHeader) { ?>
    <?php echo $this->html('html.miniheader', $node); ?>
<?php } ?>

<div class="well <?php echo ($showProfileHeader) ? 'mt-20' : '' ?>">
	<div class="well-title">
		<i class="fa fa-lock"></i> <?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_RESTRICTED' );?>
	</div>

	<p class="well-text">
		<?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_RESTRICTED_DESC' ); ?>
	</p>
</div>
