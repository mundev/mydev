EasySocial.module('fields/user/easyblog_desc/content', function($) {
	var module = this;

	EasySocial.Controller(
		'Field.Easyblog_desc',
		{
			defaultOptions:
			{
				required 		: false,

				"{field}"		: "[data-field-easyblog-desc]",

				"{input}"		: "[data-field-easyblog-desc-input]"
			}
		},
		function( self )
		{
			return {
				init : function()
				{
				},

				validateInput: function()
				{
					if( !self.options.required )
					{
						return true;
					}

					if( $._.isEmpty( self.input().val() ) )
					{
						return false;
					}

					return true;
				},

				"{input} change" : function( el , event )
				{
					if( !self.validateInput() )
					{
						self.element.addClass( 'error' );
					}
					else
					{
						self.element.removeClass( 'error' );
					}
				},

				"{self} onSubmit" : function( el , event , register )
				{
					if( !self.options.required )
					{
						register.push( true );
						return;
					}

					register.push( self.validateInput() );
					return;
				}
			}
		});

	module.resolve();
});
