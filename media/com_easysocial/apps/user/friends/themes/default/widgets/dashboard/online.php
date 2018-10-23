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
<div class="es-side-widget">
	<?php echo $this->html('widget.title', 'APP_FRIENDS_ONLINE_FRIENDS'); ?>
	
	<div class="es-side-widget__bd">
		<?php echo $this->html('widget.users', $friends, 'APP_FRIENDS_NO_ONLINE_FRIENDS_CURRENTLY'); ?>
	</div>
</div>
