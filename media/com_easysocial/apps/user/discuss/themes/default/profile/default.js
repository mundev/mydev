EasySocial.require()
.done(function($) {
	var wrapper = $('[data-discuss-wrapper]');
	var contents = $('[data-discuss-contents]');

	$('[data-discuss-filter]').click(function(){
		var filterItem = $(this);
		var type = filterItem.data('discuss-filter');
		var id = $('[data-ed-discussions]').data('id');

		setActiveFilter(filterItem);

		filterItem.addClass('is-loading');
		wrapper.addClass('is-loading');
		contents.empty();

		EasySocial.ajax('apps/user/discuss/controllers/discuss/getDiscussions', {
			"user_id": id,
			"filter": type
		}).done(function(contents, empty, pagination) {
			filterItem.removeClass('is-loading');
			updateContents(contents, empty, pagination);
		});
	});

	function updateContents(html, empty, pagination) {
		wrapper.removeClass('is-loading');
		contents.html(html);

		// $('[data-pagination]').html(pagination);
		
		if (empty) {
			wrapper.addClass('is-empty');
		} else {
			wrapper.removeClass('is-empty');
		}

		$('body').trigger('afterUpdatingContents');
	};

	function setActiveFilter(filter) {
		$('[data-discuss-filter]').removeClass('active');
		filter.addClass('active');
	}
});