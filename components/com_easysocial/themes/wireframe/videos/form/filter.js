EasySocial.ready(function($) {

	$('[data-es-filter-form] [data-delete]').on('click', function() {

		var id = $(this).data('id');
		var type = $(this).data('type');
		var cid = $(this).data('cid');
		var clusterType = $(this).data('clusterType');

		EasySocial.dialog({
			content : EasySocial.ajax( 'site/views/videos/confirmDeleteFilter', {
				"id" : id,
				"type" : type,
				"cid" : cid,
				"clusterType" : clusterType
			})
		});
	});

	$('[data-es-filter-form] [data-save]').on('click', function() {

		var wrapper = $(this).parents('[data-es-filter-form]');
		var notice = wrapper.find('[data-notice]');

		notice.html('');
		notice.addClass('t-hidden');

		// Check the form
		var title = wrapper.find('[data-title] > input[name="title"]');

		if (title.val() == '') {
			title.parents('[data-title]').addClass('t-error');

			notice.html('<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FILTER_WARNING_TITLE_EMPTY', true);?>');
			notice.removeClass('t-hidden');
			
			return false;
		}

		var hashtag = wrapper.find('[data-hashtag] > input[name="hashtag"]');

		if (hashtag.val() == '') {
			hashtag.parents('[data-hashtag]').addClass('t-error');

			notice.html('<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FILTER_WARNING_HASHTAG_EMPTY', true);?>');
			notice.removeClass('t-hidden');
			return false;
		}

		var form = wrapper.find('form');
		form.submit();
	});
});