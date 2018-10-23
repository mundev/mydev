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
<div class="es-story-blog-form" data-story-blog-form>
	<div class="o-grid">
		<div class="o-grid__cell t-lg-mr--md t-xs-mb--md">
			<div class="o-form-group">
				<input type="text" class="o-form-control" placeholder="<?php echo JText::_('APP_USER_BLOG_TITLE_PLACEHOLDER');?>" data-blog-title />
			</div>
		</div>

		<div class="o-grid__cell">
			<div class="o-form-group">
				<?php echo $categories; ?>
			</div>
		</div>
	</div>
	<div class="o-form-group">
		<textarea name="content" id="content" class="o-form-control" style="height:150px;" placeholder="<?php echo JText::_('APP_USER_BLOG_CONTENT_PLACEHOLDER');?>" data-blog-content></textarea>
	</div>
</div>
