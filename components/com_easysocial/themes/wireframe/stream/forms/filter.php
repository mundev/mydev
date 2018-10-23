<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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
	<form method="post" action="<?php echo JRoute::_('index.php');?>" class="es-forms">	
		<div class="es-forms__group">
			<div class="es-forms__title">
				<?php echo $this->html('form.title', $filter->id ? 'COM_ES_EDITING_FILTER' : 'COM_EASYSOCIAL_STREAM_FILTER_CREATE_NEW_FILTER'); ?>
			</div>

			<div class="es-forms__content">
				<div class="es-stream-filter-content">
					<p><?php echo $desc; ?></p>

					<div class="alert alert-danger t-hidden" data-notice></div>

					<div class="o-form-group" data-title>
						<input type="text" name="title" class="o-form-control"  placeholder="<?php echo JText::_('COM_EASYSOCIAL_STREAM_FILTER_TITLE_PLACEHOLDER', true); ?>"  value="<?php echo $this->html('string.escape', $filter->title); ?>"  />
						
						<div class="help-block text-note"><?php echo JText::_('COM_EASYSOCIAL_STREAM_FILTER_TITLE_DESC'); ?></div>
					</div>

					<div class="o-form-group" data-hashtag>
						<input type="text" name="hashtag" value="<?php echo $filter->getHashTag(true); ?>" class="o-form-control" placeholder="<?php echo JText::_('COM_EASYSOCIAL_STREAM_FILTER_HASHTAG_PLACEHOLDER', true ); ?>" />
						
						<div class="help-block text-note"><?php echo JText::_('COM_EASYSOCIAL_STREAM_FILTER_HASHTAG_DESC'); ?></div>
					</div>
				</div>
			</div>

			<div class="es-forms__actions">
				<div class="o-form-actions">
					<button type="button" class="btn btn-es-primary-o t-lg-pull-right" data-save><?php echo JText::_('COM_ES_SAVE');?></button>

					<?php if ($filter->id) { ?>
					<a href="javascript:void(0);" class="btn btn-es-danger-o t-lg-pull-left" data-delete data-id="<?php echo $filter->id; ?>" data-uid="<?php echo $filter->uid; ?>" data-utype="<?php echo $filter->utype; ?>">
						<?php echo JText::_('COM_ES_DELETE');?>
					</a>
					<?php } ?>
				</div>
			</div>
		</div>

		<?php echo $this->html('form.action', 'stream', 'saveFilter'); ?>

		<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
		<input type="hidden" name="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="id" value="<?php echo $filter->id; ?>" />
	</form>
</div>
