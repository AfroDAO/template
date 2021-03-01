<?php
/**
 * Plugin Name:Cryptocurrency Exchanges List PRO
 * Description:Cryptocurrency exchanges list pro WordPress plugin creates a list of 200+ best cryptocurrency exchanges by using crypto markets api data provided by coinexchangeprice.com public API. 
 * Author:Cool Plugins
 * Author URI:https://coolplugins.net
 * Version: 2.0
 * License: GPL2
 * Text Domain:celp
 * Domain Path: languages
 *
 * @package Cryptocurrency Exchanges List PRO
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'CELP_VERSION' ) ) {
	return;
}
/*
	Defined constent for later use
*/
define( 'CELP_VERSION', '2.0' );
define( 'CELP_FILE', __FILE__ );
define( 'CELP_PATH', plugin_dir_path( CELP_FILE ) );
define( 'CELP_URL', plugin_dir_url( CELP_FILE ) );


/**
 * Class CryptoCurrencyExchangesList
 */
final class Crypto_Currency_Exchanges_List {

	/**
	 * Plugin instance.
	 *
	 * @var CryptoCurrencyExchangesList
	 * @access private
	 */
	private static $instance = null;

	/**
	 * Get plugin instance.
	 *
	 * @return CryptoCurrencyExchangesList
	 * @static
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @access private
	 */
	private function __construct() {

		register_activation_hook( CELP_FILE, array( $this, 'celp_activate' ) );
	   register_deactivation_hook( CELP_FILE, array( $this, 'celp_deactivate' ) );
		
	   // including required files
		$this->includes();
		$this->installation_date();
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action('init', array($this, 'celp_rewrite_rule'));
		add_filter( 'query_vars', array($this,'celp_query_vars'));

		// run if plugin is just updated from previous version to new version
	    add_action( 'init', array($this,'celp_plugin_version_verify') );

		/*
		All Shortcodes
		*/
		add_shortcode( 'celp', 'celp_shortcode');
 
		// exchange detials handler class
		$celp_single=new Celp_Details_Shortcodes();

		// exchange prices for coin market cap single page.
		add_shortcode( 'celp-coin-exchanges','celp_coin_exchanges');


		//creating posttype for plugin settings panel
		add_action( 'init','celp_post_type');	
	
		// integrating cmb2 metaboxes in post type
		add_action( 'cmb2_admin_init',array( $this,'cmb2_celp_metaboxes'));
	
		// registering required script for plugin
		add_action( 'wp_enqueue_scripts', 'celp_register_scripts' );
		// check coin market cap plugin is activated.
		add_action( 'admin_init',array($this,'check_cmc_activated'));
		add_filter('display_post_states', array( $this, 'protected_post_states') );
		
		add_action('admin_init', array($this, 'celp_on_settings_save'));

		// rest api endpoint for sitemap generation 
		add_action('rest_api_init', function () {
			register_rest_route('exchanges-lists/v1', 'generate-sitemap', array(
				'methods' => 'GET',
				'callback' => array($this, 'ex_generate_sitemap')
			));
		});

		if(is_admin()){
			add_action('admin_init', array($this, 'celp_check_installation_time'));
			add_action('admin_init', array($this, 'celp_spare_me'), 5);
		}else{
			add_action('template_redirect', array($this, 'exchange_single_page_redirection'));
		}

		// Register schedule interval
		add_filter('cron_schedules', array($this, 'celp_cron_schedules'));
		// Register functions with cron hooks
		add_action('celp_exchange_data_update', 'celp_save_ex_data' );
	
		add_action('wp_ajax_celp_get_ex_list', 'celp_get_ex_list_data');
		add_action('wp_ajax_nopriv_celp_get_ex_list', 'celp_get_ex_list_data');
		
		add_action('wp_ajax_celp_get_coin_exchanges', 'celp_get_coin_exchanges_handler');
		add_action('wp_ajax_nopriv_celp_get_coin_exchanges', 'celp_get_coin_exchanges_handler');
		
	}


