<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Misc Functions
 * 
 * 
 * @package Social Auto Poster
 * @since 1.0.0
 */

/**
 * Get Settings From Option Page
 * 
 * Handles to return all settings value
 * 
 * @package Social Auto Poster
 * @since 1.0.0
 */
function wpw_auto_poster_settings() {
	
	$settings = is_array(get_option('wpw_auto_poster_options')) ? get_option('wpw_auto_poster_options') : array();
	
	return $settings;
}

/**
 * Initialize some intial setup
 * 
 * @package Social Auto Poster
 * @since 1.0.0
 */
function wpw_auto_poster_initialize() {
	
	global $wpw_auto_poster_options;
	
	// Facebook Application ID and Secret
	$fb_apps = wpw_auto_poster_get_fb_apps();
	
	if( !empty($_GET['wpw_fb_app_id']) ) {
		$fb_app_id = $_GET['wpw_fb_app_id'];
	} else {
		$fb_app_keys = array_keys($fb_apps);
		$fb_app_id = reset( $fb_app_keys );
	}
	$fb_app_secret 	= isset( $fb_apps[$fb_app_id] ) ? $fb_apps[$fb_app_id] 	: '';
	
	if( !defined( 'WPW_AUTO_POSTER_FB_APP_ID' ) ) {
		define( 'WPW_AUTO_POSTER_FB_APP_ID', $fb_app_id );
	}
	if( !defined( 'WPW_AUTO_POSTER_FB_APP_SECRET' ) ) {
		define( 'WPW_AUTO_POSTER_FB_APP_SECRET', $fb_app_secret );
	}
	
	// Defining the session variables
	if( !defined( 'WPW_AUTO_POSTER_FB_SESS1' ) ) {
		define( 'WPW_AUTO_POSTER_FB_SESS1', 'fb_'.WPW_AUTO_POSTER_FB_APP_ID.'_code' );
	}
	if( !defined( 'WPW_AUTO_POSTER_FB_SESS2' ) ) {
		define( 'WPW_AUTO_POSTER_FB_SESS2', 'fb_'.WPW_AUTO_POSTER_FB_APP_ID.'_access_token' );
	}
	if( !defined( 'WPW_AUTO_POSTER_FB_SESS3' ) ) {
		define( 'WPW_AUTO_POSTER_FB_SESS3', 'fb_'.WPW_AUTO_POSTER_FB_APP_ID.'_user_id' );
	}
	if( !defined( 'WPW_AUTO_POSTER_FB_SESS4' ) ) {
		define( 'WPW_AUTO_POSTER_FB_SESS4', 'fb_'.WPW_AUTO_POSTER_FB_APP_ID.'_state' );
	}
	
	// Twitter Consumer Key and Secret
	$tw_consumer_key = isset( $wpw_auto_poster_options['twitter_keys'] ) && isset( $wpw_auto_poster_options['twitter_keys']['0'] ) ? $wpw_auto_poster_options['twitter_keys']['0']['consumer_key'] : '';
	$tw_consumer_secret = isset( $wpw_auto_poster_options['twitter_keys'] ) && isset( $wpw_auto_poster_options['twitter_keys']['0'] ) ? $wpw_auto_poster_options['twitter_keys']['0']['consumer_secret'] : '';
	$tw_auth_token = isset( $wpw_auto_poster_options['twitter_keys'] ) && isset( $wpw_auto_poster_options['twitter_keys']['0'] ) ? $wpw_auto_poster_options['twitter_keys']['0']['oauth_token'] : '';
	$tw_auth_token_secret = isset( $wpw_auto_poster_options['twitter_keys'] ) && isset( $wpw_auto_poster_options['twitter_keys']['0'] ) ? $wpw_auto_poster_options['twitter_keys']['0']['oauth_secret'] : '';
	
	if( !defined( 'WPW_AUTO_POSTER_TW_CONS_KEY' ) ) {
		define( 'WPW_AUTO_POSTER_TW_CONS_KEY', $tw_consumer_key );
	}
	if( !defined( 'WPW_AUTO_POSTER_TW_CONS_SECRET' ) ) {
		define( 'WPW_AUTO_POSTER_TW_CONS_SECRET', $tw_consumer_secret );
	}
	if( !defined( 'WPW_AUTO_POSTER_TW_AUTH_TOKEN' ) ) {
		define( 'WPW_AUTO_POSTER_TW_AUTH_TOKEN', $tw_auth_token );
	}
	if( !defined( 'WPW_AUTO_POSTER_TW_AUTH_SECRET' ) ) {
		define( 'WPW_AUTO_POSTER_TW_AUTH_SECRET', $tw_auth_token_secret );
	}
	
	//LinkedIn Consumer Key and Secret
	$li_app_id = isset( $wpw_auto_poster_options[ 'linkedin_app_id'] ) ? $wpw_auto_poster_options[ 'linkedin_app_id'] : '';
	$li_app_secret = isset( $wpw_auto_poster_options[ 'linkedin_app_secret'] ) ? $wpw_auto_poster_options[ 'linkedin_app_secret'] : '';
	
	if( !defined( 'WPW_AUTO_POSTER_LI_APP_ID' ) ) {
		define( 'WPW_AUTO_POSTER_LI_APP_ID', $li_app_id);
	}
	if( !defined( 'WPW_AUTO_POSTER_LI_APP_SECRET' ) ) {
		define( 'WPW_AUTO_POSTER_LI_APP_SECRET', $li_app_secret );
	}
	if( !defined( 'WPW_AUTO_POSTER_LINKEDIN_PORT_HTTP' ) ) { //http port value
	 	define( 'WPW_AUTO_POSTER_LINKEDIN_PORT_HTTP', '80' );
	}
	if( !defined( 'WPW_AUTO_POSTER_LINKEDIN_PORT_HTTP_SSL' ) ) { //ssl port value
	  	define( 'WPW_AUTO_POSTER_LINKEDIN_PORT_HTTP_SSL', '443' );
	}
	
	//Tumblr Consumer Key and Secret
	$tb_consumer_key = isset( $wpw_auto_poster_options[ 'tumblr_consumer_key' ] ) ? $wpw_auto_poster_options[ 'tumblr_consumer_key' ] : '';
	$tb_consumer_secret = isset( $wpw_auto_poster_options[ 'tumblr_consumer_secret' ] ) ? $wpw_auto_poster_options[ 'tumblr_consumer_secret' ] : '';
	
	if( !defined( 'WPW_AUTO_POSTER_TB_CONS_KEY' ) ) {
		define( 'WPW_AUTO_POSTER_TB_CONS_KEY', $tb_consumer_key );
	}
	if( !defined( 'WPW_AUTO_POSTER_TB_CONS_SECRET' ) ) {
		define( 'WPW_AUTO_POSTER_TB_CONS_SECRET', $tb_consumer_secret );
	}
	
	
	//BufferApp Client id and secret
	$ba_client_id = isset( $wpw_auto_poster_options[ 'bufferapp_client_id' ] ) ? $wpw_auto_poster_options['bufferapp_client_id'] : '';
	$ba_client_secret = isset( $wpw_auto_poster_options['bufferapp_client_secret' ] ) ? $wpw_auto_poster_options['bufferapp_client_secret'] : '';
	
	if( !defined( 'WPW_AUTO_POSTER_BA_CLIENT_ID' ) ) {
		define( 'WPW_AUTO_POSTER_BA_CLIENT_ID', $ba_client_id );
	}
	if( !defined( 'WPW_AUTO_POSTER_BA_CLIENT_SECRET' ) ) {
		define( 'WPW_AUTO_POSTER_BA_CLIENT_SECRET', $ba_client_secret );
	}

	// Pinterest Application ID and Secret added since 2.6.0
	$pin_apps = wpw_auto_poster_get_pin_apps();
	
	if( !empty($_GET['wpw_pin_app_id']) ) {
		$pin_app_id = $_GET['wpw_pin_app_id'];
	} else {
		$pin_app_keys = array_keys($pin_apps);
		$pin_app_id = reset( $pin_app_keys );
	}
	$pin_app_secret 	= isset( $pin_apps[$pin_app_id] ) ? $pin_apps[$pin_app_id] 	: '';
	
	if( !defined( 'WPW_AUTO_POSTER_PIN_APP_ID' ) ) {
		define( 'WPW_AUTO_POSTER_PIN_APP_ID', $pin_app_id );
	}
	if( !defined( 'WPW_AUTO_POSTER_PIN_APP_SECRET' ) ) {
		define( 'WPW_AUTO_POSTER_PIN_APP_SECRET', $pin_app_secret );
	}
}

