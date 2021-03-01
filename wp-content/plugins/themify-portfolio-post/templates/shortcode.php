<?php
/**
 * Template to display the themify_portfolio_posts shortcode
 *
 * To override this template copy it to <your-theme>/themify-portfolio-posts/shortcode.php and edit.
 *
 * @var $query the WP_Query object of queried posts
 * @var $posts result of $query->query()
 * @var $atts parsed shortcode attributes
 */

global $post;

if ( is_object( $post ) )
	$saved_post = clone $post;
?>

<script type="text/javascript">if ( ! document.getElementById( "tpp-styles" ) ) document.getElementsByTagName( "head" )[0].innerHTML += "<link id='tpp-styles' rel='stylesheet' href='<?php echo $this->url . 'assets/styles.css'; ?>' type='text/css' />";</script>

<div class="tpp-loop <?php echo $atts['style']; ?>">

	<?php
	foreach ( $posts as $post ) :
		setup_postdata( $post );
		?>

		<article id="post-<?php the_id(); ?>" <?php tpp_get_post_category_classes(); ?>>

			<div class="post-image">
				<?php echo tpp_get_image( array(
					'width' => $atts['image_w'],
					'height' => $atts['image_h'],
					'before' => $atts['unlink_image'] === 'yes' ? '' : sprintf( '<a href="%s">', tpp_get_permalink() ),
					'after' => $atts['unlink_image'] === 'yes' ? '' : '</a>',
				) ); ?>
			</div>
			<div class="post-content">

				<?php if ( $atts['post_meta'] === 'yes' ) : ?>
					<p class="post-meta entry-meta">
						<?php 
							$term_lists = get_the_term_list( get_the_ID(), get_post_type().'-category', ' <span class="post-category">', ', ', '</span>' );
							if ( ! is_wp_error( $term_lists ) ) echo $term_lists;
						?>
					</p>
				<?php endif; ?>

				<?php if ( $atts['title'] === 'yes' ) : ?>
					<?php tpp_post_title( array(
						'unlink' => $atts['unlink_title'] === 'yes',
					) ); ?>
				<?php endif; ?>

				<?php if ( $atts['post_date'] === 'yes' ) : ?>
					<time datetime="<?php the_time( 'o-m-d' ) ?>" class="post-date entry-date updated published">
						<?php echo get_the_date() ?>
					</time>
				<?php endif; //post date ?>

				<div class="entry-content">

					<?php if ( $atts['display'] === 'content' ) : ?>
						<?php the_content( $atts['more_text'] ); ?>
					<?php elseif ( $atts['display'] === 'none' ) : ?>
						<!-- display: none -->
					<?php else : ?>
						<?php the_excerpt(); ?>
					<?php endif; //display content ?>

				</div><!-- /.entry-content -->

			</div>
		</article>

	<?php endforeach; ?>

	<div class="clear"></div>

	<?php if ( $atts['pagination'] === 'yes' ) echo tpp_get_pagenav( '', '', $query ) ?>

</div>

<?php
if ( isset( $saved_post ) && is_object( $saved_post ) ) {
	$post = $saved_post;
	/**
	 * WooCommerce plugin resets the global $product on the_post hook,
	 * call setup_postdata on the original $post object to prevent fatal error from WC
	 */
	setup_postdata( $saved_post );
}