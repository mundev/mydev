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
<li>
	<a href="<?php echo FRoute::apps( array( 'layout' => 'canvas' , 'customView' => 'create' , 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP , 'id' => $app->getAlias() ) );?>"><?php echo JText::_( 'APP_GROUP_DISCUSSIONS_CREATE_DISCUSSION' );?></a>
</li>
