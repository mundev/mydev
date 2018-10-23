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
<div class="eb-fields-hyperlink">
	<div>
		<textarea class="form-control" 
				name="<?php echo $formElement;?>[<?php echo $field->id;?>][textlink]" 
				placeholder="<?php echo $params->get('placeholder');?>" 
				cols="1" 
				rows="2" 
				style="resize: none;" 
				data-field-class-input-hyperlink
			><?php echo isset($value->textlink) ? $value->textlink : '';?></textarea>
	</div>

	<div style="padding-top: 10px;">
		<input type="text" 
			class="form-control" 
			name="<?php echo $formElement;?>[<?php echo $field->id;?>][url]" 
			value="<?php echo isset($value->url) ? $value->url : '';?>" 
			placeholder="<?php echo JText::_('COM_EB_FIELDS_TYPE_HYPERLINK_URL_PLACEHOLDER');?>"
		/>
	</div>

	<div style="padding-top: 10px;">
		<select id="<?php echo $formElement;?>[<?php echo $field->id;?>][targetblank]" 
				name="<?php echo $formElement;?>[<?php echo $field->id;?>][targetblank]" 
				size="1" 
				class="form-control input select"
			>
			<option value="1" <?php echo isset($value->targetblank) && $value->targetblank ? 'selected="selected"' : '';?>>Open in new tab</option>
			<option value="0" <?php echo isset($value->targetblank) && !$value->targetblank ? 'selected="selected"' : '';?>>Stay on same page</option>
		</select>
	</div>
</div>