	//check coin market cap plugin is activated. then enable links
	function check_cmc_activated() {
 	if (is_plugin_active( 'coin-market-cap/coin-market-cap.php' ) || class_exists( 'CoinMarketCap' ) ) {
   		update_option('cmc-dynamic-links',true);
	  }else{
		update_option('cmc-dynamic-links',false);
	  }
	}
	
	/**
	 * Load plugin function files here.
	 */
	public function includes() {
		require_once __DIR__ . '/includes/celp-post-types.php';
		require_once(__DIR__ . '/includes/celp-exchanges-db.php');
		require_once(__DIR__ . '/includes/celp-exchanges-pairs-db.php');
		require_once __DIR__ . '/includes/celp-functions.php';
		require_once __DIR__ . '/includes/celp-shortcode.php';
		require_once __DIR__ . '/includes/celp-details-shortcodes.php';
		require_once __DIR__ . '/includes/celp-coin-exchanges.php';
		require_once __DIR__ . '/exchanges-disable-list/exchange-coins-list-class.php';
		
		//require_once(CMC_PATH . '/includes/cmc-coins-db.php');
		/**
		 * Get the bootstrap!
		 */
		 require_once __DIR__ . '/cmb2/init.php';
		 require_once __DIR__ . '/includes/celp-settings.php';

		 if( is_admin() ){
			require_once __DIR__ . '/includes/init-api.php';
		 }

	}
	
	/** creating settings panel using CMB2**/
	
	function cmb2_celp_metaboxes() {
	/**For exchange description settings**/
	
	/**
     * Initiate the metabox
     */
    $cmbdes = new_cmb2_box( array(
        'id'            => 'celp_exchange_des',
        'title'         => __( 'Exchange Description', 'celp1' ),
        'object_types'  => array( 'celp'), // Post type
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true, // Show field names on the left
   
	) );	
	$ex_id = '';
	// get exchange id from curreny post (if already saved)
	if( isset( $_GET['post'] ) ){
		$ex_id = get_post_meta( $_GET['post'], 'custom_ex_id', true);
	}
	// fetch all exchanges
	$all_celp_exc = celp_get_exchange_id_api_data();	
	// fetch already created custom exchange description
	$available_exchanges = celp_fetch_custom_exchanges();
	foreach( $available_exchanges as $exc ){
		// do not remove exchange id for current post
		if( !empty( $ex_id ) && $ex_id == $exc ) continue;
		unset( $all_celp_exc[ $exc ]);
	}

		$cmbdes->add_field( array(
		    'name'    => __('Select Exchange', 'celp'),
		    'desc'    => '',
		    'id'      => 'custom_ex_id',
		    'type'    => 'select',
		     'default' => '',
		    'options' => $all_celp_exc,
			'column' => array(
				'position' => 2,
				'name'     => __('Exchange Name', 'celp1'),
			),
			 ) );

		    $cmbdes->add_field( array(
		        'name' => __( 'Description', 'celp' ),
		        'id' =>'custom_description',
		        'type' => 'wysiwyg',
		    ) );

		    $cmbdes->add_field( array(
		        'name' => __( 'Affiliate Links', 'celp1' ),
		        'id' =>'affiliate_link',
		        'type' => 'text_url',
				'column' => array(
				'position' => 3,
				'name'     => __('Affiliate Links', 'celp1'),
			),
		    ) );
		}
	
		/**
		 * Code you want to run when all other plugins loaded.
		 */
		public function init() {
			load_plugin_textdomain( 'celp', false, basename(dirname(__FILE__)) . '/languages' );
		}

		/*
		|--------------------------------------------------------------------------
		|  Check if plugin is just updated from older version to new
		|--------------------------------------------------------------------------
		*/	
		public function celp_plugin_version_verify( ) {
			
			$CELP_VERSION = get_option('CELP_PRO_VERSION');
			if( !isset($CELP_VERSION) || version_compare( $CELP_VERSION, CELP_VERSION, '<' ) ){
				
				$this->ex_detail_page();
				$this->celp_rewrite_rule();
				if (!wp_next_scheduled('celp_exchange_data_update')) {
					wp_schedule_event(time(), 'celp-7-min', 'celp_exchange_data_update');
				}
				flush_rewrite_rules();	
				update_option('CELP_PRO_VERSION', CELP_VERSION );
			}
		}	// end of celp_plugin_version_verify()
		
