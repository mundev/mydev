
EasyBlog.ready(function($){

	// Prevent closing
	$(document).on('click.toolbar', '[data-eb-toolbar-dropdown]', function(event) {
		event.stopPropagation();
	});

	// Logout
	$(document).on('click', '[data-blog-toolbar-logout]', function(event) {
		$('[data-blog-logout-form]').submit();
	});

	// Search
	$('[data-eb-toolbar-search]').on('click', function() {
		$('[data-eb-toolbar-search-wrapper]').toggleClass('hide');
	});

	<?php if ($this->isMobile()) { ?>

	$('[data-eb-mobile-menu]').on('click', function() {
		$('[data-eb-container]').toggleClass('eb-sidemenu-open');
	});

	$('.btn-eb-navbar').click(function() {
		$('.eb-nav-collapse').toggleClass("nav-show");
		return false;
	});

	$('[data-eb-toolbar-toggle]').on('click', function() {
		var contents = $('[data-eb-mobile-toolbar]').html();

		EasyBlog.dialog({
			"title": "<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MENU_TITLE', true);?>",
			"content": contents
		});
	});

	$('[data-eb-toolbar-dashboard-toggle]').on('click', function() {
		var contents = $('[data-eb-mobile-dashboard-toolbar]').html();

		EasyBlog.dialog({
			"title": "<?php echo JText::_('COM_EASYBLOG_TOOLBAR_DASHBOARD_TITLE', true);?>",
			"content": contents
		});
	});
	<?php } ?>
});
