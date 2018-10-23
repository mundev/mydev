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
<div class="eb-comp-toolbar">
	<div class="eb-comp-toolbar__back">
		<a href="javascript:void(0);" class="btn eb-comp-toolbar__btn-back" data-url="<?php echo $returnUrl;?>" data-composer-exit>
			<i class="fa fa-long-arrow-left"></i> 
			<?php if (!$this->isMobile()) { ?>
				<?php echo JText::_('COM_EASYBLOG_BACK');?>
			<?php } ?>
		</a>	
	</div>

	<div class="eb-comp-toolbar__mobile-nav">

		<?php if ($post->isBlank() || $post->isPublished() || ($post->isDraft() && !$post->isPostUnpublished())) { ?>
		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-savedraft btn eb-comp-toolbar__nav-btn" data-composer-save-draft>
			<?php echo JText::_('COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON');?>
		</a>
		<?php } ?>

		<?php if (($post->isBlank() || ($post->isNew() && !$post->isPending()) || $post->isUnpublished() || $post->isPostUnpublished()) && !$post->isScheduled() && $this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-publish btn eb-comp-toolbar__nav-btn btn-eb-primary" data-composer-publish>
			<?php echo JText::_('COM_EASYBLOG_PUBLISH');?>
		</a>
		<?php } ?>

		<?php if ((!$post->isBlank() && (!$post->isDraft() || ($post->isDraft() && $post->isPostUnpublished()) )) && $this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-publish btn eb-comp-toolbar__nav-btn<?php echo $post->isUnpublished() || $post->isPostUnpublished() || $post->isPending() ? ' btn-eb-default' : ' btn-eb-primary'; ?>" data-composer-update>
			<?php echo JText::_('COM_EASYBLOG_UPDATE_POST');?>
		</a>
		<?php } ?>

		<?php if ((!$post->isBlank() && !$post->isNew() && $post->isDraft() && !$post->isPostUnpublished()) && $this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-publish btn eb-comp-toolbar__nav-btn btn-eb-primary" data-composer-publish>
			<?php echo JText::_('COM_EASYBLOG_PUBLISH');?>
		</a>
		<?php } ?>
		
		<?php if ((!$post->isBlank() || !$post->isPublished()) && !$this->acl->get('publish_entry')) { ?>
		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-publish btn eb-comp-toolbar__nav-btn btn-eb-success" data-composer-submit-approval>
			<?php echo JText::_('COM_EASYBLOG_SUBMIT_POST_FOR_APPROVAL'); ?>
		</a>
		<?php } ?>

		<a href="javascript:void(0);" class="eb-comp-toolbar__btn-option btn eb-comp-toolbar__nav-btn" data-composer-mobile-info>
			<i class="fa fa-ellipsis-h"></i>
		</a>
	</div>
		
	<div class="eb-comp-toolbar__nav toolbar-dropping" data-toolbar-blocks>
		<div class="eb-block-hint">
			<i class="fa fa-hand-o-up"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_DROP_DESC');?>
		</div>

		<button type="button" class="btn eb-comp-toolbar__nav-btn-cancel" data-blocks-cancel-drop-button>
			<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CANCEL_DROP');?>
		</button>
	</div>

	<div class="eb-comp-toolbar__nav toolbar-moving" data-toolbar-blocks>
		<div class="eb-block-hint eb-block-hint-moving">
			<i class="fa fa-arrows"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_MOVE_DESC');?>
		</div>

		<button type="button" class="btn eb-comp-toolbar__nav-btn-cancel" data-blocks-cancel-move>
			<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CANCEL_DROP');?>
		</button>
	</div>

	<div class="eb-comp-toolbar__nav toolbar-composing" data-toolbar-composing>		
		<?php if ((!$post->isLegacy() && !$templateEditor) || ($templateEditor && !$postTemplate->isLegacy())) { ?>
		<div class="btn-group" data-toolbar-blocks-wrapper>
			<?php echo $this->output('site/composer/toolbar/blocks'); ?>
		</div>
		<?php } else { ?>
			<?php echo $this->output('site/composer/toolbar/blocks.legacy'); ?>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_history') && !$templateEditor) { ?>
		<div class="btn-group" data-toolbar-view data-type="revisions">
			<?php echo $this->output('site/composer/toolbar/revisions'); ?>
		</div>
		<?php } ?>

		<?php if ($post->isLegacy()) { ?>
		<div class="btn-group" data-toolbar-view data-type="video">
			<button type="button" class="btn eb-comp-toolbar__nav-btn" data-eb-composer-embed-video>
				<i class="fa fa-film"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_EMBED_VIDEO');?>
			</button>
		</div>
		<?php } ?>

		<div class="btn-group">
			<button type="button" class="btn eb-comp-toolbar__nav-btn" data-eb-composer-media data-uri="post">
				<i class="fa fa-camera"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_MEDIA');?>
			</button>
		</div>

		<div class="btn-group" data-toolbar-view data-type="posts">
			<?php echo $this->output('site/composer/toolbar/posts'); ?>
		</div>

		<?php if ($this->config->get('main_locations')) { ?>
		<div class="btn-group" data-toolbar-view data-type="location">
			<?php echo $this->output('site/composer/toolbar/location'); ?>
		</div>
		<?php } ?>
		
		<div class="btn-group" data-toolbar-view data-type="cover">
			<?php echo $this->output('site/composer/toolbar/cover'); ?>
		</div>
			
	</div>

	<div class="eb-comp-toolbar__close">
		<a href="javascript:void(0);" data-eb-composer-toggle-sidebar><i class="fa eb-comp-toolbar__close-icon"></i></a>	
	</div>
</div>