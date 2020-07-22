<?php

if (!class_exists('CVCTProFeedbackNotice')) {
    class CVCTProFeedbackNotice {
        /**
         * The Constructor
         */
        public function __construct() {
            // register actions
         
            if(is_admin()){
                add_action( 'admin_notices',array($this,'admin_notice_for_reviews'));
                add_action( 'admin_print_scripts', array($this, 'load_script' ) );
                add_action( 'wp_ajax_cvct_dismiss_notice',array($this,'cvct_dismiss_review_notice' ) );
            }
        }

    /**
	 * Load script to dismiss notices.
	 *
	 * @return void
	 */
	public function load_script() {
		wp_register_script( 'cvct-feedback-notice-script', CVCT_URL. 'assets/js/cvct-admin-feedback-notice.js', array( 'jquery' ),null, true );
        wp_enqueue_script( 'cvct-feedback-notice-script' );
        wp_register_style( 'cvct-feedback-notice-styles',CVCT_URL.'assets/css/cvct-admin-feedback-notice.css' );
        wp_enqueue_style( 'cvct-feedback-notice-styles' );
    }
    // ajax callback for review notice
    public function cvct_dismiss_review_notice(){
        $rs=update_option( 'cvct-alreadyRated','yes' );
        echo  json_encode( array("success"=>"true") );
        exit;
    }
   // admin notice  
    public function admin_notice_for_reviews(){

        if( !current_user_can( 'update_plugins' ) ){
            return;
         }
         // get installation dates and rated settings
         $installation_date = get_option( 'cvct_activation_time' );
         $alreadyRated =get_option( 'cvct-alreadyRated' )!=false?get_option( 'cvct-alreadyRated'):"no";

         // check user already rated 
         if( $alreadyRated=="yes") {
                return;
            }

            // grab plugin installation date and compare it with current date
            $display_date = date( 'Y-m-d h:i:s' );
            $install_date= new DateTime( $installation_date );
            $current_date = new DateTime( $display_date );
            $difference = $install_date->diff($current_date);
            $diff_days= $difference->days;
          
            // check if installation days is greator then week
              if (isset($diff_days) && $diff_days>=1) {
                echo $this->create_notice_content();
              }
       }  

       // generated review notice HTML
       function create_notice_content(){
        $ajax_url=admin_url( 'admin-ajax.php' );
        $ajax_callback='cvct_dismiss_notice';
        $wrap_cls="notice notice-info is-dismissible";
        $img_path=CVCT_URL.'assets/images/cvct-logo.png';
        $p_name="Corona Virus Cases Tracker (COVID-19 Tracker)";
        $like_it_text='Rate Now! ★★★★★';
        $already_rated_text=esc_html__( 'I already rated it', 'ect' );
        $not_interested=esc_html__( 'Not Interested', 'ect' );
        $not_like_it_text=esc_html__( 'No, not good enough, i do not like to rate it!', 'ect' );
        $p_link=esc_url('https://codecanyon.net/item/corona-virus-cases-tracker-covid19-tracker/reviews/26055464');
        $message="We hope our plugin is helping your website visitors to view current worldwide covid-19 cases status.
         <br/>Please give us a rating on codecanyon, it works as a boost for us to work continuously on this plugin in this hard time.
         <br><strong> STAY HOME! STAY SAFE!</strong>";
        // $dont_want_rate=esc_html__( "Close It, I don't Want to Rate", 'ect' ); 
        $html='<div data-ajax-url="%8$s"  data-ajax-callback="%9$s" class="cool-feedback-notice-wrapper %1$s">
        <div class="logo_container"><a href="%5$s"><img src="%2$s" alt="%3$s"></a></div>
        <div class="message_container">%4$s
        <div class="callto_action">
        <ul>
            <li class="love_it"><a href="%5$s" class="like_it_btn button button-primary" target="_new" title="%6$s">%6$s</a></li>
            <li class="already_rated"><a href="javascript:void(0);" class="already_rated_btn button cvct_dismiss_notice" title="%7$s">%7$s</a></li>
            <li class="close_it"><a href="javascript:void(0);" class="already_rated_btn button cvct_dismiss_notice" title="%10$s">%10$s</a></li>
            
        </ul>
        <div class="clrfix"></div>
        </div>
        </div>
        </div>';

 return sprintf($html,
        $wrap_cls,
        $img_path,
        $p_name,
        $message,
        $p_link,
        $like_it_text,
        $already_rated_text,
        $ajax_url,// 8
        $ajax_callback,//9
        // $dont_want_rate,
        $not_interested
        );
        
       }

    } //class end

} 



