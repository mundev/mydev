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
<div class="o-form-group">
	<label class="o-control-label eb-composer-field-label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_POST_LANGUAGE');?></label>

	<div class="o-control-input">
		<select name="eb_language" class="o-form-control input-sm" data-composer-language>
			<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $post->language);?>
		</select>
	</div>
</div>
