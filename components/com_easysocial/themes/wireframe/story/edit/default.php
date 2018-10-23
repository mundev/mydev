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
<div class="es-story is-editing is-expanded" data-stream-id="<?php echo $streamId; ?>" data-story="<?php echo $story->id;?>" data-story-form data-story-hashtags="<?php echo implode(',', $story->hashtags); ?>">

	<div class="es-story-body" data-body>
		<div class="es-story-text-placeholder-ie9"><?php echo $placeholderText; ?></div>
		<div class="es-story-text">
			<div class="es-story-textbox mentions-textfield" data-story-textbox>
				<div class="mentions">
					<div data-mentions-overlay data-default="<?php echo $this->html('string.escape', $defaultOverlay); ?>"><?php echo $story->overlay; ?></div>
					<textarea class="es-story-textfield" name="content" data-story-textField
						data-mentions-textarea
						data-default="<?php echo $this->html('string.escape', $defaultContent); ?>"
						data-initial="0"
						placeholder="<?php echo $placeholderText; ?>"><?php echo $story->content; ?></textarea>
				</div>
			</div>
		</div>

		<?php if ($story->panels) { ?>
		<div class="es-story-panel-content">
			<div class="es-story-panel-contents active" data-story-panel-contents>
				<?php foreach ($story->panels as $panel) { ?>
					<div class="es-story-panel-content <?php echo $panel->content->classname; ?> for-<?php echo $panel->name; ?>" data-story-panel-content data-story-plugin-name="<?php echo $panel->name; ?>">
						<?php echo $panel->content->html; ?>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>

	</div>

	<div class="es-story-footer" data-footer>

		<?php if ($this->config->get('stream.story.mentions') || $this->config->get('stream.story.location') || $this->config->get('stream.story.moods')) { ?>
		<div class="es-story-meta-contents" data-story-meta-contents>

			<?php if ($this->config->get('stream.story.mentions')) { ?>
			<div class="es-story-meta-content" data-story-meta-content="friends">
				<div class="es-story-friends" data-story-friends>
					<div class="es-story-friends-textbox textboxlist" data-friends-wrapper>

						<?php if ($mentionedUsers) { ?>
							<?php foreach ($mentionedUsers as $user) { ?>
								<div data-textboxlist-item="" class="textboxlist-item" data-id="<?php echo $user->id; ?>"><span data-textboxlist-itemcontent="" class="textboxlist-itemContent">
								<img width="16" height="16" data-suggest-avatar="" src="<?php echo $user->getAvatar(SOCIAL_AVATAR_MEDIUM); ?>"> <?php echo $user->getName(); ?>
								<input type="hidden" data-suggest-title="" value="<?php echo $user->getName(); ?>">
								<input type="hidden" data-suggest-id="" value="<?php echo $user->id; ?>" name=""></span><div data-textboxlist-itemremovebutton="" class="textboxlist-itemRemoveButton"><i class="fa fa-times"></i></div></div>
							<?php } ?>
						<?php } ?>

						<input type="text" class="textboxlist-textField" autocomplete="off" placeholder="<?php echo $mentionedUsers ? '' : JText::_('COM_EASYSOCIAL_WHO_ARE_YOU_WITH', true); ?>" data-textboxlist-textField />
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
							<button class="btn btn-es-default-o btn-sm es-location-detect-button" type="button" data-story-location-detect-button>
								<i class="fa fa-map-marker t-text--danger"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_DETECT_MY_LOCATION', true); ?>
							</button>
						</div>
					</div>
				</div>

				<div class="es-location-form" data-story-location-form>
					<div class="es-location-textbox" data-story-location-textbox data-language="<?php echo $this->config->get('general.location.language'); ?>">
						<input type="text" class="input-sm form-control" placeholder="<?php echo JText::_('COM_EASYSOCIAL_WHERE_ARE_YOU_NOW'); ?>" autocomplete="off" data-story-location-textField disabled value="<?php echo $currentLocation ? $currentLocation->address : ''; ?>" />
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
			<div class="es-story-meta-content es-story-mood <?php echo $currentMood ? 'using-preset' : 'is-empty'; ?>" data-story-mood data-story-meta-content="mood">
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
							<input type="text" class="input-sm form-control" placeholder="<?php echo JText::_('COM_EASYSOCIAL_HOW_ARE_YOU_FEELING'); ?>" autocomplete="off" data-story-mood-textfield />

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
				<i class="fa fa-user"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_STORY_META_PEOPLE');?>
			</div>
			<?php } ?>

			<?php if ($this->config->get('stream.story.location')) { ?>
			<div class="btn btn-sm es-story-meta-button" data-story-meta-button="location">
				<i class="fa fa-map-marker"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_STORY_META_LOCATION');?>
			</div>
			<?php } ?>

			<?php if ($this->config->get('stream.story.moods')) { ?>
			<div class="btn btn-sm es-story-meta-button" data-story-meta-button="mood">
				<i class="fa fa-smile-o"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_STORY_META_MOOD');?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>

		<div class="es-story-actions">
			<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm pull-left t-lg-mr--sm" data-edit-cancel><?php echo JText::_('COM_ES_CANCEL');?></a>
			<a href="javascript:void(0);" class="btn btn-es-primary-o btn-sm pull-right" data-story-submit>
				<?php echo JText::_('COM_ES_UPDATE');?>
			</a>
		</div>

	</div>

	<?php echo $this->html('suggest.hashtags'); ?>
	<?php echo $this->html('suggest.friends'); ?>

	<input type="hidden" name="target" data-story-target value="<?php echo $story->getTarget(); ?>" />
	<input type="hidden" name="cluster" data-story-cluster value="<?php echo $story->getClusterId(); ?>" />
	<input type="hidden" name="clustertype" data-story-clustertype value="<?php echo $story->getClusterType(); ?>" />

	<div class="story-loading"><div class="o-loader is-active"></div></div>

</div>
