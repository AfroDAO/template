<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Admin Class
 *
 * Handles generic Admin functionality and AJAX requests.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
class Wpw_Auto_Posting_AdminPages {

    public $scripts, $model, $render, $message, $logs,
            $fbposting, $twposting, $liposting, $insposting, $admin, $pinposting;

    public function __construct() {

        global $wpw_auto_poster_scripts, $wpw_auto_poster_model, $wpw_auto_poster_render, $wpw_auto_poster_message_stack,
        $wpw_auto_poster_fb_posting, $wpw_auto_poster_tw_posting, $wpw_auto_poster_li_posting, $wpw_auto_poster_ba_posting,
        $wpw_auto_poster_tb_posting, $wpw_auto_poster_ins_posting, $wpw_auto_poster_logs, $wpw_auto_poster_admin, $wpw_auto_poster_pin_posting;

        $this->scripts = $wpw_auto_poster_scripts;
        $this->model = $wpw_auto_poster_model;
        $this->render = $wpw_auto_poster_render;
        $this->message = $wpw_auto_poster_message_stack;
        $this->logs = $wpw_auto_poster_logs;
        $this->admin = $wpw_auto_poster_admin;

        //social posting class objects
        $this->fbposting = $wpw_auto_poster_fb_posting;
        $this->twposting = $wpw_auto_poster_tw_posting;
        $this->liposting = $wpw_auto_poster_li_posting;
        $this->tbposting = $wpw_auto_poster_tb_posting;
        $this->baposting = $wpw_auto_poster_ba_posting;
        $this->insposting = $wpw_auto_poster_ins_posting;
        $this->pinposting = $wpw_auto_poster_pin_posting;

    }

    /**
     * Register Settings
     *
     * Runs when the admin_init hook fires and registers
     * the plugin settings with the WordPress settings API.
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    function wpw_auto_poster_init() {

        register_setting('wpw_auto_poster_plugin_options', 'wpw_auto_poster_options', array($this, 'wpw_auto_poster_validate_options'));
    }

    /**
     * Validation/Sanitization
     *
     * Sanitize and validate input fields.
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_validate_options($input) {

        global $wpw_auto_poster_options;

        $social_types_arr = $this->model->wpw_auto_poster_get_social_type_data();

        /****  Category and Tag handling start ****/

        foreach($social_types_arr as $prefix => $value) {

            if( !empty( $input['enable_'.$value.'_for'] ) ) {
                $prevent_meta = $input['enable_'.$value.'_for'];
            }

            // Custom post type tag taxonomy code
            if(!empty($input[$prefix.'_post_type_tags'])) {

                $post_type_tags = $input[$prefix.'_post_type_tags'];
                
                if(!empty($prevent_meta)) {

                    $wpw_auto_poster_tags =  array();
                    foreach ($post_type_tags as $post_type_tag) {

                        $tagData = explode("|",$post_type_tag);
                        $post_type = $tagData[0];
                        $post_tag = $tagData[1];
                        if(in_array( $post_type, $prevent_meta )){
                            $wpw_auto_poster_tags[$post_type][] = $post_tag;
                        }
                    }
                    $input[$prefix.'_post_type_tags'] = $wpw_auto_poster_tags;
                }
            }

            // Custom post type category taxonomy code
            if(!empty($input[$prefix.'_post_type_cats'])) {

                $post_type_cats = $input[$prefix.'_post_type_cats'];

                if(!empty($prevent_meta)) {

                    $wpw_auto_poster_cats =  array();
                    foreach ($post_type_cats as $post_type_cat) {

                        $tagData = explode("|",$post_type_cat);
                        $post_type = $tagData[0];
                        $post_cat = $tagData[1];
                        if(in_array( $post_type, $prevent_meta )){
                            $wpw_auto_poster_cats[$post_type][] = $post_cat;
                        }
                    }
                    $input[$prefix.'_post_type_cats'] = $wpw_auto_poster_cats;
                }
            }
        }

        /****  Category and Tag handling end ****/

        /*** Excluding Category handling start ***/

        foreach($social_types_arr as $prefix => $value) {

            // Custom post type exclude category code
            if( !empty( $input[$prefix.'_exclude_cats']) ) {

                $post_type_exclude_cats         = $input[$prefix.'_exclude_cats'];
                $wpw_auto_poster_exclude_cats   =  array();

                foreach ( $post_type_exclude_cats as $post_type_exclude_cat ) {

                    $tagData    = explode("|",$post_type_exclude_cat);
                    $post_type  = $tagData[0]; // post type
                    $cat_slug   = $tagData[1]; // category slug
                    
                    $wpw_auto_poster_exclude_cats[$post_type][] = $cat_slug;
                }

                $input[$prefix.'_exclude_cats'] = $wpw_auto_poster_exclude_cats;
            }

        }
        /*** Excluding Category handling end ***/

        //Facebook Settings Options
        //$input['fb_bitly_username']		=	$this->model->wpw_auto_poster_stripslashes_deep( $input['fb_bitly_username'] );
        $input['fb_bitly_access_token'] = $this->model->wpw_auto_poster_stripslashes_deep($input['fb_bitly_access_token']);
        $input['facebook_keys'] = isset($input['facebook_keys']) ? $this->model->wpw_auto_poster_stripslashes_deep($input['facebook_keys']) : '';

        if( !empty( $input['custom_status_msg'] ) ) {
            $input['custom_status_msg'] = $this->model->wpw_auto_poster_stripslashes_deep($input['custom_status_msg']);
        }

        $input['fb_custom_img'] = ( isset( $input['fb_custom_img'] ) ) ? $this->model->wpw_auto_poster_stripslashes_deep($input['fb_custom_img']) : '';

        // Get facebook account details
        if (!empty($input['facebook_keys'])) {

            $facebook_keys = $input['facebook_keys'];

            // Check difference of arrays
            $facebook_keys_old_data = $this->model->wpw_auto_poster_get_one_dim_array($wpw_auto_poster_options['facebook_keys']);
            $facebook_keys_new_data = $this->model->wpw_auto_poster_get_one_dim_array($facebook_keys);

            $facebook_keys_result = array_diff($facebook_keys_new_data, $facebook_keys_old_data);
            $facebook_keys_result_vise = array_diff($facebook_keys_old_data, $facebook_keys_new_data);

            // Check any one array is different then reindex all values so if any blank row set it will not consider it.
            if (!empty($facebook_keys_result) || !empty($facebook_keys_result_vise)) {

                $new_fb_keys = array();
                $fb_count_key = 0;
                $wpw_auto_poster_facebook_keys = array();

                foreach ($facebook_keys as $fb_key => $fb_value) {

                    $fb_app_id = trim($fb_value['app_id']);
                    $fb_app_secret = trim($fb_value['app_secret']);

                    if (!empty($fb_app_id) || !empty($fb_app_secret)) { // Check any one key is set as not empty
                        $wpw_auto_poster_facebook_keys[$fb_count_key]['app_id'] = $fb_app_id;
                        $wpw_auto_poster_facebook_keys[$fb_count_key]['app_secret'] = $fb_app_secret;

                        $fb_count_key++;
                    }

                    // Just taking fb app ids
                    if (!empty($fb_app_id) && !empty($fb_app_secret)) {
                        $new_fb_keys[] = $fb_app_id;
                    }
                }
                $input['facebook_keys'] = $wpw_auto_poster_facebook_keys;

                /*                 * *** Reset facebook session data is app key or appid is deleted **** */
                // Note : wpw_auto_poster_fb_reset_session() Function is called just to flush the session variable not options
                // If data is not empty then check which existing key
                $wpw_auto_poster_fb_sess_data = get_option('wpw_auto_poster_fb_sess_data');

                // Getting facebook keys from the stored session data
                $old_fb_keys = (!empty($wpw_auto_poster_fb_sess_data) && is_array($wpw_auto_poster_fb_sess_data) ) ? array_keys($wpw_auto_poster_fb_sess_data) : array();

                // Getting difference between stored fb keys and setting fb keys
                $diff_fb_keys = array_diff($old_fb_keys, $new_fb_keys);

                if (!empty($diff_fb_keys)) {

                    $this->fbposting->wpw_auto_poster_fb_reset_session(); // Flush session variable

                    foreach ($diff_fb_keys as $flush_app_key => $flush_app_data) {
                        // Removing app data from the stored fb session data
                        if (isset($wpw_auto_poster_fb_sess_data[$flush_app_data])) {
                            unset($wpw_auto_poster_fb_sess_data[$flush_app_data]);
                        }
                    }

                    // Updating stored fb session data
                    update_option('wpw_auto_poster_fb_sess_data', $wpw_auto_poster_fb_sess_data);
                }
                /*                 * *** Reset facebook session ends **** */
            }
            // end code for reindexing
        }

        //Instagram Settings Options
        $input['ins_bitly_access_token'] = $this->model->wpw_auto_poster_stripslashes_deep($input['ins_bitly_access_token']);
        $input['instagram_keys'] = isset($input['instagram_keys']) ? $this->model->wpw_auto_poster_stripslashes_deep($input['instagram_keys']) : '';
        $input['ins_template'] = $this->model->wpw_auto_poster_stripslashes_deep($input['ins_template']);
        $input['ins_custom_img'] = ( isset( $input['ins_custom_img'] ) ) ? $this->model->wpw_auto_poster_stripslashes_deep($input['ins_custom_img']) : '';
      
        // Get instagram account details
        if (!empty($input['instagram_keys'])) {

            $instagram_keys = $input['instagram_keys'];

            // Check difference of arrays
            $instagram_keys_old_data = $this->model->wpw_auto_poster_get_one_dim_array($wpw_auto_poster_options['instagram_keys']);
            $instagram_keys_new_data = $this->model->wpw_auto_poster_get_one_dim_array($instagram_keys);

            $instagram_keys_result = array_diff($instagram_keys_new_data, $instagram_keys_old_data);
            $instagram_keys_result_vise = array_diff($instagram_keys_old_data, $instagram_keys_new_data);

            // Check any one array is different then reindex all values so if any blank row set it will not consider it.
            if (!empty($instagram_keys_result) || !empty($instagram_keys_result_vise)) {

                $new_ins_keys = array();
                $ins_count_key = 0;
                $wpw_auto_poster_instagram_keys = array();

                foreach ($instagram_keys as $ins_key => $ins_value) {
                    
                    if( !is_array( $ins_value ) )
                        continue;

                    $ins_username = trim($ins_value['username']);
                    $ins_password = trim($ins_value['password']);

                    if (!empty($ins_username) || !empty($ins_password)) { // Check any one key is set as not empty
                        $wpw_auto_poster_instagram_keys[$ins_count_key]['username'] = $ins_username;
                        $wpw_auto_poster_instagram_keys[$ins_count_key]['password'] = $ins_password;

                        $ins_count_key++;
                    }

                    // Just taking fb app ids
                    if (!empty($ins_username) && !empty($ins_password)) {
                        $new_ins_keys[] = $ins_username ."|".$ins_password;
                    }
                }
                $input['instagram_keys'] = $wpw_auto_poster_instagram_keys;

                //Update instagram acoount details
                update_option('wpw_auto_poster_ins_account_details', $new_ins_keys);
            }
            // end code for reindexing
        }

        //Twitter Settings Options
        $input['tw_tweet_img'] = $this->model->wpw_auto_poster_stripslashes_deep($input['tw_tweet_img']);
        //$input['tw_bitly_username']		=	$this->model->wpw_auto_poster_stripslashes_deep( $input['tw_bitly_username'] );
        $input['tw_bitly_access_token'] = $this->model->wpw_auto_poster_stripslashes_deep($input['tw_bitly_access_token']);
        $input['twitter_keys'] = isset($input['twitter_keys']) ? $this->model->wpw_auto_poster_stripslashes_deep($input['twitter_keys']) : '';

