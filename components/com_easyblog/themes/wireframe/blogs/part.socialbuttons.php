<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// This template file is added to support backward compatibility only! Please do not rely on this file
?>
<?php if ($this->params->get('post_social_buttons', true)) { ?>
	<?php echo EB::socialbuttons()->html($post, 'listings'); ?>
<?php } ?>