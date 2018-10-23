EasySocial.module('site/groups/browser', function($) {

var module = this;

EasySocial.require()
.done(function($) {

EasySocial.Controller('Groups.Browser', {
	defaultOptions: {
		delayed: false,

		"{filterItem}": "[data-filter-item]",

		"{wrapper}": "[data-wrapper]",
		"{subWrapper}": "[data-sub-wrapper]",
		'{contents}': '[data-contents]',
		"{result}": "[data-result]",
		"{list}": "[data-list]",

		// Fetching location wrapper
		"{fetchingLocation}": "[data-fetching-location]",
		"{detectLocationMessage}": "[data-detecting-location-message]",

		"{header}": "[data-header]",
		"{ordering}": "[data-sorting]",
		"{items}": "[data-groups-item]",
		"{featured}": "[data-groups-featured-item]",
		"{listContents}": "[data-es-groups-list]",
		'{radius}': '[data-radius]',
	}
}, function(self, opts) { return {

	init: function() {
		if (opts.delayed) {
			self.delayedInit();
		}
	},

	clearContents: function(showLoading) {
		var showLoading = showLoading === undefined ? true : showLoading;

		self.element.removeClass('is-detecting-location');
		self.contents().empty();

		if (showLoading) {
			self.wrapper().addClass('is-loading');
		}
	},

	setContents: function(contents) {

		// Remove loading indicators
		self.wrapper().removeClass('is-loading');
		self.filterItem().removeClass('is-loading');

		self.contents().html(contents);
	},

	updatingListing: function() {
		self.list().empty();
		self.subWrapper().addClass('is-loading');
	},

	updateListing: function(html) {
		self.subWrapper().removeClass('is-loading');
		self.list().html(html);
	},

	// Set active filter
	setActiveFilter: function(filter) {

		// Set correct active state
		self.filterItem().removeClass('active');
		filter.addClass('active');

		// Update the URL on the browser
		filter.find('a').route();

		// Set loading on the correct filter
		filter.addClass('is-loading');
	},

	filterNearby: function() {

		// Set loading indicator
		self.clearContents(false);

		// Try to get the location first
		self.element.addClass('is-detecting-location');
		
		EasySocial.require()
		.library('gmaps')
		.done(function() {
			$.GMaps.geolocate({
				success: function(position) {

					self.element.removeClass('is-detecting-location');

					opts.latitude = position.coords.latitude;
					opts.longitude = position.coords.longitude;

					self.getItems();
				},
				error: function(error) {
					self.filterItem().removeClass('is-loading');
					self.fetchingLocation().addClass('t-text--danger');
					self.fetchingLocation().find('>i').addClass('t-text--danger');
					self.detectLocationMessage().html(error.message);
				}
			});
		});
	},

	getItems: function(isOrdering, callback) {

		var options = {
			"userId": opts.userId,
			"filter": opts.filter,
			"categoryId": opts.categoryId,
			"ordering": opts.ordering,
			"sort": isOrdering ? 1 : 0,
		};

		if (options.filter == 'nearby') {
			options["latitude"] = opts.latitude;
			options["longitude"] = opts.longitude;
			options["distance"] = opts.distance;
		}

		EasySocial.ajax('site/controllers/groups/filter', options)
		.done(function(contents, distanceUrl) {

			if ($.isFunction(callback)) {
				callback.call(this, contents, distanceUrl);
			}

			if (isOrdering) {
				self.updateListing(contents);
				return;
			}

			self.setContents(contents);

			$('body').trigger('afterUpdatingContents', [contents]);

			// Trigger sidebar toggle for responsive view.
			self.trigger('onEasySocialFilterClick');
		});
	},

	"{filterItem} click": function(filterItem, event) {
		// Prevent default.
		event.preventDefault();
		event.stopPropagation();

		// Set active filter state
		self.setActiveFilter(filterItem);

		opts.filter = filterItem.data('type');

		if (opts.filter == 'category') {
			opts.categoryId = filterItem.data('id');
		} else {
			opts.categoryId = null;
		}

		// If this is filtering by nearby we need to get the user coordinates
		if (opts.filter == 'nearby' && !opts.latitude && !opts.longitude) {
			return self.filterNearby();
		}

		self.clearContents();
		self.wrapper().addClass('is-loading');

		self.getItems(false);
	},

	"{ordering} click" : function(ordering, event) {

		// Get the sort type
		var type = ordering.data('type');
		var categoryId = ordering.data('id');

		// Route the item so that we can update the url
		ordering.route();

		// Add the active state on the current element.
		opts.ordering = type;

		self.ordering().removeClass('active');
		ordering.addClass('active');

		self.updatingListing();
		self.getItems(true);
	},

	'{radius} click': function(dropdown, event) {

		// Get the distance
		opts.distance = dropdown.data('radius');

		self.updatingListing();

		self.getItems(true, function(contents, distanceUrl) {
			// Update the current URL now since the distance has changed
			History.pushState({state:1}, document.title, distanceUrl);
		});
	}
}});

module.resolve();

});
});