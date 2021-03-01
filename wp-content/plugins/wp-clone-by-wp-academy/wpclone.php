<?php
/**
 * Plugin Name: WP Clone
 * Description: Move or copy a WordPress site to another server or to another domain name, move to/from local server hosting, and backup sites.
 *      Author: Migrate
 *  Author URI: https://backupbliss.com
 *  Plugin URI: https://backupbliss.com
 * Text Domain: wp-clone
 * Domain Path: /languages
 *     Version: 2.3.1
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

// Exit on direct access
if (!defined('ABSPATH')) {
  exit;
}

require_once 'analyst/main.php';
analyst_init(array(
	'client-id' => '9zdex5mar85kmgya',
	'client-secret' => 'd5702a59d32c01c211316717493096485d5156e8',
	'base-dir' => __FILE__
));


/**
 *
 * @URI https://backupbliss.com
 */

include_once 'lib/functions.php';
include_once 'lib/class.wpc-wpdb.php';

$upload_dir = wp_upload_dir();

define('WPBACKUP_FILE_PERMISSION', 0755);
define('WPCLONE_ROOT',  rtrim(str_replace("\\", "/", ABSPATH), "/\\") . '/');
define('WPCLONE_BACKUP_FOLDER',  'wp-clone');
define('WPCLONE_DIR_UPLOADS',  str_replace('\\', '/', $upload_dir['basedir']));
define('WPCLONE_DIR_PLUGIN', str_replace('\\', '/', plugin_dir_path(__FILE__)));
define('WPCLONE_URL_PLUGIN', plugin_dir_url(__FILE__));
define('WPCLONE_DIR_BACKUP',  WPCLONE_DIR_UPLOADS . '/' . WPCLONE_BACKUP_FOLDER . '/');
define('WPCLONE_INSTALLER_PATH', WPCLONE_DIR_PLUGIN);
define('WPCLONE_WP_CONTENT' , str_replace('\\', '/', WP_CONTENT_DIR));
define('WPCLONE_ROOT_FILE_PATH' , __FILE__);


/* Init options & tables during activation & deregister init option */
register_activation_hook((__FILE__), 'wpa_wpclone_activate');
register_deactivation_hook(__FILE__ , 'wpa_wpclone_deactivate');
register_uninstall_hook(__FILE__ , 'wpa_wpclone_uninstall');
add_action('admin_menu', 'wpclone_plugin_menu');
add_action( 'wp_ajax_wpclone-ajax-size', 'wpa_wpc_ajax_size' );
add_action( 'wp_ajax_wpclone-ajax-dir', 'wpa_wpc_ajax_dir' );
add_action( 'wp_ajax_wpclone-ajax-delete', 'wpa_wpc_ajax_delete' );
add_action( 'wp_ajax_wpclone-ajax-uninstall', 'wpa_wpc_ajax_uninstall' );
add_action( 'wp_ajax_wpclone-search-n-replace', 'wpa_wpc_ajax_search_n_replace' );
add_action('admin_init', 'wpa_wpc_plugin_redirect');

function wpclone_plugin_menu() {
    add_menu_page (
        'WP Clone Plugin Options',
        'WP Clone',
        'manage_options',
        'wp-clone',
        'wpclone_plugin_options'
    );
}

function wpa_wpc_ajax_size() {

    check_ajax_referer( 'wpclone-ajax-submit', 'nonce' );

    $cached = get_option( 'wpclone_directory_scan' );
    $interval = 600; /* 10 minutes */

    if( false !== $cached && time() - $cached['time'] < $interval ) {
        $size = $cached;
        $size['time'] = date( 'i', time() - $size['time'] );
    } else {
        $size = wpa_wpc_dir_size( WP_CONTENT_DIR );
    }

    echo json_encode( $size );
    wp_die();

}

function wpa_wpc_ajax_dir() {

    check_ajax_referer( 'wpclone-ajax-submit', 'nonce' );
    if( ! file_exists( WPCLONE_DIR_BACKUP ) ) wpa_create_directory();
    wpa_wpc_scan_dir();
    wp_die();

}

function wpa_wpc_ajax_delete() {

    check_ajax_referer( 'wpclone-ajax-submit', 'nonce' );

    if( isset( $_REQUEST['fileid'] ) && ! empty( $_REQUEST['fileid'] ) ) {

        echo json_encode( DeleteWPBackupZip( $_REQUEST['fileid'] ) );


    }

    wp_die();

}