/**
 * Get Social Auto poster Screen ID
 * 
 * Handles to get social auto poster screen id
 * 
 * @package Social Auto Poster
 * @since 1.8.1
 */
function wpw_auto_poster_get_sap_screen_id() {
	
	$wpsap_screen_id = sanitize_title( __( 'Social Auto Poster', 'wpwautoposter' ) );
	return apply_filters( 'wpw_auto_poster_get_sap_screen_id', $wpsap_screen_id );
}

/**
 * Get Social Auto poster Screen ID
 * 
 * Handles to get social auto poster screen id
 * 
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_get_fb_apps() {
	
	global $wpw_auto_poster_options;
	
	$fb_apps	= array();
	$fb_keys 	= !empty($wpw_auto_poster_options['facebook_keys']) ? $wpw_auto_poster_options['facebook_keys'] : array();
	
	if( !empty( $fb_keys ) ) {
		
		foreach ( $fb_keys as $fb_key_id => $fb_key_data ){
			
			if( !empty( $fb_key_data['app_id'] ) && !empty($fb_key_data['app_secret']) ) {
				$fb_apps[ $fb_key_data['app_id'] ] = $fb_key_data['app_secret'];
			}
			
		} // End of for each
	} // End of main if
	
	return $fb_apps;
}

/**
 * Get Social Auto poster Screen ID
 * 
 * Handles to get social auto poster screen id
 * 
 * @package Social Auto Poster
 * @since 2.2.0
 */
