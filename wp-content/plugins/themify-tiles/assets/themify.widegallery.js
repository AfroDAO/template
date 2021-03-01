/**
 * Themify Wide Gallery Plugin
 * Functionality for WordPress Gallery Shortcode or Themify Gallery Post Type
 * Copyright (c) Themify
 *
 * Elements:
 * twg-wrap element wrapping both twg-holder and twg-controls
 * twg-holder element where the image is loaded
 * twg-caption element where the caption is written
 * twg-controls wrapper for the list of interface elements
 * twg-list list of interface elements that drive the image and caption
 * twg-item atomic interface element
 * twg-link trigger link
 */
;(function ( $, window, document, undefined ) {

	var defaults = {
			info: '.twg-info',
			terms: '.twg-terms',
			title: '.twg-title',
			date: '.twg-date',
			primaryAction: '.twg-primary-action',
			actions: '.twg-actions',
			speed: 300,
			event: 'click',
			ajax_url: '',
			ajax_action: 'themify_get_gallery_entry',
			ajax_nonce: '',
			networkError: '',
			termSeparator: '',
			loader: '<div class="themify-loader"><div class="themify-loader_1 themify-loader_blockG"></div><div class="themify-loader_2 themify-loader_blockG"></div><div class="themify-loader_3 themify-loader_blockG"></div></div>'
		};

	function ThemifyWideGallery( element, options ) {
		this.element = element;
		this.options = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this.init();
	}

	ThemifyWideGallery.prototype = {

		init: function () {
			if ( $(this.element).hasClass( 'twg-gallery-shortcode' ) ) {
				this.galleryShortcode();
			} else {
				this.galleryPostType();
			}

			// on resize event
			var resizeId, self = this;
			$(window).resize(function(){
				clearTimeout(resizeId);
				resizeId = setTimeout(function(){
					self.doneResizing($(self.element));
				}, 500);
			}).on('orientationchange', function(){
				self.doneResizing($(self.element));
			});
		},

		/**
		 * Function to render wide gallery based on gallery post type entries
		 */
		galleryPostType: function() {
			var self = this,
				$wrap = $(this.element),
				$controls = $('.twg-controls', this.element),
				$holder = $('.twg-holder', this.element),
				$loading = $('.twg-loading', this.element),
				$img = $('<img />' );

			// First image
			$img.on('load', function(){
				var $firstEntry = $('.twg-link:first-child', $controls);
				// Hide loading icon
				$loading.html('').fadeOut();
				// Add first image and caption
				if ( typeof $.fn.backstretch !== 'undefined') {
					$wrap.backstretch($(this).attr('src'));
					$('.backstretch', $wrap).addClass('twg-deleteable').hide().fadeIn();
				} else {
					$(this).addClass('twg-deleteable').hide().prependTo($wrap).fadeIn();
				}
				// Show first entry data
				self.displayEntry($firstEntry.data('entry_id'));

			}).attr('src', $('.twg-link:first-child', $controls).data('image'));

			// Attach click event to thumbnails
			$controls.on(self.options.event, '.twg-link', function(e) {
				e.preventDefault();
				self.initEntryDisplay($(this));
			});
		},

		initEntryDisplay: function($el) {
			var self = this,
				$wrap = $(this.element),
				$holder = $('.twg-holder', this.element),
				$loading = $('.twg-loading', this.element),
				url = $el.data('image'),
				$img = $('<img />' );

			// Show loading icon
			$loading.html(self.options.loader).fadeIn();

			// When image is loaded, do function
			$img.on('load', function() {
				// Set the new image behind the old one
				if ( typeof $.fn.backstretch !== 'undefined') {
					$wrap.backstretch($(this).attr('src'));
					$('.backstretch', $wrap).addClass('twg-behind');
				} else {
					$(this).addClass('twg-behind').prependTo($wrap);
				}

				// Hide old image
				$('.twg-deleteable', $wrap).fadeOut(self.options.speed, function(){
					// Set new image as the one that will be deleted later
					$('.twg-behind', $wrap).addClass('twg-deleteable' ).removeClass('twg-behind');
					// Remove old image
					$(this).remove();
					// Hide loading icon
					$loading.html('').fadeOut();
				});
				// Show entry data
				self.displayEntry($el.data('entry_id'));

			// Start image load
			}).attr('src', url);
		},

		displayEntry: function(id) {
			var self = this,
				$info = $(this.options.info, self.element),
				$terms = $(this.options.terms, self.element),
				$title = $(this.options.title, self.element),
				$date = $(this.options.date, self.element ),
				$primaryAction = $(this.options.primaryAction, self.element ),
				$actions = $(this.options.actions, self.element ),
				$caption = $('.twg-caption', this.element);

			// Hide all entry content
			$info.fadeOut(self.options.speed);

			// Request gallery entry
			$.post(self.options.ajax_url,
			{
				action: self.options.ajax_action,
				nonce: self.options.ajax_nonce,
				entry_id: id
			},
			function(response, status) {
				if('success' != status) {
					$caption.text(self.options.networkError).wrapInner('<div class="text-caption" />');
					return;
				}
				var entry = jQuery.parseJSON(response);

				// Clear existing terms and add new terms
				$terms.empty();
				$.each(entry.terms, function(index, item){
					$('<a href="' + item.link + '">' + item.name + '</a>').appendTo($terms);
					$('<span class="separator">' + self.options.termSeparator + '</span>' ).appendTo($terms);
				});

				// Set title and permalink
				$title.text(entry.title).attr('href', entry.link);

				// Set date
				$date.text(entry.date);

				// Clear caption and set new content
				$caption.empty();
				$caption.append(entry.excerpt);
				if ( entry.excerpt != '' ) {
					$caption.wrapInner('<div class="text-caption" />');
				}

				// Set link
				$primaryAction.attr('href', entry.link);
				$actions.fadeIn();

				// Show all entry content
				$info.fadeIn(self.options.speed);
			});
		},

		/**
		 * Function to render wide gallery based on images set in WordPress gallery shortcode
		 */
		galleryShortcode: function() {
			var self = this,
				$wrap = $(this.element),
				$caption = $('.twg-caption', this.element),
				$controls = $('.twg-controls', this.element),
				$holder = $('.twg-holder', this.element ),
				$loading = $('.twg-loading', this.element);
				$img = $('<img />');

			// First image
			$img.on('load', function() {
				// Hide loading icon
				$loading.html('').fadeOut();

				// Add first image and caption
				if ( typeof $.fn.backstretch !== 'undefined') {
					$wrap.backstretch($(this).attr('src'));
					$('.backstretch', $wrap).addClass('twg-deleteable').hide().fadeIn(self.options.speed, function(){
						$caption.text($('.twg-link:first-child', $controls).data('description'));
						if ( $caption.text() != '' ) {
							$caption.wrapInner('<div class="text-caption" />');
						}
					});
				} else {
					$(this).addClass('twg-deleteable').hide().prependTo($wrap).fadeIn(self.options.speed, function(){
						$caption.text($('.twg-link:first-child', $controls).data('description'));
						if ( $caption.text() != '' ) {
							$caption.wrapInner('<div class="text-caption" />');
						}
					});
				}

			}).attr('src', $('.twg-link:first-child', $controls).data('image'));

			// Attach click event to thumbnails
			$controls.on(self.options.event, '.twg-link', function(e) {
				e.preventDefault();
				self.initImageDisplay($(this));
			});
		},

		initImageDisplay: function($el){
			var self = this,
				$wrap = $(this.element),
				$caption = $('.twg-caption', this.element),
				$holder = $('.twg-holder', this.element ),
				$loading = $('.twg-loading', this.element),
				url = $el.data('image'),
				$img = $('<img />' );

			// Show loading icon
			$loading.html(self.options.loader).fadeIn();

			// When image is loaded, do function
			$img.on('load', function() {
				// Set the new image behind the old one
				if ( typeof $.fn.backstretch !== 'undefined') {
					$wrap.backstretch($(this).attr('src'));
					$('.backstretch', $wrap).addClass('twg-behind');
				} else {
					$(this).addClass('twg-behind').prependTo($wrap);
				}
				// Hide old image
				$('.twg-deleteable', $wrap).fadeOut(self.options.speed, function(){
					// Set new image as the one that will be deleted later
					$('.twg-behind', $wrap).addClass('twg-deleteable').removeClass('twg-behind');
					// Remove old image
					$(this).remove();
					// Hide loading icon
					$loading.html('').fadeOut();
				});
				// Hide caption, set new text and show again
				$caption.fadeOut( self.options.speed, function(){
					$(this).text($el.data('description')).fadeIn();
					if ( $(this).text() != '' ) {
						$caption.wrapInner('<div class="text-caption" />');
					}
				});
			// Start image load
			}).attr('src', url);
		},

		doneResizing: function($el){
			if ( typeof $.fn.backstretch !== 'undefined') {
				var instance = $el.data("backstretch");
				if('undefined' !== typeof instance) instance.resize();
			}
		},

		isTouchDevice: function () {
			try {
				document.createEvent('TouchEvent');
				return true;
			} catch ( e ) {
				return false;
			}
		}
	};

	$.fn.ThemifyWideGallery = function ( options ) {
		return this.each( function () {
			var obj = $.data( this, 'ThemifyWideGallery' );
			if ( ! obj ) {
				$.data( this, 'ThemifyWideGallery', new ThemifyWideGallery( this, options ) );
			}
			if ( 'undefined' != typeof options.method && 'function' == typeof obj[options.method] ) {
				if ( 'undefined' != typeof options.element ) {
					obj[options.method](options.element);
				} else {
					obj[options.method]();
				}
			}
		});
	};

})( jQuery, window, document );