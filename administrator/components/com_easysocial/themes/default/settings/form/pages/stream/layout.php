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
<div class="row">
	<div class="col-md-6">

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_STREAM_SETTINGS_LAYOUT'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_STREAM_SETTINGS_TRUNCATION'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'stream.content.truncate', $this->config->get('stream.content.truncate')); ?> 
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_STREAM_SETTINGS_TRUNCATION_LENGTH'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.inputbox', 'stream.content.truncatelength', $this->config->get('stream.content.truncatelength'), '', array('class' => 'input-short text-center')); ?>
						<?php echo JText::_('COM_EASYSOCIAL_CHARACTERS'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_THEMES_WIREFRAME_FIELD_DATE_DISPLAY_STYLE'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'stream.timestamp.style', $this->config->get('stream.timestamp.style'), array(
								array('value' => 'elapsed', 'text' => 'COM_EASYSOCIAL_STREAM_TIMESTAMP_ELAPSED'),
								array('value' => 'datetime', 'text' => 'COM_EASYSOCIAL_STREAM_TIMESTAMP_STANDARD')
							), 'stream-timestamp-style', array('data-timestamp-style')); ?>
					</div>
				</div>

				<div class="form-group <?php echo $this->config->get('stream.timestamp.style') == 'datetime' ? '' : 't-hidden';?>" data-datetime-format>
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_THEMES_WIREFRAME_FIELD_DATETIME_FORMAT'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.inputbox', 'stream.timestamp.format', $this->config->get('stream.timestamp.format')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_STREAM_SETTINGS_STORY_FORM'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_STREAM_SETTINGS_DISPLAY_MENTIONS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'stream.story.mentions', $this->config->get('stream.story.mentions')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_STREAM_SETTINGS_DISPLAY_LOCATION'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'stream.story.location', $this->config->get('stream.story.location')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_STREAM_SETTINGS_ENABLE_MOODS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'stream.story.moods', $this->config->get('stream.story.moods')); ?>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>