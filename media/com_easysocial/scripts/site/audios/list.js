EasySocial.module('site/audios/list', function($) {

var module = this;

EasySocial.Controller('Audios.List', {
	defaultOptions: {

		// Audio filters
		"{filter}": "[data-audios-filter]",
		"{sorting}": "input[name='sorting']",
		"{sortItem}": "[data-sorting]",
		"{counters}": "[data-counter]",

		// content wrapper
		"{wrapper}": "[data-wrapper]",

		// Audios result
		"{result}": "[data-audios-result]",
		"{list}": "[data-result-list]",

		// Audio actions
		"{item}": "[data-audio-item]",
		"{deleteButton}": "[data-audio-delete]",
		"{featureButton}": "[data-audio-feature]",
		"{unfeatureButton}": "[data-audio-unfeature]",
		"{createFilter}": "[data-audio-create-filter]",
		"{playlistItem}": "[data-playlist-item]"
	}
}, function(self, opts, base) { return {

	clicked: false,

	init: function() {
		self.activeFilter = self.filter('[data-type=' + opts.active + ']');
	},

	// Default filter
	currentFilter: "",
	currentSorting: "",
	genreId: null,
	isSort: false,

	setActiveFilter: function(filter) {

		self.activeFilter = filter;

		// Remove all active classes.
		self.filter().parent().removeClass('active');

		// Set the active class to the filter's parent.
		filter.parent().addClass('active is-loading');
	},

	getAudios: function() {

		if (!self.currentSorting) {
			// Set the current sorting
			self.currentSorting = self.sorting().val();
		}

		if (!self.currentFilter) {
			// Set the current sorting
			self.currentFilter = self.activeFilter.data('type');
		}

		// if still empty the filter, just set to all.
		if (!self.currentFilter) {
			self.currentFilter = "all";
		}

		var isSortReq = self.isSort ? "1" : "0";

		EasySocial.ajax('site/controllers/audios/getAudios',{
			"filter": self.currentFilter,
			"genreId": self.genreId,
			"sort": self.currentSorting,
			"uid": opts.uid,
			"type": opts.type,
			"hashtags": opts.hashtag,
			"hashtagFilterId": self.hashtagId,
			"isSort": isSortReq
		}).done(function(output) {

			self.filter().parent().removeClass('is-loading');

			if (self.isSort) {
				self.result().removeClass('is-loading');
				self.list().html(output);
			} else {
				self.wrapper().removeClass('is-loading');
				self.result().html(output);
			}

			$('body').trigger('afterUpdatingContents', [output]);
		});
	},

	"{sortItem} click" : function(sortItem, event) {
		
		// Get the sort type
		var type = sortItem.data('type');
		self.currentSorting = type;

		self.isSort = true;

		// Route the item so that we can update the url
		sortItem.route();

		self.result().addClass('is-loading');
		self.list().empty();

		self.getAudios();
	},

	loadPlaylist: function(playlistId) {
		
		self.wrapper().addClass('is-loading');
		self.result().empty();

		EasySocial.ajax('site/views/audios/loadPlaylist', {
			id: playlistId
		}).done(function(output) {
			self.activeFilter.parent().removeClass('is-loading');

			self.result().html(output);
			self.wrapper().removeClass('is-loading');

			$('body').trigger('afterUpdatingContents', [output]);
		});
	},

	"{filter} click": function(filter, event) {
		// Prevent bubbling up
		event.preventDefault();
		event.stopPropagation();

		var type = filter.data('type');

		// Route the inner filter links
		filter.route();

		// Add an active state to the parent
		self.setActiveFilter(filter);

		// If this is list filter, we generate the playlist player
		if (type == 'list') {
			playlistId = filter.data('id');

			this.loadPlaylist(playlistId);
			return;
		}

		// Filter by genre
		var genreId = null;

		if (type == 'genre') {
			type = 'all';
			genreId = filter.data('id');
		}

		var hashtagId = null;

		if (type == 'hashtag') {
			hashtagId = filter.data('tagId');
		}

		// Set the current filter
		self.currentFilter = type;
		self.genreId = genreId;
		self.hashtagId = hashtagId;
		self.isSort = false;

		self.result().empty();
		self.wrapper().addClass('is-loading');

		self.getAudios();

		// trigger sidebar toggle for responsive view.
		self.trigger('onEasySocialFilterClick');

	},

	"{createFilter} click": function(filter, event) {

		if (self.clicked) {
			return;
		}

		// Prevent default
		event.preventDefault();
		event.stopPropagation();

		self.clicked = true;

		// Update the url
		filter.route();

		// Add an active state to the parent
		self.setActiveFilter(filter);

		EasySocial.ajax('site/views/audios/getFilterForm', {
			"type": filter.data('type'),
			"id": filter.data('id'),
			"cid": filter.data('uid'),
			"clusterType": filter.data('clusterType')
		}).done(function(outputs) {
			// Stop the loading
			self.element.removeClass('is-loading');

			self.result().html(outputs);
		}).fail(function(messageObj) {
			return messageObj;
		}).always(function() {
			self.clicked = false;
		});
	},

	"{deleteButton} click": function(deleteButton, event) {

		var item = deleteButton.parents(self.item.selector);
		var id = item.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/audios/confirmDelete', {
				"id": id
			})
		});
	},

	"{unfeatureButton} click": function(unfeatureButton, event) {
		var item = unfeatureButton.parents(self.item.selector);
		var id = item.data('id');
		var returnUrl = unfeatureButton.data('return');

		var options = {
			"id": id
		};

		if (returnUrl.length > 0) {
			options["callbackUrl"] = returnUrl;
		}

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/audios/confirmUnfeature', options)
		});
	},

	"{featureButton} click": function(featureButton, event) {
		var item = featureButton.parents(self.item.selector);
		var id = item.data('id');
		var returnUrl = featureButton.data('return');

		var options = {
			"id": id
		};

		if (returnUrl) {
			options["callbackUrl"] = returnUrl;
		}

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/audios/confirmFeature', options)
		});
	},

	"{playlistItem} click": function(playlistItem, event) {
		var item = playlistItem.parents(self.item.selector);
		var audioId = item.data('id');
		var playlistId = playlistItem.data('id');
		var previouslyAdded = playlistItem.find('i').length > 0;
		var overlayNotice = item.find('[data-overlay-notice]');

		EasySocial.ajax('site/controllers/audios/addToPlaylist',{
			"playlistId": playlistId,
			"audioId": audioId
		}).done(function(message) {
			self.trigger('updateListCounters');

			if (previouslyAdded === false) {
				playlistItem.append('<i class="fa fa-check pull-right"></i>');
			}

			overlayNotice.fadeIn('fast');
			
			setTimeout(function(){ 
				overlayNotice.fadeOut('slow');
			}, 2000); 
		});
	},

	"{self} updateListCounters": function() {

		EasySocial.ajax('site/controllers/audios/getListCounts')
		.done(function(lists) {

			$(lists).each(function(i, list){
				self.filter('[data-type="list"][data-id="' + list.id + '"]')
					.siblings(self.counters.selector)
					.html(list.count);
			});
		});
	}
}});

module.resolve();


});
