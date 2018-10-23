EasyBlog.require()
.script('site/reactions')
.done(function($) {
	// Implement reactions
	$('[data-reactions]').implement(EasyBlog.Controller.Reactions, {
		"allowed": <?php echo $canReact ? 'true' : 'false'; ?>
	});
});