EasySocial.module('site/locations/popbox', function($){

	EasySocial.module("locations/popbox", function($){

		this.resolve(function(popbox){

			var apiKey = window.es.gmapsApiKey;

			var button = popbox.button,
				lat = button.data("lat"),
				lng = button.data("lng"),
				link = "//maps.google.com/?q=" + lat + "," + lng,
				language = window.es.locationLanguage || 'en',
				url = "//maps.googleapis.com/maps/api/staticmap?key=" + apiKey + "&size=400x200&sensor=true&zoom=15&center=" + lat + "," + lng + "&markers=" + lat + "," + lng + "&language=" + language;

			return {
				id: "es",
				component: "",
				type: "location",
				content: '<a href="' + link + '" target="_blank"><img src="' + url + '" width="400" height="200" /></a>'
			}
		});

	});

	this.resolve();
});