function wpa_wpc_ajax_uninstall() {

    check_ajax_referer( 'wpclone-ajax-submit', 'nonce' );
    if( file_exists( WPCLONE_DIR_BACKUP ) ) {
        wpa_delete_dir( WPCLONE_DIR_BACKUP );

    }

    if( file_exists( WPCLONE_WP_CONTENT . 'wpclone-temp' ) ) {
        wpa_delete_dir( WPCLONE_WP_CONTENT . 'wpclone-temp' );

    }

    delete_option( 'wpclone_backups' );
    wpa_wpc_remove_table();
    wp_die();

}

function wpa_wpc_ajax_search_n_replace() {
    check_ajax_referer( 'wpclone-ajax-submit', 'nonce' );
    global $wpdb;
    $search  = isset( $_POST['search'] ) ? $_POST['search'] : '';
    $replace = isset( $_POST['replace'] ) ? $_POST['replace'] : '';

    if( empty( $search ) || empty( $replace ) ) {
        echo '<p class="error">Search and Replace values cannot be empty.</p>';
        wp_die();
    }

    wpa_bump_limits();
    $report = wpa_safe_replace_wrapper( $search, $replace, $wpdb->prefix );
    echo wpa_wpc_search_n_replace_report( $report );

    wp_die();
}

function wpclone_plugin_options() {
    include_once 'lib/view.php';
}

function wpa_enqueue_scripts(){
    wp_register_script('clipboard', plugin_dir_url(__FILE__) . '/lib/js/clipboard.min.js', array('jquery'));
    wp_register_script('wpclone', plugin_dir_url(__FILE__) . '/lib/js/backupmanager.js', array('jquery'));
    wp_register_style('wpclone', plugin_dir_url(__FILE__) . '/lib/css/style.css');
    wp_localize_script('wpclone', 'wpclone', array( 'nonce' => wp_create_nonce( 'wpclone-ajax-submit' ), 'spinner' => esc_url( admin_url( 'images/spinner.gif' ) ) ) );
    wp_enqueue_script('clipboard');
    wp_enqueue_script('wpclone');
    wp_enqueue_style('wpclone');
    wp_deregister_script('heartbeat');
    add_thickbox();
}
if( isset($_GET['page']) && 'wp-clone' == $_GET['page'] ) add_action('admin_enqueue_scripts', 'wpa_enqueue_scripts');

function wpa_wpclone_activate() {

	//Control after activating redirect to settings page
	add_option('wpa_wpc_plugin_do_activation_redirect', true);

	wpa_create_directory();
}

function wpa_wpclone_deactivate() {

	//Control after activating redirect to settings page
	delete_option("wpa_activation_redirect_required");

    if( file_exists( WPCLONE_DIR_BACKUP ) ) {
        $data = "<Files>\r\n\tOrder allow,deny\r\n\tDeny from all\r\n\tSatisfy all\r\n</Files>";
        $file = WPCLONE_DIR_BACKUP . '.htaccess';
        file_put_contents($file, $data);
    }

}

function wpa_wpclone_uninstall() {
	//Control after activating redirect to settings page
	delete_option("wpa_activation_redirect_required");
}

function wpa_wpc_remove_table() {
    global $wpdb;
    $wp_backup = $wpdb->prefix . 'wpclone';
    $wpdb->query ("DROP TABLE IF EXISTS $wp_backup");
}

function wpa_create_directory() {
    $indexFile = (WPCLONE_DIR_BACKUP.'index.html');
    $htacc = WPCLONE_DIR_BACKUP . '.htaccess';
    $htacc_data = "Options All -Indexes";
    if (!file_exists($indexFile)) {
        if(!file_exists(WPCLONE_DIR_BACKUP)) {
            if(!mkdir(WPCLONE_DIR_BACKUP, WPBACKUP_FILE_PERMISSION)) {
                die("Unable to create directory '" . rtrim(WPCLONE_DIR_BACKUP, "/\\"). "'. Please set 0755 permission to wp-content.");
            }
        }
        $handle = fopen($indexFile, "w");
        fclose($handle);
    }
    if( file_exists( $htacc ) ) {
        @unlink ( $htacc );
    }
    file_put_contents($htacc, $htacc_data);
}

function wpa_wpc_import_db(){

    global $wpdb;
    $table_name = $wpdb->prefix . 'wpclone';

    if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'") === $table_name ) {

        $old_backups = array();
        $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpclone ORDER BY id DESC", ARRAY_A);

        foreach( $result as $row ) {

            $time = strtotime( $row['data_time'] );
            $old_backups[$time] = array(
                    'name' => $row['backup_name'],
                    'creator' => $row['creator'],
                    'size' => $row['backup_size']

            );

        }

        if( false !== get_option( 'wpclone_backups' ) ) {
            $old_backups = get_option( 'wpclone_backups' ) + $old_backups;
        }

        update_option( 'wpclone_backups', $old_backups );

        wpa_wpc_remove_table();

    }


}

