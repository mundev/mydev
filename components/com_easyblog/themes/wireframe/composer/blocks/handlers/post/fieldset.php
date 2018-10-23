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
<div class="eb-composer-fieldset eb-video-controls-fieldset">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_FIELDSET_HEADER_GENERAL'); ?></strong>
	</div>
	<div class="eb-composer-fieldset-content">
		<?php echo $this->html('composer.field', 'form.toggler', 'show_image', 'COM_EASYBLOG_BLOCKS_POST_SHOW_IMAGE', true, array('data-post-option-image')); ?>

		<?php echo $this->html('composer.field', 'form.toggler', 'show_intro', 'COM_EASYBLOG_BLOCKS_POST_SHOW_INTRO', true, array('data-post-option-intro')); ?>

		<?php echo $this->html('composer.field', 'form.toggler', 'show_link', 'COM_EASYBLOG_BLOCKS_POST_SHOW_LINK', true, array('data-post-option-link')); ?>
	</div>
</div>
