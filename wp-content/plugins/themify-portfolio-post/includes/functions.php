<?php

/**
 * Conditional tag to check if we're on a portfolio category archive page
 * Can optionally check for specific portfolio category terms
 * Should only be used after template_redirect
 *
 * @return bool
 * @since 1.0.0
 */
function tpp_is_portfolio_category( $term = null ) {
	global $themify_portfolio_posts;

	return $themify_portfolio_posts->is_portfolio_category( $term );
}

/**
 * Conditional tag to check if we're on a single portfolio page
 * Can optionally check for specific portfolio slug
 * Should only be used after template_redirect
 *
 * @return bool
 * @since 1.0.0
 */
function tpp_is_portfolio_single( $slug = null ) {
	global $themify_portfolio_posts;

	return $themify_portfolio_posts->is_portfolio_single( $slug );
}

/**
 * Check if option is set for the current item in the loop
 *
 * @since 1.0
 */
function tpp_check( $var ) {
	global $post;

	if ( is_object( $post ) && get_post_meta( $post->ID, $var, true ) != '' && get_post_meta( $post->ID, $var, true ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Get an option for the current item in the loop
 *
 * @since 1.0
 */
function tpp_get( $var, $default = null ) {
	global $post;

	if ( is_object( $post ) && get_post_meta( $post->ID, $var, true ) != '' ) {
		return get_post_meta( $post->ID, $var, true );
	} else {
		return $default;
	}
}

if ( ! function_exists( 'tpp_get_permalink' ) ) :
/**
 * Get permalink for a portfolio post
 *
 * @return string
 * @since 1.0.8
 */
function tpp_get_permalink() {
	return get_permalink();
}
endif;

if( ! function_exists( 'tpp_post_title_tag' ) ) :
/**
 * Get the HTML tag to be used for post titles
 *
 * @since 1.0.8
 * @return string
 */
function tpp_post_title_tag() {
	$tag = 'h2';
	if( is_singular() ) {
		$tag = 'h1';
	}

	return apply_filters( 'tpp_post_title_tag', $tag );
}
endif;

if( ! function_exists( 'tpp_post_title' ) ) :
/**
 * Template tag to display the post title
 *
 * @since 1.0.8
 */
function tpp_post_title( $args = array() ) {
	global $themify;

	extract( wp_parse_args( $args, array(
		'tag' => tpp_post_title_tag(),
		'class' => 'post-title entry-title',
		'before' => '',
		'after' => '',
		'before_title' => '',
		'after_title' => '',
		'echo' => true,
		'unlink' => false,
	), 'post_title' ) );

	$link_before = $unlink ? '' : '<a href="' . tpp_get_permalink() .'">';
	$link_after = $unlink ? '' : '</a>';

	$before = "{$before} <{$tag} class=\"{$class}\">{$before_title}{$link_before}";
	$after = "{$link_after}{$after_title} </{$tag}>{$after}";

	the_title( $before, $after, $echo );
}
endif;

if ( ! function_exists( 'tpp_get_image' ) ) :
/**
 * Display post thumbnail
 *
 * @return string
 * @since 1.0.8
 */
function tpp_get_image( $args ) {
	if ( ! has_post_thumbnail() ) {
		return '';
	}

	global $wp_version;

	/**
	 * List of parameters
	 * @var array
	 */
	$args = wp_parse_args( $args, array(
		'id'          => '',
		'ignore'      => '',
		'width'       => '',
		'height'      => '',
		'before'      => '',
		'after'       => '',
		'class'       => '',
		'alt'         => '',
		'title'       => '',
		'image_meta'  => true,
		'crop'        => true,
	) );
	extract( $args );

	$id = (int) get_post_thumbnail_id(); /* Image script works with thumbnail IDs as well as URLs, use ID which is faster */

	$temp = themify_do_img( $id, $width, $height, (bool) $args['crop'] );
	$img_url = $temp['url'];

	// Build final image
	$out = '';
	if ( $args['image_meta'] == true ) {
		$out .= "<meta itemprop=\"width\" content=\"{$width}\">";
		$out .= "<meta itemprop=\"height\" content=\"{$height}\">";
		$out .= "<meta itemprop=\"url\" content=\"{$img_url}\">";
	}
	$out .= "<img src=\"{$img_url}\"";
	if ( $width ) {
		$out .= " width=\"{$width}\"";
	}
	if ( $height ) {
		$out .= " height=\"{$height}\"";
	}
	$args['class'] .= ' wp-post-image wp-image-' . $id; /* add attachment_id class to img tag */

	if ( ! empty( $args['class'] ) ) {
		$out .= " class=\"{$args['class']}\"";
	}

	// Add title attribute only if explicitly set in $args
	if ( ! empty( $args['title'] ) ) {
		$out .= ' title="' . esc_attr( $args['title'] ) . '"';
	}

	// If alt was passed as parameter, use it. Otherwise use alt text by attachment id if it was fetched or post title.
	if ( ! empty( $args['alt'] ) ) {
		$out_alt = $args['alt'] === 'false' ? '' : $args['alt'];
	} elseif ( ! empty( $img_alt ) ) {
		$out_alt = $img_alt;
	} else {
		if ( ! empty( $args['title'] ) ) {
			$out_alt = $args['title'];
		} elseif ( $id ) {
			$p = get_post( $id );
			$out_alt = $p->post_title;
		} else {
			$out_alt = the_title_attribute( 'echo=0' );
		}
	}
	$out .= ' alt="' . esc_attr( $out_alt ) . '" />';
	$out = $args['before'] . $out . $args['after'];

	if( version_compare( $wp_version, '4.4', '>=' ) ) {
		$out =function_exists('wp_filter_content_tags')?wp_filter_content_tags($out):wp_make_content_images_responsive( $out );
	}

	return $out;
}
endif;

if ( ! function_exists( 'tpp_get_post_category_classes' ) ) :
/**
 * Augments post_class with additional portfolio-post classes
 *
 * @return string
 */
function tpp_get_post_category_classes( $classes = array( 'post', 'portfolio-post' ) ) {
	$categories = wp_get_object_terms( get_the_id(), 'portfolio-category' );
	foreach ( $categories as $cat ) {
		$classes[] = ' cat-' . $cat->term_id;
	}

	post_class( join( $classes, ' ' ) );
}
endif;

if ( ! function_exists( 'tpp_get_paged_query' ) ) :
function tpp_get_paged_query() {
	global $wp;
	$page = 1;
	$qpaged = get_query_var('paged');
	if (!empty($qpaged)) {
		$page = $qpaged;
	} else {
		$qpaged = wp_parse_args($wp->matched_query);
		if (isset($qpaged['paged']) && $qpaged['paged'] > 0) {
			$page = $qpaged['paged'];
		}
	}
	return $page;
}
endif;

if ( ! function_exists( 'tpp_get_pagenav' ) ) :
/**
 * Returns page navigation
 * @param string Markup to show before pagination links
 * @param string Markup to show after pagination links
 * @param object WordPress query object to use
 * @param original_offset number of posts configured to skip over
 * @return string
 */
function tpp_get_pagenav( $before = '', $after = '', $query = false ) {
	global $wp_query;

	if (false == $query) {
		$query = $wp_query;
	}

	$paged = intval(tpp_get_paged_query());
	$numposts = $query->found_posts;

	$max_page = ceil($numposts / $query->query_vars['posts_per_page']);
	$out = '';

	if (empty($paged)) {
		$paged = 1;
	}
	$pages_to_show = 5;
	$pages_to_show_minus_1 = $pages_to_show - 1;
	$half_page_start = floor($pages_to_show_minus_1 / 2);
	$half_page_end = ceil($pages_to_show_minus_1 / 2);
	$start_page = $paged - $half_page_start;
	if ($start_page <= 0) {
		$start_page = 1;
	}
	$end_page = $paged + $half_page_end;
	if (($end_page - $start_page) != $pages_to_show_minus_1) {
		$end_page = $start_page + $pages_to_show_minus_1;
	}
	if ($end_page > $max_page) {
		$start_page = $max_page - $pages_to_show_minus_1;
		$end_page = $max_page;
	}
	if ($start_page <= 0) {
		$start_page = 1;
	}

	if ($max_page > 1) {
		$out .= $before . '<div class="pagenav clearfix">';
		if ($start_page >= 2 && $pages_to_show < $max_page) {
			$first_page_text = "&laquo;";
			$out .= '<a href="' . esc_url(get_pagenum_link()) . '" title="' . esc_attr($first_page_text) . '" class="number">' . $first_page_text . '</a>';
		}
		if ($pages_to_show < $max_page)
			$out .= get_previous_posts_link('&lt;');
		for ($i = $start_page; $i <= $end_page; $i++) {
			if ($i == $paged) {
				$out .= ' <span class="number current">' . $i . '</span> ';
			} else {
				$out .= ' <a href="' . esc_url(get_pagenum_link($i)) . '" class="number">' . $i . '</a> ';
			}
		}
		if ($pages_to_show < $max_page)
			$out .= get_next_posts_link('&gt;');
		if ($end_page < $max_page) {
			$last_page_text = "&raquo;";
			$out .= '<a href="' . esc_url(get_pagenum_link($max_page)) . '" title="' . esc_attr($last_page_text) . '" class="number">' . $last_page_text . '</a>';
		}
		$out .= '</div>' . $after;
	}
	return $out;
}
endif;
