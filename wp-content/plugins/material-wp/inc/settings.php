<?php

/**
 * Create our admin panel, so qe can add our styling and setup configs
 */
$panel = $this->options->createAdminPanel(array(
  'id'    => $this->slugfy('settings'),
  'name'  => __('Material WP', 'material-wp'),
  'title' => __('Material WP', 'material-wp'),
  'desc'  => __('The badges in each tab represents the number of new options in this version!<br>Also, check the "Activate Material WP" tab to join our <strong>CodeCanyon Happy Buyers Club</strong>!', 'material-wp'),
  'icon'  => 'dashicons-art',
  //'capability' => $this->options->getOption('super-admin-only') ? 'manage_network' : 'manage_options'
));

/**
 * Start creating tab
 */

// Styling Tab
$style = $panel->createTab(array(
  'id'   => 'style',
  'name' => __('Styling Settings', 'material-wp').'<span class="new badge no-new" style="margin-left:5px;" title="'. sprintf(__('%s new options!'), 1) .'">1</span>',
));

// Functionality Tabs
$func = $panel->createTab(array(
  'id'   => 'func',
  'name' => __('Functionality', 'material-wp').'<span class="new badge no-new" style="margin-left:5px;" title="'. sprintf(__('%s new options!'), 1) .'">1</span>',
));

// Custom CSS and JS Tab
$customCode = $panel->createTab(array(
  'name' => __('Custom CSS', 'material-wp'),
));

/**
 * Custom Code tab options
 */

$customCode->createOption(array(
  'id'   => 'custom-css',
  'type' => 'code',
  'lang' => 'scss',
  'name' => __('Custom CSS Code', 'material-wp'),
  'desc' => __('Enter your custom CSS code to be applied to the dashboard! SCSS syntax is supported!', 'material-wp'),
));

$customCode->createOption(array(
  'type' => 'save',
));

/**
 * Start creating options
 */

// Primary Color
$style->createOption(array(
  'id'   => 'primary-color',
  'name' => __('Primary Color', 'material-wp'),
  'desc' => __('This color will be used in the admin bar and in the parallax block effect.', 'material-wp'),
  'type' => 'color',
  'default' => '#2296F3',
));

// Accent Color
$style->createOption(array(
  'id'   => 'accent-color',
  'name' => __('Accent Color', 'material-wp'),
  'desc' => __('This color will be used in the buttons and links across the admin.', 'material-wp'),
  'type' => 'color',
  'default' => '#8AC249',
));

// BG Color
$style->createOption(array(
  'id'   => 'bg-color',
  'name' => __('Background Color', 'material-wp'),
  'desc' => __('This color will be applied to the background of the admin pages.', 'material-wp'),
  'type' => 'color',
  'default' => '#ececec',
));

// Enable Menu Random colors
$style->createOption(array(
  'id'      => 'menu-random-color',
  'name'    => __('Menu Icons Random Colors', 'material-wp'),
  'desc'    => __('Use this option to enable/disable the random colors for the admin links.', 'material-wp'),
  'type'    => 'enable',
  'default' => true,
));

// Menu icon Color Custom
$style->createOption(array(
  'id'      => 'menu-custom-color',
  'name'    => __('Menu Icons Custom Color', 'material-wp'),
  'desc'    => __('Select your own color for the admin menu icons. Note: this only works for the icon-font icons and this option is only used if the "Menu Icons Random Colors" option is disabled.', 'material-wp'),
  'type'    => 'color',
  'default' => '#333',
));

// Sidemenu Width
$style->createOption(array(
  'id'   => 'menu-width',
  'name' => __('Admin Menu Width', 'material-wp'),
  'desc' => __('Change the width of the sidemenu.', 'material-wp'),
  'type' => 'number',
  'unit' => 'px',
  'default' => '280',
  'min'  => '180',
  'max'  => '320',
));

// Parallax Block Height
$style->createOption(array(
  'id'   => 'parallax-height',
  'name' => __('Parallax Block Height', 'material-wp'),
  'desc' => __('Change the height of the parallax block.', 'material-wp'),
  'type' => 'number',
  'unit' => 'px',
  'default' => '300',
  'min'  => '250',
  'max'  => '300',
));

/**
 * @since 0.0.41 Min height
 */