        //Get twitter account details
        if (!empty($input['twitter_keys'])) {

            //Get twitter account details
            $tw_account_details = array();

            $twitter_keys = $input['twitter_keys'];

            //Check difference of arrays
            $twitter_keys_old_data = $this->model->wpw_auto_poster_get_one_dim_array($wpw_auto_poster_options['twitter_keys']);
            $twitter_keys_new_data = $this->model->wpw_auto_poster_get_one_dim_array($twitter_keys);

            $twitter_keys_result = array_diff($twitter_keys_new_data, $twitter_keys_old_data);
            $twitter_keys_result_vise = array_diff($twitter_keys_old_data, $twitter_keys_new_data);

            // Check any one array is different
            if (!empty($twitter_keys_result) || !empty($twitter_keys_result_vise)) {

                $tw_count_key = 0;
                $wpw_auto_poster_twitter_keys = array();
                foreach ($twitter_keys as $tw_key => $tw_value) {

                    $tw_consumer_key = trim($tw_value['consumer_key']);
                    $tw_consumer_secret = trim($tw_value['consumer_secret']);
                    $tw_auth_token = trim($tw_value['oauth_token']);
                    $tw_auth_token_secret = trim($tw_value['oauth_secret']);

                    if (!empty($tw_consumer_key) || !empty($tw_consumer_secret) || !empty($tw_auth_token) || !empty($tw_auth_token_secret)) { // Check any one key is set as not empty
                        $wpw_auto_poster_twitter_keys[$tw_count_key]['consumer_key'] = $tw_consumer_key;
                        $wpw_auto_poster_twitter_keys[$tw_count_key]['consumer_secret'] = $tw_consumer_secret;
                        $wpw_auto_poster_twitter_keys[$tw_count_key]['oauth_token'] = $tw_auth_token;
                        $wpw_auto_poster_twitter_keys[$tw_count_key]['oauth_secret'] = $tw_auth_token_secret;

                        $tw_count_key = $tw_count_key + 1;
                        $user_profile_data = $this->twposting->wpw_auto_poster_get_user_data($tw_consumer_key, $tw_consumer_secret, $tw_auth_token, $tw_auth_token_secret);
                        if (!empty($user_profile_data)) { // Check user data are not empty
                            if (isset($user_profile_data->name) && !empty($user_profile_data->name)) { // Check user name is not empty
                                $tw_account_details[$tw_count_key] = $user_profile_data->name;
                            }
                        }
                    }
                }

                $input['twitter_keys'] = $wpw_auto_poster_twitter_keys;

                //Update twitter acoount details
                update_option('wpw_auto_poster_tw_account_details', $tw_account_details);

                /*                 * ***** Code for selected category Twitter account ***** */

                // unset selected twitter account option for category
                $cat_selected_social_acc = array();
                $cat_selected_acc = get_option('wpw_auto_poster_category_posting_acct');
                $cat_selected_social_acc = (!empty($cat_selected_acc) ) ? $cat_selected_acc : $cat_selected_social_acc;

                if (!empty($cat_selected_social_acc)) {
                    foreach ($cat_selected_social_acc as $cat_id => $cat_social_acc) {
                        if (isset($cat_social_acc['tw'])) {
                            if (!empty($cat_social_acc['tw'])) {
                                $new_cat_stored_users = array_diff($cat_social_acc['tw'], $tw_account_details);
                                if (!empty($new_cat_stored_users)) {
                                    $cat_selected_acc[$cat_id]['tw'] = $new_cat_stored_users;
                                } else {
                                    unset($cat_selected_acc[$cat_id]['tw']);
                                }
                            } else {
                                unset($cat_selected_acc[$cat_id]['tw']);
                            }
                        }
                    }

                    // Update autoposter category FB posting account options
                    update_option('wpw_auto_poster_category_posting_acct', $cat_selected_acc);
                }
            }
        }

        //LinkedIn Settings Options
        //$input['li_bitly_username']		=	$this->model->wpw_auto_poster_stripslashes_deep( $input['li_bitly_username'] );
        $input['li_bitly_access_token'] = $this->model->wpw_auto_poster_stripslashes_deep($input['li_bitly_access_token']);
        $input['linkedin_app_id'] = $this->model->wpw_auto_poster_stripslashes_deep($input['linkedin_app_id']);
        $input['linkedin_app_secret'] = $this->model->wpw_auto_poster_stripslashes_deep($input['linkedin_app_secret']);
        $input['li_post_image'] = $this->model->wpw_auto_poster_stripslashes_deep($input['li_post_image']);

        //linkedin application id or secret blank or change then reset session
        if (( empty($input['linkedin_app_id']) || empty($input['linkedin_app_secret']) ) || ( $wpw_auto_poster_options['linkedin_app_id'] != $input['linkedin_app_id'] ) || ( $wpw_auto_poster_options['linkedin_app_secret'] != $input['linkedin_app_secret'] )) {
            //reset linkedin session data
            $this->liposting->wpw_auto_poster_li_reset_session();
        }

        //Tumblr Settings Options
        //$input['tb_bitly_username']		=	$this->model->wpw_auto_poster_stripslashes_deep( $input['tb_bitly_username'] );
        $input['tb_bitly_access_token'] = $this->model->wpw_auto_poster_stripslashes_deep($input['tb_bitly_access_token']);
        $input['tumblr_consumer_key'] = $this->model->wpw_auto_poster_stripslashes_deep($input['tumblr_consumer_key']);
        $input['tumblr_consumer_secret'] = $this->model->wpw_auto_poster_stripslashes_deep($input['tumblr_consumer_secret']);

        //tumblr consumer key or secret balnk or change then reset session
        if (( empty($input['tumblr_consumer_key']) || empty($input['tumblr_consumer_secret']) ) || ( $wpw_auto_poster_options['tumblr_consumer_key'] != $input['tumblr_consumer_key'] ) || ( $wpw_auto_poster_options['tumblr_consumer_secret'] != $input['tumblr_consumer_secret'] )) {
            //reset tumblr session data
            $this->tbposting->wpw_auto_poster_tb_reset_session();
        }

        // BufferApp Settings Option
        //$input['ba_bitly_username']		=	$this->model->wpw_auto_poster_stripslashes_deep( $input['ba_bitly_username'] );
        $input['ba_bitly_access_token'] = $this->model->wpw_auto_poster_stripslashes_deep($input['ba_bitly_access_token']);
        $input['bufferapp_client_id'] = $this->model->wpw_auto_poster_stripslashes_deep($input['bufferapp_client_id']);
        $input['bufferapp_client_secret'] = $this->model->wpw_auto_poster_stripslashes_deep($input['bufferapp_client_secret']);
        $input['ba_post_img'] = $this->model->wpw_auto_poster_stripslashes_deep($input['ba_post_img']);

        //BufferApp client id or secret blank or change then reset session
        if (( empty($input['bufferapp_client_id']) || empty($input['bufferapp_client_secret']) ) || ( $wpw_auto_poster_options['bufferapp_client_id'] != $input['bufferapp_client_id'] ) || ( $wpw_auto_poster_options['bufferapp_client_secret'] != $input['bufferapp_client_secret'] )) {
            //reset bufferapp session data
            $this->baposting->wpw_auto_poster_ba_reset_session();
        }

        //Pinterest Settings Options
        $input['pin_bitly_access_token'] = $this->model->wpw_auto_poster_stripslashes_deep($input['pin_bitly_access_token']);
        $input['pinterest_keys'] = isset($input['pinterest_keys']) ? $this->model->wpw_auto_poster_stripslashes_deep($input['pinterest_keys']) : '';
        $input['pin_custom_template'] = $this->model->wpw_auto_poster_stripslashes_deep($input['pin_custom_template']);
        $input['pin_custom_img'] = ( isset( $input['pin_custom_img'] ) ) ? $this->model->wpw_auto_poster_stripslashes_deep($input['pin_custom_img']) : '';

        // Get pinterest account details
        if (!empty($input['pinterest_keys'])) {

            $pinterest_keys = $input['pinterest_keys'];

            // Check difference of arrays
            $pinterest_keys_old_data = $this->model->wpw_auto_poster_get_one_dim_array($wpw_auto_poster_options['pinterest_keys']);
            $pinterest_keys_new_data = $this->model->wpw_auto_poster_get_one_dim_array($pinterest_keys);

            $pinterest_keys_result = array_diff($pinterest_keys_new_data, $pinterest_keys_old_data);
            $pinterest_keys_result_vise = array_diff($pinterest_keys_old_data, $pinterest_keys_new_data);

            // Check any one array is different then reindex all values so if any blank row set it will not consider it.
            if (!empty($pinterest_keys_result) || !empty($pinterest_keys_result_vise)) {

                $new_pin_keys = array();
                $pin_count_key = 0;
                $wpw_auto_poster_pinterest_keys = array();

                foreach ($pinterest_keys as $pin_key => $pin_value) {

                    $pin_app_id = trim($pin_value['app_id']);
                    $pin_app_secret = trim($pin_value['app_secret']);

                    if (!empty($pin_app_id) || !empty($pin_app_secret)) { // Check any one key is set as not empty
                        $wpw_auto_poster_pinterest_keys[$pin_count_key]['app_id'] = $pin_app_id;
                        $wpw_auto_poster_pinterest_keys[$pin_count_key]['app_secret'] = $pin_app_secret;

                        $pin_count_key++;
                    }

                    // Just taking pin app ids
                    if (!empty($pin_app_id) && !empty($pin_app_secret)) {
                        $new_pin_keys[] = $pin_app_id;
                    }
                }
                $input['pinterest_keys'] = $wpw_auto_poster_pinterest_keys;

                /*                 * *** Reset pinterest session data is app key or appid is deleted **** */
                // Note : wpw_auto_poster_pin_reset_session() Function is called just to flush the session variable not options
                // If data is not empty then check which existing key
                $wpw_auto_poster_pin_sess_data = get_option('wpw_auto_poster_pin_sess_data');

                // Getting pinterest keys from the stored session data
                $old_pin_keys = (!empty($wpw_auto_poster_pin_sess_data) && is_array($wpw_auto_poster_pin_sess_data) ) ? array_keys($wpw_auto_poster_pin_sess_data) : array();

                // Getting difference between stored pinterest keys and setting pinterest keys
                $diff_pin_keys = array_diff($old_pin_keys, $new_pin_keys);

                if (!empty($diff_pin_keys)) {

                    $this->pinposting->wpw_auto_poster_pin_reset_session(); // Flush session variable

                    foreach ($diff_pin_keys as $flush_app_key => $flush_app_data) {
                        // Removing app data from the stored pinterest session data
                        if (isset($wpw_auto_poster_pin_sess_data[$flush_app_data])) {
                            unset($wpw_auto_poster_pin_sess_data[$flush_app_data]);
                        }
                    }

                    // Updating stored pinterest session data
                    update_option('wpw_auto_poster_pin_sess_data', $wpw_auto_poster_pin_sess_data);
                }
                /*                 * *** Reset pinterest session ends **** */
            }
            // end code for reindexing
        }

        //set session to set tab selected in settings page
        $selectedtab = isset($input['selected_tab']) ? $input['selected_tab'] : '';
        $this->message->add_session('poster-selected-tab', strtolower($selectedtab));

        // apply filters for validate settings
        $input = apply_filters('wpw_auto_poster_validate_settings', $input, $wpw_auto_poster_options);

