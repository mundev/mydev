<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-form-group">
	<label for="copyrights" class="o-control-label eb-composer-field-label"><?php echo JText::_('COM_EASYBLOG_COPYRIGHTS'); ?></label>
	
	<div class="o-control-input">
		<textarea name="copyrights" class="form-control" rows="5"><?php echo $this->escape($post->copyrights); ?></textarea>
	</div>
</div>