<?php

class Themify_Portfolio_Post {

	/**
	 * Path to the plugin's system directory */
	var $dir;
	var $url;
	var $version;

	public function __construct( $args ) {
		$this->dir = trailingslashit( $args['dir'] );
		$this->url = trailingslashit( $args['url'] );
		$this->version = $args['version'];
		$this->actions();
	}

	public function actions() {
		add_action( 'init', array( $this, 'register' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		include( $this->dir . 'includes/functions.php' );
		add_filter( 'builder_is_portfolio_active', '__return_true' );
		add_action( 'after_setup_theme', array( $this, 'admin' ), 100 );

		// compatibility mode: let the theme handle everything
		if( THEMIFY_PORTFOLIO_POSTS_COMPAT_MODE == true ) {
			return;
		}

		add_action( 'after_setup_theme', array( $this, 'load_dependencies' ), 15 );
		add_action( 'init', array( $this, 'init' ) );
	}

	public function admin() {
		if( is_admin() ) {
			include $this->dir . 'includes/admin.php';
			new Themify_Portfolio_Posts_Admin();
		}
	}

	/**
	 * Registers all frontend related functionalities of portfolios
	 *
	 * @since 1.0.8
	 */
	function init() {
		if ( ! shortcode_exists( 'themify_portfolio_posts' ) ) {
			add_shortcode( 'themify_portfolio_posts', array( $this, 'shortcode' ) );
		}
		add_filter( 'the_content', array( $this, 'default_template' ) );
	}

	/**
	 * Load external libraries required by the plugin
	 *
	 * @since 1.0
	 */
	public function load_dependencies() {
		defined( 'THEMIFY_METABOX_DIR' ) || define( 'THEMIFY_METABOX_DIR', $this->dir . '/includes/themify-metabox/' );
		defined( 'THEMIFY_METABOX_URI' ) || define( 'THEMIFY_METABOX_URI', $this->url . '/includes/themify-metabox/' );
		include_once( $this->dir . 'includes/themify-metabox/themify-metabox.php' );
		require_once( $this->dir . 'includes/themify/img.php' );
	}

	/**
	 * Register post type and taxonomy
	 */
	function register() {
		$slugs = apply_filters( 'themify_portfolio_post_rewrite', array('post'=>'project','tax'=>'portfolio-category') );
		$cpt = array(
			'plural' => __( 'Portfolios', 'themify-portfolio-posts' ),
			'singular' => __( 'Portfolio', 'themify-portfolio-posts' ),
			'rewrite' => empty($slugs['post']) ? apply_filters( 'themify_portfolio_rewrite', 'project' ) : $slugs['post'],
		);
		$post_type = array(
			'labels' => array(
				'name' => $cpt['plural'],
				'singular_name' => $cpt['singular']
			),
			'supports' => isset( $cpt['supports'] )? $cpt['supports'] : array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt','author' ),
			'hierarchical' => false,
			'has_archive' => true,
			'public' => true,
			'rewrite' => array( 'slug' => $cpt['rewrite'], 'with_front' => false ),
			'query_var' => true,
			'can_export' => true,
			'capability_type' => 'post',
			'menu_icon' => 'dashicons-portfolio',
		);
		/**
		 * Filter post type parameters sent to register_post_type()
		 *
		 * @param $post_type array
		 */
		$post_type = apply_filters( 'themify_portfolio_post_args', $post_type );
		register_post_type( 'portfolio', $post_type );
		update_option( '__portfolio_slug', $cpt['rewrite'] ) && flush_rewrite_rules();

		register_taxonomy( 'portfolio-category', array( 'portfolio' ), array(
			'labels' => array(
				'name' => sprintf( __( '%s Categories', 'themify-portfolio-posts' ), $cpt['singular'] ),
				'singular_name' => sprintf( __( '%s Category', 'themify-portfolio-posts' ), $cpt['singular'] )
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => true,
			'rewrite' => array( 'slug' => empty($slugs['tax']) ? apply_filters( 'themify_portfolio_rewrite', 'portfolio-category' ) : $slugs['tax'], 'with_front' => false ),
			'query_var' => true
		));
	}

	/**
	 * Default display of Portfolio posts: the meta fields are appended to the_content
	 *
	 * @return string
	 * @since 1.0.8
	 */
	public function default_template( $content ) {
		global $post;

		if ( $post->post_type === 'portfolio' ) {
			$content = $this->get_template( 'default.php', compact( 'content' ) );
		}

		return $content;
	}

	/**
	 * "themify_portfolio_posts" shortcode callback
	 *
	 * @return string_id
	 */
	public function shortcode( $atts, $content = '' ) {
		$atts = shortcode_atts( array(
			'id' => '',
			'title' => 'yes',
			'unlink_title' => 'no',
			'image' => 'yes', // no
			'unlink_image' => 'no',
			'image_w' => 290,
			'image_h' => 290,
			'display' => 'excerpt', // excerpt, content
			'post_meta' => 'yes', // no
			'post_date' => 'yes', // no
			'more_text' => __( 'More &rarr;', 'themify-portfolio-posts' ),
			'limit' => 4,
			'category' => '', // integer category ID
			'order' => 'DESC', // ASC
			'orderby' => 'date', // title, rand
			'style' => 'grid4', // grid4, grid3, grid2
			'paged' => '0', // internal use for pagination, dev: previously was 1
			'use_original_dimensions' => 'no', // yes
			'filter' => 'no', // entry filter
			'pagination' => 'yes',
		), $atts, 'themify_portfolio_posts' );
		extract( $atts );

		// Pagination
		global $paged;
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		// Parameters to get posts
		$args = array(
			'post_type' => 'portfolio',
			'posts_per_page' => $limit,
			'order' => $order,
			'orderby' => $orderby,
			'suppress_filters' => false,
			'paged' => $paged
		);

		// Category parameters
		if ( ! empty( $category ) ) {
			$args['tax_query'] = $this->parse_category_args( $category );
		}

		// Get posts according to parameters
		$query = new WP_Query();
		$posts = $query->query( apply_filters( 'themify_portfolio_posts_query', $args, $atts ) );

		$output = '';
		if( $query ) {
			$output = $this->get_template( 'shortcode.php', compact( 'query', 'posts', 'atts' ) );
		}

		return $output;
	}

	/**
	 * Parses the arguments given as category to see if they are category IDs or slugs and returns a proper tax_query
	 * @param $category
	 * @return array
	 */
	function parse_category_args( $category ) {
		$tax_query = array();
		$taxonomy = 'portfolio-category';
		if ( '0' !== $category ) {
			$category = array_map( 'trim', explode( ',', $category ) );
			function themify_callback_ids_in($a){return is_numeric( $a ) && "-" !== $a[0];}
			$ids_in = @array_filter( $category, 'themify_callback_ids_in' );

			function themify_callback_ids_not_in($a){return is_numeric( $a ) && "-" === $a[0];}
			$ids_not_in = @array_filter( $category, 'themify_callback_ids_not_in' );

			function themify_callback_slugs_in($a){return ! is_numeric( $a ) && "-" !== $a[0];}
			$slugs_in = @array_filter( $category, 'themify_callback_slugs_in' );

			function themify_callback_slugs_not_in($a){return ! is_numeric( $a ) && "-" === $a[0];}
			$slugs_not_in = @array_filter( $category, 'themify_callback_slugs_not_in' );

			$tax_query = array(
				'relation' => 'AND'
			);
			if ( ! empty( $ids_in ) ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field' => 'id',
					'terms' => $ids_in
				);
			}
			if ( ! empty( $ids_not_in ) ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field' => 'id',
					'terms' => array_map( 'abs', $ids_not_in ),
					'operator' => 'NOT IN'
				);
			}
			if ( ! empty( $slugs_in ) ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => $slugs_in
				);
			}
			if ( ! empty( $slugs_not_in ) ) {
				function themify_callback_tax_query($a){return substr( $a, 1 );}
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => array_map( 'themify_callback_tax_query', $slugs_not_in ), // remove the minus sign (first character)
					'operator' => 'NOT IN'
				);
			}
		}