function wpw_auto_poster_get_fb_accounts( $data_type = false ) {
	
	// Taking some defaults
	$res_data = array();
	
	// Get stored fb app grant data
	$wpw_auto_poster_fb_sess_data = get_option( 'wpw_auto_poster_fb_sess_data' );
	
	if( is_array( $wpw_auto_poster_fb_sess_data ) && !empty($wpw_auto_poster_fb_sess_data) ) {
		
		foreach ( $wpw_auto_poster_fb_sess_data as $fb_sess_key => $fb_sess_data ) {
			
			$fb_sess_acc 	= isset( $fb_sess_data['wpw_auto_poster_fb_user_accounts']['auth_accounts'] ) 	? $fb_sess_data['wpw_auto_poster_fb_user_accounts']['auth_accounts'] 	: array();
			$fb_sess_token 	= isset( $fb_sess_data['wpw_auto_poster_fb_user_accounts']['auth_tokens'] ) 	? $fb_sess_data['wpw_auto_poster_fb_user_accounts']['auth_tokens'] 		: array();
			
			// Retrives only App Users
			if( $data_type == 'all_app_users' ) {
				
				// Loop of account and merging with page id and app key
				foreach ( $fb_sess_acc as $fb_page_id => $fb_page_name ) {
					$res_data[$fb_sess_key][] = $fb_page_id .'|'. $fb_sess_key;
				}
				
			} elseif( $data_type == 'all_app_users_with_name' ) {
				
				// Loop of account and merging with page id and app key
				foreach ( $fb_sess_acc as $fb_page_id => $fb_page_name ) {
					$res_data[$fb_sess_key][$fb_page_id .'|'. $fb_sess_key] = $fb_page_name;
				}
				
			} elseif ( $data_type == 'app_users' ) {
				
				$res_data[$fb_sess_key] = ( !empty($fb_sess_acc) && is_array($fb_sess_acc) ) ? array_keys( $fb_sess_acc ) : array();
				
			} elseif ( $data_type == 'all_auth_tokens' ) {
				
				// Loop of tokens and merging with page id and app key
				foreach ( $fb_sess_token as $fb_sess_token_id => $fb_sess_token_data ) {
					$res_data[$fb_sess_token_id .'|'. $fb_sess_key] = $fb_sess_token_data;
				}
				
			} elseif ( $data_type == 'auth_tokens' ) {
				
				// Merging the array
				$res_data = $res_data + $fb_sess_token;
				
			} elseif ( $data_type == 'all_accounts' ) {
				
				// Loop of account and merging with page id and app key
				foreach ( $fb_sess_acc as $fb_page_id => $fb_page_name ) {
					$res_data[$fb_page_id .'|'. $fb_sess_key] = $fb_page_name;
				}
				
			} else {
				
				// Merging the array
				$res_data = $res_data + $fb_sess_acc;
				
			}
		}
	}
	
	return $res_data;
}