$style->createOption(array(
  'id'      => 'stage-min-height',
  'name'    => __('Stage\'s Min Height', 'material-wp'),
  'desc'    => __('Define the min height for the stage. Put 0 to leave it without a min value.', 'material-wp'),
  'type'    => 'number',
  'unit'    => 'px',
  'default' => '500',
  'min'     => '0',
));

/**
 * New in 0.0.8: Admin Bar height selector
 */
$style->createOption(array(
  'id'   => 'adminbar-height',
  'name' => __('Adminbar Height', 'material-wp'),
  'desc' => __('Change the height of the adminbar.', 'material-wp'),
  'type' => 'number',
  'unit' => 'px',
  'default' => '55',
  'min'  => '35',
  'max'  => '100',
));

/**
 * New in 0.0.8: Admin Bar subitem height selector
 */
$style->createOption(array(
  'id'   => 'adminbar-subitem-height',
  'name' => __('Adminbar Subitem Height', 'material-wp'),
  'desc' => __('Change the height of the items of the adminbar submenu.', 'material-wp'),
  'type' => 'number',
  'unit' => 'px',
  'default' => '40',
  'min'  => '25',
  'max'  => '55',
));

$style->createOption(array(
  'id'   => 'adminbar-subitem-font-size',
  'name' => __('Adminbar Subitem Font-Size', 'material-wp'),
  'desc' => __('Change the font-size of the adminbar subitems and items.', 'material-wp'),
  'type' => 'number',
  'unit' => 'px',
  'default' => '16',
  'min'  => '10',
  'max'  => '20',
));

// Parallax options
$style->createOption(array(
  'id'   => 'parallax-options',
  'name' => __('Parallax Block Settings', 'material-wp'),
  'desc' => __('Here you can select the style of the display of the parallax block. You can choose to use a solid color, the parallax with the opacity effect or just the parallax effect.', 'material-wp'),
  'type' => 'radio',
  'default' => 'default',
  'options' => array(
    'default'     => __('Parallax + Opacity Effect', 'material-wp'),
    'parallax'    => __('Only Parallax Effect', 'material-wp'),
    'solid-color' => __('Solid Color', 'material-wp'),
  ),
));

// Menu icon Color Custom
$style->createOption(array(
  'id'      => 'parallax-bg-color',
  'name'    => __('Parallax BG Color', 'material-wp'),
  'desc'    => __('Select the background color of the parallax block.', 'material-wp'),
  'type'    => 'color',
  'default' => '#2296F3',
));

// preset BG
$style->createOption(array(
  'id'   => 'default-bg',
  'name' => __('Preset Parallax Block BG', 'material-wp'),
  'desc' => __('Select the background image of the parallax block from one of our preset images. You you prefer, you can choose your own image using the field bellow.', 'material-wp'),
  'type' => 'radio-image',
  'default' => 'bg1',
  'options' => array(
    'bg1'   => $this->getAsset('bgs-small/bg1.jpg'),
    'bg2'   => $this->getAsset('bgs-small/bg2.jpg'),
    'bg3'   => $this->getAsset('bgs-small/bg3.jpg'),
    'bg4'   => $this->getAsset('bgs-small/bg4.jpg'),
    //'no-bg' => $this->getAsset('bgs-small/no-bg.jpg'),
  ),
));

// Custom BG
$style->createOption(array(
  'id'   => 'custom-bg',
  'name' => __('Custom Parallax Block BG', 'material-wp'),
  'desc' => __('Select your custom Parallax Block BG.', 'material-wp'),
  'type' => 'upload',
  'placeholder' => __('Select your custom parallax block BG.', 'material-wp'),
  'default' => false,
));

// Custom Logo
$style->createOption(array(
  'id'   => 'custom-logo',
  'name' => __('Custom Logo', 'material-wp'),
  'desc' => __('Select the custom Logo you want to use. It will be displayed in the admin bar and in the login page as well.', 'material-wp'),
  'type' => 'upload',
  'placeholder' => __('Select your custom logo image.', 'material-wp'),
  'default' => $this->getAsset('logo.png'),
));

// Save Button
$style->createOption(array(
  'type' => 'save',
));

/**
 * Functionality Settings
 * All the Settings that relate to enablign and disabling functionality
 */

