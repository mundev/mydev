EasyBlog.module('admin/tag/suggest', function($) {

var module = this;

EasyBlog.require()
.library('textboxlist')
.done(function($) {

EasyBlog.Controller('Tag.Suggest', {
	defaultOptions: {
		max: null,

		// this otion determine for whether you allow highlighted those unrelated string
		exclusive: false,
		exclusion: [],
		minLength: 1,
		highlight: true,
		name: "uid[]",
		type: "",

		"query": {
			"suggest": "admin/controllers/blogs/suggest"
		},

		includeSelf: false
	}
}, function(self, opts, base) { return {

	init: function() {

		// Implement the textbox list on the implemented element.
		self.element
			.textboxlist({
				"component": 'eb',
				"name": opts.name,
				"max": opts.max,
				"plugin": {
					"autocomplete": {
						"exclusive": opts.exclusive,
						"minLength": opts.minLength,
						"highlight": opts.highlight,
						"showLoadingHint": true,
						"showEmptyHint": true,

						query: function(keyword) {

							// Suggest tags list
							return EasyBlog.ajax(opts.query.suggest, {
								"search": keyword,
								"inputName": opts.name,
								"exclusion": opts.exclusion
							});
						}
					}
				}
			})
			.textboxlist("enable");
	},

	"{self} filterItem": function(el, event, item) {

		var html = $('<div/>').html(item.html);
		var title = html.find('[data-tag-title]').text();
		var id = html.find('[data-tag-id]').val();

		item.id = id;
		item.title = title;
		item.menuHtml = item.html;
	},

	"{self} filterMenu": function(el, event, menu, menuItems, autocomplete, textboxlist) {

		// Get list of excluded users
		var items = textboxlist.getAddedItems();
		var users = $.pluck(items, "id");
		var users = users.concat(self.options.exclusion);

		menuItems.each(function(){

			var menuItem = $(this);
			var item = menuItem.data("item");

			// If this user is excluded, hide the menu item
			menuItem.toggleClass("hidden", $.inArray(item.id.toString(), users) > -1);
		});
	}

}});

module.resolve();
});

});