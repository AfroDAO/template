(function ($) {
	var selector = '.module-pro-image';

	function flip_effect( el, side ) {
		side = side || ( el.hasClass( 'image-pro-flip' ) ? 'front' : 'back' );

		el.toggleClass( 'image-pro-flip', side === 'back' );
		window.setTimeout( function(){ el.toggleClass( 'image-pro-flipped', side === 'back' ); }, 1000 );
	}

	$( 'body' )
		.on( 'mouseenter touchstart', selector, function(){
			var $this = $( this ),
				entranceEffect = $this.data( 'entrance-effect' );

			$(selector).not( $this )
				.filter( function( index, element ) { return $( this ).data( 'image-enter' ) } )
				.trigger( 'mouseleave' );

			$this.data( 'image-enter', true );

			if ( ['flip-horizontal', 'flip-vertical'].includes( entranceEffect ) ) {
				flip_effect( $this, 'back' );
			} else if( entranceEffect === 'none' ) {
				$this.find( '.image-pro-overlay' )
					.css( 'visibility', 'visible' )
					.addClass( entranceEffect );
			} else {
				$this.find( '.image-pro-overlay' )
					.css( { visibility: 'visible', 'animation-name' : '' } )
					.removeClass( $this.data( 'exit-effect' ) )
					.addClass( 'wow animated ' + entranceEffect );
			}
		} )
		.on( 'mouseleave', selector, function(ev){
			var $this = $( this ),
				entranceEffect = $this.data( 'entrance-effect' );

			$this.data( 'image-enter', false );

			if( $('.mfp-wrap').length ) {
				setTimeout( function() {
					$this.find( '.image-pro-overlay' )
						.hide()
						.removeAttr( 'style' )
						.removeClass( 'wow ' + entranceEffect );
				}, 1200 );

				return false;
			}

			if( ['flip-horizontal', 'flip-vertical'].includes( entranceEffect ) ) {
				flip_effect( $this, 'front' );
			} else if( entranceEffect === 'none' ) {
				$this.find( '.image-pro-overlay' )
					.css( 'visibility', 'visible' )
					.addClass( entranceEffect );
			} else {
				$this.find( '.image-pro-overlay' )
					.css( { 'animation-name' : '' } )
					.removeClass( entranceEffect )
					.addClass( 'wow animated ' + $this.data( 'exit-effect' ) );
			}
		} )
		// There is no 'touchleave' event, so we have to emulate it
		// checking if a touch is started out of our area of interest
		.on( 'touchstart', function( e ) {
			$( e.target ).closest( selector ).length && $( selector ).trigger( 'mouseleave' );
		} );

		$( selector ).each( function() {
			var $this = $( this );

			if( $this.data( 'entrance-effect' ) === 'partial-overlay' ) {
				$this.one( 'webkitAnimationEnd oanimationend msAnimationEnd animationend', function() {
					$this.find( '.image-pro-overlay' ).css( 'visibility', 'visible' );
				} );
				
				! $this.hasClass( 'wow' ) && $this.trigger( 'animationend' );
			}
		} );

	function builder_image_pro_init(e, el, type ) {
		if ('undefined' !== typeof $.fn.magnificPopup) {
			var items = $( selector, el );

			items = el && el.hasClass( 'module-pro-image' ) ? items.add( el ) : items;
			items.find( '.themify_lightbox' ).length && Themify.InitGallery();
		}
	}

        
        if ( Themify.is_builder_active ) {
           $( 'body' ).on( 'builder_load_module_partial',builder_image_pro_init ).on( 'click', '.module-pro-image a',function( e ) {
                    ! $( this ).is( '.themify_lightbox[target="_blank"]' ) && e.preventDefault();
            } );
            if(Themify.is_builder_loaded){
                builder_image_pro_init();
            }
        }
        else{
            builder_image_pro_init();
        }
	
}( jQuery ) );