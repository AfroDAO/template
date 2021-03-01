<?php if(!is_single()) { global $more; $more = 0; } //enable more link ?>
<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php
$categories = wp_get_object_terms( get_the_id(), 'portfolio-category' );
$class = '';
if ( ! is_wp_error( $categories ) ) {
	foreach ( $categories as $cat ) {
		if ( is_object( $cat ) ) {
			$class .= ' cat-' . $cat->term_id;
		}
	}
}
?>

<?php themify_post_before(); //hook ?>
<article id="portfolio-<?php the_id(); ?>" class="<?php echo implode(' ', get_post_class('post clearfix portfolio-post' . $class)); ?>">
	<?php themify_post_start(); //hook ?>

    <?php if($themify->unlink_title!='yes' || $themify->unlink_image!='yes'):?>
        <a href="<?php echo themify_get_featured_image_link(); ?>" data-post-permalink="yes" style="display: none;"></a>
    <?php endif;?>

	<?php if ( themify_loop_is_singular( 'portfolio' ) ) : ?>

		<?php if ( $themify->hide_meta != 'yes' ): ?>
			<p class="post-meta entry-meta">
				<?php the_terms( get_the_id(), get_post_type() . '-category', '<span class="post-category">', ' <span class="separator">/</span> ', ' </span>' ) ?>
			</p>
		<?php endif; //post meta ?>

		<?php if($themify->hide_title != 'yes'): ?>
			<?php themify_post_title(); ?>
		<?php endif; //post title ?>

		 <?php get_template_part('includes/portfolio-meta', get_post_type()); ?>

	<?php endif; // is singular portfolio ?>

	<?php if ( ! themify_loop_is_singular( 'portfolio' ) ) : ?>

		<?php if( $themify->hide_image != 'yes' ) : ?>
			<?php get_template_part( 'includes/post-media', get_post_type() ); ?>
		<?php endif //hide image ?>

	<?php endif; // not singular portfolio ?>

	<div class="post-content">

		<?php if ( ! themify_loop_is_singular( 'portfolio' ) ) : ?>

			<div class="disp-table">
				<div class="disp-row">
					<div class="disp-cell valignmid">
						<?php if ( $themify->hide_date != 'yes' ) : ?>
						<div class="post-date-wrap<?php echo $themify->inline_date?' post-date-inline':''?>">
							<time class="post-date entry-date updated" datetime="<?php echo esc_attr( the_time( 'o-m-d' ) ); ?>">
											<?php if($themify->inline_date):?>
												<span class="year"><?php the_date()?></span>
											<?php else:?>
												<?php $format = themify_date_format();?>
												<?php foreach($format as $val):?>
													<span class="<?php echo $val[1]?>"><?php the_time( $val[0] ); ?></span>
												<?php endforeach;?>
											<?php endif;?>
							</time>
						</div>
						<?php endif; ?>
						<?php if ( $themify->hide_meta != 'yes' ): ?>
							<p class="post-meta entry-meta">
								<?php the_terms( get_the_id(), get_post_type() . '-category', '<span class="post-category">', ' <span class="separator">/</span> ', ' </span>' ) ?>
							</p>
						<?php endif; //post meta ?>

						<?php if($themify->hide_title != 'yes'): ?>
							<h2 class="post-title entry-title">
								<?php if($themify->unlink_title == 'yes'): ?>
									<?php the_title(); ?>
								<?php else: ?>
									<a href="<?php echo themify_get_featured_image_link(); ?>"><?php the_title(); ?></a>
								<?php endif; //unlink post title ?>
							</h2>
						<?php endif; //post title ?>

		<?php endif; // is singular portfolio ?>

		<div class="entry-content">

			<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>

				<?php the_excerpt(); ?>

			<?php elseif ( 'none' == $themify->display_content && ! is_attachment() ) : ?>

			<?php else: ?>

				<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>

			<?php endif; //display content ?>

		</div><!-- /.entry-content -->

		<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

		<?php if ( ! themify_loop_is_singular( 'portfolio' ) ) : ?>

					</div>
					<!-- /.disp-cell -->
				</div>
				<!-- /.disp-row -->
			</div>
			<!-- /.disp-table -->
		<?php endif; // is singular portfolio ?>

	</div>
	<!-- /.post-content -->

	<?php themify_post_end(); //hook ?>
</article>
<!-- /.post -->
<?php themify_post_after(); //hook ?>
