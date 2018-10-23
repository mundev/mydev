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
<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_PDF_PREVIEWER_SIZE'); ?></strong>
	</div>

	<div class="eb-composer-fieldset-content">
		<div class="o-grid">
			<div class="o-grid__cell o-grid__cell--auto-size t-lg-mr--lg">
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_HEIGHT');?>
			</div>
			<div class="o-grid__cell">
				<input type="text" name="image-height" class="o-form-control" data-pdf-height value="600" />
			</div>
		</div>
	</div>
</div>