<?php
/**
 * Partial template that displays an entry filter.
 *
 * Created by themify
 * @since 1.0.0
 */

global $themify;

if ( isset( $themify->is_shortcode ) && $themify->is_shortcode ) {
	$cats = $themify->shortcode_query_category;
	$taxo = $themify->shortcode_query_taxonomy;
} else {
	if ( is_array( $themify->query_category ) ) {
		$cats = join(',', $themify->query_category);
	} else {
		$cats = $themify->query_category;
	}
	$taxo = $themify->query_taxonomy;
}

$args = "show_option_none=0&echo=0&hierarchical=0&show_count=0&title_li=&include=$cats&taxonomy=$taxo";

if( is_category() && themify_get( 'setting-filter-category' ) ) {
	$category = get_queried_object();

	if( ! empty( $category ) ) {
		$args .= '&child_of=' . $category->term_id;
	}
}

$list_categories = wp_list_categories( $args );

if( ! empty( $list_categories ) ) {
	printf( '<ul class="post-filter">%s</ul>', $list_categories );
}