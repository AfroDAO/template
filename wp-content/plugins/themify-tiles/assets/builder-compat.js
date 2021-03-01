var ThemifyPageBuilder;
(function($, window, document, undefined) {

	ThemifyPageBuilder = {
		openGallery: function() {
			var clone = wp.media.gallery.shortcode,
				file_frame;
			
			$( 'body' ).on('click', '.tf-gallery-btn', function( event ){
				var shortcode_val = $(this).closest('.themify_builder_input').find('.tf-shortcode-input');
				
				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					frame:     'post',
					state:     'gallery-edit',
					title:     wp.media.view.l10n.editGalleryTitle,
					editing:   true,
					multiple:  true,
					selection: false
				});

				wp.media.gallery.shortcode = function(attachments) {
					var props = attachments.props.toJSON(),
					attrs = _.pick( props, 'orderby', 'order' );

					if ( attachments.gallery )
						_.extend( attrs, attachments.gallery.toJSON() );

					attrs.ids = attachments.pluck('id');

					// Copy the `uploadedTo` post ID.
					if ( props.uploadedTo )
						attrs.id = props.uploadedTo;

					// Check if the gallery is randomly ordered.
					if ( attrs._orderbyRandom )
						attrs.orderby = 'rand';
					delete attrs._orderbyRandom;

					// If the `ids` attribute is set and `orderby` attribute
					// is the default value, clear it for cleaner output.
					if ( attrs.ids && 'post__in' === attrs.orderby )
						delete attrs.orderby;

					// Remove default attributes from the shortcode.
					_.each( wp.media.gallery.defaults, function( value, key ) {
						if ( value === attrs[ key ] )
							delete attrs[ key ];
					});

					var shortcode = new wp.shortcode({
						tag:    'gallery',
						attrs:  attrs,
						type:   'single'
					});

					shortcode_val.val(shortcode.string());

					wp.media.gallery.shortcode = clone;
					return shortcode;
				}

				file_frame.on( 'update', function( selection ) {
					var shortcode = wp.media.gallery.shortcode( selection ).string().slice( 1, -1 );
					shortcode_val.val('[' + shortcode + ']');
				});
			
				if($.trim(shortcode_val.val()).length > 0) {
					file_frame = wp.media.gallery.edit($.trim(shortcode_val.val()));
					file_frame.state('gallery-edit').on( 'update', function( selection ) {
						var shortcode = wp.media.gallery.shortcode( selection ).string().slice( 1, -1 );
						shortcode_val.val('[' + shortcode + ']');
					});
				} else {
					file_frame.open();
					$('.media-menu').find('.media-menu-item').last().trigger('click');
				}
				event.preventDefault();
			});
			
		},

		mediaUploader: function() {

			// Field Uploader
			$( 'body' ).on('click', '.themify-builder-media-uploader', function( event ){
				var $el = $(this);

				var libraryType = $el.data('library-type')? $el.data('library-type') : 'image';

				var file_frame = wp.media.frames.file_frame = wp.media({
					title: $(this).data('uploader-title'),
					library: {
						type: libraryType
					},
					button: {
						text: $(this).data('uploader-button-text')
					},
					multiple: false  // Set to true to allow multiple files to be selected
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					var attachment = file_frame.state().get('selection').first().toJSON();

					// Do something with attachment.id and/or attachment.url here
					$el.closest('.themify_builder_input').find('.themify-builder-uploader-input').val(attachment.url).trigger( 'change' )
					.parent().find('.img-placeholder').empty()
					.html($('<img/>', {src: attachment.url, width: 50, height:50}))
					.parent().show();

					// Attached id to input
					$el.closest('.themify_builder_input').find('.themify-builder-uploader-input-attach-id').val(attachment.id);
				});

				// Finally, open the modal
				file_frame.open();
				event.preventDefault();
			});

			// delete button
			$( 'body' ).on('click', '.themify-builder-delete-thumb', function(e){
				$(this).prev().empty().parent().hide();
				$(this).parents('.themify_builder_input').find('.themify-builder-uploader-input').val('');
				e.preventDefault();
			});

			// Media Buttons
			$( 'body' ).on('click', '.insert-media', function(e) {
				window.wpActiveEditor = $(this).data('editor');
			});
		}
	}

}(jQuery, window, document));