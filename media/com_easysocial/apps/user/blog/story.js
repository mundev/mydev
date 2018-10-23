EasySocial.module("story/blog", function($){

var module = this;

EasySocial.Controller("Story.Blog", {
	defaultOptions: {
		"{title}" : "[data-blog-title]",
		"{content}" : "[data-blog-content]",
		"{category}": "[data-story-blog-category]"
	}
}, function(self, opts) { return {

	resetForm: function() {
		self.title().val('');
		self.content().val('');
	},

	"{story} save": function(element, event, save) {

		if (save.currentPanel != 'blog') {
			return;
		}
		
		self.savePost = save.addTask('savePost');

		self.save(save);		
	},

	save: function(save) {

		var savePost = self.savePost;

		if (!savePost) {
			return;
		}
			
		var title = self.title().val();

		if (!title) {
			self.clearMessage();
			save.reject('<?php echo $errorTitle; ?>');
			return false;
		}

		var content = self.content().val();

		if (!content) {
			self.clearMessage();
			self.reject('<?php echo $errorContent; ?>');
			return false;
		}


		var data = {"categoryId" : self.category().val(), "title" : title, "content" : content};

		self.resetForm();

		save.addData(self, data);

		savePost.resolve();

		delete self.savePost;

	}
}});

// Resolve module
module.resolve();

});


EasySocial.require()
.script("story/blog")
.done(function($) {
	var plugin = story.addPlugin("blog");
});
