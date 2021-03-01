<?php
/**
 * Plugin Name: Material WP
 * Plugin URI: http://codecanyon.net/item/material-wp-material-design-dashboard-theme/12981098?ref=732
 * Text Domain: material-wp
 * Description: Bring Material Design to you WordPress Dashboard.
 * Version: 1.0.6
 * Author: Arindo Duque - NextPress (Formely 732)
 * Author URI: http://nextpress.co/
 * Copyright: Arindo Duque, NextPress.
 */

require_once plugin_dir_path(__FILE__).'/paradox/paradox.php';

require_once plugin_dir_path(__FILE__).'/inc/np-theme-factory.php';

/**
 * Here starts our plugin.
 */
class MaterialWP extends NextPress_Theme_Factory {
    /**
     * SCRIPTS AND STYLES
     * The section bellow handles the adding of scripts and css files to the different hooks WordPress offers
     * such as Admin, Frontend and Login. Calling anyone of these hooks on the child class you automatically
     * add the scripts hooked to the respective hook.
     *
     * @param mixed $scripts
     */

    /**
     * Fix the problems we were having with the common script.
     *
     * @param WP_Scripts $scripts
     */
    public function changeCommonScript(&$scripts) {
        if (isset($scripts->registered['common'])) {
            $scripts->registered['common']->src = $this->url('assets/js/common.min.js');

            $localization_info = [
                'warnDelete' => __("You are about to permanently delete the selected items.\n  'Cancel' to stop, 'OK' to delete.", 'material-wp'),
                'dismiss' => __('Dismiss this notice.', 'material-wp'),
                'expandLabel' => __('Expand / Compress Stage', 'material-wp'),
            ];

            // Localize it
            wp_localize_script('common', 'commonL10n', $localization_info);
        } // end if;
    }

    // end changeCommonScripts;

    /**
     * Adds Parallax Block.
     */
    public function addParallaxBlock() {
        // We only add if it is not the VC Frontend
        if (isset($_GET['vc_action']) && 'vc_inline' == $_GET['vc_action']) {
            return;
        }
        // Include our SCSS Compiler class
        $this->render('parallax-block');
    }

    // Remove the WordPress Logo from the WordPress Admin Bar
    public function editAdminBar() {
        // If it does not go to the backend, does even show
        if (!is_admin() && !$this->options->getOption('admin-bar-frontend')) {
            return;
        }
        // Get global var admin bar
        global $wp_admin_bar;

        // Remove undesired nodes
        $wp_admin_bar->remove_menu('wp-logo');

        // Get user card
        $userCard = $wp_admin_bar->get_node('my-account');
        $wp_admin_bar->remove_menu('my-account');

        // Updates
        $update_node = $wp_admin_bar->get_node('updates');

        // If Node Exists
        if ($update_node) {
            // Add classes
            $update_node->meta['class'] = 'force-mdi tooltiped tooltip-ajust';
            // Ajust title
            $update_node->title = str_replace('<span class="ab-icon"></span>', '<i class="mdi-notification-sync"></i>', $update_node->title);
            $updates = [
                'id' => $update_node->id,
                'title' => $update_node->title,
                'href' => $update_node->href,
                'parent' => $update_node->parent,
                'meta' => $update_node->meta,
            ];

            // Add Editted Updates
            $wp_admin_bar->add_node($updates);
        }

        // We need to check if this is network
        if (!is_network_admin() && $wp_admin_bar->get_node('comments')) {
            // Comments
            $comments_node = $wp_admin_bar->get_node('comments');
            // Add classes
            $comments_node->meta['class'] = 'force-mdi';
            // Ajust title
            $comments_node->title = str_replace('<span class="ab-icon"></span>', '<i class="mdi-notification-sms"></i>', $comments_node->title);
            $comments = [
                'id' => $comments_node->id,
                'title' => $comments_node->title,
                'href' => $comments_node->href,
                'parent' => $comments_node->parent,
                'meta' => $comments_node->meta,
            ];

            // Edited comments
            $wp_admin_bar->add_node($comments);
        } // end if;

        // help
        $help = [
            'id' => 'mwp-help',
            'title' => '<i class="mdi-action-help"></i>',
            'href' => wp_logout_url(),
            'parent' => 'top-secondary',
            'meta' => [
                'class' => 'force-mdi tooltiped tooltip-ajust',
                'title' => __('Help', 'material-wp'),
            ],
        ];

        // Settings
        $settings = [
            'id' => 'mwp-settings',
            'title' => '<i class="mdi-action-settings"></i>',
            'href' => admin_url('options-general.php'),
            'parent' => 'top-secondary',
            'meta' => [
                'class' => 'force-mdi tooltiped tooltip-ajust',
                'title' => __('Settings', 'material-wp'),
            ],
        ];

        // Logout
        $logout = [
            'id' => 'mwp-logout',
            'title' => '<i class="mdi-action-exit-to-app"></i>',
            'href' => wp_logout_url(),
            'parent' => 'top-secondary',
            'meta' => [
                'class' => 'force-mdi tooltiped tooltip-ajust',
                'title' => __('Logout', 'material-wp'),
            ],
        ];

        // Editted My Sites
        // $wp_admin_bar->add_node($mysite);

        // Readd the user card
        if ($this->options->getOption('admin-bar-user-card')) {
            // $userCard->title = str_replace('Howdy,', __('Welcome back,', 'pro-theme'), $userCard->title);
            $wp_admin_bar->add_node($userCard);
        }

        // Else, we add just the logout
        else {
            // Add logout
            $wp_admin_bar->add_node($logout);
        }

        // Add settings
        if (current_user_can('manage_options') && $this->options->getOption('menu-settings-link')) {
            $wp_admin_bar->add_node($settings);
        }
        // Add help
    // $wp_admin_bar->add_node($help);
    }

    /**
     * Call private functions/actions in children class;
     * private_theme_functions() called into NextPress_Theme_Factory::Plugin().
     */
    public function private_theme_functions() {
        // Adds plus button
        add_action('in_admin_header', [&$this, 'addParallaxBlock'], -200);

        // Replace the common scripts
        add_action('wp_default_scripts', [$this, 'changeCommonScript'], 11);
    }

    protected function initialize() {
        $this->_domain = 'material-wp';
    }
} // end MaterialWP;

function initialize_material_wp() {
    if (class_exists('NextPress_Theme_Factory')) {
        // Now we need to load our config file
        $config = include plugin_dir_path(__FILE__).'/config.php';

        /**
         * We execute our plugin, passing our config file.
         */
        $MaterialWP = new MaterialWP($config);
        $MaterialWP->_refer_js = 'materialwpl10n';
    }
}

/*
 * Load the MaterialWP
 * @since 1.1.0
 */
add_action('plugins_loaded', 'initialize_material_wp', 11);