		return $tax_query;
	}

	function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( __FILE__ ) == $file ) {
			$row_meta = array(
			  'changelogs'    => '<a href="' . esc_url( 'https://themify.me/changelogs/' ) . basename( dirname( $file ) ) .'.txt" target="_blank" aria-label="' . esc_attr__( 'Plugin Changelogs', 'themify-portfolio-post' ) . '">' . esc_html__( 'View Changelogs', 'themify-portfolio-post' ) . '</a>'
			);
	 
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	/**
	 * Loads a template file and returns the output
	 *
	 * @return string|bool
	 */
	public function get_template( $name, $args = array() ) {
		extract( $args );
		if( $path = $this->locate_template( $name ) ) {
			ob_start();
			include $path;
			return ob_get_clean();
		}

		return false;
	}

	/**
	 * Locates a template file. Searches within <theme>/themify-portfolio-posts directory first,
	 * then within the plugin's /templates.
	 *
	 * @return string|bool path to the template file, false on fail
	 */
	public function locate_template( $name ) {
		if( locate_template( 'themify-portfolio-posts/' . $name ) ) {
			return locate_template( 'themify-portfolio-posts/' . $name );
		} elseif( file_exists( $this->dir . 'templates/' . $name ) ) {
			return $this->dir . 'templates/' . $name;
		}

		return false;
	}
}
