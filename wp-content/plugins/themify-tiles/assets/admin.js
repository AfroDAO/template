var ThemifyTiles;

jQuery(function($){

	var $body = $( 'body' );
	var current_tile = null;

	ThemifyTiles = {
		init : function(){
			ThemifyTiles.setupLightbox();
			ThemifyPageBuilder.openGallery();
			ThemifyPageBuilder.mediaUploader();
			$body.on( 'click', '.tf-tiles-add', ThemifyTiles.add_new_tile );
			$body.on( 'click', '.tf-tiles-save', ThemifyTiles.save_tiles );
			$body.on( 'click', '#tf-tiles-save-settings a', ThemifyTiles.preview_tile );
			$body.on( 'click', '#themify_tiles_lightbox_parent .close_lightbox', ThemifyTiles.closeLightbox );
			$body.on( 'dblclick', '#themify_tiles_overlay', ThemifyTiles.closeLightbox );
			$body.on( 'dblclick', '.tf-tiles-editing .tf-tile .themify_builder_module_front_overlay', ThemifyTiles.edit_tile );
			$body.on( 'click', '.tf-tiles-editing .tf-tile .themify_module_options', ThemifyTiles.edit_tile );
			$body.on( 'click', '.tf-tiles-editing .tf-tile .themify_module_delete', ThemifyTiles.remove_tile );
			$body.on( 'click', '.tf-tiles-editing .tf-tile .themify_module_duplicate', ThemifyTiles.duplicate_tile );

			$body.on( 'mouseenter', '.tf-tiles-editing .tf-tile', ThemifyTiles.add_edit_edit_overlay );
			$body.on( 'mouseleave', '.tf-tiles-editing .tf-tile', ThemifyTiles.remove_edit_overlay );

			// layout icon selected
			$body.on('click', '.tfl-icon', function(e){
				$(this).addClass('selected').siblings().removeClass('selected');
				e.preventDefault();
			});

			$('<div/>', {id: 'themify_builder_alert', class: 'themify-builder-alert'}).appendTo( 'body' ).hide();

			ThemifyTiles.enable_editor();
		},

		add_edit_edit_overlay : function(){
			var markup = '<div class="themify_builder_module_front_overlay" style="display: block;"></div>' + 
				'<div class="module_menu_front">' +
					'<ul class="themify_builder_dropdown_front" style="display: block;">' +
						'<li class="themify_module_menu">' +
							'<span class="ti-menu"></span>' +
							'<ul>' +
								'<li><a href="#" title="Edit" class="themify_module_options" data-module-name="box">Edit</a></li>' +
								'<li><a href="#" title="Duplicate" class="themify_module_duplicate">Duplicate</a></li>' +
								'<li><a href="#" title="Delete" class="themify_module_delete">Delete</a></li>' +
							'</ul>' +
						'</li>' +
					'</ul>' +
				'</div>';
			$( this ).prepend( markup );
		},

		remove_edit_overlay : function() {
			$( this ).find( '.themify_builder_module_front_overlay, .module_menu_front' ).remove();
			/* @todo: re-arrange the tiles */
		},

		add_new_tile : function(){
			// make sure current_tile is empty
			current_tile = null;

			ThemifyTiles.openLightbox( $('#themify-tiles-settings').html(), function(){} );
			return false;
		},

		remove_tile : function(e){
			e.preventDefault();
			$( this ).closest( '.tf-tile' ).remove();
		},

		duplicate_tile : function(e){
			// make sure current_tile is empty
			current_tile = null;

			var data = $.parseJSON( ThemifyTiles.get_tile_data( $( this ).closest( '.tf-tile' ) ) );
			ThemifyTiles._add_tile( data );
			return false;
		},

		getDocHeight: function(){
			var D = window.document;
			return Math.max(
				Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
				Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
				Math.max(D.body.clientHeight, D.documentElement.clientHeight)
			);
		},

		setupLightbox: function() {
			var isThemifyTheme = '',
			// var isThemifyTheme = 'true' == themifyBuilder.isThemifyTheme? 'is-themify-theme' : 'is-not-themify-theme',
				markup = '<div id="themify_tiles_lightbox_parent" class="themify_builder builder-lightbox themify_builder_admin clearfix ' + isThemifyTheme + '">' +
				'<h3 class="themify_builder_lightbox_title"></h3>' +
				'<a href="#" class="close_lightbox"><i class="ti ti-close"></i></a>' +
				'<div id="themify_tiles_lightbox_container"></div>' +
			'</div>' +

			'<div id="themify_tiles_overlay"></div>';

			$(markup).hide().appendTo('body');

		},

		setColorPicker: function(context) {
			$('.builderColorSelect', context).each(function(){
				var $minicolors = $(this),
					// Hidden field used to save the value
					$input = $minicolors.parent().parent().find('.builderColorSelectInput'),
					// Visible field used to show the color only
					$colorDisplay = $minicolors.parent().parent().find('.colordisplay'),
					setColor = '',
					setOpacity = 1.0,
					sep = '_';

				if ( '' != $input.val() ) {
					// Get saved value from hidden field
					var colorOpacity = $input.val();
					if ( -1 != colorOpacity.indexOf(sep) ) {
						// If it's a color + opacity, split and assign the elements
						colorOpacity = colorOpacity.split(sep);
						setColor = colorOpacity[0];
						setOpacity = colorOpacity[1] ? colorOpacity[1] : 1;
					} else {
						// If it's a simple color, assign solid to opacity
						setColor = colorOpacity;
						setOpacity = 1.0;
					}
					// If there was a color set, show in the dummy visible field
					$colorDisplay.val( setColor );
				}

				$minicolors.minicolors({
					opacity: 1,
					textfield: false,
					change: function(hex, opacity) {
						if ( '' != hex ) {
							if ( opacity && '0.99' == opacity ) {
								opacity = '1';
							}
							var value = hex.replace('#', '') + sep + opacity;
							$( this ).parent().parent().find('.builderColorSelectInput').val(value);
							$colorDisplay.val( hex.replace('#', '') );
						}
					}
				});
				// After initialization, set initial swatch, either defaults or saved ones
				$minicolors.minicolors('value', setColor);
				$minicolors.minicolors('opacity', setOpacity);
			});

			$('body').on('blur', '.colordisplay', function(){
				var $input = $(this),
					tempColor = '',
					$minicolors = $input.parent().find('.builderColorSelect'),
					$field = $input.parent().find('.builderColorSelectInput');
				if ( '' != $input.val() ) {
					tempColor = $input.val();
				}
				$input.val( tempColor.replace('#', '') );
				$field.val( $input.val().replace(/[abcdef0123456789]{3,6}/i, tempColor.replace('#', '')) );
				$minicolors.minicolors('value', tempColor);
			}).on('keyup', '.colordisplay', function(){
				var $input = $(this),
					tempColor = '',
					$minicolors = $input.parent().find('.builderColorSelect'),
					$field = $input.parent().find('.builderColorSelectInput');
				if ( '' != $input.val() ) {
					tempColor = $input.val();
				}
				$input.val( tempColor.replace('#', '') );
				$field.val( $input.val().replace(/[abcdef0123456789]{3,6}/i, tempColor.replace('#', '')) );
				$minicolors.minicolors('value', tempColor);
			});
		},

		openLightbox: function( options, callback, title ) {
			var self = ThemifyTiles,
				$lightboxContainer = $('#themify_tiles_lightbox_container');
			title = title || '';

			$lightboxContainer.empty();
			$('#themify_tiles_overlay').show();

			var top = $(document).scrollTop() + 50;

			// self.freezePage();
			// $( document ).on( 'keyup', ThemifyPageBuilder.lightboxCloseKeyListener );
			$("#themify_tiles_lightbox_parent")
				.show()
				.css('top', self.getDocHeight())
				.animate({
					top: top
				}, 800 );

			$('.themify_builder_lightbox_title').text(title);
			$lightboxContainer.html( options );
			$('#themify_tiles_lightbox_parent').show();

			// Get content height
			var h = $('#themify_tiles_lightbox_container').height(),
				windowH = $(window).height();

			$('#themify_tiles_lightbox_container .themify_builder_options_tab_content').css({'maxHeight': windowH * (70/100)});

			$body.trigger( 'tf_tiles_edit_tile' );

			if( $.isFunction( callback ) ){
				callback.call( this );
			}

			self.options_init();
		},

		closeLightbox : function(){
			if ( typeof tinyMCE !== 'undefined' ) {
				$( '#themify_tiles_lightbox_parent' ).find('.tfb_lb_wp_editor').each( function(){
					var $id = $(this).prop('id');
					switchEditors.go($id, 'tmce');
				});
			}
			$('#themify_tiles_overlay').hide();
			$("#themify_tiles_lightbox_parent").hide();
		},

		options_init: function(){
			ThemifyTiles.setColorPicker();

			// tabular options
			$('.themify_builder_tabs').tabs();

			$('#themify_tiles_lightbox_container').find('select').wrap('<div class="selectwrapper"></div>');
			$('.selectwrapper').click(function(){
				$(this).toggleClass('clicked');
			});

			$( '#type_front a, #type_back a' ).click(function(){
				var thiz = $(this),
					id = thiz.attr( 'id' );

				thiz.closest( '.themify_builder_tab' )
					.find( '> .tf-tile-options' ).hide()
					.filter( '.tf-tile-options-' + id ).show()
			});

			// patch to fix the display of grouped options
			$( '.tf-option-checkbox-enable input' ).click(function(){
				var val = $(this).val();
				$( this ).closest( '.themify_builder_tab' ).find( '.tf-group-element' ).hide().filter( '.tf-group-element-' + val ).show();
			}).filter( ':first-child' ).click();

			$( '.themify-layout-icon' ).each(function(){
				var $selected = $( this ).find( 'a' ).filter( '.selected' );
				if( $selected.length < 1 ) {
					$selected = $( this ).find( 'a' ).filter( ':first' );
				}
				$selected.click();
			});

			// TinyMCE editor
			$( '.tfb_lb_wp_editor' ).each(function(){
				var id = $( this ).attr( 'id' );
				ThemifyTiles.initQuickTags( id );
				if ( typeof tinyMCE !== 'undefined' ) {
					ThemifyTiles.initNewEditor( id );
				}
			});

			ThemifyTiles.builderPlupload( 'normal' );
		},

		initQuickTags: function(editor_id) {
			// add quicktags
			if ( typeof(QTags) == 'function' ) {
				quicktags( {id: editor_id} );
				QTags._buttonsInit();
			}
		},

		initNewEditor: function( editor_id ) {
			var self = ThemifyTiles;
			if ( typeof tinyMCEPreInit.mceInit[editor_id] !== "undefined" ) {
				self.initMCEv4( editor_id, tinyMCEPreInit.mceInit[editor_id] );
				return;
			}
			var tfb_new_editor_object = self.tfb_hidden_editor_object;
			
			tfb_new_editor_object['elements'] = editor_id;
			tinyMCEPreInit.mceInit[editor_id] = tfb_new_editor_object;

			// v4 compatibility
			self.initMCEv4( editor_id, tinyMCEPreInit.mceInit[editor_id] );
		},

		initMCEv4: function( editor_id, $settings ){
			// v4 compatibility
			if( parseInt( tinyMCE.majorVersion) > 3 ) {
				// Creates a new editor instance
				var ed = new tinyMCE.Editor(editor_id, $settings, tinyMCE.EditorManager);	
				ed.render();
			}
		},

		builderPlupload: function(action_text) {
			var self = ThemifyTiles,
				class_new = action_text == 'new_elemn' ? '.plupload-clone' : '',
				$builderPluploadUpload = $(".themify-builder-plupload-upload-uic" + class_new);

			if($builderPluploadUpload.length > 0) {
				var pconfig = false;
				$builderPluploadUpload.each(function() {
					var $this = $(this),
						id1 = $this.attr("id"),
						imgId = id1.replace("themify-builder-plupload-upload-ui", "");

					pconfig=JSON.parse(JSON.stringify(themify_builder_plupload_init));

					pconfig["browse_button"] = imgId + pconfig["browse_button"];
					pconfig["container"] = imgId + pconfig["container"];
					pconfig["drop_element"] = imgId + pconfig["drop_element"];
					pconfig["file_data_name"] = imgId + pconfig["file_data_name"];
					pconfig["multipart_params"]["imgid"] = imgId;
					pconfig["multipart_params"]["_ajax_nonce"] = $this.find(".ajaxnonceplu").attr("id").replace("ajaxnonceplu", "");
					pconfig["multipart_params"]['topost'] = ThemifyTilesAdmin.post_id;
					if ( $this.data( 'extensions' ) ) {
						pconfig['filters'][0]['extensions'] = $this.data('extensions');
					}

					var uploader = new plupload.Uploader(pconfig);

					uploader.bind('Init', function(up){});

					uploader.init();

					// a file was added in the queue
					uploader.bind('FilesAdded', function(up, files){
						up.refresh();
						up.start();
						self.showLoader('show');
					});

					uploader.bind('Error', function(up, error){
						var $promptError = $('.prompt-box .show-error');
						$('.prompt-box .show-login').hide();
						$promptError.show();
						
						if($promptError.length > 0){
							$promptError.html('<p class="prompt-error">' + error.message + '</p>');
						}
						$(".overlay, .prompt-box").fadeIn(500);
					});

					// a file was uploaded
					uploader.bind('FileUploaded', function(up, file, response) {
						var json = JSON.parse(response['response']), status;
						
						if('200' == response['status'] && !json.error) {
							status = 'done';
						} else {
							status = 'error';
						}
						
						$("#themify_builder_alert").removeClass("busy").addClass(status).delay(800).fadeOut(800, function() {
							$(this).removeClass(status);
						});
						
						if(json.error){
							alert(json.error);
							return;
						}

						var response_file = json.file,
							response_url = json.large_url ? json.large_url : json.url,
							response_id = json.id,
							thumb_url = json.thumb;

						$this.closest('.themify_builder_input').find('.themify-builder-uploader-input').val(response_url).trigger( 'change' )
						.parent().find('.img-placeholder').empty()
						.html($('<img/>', {src: thumb_url, width: 50, height:50}))
						.parent().show();

						// Attach image id to the input
						$this.closest('.themify_builder_input').find('.themify-builder-uploader-input-attach-id').val(response_id);

					});

					$this.removeClass('plupload-clone');
 
				});
			}
		},

		showLoader: function(stats) {
			if(stats == 'show'){
				$('#themify_builder_alert').addClass('busy').show();
			}
			else if(stats == 'spinhide'){
				$("#themify_builder_alert").delay(800).fadeOut(800, function() {
					$(this).removeClass('busy');
				});
			}
			else{
				$("#themify_builder_alert").removeClass("busy").addClass('done').delay(800).fadeOut(800, function() {
					$(this).removeClass('done');
				});
			}
		},

		retrieve_data : function(){
			var options = {};

			$('#themify_tiles_lightbox_parent .tfb_lb_option').each(function(iterate){
				var option_value,
					this_option_id = $(this).attr('id');

				if ( $(this).hasClass('tfb_lb_wp_editor') ){
					if ( typeof tinyMCE !== 'undefined' ) {
						option_value = $(this).is(':hidden') ? tinyMCE.get( this_option_id ).getContent() : switchEditors.wpautop( tinymce.DOM.get( this_option_id ).value );
					} else {
						option_value = $(this).val();
					}
				}
				else if ( $(this).hasClass('themify-checkbox') ) {
					var cselected = [];
					$(this).find('.tf-checkbox:checked').each(function(i){
						cselected.push($(this).val());
					});
					if ( cselected.length > 0 ) {
						option_value = cselected.join('|');
					} else {
						option_value = '|';
					}
				}
				else if ( $(this).hasClass('themify-layout-icon') ) {
					if( $(this).find('.selected').length > 0 ){
						option_value = $(this).find('.selected').attr('id');
					}
					else{
						option_value = $(this).children().first().attr('id');
					}
				}
				else if ( $(this).hasClass('themify-option-query-cat') ) {
					var parent = $(this).parent(),
							single_cat = parent.find('.query_category_single'),
							multiple_cat  = parent.find('.query_category_multiple');

					if( multiple_cat.val() != '' ) {
						option_value = multiple_cat.val() + '|multiple';
					} else {
						option_value = single_cat.val() + '|single';
					}
				}
				else if( $(this).hasClass('themify_builder_row_js_wrapper') ){
					var row_items = [];
					$(this).find('.themify_builder_row').each(function(){
						var temp_rows = {};
						$(this).find('.tfb_lb_option_child').each(function(){
							var option_value_child,
							this_option_id_child = $(this).data('input-id');

							if( $(this).hasClass('tf-radio-choice') ){
								option_value_child = ($(this).find(':checked').length > 0) ? $(this).find(':checked').val() : '';
							}
							else if ($(this).hasClass('tfb_lb_wp_editor')){
								var text_id = $(this).attr('id');
								this_option_id_child = $(this).attr('name');
								if( typeof tinyMCE !== 'undefined' ) {
									option_value_child = $(this).is(':hidden') ? tinyMCE.get( text_id ).getContent() : switchEditors.wpautop( tinymce.DOM.get( text_id ).value );
								} else {
									option_value_child = $(this).val();
								}
							}
							else{
								option_value_child = $(this).val();
							}

							if( option_value_child ) {
								temp_rows[this_option_id_child] = option_value_child;
							}
						});
						row_items.push(temp_rows);
					});
					option_value = row_items;
				}
				else if ( $(this).hasClass('tf-radio-input-container') ) {
					option_value = $(this).find('input[name="'+this_option_id+'"]:checked').val();
				}
				else if ( $(this).hasClass('module-widget-form-container') ) {
					option_value = $(this).find(':input').serializeObject();
				}
				else if ( $(this).is('select, input, textarea') ) {
					option_value = $(this).val();
				}

				if(option_value){
					options[this_option_id] = option_value;
				}
			});

			return options;
		},

		edit_tile : function( e ) {
			current_tile = $( this ).closest( '.tf-tile' );
			var settings = JSON.parse( current_tile.find('.tf-tile-data script').text() );

			ThemifyTiles.openLightbox( $('#themify-tiles-settings').html(), function(){
				$('#themify_tiles_lightbox_parent .tfb_lb_option').each( function(){
					var $this_option = $(this),
						this_option_id = $this_option.attr( 'id' ),
						$check_found_element = (typeof settings[this_option_id] !== 'undefined'),
						$found_element = settings[this_option_id];

					if ( $found_element ){
						if ( $this_option.hasClass('select_menu_field') ){
							if ( !isNaN( $found_element ) ) {
								$this_option.find("option[data-termid='" + $found_element + "']").attr('selected','selected');
							} else {
								$this_option.find("option[value='" + $found_element + "']").attr('selected','selected');
							}
						} else if ( $this_option.is('select') ){
							$this_option.val( $found_element );
						} else if( $this_option.hasClass('themify-builder-uploader-input') ) {
							var img_field = $found_element,
									img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

							if( img_field != '' ){
								$this_option.val(img_field);
								$this_option.parent().find('.img-placeholder').empty().html(img_thumb);
							}
							else{
								$this_option.parent().find('.thumb_preview').hide();
							}

						} else if($this_option.hasClass('themify-option-query-cat')){
							var parent = $this_option.parent(),
									single_cat = parent.find('.query_category_single'),
									multiple_cat  = parent.find('.query_category_multiple'),
									elems = $found_element,
									value = elems.split('|'),
									cat_type = value[1],
									cat_val = value[0];

							multiple_cat.val( cat_val );
							parent.find("option[value='" + cat_val + "']").attr('selected','selected');

						} else if( $this_option.hasClass('themify_builder_row_js_wrapper') ) {
							var row_append = 0;
							if($found_element.length > 0){
								row_append = $found_element.length - 1;
							}

							// add new row
							for (var i = 0; i < row_append; i++) {
								$this_option.parent().find('.add_new a').first().trigger('click');
							}

							$this_option.find('.themify_builder_row').each(function(r){
								$(this).find('.tfb_lb_option_child').each(function(i){
									var $this_option_child = $(this),
									this_option_id_real = $this_option_child.attr('id'),
									this_option_id_child = $this_option_child.hasClass('tfb_lb_wp_editor') ? $this_option_child.attr('name') : $this_option_child.data('input-id'),
									$found_element_child = $found_element[r][''+ this_option_id_child +''];
									
									if( $this_option_child.hasClass('themify-builder-uploader-input') ) {
										var img_field = $found_element_child,
											img_thumb = $('<img/>', {src: img_field, width: 50, height: 50});

										if( img_field != '' && img_field != undefined ){
											$this_option_child.val(img_field);
											$this_option_child.parent().find('.img-placeholder').empty().html(img_thumb).parent().show();
										}
										else{
											$this_option_child.parent().find('.thumb_preview').hide();
										}

									}
									else if( $this_option_child.hasClass('tf-radio-choice') ){
										$this_option_child.find("input[value='" + $found_element_child + "']").attr('checked','checked');  
									}
									else if( $this_option_child.is('input, textarea, select') ){
										$this_option_child.val($found_element_child);
									}

									if ( $this_option_child.hasClass('tfb_lb_wp_editor') && !$this_option_child.hasClass('clone') ) {
										self.initQuickTags(this_option_id_real);
										if ( typeof tinyMCE !== 'undefined' ) {
											self.initNewEditor( this_option_id_real );
										}
									}

								});
							});

						} else if ( $this_option.hasClass('tf-radio-input-container') ){
							$this_option.find("input[value='" + $found_element + "']").attr('checked', 'checked');  
							var selected_group = $this_option.find('input[name="'+this_option_id+'"]:checked').val();

							// has group element enable
							if($this_option.hasClass('tf-option-checkbox-enable')){
								$('.tf-group-element').hide();
								$('.tf-group-element-' + selected_group ).show();
							}

						} else if ( $this_option.is('input, textarea') ){
							$this_option.val( $found_element );
						} else if ( $this_option.hasClass('themify-checkbox') ){
							var cselected = $found_element;
							cselected = cselected.split('|');

							$this_option.find('.tf-checkbox').each(function(){
								if($.inArray($(this).val(), cselected) > -1){
									$(this).prop('checked', true);
								}
								else{
									$(this).prop('checked', false);
								}
							});

						} else if ( $this_option.hasClass('themify-layout-icon') ) {
								$this_option.find('#' + $found_element.trim()).addClass('selected');
						} else { 
							$this_option.html( $found_element );
						}
					}
					else{
						if ( $this_option.hasClass('themify-layout-icon') ){
							$this_option.children().first().addClass('selected');
						}
						else if ( $this_option.hasClass('themify-builder-uploader-input') ) {
							$this_option.parent().find('.thumb_preview').hide();
						}
						else if ( $this_option.hasClass('tf-radio-input-container') ) {
							$this_option.find('input[type="radio"]').first().prop('checked');
							var selected_group = $this_option.find('input[name="'+this_option_id+'"]:checked').val();
							
							// has group element enable
							if($this_option.hasClass('tf-option-checkbox-enable')){
								$('.tf-group-element').hide();
								$('.tf-group-element-' + selected_group ).show();
							}
						}
						else if( $this_option.hasClass('themify_builder_row_js_wrapper') ){
							$this_option.find('.themify_builder_row').each(function(r){
								$(this).find('.tfb_lb_option_child').each(function(i){
									var $this_option_child = $(this),
									this_option_id_real = $this_option_child.attr('id');

									if ( $this_option_child.hasClass('tfb_lb_wp_editor') ) {
										
										var this_option_id_child = $this_option_child.data('input-id');

										self.initQuickTags(this_option_id_real);
										if ( typeof tinyMCE !== 'undefined' ) {
											self.initNewEditor( this_option_id_real );
										}
									}

								});
							});
						}
						else if( $this_option.hasClass('themify-checkbox') /*&& is_settings_exist*/ ) {
							$this_option.find('.tf-checkbox').each(function(){
								$(this).prop('checked', false);
							});
						}
						else if( $this_option.is('input, textarea') /*&& is_settings_exist*/ ) {
							$this_option.val('');
						}
					}
				});
			} );
		},

		/**
		 * Handles the Save button in the tile edit lightbox
		 */
		preview_tile : function(e){
			e.preventDefault();
			ThemifyTiles._add_tile( ThemifyTiles.retrieve_data() );
		},

		_add_tile : function( options ) {
			$.ajax({
				type : 'POST',
				url : ajaxurl,
				data : {
					action : 'tf_preview_tile',
					tf_tile : options,
					tf_post_id : ThemifyTilesAdmin.post_id,
					tile_id : current_tile ? current_tile.data( 'tile_id' ) : ''
				},
				success : function( result ) {
					if( current_tile ) {
						current_tile.replaceWith( result );
					} else {
						$( '#themify-tiles' ).find( '.tf-tiles-edit-wrap' ).append( result );
					}
					ThemifyTiles.closeLightbox();
					$body.trigger( 'tf_tiles_update' );
				}
			});
		},

		get_tile_data : function( $tile ) {
			return $tile.find( '.tf-tile-data script' ).text();
		},

		/**
		 * Saving tiles for a post
		 */
		save_tiles : function(e, success_callback){
			e.preventDefault();
			var $this = $( this ),
				container = $( this ).closest( '.tf-tiles' ),
				tiles = [];

			if( $this.hasClass( 'saving' ) ) return;
			$this.addClass( 'saving' );

			container.find( '.tf-tiles-edit-wrap .tf-tile' ).each(function(){
				tiles.push( ThemifyTiles.get_tile_data( $( this ) ) );
			});

			$.ajax({
				type : 'POST',
				url : ajaxurl,
				data : {
					action : 'tf_save_tiles',
					tf_post_id : container.data( 'post_id' ),
					tf_data : tiles
				},
				success : function( result ){
					$this.removeClass( 'saving' );
					if( typeof success_callback == 'function' ) {
						success_callback();
					}
					$( '#publish' ).click();
				}
			});
		},

		enable_editor : function() {
			$( ".tf-tiles-edit-wrap", '#themify-tiles' ).sortable({
				placeholder: 'themify_builder_ui_state_highlight',
				cursor: 'move',
				handle: '.themify_builder_module_front_overlay, .themify_builder_sub_row_top',
				connectWith: '.tf-tile-edit-wrap',
				revert: 100,
				// helper: function() {
					// return $('<div class="themify_builder_sortable_helper"/>');
				// }
			});

			$body.trigger( 'tf_tiles_edit' );
		}
	};

	ThemifyTiles.init();

});