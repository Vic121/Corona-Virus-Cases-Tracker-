<?php
/**
 * Plugin Name:Corona Virus Cases Tracker (COVID-19 Tracker)
 * Description:Corona Virus Cases Tracker [cvct country-code="all" style="style-2" title="Global Stats" label-total="Total Cases" label-deaths="Deaths" label-active="Active Cases" label-recovered="Recovered" bg-color="#DDDDDD" font-color="#000"]
 * Author:Cool Plugins
 * Author URI:https://coolplugins.net/
 * Plugin URI:https://cryptowidget.coolplugins.net/
 * Version:1.7
 * License: GPL2
 * Text Domain:cvct
 * Domain Path: languages
 *
 *@package Corona Virus Cases Tracker*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'CVCT_VERSION' ) ) {
	return;
}
/*
Defined constent for later use
*/
define( 'CVCT_VERSION', '1.7' );
define( 'CVCT_FILE', __FILE__ );
define( 'CVCT_API_ENDPOINT', 'https://tvf5ksnujo01bafz.disease.sh' );
define( 'CVCT_Cache_Timing', 5 * MINUTE_IN_SECONDS);
define( 'CVCT_DIR', plugin_dir_path( CVCT_FILE ) );
define( 'CVCT_URL', plugin_dir_url( CVCT_FILE ) );
/**
 * Class Corona Virus Cases Tracker
 */
final class Corona_Virus_Cases_Tracker {

	/**
	 * Plugin instance.
	 *
	 * @var Corona_Virus_Cases_Tracker
	 * @access private
	 */
	private static $instance = null;

	/**
	 * Get plugin instance.
	 *
	 * @return Corona_Virus_Cases_Tracker
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
	 * @access private
	 */
	private function __construct() {
		// register activation/ deactivation hooks
		register_activation_hook( CVCT_FILE, array( $this , 'cvct_activate' ) );
		register_deactivation_hook(CVCT_FILE, array($this , 'cvct_deactivate' ) );
		// include required files
		$this->cvct_includes();
		// load text domain for translation
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array($this,'cvct_delete_all_cache') );

		if(is_admin()){
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this,'cvct_setting_panel_action_link'));       
		}
	
		if(!is_admin()){
			add_action('wp_enqueue_scripts','cvct_load_assets' );
		}
	}

	public function cvct_delete_all_cache( ) {
	$delete_all_cache=get_option('cvct_delete_all_cache');
		if($delete_all_cache==false){
			delete_transient('cvct_global_data');
			delete_transient('cvct_gs');
			delete_transient('historical_data_all-15');
			delete_transient('cvct_countries_data');
			delete_transient('cvct_india_states_data');
			delete_transient('cvct_state_data_USA');
			$countries_arr=get_country_arr();
			$days_data=30;
			foreach($countries_arr as $country_code=>$name){
				delete_transient('cvct_cs_'.$name);
				delete_transient('historical_data_'.$country_code.'-'.$days_data);
			}

			update_option('cvct_delete_all_cache',true);
		} 
	}	
	
	
/*
|--------------------------------------------------------------------------
| Load required files
|--------------------------------------------------------------------------
*/  
	public function cvct_includes() {
		//loading required functions
				
		require_once CVCT_DIR .'includes/cvct-functions.php';
		require_once CVCT_DIR . 'includes/shortcodes/cvct-cards-shortcode.php';
		new CVCT_Shortcode();
		require_once CVCT_DIR . 'includes/shortcodes/cvct-charts-shortcode.php';
		new CVCT_Charts_Shortcode();

		require_once CVCT_DIR . 'includes/shortcodes/cvct-maps-shortcode.php';
		new CVCT_Maps_Shortcode();

		require_once CVCT_DIR . 'includes/shortcodes/cvct-tables-shortcode.php';
		new CVCT_Table_Shortcode();
		require_once CVCT_DIR . 'includes/shortcodes/cvct-countrystats-shortcode.php';
		new CVCT_CountryStats();

		require_once CVCT_DIR . 'includes/shortcodes/cvct-advance-shortcode.php';
		new CVCT_Advance_Shortcode();
		require_once CVCT_DIR . 'includes/shortcodes/cvct-historical-charts-shortcode.php';
		new CVCT_HISTORICAL_Shortcode();
		require_once CVCT_DIR . 'includes/shortcodes/cvct-spread-trends.php';
		new CVCT_SPREAD_TREND_Shortcode();

		if(is_admin()){
			require_once CVCT_DIR .'includes/admin/cvct-feedback-notice.php';
			new CVCTProFeedbackNotice();
			
			require_once CVCT_DIR .'includes/admin/cvct-post-type.php';
			new CVCTPostType();

			require_once CVCT_DIR .'includes/admin/CoronaVirusCasesTrackerBase.php';
			new CoronaVirusCasesTrackerBase(__FILE__);
		}
		
	}
	/**
	 * Code you want to run when all other plugins loaded.
	 */
	public function load_textdomain() {
		load_plugin_textdomain('cvct', false, basename(dirname(__FILE__)) . '/languages/' );
	}
	/**
	 * Run when activate plugin.
	 */
	public function cvct_activate() {
		update_option("cvct-type","PRO");
		update_option("cvct_activation_time",date('Y-m-d h:i:s') );
		update_option("cvct-alreadyRated","no");
	}
	// other shortcodes link in all plugins section
	function cvct_setting_panel_action_link($link){
		$link[] = '<a style="font-weight:bold" href="'. esc_url( get_admin_url(null, 'edit.php?post_type=cvct&page=cvct_shortcodes') ) .'">Shortcodes</a>';
		return $link;
    }
	public function cvct_deactivate(){
		delete_transient('cvct_gs');
	}
}
function Corona_Virus_Cases_Tracker() {
	return Corona_Virus_Cases_Tracker::get_instance();
}
Corona_Virus_Cases_Tracker();
