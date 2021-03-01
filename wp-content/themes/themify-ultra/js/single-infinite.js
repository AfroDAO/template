/* Themify Single Infinite Scroll Script - https://themify.me/ */
var wp, themifySI;

(function ( $ ) {

	'use strict';

	var $body, $window = $( window );

	themifySI.run = {

		// Insertion point reference
		$point: null,

		// Handle MediaElement.js
		wpMediaelement: null,

		// Back to top link
		$backTop: null,

		// Cache #footerwrap
		$footerwrap: null,

		/**
		 * Initialize everything
		 */
		constructor: function() {
			var self = this;

			// Check ad JS template
			this.adReady = $( '#tmpl-themify_ad' ).length > 0;

			// Cache ad code.
			this.loadedAd = undefined;

			// Flag so a new fetch is not performed until the current fetch finishes.
			this.blockFetch = false;

			// Everything must be added before this element so this is always at the end
			this.$point = $( '<div id="single-infinite-wrap" class="clearfix" />' );

			// Add reactive element
			$( '#body' ).append( this.$point );

			// Append loading animation
			this.$loadingImage = $( '<img />', { src: themifySI.loading, class: 'single-infinite-loading' } );
			this.$point.append( this.$loadingImage );

			// Bind functions to this so they have the correct scope
			_.bindAll( this, 'atTheEnd', 'maybeChangeURL', 'loadMore', 'onScroll', 'toggleBackTop' );

			if ( themifySI.manual ) {
				var loadMore = document.createElement( 'a' );
				loadMore.className = 'single-infinite-load load-more-button';
				loadMore.textContent = themifySI.texts.load_more;
				$body.on( 'click', '.single-infinite-load', this.loadMore );
				this.$point.after( $( '<p>', { id: 'load-more' } ).append( loadMore ) );
			} else {
				// Perform initial end of post check.
				this.atTheEnd();

				// Check end of post on scroll throttling it to avoid multiple firing.
				$window.on( 'scroll.themifysi', _.throttle( this.atTheEnd, 50 ) );
			}

			// Throttle browser URL change.
			$window.on( 'scroll.themifysi', _.throttle( this.onScroll, 250 ) );

			// Perform actions when Builder is turned on/off.
			$body.on( 'builder_toggle_frontend', this.builderToggle );

			// Google Analytics integration
			$body.on( 'postloaded.themify', this.googleAnalytics );
		},

		/**
		 * Integration with Google Analytics, log page views on load
		 */
		googleAnalytics: function( e, response, $post ) {
			if( response.type == 'success' ) {
				if ( 'object' === typeof _gaq ) {
					_gaq.push( [ '_trackPageview', response.previous_post_url ] );
				}
				if ( 'function' === typeof ga ) {
					ga( 'send', 'pageview', response.previous_post_url );
				}
			}
		},

		/**
		 * On scroll, check if user reached the end of the post and perform the tasks.
		 */
		atTheEnd: function() {
			if ( ! this.blockFetch && this.isVisible( this.$point ) && ! $body.hasClass( 'themify_builder_active' ) ) {
				this.loadMore();
			}
		},

		/**
		 * Load ad, set flag indicating loading started and load next post.
		 */
		loadMore: function() {
			// Insert ad code between posts
			this.fetchAd();

			// Block fetch, show loading animation
			this.loadingStarted();

			// Load next post URL
			this.getPreviousEntryURL();
		},

		/**
		 * Perform actions on document scroll.
		 */
		onScroll: function() {
			this.maybeChangeURL();
			this.toggleBackTop();
		},

		/**
		 * Show or hide the link to go back to the top.
		 */
		toggleBackTop: function() {
			if ( _.isNull( this.$backTop ) ) {
				return;
			}
			if ( this.$backTop.length > 0 && ( this.isVisible( this.$footerwrap ) || window.scrollY < 10 ) ) {
				this.$backTop.addClass( 'infinite-back-top-hide' );
			} else {
				this.$backTop.removeClass( 'infinite-back-top-hide' );
			}
		},

		/**
		 * Change URL in browser's address bar according to the post visible in viewport.
		 */
		maybeChangeURL: function() {
			var state = {
				title: '',
				url  : ''
			};
			var self = this,
				$wrapper = $( '.single_posts_wrapper' );
			if ( window.scrollY < 50 ) {
				var $self = $wrapper.eq( 0 );
				state = {
					title: $self.data( 'title' ),
					url  : $self.data( 'url' )
				};
			} else {
				$wrapper.each( function () {
					var $self = $( this );
					if ( self.isVisible( $self ) ) {
						state = {
							title: $self.data( 'title' ),
							url  : $self.data( 'url' )
						};
					}
				} );
			}
			if(state.title){
				history.replaceState( {}, state.title, state.url );
				document.title = state.title;
            }
		},

		/**
		 * Triggered when entry html, styles and scripts are fully loaded.
		 *
		 * @param { object } response
		 */
		afterPostLoad: function( response ) {

			// Insert ad
			this.insertAd();

			if ( 'success' === response.type ) {

				// Load styles and scripts
				this.loadAssets( response );

				return;
			}

			// Unblock fetch, hide loading animation
			this.loadingEnded();
		},

		/**
		 * Add HTML. This is called after scripts are loaded.
		 */
		insertHTML: function( response ) {
			var $post = $( response.html );

			// Open Turn On Builder button in a new tab and auto-load Builder
			var $tbContent = $post.find('.themify_builder_content');
			if ( $tbContent.length > 0 && 'undefined' !== typeof tbLoaderVars ) {
				$tbContent.after( $( '<a class="themify_builder_turn_on" href="#"><span class="dashicons dashicons-edit"></span>' + tbLoaderVars.turnOnBuilder + '</a>' ).on( 'click', function( e ) {
					e.preventDefault();
					document.location = response.previous_post_url + '#builder_active';
					document.location.reload();
				} ) );
			}

			// Add nodes before insertion point
			$post.insertBefore( this.$point ).fadeIn();

			// Update browser URL pointing to the loaded entry
			this.maybeChangeURL();

			// Initialize JS elements
			if ( 'undefined' !== typeof tbLocalScript && ! _.isUndefined( tbLocalScript.transitionSelectors ) ) {
				_.each( tbLocalScript.transitionSelectors.split(','), function( selector ) {
					$( selector.trim() ).css( 'visibility', 'visible' );
				});
			}

			if ( 'undefined' !== typeof ThemifyBuilderModuleJs ) {
				ThemifyBuilderModuleJs.document_ready();
				ThemifyBuilderModuleJs.window_load();
			}
			this.initializeMejs( {}, response );

			/**
			 * Emit event so plugins can hook to it.
			 *
			 * @param { object } response The complete object returned when post was loaded.
			 * @param { object } $post A jQuery object reference to the markup inserted.
			 */
			$body.trigger( 'postloaded.themify', [response, $post] );

			// Unblock fetch, hide loading animation
			this.loadingEnded();
		},

		/**
		 * Load styles and scripts required by post loaded.
		 */
		loadAssets: function( response ) {
			// If additional stylesheets are required by the incoming set of posts, parse them
			if ( response.styles ) {
				_.each( response.styles, function ( element ) {
					// Add stylesheet handle to list of those already parsed
					themifySI.styles.push( element.handle );

					// Build link tag and append to DOM in head
					var style = document.createElement( 'link' );
					style.rel = 'stylesheet';
					style.href = element.src;
					style.id = element.handle + '-css';
					document.getElementsByTagName( 'head' )[0].appendChild( style );
				}, this );
			}
			// If additional scripts are required by the incoming set of posts, parse them
			if ( response.scripts ) {
				var countScripts = response.scripts.length - 1;
				_.each( response.scripts, function ( element ) {
					var self = this,
						elementToAppendTo = element.footer ? 'body' : 'head';

					// Add script handle to list of those already parsed
					themifySI.scripts.push( element.handle );

					// Output extra data, if present
					if ( element.extra_data ) {
						var data = document.createElement( 'script' ),
							dataContent = document.createTextNode( "//<![CDATA[ \n" + element.extra_data + "\n//]]>" );
						data.type = 'text/javascript';
						data.appendChild( dataContent );
						document.getElementsByTagName( elementToAppendTo )[0].appendChild( data );
					}

					// Build script tag and append to DOM in requested location
					var script = document.createElement( 'script' );
					script.type = 'text/javascript';
					script.src = element.src;
					script.id = element.handle;
					script.async = false;
					script.onload = function() {
						if ( 0 === countScripts ) {
							// If last script loaded ok, insert HTML.
							self.insertHTML( response );
						}
						countScripts--;
					};
					script.onerror = function() {
						if ( 0 === countScripts ) {
							// If last script loaded with error, still insert HTML.
							self.insertHTML( response );
						}
						countScripts--;
					};
					// If MediaElement.js is loaded in by this set of posts, don't initialize the players a second time as it breaks them all
					if ( 'wp-mediaelement' === element.handle ) {
						$body.unbind( 'postloaded.themify', this.initializeMejs );
					}

					if ( 'wp-mediaelement' === element.handle && 'undefined' === typeof mejs ) {
						this.wpMediaelement = {};
						this.wpMediaelement.tag = script;
						this.wpMediaelement.element = elementToAppendTo;
						setTimeout( this.maybeLoadMejs.bind( this ), 250 );
					} else {
						document.getElementsByTagName( elementToAppendTo )[0].appendChild(script);
					}
				}, this );
			} else {
				// If there are no scripts, insert HTML immediately.
				this.insertHTML( response );
			}
		},

		/**
		 * Delay MediaElement initialization until it's loaded, which is when 'mejs' will be defined.
		 */
		maybeLoadMejs: function () {
			if ( null === this.wpMediaelement ) {
				return;
			}

			if ( 'undefined' === typeof mejs ) {
				setTimeout( this.maybeLoadMejs, 250 );
			} else {
				document.getElementsByTagName( this.wpMediaelement.element )[0].appendChild( this.wpMediaelement.tag );
				this.wpMediaelement = null;

				// Ensure any subsequent IS loads initialize the players
				$body.bind( 'postloaded.themify', { self: this }, this.initializeMejs );
			}
		},

		/**
		 * Initialize MediaElement in any player not previously initialized.
		 *
		 * @param { object } e Event object.
		 * @param { response } response Object modeling the post loaded.
		 */
		initializeMejs: function ( e, response ) {
			// Are there media players in the post loaded?
			if ( !response.html || -1 === response.html.indexOf( 'wp-audio-shortcode' ) && -1 === response.html.indexOf( 'wp-video-shortcode' ) ) {
				return;
			}

			// Don't bother if mejs isn't loaded for some reason
			if ( 'undefined' === typeof mejs ) {
				return;
			}

			// Adapted from wp-includes/js/mediaelement/wp-mediaelement.js
			// Modified to not initialize already-initialized players, as Mejs doesn't handle that well
			$( function () {
				var settings = {};

				if ( typeof _wpmejsSettings !== 'undefined' ) {
					settings.pluginPath = _wpmejsSettings.pluginPath;
				}

				settings.success = function ( mejs ) {
					var autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;
					if ( 'flash' === mejs.pluginType && autoplay ) {
						mejs.addEventListener( 'canplay', function () {
							mejs.play();
						}, false );
					}
				};

				$( '.wp-audio-shortcode, .wp-video-shortcode' ).not( '.mejs-container' ).mediaelementplayer( settings );
			} );
		},

		/**
		 * Insert ad code. Checks that JS template exists.
		 */
		insertAd: function () {
			if ( this.adReady && _.isObject( this.loadedAd ) ) {
				this.$point.before( wp.template( 'themify_ad' )( this.loadedAd ) ).fadeIn();
			}
		},

		/**
		 * Fetch ad code.
		 */
		fetchAd: function () {
			var self = this;
			if ( _.isUndefined( this.loadedAd ) ) {
				$.post( themifySI.admin_ajax_url, {
					action     : 'themify_theme_fetch_ad',
					_ajax_nonce: themifySI.ajax_nonce
				}, function ( response ) {
					if ( _.isObject( response ) && !_.isUndefined( response.success ) && response.success ) {
						self.loadedAd = _.isObject( response.data ) ? response.data : '';
					}
				} );
			}
		},

		/**
		 * Fetch previous post URL, load HTML and start loading styles and scripts.
		 */
		getPreviousEntryURL: function () {
			var self = this;

			$.post( themifySI.ajax_url, {
				action     : 'themify_theme_get_previous_entry',
				_ajax_nonce: themifySI.ajax_nonce,
				post_id    : themifySI.post_id,
				styles: themifySI.styles,
				scripts: themifySI.scripts
			}, function ( response ) {
				if ( _.isObject( response ) && !_.isUndefined( response.success ) && response.success ) {
					var data = _.isObject( response.data ) ? response.data : '';
					if ( data ) {
						// Set current post ID to the previous one so it's ready for next retrieval
						self.afterPostLoad( data );
						if ( data.previous_post_id ) {
							themifySI.post_id = data.previous_post_id;
						}
					}
				} else {
					self.$loadingImage.remove();
					$window.off( 'scroll.themifysi' );
				}
			} );
		},

		/**
		 * When loading starts, show loading image and block further requests.
		 */
		loadingStarted: function () {
			if ( this.$loadingImage.length > 0 ) {
				this.$loadingImage.removeClass( 'js-hide-loading' ).addClass( 'js-show-loading' );
			}

			// Block ad insertion until afterPostLoad()
			this.blockFetch = true;
		},

		/**
		 * When loading ends, hide loading image and allow further requests.
		 */
		loadingEnded: function () {
			if ( this.$loadingImage.length > 0 ) {
				this.$loadingImage.removeClass( 'js-show-loading' ).addClass( 'js-hide-loading' );
			}

			// Create back to top link if it doesn't exist
			if ( _.isNull( this.$backTop ) ) {
				this.$footerwrap = $( '#footerwrap' );
				this.$backTop = $( themifySI.back_top ).on( 'click', function( e ) {
					e.preventDefault();
					$( 'body,html' ).animate( {
						scrollTop: 0
					}, 800 );
				});
				$( 'body' ).append( this.$backTop );
			}
			// Check back to top link visibility
			this.toggleBackTop();

			// Unblock ad insertion
			this.blockFetch = false;
		},

		/**
		 * Check if the insertion point is visible
		 *
		 * @param { object } el
		 */
		isVisible: function ( el ) {
			var win = $window,
				viewport = {
					top : win.scrollTop(),
					left: win.scrollLeft()
				},
				bounds = el.offset();

			viewport.right = viewport.left + win.width();
			viewport.bottom = viewport.top + win.height();

			bounds.right = bounds.left + el.outerWidth();
			bounds.bottom = bounds.top + el.outerHeight();

			return !(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom);
		},

		/**
		 * When Builder is turned on, dismiss the posts loaded and set post id again to the current post.
		 */
		builderToggle: function() {
			themifySI.post_id = themifySI.base_post_id;
			$( '#layout' ).find( '.single-divider-ad, .single-wrapper' ).not( '.single-wrapper:first-child' ).remove();
		}

	};

	$( document ).ready( function () {
		$body = $( 'body' );
		themifySI.run.constructor();
	} );

})(jQuery);