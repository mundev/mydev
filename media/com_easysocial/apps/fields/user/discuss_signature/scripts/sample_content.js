EasySocial.module('fields/user/discuss_signature/sample_content', function($) {
	var module = this;

	EasySocial.Controller('Field.Discuss_signature.Sample', {
		defaultOptions: {
			'{textarea}'	: '[data-textarea]'
		}
	}, function(self) {
		return {
			init: function() {
				if(EasyDiscuss) {
					EasyDiscuss.require().library('markitup').done(function(ED) {
						ED(self.textarea()).markItUp({set: 'bbcode_easydiscuss'});
					});
				}
			}
		}
	});

	module.resolve();
});