	/**
	 * Run when activate plugin.
	 */
	 function celp_activate() {
		$this->celp_create_table();
		$this->ex_detail_page();
		$this->celp_rewrite_rule();
		if (!wp_next_scheduled('celp_exchange_data_update')) {
			wp_schedule_event(time(), 'celp-7-min', 'celp_exchange_data_update');
		}

	 flush_rewrite_rules();	
	}

	/**
	 * Run when deactivate plugin.
	 */
	 function celp_deactivate() {
		 $this->celp_delete_table();
		 wp_clear_scheduled_hook('celp_exchange_data_update');
		 delete_transient('celp-saved-ex');
	 	 flush_rewrite_rules();
	}

	/*
	*	Create new schedule interval if not already registered
	*/
	function celp_cron_schedules($schedules)
	{
		if (!isset($schedules["celp-7-min"])) {
			$schedules["celp-7-min"] = array(
				'interval' => 7 * 60,
				'display' => __('After every 7 minutes')
			);
		}
		return $schedules;
	}
	/*
	 saving plugin installation date for later use
	*/

	function installation_date(){
		 $get_installation_time = strtotime("now");
   	 	  add_option('celp_activation_time', $get_installation_time ); 
	}	

	//check if review notice should be shown or not

	function celp_check_installation_time() {

    $spare_me = get_option('celp_spare_me');
	    if( $spare_me!=1 ){
	        $install_date = get_option( 'celp_activation_time' );
	        $past_date = strtotime( '-1 days' );
	     
	      if ( $past_date >= $install_date ) {
			  add_action( 'admin_notices', array($this,'celp_display_admin_notice'));
		

	     		}
	    }
	}
	// remove the notice for the user if review already done or if the user does not want to
	function celp_spare_me()
	{
		if (isset($_GET['ex_spare_me']) && !empty($_GET['ex_spare_me'])) {
			$spare_me = $_GET['ex_spare_me'];
			if ($spare_me == 1) {
				update_option('celp_spare_me', 1);
			}
		}
	}
	
	
	/**
	* Display Admin Notice, asking for a review
	**/
	function celp_display_admin_notice() {
	    // wordpress global variable 
	    global $pagenow;
	//    if( $pagenow == 'index.php' ){
	 
	        $dont_disturb = esc_url( get_admin_url() . '?ex_spare_me=1' );
			$plugin_info = get_plugin_data( __FILE__ , true, true );
			$review_css = "
			<style>
				#menu-posts-celp-description .wp-menu-image img,
				#menu-posts-celp .wp-menu-image img {
					width: 20px;
					height: 20px;
					opacity: 1;
					padding-top: 8px;
				}
				.celp-review.wrap {
					background: #ffffff !important;
					max-width: 820px;
					padding: 5px;
					display: table;
					width: 100%;
					clear: both;
					border-radius: 5px;
					border: 2px solid #b7bfc7;
					box-sizing: border-box;
				}
				.celp-review.wrap img {
					width: 80px;
					display: table-cell;
					padding: 5px;
					margin-right: 20px;
					vertical-align: middle;
				}
				.celp-review.wrap p {
					display: table-cell;
					padding: 5px 20px 5px 5px;
					vertical-align: middle;
				}
				a.button.button-primary {
					margin-right: 8px;
				}
				a.button.button-secondary {
					margin-right: 8px;
				}
			</style>
			";	// end of css
			
