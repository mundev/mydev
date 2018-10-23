EasyBlog.require()
.script('site/posts/posts')
.done(function($) {
	$('[data-blog-posts]').implement(EasyBlog.Controller.Posts, {
		"ratings": <?php echo $this->config->get('main_ratings') ? 'true' : 'false';?>
	});
});

EasyBlog.ready(function($){

	$('[data-show-all-authors]').on('click', function() {
		
		$('[data-author-item]').each(function() {
			$(this).find('img').attr('src', $(this).data('src'));

			$(this).removeClass('hide');
		});

		// Hide the button block
		$(this).addClass('hide');
	});

	$('[data-more-categories-link]').on('click', function() {
		$(this).hide();
		$('[data-more-categories]').css('display', 'inline-block');
	});
});
