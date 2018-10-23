<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="form-group form-group--float-label" data-eb-label>
	<?php echo $this->html('form.' . $type, $name, $value, $id, array('class' => 'form-control o-float-label__input', 'attr' =>'autocomplete="off"')); ?>
	<label for="<?php echo $id;?>"><?php echo $label;?></label>
</div>