			$reviewurl = esc_url( 'https://codecanyon.net/item/cryptocurrency-exchanges-list-pro-wordpress-plugin/reviews/22098669' );
	  	  printf(__('%s <div class="celp-review wrap"><img src="'.plugin_dir_url(__FILE__).'assets/celp-logo.png" /><p>You have been using <b> %s </b> for a while. We hope you liked it ! Please give us a quick rating, it works as a boost for us to keep working on the plugin !</br></br><a href="%s" class="button button-primary" target=
				"_blank">Rate Now!</a><a href="%s" class="celp-review-done button button-secondary"> Already Done !</a><a href="%s" class="celp-review-done button button-secondary"> Not Interested </a></p></div>', $plugin_info['TextDomain']),$review_css, $plugin_info['Name'], $reviewurl, $dont_disturb,$dont_disturb );
	       
	   // }
	}

	
	/*
	 check admin side post type page
	*/
	function celp_get_post_type_page() {
    global $post, $typenow, $current_screen;
 
	 if ( $post && $post->post_type ){
	        return $post->post_type;
	 }elseif( $typenow ){
	        return $typenow;
	  }elseif( $current_screen && $current_screen->post_type ){
	        return $current_screen->post_type;
	 }
	 elseif( isset( $_REQUEST['post_type'] ) ){
	        return sanitize_key( $_REQUEST['post_type'] );
	 }
	 elseif ( isset( $_REQUEST['post'] ) ) {
	   return get_post_type( $_REQUEST['post'] );
	 }
	  return null;
	}

	/*-----------------------------------------------------------------------------------|
    | 			The below function verify if the requested exchange is enabled			 |
	|			If the exchange is disabled, single page only shows 404 error			 |
	|		It will also return 404 error if exchange is not available in the database	 |
	|------------------------------------------------------------------------------------|
	*/
	function exchange_single_page_redirection(){
		GLOBAL $post;
		$page_id = get_option('ex-single-page-id');

		if( isset( $post->ID ) && $post->ID != $page_id) return;

			$ex_id = get_query_var( 'exchange-id' ) ;

			$db = new CELP_Exchanges();
			$exchange = !empty($ex_id)? $db->get_exchanges( array('ex_id'=> trim( $ex_id ) )) : null;

			if( $exchange == null ){
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				include( get_query_template( '404' ) );
				exit();
			}
	}
	
	function celp_on_settings_save(){
		if (
		 isset($_POST['submit-cmb']) && isset($_POST['action'])
			) {
				$new= $_POST['exchange-page-slug'];
				$slug = celp_get_option('exchange-page-slug');
				if($new!=$slug){
				add_filter('generate_rewrite_rules', array($this, 'celp_dynamic_rewrite_rules'));
				}
				

			}
	}
	
	// adding rewrite rule
	function celp_rewrite_rule() {
		$page_id=get_option('ex-single-page-id');
		$slug =celp_get_option('exchange-page-slug');
		$page_slug=!empty($slug)?$slug:"exchange";
		 add_rewrite_rule('^' . $page_slug . '/([^/]*)?$', 'index.php?page_id='.$page_id.'&exchange-id=$matches[1]', 'top');
	}

	// adding dyanmic rewrite rule after save changes in slug settings 		
	function celp_dynamic_rewrite_rules($wp_rewrite)
	{
		$page_id = get_option('ex-single-page-id');
		$slug = celp_get_option('exchange-page-slug');
		
		$page_slug = !empty($slug) ? $slug : "exchange";
		$feed_rules = array(
			'^' . $page_slug . '/([^/]*)?$' => 'index.php?page_id=' . $page_id . '&exchange-id=$matches[1]',
			
		);
		$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
		return $wp_rewrite->rules;
	} 

	// create query var for url 
		function celp_query_vars( $query_vars ){
			$query_vars[] = 'exchange-id';
			return $query_vars;
		}

	// generating dynamic page for exchange single page
	function ex_detail_page(){
		 
		 	$post_data = array(
		    'post_title' => 'cmc exchange details',
		    'post_type' => 'page',
		    'post_content'=>'[celp-detail][celp-dynamic-description][celp-description][celp-currencies-pairs][celp-twitter-feed]',
		     'post_status'   => 'publish',
		      'post_author'  => get_current_user_id(),
			);

			$single_page_id=get_option('ex-single-page-id');

			if('publish' === get_post_status( $single_page_id)){
			
			}else{
				$post_id = wp_insert_post( $post_data );
				update_option('ex-single-page-id',$post_id);
			}


		 }
		 
	function protected_post_states($states) {
		global $post;

		$custom_state	=	__("Don't Delete",'celp1');

    	if( $post->ID == get_option('ex-single-page-id') ){
    	   return $states[] = array( $custom_state );
		}
		return $states;
    
	}

	
	

		//generating sitemap 
	function ex_generate_sitemap()
	{
        /*---- Create cryptocurrency-exchanges-list-pro sitemap Upload  ----*/
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/cryptocurrency-exchanges-list-pro';        

        if (! is_dir($upload_dir)) {
            mkdir( $upload_dir );           
        }
        if(! is_dir($upload_dir.'/sitemap')){
            mkdir( $upload_dir.'/sitemap' );
        }

		$response = array();
		$api_response = celp_get_all_exchanges();
        $sitemap_url = home_url('wp-content/uploads/cryptocurrency-exchanges-list-pro/sitemap/', '/');

		if (!empty($api_response) && ( is_object($api_response) || gettype($api_response) == 'array' ) ) {
			$all_exchanges = celpobjectToArray($api_response);
			$this->ex_create_sitemap($all_exchanges);
			
			$combine_sitemap = "<?xml version='1.0' encoding='UTF-8'?>";
			$combine_sitemap .= "<sitemapindex xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>";

				$combine_sitemap .= "<sitemap>";
					$combine_sitemap .=	"<loc>". $sitemap_url . 'sitemap.xml' ."</loc>";
			//		$combine_sitemap .= "<lastmod>".date('Y-m-d',filemtime( rtrim(CMC_PATH,'/\\') . '\sitemap\sitemap-1.xml'))."</lastmod>";
					$combine_sitemap .= "</sitemap>";
			$combine_sitemap .= "</sitemapindex>";
			header('content-type: text/xml');
			echo $combine_sitemap;
			die();
		} else {
			$response[] = array(
				'status' => 'Error',
				'error' => 'API Request Timeout'
			);
			echo $rs = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			die();
		}


	}




	function ex_create_sitemap($all_exchanges)
	{
		$coins_xml = '';
		$sitemap = '';
		$slug = !empty(celp_get_option('exchange-page-slug')) ? celp_get_option('exchange-page-slug') : "exchange";
        $detail_page_url = esc_url(home_url($slug, '/'));
        
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $sitemap_dir = $upload_dir . '/cryptocurrency-exchanges-list-pro/sitemap';

		if (is_array($all_exchanges) && count($all_exchanges) > 0) {

			foreach ($all_exchanges as $key => $exchange) {
				 $exchange_content = (array)$exchange;
				$e_id = $exchange_content['ex_id'];
 

				$ex_url = $detail_page_url . '/' . $e_id.'/';
				$static = '12';
				$coins_xml .= '<url>' .
					'<loc>' . $ex_url . '</loc>' .
					'<priority>1</priority>' .
					'<changefreq>daily</changefreq>' .
					'</url>';
			}
			$sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
			$sitemap .= '<?xml-stylesheet type="text/xsl" href="' . CELP_URL . '/sitemap/sitemap-style.xsl"?>';
			$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			$sitemap .= $coins_xml;
			$sitemap .= '</urlset>';
			
            $fp = fopen($sitemap_dir . '/' . "sitemap.xml", 'w');
			fwrite($fp, $sitemap);
			fclose($fp);
		}
	}


/*
|--------------------------------------------------------------------------
| generating coins tables
|--------------------------------------------------------------------------
 */
function celp_create_table(){
	$celp_ex_db = new CELP_Exchanges;
	$celp_ex_pair_db = new CELP_Exchanges_Pairs;
	$celp_ex_db->create_table();
	$celp_ex_pair_db->create_table();
}
/*
|--------------------------------------------------------------------------
| deleting coins tables on plugin deactivation
|--------------------------------------------------------------------------
*/		 
function celp_delete_table(){
global $wpdb;
$coin_table = $wpdb->prefix . 'celp_exchanges';
$coin_meta_table = $wpdb->prefix . 'celp_exchanges_pairs';
$wpdb->query("DROP TABLE IF EXISTS " . $coin_table);
$wpdb->query("DROP TABLE IF EXISTS " . $coin_meta_table);
}


} // class end 

function Crypto_Currency_Exchanges_List() {
	return Crypto_Currency_Exchanges_List::get_instance();
}

$GLOBALS['Crypto_Currency_Exchanges_List'] = Crypto_Currency_Exchanges_List();
