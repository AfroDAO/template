<?php

class Themify_Tiles {

	private static $instance = null;
	var $mobile_breakpoint = 768;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return	A single instance of this class.
	 */
	public static function get_instance() {
		return null == self::$instance ? self::$instance = new self : self::$instance;
	}

	private function __construct() {
		add_action( 'after_setup_theme', array( $this, 'load_themify_metabox' ) );
		add_action( 'init', array( $this, 'load_themify_library' ), 1 );
		add_action( 'init', array( $this, 'builder_dependencies' ), 2 );
		add_action( 'init', array( $this, 'i18n' ), 5 );
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 13 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'wp_head', array( $this, 'dynamic_css' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_shortcode( 'themify_tiles', array( $this, 'shortcode' ) );
		add_action( 'themify_do_metaboxes', array( $this, 'themify_do_metaboxes' ) );
		add_filter( 'themify_specific_post_types', array( $this, 'themify_specific_post_types' ) );

		if( is_admin() ) {
			include( THEMIFY_TILES_DIR . 'includes/admin.php' );
			add_action( 'wp_ajax_tf_preview_tile', array( $this, 'ajax_preview_tile' ) );
			add_action( 'wp_ajax_tf_get_tiles_edit', array( $this, 'ajax_get_tiles_edit' ) );
			add_action( 'wp_ajax_tf_save_tiles', array( $this, 'ajax_save_tiles' ) );
			add_action( 'wp_ajax_tf_clear_tiles', array( $this, 'ajax_clear_tiles' ) );
			add_action( 'wp_ajax_themify_tiles_plupload_action', array($this, 'builder_plupload'), 10 );
		}
	}

	function load_themify_metabox() {
		include( THEMIFY_TILES_DIR . 'includes/themify-metabox/themify-metabox.php' );
	}

	function themify_specific_post_types( $types ) {
		$types[] = 'themify_tile';
		return $types;
	}

	/**
	 * Setup Themify library if its not already loaded
	 */
	public function load_themify_library() {
		if( ! defined( 'THEMIFY_DIR' ) ) {
			define( 'THEMIFY_VERSION', '2.0.9' );
			define( 'THEMIFY_DIR', THEMIFY_TILES_DIR . '/includes/themify' );
			define( 'THEMIFY_URI', THEMIFY_TILES_URI . '/includes/themify' );
			include( THEMIFY_DIR . '/themify-database.php' );
			include( THEMIFY_DIR . '/themify-utils.php' );
			include( THEMIFY_DIR . '/themify-wpajax.php' );

			if ( ! class_exists( 'Themify_Mobile_Detect' ) ) {
				require_once THEMIFY_DIR . '/class-themify-mobile-detect.php';
				global $themify_mobile_detect;
				$themify_mobile_detect = new Themify_Mobile_Detect;
			}
		}
	}

	public function builder_dependencies() {
		if( ! function_exists( 'themify_builder_module_settings_field' ) ) {
			include( THEMIFY_TILES_DIR . 'includes/themify-builder/includes/themify-builder-options.php' );
			include( THEMIFY_TILES_DIR . 'includes/theme-options.php' );
		}
	}

	public function i18n() {
		load_plugin_textdomain( 'themify-tiles', false, THEMIFY_TILES_DIR . 'languages/' );
	}

	function register_post_type() {
		$labels = array(
			'name'               => _x( 'Tiles Group', 'post type general name', 'themify-tiles' ),
			'singular_name'      => _x( 'Tile Group', 'post type singular name', 'themify-tiles' ),
			'menu_name'          => _x( 'Themify Tiles', 'admin menu', 'themify-tiles' ),
			'name_admin_bar'     => _x( 'Tile Group', 'add new on admin bar', 'themify-tiles' ),
			'add_new'            => _x( 'Add New', 'book', 'themify-tiles' ),
			'add_new_item'       => __( 'Add New Tile Group', 'themify-tiles' ),
			'new_item'           => __( 'New Tile Group', 'themify-tiles' ),
			'edit_item'          => __( 'Edit Tile Group', 'themify-tiles' ),
			'all_items'          => __( 'Manage Tiles', 'themify-tiles' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'book' ),
			'capability_type'    => 'post',
			'menu_position'      => 80, /* below Settings */
			'has_archive'        => false,
			'supports'           => array( 'title' ),
			'register_meta_box_cb' => array( $this, 'admin_metabox' )
		);

		register_post_type( 'themify_tile', $args );
	}

	public function admin_metabox( $post ) {
		add_meta_box(
			'themify-tiles',
			__( 'Tiles', 'themify-tiles' ),
			array( $this, 'tiles_metabox' ),
			'themify_tile',
			'normal'
		);
	}

	public function tiles_metabox( $post ) {
		echo $this->load_view( 'tiles-edit.php', array(
			'post_id' => $post->ID,
			'data' => $this->get_tiles_data( $post->ID )
		) );
	}

	public function resposive_metabox( $post ) {
		echo $this->load_view( 'edit.php', array(
			'post_id' => $post->ID,
			'data' => $this->get_tiles_data( $post->ID )
		) );
	}

	public function shortcode( $atts, $content = '' ) {
		$output = '';
		if( isset( $atts['group'] ) ) {
			if( ! is_numeric( $atts['group'] ) ) {
				global $wpdb;
				$atts['group'] = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s AND post_status = 'publish'", $atts['group'], 'themify_tile' ) );
			}
			$post = get_post( $atts['group'] );
			if( $post && $post->post_type == 'themify_tile' ) {
				$output .= $this->render_tiles( $atts['group'] );
				ob_start();
				edit_post_link( __( 'Edit this Tile Group', 'themify-tiles' ), '<p style="clear: both;">', '</p>', $atts['group'] );
				$output .= ob_get_clean();
			}
		}

		return apply_filters( 'themify_tiles_output', $output, $atts );
	}

	function is_admin_screen() {
		global $hook_suffix, $post;
		if( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && $post->post_type == 'themify_tile' ) {
			return true;
		}
		return false;
	}

	public function render_tiles( $group_id ) {
		$post = get_post( $group_id );
		$output = '';
		if( $post && $post->post_type == 'themify_tile' ) {
			$data = $this->get_tiles_data( $group_id );
			$template = 'template-tiles.php';
			$output .= $this->load_view( $template, array(
				'data' => $data,
				'gutter' => get_post_meta( $group_id, 'tf_tiles_gutter', true ),
				'fluid_tiles' => get_post_meta( $group_id, 'tf_tiles_fluid_tiles', true ),
				'post_id' => $group_id
			) );
		}

		return $output;
	}

	/**
	 * Get the saved tiles data for a post
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function get_tiles_data( $post_id = null ) {
		if( ! $post_id )
			$post_id = get_the_ID();

		return apply_filters( 'themify_tiles_get_tiles', get_post_meta( $post_id, '_themify_tiles', true ), $post_id );
	}

	/**
	 * Get the physical path to a view file
	 *
	 * @return string template path, false if fails
	 * @since 1.0
	 */
	public function get_view_path( $name ) {
		if( locate_template( 'themify-tiles/' . $name ) ) {
			return locate_template( 'themify-tiles/' . $name );
		} elseif( file_exists( THEMIFY_TILES_DIR . 'views/' . $name ) ) {
			return THEMIFY_TILES_DIR . 'views/' . $name;
		}

		return false;
	}

	public function load_view( $name, $data = array() ) {
		extract( $data );
		if( $view = $this->get_view_path( $name ) ) {
			ob_start();
			include( $view );
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Queue the necessary assets to render the tiles on front end
	 *
	 * @since 1.0
	 */
	public function enqueue() {
		$options = get_option( 'themify_tiles' );
		$key = isset( $options['google_maps_key'] ) ? '&key=' . $options['google_maps_key'] : '';

		// assets shared with Builder
		wp_enqueue_script( 'themify-smartresize', THEMIFY_TILES_URI . 'assets/jquery.smartresize.js', array( 'jquery' ), THEMIFY_TILES_VERSION, true );
		wp_enqueue_script( 'themify-widegallery', THEMIFY_TILES_URI . 'assets/themify.widegallery.js', array( 'jquery', 'jquery-masonry' ), THEMIFY_TILES_VERSION, true );
		wp_enqueue_style( 'themify-animate', THEMIFY_TILES_URI . 'includes/themify-builder/css/animate.min.css', array(), THEMIFY_TILES_VERSION );

		if ( ! wp_script_is( 'themify-carousel-js' ) ) {
			wp_enqueue_script( 'themify-carousel-js', THEMIFY_URI . '/js/carousel.js', array('jquery') ); // grab from themify framework
		}
		wp_register_script( 'themify-builder-map-script', themify_https_esc( 'http://maps.google.com/maps/api/js' ) . '?sensor=false' . $key, array(), false, true );

		wp_enqueue_style( 'themify-tiles', THEMIFY_TILES_URI . 'assets/style.css', null, THEMIFY_TILES_VERSION );

		wp_enqueue_style( 'themify-font-icons-css', THEMIFY_URI . '/fontawesome/css/font-awesome.min.css', array(), THEMIFY_TILES_VERSION );

		wp_enqueue_style ( 'magnific', THEMIFY_URI . '/css/lightbox.css', array(), THEMIFY_VERSION );
		wp_enqueue_script( 'magnific', THEMIFY_URI . '/js/lightbox.js', array( 'jquery' ), THEMIFY_VERSION, true );

		if ( ! wp_script_is( 'themify-gallery' ) && ! themify_is_themify_theme() ) {
			wp_enqueue_script( 'themify-gallery', THEMIFY_URI . '/js/themify.gallery.js', array( 'jquery' ), false, true );

			//Inject variable values in gallery script
			wp_localize_script( 'themify-gallery', 'themifyScript', array(
					'lightbox' => themify_lightbox_vars_init(),
					'lightboxContext' => apply_filters( 'themify_lightbox_context', 'body' ),
					'isTouch' => themify_is_touch()? 'true': 'false',
				)
			);
		}

		wp_enqueue_script( 'themify-tiles', THEMIFY_TILES_URI . 'assets/script.js', array( 'jquery', 'jquery-masonry' ), THEMIFY_TILES_VERSION, true );
		wp_localize_script( 'themify-tiles', 'ThemifyTiles', apply_filters( 'themify_tiles_script_vars', array(
			'ajax_nonce'	=> wp_create_nonce('ajax_nonce'),
			'ajax_url'		=> admin_url( 'admin-ajax.php' ),
			'networkError'	=> __('Unknown network error. Please try again later.', 'themify'),
			'termSeparator'	=> ', ',
			'galleryFadeSpeed' => '300',
			'galleryEvent' => 'click',
			'transition_duration' => 750,
			'isOriginLeft' => is_rtl() ? 0 : 1,
			'fluid_tiles' => 'yes',
			'fluid_tile_rules' => array(
				array( 'query' => 'screen and (max-width: 600px)', 'size' => '2' ),
				array( 'query' => 'screen and (min-width: 601px) and (max-width: 1001px)', 'size' => '4' ),
				array( 'query' => 'screen and (min-width: 1001px)', 'size' => '5' ),
				array( 'query' => 'screen and (min-width: 1501px)', 'size' => '6' ),
			)
		) ) );
	}

	/**
	 * Queue the necessary assets for the tiles editor
	 *
	 * @since 1.0
	 */
	public function admin_enqueue() {
		global $post;

		if( ! $this->is_admin_screen() )
			return;

		themify_enqueue_scripts( 'post-new.php' );
		/* load assets for front end, needed for preview */
		$this->enqueue();
		/* add the CSS codes to set the tile sizes */
		$this->dynamic_css();

		wp_enqueue_media();

		// assets borrowed from Builder & framework
		wp_enqueue_style( 'themify-colorpicker', THEMIFY_URI . '/css/jquery.minicolors.css' );
		wp_enqueue_script( 'themify-colorpicker', THEMIFY_URI . '/js/jquery.minicolors.js', array( 'jquery' ) );
		wp_enqueue_script( 'themify-font-icons-js', THEMIFY_URI . '/js/themify.font-icons-select.js', array( 'jquery' ) );
		wp_localize_script('themify-font-icons-js', 'themifyIconPicker', array(
			'icons_list' => THEMIFY_URI . '/fontawesome/list.html',
		));
		add_action( 'admin_footer', 'themify_font_icons_dialog' );
		wp_enqueue_style( 'themify-builder-main', THEMIFY_TILES_URI . 'includes/themify-builder/css/themify-builder-main.css', array() );
		wp_enqueue_style( 'themify-builder-admin-ui', THEMIFY_TILES_URI . 'includes/themify-builder/css/themify-builder-admin-ui.css', array() );
		wp_enqueue_script( 'themify-plupload', THEMIFY_URI . '/js/plupload.js', array('jquery', 'themify-scripts'), false);
		wp_register_script( 'gallery-shortcode', THEMIFY_URI . '/js/gallery-shortcode.js', array( 'jquery', 'themify-scripts' ), false, true );
		wp_enqueue_script( 'themify-builder-map-script' );

		wp_enqueue_style( 'themify-tiles-admin', THEMIFY_TILES_URI . 'assets/admin.css' );
		wp_enqueue_script( 'themify-tiles-admin', THEMIFY_TILES_URI . 'assets/admin.js', array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-sortable', 'jquery-ui-tabs', 'plupload-all' ), THEMIFY_TILES_VERSION, true );
		wp_localize_script( 'themify-tiles-admin', 'ThemifyTilesAdmin', array(
			'post_id' => $post->ID
		) );
		wp_localize_script( 'themify-tiles-admin', 'themify_builder_plupload_init', $this->get_builder_plupload_init() );

		/* Script files to load only if Builder is not loaded */
		if( ! wp_script_is( 'themify-builder-front-ui-js' ) ) {
			wp_enqueue_script( 'themify-tiles-builder-compat', THEMIFY_TILES_URI . 'assets/builder-compat.js', array( 'jquery' ) );
		}

		wp_enqueue_style( 'themify-icons', THEMIFY_URI . '/themify-icons/themify-icons.css', array(), THEMIFY_TILES_VERSION );
	}

	public function admin_footer() {
		if( ! $this->is_admin_screen() )
			return;

		if( class_exists('TC_admin_init') ) {
			remove_filter('tiny_mce_before_init', array(TC_admin_init::$instance,'tc_user_defined_tinymce_css'));
		}
		echo '<script type="text/html" id="themify-tiles-settings">';
		$options = include( $this->get_view_path( 'config.php' ) );
		themify_builder_module_settings_field( $options['options'], '' );
		echo '<div id="tf-tiles-save-settings"><a href="#" class="builder_button">'. __( 'Save', 'themify-tiles' ) .'</a></div>';
		echo '</script>';
	}

	public function ajax_preview_tile() {
		if( isset( $_POST['tf_tile'] ) ) {
			$data = stripslashes_deep( (array) $_POST['tf_tile'] );
			$post_id = $_POST['tf_post_id'];
			echo $this->load_view( 'tile-single.php', array(
				'mod_settings' => $data,
				'module_ID' => 'tf-tile-' . $post_id . '-' . uniqid(),
			) );
		}

		die;
	}

	public function ajax_save_tiles() {
		if( isset( $_POST['tf_post_id'] ) ) {
			$post_id = $_POST['tf_post_id'];
			$tiles_data = $_POST['tf_data'];
			$tiles_data = array_map( 'stripcslashes', $tiles_data );
			$tiles_data = array_map( 'json_decode', $tiles_data );

			update_post_meta( $post_id, '_themify_tiles', $tiles_data );

			echo '1';
		}

		die;
	}

	public function get_tile_sizes() {
		return apply_filters( 'builder_tiles_sizes', array(
			'square-large' => array( 'label' => __( 'Square Large', 'themify-tiles' ), 'width' => 480, 'height' => 480, 'mobile_width' => 280, 'mobile_height' => 280, 'image' => THEMIFY_TILES_URI . 'assets/size-sl.png' ),
			'square-small' => array( 'label' => __( 'Square Small', 'themify-tiles' ), 'width' => 240, 'height' => 240, 'mobile_width' => 140, 'mobile_height' => 140, 'image' => THEMIFY_TILES_URI . 'assets/size-ss.png' ),
			'landscape' => array( 'label' => __( 'Landscape', 'themify-tiles' ), 'width' => 480, 'height' => 240, 'mobile_width' => 280, 'mobile_height' => 140, 'image' => THEMIFY_TILES_URI . 'assets/size-l.png' ),
			'portrait' => array( 'label' => __( 'Portrait', 'themify-tiles' ), 'width' => 240, 'height' => 480, 'mobile_width' => 140, 'mobile_height' => 280, 'image' => THEMIFY_TILES_URI . 'assets/size-p.png' ),
		) );
	}

	public function dynamic_css() {
		$css = '';
		foreach( $this->get_tile_sizes() as $key => $size ) {
			$css .= sprintf( '
			.tf-tile.size-%1$s,
			.tf-tile.size-%1$s .map-container {
				width: %2$spx;
				height: %3$spx;
				max-width: 100%%;
			}',
				$key,
				$size['width'],
				$size['height'],
				$size['mobile_width'],
				$size['mobile_height']
			);
		}
		echo sprintf( '<style>%s</style>', $css );
	}

	/**
	 * Get RGBA color format from hex color
	 *
	 * @return string
	 */
	function get_rgba_color( $color ) {
		$color = explode( '_', $color );
		$opacity = isset( $color[1] ) ? $color[1] : '1';
		return 'rgba(' . $this->hex2rgb( $color[0] ) . ', ' . $opacity . ')';
	}

	/**
	 * Converts color in hexadecimal format to RGB format.
	 *
	 * @since 1.9.6
	 *
	 * @param string $hex Color in hexadecimal format.
	 * @return string Color in RGB components separated by comma.
	 */
	function hex2rgb( $hex ) {
		$hex = str_replace( "#", "", $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		return implode( ',', array( $r, $g, $b ) );
	}

	/**
	 * Get images from gallery shortcode
	 * @return object
	 */
	function get_images_from_gallery_shortcode( $shortcode ) {
		preg_match( '/\[gallery.*ids=.(.*).\]/', $shortcode, $ids );
		$image_ids = explode( ",", $ids[1] );
		$orderby = $this->get_gallery_param_option( $shortcode, 'orderby' );
		$orderby = $orderby != '' ? $orderby : 'post__in';
		$order = $this->get_gallery_param_option( $shortcode, 'order' );
		$order = $order != '' ? $order : 'ASC';

		// Check if post has more than one image in gallery
		return get_posts( array(
			'post__in' => $image_ids,
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'numberposts' => -1,
			'orderby' => $orderby,
			'order' => $order
		) );
	}

	/**
	 * Get gallery shortcode options
	 * @param $shortcode
	 * @param $param
	 */
	function get_gallery_param_option( $shortcode, $param = 'link' ) {
		if ( $param == 'link' ) {
			preg_match( '/\[gallery .*?(?=link)link=.([^\']+)./si', $shortcode, $out );
		} elseif ( $param == 'order' ) {
			preg_match( '/\[gallery .*?(?=order)order=.([^\']+)./si', $shortcode, $out );	
		} elseif ( $param == 'orderby' ) {
			preg_match( '/\[gallery .*?(?=orderby)orderby=.([^\']+)./si', $shortcode, $out );	
		} elseif ( $param == 'columns' ) {
			preg_match( '/\[gallery .*?(?=columns)columns=.([^\']+)./si', $shortcode, $out );	
		}
		
		$out = isset($out[1]) ? explode( '"', $out[1] ) : array('');
		return $out[0];
	}

	/**
	 * Get initialization parameters for plupload. Filtered through themify_tiles_plupload_init_vars.
	 * @return mixed|void
	 * @since 1.4.2
	 */
	function get_builder_plupload_init() {
		return apply_filters( 'themify_tiles_plupload_init_vars', array(
			'runtimes'				=> 'html5,flash,silverlight,html4',
			'browse_button'			=> 'themify-builder-plupload-browse-button', // adjusted by uploader
			'container' 			=> 'themify-builder-plupload-upload-ui', // adjusted by uploader
			'drop_element' 			=> 'drag-drop-area', // adjusted by uploader
			'file_data_name' 		=> 'async-upload', // adjusted by uploader
			'multiple_queues' 		=> true,
			'max_file_size' 		=> wp_max_upload_size() . 'b',
			'url' 					=> admin_url('admin-ajax.php'),
			'flash_swf_url' 		=> includes_url('js/plupload/plupload.flash.swf'),
			'silverlight_xap_url' 	=> includes_url('js/plupload/plupload.silverlight.xap'),
			'filters' 				=> array( array(
				'title' => __( 'Allowed Files', 'themify-tiles' ),
				'extensions' => 'jpg,jpeg,gif,png,zip,txt'
			)),
			'multipart' 			=> true,
			'urlstream_upload' 		=> true,
			'multi_selection' 		=> false, // added by uploader
			 // additional post data to send to our ajax hook
			'multipart_params' 		=> array(
				'_ajax_nonce' 		=> '', // added by uploader
				'action' 			=> 'themify_tiles_plupload_action', // the ajax action name
				'imgid' 			=> 0 // added by uploader
			)
		));
	}

	/**
	 * Plupload ajax action
	 */
	function builder_plupload() {

		$imgid = $_POST['imgid'];

		/** If post ID is set, uploaded image will be attached to it. @var String */
		$postid = $_POST['topost'];

		/** Handle file upload storing file|url|type. @var Array */
		$file = wp_handle_upload($_FILES[$imgid . 'async-upload'], array('test_form' => true, 'action' => 'themify_tiles_plupload_action'));

		//let's see if it's an image, a zip file or something else
		$ext = explode('/', $file['type']);

		// Import routines
		
		// Insert into Media Library
		// Set up options array to add this file as an attachment
		$attachment = array(
			'post_mime_type' => sanitize_mime_type($file['type']),
			'post_title' => str_replace('-', ' ', sanitize_file_name(pathinfo($file['file'], PATHINFO_FILENAME))),
			'post_status' => 'inherit'
		);

		if ($postid)
			$attach_id = wp_insert_attachment($attachment, $file['file'], $postid);

		// Common attachment procedures
		require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata($attach_id, $file['file']);
		wp_update_attachment_metadata($attach_id, $attach_data);

		if ($postid) {
			$large = wp_get_attachment_image_src($attach_id, 'large');
			$thumb = wp_get_attachment_image_src($attach_id, 'thumbnail');

			//Return URL for the image field in meta box
			$file['large_url'] = $large[0];
			$file['thumb'] = $thumb[0];
			$file['id'] = $attach_id;
		}
		

		$file['type'] = $ext[1];
		// send the uploaded file url in response
		echo json_encode($file);
		exit;
	}

	function themify_do_metaboxes( $panels ) {
		$options = array(
			array(
			"name" => "tf_tiles_fluid_tiles",
			"title" => __('Fluid Tiles', 'themify-tiles'),
			"description" => __( "If enabled, tiles will display fluid in % width (eg. small tile will be 25% width)", 'themify-tiles' ),
			"type" => "dropdown",
			"meta" => array(
				array("value" => 'yes', 'name' => __('Enable', 'themify-tiles')),
				array("value" => 'no', 'name' => __('Disable', 'themify-tiles'))
			)
			),
			array(
				"name" => "tf_tiles_gutter",
				"title" => __('Tile Spacing', 'themify-tiles'),
				"description" => "",
				"type" => "textbox",
				"meta" => array( "size"=>"small"),
				'after' => ' px'
			),
			array(
				'type' => 'function',
				'name' => 'tf_tiles_shortcode',
				'title' => __('Shortcode', 'themify-tiles'),
				'function' => array( $this, 'display_shortcode' ),
			),
		);
		$panels[] = array(
			'name' => __( 'Tile Group Options', 'themify-tiles' ),
			'id' => 'tf-tiles',
			'options' => $options,
			'pages' => 'themify_tile'
		);

		return $panels;
	}

	function display_shortcode( $meta_box ) {
		global $hook_suffix, $post;

		if( 'post.php' == $hook_suffix ) {
			echo __( 'To display this tile group you can use this shortcode:', 'themify-tiles' );
			echo '<br/><code>[themify_tiles group="'. $post->ID .'"]</code>';
			echo '<br/><code>[themify_tiles group="'. $post->post_name .'"]</code>';
		} else {
			_e( 'Please save the Tile Group first.', 'themify-tiles' );
		}
	}
}