<?php

class Themify_Portfolio_Posts_Admin {

	var $options;
	var $post_type = 'portfolio';
	var $tax = 'portfolio-category';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'manage_and_filter' ) );
		add_filter( 'manage_edit-'.$this->tax.'_columns', array( $this, 'taxonomy_header' ), 10, 2 );
		add_filter( 'manage_'.$this->tax.'_custom_column', array( $this, 'taxonomy_column_id' ), 10, 3 );
		add_filter( 'attachment_fields_to_edit', array( $this, 'attachment_fields_to_edit' ), 10, 2 );
		add_action( 'edit_attachment', array($this, 'attachment_fields_to_save'), 10, 2 );

		// Compatibility mode: do not setup metabox or options page
		if( THEMIFY_PORTFOLIO_POSTS_COMPAT_MODE == true ) {
			return;
		}

		add_action( 'init', array( $this, 'setup_portfolio_metabox' ) );
	}

	public function setup_options_page() {
		add_submenu_page( 'edit.php?post_type=portfolio', __( 'Portfolio Options', 'themify-portfolio-posts' ), __( 'Portfolio Options', 'themify-portfolio-posts' ), 'manage_options', 'themify-portfolio-posts', array( $this, 'create_admin_page' ) );
	}

	public function setup_portfolio_metabox() {
		add_filter( 'themify_do_metaboxes', array( $this, 'themify_do_metaboxes' ) );
	}

	public function themify_do_metaboxes( $metaboxes ) {
		global $themify_portfolio_posts, $pagenow;

		$portfolio_options = array(
			array(
				'name'		=> __( 'Project Info', 'themify-portfolio-posts' ),
				'id' 		=> 'tpp-project-info',
				'options' 	=> include $themify_portfolio_posts->dir . 'includes/config.php',
				'pages'		=> 'portfolio'
			)
		);
		$portfolio_options = apply_filters( "themify_portfolio_post_options", $portfolio_options );

		return array_merge( $portfolio_options, $metaboxes );
	}

	/**
	 * Trigger at the end of __construct of this shortcode
	 */
	function manage_and_filter() {
		add_filter( "manage_edit-{$this->post_type}_columns", array( $this, 'type_column_header' ), 10, 2 );
		add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'type_column' ), 10, 3 );
		global $typenow;
		if ( $typenow == $this->post_type ) {
			add_action( 'load-edit.php', array( $this, 'filter_load' ) );
			add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 1 );
		}
	}

	/**
	 * Filter request to sort
	 */
	function filter_load() {
		add_action( current_filter(), array( $this, 'setup_vars' ), 20 );
		add_action( 'restrict_manage_posts', array( $this, 'get_select' ) );
		add_filter( "manage_taxonomies_for_{$this->post_type}_columns", array( $this, 'add_columns' ) );
	}

	/**
	 * Setup vars when filtering posts in edit.php
	 */
	function setup_vars() {
		$this->post_type =  get_current_screen()->post_type;
		$this->taxonomies = array_diff(get_object_taxonomies($this->post_type), get_taxonomies(array('show_admin_column' => 'false')));
	}

	/**
	 * Select form element to filter the post list
	 * @return string HTML
	 */
	public function get_select() {
		$html = '';
		foreach ( $this->taxonomies as $tax ) {
			$options = sprintf( '<option value="">%s %s</option>', __('View All', 'themify-portfolio-posts'),
			get_taxonomy($tax)->label );
			$class = is_taxonomy_hierarchical( $tax ) ? ' class="level-0"' : '';
			foreach ( get_terms( $tax ) as $taxon ) {
				$options .= sprintf( '<option %s%s value="%s">%s%s</option>', isset( $_GET[$tax] ) ? selected( $taxon->slug, $_GET[$tax], false ) : '', '0' !== $taxon->parent ? ' class="level-1"' : $class, $taxon->slug, '0' !== $taxon->parent ? str_repeat( '&nbsp;', 3 ) : '', "{$taxon->name} ({$taxon->count})" );
			}
			$html .= sprintf( '<select name="%s" id="%s" class="postform">%s</select>', esc_attr( $tax ), esc_attr( $tax ), $options );
		}
		echo $html;
	}

	/**
	 * Add columns when filtering posts in edit.php
	 */
	public function add_columns( $taxonomies ) {
		return array_merge( $taxonomies, $this->taxonomies );
	}

	/**
	 * Display an additional column in list
	 * @param array
	 * @return array
	 */
	function type_column_header( $columns ) {
		unset( $columns['date'] );
		return $columns;
	}

	/**
	 * Display shortcode, type, size and color in columns in tiles list
	 * @param string $column key
	 * @param number $post_id
	 * @return string
	 */
	function type_column( $column, $post_id ) {
		switch( $column ) {
			case 'shortcode' :
				$shortcode = '[' . $this->post_type . ' id="' . esc_attr( $post_id ) . '"]';
				echo '<input type="text" value="' . esc_attr( $shortcode ) . '" readonly="readonly" class="widefat" onclick="this.select()" />';
				break;
		}
	}

	/**
	 * Remove quick edit action from entries list in admin
	 * @param $actions
	 * @return mixed
	 */
	function remove_quick_edit( $actions ) {
		unset($actions['inline hide-if-no-js']);
		return $actions;
	}

	function attachment_fields_to_edit( $form_fields, $post ) {
		if ( ! preg_match( '!^image/!', get_post_mime_type( $post->ID ) ) ) {
			return $form_fields;
		}

		$include = get_post_meta( $post->ID, 'themify_gallery_featured', true );

		$name = 'attachments[' . $post->ID . '][themify_gallery_featured]';

		$form_fields['themify_gallery_featured'] = array(
			'label' => __( 'Larger', 'themify-portfolio-posts' ),
			'input' => 'html',
			'helps' => __('Show larger image in the gallery.', 'themify-portfolio-posts'),
			'html'  => '<span class="setting"><label for="' . esc_attr( $name ) . '" class="setting"><input type="checkbox" name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" value="featured" ' . checked( $include, 'featured', false ) . ' />' . '</label></span>',
		);

		return $form_fields;
	}

	function attachment_fields_to_save( $attachment_id ) {
		if( isset( $_REQUEST['attachments'][$attachment_id]['themify_gallery_featured'] ) && preg_match( '!^image/!', get_post_mime_type( $attachment_id ) ) ) {
			update_post_meta($attachment_id, 'themify_gallery_featured', 'featured');
		} else {
			update_post_meta($attachment_id, 'themify_gallery_featured', '');
		}
	}

	/**
	 * Display an additional column in categories list
	 * @since 1.0.0
	 */
	function taxonomy_header($cat_columns) {
		$cat_columns['cat_id'] = 'ID';
		return $cat_columns;
	}

	/**
	 * Display ID in additional column in categories list
	 * @since 1.0.0
	 */
	function taxonomy_column_id($null, $column, $termid) {
		return $termid;
	}
}
