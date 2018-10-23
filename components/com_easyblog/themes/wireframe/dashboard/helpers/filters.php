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
<div class="eb-filter-select-group eb-filter-select-group--inline ">
	<select class="form-control " name="filter" data-eb-filter-dropdown>
		<option value="all"<?php echo $state == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_SELECT_FILTER');?></option>
		<option value="<?php echo EASYBLOG_POST_PUBLISHED;?>"<?php echo $state === EASYBLOG_POST_PUBLISHED ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_PUBLISHED');?></option>
		<option value="<?php echo EASYBLOG_POST_UNPUBLISHED;?>"<?php echo $state === EASYBLOG_POST_UNPUBLISHED ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_UNPUBLISHED');?></option>
		<option value="<?php echo EASYBLOG_POST_PENDING;?>"<?php echo $state === EASYBLOG_POST_PENDING ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_UNDER_REVIEW');?></option>
		<option value="<?php echo EASYBLOG_POST_SCHEDULED;?>"<?php echo $state === EASYBLOG_POST_SCHEDULED ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_SCHEDULED');?></option>
		<option value="<?php echo EASYBLOG_POST_DRAFT;?>"<?php echo $state === EASYBLOG_POST_DRAFT ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_DRAFT');?></option>
		<option value="<?php echo EASYBLOG_DASHBOARD_TRASHED;?>"<?php echo $state === EASYBLOG_DASHBOARD_TRASHED ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_FILTER_TRASHED');?></option>
	</select>
	<label class="eb-filter-select-group__drop"></label>
</div>