function wpa_wpc_msnotice() {
    echo '<div class="error">';
    echo '<h4>WP Clone Notice.</h4>';
    echo '<p>WP Clone is not compatible with multisite installations.</p></div>';
}

if ( is_multisite() )
    add_action( 'admin_notices', 'wpa_wpc_msnotice');

function wpa_wpc_phpnotice() {
    echo '<div class="error">';
    echo '<h4>WP Clone Notice.</h4>';
    printf( '<p>WP Clone is not compatible with PHP %s, please upgrade to PHP 5.3 or newer.</p></div>', phpversion() );
}

if( version_compare( phpversion(), '5.3', '<' ) ){
    add_action( 'admin_notices', 'wpa_wpc_phpnotice');
}

function wpa_wpc_plugin_redirect() {

	//Control after activating redirect to settings page
	if (get_option('wpa_wpc_plugin_do_activation_redirect', false)) {

		delete_option('wpa_wpc_plugin_do_activation_redirect');

		wp_redirect(admin_url('admin.php?page=wp-clone'));
	}

}

/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */

/** –– **\
 * Notices handler
 * @since 2.3.0
 */
 // HTML Print the Notification
 function wp_clone_print_wpse1_2023($isWPClone = false) {
	 ?>
	 <div id="wpse1_2023_complete"<?php echo (($isWPClone)?' style="margin-left: 13px;"':'') ?>>
		 <div id="wpse1_2023" data-url="<?php echo get_site_url(); ?>">
			 <div id="wpse1_2023_container">

				 <div id="wpse1_2023_img">
					 <span></span>
				 </div>
				 <div id="wpse1_2023_text">
					 <span id="wpse1_2023_news">BIG NEWS:</span> The new WP Clone is live
				 </div>
				 <div id="wpse1_2023_btns">
					 <div id="wpse1_2023_install">
						 <button type="button" id="wpse1_2023_install_btn" name="button">Install it now</button>
						 <span>(from WP directory)</span>
					 </div>
					 <div id="wpse1_2023_other">
						 <div id="wpse1_2023_show">
							 ...or <a href="https://bit.ly/2JSp512" target="_blank">learn more</a>
						 </div>
						 <?php if (!$isWPClone): ?>
						 <div id="wpse1_2023_dismiss">
							 <a href="#" id="wpse1_2023_btn">Hide <span id="wpse1_2023_smile" style="opacity: 0;"> :(</span></a>
						 </div>
						 <?php endif; ?>
					 </div>
				 </div>

			 </div>
		 </div>
	 </div>
	 <?php
 }

	// Styles & scripts
	add_action('admin_enqueue_scripts', function () {

		// Get screen and pagenow
		global $pagenow;
		$screen_id = get_current_screen()->id;

		// 97-104 [a-h] // 48-57 [0-9]
		// $minL = ord('a'); $maxL = ord('f'); $numT = ord('w');
		// $dL = ord(substr(strtolower(parse_url(get_site_url())['host']), 0, 1));
		// if (!(($dL >= $minL && $maxL >= $dL) || ($dL >= 48 && 57 >= $dL) || ($numT == $dL))) return;

		if (get_option('_wps82023_now_already', false)) return;
		if (get_option('_wps82023_only_now', false)) return;

		// Only if not dismissed
		$already = false;
		$plugin_prefix = 'wpse1_2023';
		if (is_plugin_active('backup-backup/backup-backup.php')) $already = true;
		if (defined('WP_PLUGIN_DIR') && is_dir(WP_PLUGIN_DIR . '/backup-backup')) $already = true;
		$dismisses = get_option("__{$plugin_prefix}_notiad", false);
		if (($dismisses != false || $already) && ($already || (isset($screen_id) && $screen_id != 'toplevel_page_wp-clone')))
			if ($already || (array_key_exists(get_current_user_id(), $dismisses) && $dismisses[get_current_user_id()] == true)) return;

		// URL to plugin directory
		$curdir = dirname(__FILE__);
		$plug_url = plugins_url('', __FILE__);

		// URL to styles folder
		$stylURL =  '/' . "wpses/" . $plugin_prefix . '_notiad.min.css';
		$scriptURL = '/' . "wpses/". $plugin_prefix . '_notiad.min.js';

		// Enqueue them
		wp_enqueue_style($plugin_prefix . '-css-notiad', $plug_url . $stylURL, '', filemtime($curdir . $stylURL));
		wp_enqueue_script($plugin_prefix . '-js-notiad', $plug_url . $scriptURL, ['jquery'], filemtime($curdir . $scriptURL), true);

	});

	// Display
	add_action('admin_notices', function () {

		// Get screen and pagenow
		global $pagenow;
		$screen_id = get_current_screen()->id;

		// 97-121 [a-h] // 48-57 [0-9]
		// $minL = ord('a'); $maxL = ord('f'); $numT = ord('w');
		// $dL = ord(substr(strtolower(parse_url(get_site_url())['host']), 0, 1));
		// if (!(($dL >= $minL && $maxL >= $dL) || ($dL >= 48 && 57 >= $dL) || ($numT == $dL))) return;

		// Block other plugins to display this banner
		if (get_option('_wps82023_now_already', false)) return;
		else update_option('_wps82023_now_already', true);

		// Dismiss not completely
		if (get_option('_wps82023_only_now', false)) {
			delete_option('_wps82023_only_now');
			return;
		}

		// Prefixes
		$dissmissed = false;
		$already = false;
		$plugin_prefix = 'wpse1_2023';

		// If you want to see this banner again uncomment below two lines
		// delete_option('_wps82023_installed');
		// delete_option("__{$plugin_prefix}_notiad");

		// Stop on this
		if (get_option('_wps82023_installed', false) == true) return;

		if (is_plugin_active('backup-backup/backup-backup.php')) $already = true;
		if (defined('WP_PLUGIN_DIR') && is_dir(WP_PLUGIN_DIR . '/backup-backup')) $already = true;
		$dismisses = get_option("__{$plugin_prefix}_notiad", false);
		if (($dismisses != false || $already)) {
			if ($already || (array_key_exists(get_current_user_id(), $dismisses) && $dismisses[get_current_user_id()] == true)) {
				if ((isset($screen_id) && $screen_id != 'toplevel_page_wp-clone') || $already) return;
				else $dissmissed = true;
			}
		}

		// Plugins URL
		$url = plugin_dir_url(__FILE__);

		// URL to images folder
		$images = $url . 'wpses/' . $plugin_prefix;

		// Get plugins name
		$plugin_data = get_plugin_data(__FILE__);
		$plugin_name = $plugin_data['Name'];

		if (isset($screen_id) && $screen_id == 'toplevel_page_wp-clone') {
			if ($dissmissed) {
				?>
				<div id="wpse1_2023_wpclone">
					Have a look at <a href="#" id="wpse1_2023_wpclone_show">WP Clone’s successor</a>
				</div>
				<?php
				add_action('wp_clone_accessor_print', function () {
					wp_clone_print_wpse1_2023(true);
				});
			} else wp_clone_print_wpse1_2023(true);
		} else wp_clone_print_wpse1_2023(false);

	}, 10);

	// Handle dissmiss
	add_action('wp_ajax_wpse1_2023_btn', function () {
		$plugin_prefix = 'wpse1_2023';
		$dismisses = get_option("__{$plugin_prefix}_notiad", array());
		if (!is_array($dismisses)) $dismisses = array();
		$dismisses[get_current_user_id()] = true;
		update_option("__{$plugin_prefix}_notiad", $dismisses);
	});

	// Handle install
	add_action('wp_ajax_wpse1_2023_install', function () {

		if (get_option('_wps82023_now_already', false)) return;
		else update_option('_wps82023_now_already', true);

		function is_plugin_installed($slug) {
			$all_plugins = get_plugins();

			if (!empty($all_plugins[$slug])) return true;
			else return false;
		}

		function install_plugin($plugin_zip) {
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			wp_cache_flush();

			$upgrader = new Plugin_Upgrader();
			$installed = $upgrader->install($plugin_zip);

			return $installed;
		}

		function upgrade_plugin($plugin_slug) {
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			wp_cache_flush();

			$upgrader = new Plugin_Upgrader();
			$upgraded = $upgrader->upgrade($plugin_slug);

			return $upgraded;
		}

	  $plugin_slug = 'backup-backup/backup-backup.php';
	  $plugin_zip = 'https://downloads.wordpress.org/plugin/backup-backup.latest-stable.zip';

	  if (is_plugin_installed($plugin_slug)) {
	    upgrade_plugin($plugin_slug);
	    $installed = true;
	  } else $installed = install_plugin($plugin_zip);

	  if (!is_wp_error($installed) && $installed) {
	    $activate = activate_plugin($plugin_slug);

	    if (is_null($activate)) {
				update_option('_bmi_cool_installation', true);
				update_option('_wps82023_installed', true);
				update_option('_wps82023_now_already', false);
				echo json_encode(array('status' => 'success', 'url' => admin_url('admin.php?page=backup-migration')));
			}

	  } else {
			update_option('_wps82023_only_now', true);
			update_option('_wps82023_now_already', false);
			echo json_encode(array('status' => 'fail'));
		}

	});

	// End the action
	add_action('admin_footer', function () {
		update_option('_wps82023_now_already', false);
	});
/** –– **/
