/**
* @package      Office Template
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* This is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
$ = jQuery;

$(document).ready(function ($) {

	EasySocial.isMobile = function() {
		try {
			document.createEvent('TouchEvent');
			return true;
		} catch (e) {
			return false;
		}
	}

	// Start Helix3
	if (typeof sp_offanimation === 'undefined' || sp_offanimation === '') {
		sp_offanimation = 'default';
	}

	// Template sidebar
	var sidebar = $("#sidebar-toggler");
	var body = $("body");

	// Sidebar overlay
	$('<div class="sidebar-overlay"></div>').insertBefore('.sidebar-element');

	$("#sidebar-toggler .fa-bars").on("click touch", function(event) {
		addOffSidebar(event);
	});

	$("#sidebar-toggler .fa-remove, .sidebar-overlay, #es.mod-es-dropdown-menu .dropdown-toggle_, [data-office-noti-toggle], [data-office-search-toggle], [data-filter-item], [data-videos-filter], [data-audios-filter], [data-discussion-filter], [data-create-filter], [data-review-filter], [data-sidebar-item], [data-tasks-filter], [data-es-alert-item], [data-profile-edit-fields-step]")
		.on("click touch", function(event) {
			removeOffSidebar(event);
		});

	var addOffSidebar = function (event) {
		event.preventDefault();
		sidebar.addClass("open");
		body.addClass('offcanvas');
		body.removeClass('office-right-open');
		body.removeClass('office-left-open');
		
		// Removing double scrolling on mobile.
		$("body.is-mobile").attr("style", "overflow: hidden; position: fixed;");
	}

	var removeOffSidebar = function (event) {
		event.preventDefault();
		body.removeClass("offcanvas");
		sidebar.removeClass("open");
		body.removeClass('office-right-open');
		body.removeClass('office-left-open');

		// Removing double scrolling on mobile.
		$("body.is-mobile").attr("style", "");
	}

	// Mobile sidebar
	$('[data-office-noti-toggle]').on('click touch', function(event) {
		event.preventDefault();
		body.toggleClass('office-noti-open');
	});

	$("[data-office-search-toggle]").on("click touch", function(event) {
		event.preventDefault();
		body.toggleClass('office-search-open');
	});

	var leftToggle = body.find(".es-sidebar");

	if (leftToggle.length == 0) {
		leftToggle = body.find(".office-container__content-menu");
	}

	if (leftToggle.length > 0 && leftToggle.children().length > 0) {
		$('[data-office-left-toggle]').on('click touch', function(event) {
			event.preventDefault();
			body.toggleClass('office-left-open')
			.removeClass('office-right-open');
		});
	}
	else {
		$('[data-office-left-toggle]').remove();
	}

	var rightToggle = body.find('.office-container__right');

	if (rightToggle.length > 0 && rightToggle.children().length > 0) {
		$('[data-office-right-toggle]').on('click touch', function(event) {
			event.preventDefault();
			body.toggleClass('office-right-open')
			.removeClass('office-left-open');
		});
	}
	else {
		$('[data-office-right-toggle]').remove();
	}

	// Sticky header
	var stickyHeader = $("body.sticky-header");

	if (stickyHeader.length > 0) {
		var fixedSection = $("#sp-header");

		var headerHeight = fixedSection.outerHeight();
		var stickyNavTop = fixedSection.offset().top;

		// fixedSection.addClass("animated menu-fixed-out")
		// 	.before('<div class="nav-placeholder"></div>');

		// var navPlaceholder = $(".nav-placeholder");
		// navPlaceholder.height("inherit");

		var stickyNav = function() {
			var scrollTop = $(window).scrollTop();

			if (scrollTop > stickyNavTop) {
				fixedSection.removeClass("menu-fixed-out")
					.addClass("menu-fixed");
				navPlaceholder.height(headerHeight);
			}
			else {
				if (fixedSection.hasClass("menu-fixed")) {
					fixedSection.removeClass("menu-fixed")
						.addClass("menu-fixed-out");
					navPlaceholder.height("inherit");
				}
			}
		};

		stickyNav();
		$(window).scroll(function() {
			stickyNav();
		});
	}

	// Go to top.
	if (typeof sp_gotop === 'undefined' || sp_gotop === 0) {
		sp_gotop = '';
	}

	if (sp_gotop) {
		var scrollUp = $(".scrollup");

		$(window).scroll(function() {
			if ($(this).scrollTop() > 100) {
				scrollUp.fadeIn();
			}
			else {
				scrollUp.fadeOut(400);
			}
		});

		scrollUp.click(function() {
			$("html, body").animate({
				scrollTop: 0
			}, 600);

			return false;
		});
	}

	// Preloader
	if (typeof sp_preloader === 'undefined' || sp_preloader === 0) {
		sp_preloader = '';
	}

	if (sp_preloader) {
		$(window).on("load", function() {
			if ($(".sp-loader-with-logo").length > 0) {
				move();
			}

			setTimeout(function() {
				$(".sp-pre-loader").fadeOut();
			}, 1000);
		});
	}

	var move = function() {
		var element = $("#line-load");
		var width = 1;
		var id = setInterval(frame, 10);

		function frame() {
			if (width >= 100)  {
				clearInterval(id);
			}
			else {
				width++;
				element.attr("style", "width: " + width + "%");
			}
		};
	};

	// Tooltip
	$('[data-toggle="tooltip"]').tooltip();

	// EasySocial stuff
	EasySocial.require()
	.script('site/system/notifications')
	.done(function($) {
		var body = $("body");
		var userId = body.data('oid');

		body.implement(EasySocial.Controller.System.Notifications, {
			"userId": atob(userId)
		});

		// For notification
		body.on('notification.updates', function(event, data) {
			conversationTotal = data.conversation.total;
			friendRequestTotal = data.friend.total;
			systemNotificationTotal = data.system.total;

			if (conversationTotal > 0 || friendRequestTotal > 0 || systemNotificationTotal > 0) {
				body.addClass('has-es-noti');
			} else {
				body.removeClass('has-es-noti');
			}
		});
	});

	var appHeader = $('[data-office-app-header]').children();

	if (appHeader.length > 0) {
		$('.es-content').prepend(appHeader);
	}

	// Move officecontenttop & officecontentbottom position into es-content.
	if ($('#es[data-es-structure]').length > 0) {
		$('.officecontenttop').prependTo($(this).find('[data-es-container] .es-content'));
		$('.officecontentbottom').appendTo($(this).find('[data-es-container] .es-content'));
	}

});
