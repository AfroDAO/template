<?php
/**
 * Partial template for single view.
 * Loaded in regular page load queries and also through ajax with Themify Single Infinite.
 *
 */
global $themify;

?>

<div class="single_posts_wrapper <?php if(themify_theme_is_single_infinite_enabled()): echo "single_posts_wrapper_infinite". " " . esc_attr( themify_theme_single_wrapper_classes() ); endif;?>" style="position: relative"   data-title="<?php the_title_attribute(); ?>" data-url="<?php echo esc_url( themify_https_esc( get_permalink() ) ); ?>">

    <?php if( $themify->post_layout_type && $themify->post_layout_type !== 'classic' ) : ?>
        <div class="featured-area fullcover">
            <?php if ( $themify->post_layout_type == 'gallery' || $themify->post_layout_type == 'slider' ) : ?>
                <?php if($themify->hide_image != 'yes'):?>
                    <?php get_template_part('includes/single-' . $themify->post_layout_type, 'single'); ?>
                <?php endif;?>
            <?php else: ?>
                <?php get_template_part('includes/post-media', get_post_type()); ?>
            <?php endif; ?>
            <?php get_template_part('includes/post-meta',  get_post_type()); ?>
        </div>
    <?php endif;?>

<div id="layout" class="pagewidth clearfix">
    <?php themify_content_before(); // hook ?>

    <!-- content -->
    <div id="content" class="list-post">
        <?php themify_content_start(); // hook ?>

        <?php if( ! $themify->post_layout_type || $themify->post_layout_type === 'classic' ) : ?>

            <?php get_template_part( 'includes/loop', get_post_type()); ?>

        <?php else:?>

            <?php themify_post_before(); // hook ?>

            <article id="post-<?php  the_id(); ?>" <?php post_class('post clearfix'); ?>>
                <?php themify_post_start(); // hook  ?>
                <div class="post-content">
                    <div class="entry-content" itemprop="articleBody">
                        <?php the_content(); ?>
                    </div>
                </div>
                <?php themify_post_end(); // hook  ?>
            </article>

            <?php themify_post_after(); // hook ?>

        <?php endif;?>

        <?php wp_link_pages( array( 'before' => '<p class="post-pagination"><strong>' . __( 'Pages:', 'themify' ) . ' </strong>', 'after' => '</p>', 'next_or_number' => 'number' ) ); ?>

        <?php get_template_part( 'includes/author-box', 'single' ); ?>

        <?php get_template_part( 'includes/post-nav' ); ?>

        <?php if ( is_single() && 'none' != themify_get( 'setting-relationship_taxonomy' ) ) : ?>
            <?php get_template_part( 'includes/related-posts', 'loop' ); ?>
        <?php endif; ?>

        <?php if(!themify_check('setting-comments_posts')): ?>
            <?php comments_template(); ?>
        <?php endif; ?>

        <?php themify_content_end(); // hook ?>
    </div>
    <!-- /content -->

    <?php themify_content_after(); // hook ?>

    <?php
    /////////////////////////////////////////////
    // Sidebar
    /////////////////////////////////////////////
    if ($themify->layout != "sidebar-none"): get_sidebar(); endif; ?>

    <div class="clearfix" ></div>

</div>

    <div class="clearfix" ></div>

</div>