/**
 * Check Extra Security
 * 
 * Handles to check extra security
 * 
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_extra_security( $post_id, $post ) {
	
	$extra_security	= false;

	if( ( ! isset( $_POST['post_ID'] ) || $post_id != $_POST['post_ID'] ) ) {
		$extra_security	= true;
	}
	
	$post_type_object = get_post_type_object( $post->post_type );

	// 
	$wpw_auto_poster_set_option = get_option( 'wpw_auto_poster_options' );
	
	if( ( isset( $wpw_auto_poster_set_option['autopost_thirdparty_plugins'] ) && $wpw_auto_poster_set_option['autopost_thirdparty_plugins'] == 1 ) ) {
		
		$extra_security	= false;
	}
	
	/**
	 * Current user can edit post not working on cron. Added compability of WordPress Automatic Plugin
	 * 
	 * Change code to solved post save capability issue with thirdparty plugin
	 */
	if( ( !isset( $wpw_auto_poster_set_option['autopost_thirdparty_plugins'] ) || $wpw_auto_poster_set_option['autopost_thirdparty_plugins'] != 1 ) && ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) )  ) {
		$extra_security	= true;
	}
	
	$extra_security = apply_filters( 'wpw_auto_poster_extra_security', $extra_security, $post_id );
	
	return $extra_security;
}

/**
 * Get all configured Pinterest accounts
 * 
 * Handler to get all configured pinterest account on settings page
 * 
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_pin_apps() {
	
	global $wpw_auto_poster_options;
	
	$pin_apps	= array();
	$pin_keys 	= !empty($wpw_auto_poster_options['pinterest_keys']) ? $wpw_auto_poster_options['pinterest_keys'] : array();
	
	if( !empty( $pin_keys ) ) {
		
		foreach ( $pin_keys as $pin_key_id => $pin_key_data ){
			
			if( !empty( $pin_key_data['app_id'] ) && !empty($pin_key_data['app_secret']) ) {
				$pin_apps[ $pin_key_data['app_id'] ] = $pin_key_data['app_secret'];
			}
			
		} // End of for each
	} // End of main if
	
	return $pin_apps;
}

/**
 * Get Granted Pinterest Account
 * 
 * Handles to get all granted pinterest account as per requirement
 * 
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_pin_accounts( $data_type = false ) {
	
	// Taking some defaults
	$res_data = array();
	
	// Get stored pin app grant data
	$wpw_auto_poster_pin_sess_data = get_option( 'wpw_auto_poster_pin_sess_data' );
	
	if( is_array( $wpw_auto_poster_pin_sess_data ) && !empty($wpw_auto_poster_pin_sess_data) ) {
		
		foreach ( $wpw_auto_poster_pin_sess_data as $pin_sess_key => $pin_sess_data ) {
			
			$pin_sess_acc_boards 	= isset( $pin_sess_data['wpw_auto_poster_pin_user_boards'] ) 	? $pin_sess_data['wpw_auto_poster_pin_user_boards'] : array();
			$pin_sess_token 	= isset( $pin_sess_data['wpw_auto_poster_pin_token'] ) 	? $pin_sess_data['wpw_auto_poster_pin_token'] : array();
			
			if( $data_type == 'all_app_users_with_boards' ) {
				
				// Loop of account and merging with board id and app key
				foreach ( $pin_sess_acc_boards as $pin_board_id => $pin_board_name ) {
					$res_data[$pin_sess_key][$pin_board_id .'|'. $pin_sess_key] = $pin_board_name;
				}
				
			} elseif ( $data_type == 'all_accounts' ) {
				
				// Loop of account and merging with board id and app key
				foreach ( $pin_sess_acc_boards as $pin_board_id => $pin_board_name ) {
					$res_data[$pin_board_id .'|'. $pin_sess_key] = $pin_board_name;
				}
				
			} elseif ( $data_type == 'all_auth_tokens' ) {
				$res_data[$pin_sess_key] = $pin_sess_token;

			} else {
				
				// Merging the array
				$res_data = $res_data + $pin_sess_acc_boards;
				
			}
		}
	}
	return $res_data;
}

/**
 * Get all catgeories list for all post types
 *
 * Handles to fetch categories for all custom post types
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_all_categories() {

    $all_types 	= get_post_types(array('public' => true), 'objects');

    $all_types 	= is_array($all_types) ? $all_types : array();
    $data 		= array();

    // If $_POST for post type value is not empty
    if ( !empty($all_types) ) {

        foreach ( $all_types as $type) {

            if ( !is_object($type))
                continue;

            $label = @$type->labels->name ? $type->labels->name : $type->name;
            $post_type = $type->name;
            $categories_array = array();

            if ( $label == 'Media' || $label == 'media')
                continue; // skip media

            $all_taxonomies = get_object_taxonomies($post_type, 'objects');

            // Loop on all taxonomies
            foreach ( $all_taxonomies as $taxonomy) {

                if ( is_object($taxonomy) && !empty($taxonomy->hierarchical)) {

                    $categories = get_terms( $taxonomy->name, array('hide_empty' => false)); // Get categories

                    foreach ( $categories as $category) {

                        $categories_array[$category->slug] = $category->name;
                    }
                }
            }
            if ( !empty($categories_array) ) {

            	$data[$post_type]['label'] = $label;
                $data[$post_type]['categories'] = $categories_array;
                unset($categories_array);
            }
        }
    }
    
    return $data;
}

/**
 * Get all static list for all post types - post/ download / product
 *
 * Handles to fetch taxonomy for static post types
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */

