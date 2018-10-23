EasySocial.module('site/groups/item', function($) {
	
var module = this;

EasySocial.require()
.library('history')
.done(function($) {

EasySocial.Controller('Groups.Item', {
	defaultOptions: {
		title: null,

		// Content area
		"{contents}": "[data-contents]",
		"{wrapper}": "[data-wrapper]",

		// Filter item
		"{filterItem}": "[data-filter-item]",
		"{createFilter}": "[data-create-filter]",

		// Edit custom filters
		"{editFilter}": "[data-edit-filter]",        

		// Sections
		"{section}": "[data-section]",
		"{sectionLists}": "[data-section-lists]",
		"{showAllSection}": "[data-section-showall]",

		"{filterBtn}": "[data-stream-filter-button]",
		"{filterEditBtn}": "[data-dashboardFeeds-Filter-edit]",

		// hashtag filter save
		"{saveHashTag}" : "[data-hashtag-filter-save]"
	}
}, function(self, opts, base) { return {
			
	init : function(){
		opts.id = self.element.data('id');

		var activeFilter = self.filterItem('.active');
		opts.filter = activeFilter.data('filter-item');
		opts.filterId = activeFilter.data('id');
	},

	removeActive: function() {
		self.filterItem().removeClass('active');
		self.createFilter().removeClass('active');
	},

	setActiveFilter: function(filterItem) {
		// Toggle active state
		self.createFilter().removeClass('active');
		self.filterItem().removeClass('active');
		filterItem.addClass('active');

		var activeFilter = filterItem;
		opts.filter = filterItem.data('filter-item');
		opts.filterId = filterItem.data('id');

		// Update the url
		var anchor = filterItem.find('a');
		anchor.attr('title', opts.title);
		anchor.route();

		// Set loading class
		filterItem.addClass('is-loading');
		self.contents().empty();
		self.wrapper().addClass('is-loading');
	},

	setContents: function(filterItem, contents) {
		// Remove loading indicators
		filterItem.removeClass('is-loading');
		self.wrapper().removeClass('is-loading');

		// Update the contents
		self.contents().html(contents);
	},
	
	getAppContents: function(filterItem, callback) {
		EasySocial.ajax('site/controllers/groups/getAppContents', {
			"appId": opts.filterId,
			"id": opts.id
		}).done(function(contents) {
			self.setContents(filterItem, contents);

			if ($.isFunction(callback)) {
				callback.call(this, contents);
			}
		}).always(function() {
			filterItem.removeClass('is-loading');
		});
	},

	getStream : function(filterItem, callback) {
		// Perform an ajax to get the group's stream data
		EasySocial.ajax('site/controllers/groups/getStream', {
			"groupId": opts.id,
			"filter": opts.filter,
			"id": opts.filterId
		}).done(function(contents){
			self.setContents(filterItem, contents);
		})
	},

	getInfo: function(filterItem, callback) {
		EasySocial.ajax('site/controllers/groups/getInfo', {
			"step": opts.filterId,
			"id": opts.id
		}).done(function(contents) {
			self.setContents(filterItem, contents);

			if ($.isFunction(callback)) {
				callback.call(this, contents);
			}
		}).always(function() {
			filterItem.removeClass('is-loading');
		});
	},

	addCustomFilter: function(feed) {
		var sectionContent = self.section('[data-type="custom-filters"]').find(self.sectionLists.selector);

		if (!$.trim(sectionContent.html()).length) {
			var emptyList = self.section('[data-type="custom-filters"]').find('[data-filter-empty]');
			emptyList.hide();
		}

		feed.appendTo(sectionContent);        
	},

	"{showAllSection} click": function(link, event) {

		var parent = link.closest(self.section.selector);

		// Display all filters under the respective section
		parent.find(self.sectionLists.selector).removeClass('t-hidden');

		link.remove();
	},

	"{createFilter} click": function(createFilter, event) {
		// Prevent event from bubbbling up
		event.preventDefault();
		event.stopPropagation();

		// Update the browsers address bar
		createFilter.attr('title', opts.title);
		createFilter.route();

		// Remove active classes
		self.removeActive();

		createFilter.addClass('active');

		// Set the loading state
		createFilter.addClass('is-loading');
		self.contents().empty();
		self.wrapper().addClass('is-loading');

		EasySocial.ajax('site/views/stream/getFilterForm', {
			"uid": opts.id,
			"type": "group"
		}).done(function(contents){
			self.setContents(createFilter, contents);
		});
	},

	"{editFilter} click": function(editFilter, event) {
		
		event.preventDefault();
		event.stopPropagation();

		// Updated the browser's url
		editFilter.attr('title', opts.title);
		editFilter.route();

		// Get the filter attributes
		var id = editFilter.data('id');
		var type = editFilter.data('type');

		editFilter.addClass('is-loading');
		self.contents().empty();
		self.wrapper().addClass('is-loading');

		EasySocial.ajax('site/views/stream/getFilterForm', {
			"id": id,
			"uid": opts.id,
			"type": type
		}).done(function(contents) {
			self.setContents(editFilter, contents);
		}).fail(function(messageObj) {
			return messageObj;
		});
	},   

	"{filterItem} click": function(filterItem, event) {
		// Prevent event from bubbling up
		event.preventDefault();
		event.stopPropagation();

		// Update the browsers address bar
		var anchor = filterItem.find('> a');
		anchor.attr('title', opts.title);
		anchor.route();

		// Set the active filter now
		self.setActiveFilter(filterItem);

		var stream = true;

		// Trigger so that other scripts can perform other stuffs if needed
		$('body').trigger('beforeUpdatingContents');

		// Display app contents
		if (opts.filter == 'apps') {
			self.getAppContents(filterItem);
			stream = false;
		}

		// Display stream items
		if (stream) {
			self.getStream(filterItem);
		}

		$('body').trigger('afterUpdatingContents');

		// trigger sidebar toggle for responsive view.
		self.trigger('onEasySocialFilterClick');
	},

	"{saveHashTag} click": function(el) {
		var hashtag = el.data('tag');
		var uid = el.data('uid');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/stream/confirmSaveFilter', {"tag" : hashtag}),
			bindings: {
				"{saveButton} click": function() {
					this.inputWarning().hide();

					filterName = this.inputTitle().val();

					if (filterName == '') {
						this.inputWarning().show();
						return;
					}

					EasySocial.ajax('site/controllers/stream/addFilter', {
						"title": filterName,
						"tag": hashtag,
						"uid": uid,
						"type": 'group'
					}).done(function(html, msg) {
						var item = $.buildHTML(html);

						// Add a new custom filter to the sidebar
						self.addCustomFilter(item);

						// Show message
						EasySocial.dialog(msg);

						// Close the dialog automatically
						setTimeout(function() {
							EasySocial.dialog().close();
						}, 2000);
					});
				}
			}
		});
	}
}});

	module.resolve();
});

});

