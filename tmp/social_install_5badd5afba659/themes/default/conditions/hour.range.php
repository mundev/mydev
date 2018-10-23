<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<input data-condition type="hidden" class="o-form-control input-sm" name="conditions[]" value="<?php echo $this->html('string.escape', $selected);?>" <?php echo $condition->range ? 'data-range' : ''; ?>/>
<?php
	$data[0] = '';
	$data[1] = '';

	if ($selected) {
		$tmp = explode( '|', $selected );
		$data[0] = $tmp[0];
		$data[1] = $tmp[1];
	}
?>
<select class="o-form-control input-sm" data-item-start>
	<option value=""><?php echo JText::_('COM_ES_PAGE_FIELD_START_HOUR'); ?></option>
	<?php foreach ($condition->list as $item) { ?>
	<option value="<?php echo $item->value; ?>"<?php echo $item->value == $data[0] ? ' selected="true"' : ''; ?>><?php echo $item->title; ?></option>
	<?php } ?>
</select>
<select class="o-form-control input-sm" data-item-end>
	<option value=""><?php echo JText::_('COM_ES_PAGE_FIELD_END_HOUR'); ?></option>
	<?php foreach ($condition->list as $item) { ?>
	<option value="<?php echo $item->value; ?>"<?php echo $item->value == $data[1] ? ' selected="true"' : ''; ?>><?php echo $item->title; ?></option>
	<?php } ?>
</select>