function wpw_auto_poster_get_static_tag_taxonomy() {

	$result = array(
		'post'=> 'post_tag',
		'download' => 'download_tag',
		'product' => 'product_tag'
	);

	return $result;
}


/**
 * Get all selected categories for a post type
 *
 * Handles to fetch selected categories for custom post types
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_post_categories( $post_type, $postid ) {

	$categories = array();

	$all_taxonomies = get_object_taxonomies( $post_type, 'objects');

	if( !empty( $all_taxonomies ) ) {
	    // Loop on all taxonomies
	    foreach ($all_taxonomies as $taxonomy) {

	        if (is_object($taxonomy) && !empty($taxonomy->hierarchical)) {

	            $taxonomy_name = $taxonomy->name;
	        }
	    }

	    if(!empty( $taxonomy_name )) {

		    $term_list = wp_get_post_terms( $postid, $taxonomy_name, array("fields" => "slugs") );
		    
		    foreach($term_list as $term_single) {
		        
		        $categories[] = $term_single;
		    }
		}
	}

    return $categories;
}

/**
 * Get all selected categories term_id for a post type
 *
 * Handles to fetch selected categories term_id for custom post types
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_post_categories_by_ID( $post_type, $postid ) {

	$categoriesID = array();

	$all_taxonomies = get_object_taxonomies( $post_type, 'objects');

	if( !empty( $all_taxonomies ) ) {
	    // Loop on all taxonomies
	    foreach ($all_taxonomies as $taxonomy) {

	        if (is_object($taxonomy) && !empty($taxonomy->hierarchical)) {

	            $taxonomy_name = $taxonomy->name;
	        }
	    }

	    if(!empty( $taxonomy_name )) {

		    $term_list = wp_get_post_terms( $postid, $taxonomy_name, array("fields" => "ids") );
		    
		    foreach($term_list as $term_single) {
		        
		        $categoriesID[] = $term_single;
		    }
		}
	}

    return $categoriesID;
}

/**
 * Add notice to session variable
 * 
 * @param type $message
 * @param type $notice_type
 *  error – error message displayed with a red border
 *  warning – warning message displayed with a yellow border
 *  success – success message displayed with a green border
 *  info -  info message displayed with a blue border
 * 
 * @package Social Auto Poster
 * @since 2.6.2
 */
function sap_add_notice( $message, $notice_type = 'success' ) {
    
    // get existing notices
    $notices = $_SESSION['sap_notices'];
    
    // add new notice
    $notices[ $notice_type ][] = $message;
    
    // store all notices
    $_SESSION['sap_notices'] = $notices;
}
