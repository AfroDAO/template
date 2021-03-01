<?php

/**
 * Main Themify class
 */
if ( ! class_exists( 'Themify' ) ) {
	class Themify {
		/** Default sidebar layout
		 * @var string */
		public $layout;
		/** Default posts layout
		 * @var string */
		public $post_layout;

		public $hide_title = 'no';
		public $hide_meta = 'no';
		public $hide_date = 'no';
		public $hide_image = 'no';

		public $unlink_title = 'no';
		public $unlink_image = 'no';

		public $display_content = 'excerpt';

		public $width = '';
		public $height = '';

		public $avatar_size = 96;
		public $page_navigation;
		public $posts_per_page;

		public $image_align = '';
		public $auto_featured_image = 'field_name=post_image, image, wp_thumb&';
		public $image_setting = 'setting=image_post&';

		public $query_category = '';
		public $paged = '';

		/////////////////////////////////////////////
		// Set Default Image Sizes 					
		/////////////////////////////////////////////

		// Default Index Layout
		static $content_width = 978;
		static $sidebar1_content_width = 670;

		// Default Single Post Layout
		static $single_content_width = 978;
		static $single_sidebar1_content_width = 670;

		// Default Single Image Size
		static $single_image_width = 978;
		static $single_image_height = 400;

		// Grid4
		static $grid4_width = 222;
		static $grid4_height = 140;

		// Grid3
		static $grid3_width = 306;
		static $grid3_height = 180;

		// Grid2
		static $grid2_width = 474;
		static $grid2_height = 250;

		// List Large
		static $list_large_image_width = 680;
		static $list_large_image_height = 390;

		// List Thumb
		static $list_thumb_image_width = 230;
		static $list_thumb_image_height = 200;

		// List Grid2 Thumb
		static $grid2_thumb_width = 120;
		static $grid2_thumb_height = 100;

		// List Post
		static $list_post_width = 978;
		static $list_post_height = 400;

		// Sorting Parameters
		public $order = 'DESC';
		public $orderby = 'date';

		function __construct() {
		}

		function template_redirect() {
		}

		/**
		 * Returns post category IDs concatenated in a string
		 * @param number Post ID
		 * @return string Category IDs
		 */
		public function get_categories_as_classes($post_id){
			$categories = wp_get_post_categories($post_id);
			$class = '';
			foreach($categories as $cat)
				$class .= ' cat-'.$cat;
			return $class;
		}

		 /**
		  * Returns category description
		  * @return string
		  */
		 function get_category_description(){
		 	$category_description = category_description();
			if ( !empty( $category_description ) ){
				return '<div class="category-description">' . $category_description . '</div>';
			}
		 }
	}
}

/**
 * Initializes Themify class
 */
function themify_builder_global_options(){
	/**
	 * Themify initialization class
	 */
	global $themify;
	if ( class_exists( 'Themify' ) ) {
		$themify = new Themify();
	}
}
add_action( 'init','themify_builder_global_options' );