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
<button type="button" class="btn eb-comp-toolbar__nav-btn" data-bp-toggle="dropdown">
	<i class="fa fa-code-fork"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_HISTORY');?>
</button>

<div class="dropdown-menu eb-comp-toolbar-dropdown-menu eb-comp-toolbar-dropdown-menu--revisions" data-revisions-container>
	<div class="eb-comp-toolbar-dropdown-menu__hd">
		<?php echo JText::_('COM_EASYBLOG_COMPOSER_HISTORY');?>
		<div class="eb-comp-toolbar-dropdown-menu__hd-action">
			<?php if ($post->canPurgeRevisions() && $post->getRevisionCount('all') > 1) { ?>
				<a href="javascript:void(0);" class="btn btn-eb-default btn--xs" data-eb-revision-purge><?php echo JText::_('COM_EASYBLOG_CLEAR_HISTORY');?></a>
			<?php } ?>

			<a href="javascript:void(0);" class="eb-comp-toolbar-dropdown-menu__close" data-toolbar-dropdown-close>
				<i class="fa fa-times-circle"></i>
			</a>
		</div>

	</div>
	<div class="eb-comp-toolbar-dropdown-menu__bd">
		<div class="eb-comp-revisions-posts">
			<div class=" eb-revisions-list-field" data-eb-revisions-list-field>
				<div class="eb-revision-listing" data-eb-revisions-list>
					<?php echo $this->output('site/composer/revisions/list', array('revisions' => $revisions)); ?>
				</div>
			</div>
		</div>
	</div>
</div>