        return $input;
    }

    /**
     * Add Top Level Menu Page
     *
     * Runs when the admin_menu hook fires and adds a new
     * top level admin page and menu item.
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    function wpw_auto_poster_add_settings_page() {

        global $post;

        // plugin settings page
        $wpw_auto_poster_admin = add_menu_page(__('Social Auto Poster', 'wpwautoposter'), __('Social Auto Poster', 'wpwautoposter'), wpwautoposterlevel, 'wpw-auto-poster-settings', '', WPW_AUTO_POSTER_IMG_URL . '/wpw-auto-poster-icon.png');

        $wpw_auto_poster_admin = add_submenu_page('wpw-auto-poster-settings', __('Social Auto Poster Settings', 'wpwautoposter'), __('Settings', 'wpwautoposter'), wpwautoposterlevel, 'wpw-auto-poster-settings', array($this, 'wpw_auto_poster_settings_page'));
        $wpw_auto_poster_posted_logs = add_submenu_page('wpw-auto-poster-settings', __('Social Auto Poster Posting Logs', 'wpwautoposter'), __('Social Posting Logs', 'wpwautoposter'), wpwautoposterlevel, 'wpw-auto-poster-posted-logs', array($this, 'wpw_auto_poster_posted_logs_page'));

        //Page for Manage post schedules
        $wpw_auto_poster_manage_schedules = add_submenu_page('wpw-auto-poster-settings', __('Manage Schedules', 'wpwautoposter'), __('Manage Schedules', 'wpwautoposter'), wpwautoposterlevel, 'wpw-auto-poster-manage-schedules', array($this, 'wpw_auto_poster_manage_schedules_page'));

        add_action("admin_head-$wpw_auto_poster_admin", array($this->scripts, 'wpw_auto_poster_settings_page_load_scripts'));
    }

    /**
     * Settings Page
     *
     * Renders the plugin settings page.
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    function wpw_auto_poster_settings_page() {

        include_once( WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-settings-hooks.php' );
        include_once( WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-plugin-settings.php' );
    }

	/**
     * Posted Logs List
     *
     * Renders the posted logs list page.
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    function wpw_auto_poster_posted_logs_page() {
	?>
		<div class="wrap">
   			 <!-- wpweb logo -->
			<img src="<?php echo WPW_AUTO_POSTER_IMG_URL . '/wpw-auto-poster-logo.png'; ?>" class="wpw-auto-poster-logo" alt="<?php _e( 'Logo', 'wpwautoposter' );?>" />
			<h2><?php _e( 'Social Posting Logs', 'wpwautoposter' ); ?></h2>

			<div class="content">
				<h2 class="nav-tab-wrapper wpw-auto-poster-h2">
					<a class="nav-tab nav-tab-active" href="#wpw-auto-poster-tab-logs" attr-tab="sap-logs"><?php _e( 'Posting Logs', ''); ?></a>
					<a class="nav-tab" href="#wpw-auto-poster-tab-reports" attr-tab="sap-reports"><?php _e( 'Posting Reports', ''); ?></a>
				</h2>
				<div class="wpw-auto-poster-content">
					<div class="wpw-auto-poster-tab-content" id="wpw-auto-poster-tab-logs" style="display:block">
						<?php
			        	include( WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-posted-logs-list.php' ); ?>
			        </div>
			        <div class="wpw-auto-poster-tab-content" id="wpw-auto-poster-tab-reports">
			        	<?php
			        	include( WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-posted-logs-reports.php' ); ?>
			        </div>
			    </div>
	        </div>
        </div>
        <?php 
    }

    /**
     * Post Scheduling
     *
     * Renders the manage posts schedule list page.
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    function wpw_auto_poster_manage_schedules_page() {

        include( WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-manage-schedules-list.php' );
    }

    /**
     * Post to Social Medias
     *
     * Handles to post to social media
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_social_posting($post, $scheduled = false) {

        global $wpw_auto_poster_options, $postedstr, $schedulepoststr;

        // get all supported network list array
        $all_social_networks = $this->model->wpw_auto_poster_get_social_type_name();

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        $postedstr = $schedulepoststr = array();

        $postid = $post->ID;

        $post_type = $post->post_type; // Post type

        // get selected categories slugs for a post
        $post_catgeories = wpw_auto_poster_get_post_categories( $post_type, $postid );

        /** Code to exclude posting for selected category start **/         
        $main_exclude_arr = array(); // define main category exclude array for a post.


        // Initially set exclude flag to false at the begining
        $main_exclude_arr['fb'] = $main_exclude_arr['tw'] = $main_exclude_arr['li'] = $main_exclude_arr['tb'] = $main_exclude_arr['ba'] = $main_exclude_arr['ins'] = $main_exclude_arr['pin'] = false;
       
        // Loop all the supported social networks
        foreach($all_social_networks as $slug => $label) {

            // get selected categories to exclude for each social network
            $exclude_cats = !empty($wpw_auto_poster_options[$slug.'_exclude_cats']) ? $wpw_auto_poster_options[$slug.'_exclude_cats'] : array();

            // Loop through all the categories of a particualr post.
            foreach($post_catgeories as $category) {

                // Check if excluded category is selected for the current post type.
                if(!empty($exclude_cats[$post_type])) {
                    // If atleast one excluded category matches with the post categories than make flag as true
                    if(in_array($category, $exclude_cats[$post_type])){

                        // make social network exclude flag true, if atleast one excluded category matches
                        $main_exclude_arr[$slug] = true;
                        continue;
                    }
                }
            }
        }

        /** Code to exclude posting for selected category end **/

        //Facebook Posting
        $facebookarr = !empty($wpw_auto_poster_options['enable_facebook_for']) ? $wpw_auto_poster_options['enable_facebook_for'] : array();

        //get post published on facebook
        $fb_published = get_post_meta($postid, $prefix . 'fb_published_on_fb', true);

        //record logs for facebook posting
        $this->logs->wpw_auto_poster_add('Facebook Posting | ' . $post->post_type . ' | ' . $postid, true);

        $schedule_post_to = get_post_meta($postid, $prefix . 'schedule_wallpost', true);
        $schedule_post_to = !empty($schedule_post_to) ? $schedule_post_to : array();

        $post_to_facebook = get_post_meta($postid, $prefix . 'post_to_facebook', true);

        //Check If post is already published and there is disable from metabox but it has checked in backend
        //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_facebook']) && (!isset($fb_published) || $fb_published == false ) && in_array($post->post_type, $facebookarr) ) || ( isset($_POST[$prefix . 'post_to_facebook']) && $_POST[$prefix . 'post_to_facebook'] == 'on' ) || ( $scheduled === true && $post_to_facebook == 'on' )) {
			

			if( !$main_exclude_arr['fb'] ) {

	            if (empty($wpw_auto_poster_options['schedule_wallpost_option'])) { // Check schedule option is "Instantly"
	                //post to user wall on facebook
	                $fb_result = $this->fbposting->wpw_auto_poster_fb_posting($post);
	                if ($fb_result) {
	                    $postedstr[] = 'fb';
	                }
	            } else {
	
	                if (!in_array('facebook', $schedule_post_to)) {
	                    $schedule_post_to[] = 'facebook';
	                }
	                $schedulepoststr[] = 'fb';
	
	                //Update facebook status to scheduled
	                update_post_meta($postid, $prefix . 'fb_published_on_fb', 2);
	
	            }
			}
        }

        //record logs for twitter posting
        $this->logs->wpw_auto_poster_add('Twitter Posting | ' . $post->post_type . ' | ' . $postid, true);

        //Twitter Posting
        $twitterarr = !empty($wpw_auto_poster_options['enable_twitter_for']) ? $wpw_auto_poster_options['enable_twitter_for'] : array();

        $tw_published = get_post_meta($postid, $prefix . 'tw_status', true);

        //Check If post is already published and there is disable from metabox but it has checked in backend
        //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_twitter']) && (!isset($tw_published) || $tw_published == false ) && in_array($post->post_type, $twitterarr) ) || ( isset($_POST[$prefix . 'post_to_twitter']) && $_POST[$prefix . 'post_to_twitter'] == 'on' )) {

			if( !$main_exclude_arr['tw'] ) {

	            if (empty($wpw_auto_poster_options['schedule_wallpost_option'])) { // Check schedule option is "Instantly"
	                //post to twitter
	                $tw_result = $this->twposting->wpw_auto_poster_tw_posting($post);
	                if ($tw_result) {
	                    $postedstr[] = 'tw';
	                }
	            } else {
	
	                if (!in_array('twitter', $schedule_post_to)) {
	                    $schedule_post_to[] = 'twitter';
	                }
	                $schedulepoststr[] = 'tw';
	
	                //Update twitter status to scheduled
	                update_post_meta($postid, $prefix . 'tw_status', 2);
	
	            }
			}
        }

        //record logs for linkedin posting
        $this->logs->wpw_auto_poster_add('LinkedIn Posting | ' . $post->post_type . ' | ' . $postid, true);

        //LinkedIn Posting
        $linkedinarr = !empty($wpw_auto_poster_options['enable_linkedin_for']) ? $wpw_auto_poster_options['enable_linkedin_for'] : array();

        $li_published = get_post_meta($postid, $prefix . 'li_status', true);


        //Check If post is already published and there is disable from metabox but it has checked in backend
        //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_linkedin']) && (!isset($li_published) || $li_published == false ) && in_array($post->post_type, $linkedinarr) ) || ( isset($_POST[$prefix . 'post_to_linkedin']) && $_POST[$prefix . 'post_to_linkedin'] == 'on' )) {
			if( !$main_exclude_arr['li'] ) {

	            if (empty($wpw_auto_poster_options['schedule_wallpost_option'])) { // Check schedule option is "Instantly"
	                //post to linkedin
	                $li_result = $this->liposting->wpw_auto_poster_li_posting($post);
	                if ($li_result) {
	                    $postedstr[] = 'li';
	                }
	            } else {
	
	                if (!in_array('linkedin', $schedule_post_to)) {
	                    $schedule_post_to[] = 'linkedin';
	                }
	                $schedulepoststr[] = 'li';
	
	                //Update linkedin status to scheduled
	                update_post_meta($postid, $prefix . 'li_status', 2);
	
	            }
			}
        }

        //record logs for Tumblr posting
        $this->logs->wpw_auto_poster_add('Tumblr Posting | ' . $post->post_type . ' | ' . $postid, true);

        //Tumblr Posting
        $tumblrarr = !empty($wpw_auto_poster_options['enable_tumblr_for']) ? $wpw_auto_poster_options['enable_tumblr_for'] : array();

        $tb_published = get_post_meta($postid, $prefix . 'tb_status', true);

        //Check If post is already published and there is disable from metabox but it has checked in backend
        //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_tumblr']) && (!isset($tb_published) || $tb_published == false ) && in_array($post->post_type, $tumblrarr) ) || ( isset($_POST[$prefix . 'post_to_tumblr']) && !empty($_POST[$prefix . 'post_to_tumblr']) )) {
			if( !$main_exclude_arr['tb'] ) {

	            if (empty($wpw_auto_poster_options['schedule_wallpost_option'])) { // Check schedule option is "Instantly"
	                //post to tumblr
	                $tb_result = $this->tbposting->wpw_auto_poster_tb_posting($post);
	                if ($tb_result) {
	                    $postedstr[] = 'tb';
	                }
	            } else {
	
	                if (!in_array('tumblr', $schedule_post_to)) {
	                    $schedule_post_to[] = 'tumblr';
	                }
	                $schedulepoststr[] = 'tb';
	
	                //Update tumblr status to scheduled
	                update_post_meta($postid, $prefix . 'tb_status', 2);
	
	            }
			}
        }

        //record logs for BufferApp posting
        $this->logs->wpw_auto_poster_add('BufferApp Posting | ' . $post->post_type . ' | ' . $postid, true);

        //bufferapp Posting
        $bufferapparr = !empty($wpw_auto_poster_options['enable_bufferapp_for']) ? $wpw_auto_poster_options['enable_bufferapp_for'] : array();

        $ba_published = get_post_meta($postid, $prefix . 'ba_status', true);

        if ((!empty($wpw_auto_poster_options['enable_bufferapp']) && (!isset($ba_published) || $ba_published == false ) && in_array($post->post_type, $bufferapparr) ) || ( isset($_POST[$prefix . 'post_to_bufferapp']) && !empty($_POST[$prefix . 'post_to_bufferapp']) )) { //if tumblr is seleectd then post to bufferapp account
            if( !$main_exclude_arr['ba'] ) {
				if (empty($wpw_auto_poster_options['schedule_wallpost_option'])) { // Check schedule option is "Instantly"
	                //post to bufferapp
	                $ba_result = $this->baposting->wpw_auto_poster_ba_posting($post);
	                if ($ba_result) {
	                    $postedstr[] = 'ba';
	                }
	            } else {
	
	                if (!empty($_SESSION['wpw_auto_poster_ba_user_id'])) {
	
	                    if (!in_array('bufferapp', $schedule_post_to)) {
	                        $schedule_post_to[] = 'bufferapp';	
	                    }
	                    $schedulepoststr[] = 'ba';
	
	                    //Update bufferapp status to scheduled
	                    update_post_meta($postid, $prefix . 'ba_status', 2);
	
	                }
	            }
			}
        }

        $this->logs->wpw_auto_poster_add('Instagram Posting | ' . $post->post_type . ' | ' . $postid, true);
         //Instagram Posting
        $instaarr = !empty($wpw_auto_poster_options['enable_instagram_for']) ? $wpw_auto_poster_options['enable_instagram_for'] : array();
        $ins_published = get_post_meta($postid, $prefix . 'ins_published_on_ins', true);

        //Check If post is already published and there is disable from metabox but it has checked in backend
        //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_instagram']) && (!isset($ins_published) || $ins_published == false ) && in_array($post->post_type, $instaarr) ) || ( isset($_POST[$prefix . 'post_to_instagram']) && $_POST[$prefix . 'post_to_instagram'] == 'on' )) {
			if( !$main_exclude_arr['ins'] ) {
	            if (empty($wpw_auto_poster_options['schedule_wallpost_option'])) { // Check schedule option is "Instantly"
	                //post to instagram
	                $ins_result = $this->insposting->wpw_auto_poster_ins_posting($post);
	                if ($ins_result) {
	                    $postedstr[] = 'ins';
	                }
	            } else {
	
	                if (!in_array('instagram', $schedule_post_to)) {
	                    $schedule_post_to[] = 'instagram';
	                }
	                $schedulepoststr[] = 'ins';
	
	                //Update instagram status to scheduled
	                update_post_meta($postid, $prefix . 'ins_published_on_ins', 2);
	
	            }
			}
        }

        //Pinterest Posting
        $pinterestarr = !empty($wpw_auto_poster_options['enable_pinterest_for']) ? $wpw_auto_poster_options['enable_pinterest_for'] : array();

        //get post published on pinterest
        $pin_published = get_post_meta($postid, $prefix . 'pin_published_on_pin', true);

        //record logs for pinterest posting
        $this->logs->wpw_auto_poster_add('Pinterest Posting | ' . $post->post_type . ' | ' . $postid, true);
       
        $post_to_pinterest = get_post_meta($postid, $prefix . 'post_to_pinterest', true);

        //Check If post is already published and there is disable from metabox but it has checked in backend
        //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_pinterest']) && (!isset($pin_published) || $pin_published == false ) && in_array($post->post_type, $pinterestarr) ) || ( isset($_POST[$prefix . 'post_to_pinterest']) && $_POST[$prefix . 'post_to_pinterest'] == 'on' ) || ( $scheduled === true && $post_to_pinterest == 'on' )) {
			if( !$main_exclude_arr['pin'] ) {

	            if (empty($wpw_auto_poster_options['schedule_wallpost_option'])) { // Check schedule option is "Instantly"
	                //post to user wall on pinterest
	                $pin_result = $this->pinposting->wpw_auto_poster_pin_posting($post);
	                if ($pin_result) {
	                    $postedstr[] = 'pin';
	                }
	            } else {
	
	                if (!in_array('pinterest', $schedule_post_to)) {
	                    $schedule_post_to[] = 'pinterest';
	                }
	                $schedulepoststr[] = 'pin';
                    
                    //Update pinterest status to scheduled
	                update_post_meta($postid, $prefix . 'pin_published_on_pin', 2);
	
	            }
			}
        }

        //update schedule wallpost
        update_post_meta($postid, $prefix . 'schedule_wallpost', $schedule_post_to);
    }

    /**
     * Post to Social Medias
     *
     * Handles to post to social media
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_post_to_social_media($postid, $post) {
        
        global $wpw_auto_poster_options;

        //If post type is autopostlog then return auto posting
        if ($post->post_type == WPW_AUTO_POSTER_LOGS_POST_TYPE)
            return $postid;

        $post_type_object = get_post_type_object($post->post_type);

        if (( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) // Check Autosave
                //|| ( ! isset( $_POST['post_ID'] ) || $postid != $_POST['post_ID'] )
                || ( wpw_auto_poster_extra_security($postid, $post) == true ) || ( $post->post_status != 'publish' )) {
            return $postid;
        }

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        // Update Hour for Individual Post in Hourly Posting
        $wpw_auto_poster_select_hour = isset($_POST[$prefix . 'select_hour']) ? $_POST[$prefix . 'select_hour'] : '';
        $wpw_auto_poster_select_hour = ( !empty( $wpw_auto_poster_select_hour ) ) ? strtotime( $wpw_auto_poster_select_hour ) : '';

        if( !empty( $wpw_auto_poster_select_hour ) ) {
            update_post_meta( $postid, $prefix . 'select_hour', $wpw_auto_poster_select_hour);
        }
        else{
        	$current_date = date( 'Y-m-d H:i', strtotime('tomorrow midnight'));
            update_post_meta( $postid, $prefix . 'select_hour', strtotime($current_date) );
        }

        // apply filters for verify send wall posr after post create/update
        $has_send_wall_post = apply_filters('wpw_auto_poster_verify_send_wall_post', true, $post, $wpw_auto_poster_options);

        if ($has_send_wall_post) { // Verified for send wall post
            //posting to all social medias
            $this->wpw_auto_poster_social_posting($post);
        }

        //redirect to custom url after saving post
        add_filter('redirect_post_location', array($this, 'wpw_auto_poster_redirect_save_post'));
    }

    /**
     * Add Schedule posting with social media
     *
     * Handles to work posting on social media when
     * someone set schedule for particular post
     * at that time it will automatic posted on social medias
     * whichever is selected in settings page
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_schedule_posting($postid) {

        global $wpw_auto_poster_options;

        $post = get_post($postid);

        if ($post->post_type == 'revision')
            return; // Imp Line //  if revision dont do anything.
        if ($post->post_status != 'publish')
            return;

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        // apply filters for verify send wall post after post create/update
        $has_send_wall_post = apply_filters('wpw_auto_poster_verify_send_wall_post', true, $post, $wpw_auto_poster_options);

        if ($has_send_wall_post) { // Verified for send wall post
            //posting to all social medias
            $this->wpw_auto_poster_social_posting($post, true);
        }
    }

    /**
     * Redirect After Save Post
     *
     * Handles to redirect after saving post
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_redirect_save_post($loc) {

        global $postedstr, $schedulepoststr;

        if (!empty($postedstr)) {

            return add_query_arg('wpwautoposteron', $postedstr, $loc);
        } else if (!empty($schedulepoststr)) {

            return add_query_arg('wpwautoposterscheduleon', $schedulepoststr, $loc);
        } else {

            return $loc;
        }
    }

    /**
     * Admin Notices
     *
     * Handles to show admin notices after successfully
     * posted to social networks
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_admin_notices() {

        if (isset($_GET['wpwautoposteron']) || isset($_GET['wpwautoposterscheduleon'])) {

            $postedon = isset($_GET['wpwautoposteron']) ? $_GET['wpwautoposteron'] : '';
            $scheduledon = isset($_GET['wpwautoposterscheduleon']) ? $_GET['wpwautoposterscheduleon'] : '';

            $reparr = array('fb', 'tw', 'li', 'tb', 'ba', 'ins', 'pin');
            $replcarr = array(
                __('Facebook', 'wpwautoposter'),
                __('Twitter', 'wpwautoposter'),
                __('LinkedIn', 'wpwautoposter'),
                __('Tumblr', 'wpwautoposter'),
                __('BufferApp', 'wpwautoposter'),
                __('Instagram', 'wpwautoposter'),
                __('Pinterest', 'wpwautoposter')
            );

            if (!empty($scheduledon)) {

                $scheduledon = str_replace($reparr, $replcarr, $scheduledon);
                $scheduledon = implode($scheduledon, ',');
                $msg = sprintf(__('Post scheduled with %1$s', 'wpwautoposter'), $scheduledon);
            } else {

                $postedon = str_replace($reparr, $replcarr, $postedon);
                $postedon = implode($postedon, ',');
                $msg = sprintf(__('Post published on %1$s', 'wpwautoposter'), $postedon);
            }

            echo "<div class='updated notice notice-success is-dismissible'><p>{$msg}.</p>
                  <button type='button' class='notice-dismiss'><span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
        }
        
        // get all notices from sessions
        $all_notices  = isset( $_SESSION['sap_notices'] ) ? $_SESSION['sap_notices'] : array();
        
        // Display notices if there is any
        if( !empty( $all_notices ) ) {
            foreach ( $all_notices as $notice_type => $messages ) {
                
                foreach( $messages as $message ) {
                    echo "<div class='notice notice notice-$notice_type is-dismissible'>
                        <p>{$message}</p>
                        <button type='button' class='notice-dismiss'><span class='screen-reader-text'>Dismiss this notice.</span></button>
                    </div>";
                }
            }
            unset( $_SESSION['sap_notices'] );
        }
    }

    /**
     * Bulk Delete
     *
     * Handles bulk delete functinalities of posted logs
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    function wpw_auto_poster_posted_logs_bulk_delete() {

        if (( ( isset($_GET['action']) && $_GET['action'] == 'delete') || ( isset($_GET['action2']) && $_GET['action2'] == 'delete' ) ) && isset($_GET['page']) && $_GET['page'] == 'wpw-auto-poster-posted-logs' && isset($_GET['logid']) && !empty($_GET['logid'])) { //check action and page and also logid
            // get redirect url
            $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-posted-logs'), admin_url('admin.php'));

            //get bulk product array from $_GET
            $action_on_id = $_GET['logid'];

            if (count($action_on_id) > 0) { //check there is some checkboxes are checked or not
                //if there is multiple checkboxes are checked then call delete in loop
                foreach ($action_on_id as $posted_log_id) {

                    //parameters for delete function
                    $args = array(
                        'log_id' => $posted_log_id
                    );

                    //call delete function from model class to delete records
                    $this->model->wpw_auto_poster_bulk_delete($args);
                }
                $redirect_url = add_query_arg(array('message' => '3'), $redirect_url);

            }
             
            //if bulk delete is performed successfully then redirect
            wp_redirect($redirect_url);
            exit;
        }
    }

    /**
     * Bulk Scheduling
     *
     * Handles bulk scheduling functinalities of manage schedule
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    function wpw_auto_poster_scheduling_bulk_process() {

        global $wpw_auto_poster_options;

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        //Get admin url
        $admin_url = admin_url('admin.php');

        //Get all supported social network
        $all_social_networks = $this->model->wpw_auto_poster_get_social_type_name();

        //Get selected tab
        $selected_tab = !empty($_GET['tab']) ? $_GET['tab'] : 'facebook';

        //Get social network slug
        $social_network  = ucfirst($selected_tab);
        $social_slug = array_search($social_network, $all_social_networks);


        //Get social meta key
        $status_meta_key = $this->model->wpw_auto_poster_get_social_status_meta_key($selected_tab);

        //Code for Scheduling posts
        if (( ( isset($_GET['action']) && $_GET['action'] == 'schedule') || ( isset($_GET['action2']) && $_GET['action2'] == 'schedule' ) ) && isset($_GET['page']) && $_GET['page'] == 'wpw-auto-poster-manage-schedules' && isset($_GET['schedule']) && !empty($_GET['schedule'])) { //check action and page and also logid
            // get redirect url
            $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-manage-schedules', 'tab' => $selected_tab), $admin_url);

            //get bulk posts array from $_GET
            $action_on_ids = $_GET['schedule'];

            if (count($action_on_ids) > 0) { //check there is some checkboxes are checked or not
                //if there is multiple checkboxes are checked then call delete in loop
                foreach ($action_on_ids as $post_id) {

                    $main_exclude_arr[$social_slug] = false;

                    // Add network to scheduled schedule wall post
                    $schedules = get_post_meta($post_id, $prefix . 'schedule_wallpost', true);

                    $post_type = get_post_type($post_id); // get post type
                    $post_catgeories = wpw_auto_poster_get_post_categories( $post_type, $post_id ); // get post categories

                    // get excluded catgeories for the selected tab
                    $exclude_cats = !empty($wpw_auto_poster_options[$social_slug.'_exclude_cats']) ? $wpw_auto_poster_options[$social_slug.'_exclude_cats'] : array();

                    if(!empty($post_catgeories)) {
                        // Loop through all the categories of a particualr post.
                        foreach($post_catgeories as $category) {

                            // Check if excluded category is selected for the current post type.
                            if(!empty($exclude_cats[$post_type])) {
                                // If atleast one excluded category matches with the post categories than make flag as true
                                if(in_array($category, $exclude_cats[$post_type])){

                                    // make social network exclude flag true, if atleast one excluded category matches
                                    $main_exclude_arr[$social_slug] = true;
                                    continue;
                                }
                            }
                        }
                    }

                    $schedules = !empty($schedules) ? $schedules : array();
                    $schedules[] = $selected_tab;

                    // check if selected social tab has any excluded categories selected 
                    if( !$main_exclude_arr[$social_slug] ) {

                        update_post_meta($post_id, $prefix . 'schedule_wallpost', array_unique($schedules));

                        //Update scheduled meta
                        update_post_meta($post_id, $status_meta_key, 2);
                    }
                }

                if( !$main_exclude_arr[$social_slug] ) {
                    $redirect_url = add_query_arg(array('message' => '1'), $redirect_url);
                }
            
             
            }

            //if there is no checboxes are checked then redirect to listing page
            wp_redirect($redirect_url);
            exit;
        }

        //Code for Unscheduling posts
        if (( ( isset($_GET['action']) && $_GET['action'] == 'unschedule') || ( isset($_GET['action2']) && $_GET['action2'] == 'unschedule' ) ) && isset($_GET['page']) && $_GET['page'] == 'wpw-auto-poster-manage-schedules' && isset($_GET['schedule']) && !empty($_GET['schedule'])) { //check action and page and also logid
            // get redirect url
            $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-manage-schedules', 'tab' => $selected_tab), $admin_url);

            //get bulk posts array from $_GET
            $action_on_ids = $_GET['schedule'];

            if (count($action_on_ids) > 0) { //check there is some checkboxes are checked or not
                //if there is multiple checkboxes are checked then call delete in loop
                foreach ($action_on_ids as $post_id) {

                    // Add network to scheduled schedule wall post
                    $schedules = get_post_meta($post_id, $prefix . 'schedule_wallpost', true);
                    if (!empty($schedules)) {
                        if (($key = array_search($selected_tab, $schedules)) !== false) {
                            unset($schedules[$key]);
                        }
                        update_post_meta($post_id, $prefix . 'schedule_wallpost', $schedules);
                    }

                    //Remove status meta
                    delete_post_meta($post_id, $status_meta_key);
                }
                $redirect_url = add_query_arg(array('message' => '2'), $redirect_url);

            } 
            //if there is no checboxes are checked then redirect to listing page
            wp_redirect($redirect_url);
            exit;
        }
    }

    /**
     * Validate Setting
     *
     * Handles to add validate schedule settings
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_validate_setting($new_data, $old_data) {

        if ( ( !empty($new_data['schedule_wallpost_option']) && $new_data['schedule_wallpost_option'] != $old_data['schedule_wallpost_option'] ) || ( $new_data['schedule_wallpost_option'] == 'wpw_custom_mins' && !empty($new_data['schedule_wallpost_custom_minute'] ) && $new_data['schedule_wallpost_custom_minute'] != $old_data['schedule_wallpost_custom_minute'] ) || ( $new_data['schedule_wallpost_option'] == 'twicedaily' && ( $new_data['enable_twice_random_posting'] != $old_data['enable_twice_random_posting'] || ( $new_data['schedule_wallpost_twice_time1'] != $old_data['schedule_wallpost_twice_time1'] || $new_data['schedule_wallpost_twice_time2'] != $old_data['schedule_wallpost_twice_time2'] ) ) ) ) { // Check Schedule WallPost is not "Instance"
            // first clear the schedule
            wp_clear_scheduled_hook('wpw_auto_poster_scheduled_cron');

            if (!wp_next_scheduled('wpw_auto_poster_scheduled_cron')) {

                $utc_timestamp = time(); //

                $local_time = current_time('timestamp'); // to get current local time

                if ($new_data['schedule_wallpost_option'] == 'daily' && isset($new_data['schedule_wallpost_time']) && isset($new_data['schedule_wallpost_minute'])) {

                    // Schedule other CRON events starting at user defined hour and periodically thereafter
                    $schedule_time = mktime($new_data['schedule_wallpost_time'], $new_data['schedule_wallpost_minute'], 0, date('m', $local_time), date('d', $local_time), date('Y', $local_time));

                    // get difference
                    $diff = ( $schedule_time - $local_time );
                    $utc_timestamp = $utc_timestamp + $diff;

                    wp_schedule_event($utc_timestamp, 'daily', 'wpw_auto_poster_scheduled_cron');
                }
                elseif ($new_data['schedule_wallpost_option'] == 'twicedaily' && empty($new_data['enable_twice_random_posting'])) {                 // Added since version 2.5.1
                    $utc_timestamp = time();

                    // Schedule other CRON events starting at user defined hour and periodically thereafter
                    $schedule_time1 = mktime($new_data['schedule_wallpost_twice_time1'], 0, 0, date('m', $local_time), date('d', $local_time), date('Y', $local_time));

                    // get difference
                    $diff = ( $schedule_time1 - $local_time );
                    $utc_timestamp1 = $utc_timestamp + $diff;

                    wp_schedule_event( $utc_timestamp1, 'daily', 'wpw_auto_poster_scheduled_cron');

                    $schedule_time2 = mktime($new_data['schedule_wallpost_twice_time2'], 0, 0, date('m', $local_time), date('d', $local_time), date('Y', $local_time));

                    // get difference
                    $diff = ( $schedule_time2 - $local_time );
                    $utc_timestamp2 = $utc_timestamp + $diff;

                    wp_schedule_event( $utc_timestamp2, 'daily', 'wpw_auto_poster_scheduled_cron');

                }
                else if ($new_data['schedule_wallpost_option'] == 'hourly') {                 // Added since version 2.0.0

                    // logic to get hours rounded, if current time is 3:15 am it will return 4 am.
                    // return value in seconds
                    $new_time = ceil($local_time / 3600) * 3600;

                    // get difference between 3:15 and 4 so it will become 45 min (2700 seconds)
                    $diff = ( $new_time - $local_time );

                    // add 2700 seconds so cron will start runnig from 4 am.
                    $utc_timestamp = $utc_timestamp + $diff;

                    wp_schedule_event($utc_timestamp, $new_data['schedule_wallpost_option'], 'wpw_auto_poster_scheduled_cron');
                } else {

                    $scheds = (array) wp_get_schedules();
                    $current_schedule = $new_data['schedule_wallpost_option'];
                    $interval = ( isset($scheds[$current_schedule]['interval']) ) ? (int) $scheds[$current_schedule]['interval'] : 0;

                    $utc_timestamp = $utc_timestamp + $interval;

                    wp_schedule_event($utc_timestamp, $new_data['schedule_wallpost_option'], 'wpw_auto_poster_scheduled_cron');
                }
            }
        }
        return $new_data;
    }

    /**
     * Add Custom Schedule
     *
     * Handle to add custom schedule
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_add_custom_scheduled($schedules) {
        global $wpw_auto_poster_options;

        // custom minutes value from input box
        $schedule_wallpost_custom_minute = ( !empty( $wpw_auto_poster_options['schedule_wallpost_custom_minute'] ) ) ? $wpw_auto_poster_options['schedule_wallpost_custom_minute'] : WPW_AUTO_POSTER_SCHEDULE_CUSTOM_DEFAULT_MINUTE;

        // Adds once weekly to the existing schedules.
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display' => __('Once Weekly', 'wpwautoposter')
        );

        // check on update options
        if( isset( $_POST['wpw_auto_poster_options']['schedule_wallpost_custom_minute'] ) && !empty( $_POST['wpw_auto_poster_options']['schedule_wallpost_custom_minute'] ) )
            $schedule_wallpost_custom_minute = $_POST['wpw_auto_poster_options']['schedule_wallpost_custom_minute'];

        // code to set custom mins given to the input box for schedule cron
        $schedules["wpw_custom_mins"]  = array(
                'interval' => $schedule_wallpost_custom_minute*60,
                'display' => __( $schedule_wallpost_custom_minute.' minutes', 'wpwautoposter'));

        return $schedules;
    }

    /**
     * Cron Job For Send WallPost to Followers
     *
     * Handle to call schedule cron for
     * send wallpost to followers
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_scheduled_cron() {

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        // Get all post data which have send wall post
        $posts_data = $this->model->wpw_auto_poster_get_schedule_post_data();

        if (!empty($posts_data)) { // Check post data are not empty
            foreach ($posts_data as $post_data) {

                $postid = $post_data->ID;

                //get schedule wallpost
                $get_schedule = get_post_meta($postid, $prefix . 'schedule_wallpost', true);

                if (in_array('facebook', $get_schedule)) { // Check facebook

                    //post to user wall on facebook
                    $res = $this->fbposting->wpw_auto_poster_fb_posting($post_data);

                    // check if published post successfully
                    if( $res ){
                        $key = array_search ( 'facebook', $get_schedule );
                        unset( $get_schedule[$key] );
                    }
                }
                if (in_array('twitter', $get_schedule)) { // Check twitter

                    //post to twitter
                    $res = $this->twposting->wpw_auto_poster_tw_posting($post_data);

                    // check if published post successfully
                    if( $res ){
                        $key = array_search ( 'twitter', $get_schedule );
                        unset( $get_schedule[$key] );
                    }
                }
                if (in_array('linkedin', $get_schedule)) { // Check linkedin

                    //post to linkedin
                    $res = $this->liposting->wpw_auto_poster_li_posting($post_data);

                    // check if published post successfully
                    if( $res ){
                        $key = array_search ( 'linkedin', $get_schedule );
                        unset( $get_schedule[$key] );
                    }
                }
                if (in_array('tumblr', $get_schedule)) { // Check tumblr

                    //post to tumblr
                    $res = $this->tbposting->wpw_auto_poster_tb_posting($post_data);

                    // check if published post successfully
                    if( $res ){
                        $key = array_search ( 'tumblr', $get_schedule );
                        unset( $get_schedule[$key] );
                    }
                }

                if (in_array('bufferapp', $get_schedule)) { // Check bufferapp

                    //post to bufferapp
                    $res = $this->baposting->wpw_auto_poster_ba_posting($post_data);

                    // check if published post successfully
                    if( $res ){
                        $key = array_search ( 'bufferapp', $get_schedule );
                        unset( $get_schedule[$key] );
                    }
                }

                if (in_array('instagram', $get_schedule)) { // Check instagram

                    //post to user timeline on instagram
                    $res = $this->insposting->wpw_auto_poster_ins_posting($post_data);

                    // check if published post successfully
                    if( $res ){
                        $key = array_search ( 'instagram', $get_schedule );
                        unset( $get_schedule[$key] );
                    }
                }

                if (in_array('pinterest', $get_schedule)) { // Check pinterest

                    //post to user board/pins on pinterest
                    $res = $this->pinposting->wpw_auto_poster_pin_posting($post_data);

                    // check if published post successfully
                    if( $res ){
                        $key = array_search ( 'pinterest', $get_schedule );
                        unset( $get_schedule[$key] );
                    }
                }

                //delete schedule wallpost
                if( empty( $get_schedule ) ) {
                    delete_post_meta($postid, $prefix . 'schedule_wallpost');
                } else {
                    update_post_meta( $postid, $prefix . 'schedule_wallpost', $get_schedule );
                }

            }
        }
    }

    /**
     * Manage WPML compability
     * Remove status of posting on social data
     *
     * so, when user update data,
     * it's going for post data on socials
     *
     * @package Social Auto Poster
     * @since 1.8.3
     */
    public function wpw_auto_poster_wpml_dup_remove_status_meta($master_post_id, $lang, $post_array, $id) {

        if (!empty($id)) {

            global $wpw_auto_poster_options;

            $post_type = isset($post_array['post_type']) ? $post_array['post_type'] : '';

            $fb_enable_post_type = !empty($wpw_auto_poster_options['enable_facebook_for']) ? $wpw_auto_poster_options['enable_facebook_for'] : array();
            $tw_enable_post_type = !empty($wpw_auto_poster_options['enable_twitter_for']) ? $wpw_auto_poster_options['enable_twitter_for'] : array();
            $li_enable_post_type = !empty($wpw_auto_poster_options['enable_linkedin_for']) ? $wpw_auto_poster_options['enable_linkedin_for'] : array();
            $tb_enable_post_type = !empty($wpw_auto_poster_options['enable_tumblr_for']) ? $wpw_auto_poster_options['enable_tumblr_for'] : array();
            $ba_enable_post_type = !empty($wpw_auto_poster_options['enable_bufferapp_for']) ? $wpw_auto_poster_options['enable_bufferapp_for'] : array();
            $ins_enable_post_type = !empty($wpw_auto_poster_options['enable_instagram_for']) ? $wpw_auto_poster_options['enable_instagram_for'] : array();
            $pin_enable_post_type = !empty($wpw_auto_poster_options['enable_pinterest_for']) ? $wpw_auto_poster_options['enable_pinterest_for'] : array();

            if (in_array($post_type, $fb_enable_post_type))
                update_post_meta($id, '_wpweb_fb_published_on_fb', false);

            if (in_array($post_type, $tw_enable_post_type))
                update_post_meta($id, '_wpweb_tw_status', false);

            if (in_array($post_type, $li_enable_post_type))
                update_post_meta($id, '_wpweb_li_status', false);

            if (in_array($post_type, $tb_enable_post_type))
                update_post_meta($id, '_wpweb_tb_status', false);

            if (in_array($post_type, $ba_enable_post_type))
                update_post_meta($id, '_wpweb_ba_status', false);

            if (in_array($post_type, $ins_enable_post_type))
                update_post_meta($id, '_wpweb_ins_published_on_ins', false);

            if (in_array($post_type, $pin_enable_post_type))
                update_post_meta($id, '_wpweb_pin_published_on_pin', false);
        }

        return;
    }

    /**
     * Select Hour for Individual Post When Globally Hourly Posting Selected
     *
     * Handle to add meta in publish box
     *
     * @package Social Auto Poster
     * @since 1.8.4
     */
    public function wpw_auto_poster_publish_meta() {

        global $post;

        $args = array('public' => true);
        $post_types = get_post_types($args);

        $wpw_auto_poster_options = get_option('wpw_auto_poster_options');

        if ($wpw_auto_poster_options['schedule_wallpost_option'] == 'hourly' && in_array($post->post_type, $post_types)) {

            $prefix = WPW_AUTO_POSTER_META_PREFIX;

            // wordpress date format                    
            $date_format = apply_filters( 'wpw_auto_poster_display_date_format', 'Y-m-d' );

            $wpw_auto_poster_select_hour = get_post_meta($post->ID, $prefix . 'select_hour', true);

            if( !empty( $wpw_auto_poster_select_hour ) && strlen($wpw_auto_poster_select_hour) <= 2 ){
            	$time = $wpw_auto_poster_select_hour;
            	$wpw_auto_poster_select_hour = date( $date_format, current_time('timestamp') );
            	$wpw_auto_poster_select_hour = $wpw_auto_poster_select_hour.' '.$time.':00';
            	$wpw_auto_poster_select_hour = date( $date_format.' H:i', strtotime($wpw_auto_poster_select_hour) );
            }elseif(!empty( $wpw_auto_poster_select_hour )){
            	$wpw_auto_poster_select_hour = date( $date_format.' '.'H:i', $wpw_auto_poster_select_hour );
            }else{
            	$wpw_auto_poster_select_hour = '';
            }?>
			
            <div class="misc-pub-section misc-pub-schedule-date">
                <label for="<?php echo $prefix . 'select_hour'; ?>"><span class="wpw-auto-poster-schedule-icon"><img src="<?php print WPW_AUTO_POSTER_IMG_URL.'/icons/calendar.png';?>"></span>
                <span class="wpw-auto-poster-schedule-label">
                <?php _e('Schedule: ', 'wpwautoposter'); ?>
                </span>
                </label>
                <span class="wpw-auto-poster-schedule-label">
                <input type="text" name="<?php echo $prefix . 'select_hour'; ?>" id="<?php echo $prefix . 'select_hour'; ?>" class="wpw-auto-poster-schedule-date" value="<?php print $wpw_auto_poster_select_hour;?>"> 
                <span class="clear-date" title="<?php _e('Clear date', 'wpwautoposter'); ?>">X</span>
                </span>
            </div><?php
        }
    }

    /**
     * Add FB account list field to add or edit category form
     *
     * Handle to display FB account list to add category form
     *
     * @package Social Auto Poster
     * @since 2.3.1
     */
    function wpw_auto_poster_add_category_fb_acc_fields() {

        print '<table class="form-table">';
        // FB account list
        include_once( WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-fb-fields.php' );

        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

    /**
     * Add Twitter account list field to add or edit category form
     *
     * Handle to display Twitter account list to add category form
     *
     * @package Social Auto Poster
     * @since 2.3.1
     */
    function wpw_auto_poster_add_category_tw_acc_fields() {
        print '<table class="form-table">';
        include_once( WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-tw-fields.php' );
        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

    /**
     * Add Linkdin account list field to add or edit category form
     *
     * Handle to display Linkdin account list to add category form
     *
     * @package Social Auto Poster
     * @since 2.3.1
     */
    function wpw_auto_poster_add_category_li_acc_fields() {
        print '<table class="form-table">';
        // Linkdin account list
        include_once( WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-li-fields.php' );
        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

    /**
     * Add Instagram account list field to add or edit category form
     *
     * Handle to display Instagram account list to add category form
     *
     * @package Social Auto Poster
     * @since 2.3.1
     */
    function wpw_auto_poster_add_category_ins_acc_fields() {
        print '<table class="form-table">';
        include_once( WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-ins-fields.php' );
        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

    /**
     * Add Pinterest account list field to add or edit category form
     *
     * Handle to display Pinterest account list to add category form
     *
     * @package Social Auto Poster
     * @since 2.3.1
     */
    function wpw_auto_poster_add_category_pin_acc_fields() {
        print '<table class="form-table">';
        include_once( WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-pin-fields.php' );
        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

    /**
     * Add hook to category add edit form
     *
     * Handle to display social account list to add and edit category form
     *
     * @package Social Auto Poster
     * @since 2.3.1
     */
    function wpw_auto_poster_hook_taxonomy() {

        global $wpw_auto_poster_options;

        $fb_selected_post = !empty($wpw_auto_poster_options['enable_facebook_for']) ? $wpw_auto_poster_options['enable_facebook_for'] : array();
        $tw_selected_post = !empty($wpw_auto_poster_options['enable_twitter_for']) ? $wpw_auto_poster_options['enable_twitter_for'] : array();
        $li_selected_post = !empty($wpw_auto_poster_options['enable_linkedin_for']) ? $wpw_auto_poster_options['enable_linkedin_for'] : array();
        $ins_selected_post = !empty($wpw_auto_poster_options['enable_instagram_for']) ? $wpw_auto_poster_options['enable_instagram_for'] : array();
        $pin_selected_post = !empty($wpw_auto_poster_options['enable_pinterest_for']) ? $wpw_auto_poster_options['enable_pinterest_for'] : array();

        $fb_exclude_cats = !empty($wpw_auto_poster_options['fb_exclude_cats']) ? $wpw_auto_poster_options['fb_exclude_cats'] : array();
        $tw_exclude_cats = !empty($wpw_auto_poster_options['tw_exclude_cats']) ? $wpw_auto_poster_options['tw_exclude_cats'] : array();
        $li_exclude_cats = !empty($wpw_auto_poster_options['li_exclude_cats']) ? $wpw_auto_poster_options['li_exclude_cats'] : array();
        $ins_exclude_cats = !empty($wpw_auto_poster_options['ins_exclude_cats']) ? $wpw_auto_poster_options['ins_exclude_cats'] : array();
        $pin_exclude_cats = !empty($wpw_auto_poster_options['pin_exclude_cats']) ? $wpw_auto_poster_options['pin_exclude_cats'] : array();
        
        $cat_id = "";

        if( !empty( $_GET['tag_ID'] ) ) {

            $cat_id = $_GET['tag_ID'];
            $taxonomy = $_GET['taxonomy'];

            $term = get_term_by( 'id', $cat_id, $taxonomy, ARRAY_A );
            $cat_slug = $term['slug'];
        }

        // code to add category hook to each post types
        $all_post_types = get_post_types(array('public' => true), 'objects');
        $all_post_types = is_array($all_post_types) ? $all_post_types : array();

        if (!empty($all_post_types)) {

            foreach ($all_post_types as $type) {
                $tax_obj = get_taxonomies(array('object_type' => array($type->name)), 'objects');

                // FB account list field to only selcted post types
                if (in_array($type->name, $fb_selected_post)) {
                    // add or edit category form hook for FB acct list
                    foreach ($tax_obj as $key => $value) {

                        // Skip if taxonomy is not category
                        if (!$value->hierarchical)
                            continue;

                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_fb_acc_fields'));
                        
                        $edit_display = true;

                        if( !empty( $cat_id ) ) {

                            // check if the category excluded for facebook 
                            if( !empty($fb_exclude_cats[$type->name] ) ) {

                                if( in_array( $cat_slug, $fb_exclude_cats[$type->name] ) )
                                    $edit_display = false;
                            }

                            // display facebook edit category account selection if not exclude
                            if( $edit_display ) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_fb_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Twitter account list field to only selcted post types
                if (in_array($type->name, $tw_selected_post)) {
                    
                    // add or edit category form hook for TW acct list
                    foreach ($tax_obj as $key => $value) {

                        // Skip if taxonomy is not category
                        if (!$value->hierarchical)
                            continue;

                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_tw_acc_fields'));

                        $edit_display = true;

                        if( !empty( $cat_id ) ) {

                            // check if the category excluded for Twitter 
                            if( !empty($tw_exclude_cats[$type->name] ) ) {

                                if( in_array( $cat_slug, $tw_exclude_cats[$type->name] ) )
                                    $edit_display = false;
                            }

                            // display facebook edit category account selection if not exclude
                            if( $edit_display ) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_tw_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Linkdin account list field to only selcted post types
                if (in_array($type->name, $li_selected_post)) {

                    // add or edit category form hook for Linkedin acct list
                    foreach ($tax_obj as $key => $value) {

                        // Skip if taxonomy is not category
                        if (!$value->hierarchical)
                            continue;

                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_li_acc_fields'));

                        $edit_display = true;

                        if( !empty( $cat_id ) ) {

                            // check if the category excluded for Linkedin 
                            if( !empty($li_exclude_cats[$type->name] ) ) {

                                if( in_array( $cat_slug, $li_exclude_cats[$type->name] ) )
                                    $edit_display = false;
                            }

                            // display Linkedin edit category account selection if not exclude
                            if( $edit_display ) {

                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_li_acc_fields'), 999);
                            }
                        }  
                    }
                }

                // Instagram account list field to only selcted post types added since 2.6.0
                if (in_array($type->name, $ins_selected_post)) {
                    // add or edit category form hook for Instagram acct list
                    foreach ($tax_obj as $key => $value) {

                        // Skip if taxonomy is not category
                        if (!$value->hierarchical)
                            continue;

                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_ins_acc_fields'));
                        
                        $edit_display = true;

                        if( !empty( $cat_id ) ) {

                            // check if the category excluded for instagram 
                            if( !empty($ins_exclude_cats[$type->name] ) ) {

                                if( in_array( $cat_slug, $ins_exclude_cats[$type->name] ) )
                                    $edit_display = false;
                            }

                            // display instagram edit category account selection if not exclude
                            if( $edit_display ) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_ins_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Pinterest account list field to only selcted post types added since 2.6.0
                if (in_array($type->name, $pin_selected_post)) {
                    // add or edit category form hook for Pinterest acct list
                    foreach ($tax_obj as $key => $value) {

                        // Skip if taxonomy is not category
                        if (!$value->hierarchical)
                            continue;

                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_pin_acc_fields'));
                        
                        $edit_display = true;

                        if( !empty( $cat_id ) ) {

                            // check if the category excluded for pinterest 
                            if( !empty($pin_exclude_cats[$type->name] ) ) {

                                if( in_array( $cat_slug, $pin_exclude_cats[$type->name] ) )
                                    $edit_display = false;
                            }

                            // display facebook edit category account selection if not exclude
                            if( $edit_display ) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_pin_acc_fields'), 999);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Save posting to social account for each category
     *
     * Handle to save social account for category
     *
     * @package Social Auto Poster
     * @since 2.3.1
     */
    function wpw_auto_poster_category_fields_save($term_id, $tt_id, $taxonomy) {

        if (!isset($_POST['wpw_auto_category_posting']))
            return false;

        $old_cat_posting_acct = get_option('wpw_auto_poster_category_posting_acct');

        $selected_social_accounts = $_POST['wpw_auto_category_poster_options'];

        // clear old social account for term id
        if (!empty($term_id) && isset($old_cat_posting_acct[$term_id])) {
            unset($old_cat_posting_acct[$term_id]);
        }

        if (!empty($term_id) && !empty($selected_social_accounts)) {

            foreach ($selected_social_accounts as $social_acc_name => $social_acc_ids) {

                // update option for each account
                if (!empty($social_acc_ids)) {
                    $old_cat_posting_acct[$term_id][$social_acc_name] = $social_acc_ids;
                }
            }
        }

        update_option('wpw_auto_poster_category_posting_acct', $old_cat_posting_acct);
    }


    /**
     * Function to post wordpress pretty url if settings selected
     *
     * @package Social Auto Poster
     * @since 1.5.6
    */
    public function wpw_auto_poster_is_wp_pretty_url( $link, $postid, $socialtype ) {

        global $wpw_auto_poster_options;

        $is_pretty = ( !empty( $wpw_auto_poster_options[ $socialtype.'_wp_pretty_url'] ) ) ? $wpw_auto_poster_options[$socialtype.'_wp_pretty_url'] : '';

        if( $is_pretty == 'yes' ) {

            $link = get_permalink( $postid );
        }

        return $link;
    }

    /**
	 * Function to generate and download system log report
	 *
	 * @package Social Auto Poster
	 * @since 1.5.6
	 */
    /*public function wpw_auto_poster_generate_system_log(){

    	// If social auto poster system log button is clicked
		if( !empty($_GET['page']) && $_GET['page'] == 'wpw-auto-poster-settings' && !empty($_GET['wpw_auto_poster_sys_log']) && $_GET['wpw_auto_poster_sys_log'] == 1 ) {

			global $wpw_auto_poster_options;

			// Initialise variables
			$log_data = '';

			// Open new file
		    $handle = fopen("social-auto-poster-system-report.txt", "w");

		    // Get active plugins
		    $active_plugins 	= $this->model->wpw_auto_poster_get_active_plugins();

		    // Get active themes
		    $active_theme 		= wp_get_theme();

		    // Get all wall posting options
		    $schedule_wallpost_options = $this->model->wpw_auto_poster_get_all_schedules();

		    // Start writing data in our file
		    $log_data 			= '--- WPWeb Social Login Report Information ---';

			// HTML for WordPress environment
			$log_data	.= "\n\n".__('--- WordPress Environment ---', 'wpwautoposter');
			$log_data	.= "\n".__('Home URL: ', 'wpwautoposter') . get_option( 'home' );
			$log_data	.= "\n".__('WorPress Version: ', 'wpwautoposter') . get_bloginfo( 'version' );
			$log_data	.= "\n".__('WP Debug Mode: ', 'wpwautoposter') . ( (defined( 'WP_DEBUG' ) && WP_DEBUG) ? __('Yes', 'wpwautoposter') : __('No', 'wpwautoposter') );
			$log_data	.= "\n".__('WP cron: ', 'wpwautoposter') . ( ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) ? __('Yes', 'wpwautoposter') : __('No', 'wpwautoposter') );

			// HTML for Server environment
			$log_data	.= "\n\n".__('--- Server Environment ---', 'wpwautoposter');
			$log_data	.= "\n".__('PHP Version: ', 'wpwautoposter') . phpversion();
			$log_data	.= "\n".__('fsockopen/cURL: ', 'wpwautoposter') . ( (function_exists( 'fsockopen' ) || function_exists( 'curl_init' )) ? __('Yes', 'wpwautoposter') : __('No', 'wpwautoposter') );

			// HTML for Active plugins
			$log_data	.= "\n\n".__('--- Active Plugins ---', 'wpwautoposter');
			foreach($active_plugins as $plugin){

				if ( ! empty( $plugin['name'] ) ) {
					$dirname = dirname( $plugin['plugin'] );

					// Link the plugin name to the plugin url if available.
					$plugin_name = esc_html( $plugin['name'] );
					$log_data .= "\n" . $plugin_name . __( ' by ', 'wpwautoposter' ) . $plugin['author_name'] . __(' - ', 'wpwautoposter') . esc_html( $plugin['version'] );
				}
			}

			// HTML for Active theme
			$log_data .= "\n\n".__('--- Active Theme ---', 'wpwautoposter');
			$log_data .= "\n" . __('Theme Name: ', 'wpwautoposter') . $active_theme->Name;
			$log_data .= "\n" . __('Version: ') . $active_theme->Version;
			$log_data .= "\n" . __('Author URL: ') . esc_url_raw( $active_theme->{'Author URI'} );
			$log_data .= "\n" . __('Child theme: ') . ( is_child_theme() ? __('Yes', 'wpwautoposter') : __('No', 'wpwautoposter') );

			// HTML for Plugin settings
			$log_data .= "\n\n" . __('--- Plugin Settings ---', 'wpwautoposter');
			$log_data .= "\n" . __("Delete Option: ", 'wpwautoposter') . ( !empty($wpw_auto_poster_options['delete_options']) ? __('Yes', 'wpwautoposter') : __('No', 'wpwautoposter') );
			$log_data .= "\n" . __("Enable Debug: ", 'wpwautoposter') . ( !empty($wpw_auto_poster_options['enable_logs']) ? __('Yes', 'wpwautoposter') : __('No', 'wpwautoposter') );
			$log_data .= "\n" . __("Enable Social Posting Logs: ", 'wpwautoposter') . ( !empty($wpw_auto_poster_options['enable_posting_logs']) ? __('Yes', 'wpwautoposter') : __('No', 'wpwautoposter') );
			$log_data .= "\n" . __("Schedule Wall Posts: ", 'wpwautoposter') . ( !empty($wpw_auto_poster_options['schedule_wallpost_option']) ? $schedule_wallpost_options[$wpw_auto_poster_options['schedule_wallpost_option']] : __('Instantly', 'wpwautoposter'));
			$log_data .= "\n" . __("Maximum Posting per schedule: ", 'wpwautoposter') . ( !empty($wpw_auto_poster_options['daily_posts_limit']) ? $wpw_auto_poster_options['daily_posts_limit'] : __('Unlimited', 'wpwautoposter') );
			$log_data .= "\n" . __("Allow autopost from thirdparty plugins: ", 'wpwautoposter') . ( !empty($wpw_auto_poster_options['autopost_thirdparty_plugins']) ? __('Yes', 'wpwautoposter') : __('No', 'wpwautoposter') );

			fwrite($handle, $log_data);
		    fclose($handle);

		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename="'.basename('social-auto-poster-system-report.txt').'"');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize('social-auto-poster-system-report.txt'));
		    readfile('social-auto-poster-system-report.txt');
		    unlink("social-auto-poster-system-report.txt");
		    exit;
		}
    }*/

    /**
     * Handles to fetch categories from post type
     *
     * @package Social Auto Poster
     * @since 2.6.0
     */
    public function wpw_auto_poster_get_category(){

    	// If $_POST for post type value is not empty
    	if(!empty($_POST['post_type_val'])) {

    		// Get all taxonomies defined for that post type
    		$all_taxonomies = get_object_taxonomies( $_POST['post_type_val'], 'objects' );

    		// Loop on all taxonomies
    		foreach ($all_taxonomies as $taxonomy){

    			/**
    			 * If taxonomy is object and it is hierarchical, than it is our category
    			 * NOTE: If taxonomy is not hierarchical than it is tag and we should not consider this
    			 * And we will only consider first category found in our taxonomy list
    			 */
    			if(is_object($taxonomy) && !empty($taxonomy->hierarchical)){

    				$categories = get_terms( $taxonomy->name, array( 'hide_empty' => false ) ); // Get categories for taxonomy

    				// Start creating html from categories
    				$html = '<option value="">' . __('Select Category', 'wpwautoposter') . '</option>';
    				foreach ($categories as $category){

    					$html .=  '<option value="' . $category->term_id . '"';
    					// If category is already selected and current id is same as the selected one
    					if(!empty($_POST['sel_category_id']) && $_POST['sel_category_id'] == $category->term_id) {

    						$html .= " selected='selected'";
    					}
    					$html .= '>' . $category->name . '</option>';
    				}

    				// Echo html
    				echo $html;
    				exit;
    			}
    		}
    	}
    }

    /**
     *Fetch taxonomies from custom post type
     *
     * @package Social Auto Poster
     * @since 2.3.1
     */
    public function wpw_auto_poster_get_taxonomies(){
        
        global $wpw_auto_poster_options;

        $social_prefix = $_POST['social_type'];
        $static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();
        
        $post_type_tags = array();
        $post_type_cats = array();
        $selected = $cathtml = $taghtml = '';

        /***** Custom post type TAG taxonomy code ******/

        // Check if any taxonomy tag is selected or not
        if(!empty( $_POST['selected_tags'])) {

            $pre_selected_tags = $_POST['selected_tags'];

            foreach ($pre_selected_tags as $pre_selected_tag) {
                $tagData = explode("|",$pre_selected_tag);
                $post_type = $tagData[0];
                $post_tag= $tagData[1];
                $selected_tags[$post_type][] = $post_tag;
            }
            
            $post_type_tags = $selected_tags;

        } 
        /*elseif( !empty( $wpw_auto_poster_options[$social_prefix.'_post_type_tags'] ) ) { // else check if tag is set in taxonomy
            
            $post_type_tags = $wpw_auto_poster_options[$social_prefix.'_post_type_tags'];
        }*/

        /***** Custom post type CATEGORY taxonomy code ******/

        // Check if any taxonomy category is selected or not
        if(!empty( $_POST['selected_cats'])) {

            $pre_selected_cats = $_POST['selected_cats'];

            foreach ($pre_selected_cats as $pre_selected_cat) {
                $tagData = explode("|",$pre_selected_cat);
                $post_type = $tagData[0];
                $post_cat= $tagData[1];
                $selected_cats[$post_type][] = $post_cat;
            }
            
            $post_type_cats = $selected_cats;

        } 
        /*elseif( !empty( $wpw_auto_poster_options[$social_prefix.'_post_type_cats'] ) ) { // else check if category is set in taxonomy

            $post_type_cats = $wpw_auto_poster_options[$social_prefix.'_post_type_cats'];

        }*/

        // If $_POST for post type value is not empty
        if(!empty($_POST['post_type_val'])) {

            foreach($_POST['post_type_val'] as $post_type) {

                $html_tag = $html_cat = '';
                // Get all taxonomies defined for that post type
                $all_taxonomies = get_object_taxonomies( $post_type, 'objects' );
                
                // Loop on all taxonomies
                foreach ($all_taxonomies as $taxonomy){


                    if(is_object($taxonomy) && $taxonomy->hierarchical == 1){
                        
                        $selected = "";

                        if( isset( $post_type_cats[$post_type] ) && !empty( $post_type_cats[$post_type] ) ){
                            $selected = ( in_array( $taxonomy->name, $post_type_cats[$post_type] ) ) ? 'selected="selected"' : '';
                        }
                        
                        $html_cat .=  '<option value="' . $post_type."|".$taxonomy->name . '" '.$selected.'>'.$taxonomy->label.'</option>';

                    } elseif (is_object($taxonomy) && $taxonomy->hierarchical != 1) {
                        
                        if( !empty( $static_post_type_arr[$post_type] ) && $static_post_type_arr[$post_type] != $taxonomy->name){
                             continue;
                        }
                        $selected = "";

                        if( isset( $post_type_tags[$post_type] ) && !empty( $post_type_tags[$post_type] ) ) {
                            $selected = ( in_array( $taxonomy->name, $post_type_tags[$post_type] ) ) ? 'selected="selected"' : '';
                        }
                        $html_tag .=  '<option value="' .$post_type."|".$taxonomy->name . '" '.$selected.'>'.$taxonomy->label.'</option>';
                    }
                }

                if(isset($html_cat) && !empty($html_cat)) {
                    $cathtml .= '<optgroup label='.ucfirst($post_type).'>'.$html_cat.'</optgroup>';
                }
                if(isset($html_tag) && !empty($html_tag)) {
                    $taghtml .= '<optgroup label='.ucfirst($post_type).'>'.$html_tag.'</optgroup>';
                }
                
                // Unset all values
                unset($html_cat);
                unset($html_tag);

                $response['data'] = array('categories'=> $cathtml, 'tags' => $taghtml);
                
            }
            echo json_encode($response);
            unset($response['data']);
            exit;
        }

    }
    
    
    /**
     * Handles to logs report graph process
     *
     * @package Social Auto Poster
     * @since 2.6.0
     */
    public function wpw_auto_poster_logs_graph_process(){

    	$prepare = $final_array= array();

    	$social_types_list = $this->model->wpw_auto_poster_get_social_type_name();   	

    	if( !empty($_REQUEST['social_type'])){
			$final_array[] = array(  __('Month','wpwautoposter'), $social_types_list[$_REQUEST['social_type']] );
    	}else{
    		$final_array[]  = array( __('Month','wpwautoposter'), __('Facebook','wpwautoposter'), __('Twitter','wpwautoposter'),__('Linkedin','wpwautoposter'),__('Tumblr','wpwautoposter'),__('BufferApp','wpwautoposter'),__('Instagram','wpwautoposter'),__('Pinterest','wpwautoposter') );
    	}

    	$prefix = WPW_AUTO_POSTER_META_PREFIX;

    	//Default Argument
    	$args = array(
						'posts_per_page'		=> -1,
						'orderby'				=> 'ID',
						'order'					=> 'ASC',
						'wpw_auto_poster_list'	=> true
					);

		//searched by social type
		if( !empty($_REQUEST['social_type']) ) {
			$args['meta_query']	= array(
									array(
										'key' => $prefix . 'social_type',
										'value' => $_REQUEST['social_type'],
										)
								  );
		}

		if( !empty($_REQUEST['filter_type']) && $_REQUEST['filter_type']== 'custom' ){

			//Check Start date and set it in query
			if( !empty($_REQUEST['start_date']) ) {
				$args['date_query'][]['after'] = date('Y-m-d', strtotime('-1 day', strtotime($_REQUEST['start_date'])));
			}

			//Check End date and set it in query
			if(!empty($_REQUEST['end_date']) ) {
				$args['date_query'][]['before'] = date('Y-m-d', strtotime('+1 day', strtotime($_REQUEST['end_date'])));
				//$args['date_query'][]['before'] = $_REQUEST['end_date'];
			}

			//Check Start date and End date if empty then month set
			if( empty($_REQUEST['start_date']) && empty($_REQUEST['end_date'])){
				$args['m']	= date('Ym');
			}

		}else if( !empty($_REQUEST['filter_type']) && $_REQUEST['filter_type']== 'current_year' ){
			//Set Current year
			$args['date_query'][]['year'] =  date( 'Y' );
		}else if( !empty($_REQUEST['filter_type']) && $_REQUEST['filter_type']== 'last_7days' ){
			//Set Current Week
			$args['date_query'][]['year'] =  date( 'Y' );
			$args['date_query'][]['week'] =  date( 'W' );
		}else{
			//Default set current month
			$args['m']	= date('Ym');
		}

		//Get result based on argument
    	$results = $this->model->wpw_auto_poster_get_posting_logs_data( $args );

    	//Check data exist
    	if( !empty( $results['data'] ) ){

    		//modify data
    		foreach ( $results['data'] as $key => $value ){

    			$post_id     = $value['ID'];
    			$post_date   = date( 'd-M-Y',  strtotime($value['post_date']));
    			$social_type = get_post_meta( $post_id, $prefix . 'social_type', true );

    			//Check post network type
    			if( !empty($prepare[$post_date][$social_type]) ){
    				$prepare[$post_date][$social_type] = $prepare[$post_date][$social_type] + 1;
    			}else{
    				$prepare[$post_date][$social_type] = 1;
    			}
    		}

    		//Finalize prepared data
    		foreach ( $prepare as $key => $value ){

				$facebook = !empty( $value['fb'] )? $value['fb'] : 0;
				$twitter  = !empty( $value['tw'] )? $value['tw'] : 0;
				$linkedin  = !empty( $value['li'] )? $value['li'] : 0;
				$tumbler  = !empty( $value['tb'] )? $value['tb'] : 0;
				$bufferapp  = !empty( $value['ba'] )? $value['ba'] : 0;
				$instagram  = !empty( $value['ins'] )? $value['ins'] : 0;
				$pinterest  = !empty( $value['pin'] )? $value['pin'] : 0;

    			if( !empty($_REQUEST['social_type'])){
    				$final_array[] = array( $key, $value[$_REQUEST['social_type']] );
    			}else{
					$final_array[] = array( $key, $facebook, $twitter, $linkedin, $tumbler, $bufferapp, $instagram , $pinterest);    				
    			}
    		}
    	}else{
    		if( !empty($_REQUEST['social_type'])){
    			$final_array[] = array( date('d-M-Y'), 0,);
    		}else{
				$final_array[] = array( date('d-M-Y'), 0, 0, 0, 0, 0, 0, 0);
    		}
    	}
    	echo  json_encode($final_array);
    	exit();
    }

    /**
     * Adding Hooks
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function add_hooks() {

        // if the user can edit plugin options, let the fun begin!
        add_action('admin_menu', array($this, 'wpw_auto_poster_add_settings_page'));
        add_action('admin_init', array($this, 'wpw_auto_poster_init'));

        //post to social media when post or page or custom post type will be published
        add_action('save_post', array($this, 'wpw_auto_poster_post_to_social_media'), 15, 2);

        //add for schedule posting
        add_action('publish_future_post', array($this, 'wpw_auto_poster_schedule_posting'));

        //show admin notices
        add_action('admin_notices', array($this, 'wpw_auto_poster_admin_notices'));

        //add admin init for bult delete functionality
        add_action('admin_init', array($this, 'wpw_auto_poster_posted_logs_bulk_delete'));

        //add admin init for bult scheduling functionality
        add_action('admin_init', array($this, 'wpw_auto_poster_scheduling_bulk_process'));

        // add filter to add validate settings
        add_filter('wpw_auto_poster_validate_settings', array($this, 'wpw_auto_poster_validate_setting'), 10, 2);

        //add filter to add custom schedule
        add_filter('cron_schedules', array($this, 'wpw_auto_poster_add_custom_scheduled'));

        //add action to call schedule cron for send wall post
        add_action('wpw_auto_poster_scheduled_cron', array($this, 'wpw_auto_poster_scheduled_cron'));
        

        //Remove post meta for status from wpml
        add_action('icl_make_duplicate', array($this, 'wpw_auto_poster_wpml_dup_remove_status_meta'), 10, 4);

        //Add meta in publish box
        add_action('post_submitbox_misc_actions', array($this, 'wpw_auto_poster_publish_meta'));

        //Add action to add hook for all taxonomy add or edit form
        add_action('wp_loaded', array($this, 'wpw_auto_poster_hook_taxonomy'));

        //Add action to save posting social accounts for category
        add_action('created_term', array($this, 'wpw_auto_poster_category_fields_save'), 10, 3);

        //Add action to save posting social accounts for category
        add_action('edit_term', array($this, 'wpw_auto_poster_category_fields_save'), 10, 3);

        // Add filter to post pretty url instead wordpress default
        add_filter( 'wpw_custom_permalink', array( $this, 'wpw_auto_poster_is_wp_pretty_url'), 10, 3 );

        // Add action to generate and download system report file
		// add_action( 'admin_init', array( $this, 'wpw_auto_poster_generate_system_log' ) );

		// Add action to fecth categories from post type
		add_action('wp_ajax_wpw_auto_poster_get_category', array($this, 'wpw_auto_poster_get_category'));
		add_action('wp_ajax_nopriv_wpw_auto_poster_get_category', array($this, 'wpw_auto_poster_get_category'));

        // Add action to fecth categories from custom post type
        add_action('wp_ajax_wpw_auto_poster_get_taxonomies', array($this, 'wpw_auto_poster_get_taxonomies'));
        add_action('wp_ajax_nopriv_wpw_auto_poster_get_taxonomies', array($this, 'wpw_auto_poster_get_taxonomies'));
        
        		// Add action to fecth Graph data
		add_action('wp_ajax_wpw_auto_poster_logs_graph', array($this, 'wpw_auto_poster_logs_graph_process'));
		add_action('wp_ajax_nopriv_wpw_auto_poster_logs_graph', array($this, 'wpw_auto_poster_logs_graph_process'));
    }

}
