<?php
/**
 * Template for single post view
 * @package themify
 * @since 1.0.0
 */

get_header(); ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify;
?>

<?php if( have_posts() ) while ( have_posts() ) : the_post(); ?>

    <?php get_template_part( 'includes/content-single' ); ?>

<?php endwhile; ?>

<?php get_footer(); ?>