EasySocial.module('site/events/browser', function($) {

var module = this;

EasySocial.require()
.script('site/events/calendar')
.done(function($) {


EasySocial.Controller('Events.Browser', {
	defaultOptions: {

		// Filters
		"{filterItem}": "[data-filter-item]",
		"{ordering}": "[data-ordering]",

		// Contents
		'{contents}': '[data-contents]',
		'{list}': '[data-events-list]',
		'{wrapper}': '[data-wrapper]',
		'{subWrapper}': '[data-sub-wrapper]',

		// Fetching location wrapper
		"{fetchingLocation}": "[data-fetching-location]",
		"{detectLocationMessage}": "[data-detecting-location-message]",

		// Date navigation
		"{navigate}": "[data-navigation-date]",

		// Calendar
		'{calendar}': '[data-events-calendar]',
		"{calendarWrapper}": "[data-events-calendar-wrapper]",

		// Include past events options
		'{includePastCheckbox}': '[data-events-past]',
		'{includePastLink}': '[data-include-past-link]',

		// Distance searches
		'{radius}': '[data-radius]',

		filter: null,
		categoryId: 0,
		delayed: false,
		includePast: false,
		ordering: 'start',
		hasLocation: false,
		userLatitude: '',
		userLongitude: '',
		distance: 10,
		group: null,
		page: null,
		isModule: false,
		clusterId: null
	}
}, function(self, opts) { return {

	init: function() {
		opts.filter = self.element.data('filter');
		opts.categoryId = self.element.data('categoryid');
		opts.clusterId = self.element.data('clusterid');

		// Render the calendar
		self.renderCalendar();

		if (self.options.delayed) {
			self.delayedInit();
		}
	},

	delayedInit: function() {
		// It is possible that view is flagging it as "delayed" in order for javascript to make an ajax call to retrieve the data instead

		// delayed init will have some preset parameter coming from url, hence we don't use the filterbynearby method

		if (opts.filter === 'nearby') {
			self.filterEventsNearby();
		}
	},

	renderCalendar: function() {
		self.calendarWrapper().addClass('is-loading');

		EasySocial.ajax('site/views/events/renderCalendar', {
			"filter": opts.filter,
			"categoryId": opts.categoryId,
			"clusterId": opts.clusterId
		}).done(function(html) {
			self.calendar()
				.html(html)
				.addController('EasySocial.Controller.Events.Browser.Calendar', {
					'{parent}': self,
					isModule: opts.isModule
				});

			self.calendar().trigger('calendarLoaded');
		});
	},

	setActiveFilter: function(filterItem) {
		self.filterItem().removeClass('active');
		filterItem.addClass('active is-loading');

		self.activeFilter = filterItem;
	},

	updatingContents: function() {
		self.contents().empty();
		self.wrapper().addClass('is-loading');
		self.element.removeClass('is-detecting-location');
	},

	updateContents: function(html) {
		self.wrapper().removeClass('is-loading');

		if (self.activeFilter) {
			self.activeFilter.removeClass('is-loading');
		}
		
		self.contents().html(html);
	},

	updatingListing: function() {
		self.list().empty();
		self.subWrapper().addClass('is-loading');
	},

	updateListing: function(html) {

		self.subWrapper().removeClass('is-loading');
		self.list().html(html);
	},

	updateIncludePastLink: function() {
		var attr = opts.includePast ? 'nopast' : 'past';
		var link = self.includePastLink().data(opts.ordering + '-' + attr);

		self.includePastLink().attr('href', link);
	},

	setSortLink: function() {
		var includePast = self.includePastCheckbox().is(':checked') ? 1 : 0;

		$.each(self.sort(), function(i, el) {
			var el = $(el);
			el.attr('href', self.pastLink().data(el.data('ordering') + '-' + (includePast ? 'past' : 'nopast')));
		});
	},

	getEvents: function(isSorting, callback) {
		// Include past
		var includePast = opts.includePast ? 1 : 0;

		// When user clicked on My Event filter
		// We will always include past event
		if (opts.filter == 'mine' && isSorting === false) {
			includePast = 1;
		}

		EasySocial.ajax('site/controllers/events/filter', {
			"type": opts.filter,
			"date": opts.date,
			"categoryId": opts.categoryId,
			"sort": isSorting ? 1 : 0,
			"ordering": opts.ordering,
			"includePast": includePast,
			"latitude": opts.userLatitude,
			"longitude": opts.userLongitude,
			"distance": opts.distance,
			"clusterId": opts.clusterId,
			"activeUserId": opts.activeUserId,
			"browseView": opts.browseView
		}).done(function(contents, distanceUrlWithPast, distanceUrlWithoutPast) {

			if ($.isFunction(callback)) {
				callback.call(this, contents, distanceUrlWithPast, distanceUrlWithoutPast);
			}

			if (isSorting) {
				self.updateListing(contents);
				return;
			}

			self.updateContents(contents);

			$('body').trigger('afterUpdatingContents', [contents]);

			if (!isSorting) {
				// trigger sidebar toggle for responsive view.
				self.trigger('onEasySocialFilterClick');
			}
		});
	},

	filterEventsNearby: function() {

		// Set loading indicator
		self.updatingListing();

		// Ensure that we already have the necessary location values
		if (opts.hasLocation && opts.userLatitude && opts.userLongitude) {
			return self.getEvents(false);
		}

		// Try to get the location first
		self.element.addClass('is-detecting-location');
		self.contents().empty();

		EasySocial.require()
		.library('gmaps')
		.done(function() {
			$.GMaps.geolocate({
				success: function(position) {

					self.element.removeClass('is-detecting-location');

					opts.userLatitude = position.coords.latitude;
					opts.userLongitude = position.coords.longitude;

					opts.hasLocation = true;

					return self.getEvents(false);
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

	"{filterItem} click": function(filterItem, event) {
		event.preventDefault();
		event.stopPropagation();

		// Find the anchor for the filter
		var anchor = filterItem.find('> a');
		anchor.route();

		// Set active and loading states
		self.setActiveFilter(filterItem);

		// Reset the options
		opts.date = false;
		opts.filter = filterItem.data('type');
		opts.categoryId = filterItem.data('id');

		// If this is filtering by nearby we need to get the user coordinates
		if (opts.filter == 'nearby') {
			return self.filterEventsNearby();
		}
		
		// If it's not filtering by nearby, we need to update the ordering accordingly.
		if (opts.filter != 'nearby') {
			opts.ordering = 'start';
		}

		// Set loading indicator
		self.updatingContents();

		// Get the events
		self.getEvents(false);
	},

	'{ordering} click': function(button, event) {
		// Remove active classes on the button
		self.ordering().removeClass('active');
		button.addClass('active');

		opts.ordering = button.data('ordering');

		// What??
		self.updateIncludePastLink();
		// self.setSortLink();

		// Route now to set the correct url
		button.route();

		// Set loading indicator
		self.updatingListing();

		// Get the events
		self.getEvents(true);
	},

	'{includePastCheckbox} change': function(checkbox, event) {

		opts.includePast = checkbox.is(':checked');

		// Route the include past link
		self.includePastLink().route();

		// Update the links
		self.updateIncludePastLink();
		// self.setSortLink();

		// Set loading indicator
		self.updatingListing();        

		// Get the events
		self.getEvents(true);
	},

	'{includePastLink} click': function(link, event) {
		event.preventDefault();
		event.stopPropagation();

		self.includePastCheckbox().trigger('click');
	},

	'{navigate} click': function(link, event) {
		event.preventDefault();
		event.stopPropagation();

		// Route the link
		link.route();

		// Set the filter
		opts.filter = 'date';
		opts.date = link.data('navigation-date');

		self.getEvents(false, function() {
			// if (options.isToday) {
			//     self.filters().removeClass('active');

			//     self.filters('[data-events-filters-type="date"]').addClass('active');
			// }

			// if (options.isTomorrow) {
			//     self.filters().removeClass('active');

			//     self.filters('[data-events-filters-type="tomorrow"]').addClass('active');
			// }

			// if (options.isCurrentMonth) {
			//     self.filters().removeClass('active');

			//     self.filters('[data-events-filters-type="month"]').addClass('active');
			// }

			// if (options.isCurrentYear) {
			//     self.filters().removeClass('active');

			//     self.filters('[data-events-filters-type="year"]').addClass('active');
			// }
		});
	},

	'{radius} click': function(dropdown, event) {

		// Get the distance
		opts.distance = dropdown.data('radius');
		opts.ordering = 'distance';

		// Set the loading indication
		// self.list().addClass('is-loading');

		self.updatingListing();

		// Update the listing
		self.getEvents(true, function(contents, distanceUrlWithPast, distanceUrlWithoutPast) {

			// Update the current url
			History.pushState({state:1}, document.title, distanceUrlWithPast);

			// Add new attributes on the include past link
			self.includePastLink().attr('data-distance-past', distanceUrlWithPast);
			self.includePastLink().attr('data-distance-nopast', distanceUrlWithoutPast);

			// Update the include past link
			self.updateIncludePastLink();
		});
	}
}});

module.resolve();
});

});