// Block Admin Menu
$func->createOption(array(
  'name' => __('Admin Menu', 'material-wp'),
  'desc' => __('Use this block to change the settings regarding the admin menu', 'material-wp'),
  'type' => 'heading',
));

// Enable menu editing
$func->createOption(array(
  'id'      => 'menu-reordering',
  'name'    => __('Menu Reordering', 'material-wp'),
  'desc'    => __('If enabled, the reordering functionality will be presented to the backend users.', 'material-wp'),
  'type'    => 'enable',
  'default' => true,
));

// Chnage the menu Label
$func->createOption(array(
  'id'          => 'menu-label',
  'name'        => __('Menu Label', 'material-wp'),
  'desc'        => __('Select the little text that appears ontop of the admin menu. Leave blank to display nothing.', 'material-wp'),
  'placeholder' => 'This will display nothing.',
  'type'        => 'text',
  'default'     => __('Main Menu', 'material-wp'),
));

// Change the menu position
$func->createOption(array(
  'id'          => 'menu-position',
  'name'        => __('Menu Position', 'material-wp'),
  'desc'        => __('You can position the admin menu in either side of the screen.', 'material-wp'),
  'type'        => 'radio',
  'default'     => 'left',
  'options'     => array(
    'left'      => __('Left', 'material-wp'),
    'right'     => __('Right', 'material-wp'),
  )
));

// Display or not the avatar
// Since 0.0.33
$func->createOption(array(
  'id'      => 'avatar-display',
  'name'    => __('Display Avatar Block', 'material-wp'),
  'desc'    => __('Select if you want to display or not the avatar block.', 'material-wp'),
  'type'    => 'enable',
  'default' => true,
));

// Display Link
// Since 0.0.33
$func->createOption(array(
  'id'      => 'avatar-link',
  'name'    => __('Link in the Avatar', 'material-wp'),
  'desc'    => __('You can choose to use a custom link for the avatar block.', 'material-wp'),
  'type'    => 'text',
  'placeholder' => __('Defaults to the WordPress default profile link.', 'material-wp'),
));

// Save Button
$func->createOption(array(
  'type' => 'save',
));

// -- Admin Bar --

// Block Admin Menu
$func->createOption(array(
  'name' => __('Admin Bar', 'material-wp'),
  'desc' => __('Use this block to change the settings regarding the admin bar', 'material-wp'),
  'type' => 'heading',
));

$func->createOption(array(
  'id'      => 'custom-logo-link',
  'name'    => __('Link in the Logo - Admin', 'material-wp'),
  'desc'    => __('Change the link in the logo displayed in the admin bar. Leave blank to link to your the admin panel homepage.', 'material-wp'),
  'type'    => 'text',
  'placeholder' => __('Defaults to your admin panel homepage', 'material-wp'),
));

$func->createOption(array(
  'id'      => 'admin-bar-frontend',
  'name'    => __('Admin Bar in the Frontend', 'material-wp'),
  'desc'    => __('You can choose to use the default WordPress admin bar styles in the frontend by disabling our custom styles.', 'material-wp'),
  'type'    => 'enable',
  'default' => true,
));

$func->createOption(array(
  'id'      => 'admin-bar-user-card',
  'name'    => __('Display user card on the WP Toolbar', 'material-wp'),
  'desc'    => __('If you enable this option, the user card will be displayed in the WP Toolbar as well. Usefull when using plugins like BuddyPress, for example.', 'material-wp'),
  'type'    => 'enable',
  'default' => false,
));

// Enable menu editing
$func->createOption(array(
  'id'      => 'menu-settings-link',
  'name'    => __('Display the Settings Link (Cog Icon)', 'material-wp'),
  'desc'    => __('If enabled, the cog icon will be displayed in the admin top bar.', 'material-wp'),
  'type'    => 'enable',
  'default' => true,
));

// Save Button
$func->createOption(array(
  'type' => 'save',
));

// -- Login --

// Block Login Page 
$func->createOption(array(
  'name' => __('Login Page', 'material-wp'),
  'desc' => __('Use this block to change some options of the login page.', 'material-wp'),
  'type' => 'heading',
));

