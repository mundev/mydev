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
<div class="row" data-form-category-wrapper>
	<div class="col-lg-10">
		<div class="input-group">
			<input type="text" id="<?php echo $id;?>-placeholder" class="form-control" value="<?php echo $categoryTitle;?>" disabled="disabled" />
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" data-form-remove-category>
					<i class="fa fa-times"></i>
				</button>
				<button class="btn btn-default" type="button" data-form-browse-category>
					<i class="fa fa-folder-o"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SELECT_CATEGORY_BUTTON');?>
				</button>
			</span>
		</div>
		<input type="hidden" name="<?php echo $name;?>" id="<?php echo $id;?>" value="<?php echo $value;?>" />
	</div>
</div>

<script type="text/javascript">
EasyBlog.ready(function($) {

	window.insertCategory = function(id, name) {
		$('#<?php echo $id;?>-placeholder').val(name);
		$('#<?php echo $id;?>').val(id);

		EasyBlog.dialog().close();
	}

	$('[data-form-remove-category]').on('click', function() {
		var button = $(this);
		var parent = button.parents('[data-form-category-wrapper]');

		// Reset the form
		parent.find('input[type=hidden]').val('');
		parent.find('input[type=text]').val('');
	});

	$('[data-form-browse-category]').on('click', function() {
		EasyBlog.dialog({
			content: EasyBlog.ajax('admin/views/categories/browse')
		});
	});

});
</script>