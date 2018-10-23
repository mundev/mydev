<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="stream-filter" data-es-filter-form>
	<form method="post" action="<?php echo JRoute::_('index.php');?>" class="form-horizontal">	
		<div class="es-snackbar">
			<?php if (!$filter->id) { ?>
				<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_CUSTOM_FILTER_HEADER'); ?>
			<?php } else { ?>
				<?php echo JText::sprintf('COM_EASYSOCIAL_VIDEOS_CUSTOM_FILTER_EDIT_HEADER', $filter->title); ?>
			<?php } ?>
		</div>

		<div>
			<p class="t-lg-mt--xl t-lg-mb--xl"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_CUSTOM_FILTER_DESC'); ?></p>

			<div class="alert alert-danger t-hidden" data-notice></div>

			<div class="o-form-group" data-title>
				<input type="text" name="title" class="o-form-control" placeholder="<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_CUSTOM_FILTER_TITLE_PLACEHOLDER', true);?>" value="<?php echo $this->html('string.escape', $filter->title);?>"  />
				
				<div class="help-block text-note"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_CUSTOM_FILTER_TITLE_DESC'); ?></div>
			</div>

			<div class="o-form-group" data-hashtag>
				<input type="text" name="hashtag" value="<?php echo $filter->getHashTag(true); ?>" class="o-form-control" placeholder="<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_CUSTOM_FILTER_HASHTAG_PLACEHOLDER', true);?>" />
				
				<div class="help-block text-note"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_CUSTOM_FILTER_HASHTAG_DESC'); ?></div>
			</div>
		</div>

		<div class="o-form-actions">
			<button type="button" class="btn btn-es-primary-o t-lg-pull-right" data-save><?php echo JText::_('COM_ES_SAVE');?></button>

			<?php if ($filter->id) { ?>
			<a
			data-delete
			data-type="<?php echo $filterType; ?>"
			data-id="<?php echo $filter->id; ?>"
			data-cid="<?php echo $cid;?>"
			data-cluster-type="<?php echo $clusterType; ?>"
			 href="javascript:void(0);" class="btn btn-es-danger-o t-lg-pull-left"><?php echo JText::_('COM_ES_DELETE');?></a>
			<?php } ?>
		</div>

		<input type="hidden" name="filterType" value="<?php echo $filterType;?>" />
		<input type="hidden" name="clusterType" value="<?php echo $clusterType?>" />
		<input type="hidden" name="cid" value="<?php echo $cid;?>" />
		<input type="hidden" name="controller" value="tag" />
		<input type="hidden" name="option" value="com_easysocial" />
		<input type="hidden" name="task" value="saveFilter" />
		<input type="hidden" name="id" value="<?php echo $filter->id;?>" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
