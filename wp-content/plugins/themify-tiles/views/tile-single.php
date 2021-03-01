<?php
/**
 * @var $data
 * @var $post_id
 */

$fields_default = array(
	'size' => 'square-large',
	'flip_effect' => 'flip-left',
	'color_front' => '', 'color_back' => '',
	'title_front' => '', 'title_back' => '',
	'type_front' => 'text', 'type_back' => 'text',
	'text_front' => '', 'text_back' => '',
	'button_link_front' => '', 'button_link_back' => '',
	'button_param_front' => array(), 'button_param_back' => array(),
	'icon_type_front' => 'icon', 'icon_type_back' => 'icon',
	'icon_front' => '', 'icon_back' => '',
	'image_front' => '', 'image_back' => '',
	'icon_color_front' => '', 'icon_color_back' => '',
	'background_repeat_front' => '', 'background_repeat_back' => '',
	'gallery_front' => '', 'gallery_back' => '',
	'gallery_autoplay_front' => 'off', 'gallery_autoplay_back' => 'off',
	'gallery_hide_timer_front' => 'no', 'gallery_hide_timer_back' => 'no',
	'address_map_front' => '', 'address_map_back' => '',
	'type_map_front' => 'ROADMAP', 'type_map_back' => 'ROADMAP',
	'zoom_map_front' => 8, 'zoom_map_back' => 8,
	'scrollwheel_map_front' => 'enable', 'scrollwheel_map_back' => 'enable',
	'draggable_map_front' => 'enable', 'draggable_map_back' => 'enable',
	'action_text_front' => '', 'action_text_back' => '',
	'action_link_front' => '', 'action_link_back' => '',
	'action_param_front' => array(), 'action_param_back' => array(),
	'tile_autoflip' => '0',
	'background_color_front' => '', 'background_color_back' => '',
	'text_color_front' => '', 'text_color_back' => '',
	'link_color_front' => '', 'link_color_back' => '',
	'background_image_front' => '', 'background_image_back' => '',
);

$args = wp_parse_args( $mod_settings, $fields_default );

foreach( array( 'front', 'back' ) as $side ) {
	if( isset( $mod_settings["button_link_params_{$side}"] ) )
		$args["button_link_params_{$side}"] = explode( '|', $mod_settings["button_link_params_{$side}"] );
	if( isset( $mod_settings["action_param_{$side}"] ) )
		$args["action_param_{$side}"] = explode( '|', $mod_settings["action_param_{$side}"] );
}

extract( $args, EXTR_SKIP );

$tile_sizes = $this->get_tile_sizes();
$tile_size = $tile_sizes[$size];

$class = 'has-flip';
if( ( $type_back == 'blank' )
	|| ( $type_back == 'text' && $text_back == '' && $background_image_back == '' && $title_back == '' ) // enable flip on back text tiles with no text but have background image
	|| ( $type_back == 'button' && $title_back == '' && $image_back == '' && $icon_back == '' )
	|| ( $type_back == 'gallery' && $gallery_back == '' )
	|| ( $type_back == 'map' && $address_map_back == '' )
) {
	$class = 'no-flip';
}

$container_class = implode( ' ', array( 'tf-tile', 'size-' . $size, $flip_effect, $class, 'tile-type-front-' . $type_front ) );

$out_effect = array(
	'flip-horizontal' => '',
	'flip-vertical' => '',
	'fadeInUp' => 'fadeOutDown',
	'fadeIn' => 'fadeOut',
	'fadeInLeft' => 'fadeOutLeft',
	'fadeInRight' => 'fadeOutRight',
	'fadeInDown' => 'fadeOutUp',
	'zoomInUp' => 'zoomOutDown',
	'zoomInLeft' => 'zoomOutLeft',
	'zoomInRight' => 'zoomOutRight',
	'zoomInDown' => 'zoomOutUp',
);

