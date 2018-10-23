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
<?php if ($this->isMobile() && $cluster) { ?>
<a class="btn btn-es-default-o btn-sm t-lg-mb--lg" href="<?php echo $cluster->getPermalink();?>">&larr; <?php echo JText::sprintf('COM_EASYSOCIAL_BACK_TO_' . strtoupper($cluster->getType()));?></a>
<?php } ?>
<!-- Hashtag Snackbar -->
<?php if ((isset($tagsFilter) && $tagsFilter->id) || (isset($hashtags) && $hashtags)) { ?>
	<?php if ($tagsFilter->id) { ?>
		<div class="es-snackbar">
			<div class="es-snackbar__cell">
				<?php echo JText::sprintf('COM_EASYSOCIAL_VIDEOS_CUSTOM_FILTER_HEADER_TITLE', $tagsFilter->title); ?>
			</div>
			<div class="es-snackbar__cell">
				<a 
				data-video-create-filter
				data-type="videos"
				data-id="<?php echo $tagsFilter->id; ?>"
				data-cluster-type="<?php echo $type; ?>"
				data-uid="<?php echo $uid; ?>"
				title="<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FILTERS_EDIT_FILTER');?>"
				href="<?php echo $tagsFilter->getEditLink();?>" class="pull-right"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FILTERS_EDIT_FILTER'); ?></a>
			</div>
		</div>
		<p><?php echo $tagsFilter->getHashtagsLink($hashtags, 'videos', $uid, $type); ?></p>
	<?php } else { ?>
		<div class="es-snackbar">
			<div class="es-snackbar__cell">
				<?php echo $tagsFilter->getHashtagsLink($hashtags, 'videos', $uid, $type); ?>
			</div>
		</div>
	<?php } ?>
<!-- Default Snackbar -->
<?php } else { ?>
	<?php if (isset($filter) && $filter == 'pending') { ?>
		<div class="es-snackbar"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_PENDING_TITLE');?></div>
		<p class="pending-info"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_PENDING_INFO');?></p>
		<hr />
	<?php } ?>

	<?php if ((isset($isFeatured) && $isFeatured) || (isset($filter) && $filter == 'featured')) { ?>
		<div class="es-snackbar">
			<?php echo JText::_("COM_EASYSOCIAL_VIDEOS_FEATURED_VIDEOS");?>
		</div> 
	<?php } else { ?>
		<div class="es-snackbar">
			<?php echo JText::_("COM_EASYSOCIAL_VIDEOS");?>
		</div>
	<?php } ?>
<?php } ?>