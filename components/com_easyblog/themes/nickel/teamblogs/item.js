<?php echo $this->output('site/blogs/nickel'); ?>

EasyBlog.require()
.library('masonry', 'imagesloaded')
.script('site/teamblogs','site/posts/posts')
.done(function($) {

	$('[data-team-item]').implement(EasyBlog.Controller.TeamBlogs.Item);

	$('[data-blog-posts]').implement(EasyBlog.Controller.Posts, {
		"ratings": <?php echo $this->config->get('main_ratings') ? 'true' : 'false';?>
	});

	// MASONRY
	var container = $('.eb-posts-masonry');

	$('img').load(function(){
		container.imagesLoaded(function(){
			container.masonry({
				itemSelector : '.eb-post',
				isRTL: false
			});
		});
	});


	$('.eb-masonry').imagesLoaded( function(){
		$('.eb-masonry').masonry({
			itemSelector: '.eb-masonry-post'
		});
	});

	$('.eb-masonry').masonry({
		itemSelector: '.eb-masonry-post'
	});

	$(document).on('click.teamblog.join', '[data-team-join]', function() {

		var id = $(this).data('id');

		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/teamblog/join', {
				"id": id,
				"return": "<?php echo base64_encode(EBFactory::getURI(true));?>"
			})
		});

	});

	$(document).on('click.teamblog.join', '[data-team-leave]', function() {

		var id = $(this).data('id');

		EasyBlog.dialog({
			content: EasyBlog.ajax('site/views/teamblog/leave', {
				"ids": id,
				"return": "<?php echo base64_encode(EBFactory::getURI(true));?>"
			})
		});

	});

});
