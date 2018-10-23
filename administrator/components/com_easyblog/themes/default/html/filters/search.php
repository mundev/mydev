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
<input type="text" value="<?php echo $this->escape($value);?>" 
	placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH', true );?>" 
	data-table-grid-search-input 
	<?php if ($tooltip) { ?>
	data-eb-provide="tooltip"
	data-title="<?php echo $tooltip;?>"
	<?php } ?>
	class="form-control app-filter-bar__search-input" name="search"
/>
<span class="app-filter-bar__search-btn-group">
	<button class="btn btn-eb-default app-filter-bar__search-btn" data-table-grid-search="">
		<i class="fa fa-search"></i>
	</button>

	<button class="btn btn-eb-danger app-filter-bar__search-btn" data-table-grid-search-reset="">
		<i class="fa fa-times"></i>
	</button>
</span>

