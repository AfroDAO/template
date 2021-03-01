<?php
/**
 * Template Name: Clean Page
 * This template will only display the content you entered in the page editor
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body class="wpsc-popup">
<?php
    while ( have_posts() ) : the_post();  
        the_content();
    endwhile;
?>
<?php wp_footer(); ?>
</body>
</html>
