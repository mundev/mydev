EasySocial.module('site/profile/edit', function($){

var module = this;

EasySocial.require()
.script('shared/fields/validate', 'shared/fields/base', 'shared/fields/conditional')
.done(function($){

EasySocial.Controller('Profile.Edit', {
	defaultOptions: {
		userid: null,

		"{stepContent}": "[data-profile-edit-fields-content]",
		"{stepItem}": "[data-profile-edit-fields-step]",

		// Forms.
		"{profileForm}": "[data-profile-fields-form]",
		"{actions}": "[data-profile-actions]",
		"{fieldItem}": "[data-field-item]",

		// Submit buttons
		"{save}": "[data-profile-fields-save]",
		"{saveClose}": "[data-profile-fields-save-close]",
		"{switchSave}": "[data-profile-switch-save]",

		// Delete Profile
		"{deleteProfile}": "[data-profile-edit-delete]",

		'{taskInput}': 'input[name="task"]'
	}
}, function(self) { return {

	init: function() {
		self.fieldItem().addController('EasySocial.Controller.Field.Base', {
			userid: self.options.userid,
			mode: 'edit'
		});

		self.fieldItem().addController('EasySocial.Controller.Field.Conditional');
	},

	errorFields: [],

	// Support field throwing error internally
	'{fieldItem} error': function(el, ev) {
		self.triggerStepError(el);
	},

	// Support for field resolving error internally
	'{fieldItem} clear': function(el, ev) {
		self.clearStepError(el);
	},

	// Support validate.js throwing error externally
	'{fieldItem} onError': function(el, ev) {
		self.triggerStepError(el);
	},

	triggerStepError: function(el) {
		var fieldid = el.data('id'),
			stepid = el.parents(self.stepContent.selector).data('id');

		if($.inArray(fieldid, self.errorFields) < 0) {
			self.errorFields.push(fieldid);
		}

		self.stepItem().filterBy('for', stepid).trigger('error');
	},

	clearStepError: function(el) {
		var fieldid = el.data('id'),
			stepid = el.parents(self.stepContent.selector).data('id');

		self.errorFields = $.without(self.errorFields, fieldid);

		self.stepItem().filterBy('for', stepid).trigger('clear');
	},

	"{stepItem} click" : function(stepItem, event) {
		var id = stepItem.data('for');
		var actions = stepItem.data('actions') == 1 ? true : false;

		self.stepItem().removeClass('active');
		stepItem.addClass('active');

		// Remove active class on step content
		self.stepContent().removeClass('active');

		// Get the step content element
		var stepContent = self.stepContent().filterBy('id', id);

		// Add active class on the selected content
		stepContent.addClass('active');

		// Trigger onShow on the field item in the content
		stepContent.find(self.fieldItem.selector).trigger('show');

		// Determines if we should show or hide the actions
		self.actions().toggleClass('t-hidden', !actions);
	},

	"{stepItem} error": function(el) {
		el.addClass('error');
	},

	"{stepItem} clear": function(el) {
		if(self.errorFields.length < 1) {
			el.removeClass('error');
		}
	},

	"{save} click": function(el, event) {
		// Run some error checks here.
		event.preventDefault();

		$(el).addClass('btn-loading');

		self.profileForm()
			.validate({fieldSelector : self.fieldItem.selector})
			.fail(function()
			{
				$(el).removeClass('btn-loading');
				EasySocial.dialog(
				{
					content 	: EasySocial.ajax('site/views/profile/showFormError')
				});
			})
			.done(function()
			{
				self.taskInput().val('save');
				self.profileForm().submit();
			});

		return false;
	},


	"{switchSave} click": function(el, event) {
		// Run some error checks here.
		event.preventDefault();

		$(el).addClass('btn-loading');

		self.profileForm()
			.validate({fieldSelector : self.fieldItem.selector})
			.fail(function() {
				$(el).removeClass('btn-loading');
				EasySocial.dialog({
					content : EasySocial.ajax('site/views/profile/showFormError')
				});
			})
			.done(function() {
				self.taskInput().val('completeSwitch');
				self.profileForm().submit();
			});

		return false;
	},

	"{saveClose} click": function(el, event) {
		event.preventDefault();

		$(el).addClass('btn-loading');

		// Run some error checks here.
		self.profileForm()
			.validate({fieldSelector : self.fieldItem.selector})
			.fail(function() {
				$(el).removeClass('btn-loading');
				EasySocial.dialog({
					content: EasySocial.ajax('site/views/profile/showFormError')
				});
			}).done(function() {
				self.taskInput().val('saveclose');
				self.profileForm().submit();
			});

		return false;
	},

	"{deleteProfile} click" : function() {
		EasySocial.dialog({
			content: EasySocial.ajax('site/views/profile/confirmDelete')
		});
	}
}});

module.resolve();
});

});
