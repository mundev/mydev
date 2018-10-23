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
<!-- this  hack is to fix the scrolly in safari browser -->
<style type="text/css">
body {
	margin: 0;
	padding: 0;
	overflow: hidden;
	text-rendering: optimizeLegibility;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
}
html,
body {
	height: 100%; /* needed for proper layout */
	overflow: hidden;
}
</style>

<div id="eb" class="eb-composer-frame is-loading view-document 
	<?php echo $template->doctype !== 'ebd' ? ' is-legacy' : 'not-legacy'; ?>"
	data-composer-frame
	data-composer-keepalive-interval="<?php echo $keepAlive;?>"
	data-composer-autosave-interval="0"
	data-composer-tags-enabled="<?php echo $this->config->get('layout_composer_tags') ? '1' : '0';?>"
	data-post-id="<?php echo $template->id; ?>"
	data-post-uid="<?php echo $post->uid; ?>"
	data-author-id="<?php echo $this->my->id;?>"
	data-permalink="<?php echo $post->isPublished() ? $post->getExternalPermalink() : '';?>"
	data-post-doctype="<?php echo $template->doctype; ?>">

	<?php echo $composer->renderTemplateManager($templateId); ?>

	<div class="eb-composer-ghosts" data-eb-composer-ghosts>
		<div class="ebd-workarea show-guide is-ghost" data-ebd-workarea-ghosts></div>
	</div>
</div>
