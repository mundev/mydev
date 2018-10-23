EasySocial.module("site/story/audios", function($){

var module = this;

EasySocial.require()
.library('image', 'plupload')
.done(function($){

EasySocial.Controller("Story.Audios", {
	defaultOptions: {
		// This is the main wrapper for the form
		"{form}": "[data-audio-form]",

		// This is audio panel button
		"{panelButton}": '[data-story-plugin-name="audios"]',

		// Audio links
		"{insertAudio}": "[data-insert-audio]",
		"{audioLink}": "[data-audio-link]",
		"{audioGenre}": "[data-audio-genre]",

		// Audio uploads
		"{uploaderForm}": "[data-audio-uploader]",
		"{uploaderButton}": "[data-audio-uploader-button]",
		"{uploaderDropsite}": "[data-audio-uploader-dropsite]",
		"{uploaderProgressBar}": "[data-audio-uploader-progress-bar]",
		"{uploaderProgressText}": "[data-audio-uploader-progress-text]",

		"{uploaderUploadBar}": "[data-audio-uploader-upload-bar]",
		"{uploaderUploadText}": "[data-audio-uploader-upload-text]",

		// Audio preview
		"{removeButton}": "[data-remove-audio]",
		"{previewImageWrapper}": "[data-audio-preview-image]",
		"{previewTitle}": "[data-audio-preview-title]",
		"{title}": "[data-audio-title]",
		"{previewDescription}": "[data-audio-preview-description]",
		"{description}": "[data-audio-description]",
		"{previewArtist}": "[data-audio-preview-artist]",
		"{artist}": "[data-audio-artist]",
		"{previewAlbum}": "[data-audio-preview-album]",
		"{album}": "[data-audio-album]"
	}
}, function(self, opts, base) { return {

	init: function() {
		// If audio uploader form doesn't exist, perhaps the admin already disabled this
		if (self.uploaderForm().length == 0 && self.audioLink().length == 0) {
			return;
		}

		// Only implement uploader if the upload form exists
		if (self.uploaderForm().length > 0) {
			self.uploader = self.uploaderForm().addController("plupload", $.extend({
					"{uploadButton}": self.uploaderButton.selector,
					"{uploadDropsite}": self.uploaderDropsite.selector
				}, opts.uploader)
			);

			self.plupload = self.uploader.plupload;
		}
		

		if (opts.isEdit) {
			self.audio = {
				"type": opts.audio.source,
				"title": opts.audio.title,
				"artist": opts.audio.artist,
				"album": opts.audio.album,
				"description": opts.audio.description,
				"link": opts.audio.link,
				"id": opts.audio.id,
				"isEncoding": opts.audio.isEncoding
			};
		}
	},

	isProcessed: function() {
		self.form().switchClass('is-processed');

		self.processing = false;
	},

	isUploading: function() {
		self.form().switchClass('is-uploading');
	},

	isProcessing: function() {
		self.form().switchClass('is-processing');

		self.processing = true;
	},

	isEncoding: function() {
		self.form().switchClass('is-encoding');
	},

	isInitial: function() {
		self.form().switchClass('is-waiting');
	},

	currentGenre: null,
	processing: false,
	audio: null,
	audioType: null,		

	updatePreview: function(type, data, imageUrl) {

		self.audio = {
			"type": type,
			"title": data.title,
			"artist": data.artist,
			"album": data.album,
			"description": data.description,
			"link": data.link,
			"id": data.id ? data.id : '',
			"isEncoding": false
		};

		// Update the title
		if (data.title) {
			self.previewTitle().html(data.title);
		}

		if (data.artist) {
			self.previewArtist().html(data.artist);
		}

		if (data.album) {
			self.previewAlbum().html(data.album);
		}

		// Update the description
		if (data.description) {
			self.previewDescription().html(data.description);
		}

		// Load the image
		$.Image.get(imageUrl).done(function(image){
			image.appendTo(self.previewImageWrapper());
		});

	},

	resetProgress: function() {

		// Reset the progress bar
		self.uploaderProgressBar().css('width', '0%');
		self.uploaderProgressText().html('0%');
	},

	clearForm: function(resetAudio) {

		if (resetAudio) {
			self.audio = null;
		}

		// Set to initial position
		self.isInitial();

		// Reset all the form values
		self.audioLink().val('');

		self.previewImageWrapper().empty();

		self.previewTitle().empty();
		self.title().val('');

		self.previewArtist().empty();
		self.artist().val('');

		self.previewAlbum().empty();
		self.album().val('');

		self.previewDescription().empty();
		self.description().val('');
	},

	editArtistEvent: "click.es.story.audio.editLinkArtist",
	editAlbumEvent: "click.es.story.audio.editLinkAlbum",
	editTitleEvent: "click.es.story.audio.editLinkTitle",
	editDescriptionEvent: "click.es.story.audio.editLinkDescription",

	editTitle: function() {

		// Apply the class to the form wrapper
		self.form().addClass('editing-title');

		setTimeout(function(){

			self.title()
				.val(self.previewTitle().text())
				.focus()[0]
				.select();

			$(document).on(self.editTitleEvent, function(event) {
				if (event.target !== self.title()[0]) {
					self.saveTitle("save");
				}
			});

		}, 1);
	},

	saveTitle: function(operation) {

		if (!operation) {
			operation = 'save';
		}

		var value = self.title().val();

		if (operation == 'save') {
			self.previewTitle().html(value);
		}

		// Remove the editing title class
		self.form().removeClass('editing-title');

		self.audio.title = value;
		
		if (self.audio.title == '' && self.previewTitle().html().length > 0) {
			self.audio.title = self.previewTitle().html();
		}
		

		$(document).off(self.editTitleEvent);
	},

	editArtist: function() {

		// Apply the class to the form wrapper
		self.form().addClass('editing-artist');

		setTimeout(function(){

			self.artist()
				.val(self.previewArtist().text())
				.focus()[0]
				.select();

			$(document).on(self.editArtistEvent, function(event) {
				if (event.target !== self.artist()[0]) {
					self.saveArtist("save");
				}
			});

		}, 1);
	},

	saveArtist: function(operation) {

		if (!operation) {
			operation = 'save';
		}

		var value = self.artist().val();

		if (operation == 'save') {
			self.previewArtist().html(value);
		}

		// Remove the editing artist class
		self.form().removeClass('editing-artist');

		self.audio.artist = value;

		$(document).off(self.editArtistEvent);
	},

	editAlbum: function() {

		// Apply the class to the form wrapper
		self.form().addClass('editing-album');

		setTimeout(function(){

			self.album()
				.val(self.previewAlbum().text())
				.focus()[0]
				.select();

			$(document).on(self.editAlbumEvent, function(event) {
				if (event.target !== self.album()[0]) {
					self.saveAlbum("save");
				}
			});

		}, 1);
	},

	saveAlbum: function(operation) {

		if (!operation) {
			operation = 'save';
		}

		var value = self.album().val();

		if (operation == 'save') {
			self.previewAlbum().html(value);
		}

		// Remove the editing album class
		self.form().removeClass('editing-album');

		self.audio.album = value;

		$(document).off(self.editAlbumEvent);
	},

	checkAudioStatus: function(audioId, percentage) {
		EasySocial.ajax('site/controllers/audios/status', {
			"id": audioId,
			"uid": opts.audio.uid,
			"type": opts.audio.type,
			"createStream": 0,
			"percentage": percentage,
			"unpublished": 1
		}).done(function(permalink, percent, data, albumArt) {

			if (percent === 'done') {

				self.processing = false;

				// Set the progress bar to 100%
				self.uploaderProgressBar().css('width', '100%');
				self.uploaderProgressText().html('100%');

				// Update the state
				self.isProcessed();

				// Update the preview
				self.updatePreview('upload', data, albumArt);

				// Reset the progress bar
				self.resetProgress();

				return;
			}

			// There is a possibility that the progress is throwing errors on the line so we should skip this
			if (percent == 'ignore') {
				self.checkAudioStatus(audioId, percentage);
				return;
			}
			
			// Set the progress bar width
			var progress = percent + '%';
			self.uploaderProgressBar().css('width', progress);
			self.uploaderProgressText().html(progress);

			// This should run in a loop
			self.checkAudioStatus(audioId, percent);
		});
	},

	editDescription: function() {

		self.form().addClass('editing-description');

		setTimeout(function(){

			var descriptionClone = self.previewDescription().clone();
			var noDescription = descriptionClone.hasClass("no-description");

			descriptionClone.wrapInner(self.description());

			if (noDescription) {
				self.description().val("");
			}

			self.description()
				.val(self.previewDescription().text())
				.focus()[0].select();

			// Save the description when there is changes in the textbox. #819
			$(self.description()).on('change keyup paste', function() {
				self.saveDescription("apply");
			});

			$(document).on(self.editDescriptionEvent, function(event) {
				if (event.target!==self.description()[0]) {
					self.saveDescription("save");
				}
			});
		}, 1);
	},

	saveDescription: function(operation) {

		if (!operation) {
			operation = 'save';
		}

		var value = self.description().val().replace(/\n/g, "<br//>");

		switch (operation) {

			case "save":

				var noValue = (value==="");

				self.previewDescription()
					.toggleClass("no-description", noValue);

				if (noValue) {
					value = self.description().attr("placeholder");
				}

				self.previewDescription()
					.html(value);

				self.audio.description = value;

				self.form().find(".textareaClone").remove();

				self.form().removeClass("editing-description");

				$(document).off(self.editDescriptionEvent);
				break;
			case "apply":
				self.audio.description = value;
			case "revert":
				break;
		}
	},

	"{window} easysocial.story.audio.panel.insertaudiolink" : function(el, ev, url) {
		
		if (self.audio || self.processing || !url) {
			return;
		}

		// Switch to audio panel
		self.panelButton().click();

		// Clear up any data inside the form
		self.clearForm(true);

		// Append the audio link
		self.audioLink().val(url);

		// Process audio link
		self.insertAudio().click();
	},	

	"{uploaderForm} FilesAdded": function() {

		// Set the state to uploading
		self.isUploading();

		// Start the upload
		self.plupload.start();
	},

	"{uploaderForm} UploadProgress": function(el, event, uploader, file) {
		// Set the progress bar width
		var progress = file.percent + '%';

		self.uploaderUploadBar().css('width', progress);
		self.uploaderUploadText().html(progress);

	},

	"{uploaderForm} FileUploaded": function(uploaderForm, event, uploader, file, response) {

		// Server thrown an error
		if (response.error) {

			// Set the message
			self.clearMessage();
			self.setMessage(response.error);

			// Display the audio upload form again
			self.clearForm(true);

			return false;
		}

		// If the server isn't encoding on the fly, we should display some message
		if (!response.isEncoding) {

			self.processing = false;

			// Set the progress bar to 100%
			self.uploaderProgressBar().css('width', '100%');
			self.uploaderProgressText().html('100%');

			// Update the state
			self.isProcessed();

			// Update the preview
			self.updatePreview('upload', response.data, response.thumbnail);

			self.audio.isEncoding = true;

			// Reset the progress bar
			self.resetProgress();

			return;
		}

		// Set status to encoding
		self.isEncoding();

		self.processing = true;

		// Update the progress since the audio needs to be converted.
		self.checkAudioStatus(response.data.id, 0);
	},

	"{uploaderForm} Error": function(el, event, uploader, error) {

		// Get the error message
		var message = opts.errors[error.code];

		self.story.setMessage(message, "error");
	},

	"{previewTitle} click": function() {

		var editing = self.form().hasClass('editing-title');

		self.form().toggleClass('editing-title', !editing);

		if (!editing) {
			self.editTitle();
		}
	},

	"{previewArtist} click": function() {

		var editing = self.form().hasClass('editing-artist');

		self.form().toggleClass('editing-artist', !editing);

		if (!editing) {
			self.editArtist();
		}
	},

	"{previewAlbum} click": function() {

		var editing = self.form().hasClass('editing-album');

		self.form().toggleClass('editing-album', !editing);

		if (!editing) {
			self.editAlbum();
		}
	},

	"{previewDescription} click": function() {
		var editing = self.form().hasClass('editing-description');

		self.form().toggleClass('editing-description', !editing);

		if (!editing) {
			self.editDescription();
		}
	},

	"{audioGenre} change": function(audioGenre) {
		self.currentGenre = audioGenre.val();
	},

	"{audioLink} paste": function() {
		setTimeout(function() {
			self.insertAudio().click();
		}, 100);
	},

	"{insertAudio} click": function() {

		var url = self.audioLink().val();

		if (!url || self.processing) {
			return;
		}

		// Hide the form
		self.isProcessing();

		EasySocial.ajax('ajax:/apps/user/audios/controllers/process/process', {
			"type": "link",
			"link": url
		}).done(function(data, image, embed) {
			self.isProcessed();

			data.link = url;

			self.updatePreview('link', data, image);
		}).fail(function(message){

			self.isProcessed();

			self.clearForm(true);

			self.story.setMessage(message, "error");
		});
	},

	"{removeButton} click": function(removeButton) {
		self.clearForm(true);
	},

	//
	// Saving
	//

	"{story} save": function(element, event, save) {

		if (save.currentPanel != 'audios') {
			return;
		}

		// Here we save everything before submit
		if (opts.isEdit) {
			self.saveAlbum();
			self.saveArtist();
			self.saveTitle('apply');
		}
		
		var url = self.audioLink().val();
		
		// If uploading an audio link
		if (url && !self.audio) {
			save.reject(opts.errors.messages.insert);
			return;
		}

		// If sharing an audio without link and upload
		if (!url && !self.audio) {
			save.reject(opts.errors.messages.empty);
			return;
		}

		// Add the task for uploading audio
		self.uploadingAudio = save.addTask("uploadingAudio");

		self.save(save);
	},

	"{story} afterSubmit": function() {

		var uploadingAudio = self.uploadingAudio;

		if (!uploadingAudio) {
			return;
		}

		// Reset the form upon submission
		self.clearForm(true);

		delete self.uploadingAudio;

		if (self.audio && self.audio.isEncoding) {

			EasySocial.dialog({
				content: EasySocial.ajax('site/views/audios/showEncodingMessage')
			});

			delete self.audio;
			return;
		}

		delete self.audio;
	},

	save: function(save) {

		var uploadingAudio = self.uploadingAudio;

		if (!uploadingAudio) {
			return;
		}

		if (self.processing) {
			save.reject(opts.errors.messages.processing);
			return;
		}

		// Attach the genre to the audio data
		self.audio.genre = self.audioGenre().val();

		if (!self.audio.genre || self.audio.genre == 0) {
			save.reject(opts.errors.messages.genre);
			return;
		}

		save.addData(self, self.audio);

		uploadingAudio.resolve();

		self.audioType = self.audio.type;
	},

	"{story} clear": function() {
		self.clearForm(false);
	},

	"{window} easysocial.story.audio.panel.insertaudiolink" : function(el, ev, url) {

		if (self.audio || self.processing || !url) {
			return;
		}

		// Switch to audio panel
		self.panelButton().click();

		// Clear up any data inside the form
		self.clearForm(true);

		// Append the audio link
		self.audioLink().val(url);

		// Process audio link
		self.insertAudio().click();
	}
}});

// Resolve module
module.resolve();

});

});
