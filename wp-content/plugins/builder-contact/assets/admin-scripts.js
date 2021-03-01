( function( $ ) {
	'use strict';
	$(function(){
		var contactFormBuilder = function( selector ) {
			this.$table = $( selector );
			this.init();
		};

		contactFormBuilder.prototype = {
			init: function() {
				this.loadExtraFields();
				this.loadOrders();
				this.makeSortable();
				this.events();

			},

			loadOrders: function() {

				var _this = this;
				var sorted = this.$table.find( 'tbody tr' ).sort(function(a,b){
					var name1 = $( a ).find( '.tb_lb_option' ).attr( 'id' ),
						name2 = $( b ).find( '.tb_lb_option' ).attr( 'id' ),
						data;

					name1 = undefined === name1 ? $( a ).find( '.tb-new-field-textbox' ).val() : name1;
					name2 = undefined === name2 ? $( b ).find( '.tb-new-field-textbox' ).val() : name2;
					try {
						data = JSON.parse($('#field_order').val());
					} catch(e) {}

					if ( ! data ) {
						data = {};
					}
					return data[name1] - data[name2];
				});


				$.each(sorted, function(idx, itm) {
					_this.$table.find( 'tbody' ).append(itm);
				});
			},

			loadExtraFields: function() {
				var _this = this,
					data,
					$row = $( '.tb-new-field-row' );
				try {
					data = JSON.parse($('#field_extra').val());
				} catch(e) {}
				if ( ! data ) {
					data = { fields: [] };
				}

				if( $('#tmpl-builder-contact-new-field').length ) {
					var template = $('#tmpl-builder-contact-new-field').html();
				}else{
					var template = $('#themify_builder_site_canvas_iframe').contents()
						.find('#tmpl-builder-contact-new-field').html();
				}
				_.templateSettings = {
					evaluate:    /<#([\s\S]+?)#>/g,
					interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
					escape:      /\{\{([^\}]+?)\}\}(?!\})/g,
				};

				_.each( data.fields, function( field, index ) {

					var fieldTemplate = _.template( template, {variable: 'data'} );

					field.index = ++index;
					data.id = _this.$table.find( '.tb-contact-new-row' ).length++;
					data.field = field;
					var $newField = $( fieldTemplate( data ) );
					$newField.insertBefore( $row );
				});
			},

			events: function() {
				var _this = this;
				this.$table
					.on( 'click', '.tb-new-field-action', function( e ) {
						e.preventDefault();
						_this.addField( this );
					})
					.on( 'change', 'input[name*="tb-new-field-type-"]', function() {
						_this.switchField( this );
					})
					.on( 'click', '.tb-add-field-option', function( e ) {
						e.preventDefault();
						$( this ).siblings('ul').append('<li><input type="text" class="tb-multi-option"><a href="#" class="tb-contact-value-remove"><i class="ti ti-close"></i></a></li>')
					})
					.on( 'change', '.tb-multi-option', function(){
						_this.setNames();
					})
					.on( 'keyup', '.tb-contact-new-row input[type="text"], .tb-contact-new-row textarea', function(){
						_this.changeObject();
					})
					.on( 'change', '.tb-contact-new-row .tb-new-field-required', function(){
						_this.changeObject();
					})
					.on( 'click', '.tb-contact-value-remove', function(e){
						e.preventDefault();
						$( this ).closest('li').remove();
						_this.changeObject();
					})
					.on( 'click', '.tb-contact-field-remove', function(e){
						e.preventDefault();
						$( this ).closest('tr').remove();
						_this.changeObject();
					})
			},

			setNames: function() {
				this.$table.find('.tb-multi-option').each(function( index ){
					var name = $( this ).closest('tr').find('.tb-new-field-textbox').val();
					name = name.toLowerCase();
					name = name.replace(/ /g,'_');
					$( this )
						.attr( 'name', name + '_' + index )
						.attr( 'id', name + '_' + index );
				})
			},

			makeSortable: function() {
				var _this = this;

				this.$table.find( 'tbody' ).sortable( {
					items: 'tr:not(.tb-new-field-row)',
					placeholder: "ui-state-highlight",
					update: function() {
						_this.changeObject();
						_this.sort();
					}
				} ).disableSelection();
			},

			sort: function(){
				var newOrder = {};

				$( '.contact_fields tr' ).each(function(){
					var name = $( this ).find( '.tb_lb_option' ).attr( 'id' );
					if( ! name ){
						name = $( this ).find( '.tb-new-field-textbox' ).val()
					}
					newOrder[name] = $( this ).index();
				});

				$( '#field_order' ).val( JSON.stringify( newOrder ) );
				Themify.triggerEvent( $( '#field_order' ).get(0), 'keyup' );
			},

			addField: function( el ) {

				var $el = $( el ),
					data = {},
					row = $el.closest( 'tr' ),
					tbody = $el.closest( 'tbody' );

				if( $('#tmpl-builder-contact-new-field').length ) {
					var template = $('#tmpl-builder-contact-new-field').html();
				}else{
					var template = $('#themify_builder_site_canvas_iframe').contents()
						.find('#tmpl-builder-contact-new-field').html();
				}

				_.templateSettings = {
					evaluate:    /<#([\s\S]+?)#>/g,
					interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
					escape:      /\{\{([^\}]+?)\}\}(?!\})/g,
				};
				var fieldTemplate = _.template( template, {variable: 'data'} );

				data.id = tbody.find( '.tb-contact-new-row' ).length++;
				var $newField = $( fieldTemplate( data ) );
				$newField.insertBefore( row );

				$newField.find('input[name*="tb-new-field-type-"]:checked').trigger('keyup');

				tbody.sortable( 'refresh' );
				this.changeObject();
			},

			switchField: function( el ) {
				var type = $( el ).val();

				$( el ).closest( '.tb-new-field-type' ).next().find('.control-input').html( this.fieldUI( type ) );
				if( 'static' === type ){
					$( el ).closest( '.tb-new-field-type' ).next().find('.tb-new-field-required').parent().css('display', 'none');
				}else{
					$( el ).closest( '.tb-new-field-type' ).next().find('.tb-new-field-required').parent().css('display', '');

				}
			},

			fieldUI: function( type, value ) {
				var input;

				switch( type ) {
					case 'text':
					case 'textarea':
						input = $( '<input type="text" placeholder="Placeholder" class="tb-new-field-value tb-field-type-' + type + '">' );
						break;
					case 'static':
						input = $( '<textarea placeholder="Enter text or HTML here" class="tb-new-field-value tb-field-type-' + type + '"></textarea>' );
						break;
					case 'radio':
					case 'select':
					case 'checkbox':
						if( $('#tmpl-builder-contact-new-field-options').length ){
							var template = $('#tmpl-builder-contact-new-field-options').html();
						}else{
							var template = $('#themify_builder_site_canvas_iframe').contents()
								.find('#tmpl-builder-contact-new-field-options').html();
						}

						_.templateSettings = {
							evaluate:    /<#([\s\S]+?)#>/g,
							interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
							escape:      /\{\{([^\}]+?)\}\}(?!\})/g,
						};
						var optionsTemplate = _.template( template, {variable: 'data'} );
						if( ! value ){
							value = {
								type: type,
								fields:['']
							}
						}
						input = $( optionsTemplate( value ) );
						break;
				}
				this.changeObject();

				return input;
			},

			changeObject: function() {
				var object = { fields: [] };

				this.$table.find( '.tb-contact-new-row' ).each(function(){
					var $this = $( this ),
						type = $this.find( 'input[name*="tb-new-field-type-"]:checked' ).val(),
						order = $this.index(),
						label = $this.find( '.tb-new-field-textbox' ).val(),
						required = $this.find( '.tb-new-field-required' ).is(':checked'),
						value;
					switch( type ) {
						case 'text':
						case 'textarea':
						case 'static':
							value = $this.find( '.tb-new-field-value' ).val();
							break;
						case 'radio':
						case 'select':
						case 'checkbox':
							value = [];
							$this.find( '.tb-multi-option' ).each(function(){
								value.push( $( this ).val() )
							});
							break;
					}
					var field = {
						order: order,
						type: type,
						label: label,
						value: value,
						required: required
					};
					object.fields.push( field );
				});
				this.$table.find('#field_extra').val( JSON.stringify( object ) );
				Themify.triggerEvent( this.$table.find('#field_extra').get(0), 'keyup' );
				this.sort();

			}
		};

		$( 'body' ).on( 'editing_module_option', function() {
			setTimeout(function(){
				new contactFormBuilder( '.contact_fields' );
			},10)
		} );
	} )

} )( jQuery );