$func->createOption(array(
  'id'      => 'login-styles',
  'name'    => __('Styles in the Login Page', 'material-wp'),
  'desc'    => __('You can choose to use the default WordPress login styles by disabling our custom styles.', 'material-wp'),
  'type'    => 'enable',
  'default' => true,
));

$func->createOption(array(
  'id'      => 'logo-link',
  'name'    => __('Link in the Logo', 'material-wp'),
  'desc'    => __('Change the link in the logo displayed above the login form. Leave blank to link to your site\'s homepage.', 'material-wp'),
  'type'    => 'text',
  'placeholder' => __('Defaults to your site\'s homepage', 'material-wp'),
));

$func->createOption(array(
  'id'      => 'back-to-blog',
  'name'    => __('Display "Back to Blog" link?', 'material-wp'),
  'desc'    => __('Using this option you can choose to display or hide the back to blog link in the admin form.', 'material-wp'),
  'type'    => 'enable',
  'default' => true,
));

// Save Button
$func->createOption(array(
  'type' => 'save',
));

// -- Blacklist --
// 
$func->createOption(array(
  'name' => __('Page Blacklist & Target Settings', 'material-wp'),
  'desc' => __('Use this section to blacklist some pages on Material WP. Pages that match that URL will not have the styles applied to them. You can always select the role or user to apply Material WP.', 'material-wp'),
  'type' => 'heading',
));

$func->createOption(array(
  'id'      => 'material-wp-blacklist',
  'name'    => __('Pages Blacklist', 'material-wp'),
  'desc'    => __('Add URLs or URL fragments to exclude certain pages from receiving the Material WP styles. One URL per line.', 'material-wp'),
  'type'    => 'textarea',
));

if (!function_exists('get_editable_roles')) {
  require_once ABSPATH . '/wp-admin/includes/user.php';
}

$roles = get_editable_roles();

$role_options = array_map(function($item) {
  return $item['name'];
}, $roles);

if (is_multisite() && is_network_admin()) {

  $role_options = array_merge(array(
    'super-admin' => __('Super Administrator', 'material-wp'),
  ), $role_options);

} // end if;

$func->createOption(array(
  'id'      => 'material-wp-roles',
  'name'    => __('Roles to Apply', 'material-wp'),
  'desc'    => __('Select the roles to apply Material WP. Do not select anything to apply to all.', 'material-wp'),
  'type'    => 'multicheck',
  'default' => array(),
  'options' => $role_options,
  'select_all' => true,
));

global $pagenow;

if (!is_multisite() || !is_network_admin()) {

  $users = $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'material-wp_settings' ? get_users(array(
    'blog_id' => get_current_blog_id(),
  )) : array();

  $user_options = array();

  foreach($users as $item) {

    if (is_multisite()) {

      $user_options[$item->ID] = sprintf("%s", $item->display_name);
      
    } else {
      
      $user_options[$item->ID] = sprintf("%s (%s)", $item->display_name, implode(', ', $item->roles));

    }

  } // end foreach;

  $func->createOption(array(
    'id'      => 'material-wp-user',
    'name'    => __('Users to Apply', 'material-wp'),
    'desc'    => __('Select the user to apply Material WP. Do not select anything to apply to all.', 'material-wp'),
    'type'    => 'multicheck',
    'default' => array(),
    'options' => $user_options,
    'select_all' => true,
  ));

} // end if;

// Save Button
$func->createOption(array(
  'type' => 'save',
));

// -- Help Tabs --

$func->createOption(array(
  'name' => __('Help and Screen Options Tab', 'material-wp'),
  'desc' => __('Change the visibility of the Help and Screen Options tab of the WordPress admin.', 'material-wp'),
  'type' => 'heading',
));

// Hide Help
$func->createOption(array(
  'id'      => 'display-help-tab',
  'name'    => __('Display Help Tab', 'material-wp'),
  'desc'    => __('Use this option to change the visibility of the Help tab.', 'material-wp'),
  'type'    => 'enable',
  'default' => true,
));

// Hide Screen Options Tab
$func->createOption(array(
  'id'      => 'display-screen-tab',
  'name'    => __('Display Screen Options Tab', 'material-wp'),
  'desc'    => __('Use this option to change the visibility of the Screen Options tab.', 'material-wp'),
  'type'    => 'enable',
  'default' => true,
));

// Save Button
$func->createOption(array(
  'type' => 'save',
));