
EasySocial.require()
.script('site/story/story')
.done(function($) {

	// Story controller
	$("[data-story=<?php echo $story->id; ?>]")
		.addController("EasySocial.Controller.Story", {

			"errors": {
				"empty": "<?php echo JText::_('COM_EASYSOCIAL_STORY_CONTENT_EMPTY', true);?>",
				"filter": "<?php echo JText::_('COM_EASYSOCIAL_STORY_NOT_ON_STREAM_FILTER', true);?>",
				"standard": "<?php echo JText::_('COM_EASYSOCIAL_STORY_SUBMIT_ERROR', true);?>"
			},

			"moodText": "<?php echo JText::_('COM_EASYSOCIAL_MOOD_VERB_FEELING', true);?>",

			"flood": {
				"enabled": <?php echo !$this->my->isSiteAdmin() && $this->access->get('story.flood.user') ? 'true' : 'false' ; ?>,
				"interval": '<?php echo $this->access->get('story.flood.interval', '90'); ?>',
				"submit": <?php echo $this->my->canSubmitStory() ? 'true' : 'false'; ?>
			},

			<?php
			if ($story->plugins) {
				$length = count($story->plugins);
				$i = 0;
			?>
				plugin: {
					<?php foreach($story->plugins as $plugin) { ?>
					<?php echo $plugin->name; ?>: {
						id: '<?php echo $plugin->id; ?>',
						type: '<?php echo $plugin->type; ?>',
						name: '<?php echo $plugin->name; ?>'
					}<?php if (++$i < $length) { echo ','; }; ?>
					<?php } ?>
				},
			<?php } ?>
				enterToSubmit: false,
				sourceView: "<?php echo JRequest::getCmd('view',''); ?>",
				hashtagEditable: "<?php echo $story->hashtagEditable; ?>",
				singlePanel: "<?php echo $singlePanel; ?>"
			}
		);

	// Story plugins
	$.module("<?php echo $story->moduleId; ?>")
		.done(function(story) {
			<?php foreach($story->plugins as $plugin) { ?>
				<?php echo $plugin->script; ?>
			<?php } ?>
		});
});
