EasySocial.module('site/apps/reviews/reviews', function($) {

var module = this;


EasySocial.Controller('Apps.Review', {
	defaultOptions: {
		"{wrapper}": "[data-reviews-wrapper]",
		"{contents}": "[data-reviews-contents]",
		"{delete}": "[data-delete]",
		"{approve}": "[data-approve]",
		"{reject}": "[data-reject]",
		"{filter}": "[data-review-filter]",
		"{item}": "[data-review-item]"
	}
}, function(self, opts) { return {
	
	init: function() {
		opts.id = self.element.data('id');
		opts.uid = self.element.data('uid');
		opts.type = self.element.data('type');
	},

	"{delete} click" : function(el, event) {

		var item = el.parents(self.item().selector);
		var id = item.data('id');
		
		EasySocial.dialog({
			"content": EasySocial.ajax('site/views/reviews/confirmDelete', { "id" : id})
		});
	},

	"{approve} click" : function(el, ev) {

		var item = el.parents(self.item().selector);
		var id = item.data('id');

		EasySocial.dialog({
			"content": EasySocial.ajax('site/views/reviews/confirmApprove', { "id" : id})
		});
	},

	"{reject} click" : function(el, ev) {

		var item = el.parents(self.item().selector);
		var id = item.data('id');

		EasySocial.dialog({
			"content": EasySocial.ajax('site/views/reviews/confirmReject', { "id" : id})
		});
	},

	setActiveFilter: function(filter) {
		self.filter().removeClass('active');
		filter.addClass('active');
	},

	updatingContents: function() {
		self.contents().html('&nbsp;');
		self.wrapper().removeClass('is-empty').addClass('is-loading');
	},

	updateContents: function(html, empty) {
		self.wrapper().removeClass('is-loading');
		self.contents().html(html);

		if (empty) {
			self.wrapper().addClass('is-empty');
		} else {
			self.wrapper().removeClass('is-empty');
		}
	},

	"{filter} click" : function(el, ev) {
		var type = el.data('review-filter');

		self.setActiveFilter(el);
		self.updatingContents();

		EasySocial.ajax('site/controllers/reviews/getReviews', {
			"id": opts.id,
			"filter": type
		}).done(function(contents, empty){
			self.updateContents(contents, empty);
		});
	}


}});

module.resolve();
});

