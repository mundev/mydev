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
<?php if ($actor->id) { ?>
	<?php echo JText::sprintf('APP_USER_KOMENTO_STREAM_REPLIED_TITLE', $this->html('html.user', $actor->id), $comment->getPermalink(), '<a href="' . $comment->getItemPermalink() . '">' . $comment->getItemTitle() . '</a>'); ?>
<?php } else { ?>
	<?php echo JText::sprintf('APP_USER_KOMENTO_STREAM_REPLIED_TITLE', $actor->name, $comment->getPermalink(), '<a href="' . $comment->getItemPermalink() . '">' . $comment->getItemTitle() . '</a>'); ?>
<?php } ?>