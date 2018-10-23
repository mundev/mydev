EasyBlog.ready(function($){

	EasyBlog.compareVersion = function(version1, version2) {
		var nRes = 0;
		var parts1 = version1.split('.');
		var parts2 = version2.split('.');
		var nLen = Math.max(parts1.length, parts2.length);

		for (var i = 0; i < nLen; i++) {
			var nP1 = (i < parts1.length) ? parseInt(parts1[i], 10) : 0;
			var nP2 = (i < parts2.length) ? parseInt(parts2[i], 10) : 0;

			if (isNaN(nP1)) { 
				nP1 = 0; 
			}
			
			if (isNaN(nP2)) { 
				nP2 = 0; 
			}

			if (nP1 != nP2) {
				nRes = (nP1 > nP2) ? 1 : -1;
				break;
			}
		}

		return nRes;
	}

	// Retrieve the latest version info. This is much faster than php and doesn't add weight on the server	
	var versionUpdateExists = $('[data-online-version]').length > 0;

	if (versionUpdateExists) {
		$.ajax({
			url: "<?php echo EBLOG_VERSION_SERVICE;?>",
			jsonp: "callback",
			dataType: "jsonp",
			data: {
				"apikey": "<?php echo $this->config->get('main_apikey');?>",
				"current": "<?php echo $localVersion;?>"
			},
			success: function(data) {

				if (data.error) {
					$('#eb.eb-admin').prepend('<div style="margin-bottom: 0;" class="app-alert is-on alert alert-danger"><div class="row-table"><div class="col-cell cell-tight"><i class="fa fa-bolt"></i></div><div class="col-cell pl-10 pr-10">' + data.error + '</div></div></div>');
				}

				var version = {
					"latest": data.version,
					"installed": "<?php echo $localVersion;?>"
				};

				var outdated = EasyBlog.compareVersion(version.installed, version.latest) === -1;

				// Update the latest version
				$('[data-online-version]').html(version.latest);
				$('[data-local-version]').html(version.installed);
				
				if (outdated) {
					$('[data-version-checks]').toggleClass('require-updates');
				} else {
					$('[data-version-checks]').toggleClass('latest-updates');	
				}

				// Update with banner
				var banner = $('[data-outdated-banner]');

				if (banner.length > 0 && outdated) {
					banner.removeClass('hidden');
				}
			}
		});
	}

	// Sidebar menu functions
	$('[data-sidebar-parent]').on('click', function() {
		var parent = $(this).parent();

		// Disable all open states
		$('[data-sidebar-item]').removeClass('active open');

		parent.toggleClass('active open');
	});


	// Fix the header for mobile view
	$('.container-nav').appendTo($('.header'));

	$(window).scroll(function () {
		if ($(this).scrollTop() > 50) {
			$('.header').addClass('header-stick');
		} else if ($(this).scrollTop() < 50) {
			$('.header').removeClass('header-stick');
		}
	});

	$('.nav-sidebar-toggle').click(function(){
		$('html').toggleClass('show-easyblog-sidebar');
		$('.subhead-collapse').removeClass('in').css('height', 0);
	});

	// Bind tabs for settings
	$('[data-form-tabs]').on('click', function() {
		var active = $(this).attr('href');

		active = active.replace('#', '');

		var hiddenInput = $('[data-settings-active]');

		if (hiddenInput.length > 0) {
			hiddenInput.val(active);
		}
	});

});