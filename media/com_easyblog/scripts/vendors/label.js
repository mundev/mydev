(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 


//
// Label data API to support floating labels
//
var selectors = '[data-eb-label] > input[type=text], [data-eb-label] > input[type=password]';

$(document).on('focus.eb.label change.eb.label', selectors, function() {
	var self = $(this);
	var label = self.parent();

	label.addClass('is-focused');
});


$(document).on('blur.eb.label', selectors, function() {
	var self = $(this);
	var label = self.parent();
	var value = self.val();

	// When there is a value, we shouldn't remove is-focused
	if ($.trim(value) !== '') {
		return;
	}

	label.removeClass('is-focused');
});

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD50.module('label', moduleFactory);

}());