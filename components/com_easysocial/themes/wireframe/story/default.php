<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-story is-expanded" data-story="<?php echo $story->id;?>" data-story-form data-story-hashtags="<?php echo implode(',', $story->hashtags); ?>" data-story-module="<?php echo $fromModule; ?>">

	<div class="es-story-header">
		<div class="es-story-panel-buttons" data-story-panel-buttons>
			<?php if (!$singlePanel || ($singlePanel && $panelType == 'text')) { ?>
			<div class="es-story-panel-button active" data-story-panel-button data-story-plugin-name="text">
				<i class="fa fa-pencil" data-es-provide="tooltip" data-placement="top" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_POST_STATUS');?>"></i>
				<span><?php echo JText::_('COM_EASYSOCIAL_STORY_STATUS', true);?></span>
			</div>
			<?php } ?>

			<?php if ($this->isMobile() || $this->isTablet())  { ?>
				<?php if ($story->panelsMain) { ?>
					<?php foreach ($story->panelsMain as $panel) { ?>
						<div class="es-story-panel-button" data-story-panel-button data-story-plugin-name="<?php echo $panel->name;?>">
							<?php echo $panel->button->html;?>
						</div>
					<?php } ?>
				<?php } ?>

				<div class="dropdown es-story-panel-dropdown <?php echo $appExists ? 't-hidden' : '';?>">
					<a href="javascript:void(0);" data-bs-toggle="dropdown" class="es-story-panel-dropdown-toggle dropdown-toggle_">
						<i class="fa fa-chevron-down"></i>
					</a>
					<div class="dropdown-menu es-story-panel-dropdown-menu">
						<?php if ($story->panelsSecondary) { ?>
							<?php foreach ($story->panelsSecondary as $panel) { ?>
							<div class="es-story-panel-button" data-story-panel-button data-story-plugin-name="<?php echo $panel->name;?>"><?php echo $panel->button->html;?></div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
			<?php } else { ?>
				<?php if ($story->panels) { ?>
					<?php foreach ($story->panels as $panel) { ?>
						<div class="es-story-panel-button" data-story-panel-button data-story-plugin-name="<?php echo $panel->name;?>">
							<?php echo $panel->button->html;?>
						</div>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</div>
	</div>

	<div class="es-story-body" data-body>
		<div class="es-story-text-placeholder-ie9"><?php echo $placeholderText; ?></div>
		<div class="es-story-text">
			<div class="es-story-textbox mentions-textfield" data-story-textbox>
				<div class="mentions">
					<div data-mentions-overlay data-default="<?php echo $this->html('string.escape', $story->overlay); ?>"><?php echo $story->overlay; ?></div>
					<textarea class="es-story-textfield" name="content" data-story-textField data-mentions-textarea
						data-default="<?php echo $this->html('string.escape', $story->content); ?>"
						data-initial="<?php echo ($story->overlay) ? JString::strlen($story->overlay): '0'; ?>"
						placeholder="<?php echo $placeholderText; ?>"><?php echo $story->content; ?></textarea>
				</div>
				<div>
					<div data-mentions-meta-overlay></div>
				</div>
			</div>
		</div>

		<div class="es-story-panel-content">
			<div class="es-story-panel-contents" data-story-panel-contents>
				<?php foreach ($story->panels as $panel) { ?>
					<div class="es-story-panel-content <?php echo $panel->content->classname; ?> for-<?php echo $panel->name; ?>" data-story-panel-content data-story-plugin-name="<?php echo $panel->name; ?>">
						<?php echo $panel->content->html; ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="es-story-footer" data-footer>

		<?php if ($this->config->get('stream.story.mentions') || $this->config->get('stream.story.location') || $this->config->get('stream.story.moods')) { ?>
		<div class="es-story-meta-contents" data-story-meta-contents>

			<?php if ($this->config->get('stream.story.mentions')) { ?>
			<div class="es-story-meta-content" data-story-meta-content="friends">
				<div class="es-story-friends" data-story-friends>
					<div class="es-story-friends-textbox textboxlist" data-friends-wrapper>
						<input type="text" class="textboxlist-textField" autocomplete="off" placeholder="<?php echo JText::_('COM_EASYSOCIAL_WHO_ARE_YOU_WITH', true); ?>" data-textboxlist-textField />
					</div>
				</div>
			</div>
			<?php } ?>

			<?php if ($this->config->get('stream.story.location')) { ?>
			<div class="es-story-meta-content es-locations" data-story-location data-story-meta-content="location">
				<div class="es-location-map" data-story-location-map>
					<div>
						<img class="es-location-map-image" data-story-location-map-image />
						<div class="es-location-map-actions">
							<button class="btn btn-es-default-o es-location-detect-button" type="button" data-story-location-detect-button>
								<i class="fa fa-map-marker t-text--danger"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_DETECT_MY_LOCATION', true); ?>
							</button>
						</div>
					</div>
				</div>

				<div class="es-location-form" data-story-location-form>
					<div class="es-location-textbox" data-story-location-textbox data-language="<?php echo $this->config->get('general.location.language'); ?>">
						<input type="text" class="o-form-control" placeholder="<?php echo JText::_('COM_EASYSOCIAL_WHERE_ARE_YOU_NOW'); ?>" autocomplete="off" data-story-location-textField disabled/>
						<div class="es-location-autocomplete has-shadow is-sticky" data-story-location-autocomplete>
							<b><b></b></b>
							<div class="es-location-suggestions" data-story-location-suggestions></div>
						</div>
					</div>
					<div class="es-location-buttons">
						<div class="o-loader o-loader--sm"></div>
						<a class="es-location-remove-button" href="javascript: void(0);" data-story-location-remove-button>
							<i class="fa fa-remove"></i>
						</a>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php if ($this->config->get('stream.story.moods')) { ?>
			<div class="es-story-meta-content es-story-mood is-empty" data-story-mood data-story-meta-content="mood">
				<div class="es-story-mood-form">
					<table class="es-story-mood-textbox" data-story-mood-textbox>
						<tr><td>
							<div class="es-story-mood-verb" data-story-mood-verb>
								<?php foreach ($moods as $mood) { ?>
									<span<?php echo ($mood->key == 'feeling') ? ' class="active"' : ''; ?> data-story-mood-verb-type="<?php echo $mood->key; ?>"><?php echo JText::_($mood->verb); ?></span>
								<?php } ?>
							</div>
						</td>
						<td width="100%">
							<input type="text" class="o-form-control" placeholder="<?php echo JText::_('COM_EASYSOCIAL_HOW_ARE_YOU_FEELING'); ?>" autocomplete="off" data-story-mood-textfield />
						</td>
						</tr>
					</table>
					<div class="es-story-mood-buttons">
						<a class="es-story-mood-remove-button" href="javascript: void(0);" data-story-mood-remove-button><i class="fa fa-remove"></i></a>
					</div>
				</div>
				<div class="es-story-mood-presets" data-story-mood-presets>
					<ul class="g-list-unstyled">
					<?php foreach ($moods as $mood) { ?>
						<?php foreach ($mood->moods as $preset) { ?>
						<li class="es-story-mood-preset"
							data-story-mood-preset
							data-story-mood-icon="<?php echo $preset->icon ?>"
							data-story-mood-verb="<?php echo $mood->key; ?>"
							data-story-mood-subject="<?php echo $preset->key; ?>"
							data-story-mood-text="<?php echo JText::_($preset->text); ?>"
							data-story-mood-subject-text="<?php echo JText::_($preset->subject); ?>"><i class="es-emoji <?php echo $preset->icon; ?>"></i> <?php echo JText::_($preset->subject); ?></li>
						<?php } ?>
					<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>

		<?php if ($this->config->get('stream.story.mentions') || $this->config->get('stream.story.location') || $this->config->get('stream.story.moods')) { ?>
		<div class="es-story-meta-buttons">
			<?php if ($this->config->get('stream.story.mentions')) { ?>
			<div class="btn btn-sm es-story-meta-button" data-story-meta-button="friends">
				<i class="fa fa-user"></i>&nbsp; <span><?php echo JText::_('COM_EASYSOCIAL_STORY_META_PEOPLE');?></span>
			</div>
			<?php } ?>

			<?php if ($this->config->get('stream.story.location')) { ?>
			<div class="btn btn-sm es-story-meta-button" data-story-meta-button="location">
				<i class="fa fa-map-marker"></i>&nbsp; <span><?php echo JText::_('COM_EASYSOCIAL_STORY_META_LOCATION');?></span>
			</div>
			<?php } ?>

			<?php if ($this->config->get('stream.story.moods')) { ?>
			<div class="btn btn-sm es-story-meta-button" data-story-meta-button="mood">
				<i class="fa fa-smile-o"></i>&nbsp; <span><?php echo JText::_('COM_EASYSOCIAL_STORY_META_MOOD');?></span>
			</div>
			<?php } ?>
		</div>
		<?php } ?>

		<div class="es-story-actions <?php echo $story->requirePrivacy() ? '' : ' no-privacy'; ?>">
			<?php if ($story->autoposts) { ?>
			<div class="es-story-actions__share" data-story-autopost>
				<?php foreach ($story->autoposts as $autopost) { ?>
					<?php echo $autopost; ?>
				<?php } ?>
			</div>
			<?php } ?>


			<button class="btn btn-es-primary btn-sm es-story-submit" data-story-submit type="button"><?php echo JText::_("COM_EASYSOCIAL_STORY_SHARE"); ?></button>
			<?php if ($story->requirePrivacy()) { ?>
			<div class="es-story-privacy" data-story-privacy>
				<?php echo ES::privacy()->form(null, SOCIAL_TYPE_STORY, $this->my->id, 'story.view', true); ?>
			</div>
			<?php } ?>

		</div>

		<?php if ($story->requirePostAs()) { ?>
			<?php echo $this->html('form.postAs', array('page' => $story->cluster, 'user' => $this->my->id)); ?>
		<?php } ?>

	</div>

	<?php echo $this->html('suggest.hashtags'); ?>
	<?php echo $this->html('suggest.friends'); ?>

	<?php if ($customParams) { ?>
		<?php foreach ($customParams as $key => $value) { ?>
			<input type="hidden" name="params[<?php echo $key;?>]" value="<?php echo $value;?>" data-story-params />
		<?php } ?>
	<?php } ?>

	<input type="hidden" name="target" data-story-anywhere value="<?php echo $story->getAnywhereId(); ?>" />
	<input type="hidden" name="target" data-story-target value="<?php echo $story->getTarget(); ?>" />
	<input type="hidden" name="cluster" data-story-cluster value="<?php echo $story->getClusterId(); ?>" />
	<input type="hidden" name="clustertype" data-story-clustertype value="<?php echo $story->getClusterType(); ?>" />

	<div class="story-loading"><div class="o-loader is-active"></div></div>

</div>