$flip_button_enabled = apply_filters( 'builder_tiles_enable_flip_button', themify_is_touch() );
?>
<div id="<?php echo $module_ID; ?>" class="<?php echo $container_class; ?>" data-auto-flip="<?php echo $tile_autoflip; ?>" data-in-effect="<?php echo $flip_effect ?>" data-out-effect="<?php echo $out_effect[$flip_effect]; ?>">

	<div class="tile-flip-box-wrap"><div class="tile-flip-box">

	<?php foreach( array( 'front', 'back' ) as $side ) : ?>

		<?php if( $args['type_' . $side] == 'blank' ) continue; ?>

		<style>
		#<?php echo $module_ID; ?> .tile-<?php echo $side; ?> {
			<?php
			echo ( '' != $args['background_color_' . $side] ) ? 'background-color: ' . $this->get_rgba_color( $args['background_color_' . $side] ) . '; ' : '';
			echo ( '' != $args['text_color_' . $side] ) ? 'color: ' . $this->get_rgba_color( $args['text_color_' . $side] ) . '; ' : '';
			if( '' != $args['background_image_' . $side] ) {
				$image = themify_do_img( esc_url( $args['background_image_' . $side] ), $tile_size['width'], $tile_size['height'] );
				echo 'background-image: url("' . esc_url( $image['url'] ) . '"); ';
			}
			?>
		}
		#<?php echo $module_ID; ?> .tile-<?php echo $side; ?> a {
			<?php echo ( '' != $args['link_color_' . $side] ) ? 'color: ' . $this->get_rgba_color( $args['link_color_' . $side] ) . '; ' : ''; ?>
		}
		</style>

		<div class="tile-<?php echo $side; ?> tile-type-<?php echo $args['type_' . $side]; ?> ui <?php echo $args['color_' . $side]; ?>">
			<div class="tile-inner">

				<?php
				/**
				 * Text tile type
				 */
				if( $args['type_' . $side] == 'text' ) : ?>

					<?php if( $args['title_' . $side] != '' ) : ?><h4 class="tile-title"><?php echo $args['title_' . $side] ?></h4><?php endif; ?>
					<div class="tile-content">
						<?php echo apply_filters( 'themify_builder_module_content', $args['text_' . $side] ); ?>
					</div><!-- .tile-content -->

				<?php
				/**
				 * Button tile type
				 */
				elseif( $args['type_' . $side] == 'button' ) : ?>
					<?php
						$url = in_array( 'lightbox', $args["button_link_params_{$side}"] ) ? themify_get_lightbox_iframe_link( $args["button_link_{$side}"] ) : $args["button_link_{$side}"];
						$class = in_array( 'lightbox', $args["button_link_params_{$side}"] ) ? ' themify_lightbox' : '';
						$target = in_array( 'newtab', $args["button_link_params_{$side}"] ) ? ' target="_blank"' : '';
						$icon_style = ( '' != $args["icon_color_{$side}"] ) ? 'color: ' . $this->get_rgba_color( $args["icon_color_{$side}"] ) : '';
					?>
					<?php if( $args["button_link_{$side}"] != '' ) printf( '<a href="%s" class="%s" %s>', $url, $class, $target ); ?>
						<?php if( '' != $args["title_{$side}"] ) : ?><h4 class="tile-title"><?php echo $args["title_{$side}"]; ?></h4><?php endif; ?>

						<?php if( $args["icon_type_{$side}"] == 'icon' && $args["icon_{$side}"] != '' ) : ?>
							<span class="tile-icon fa <?php echo themify_get_fa_icon_classname( $args["icon_{$side}"] ); ?>" style="<?php echo $icon_style; ?>"></span>
						<?php elseif( $args["icon_type_{$side}"] == 'image' && $args["image_{$side}"] != '' ) : ?>
							<img src="<?php echo $args["image_{$side}"]; ?>" alt="<?php echo $args['title_' . $side]; ?>" class="tile-icon" />
						<?php endif; ?>
					<?php if( $args["button_link_{$side}"] != '' ) echo '</a>'; ?>

				<?php
				/**
				 * Gallery tile type
				 */
				elseif( $args['type_' . $side] == 'gallery' ) : ?>

					<?php $images = $this->get_images_from_gallery_shortcode( $args["gallery_{$side}"] );
					$returned_items = count( $images );
					if( $images ) : ?>
						<div class="gallery-shortcode-wrap twg-wrap twg-gallery-shortcode <?php echo ( 'yes' == $args["gallery_hide_timer_{$side}"] ) ? 'no-timer' : 'with-timer'; ?>" data-bgmode="cover">
							<div class="gallery-image-holder twg-holder">

								<div class="twg-loading themify-loading"></div>

								<div class="gallery-info twg-info">
									<div class="gallery-caption twg-caption">

									</div>
									<!-- /gallery-caption -->
								</div>
							</div>

							<div id="gallery-shortcode-slider-<?php echo $module_ID; ?>" class="gallery-slider-wrap twg-controls">

								<div class="gallery-slider-timer">
									<div class="timer-bar"></div>
								</div>
								<!-- /gallery-slider-timer -->

								<ul class="gallery-slider-thumbs slideshow twg-list" data-id="gallery-shortcode-slider-<?php echo $module_ID; ?>" data-autoplay="<?php echo $args["gallery_autoplay_{$side}"]; ?>" data-effect="scroll" data-speed="1000" data-visible="<?php echo $returned_items <= 12 ? $returned_items - 1: '12' ?>" data-width="33" data-wrap="yes" data-slidernav="yes" data-pager="no">
								<?php foreach ( $images as $image ) :
									$full = wp_get_attachment_image_src( $image->ID, apply_filters( 'themify_gallery_shortcode_full_size', 'large' ) );
									$caption = $image->post_excerpt == '' ? $image->post_content : $image->post_excerpt;
									$description = $image->post_content == '' ? $image->post_excerpt : $image->post_content;
									$alt = ( $alt_text = get_post_meta( $image->ID, '_wp_attachment_image_alt', true ) ) ? $alt_text : $image->post_name;
									$thumbnail = themify_do_img( $full[0], 40, 33 );
									?>
									<li class="twg-item">
										<a href="#" data-image="<?php echo $full[0]; ?>" data-caption="<?php echo $caption; ?>" data-description="<?php echo $description; ?>" class="twg-link">
											<img src="<?php echo $thumbnail['url']; ?>" width="<?php echo $thumbnail['width']; ?>" height="<?php echo $thumbnail['height']; ?>" alt="<?php echo $alt; ?>" />
										</a>
									</li>
								<?php endforeach; // images as image ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

				<?php
				/**
				 * Map tile type
				 */
				elseif( $args['type_' . $side] == 'map' ) : ?>
					<?php
					wp_enqueue_script( 'themify-builder-map-script' );
					$args["address_map_{$side}"] = preg_replace( '/\s+/', ' ', trim( $args["address_map_{$side}"] ) );
					?>

					<?php $num = rand( 0,10000 ); ?>
						<script type="text/javascript"> 
							jQuery(function(){
							Themify_Tiles.init_map("<?php echo $args["address_map_{$side}"]; ?>", <?php echo $num; ?>, <?php echo esc_js( $args["zoom_map_{$side}"] ); ?>, "<?php echo $args["type_map_{$side}"]; ?>", <?php echo $args["scrollwheel_map_{$side}"] != 'enable' ? 'false' : 'true' ; ?>, <?php echo $args["draggable_map_{$side}"] != 'enable' ? 'false' : 'true' ; ?>);
							});
						</script>
						<div id="themify_map_canvas_<?php echo $num; ?>" style="" class="map-container">&nbsp;</div>

				<?php endif; ?>

				<?php if( ( $args["type_{$side}"] == 'text' || $args["type_{$side}"] == 'gallery' ) && '' != $args["action_link_{$side}"] ) : ?>
					<?php
						$url = in_array( 'lightbox', $args["action_param_{$side}"] ) ? themify_get_lightbox_iframe_link( $args["action_link_{$side}"] ) : $args["action_link_{$side}"];
						$class = in_array( 'lightbox', $args["action_param_{$side}"] ) ? ' themify_lightbox' : '';
						$target = in_array( 'newtab', $args["action_param_{$side}"] ) ? ' target="_blank"' : '';
					?>
					<a href="<?php echo $url; ?>" class="action-button <?php echo $class; ?>" <?php echo $target; ?>><span></span> <?php echo $args["action_text_{$side}"]; ?></a>
					<?php if( isset( $args["link_color_{$side}"] ) ) printf( '<style> #%s .action-button span { border-color: %s; } </style>', $module_ID, $this->get_rgba_color( $args["link_color_{$side}"] ) ); ?>
				<?php endif; // action button check ?>

				<?php if( $flip_button_enabled ) : ?>
					<a href="#" class="tile-flip-back-button"></a>
				<?php endif; ?>

			</div><!-- .tile-inner -->

		</div><!-- .tile-<?php echo $side; ?> -->

	<?php endforeach; ?>

	</div><!-- .tile-flip-box --></div><!-- .tile-flip-box-wrap -->

	<div class="tf-tile-data">
		<script type="text/json"><?php echo json_encode( $mod_settings ); ?></script>
	</div>

</div><!-- .tf-tile -->