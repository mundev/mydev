EasySocial.module('fields/user/easyblog_permalink/content', function($) {
	var module = this;

	EasySocial.Controller(
		'Field.Easyblog_permalink',
		{
			defaultOptions:
			{
				required : false,
				id : null,
				userid : null,

				"{field}" : "[data-field-easyblog-permalink]",
				"{input}" : "[data-field-easyblog-permalink-input]",
				'{available}': '[data-ebpermalink-available]'

			}
		},
		function(self, opts, base) {

			return {
				init : function() {

					opts.message = {
						length: base.find('[data-error-length]').data('error-length'),
						required: base.find('[data-error-required]').data('error-required')
					};

					console.log(self.options.required);

				},

				validateInput: function() {

					if (self.options.required && $._.isEmpty(self.input().val())) {
						return false;
					}

					if (! self.checkPermalink()) {
						return false;
					}

					return true;
				},

				"{input} keyup" : function(el , event) {
					// if (!self.validateInput()) {
					// 	self.element.addClass('error');
					// } else {
					// 	self.element.removeClass('error');
					// }

					self.delayedCheck();
				},

				"{self} onSubmit" : function(el, event, register) {

					if (self.options.required && $._.isEmpty(self.input().val())) {
						register.push(false);
					}

					register.push(self.checkPermalink());
					return;
				},

				delayedCheck: $.debounce(function() {
					self.checkPermalink();
				}, 350),

				checkPermalink: function() {
					self.clearError();

					var permalink   = self.input().val();

					self.available().hide();

					if(self.options.max > 0 && permalink.length > self.options.max) {
						self.raiseError(opts.message.length);
						return false;
					}

					if(!$.isEmpty(permalink))
					{
						// self.checkButton().addClass('is-loading');

						var state = $.Deferred();

						EasySocial.ajax('fields/user/easyblog_permalink/isValid',
						{
							"id" : self.options.id,
							"userid" : self.options.userid,
							"permalink" : permalink
						})
						.done(function(msg)
						{
							self.available().show();
							state.resolve();

						})
						.fail(function(msg)
						{
							self.raiseError(msg);
							self.available().hide();

							state.reject();
						});

						return state;
					}

					if (self.options.required && $.isEmpty(permalink)) {
						self.available().hide();

						self.raiseError(opts.message.required);
						return false;
					}

					return true;
				},

				raiseError: function(msg) {
					self.trigger('error', [msg]);
				},

				clearError: function() {
					self.trigger('clear');
				},
			}
		});

	module.resolve();
});
