<?php
/**
 * Class Single_Infinite.
 * Routines to execute infinite scroll on single views.
 *
 * @since 1.0.0
 */
class Themify_Single_Infinite {

	/**
	 * Post types where SI is enabled.
	 *
	 * @var array
	 * @access private
	 */
	private $post_types = array();

	/**
	 * Class constructor.
	 *
	 * @param array $post_types
	 * @access public
	 */
	function __construct( $post_types = array( 'post' ) ) {
		$this->post_types = $post_types;

		// Elements in top frame
		add_action( 'wp_ajax_themify_theme_fetch_ad', array( $this, 'fetch_ad' ) );
		add_action( 'wp_ajax_nopriv_themify_theme_fetch_ad', array( $this, 'fetch_ad' ) );

		add_action( 'template_redirect', array( $this, 'ajax_response' ), 5 );
		add_action( 'themify_single_infinite_response', array( $this, 'get_previous_entry' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * If we're in a singular view of one of the expected post types, prepare SI.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function enqueue_assets() {
		if ( is_singular( $this->post_types ) ) {
			wp_enqueue_script( 'themify-single-infinite', THEME_URI . '/js/single-infinite.js', array( 'wp-util' ) );
			wp_localize_script( 'themify-single-infinite', 'themifySI', apply_filters( 'themify_single_infinite_js_vars', array(
				'ajax_nonce' => wp_create_nonce( 'ajax_nonce_si' ),
				'ajax_url'   => $this->ajax_url(),
				'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
				'post_id'    => get_the_ID(),
				'base_post_id' => get_the_ID(),
				'loading' 	 => THEME_URI . '/images/loading.gif',
				'manual'		 => themify_theme_is_single_infinite_manual(),
				'texts'		 => array(
					'load_more' => esc_html__( 'Load More', 'themify' ),
				),
				'styles' => array(),
				'scripts' => array(),
				'js_templates' => array(),
				'back_top' => '<a class="infinite-back-top" href="#header"><span>' . esc_html__( 'Back to top', 'themify' ) . '</span></a>',
			) ) );
			add_action( 'wp_footer', array( $this, 'js_templates' ) );
			// Core prints footer scripts at priority 20, but just in case we'll wait a bit.
			add_action( 'wp_footer', array( $this, 'output_existing_assets' ), 99 );
			add_filter( 'themify_single_infinite_results', array( $this, 'include_assets_in_response' ), 10 );
		}
	}

	/**
	 * Output required JS templates.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function js_templates() {
		if ( trim( themify_get( 'setting-single_ad_code' ) ) ) :
			?>
			<script id="tmpl-themify_ad" type="text/html">
				<div class="single-divider-ad">
					{{{ data.ad_code }}}
				</div>
			</script>
			<?php
		endif;
	}

	/**
	 * Catches AJAX request for ad code. Sends JSON encoded response.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function fetch_ad() {
		check_ajax_referer( 'ajax_nonce_si' );
		$ad = trim( themify_get( 'setting-single_ad_code' ) );
		if ( ! empty( $ad ) ) {
			wp_send_json_success( array(
				'ad_code' => $ad,
			) );
		}
		wp_send_json_error( esc_html__( 'Error getting ad code.', 'themify' ) );
	}

	/**
	 * Returns Ajax URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @uses home_url, add_query_arg, apply_filters
	 * @return string
	 */
	function ajax_url() {
		return add_query_arg( array( 'themify_si' => 'loading' ), set_url_scheme( home_url( '/' ) ) );
	}

	/**
	 * Is this request a Themify Single Infinite load?
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	function is_loading() {
		return isset( $_GET[ 'themify_si' ] );
	}

	/**
	 * Custom Ajax response so it doesn't call admin-ajax which sets is_admin() to true.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function ajax_response() {
		if ( ! $this->is_loading() ) {
			return false;
		}

		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		send_nosniff_header();

		/**
		 * 10 - Themify_Single_Infinite::get_previous_entry
		 */
		do_action( 'themify_single_infinite_response' );

		die( '0' );
	}

	/**
	 * Processes AJAX request for previous entry URL. Sends JSON encoded response.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function get_previous_entry() {
		check_ajax_referer( 'ajax_nonce_si' );
		if ( ! isset( $_POST['post_id'] ) || ! is_numeric( $_POST['post_id'] ) ) {
			wp_send_json_error( esc_html__( 'Invalid or missing post id.', 'themify' ) );
		}

		global $post;
		$post = get_post( $_POST['post_id'] );
		$post = apply_filters( 'themify_infinite_get_previous_entry', get_adjacent_post(), $post );

		if ( ! is_a( $post, 'WP_Post' ) ) 
			wp_send_json_error( esc_html__( 'Error getting previous post.', 'themify' ) );

		/////////////
		$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'] = new WP_Query( array(
			'p' => $post->ID,
		) );
		setup_postdata( $post );

		global $themify;
//        $themify = new Themify();
        $themify->template_redirect();

        $results = array(
			'previous_post_id'  => $post->ID,
			'previous_post_url' => esc_url( set_url_scheme( get_permalink( $post->ID ) ) ),
		);

		if ( have_posts() ) {
			// Fire wp_head to ensure that all necessary scripts are enqueued. Output isn't used, but scripts are extracted in self::output_existing_assets.
			ob_start();
			wp_head();
			while ( ob_get_length() ) {
				ob_end_clean();
			}

			$results['type'] = 'success';

			ob_start();

			do_action( 'themify_single_infinite_render_start' );

			while( have_posts() ) {
				the_post();
				get_template_part( 'includes/content-single' );
			}

			do_action( 'themify_single_infinite_render_end' );

			$results['html'] = ob_get_clean();

			// Fire wp_footer to ensure that all necessary scripts are enqueued. Output isn't used, but scripts are extracted in self::output_existing_assets.
			ob_start();
			wp_footer();
			while ( ob_get_length() ) {
				ob_end_clean();
			}

		} else {
			$results['type'] = 'empty';
		}
		/////////////

		if ( is_object( $post ) ) {
			wp_send_json_success( apply_filters( 'themify_single_infinite_results', $results ) );
		}
		wp_send_json_error( esc_html__( 'Error getting previous post.', 'themify' ) );
	}

	/**
	 * Provide SI with a list of the scripts and stylesheets already present on the page.
	 * Since posts may contain require additional assets that haven't been loaded, this data will be used to track the additional assets.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @global $wp_scripts, $wp_styles
	 * @action wp_footer
	 * @return string
	 */
	function output_existing_assets() {
		global $wp_scripts, $wp_styles;

		$scripts = is_a( $wp_scripts, 'WP_Scripts' ) ? $wp_scripts->done : array();
		$scripts = apply_filters( 'themify_single_infinite_existing_scripts', $scripts );

		$styles = is_a( $wp_styles, 'WP_Styles' ) ? $wp_styles->done : array();
		$styles = apply_filters( 'themify_single_infinite_existing_stylesheets', $styles );

		?><script type="text/javascript">
			jQuery.extend( themifySI.scripts, <?php echo json_encode( $scripts ); ?> );
			jQuery.extend( themifySI.styles, <?php echo json_encode( $styles ); ?> );
		</script><?php
	}

	/**
	 * Identify additional scripts required by the latest set of SI posts and provide the necessary data to the SI response handler.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @global $wp_scripts
	 * @return array
	 * @param array $results
	 * @uses   sanitize_text_field, add_query_arg
	 * @filter infinite_scroll_results
	 */
	function include_assets_in_response( $results ) {
		// Don't bother unless there are posts to display
		if ( 'success' != $results['type'] ) {
			return $results;
		}

		// Parse and sanitize the script handles already output
		$initial_scripts = isset( $_REQUEST['scripts'] ) && is_array( $_REQUEST['scripts'] ) ? array_map( 'sanitize_text_field', $_REQUEST['scripts'] ) : false;

		if ( is_array( $initial_scripts ) ) {
			global $wp_scripts;

			// Identify new scripts needed by the latest set of SI posts
			$new_scripts = array_diff( $wp_scripts->done, $initial_scripts );

			// If new scripts are needed, extract relevant data from $wp_scripts
			if ( ! empty( $new_scripts ) ) {
				$results['scripts'] = array();

				foreach ( $new_scripts as $handle ) {
					// Abort if somehow the handle doesn't correspond to a registered script
					if ( ! isset( $wp_scripts->registered[ $handle ] ) )
						continue;

					// Provide basic script data
					$script_data = array(
						'handle'     => $handle,
						'footer'     => ( is_array( $wp_scripts->in_footer ) && in_array( $handle, $wp_scripts->in_footer ) ),
						'extra_data' => $wp_scripts->print_extra_script( $handle, false )
					);

					// Base source
					$src = $wp_scripts->registered[ $handle ]->src;

					// Take base_url into account
					if ( strpos( $src, 'http' ) !== 0 )
						$src = $wp_scripts->base_url . $src;

					// Version and additional arguments
					if ( null === $wp_scripts->registered[ $handle ]->ver )
						$ver = '';
					else
						$ver = $wp_scripts->registered[ $handle ]->ver ? $wp_scripts->registered[ $handle ]->ver : $wp_scripts->default_version;

					if ( isset( $wp_scripts->args[ $handle ] ) )
						$ver = $ver ? $ver . '&amp;' . $wp_scripts->args[$handle] : $wp_scripts->args[$handle];

					// Full script source with version info
					$script_data['src'] = add_query_arg( 'ver', $ver, $src );

					// Add script to data that will be returned to SI JS
					array_push( $results['scripts'], $script_data );
				}
			}
		}

		// Expose additional script data to filters, but only include in final `$results` array if needed.
		if ( ! isset( $results['scripts'] ) )
			$results['scripts'] = array();

		$results['scripts'] = apply_filters( 'themify_single_infinite_additional_scripts', $results['scripts'], $initial_scripts, $results );

		if ( empty( $results['scripts'] ) )
			unset( $results['scripts' ] );

		// Parse and sanitize the style handles already output
		$initial_styles = isset( $_REQUEST['styles'] ) && is_array( $_REQUEST['styles'] ) ? array_map( 'sanitize_text_field', $_REQUEST['styles'] ) : false;

		if ( is_array( $initial_styles ) ) {
			global $wp_styles;

			// Identify new styles needed by the latest set of SI posts
			$new_styles = array_diff( $wp_styles->done, $initial_styles );

			// If new styles are needed, extract relevant data from $wp_styles
			if ( ! empty( $new_styles ) ) {
				$results['styles'] = array();

				foreach ( $new_styles as $handle ) {
					// Abort if somehow the handle doesn't correspond to a registered stylesheet
					if ( ! isset( $wp_styles->registered[ $handle ] ) )
						continue;

					// Provide basic style data
					$style_data = array(
						'handle' => $handle,
						'media'  => 'all'
					);

					// Base source
					$src = $wp_styles->registered[ $handle ]->src;

					// Take base_url into account
					if ( strpos( $src, 'http' ) !== 0 )
						$src = $wp_styles->base_url . $src;

					// Version and additional arguments
					if ( null === $wp_styles->registered[ $handle ]->ver )
						$ver = '';
					else
						$ver = $wp_styles->registered[ $handle ]->ver ? $wp_styles->registered[ $handle ]->ver : $wp_styles->default_version;

					if ( isset($wp_styles->args[ $handle ] ) )
						$ver = $ver ? $ver . '&amp;' . $wp_styles->args[$handle] : $wp_styles->args[$handle];

					// Full stylesheet source with version info
					$style_data['src'] = add_query_arg( 'ver', $ver, $src );

					// Parse stylesheet's conditional comments if present, converting to logic executable in JS
					if ( isset( $wp_styles->registered[ $handle ]->extra['conditional'] ) && $wp_styles->registered[ $handle ]->extra['conditional'] ) {
						// First, convert conditional comment operators to standard logical operators. %ver is replaced in JS with the IE version
						$style_data['conditional'] = str_replace( array(
							'lte',
							'lt',
							'gte',
							'gt'
						), array(
							'%ver <=',
							'%ver <',
							'%ver >=',
							'%ver >',
						), $wp_styles->registered[ $handle ]->extra['conditional'] );

						// Next, replace any !IE checks. These shouldn't be present since WP's conditional stylesheet implementation doesn't support them, but someone could be _doing_it_wrong().
						$style_data['conditional'] = preg_replace( '#!\s*IE(\s*\d+){0}#i', '1==2', $style_data['conditional'] );

						// Lastly, remove the IE strings
						$style_data['conditional'] = str_replace( 'IE', '', $style_data['conditional'] );
					}

					// Parse requested media context for stylesheet
					if ( isset( $wp_styles->registered[ $handle ]->args ) )
						$style_data['media'] = esc_attr( $wp_styles->registered[ $handle ]->args );

					// Add stylesheet to data that will be returned to SI JS
					array_push( $results['styles'], $style_data );
				}
			}
		}

		// Expose additional stylesheet data to filters, but only include in final `$results` array if needed.
		if ( ! isset( $results['styles'] ) )
			$results['styles'] = array();

		$results['styles'] = apply_filters( 'themify_single_infinite_additional_stylesheets', $results['styles'], $initial_styles, $results );

		if ( empty( $results['styles'] ) )
			unset( $results['styles' ] );

		// Lastly, return the IS results array
		return $results;
	}

}

/**
 * Return the status of Single IS.
 *
 * @since 1.0.0
 * @access public
 *
 * @return bool
 */
function themify_theme_is_single_infinite_enabled() {
	return 'on' == themify_get( 'setting-infinite_single_posts' );
}

/**
 * Return whether single infinite load is automatic or not.
 * Note: is true if auto load is disabled.
 *
 * @since 1.0.0
 * @access public
 *
 * @return bool
 */
function themify_theme_is_single_infinite_manual() {
	return themify_check( 'setting-single_autoinfinite' );
}

/**
 * Check if SI is enabled and start it.
 */
if ( themify_theme_is_single_infinite_enabled() ) {
	new Themify_Single_Infinite();
}