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
?>
<div class="eb-checkbox mt-10">
	<input type="checkbox" id="<?php echo $name;?>" name="system" value="1" <?php echo $attributes;?> <?php echo $value ? ' checked="checked"' : '';?> />
	<label for="<?php echo $name;?>">
		<?php echo $title;?>
	</label>
</div>