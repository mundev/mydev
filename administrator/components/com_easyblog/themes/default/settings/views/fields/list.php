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
<div class="row">
	<div class="col-lg-8">
		<select name="<?php echo $key;?>_<?php echo $field->attributes->name;?>" class="form-control">
			<?php foreach ($field->options as $option) { ?>
				<?php 
				// Since this is already the global settings, it cannot be inherited any further
				if ($option->value == -1) {
					continue;
				}
				?>
			<option value="<?php echo $option->value;?>" <?php echo $this->config->get($key . '_' . $field->attributes->name) == $option->value ? ' selected="selected"' : '';?>><?php echo JText::_($option->label);?></option>
			<?php } ?>
		</select>
	</div>
</div>