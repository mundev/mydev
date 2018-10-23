EasySocial.module('fields/user/discuss_signature/content', function($) {
	var module = this;

	EasySocial
		.require()
		.library()
		.language('PLG_FIELDS_TEXTAREA_VALIDATION_PLEASE_ENTER_SOME_VALUES')
		.done(function($) {
			EasySocial.Controller(
				'Field.Discuss_signature',
				{
					defaultOptions:
					{
						required 		: false,

						'{item}'		: '[data-field-discussSignature-item]',
						'{field}'		: '[data-inputField]',
						'{notice}'		: '[data-check-notice]'
					}
				},
				function( self )
				{
					return {
						init : function()
						{
							var item 	= self.item();

							// EasyDiscuss.require()
							// .script('bbcode')
							// .library('markitup')
							// .done(function($)
							// {
							// 	$(item).markItUp($.getEasyDiscussBBCodeSettings);
							// });
						},

						validateInput : function()
						{
							var val 	= self.item().val();

							if( $._.isEmpty( val ) )
							{
								self.element.addClass('error');

								self.notice().html($.language('PLG_FIELDS_TEXTAREA_VALIDATION_PLEASE_ENTER_SOME_VALUES'));

								return false;
							}

							self.element.removeClass('error');

							return true;
						},

						'{self} onSubmit' : function(el, event, register)
						{
							// If field is not required, skip the checks.

							if(!self.options.required)
							{
								register.push(true);
								return;
							}

							register.push(self.validateInput());

							return;
						}
					}
				});

			module.resolve();
		});
});
