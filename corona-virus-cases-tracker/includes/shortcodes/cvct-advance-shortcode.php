<?php
class CVCT_Advance_Shortcode
{
function __construct() {
   // add_shortcode( 'cvct-ticker', array($this, 'cvct_tickers_shortcode' ));
    
    add_shortcode( 'cvct-advance', array($this, 'cvct_tickers_shortcode' ));  
    
    //add_action( 'wp_enqueue_scripts',array($this,'CVCT_register_frontend_assets')); //registers js and css for frontend
    add_action( 'wp_footer', array($this,'cvct_ticker_in_footer') );
	
}

/*
|--------------------------------------------------------------------------
| Shows ticker in Footer
|--------------------------------------------------------------------------
*/
function cvct_ticker_in_footer(){

    if (!wp_script_is('jquery', 'done')) {
        wp_enqueue_script('jquery');
    }
    
    $ids = array();
    $header_id = get_option('cvct-p-id');
    $footer_id = get_option('cvct-fp-id');

    if($header_id != $footer_id){
        $ids=[$header_id,$footer_id];
    }
    else{
        $ids=[$header_id];
    }   

    if(!empty($ids)){
        foreach($ids as $id){
            $type = 'ticker';
            //------------------------------------------------------------------
            $page_select = get_post_meta($id,'cvct_ticker_disable', true ) ;
            $ids_arr= explode(',',$page_select);
            global $wp_query;
            $ticker_position = get_post_meta($id,'cvct_ticker_position', true );
            if($ticker_position=="header"||$ticker_position=="footer"){
                if ( is_object($wp_query->post) && !in_array($wp_query->post->ID,$ids_arr)){
                    echo do_shortcode("[cvct-advance id=".$id."]");
                }
            }
        }
    }
    

}

public function get_ticker_data( $selected_countries){
   if(is_array($selected_countries)){
    $countries_data=[];
    if (false !== $key = array_search('World', $selected_countries)) {
        $global = cvct_get_global_data();
        $k='World';
        $active_case= $global['total_cases'] - ($global['total_deaths']+$global['total_recovered']);
        $flag=CVCT_URL.'/assets/images/cvct-world.png';
        $countries_data[$k]['flag']=$flag;
        $countries_data[$k]['cases']=$global['total_cases'];
        $countries_data[$k]['deaths']=$global['total_deaths'];
        $countries_data[$k]['recovered']=$global['total_recovered'];
        $countries_data[$k]['active']=$active_case;
        unset($selected_countries[$key]);
    }
    $cvct_get_data = cvct_get_all_country_data();
if(is_array($cvct_get_data)&& count($cvct_get_data)){
    foreach($cvct_get_data as  $cvct_values){
        $country = $cvct_values['country'];
        if(!in_array($country,$selected_countries)){
             continue;
        }
       // $countryInfo= isset($cvct_values['all_data']->countryInfo)?$cvct_values['all_data']->countryInfo:'';
       // $flag=$cvct_values['flag'];
       
        $countries_data[$country]['flag']=$cvct_values['flag'];
        $countries_data[$country]['cases']=$cvct_values['cases'];
        $countries_data[$country]['deaths']=$cvct_values['deaths'];
        $countries_data[$country]['recovered']=$cvct_values['recoverd'];
        $countries_data[$country]['active']=$cvct_values['active'];
            }
        }
   }
   return $countries_data;
}

function CVCT_register_frontend_assets(){

    /**
     * Ticker assets
    */
    wp_register_style("cvct_tooltip_style",CVCT_URL.'assets/css/tooltipster.bundle.min.css',CVCT_VERSION,CVCT_VERSION);
    wp_register_script('cvct-tooltip-js', CVCT_URL . 'assets/js/tooltipster.bundle.min.js', array('jquery', 'cvct_bxslider_js'), CVCT_VERSION, true);
    wp_register_style("cvct_ticker_styles",CVCT_URL.'assets/css/cvct-tickers.min.css');
    wp_register_script('cvct_bxslider_js', '//cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js', array('jquery'), CVCT_VERSION, true);
    wp_register_script('cvct_ticker',CVCT_URL . 'assets/js/cvct-ticker.min.js', array('jquery', 'cvct_bxslider_js'),CVCT_VERSION, true);


    wp_register_style( 'cvct-slick-css', "https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css", array(), null );
    wp_register_script( 'cvct-slick-js', "https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js", array('jquery'), '1.0',false );
    wp_register_style( 'cvct-fontello', CVCT_URL . 'assets/css/cvct-fontello.css', array(),false);
		
}
function cvct_tickers_shortcode($atts, $content = null){
    $atts = shortcode_atts( array(
        'id'=>'style-1'
    ), $atts, 'cvct-ticker' );
    $post_id = $atts['id'];

    $post_status = get_post_status($post_id);
    $check_post_type = get_post_type($post_id);
   
    // make sure the post is already published!
    if( $check_post_type!='cvct' || $post_status != 'publish' ){
            return;
    }

    $ticker_position = get_post_meta($post_id,'cvct_ticker_position', true );
    $style = get_post_meta($post_id,'cvct_style', true );
    $selected_countries = get_post_meta($post_id,'cvct_select_countries', true );    
    $ticker_speed = get_post_meta($post_id,'cvct_ticker_speed', true );
    $ticker_speed = isset($ticker_speed)&&!empty($ticker_speed)?$ticker_speed:'10';  
    $t_speed =  $ticker_speed*1000;   
    $ticker_heading = get_post_meta($post_id,'cvct_ticker_heading', true );    
    $ticker_heading = isset($ticker_heading)&&!empty($ticker_heading)?$ticker_heading:__("Live Updates COVID-19 CASES","cvct"); 
    $background_color = get_post_meta($post_id,'cvct_bg_color', true );
    $text_color = get_post_meta($post_id,'cvct_text_color', true );
    $text_bg_color = get_post_meta($post_id,'cvct_text_bg_color', true );
    $ticker_desc = get_post_meta($post_id,'cvct_ticker_desc', true );

    $confirmed_label = get_post_meta($post_id,'cvct_confirmed_label', true );
    $confirmed_label = isset($confirmed_label)&&!empty($confirmed_label)?$confirmed_label:__("Confirmed","cvct");
        
    $active_label = get_post_meta($post_id,'cvct_active_label', true );
    $active_label = isset($active_label)&&!empty($active_label)?$active_label:__("Active","cvct"); 

    $death_label = get_post_meta($post_id,'cvct_death_label', true );
    $death_label = isset($death_label)&&!empty($death_label)?$death_label:__("Death","cvct"); 
    
    $recovered_label = get_post_meta($post_id,'cvct_recovered_label', true );
    $recovered_label = isset($recovered_label)&&!empty($recovered_label)?$recovered_label:__("Recovered","cvct"); 

    $dynamic_styles='';
    $bg_color=!empty($background_color)? "background-color:".$background_color."!important;":"background-color:#fff;";
    $fnt_color=!empty($text_color)? "color:".$text_color."!Important;":"color:#000;";
    $border_color=!empty($text_bg_color)? "border-color:".$text_bg_color."!important;":"border-color:red;";
    $text_bg_color=!empty($text_bg_color)? "background-color:".$text_bg_color."!important;":"background-color:red;";
    

    $custom_css = get_post_meta($post_id,'cvct_custom_css', true );
    
    $this->CVCT_register_frontend_assets();

    $total_count = 0;
    
    $output = '';
    $ticker_html = '';
    $ticker_id = "cvct-ticker-widget-" . esc_attr($post_id);
    if($style=="style-1"){
       wp_enqueue_script('cvct_ticker');
        wp_enqueue_script('cvct-tooltip-js');
        wp_enqueue_style('cvct_tooltip_style');    
        wp_enqueue_script('cvct_bxslider_js');
        //wp_enqueue_style('cvct_ticker_styles');
        wp_add_inline_script('cvct_bxslider_js', 'jQuery(document).ready(function($){
            $(".cvct-ticker #'.$ticker_id.'").each(function(index){
            var tickerCon=$(this);
            var ispeed=Number(tickerCon.attr("data-tickerspeed"));
        
            $(this).bxSlider({
                ticker:true,
                minSlides:1,
                maxSlides:12,
                slideWidth:"auto",
                tickerHover:true,
                wrapperClass:"cvct-ticker-container",
                speed: ispeed,
                infiniteLoop:true
                });
            });
        });' ); 
    }
    elseif($style=="style-2" || $style=="style-3"){
       wp_enqueue_script('cvct_ticker');
        wp_enqueue_script('cvct-slick-js');
        wp_enqueue_style('cvct-slick-css');
       wp_enqueue_style('cvct_ticker_styles');
       wp_enqueue_script('cvct_resizer_sensor');
    wp_enqueue_script('cvct_resizer_queries');
        wp_enqueue_style('cvct-fontello');

        if($style=="style-2"){

            $arrows = 'true';
            $autoplay = 'false';            
            $infinite = 'false';
        }
        else{
            $arrows = 'false';
            $autoplay = 'true';            
            $infinite = 'true';
        }

        
        $nextArrow = '<button type="button" class="ctl-slick-next "><i class="icon-right-open"></i></button>';
        $prevArrow  = '<button type="button" class="ctl-slick-prev"><i class="icon-left-open"></i></button>';
        wp_add_inline_script( 'cvct-slick-js', "jQuery(document).ready(function($){
            $('.cvct-ticker #".$ticker_id."').slick({
            slidesToShow:1,
            slidesToScroll:1,
            arrows: $arrows,
            dots: false,
            infinite: $infinite,
            autoplay: $autoplay,
            autoplaySpeed: 2000,    
            adaptiveHeight: true,
            centerMode: false,
            centerPadding: '60px',
            nextArrow:'".$nextArrow."',
            prevArrow:'".$prevArrow."',
            });
        
        });" );   
    }

  
    $countries_data=$this->get_ticker_data( $selected_countries);
    
   if(is_array($countries_data)){
       foreach($countries_data as $country=> $all_info){
        $cases=$all_info['cases']? number_format($all_info['cases']):'?';
        $active=$all_info['active']? number_format($all_info['active']):'?';
        $recovered=$all_info['recovered']? number_format($all_info['recovered']):'?';
        $deaths=$all_info['deaths']? number_format($all_info['deaths']):'?';
        $flag=$all_info['flag']? $all_info['flag']:'?';
    
        if($style=="style-2" || $style=="style-3" ){
                $ticker_html .= '
                <li symbol="' . esc_attr($country) . '">                  
                    <div class="cvct-ticker-slider-data">
                        <div class="cvct-slider-column">
                            <span class="cvct-ticker-label"><img src="' .$flag. '"></span>
                            <span class="cvct-ticker-value">' .ucfirst($country) . '</span>
                        </div>
                        <div class="cvct-slider-column">
                            <span class="cvct-ticker-label">'.$confirmed_label.'</span>                    
                            <span class="cvct-ticker-value">'.$cases. '</span>
                        </div>
                        <div class="cvct-slider-column">
                            <span class="cvct-ticker-label">'.$active_label.'</span>
                            <span class="cvct-ticker-value">' .$active. '</span>
                        </div>
                        <div class="cvct-slider-column">
                            <span class="cvct-ticker-label">'.$recovered_label.'</span>
                            <span class="cvct-ticker-value">' .$recovered. '</span>
                        </div>
                        <div class="cvct-slider-column">
                            <span class="cvct-ticker-label">'.$death_label.'</span>
                            <span class="cvct-ticker-value">' .$deaths. '</span>   
                        </div>
                    </div>     
                </li>';

            }
            else{
                $rpl_string=[' ','.'];
                $ticker_html .= '
                <li class="cvct-tooltip ' . esc_attr(strtolower($country)) . '" data-tooltip-content="#tooltip_content_' . strtolower(str_replace($rpl_string, '', $country)).'">
                    <img src="' .$flag. '">
                    <span class="ticker-country">' . $country . '</span>
                    <span class="ticker-cases">' . $cases . '</span>
                    <div class="cvct-ticker-tooltip-hidden">
                        <div id="tooltip_content_' .strtolower(str_replace($rpl_string, '', $country)). '">
                            <div class="tooltip-title">' . ucfirst($country) . '</div>
                            <div class="tooltip-cases"><b>'.$confirmed_label.':</b> ' .$cases. '</div>
                            <div class="tooltip-cases"><b>'.$active_label .':</b> ' .$active. '</div>
                            <div class="tooltip-cases"><b>'.$recovered_label .':</b> ' .$recovered. '</div>
                            <div class="tooltip-cases"><b>'.$death_label .':</b> ' .$deaths. '</div>
                        </div>
                    </div>
                </li>';
            }

       
     }
  }

    $container_cls=''; $body_cls='';
    $id = "cvct-ticker-widget-" . esc_attr($post_id);
   if($ticker_position=="footer"||$ticker_position=="header"){
        $cls='cvct-sticky-ticker';
        if($ticker_position=="footer"){
            $container_cls='cvct-ticker-footer';
            $body_cls ='cvct-ticker-bottom';
        }else{
            $container_cls='cvct-ticker-header';
            $body_cls ='cvct-ticker-top';
        }					 
    }else{
         $cls='cvct-ticker';
         $container_cls='';
    }

         
    if($style=="style-2"){ 
        $img_path=CVCT_URL.'assets/images/cvct-logo.png';   
        $output .= '<div id="cvct-ticker-'.$post_id.'" class="cvct-ticker cvct-ticker-'.$style.' '.esc_attr($container_cls).'" data-ticker-style="'.$style.'" data-ticker-position-cls="'.$body_cls.'">
            <div class="cvct-ticker-slider">
                <div class="cvct-ticker-heading">'.$ticker_heading.'</div>		
                <ul data-tickerspeed="'.$t_speed.'" id="'.$id.'">';
                    $output .= $ticker_html;
                    $output	.=	'
                </ul>
            </div>';
            if($ticker_desc!=''){
                $output .= '<div class="cvct-ticker-notice">
                '.$ticker_desc.'
                </div>';
            }
            if($body_cls!=''){
                $output .= '<div class="cvct-close-button"><i class="icon-cancel-circled-outline" aria-hidden="true"></i></div>';
            }       
        $output .= '</div>';
        if($body_cls!=''){
            $output .= '<div class="cvct-show-button"><i aria-hidden="true" class="icon-up-big" ></i></div>';
        }
    }
    elseif($style=="style-3") {
        $output .= '<div id="cvct-ticker-'.$post_id.'" class="cvct-ticker cvct-ticker-'.$style.' '.esc_attr($container_cls).'" data-ticker-style="'.$style.'" data-ticker-position-cls="'.$body_cls.'">
            <div class="cvct-ticker-heading">'.$ticker_heading.'</div>		
            <ul data-tickerspeed="'.$t_speed.'" id="'.$id.'">';
                $output .= $ticker_html;
                $output	.=	'
            </ul>';
            if($body_cls!=''){
                $output .= '<div class="cvct-close-button"><i class="icon-cancel-circled-outline" aria-hidden="true"></i></div>';
            }  
        $output .= '</div>';
        if($body_cls!=''){
            $output .= '<div class="cvct-show-button"><i aria-hidden="true" class="icon-up-big" ></i></div>';
        }
    }
    else{      
        $output .= '
        <div id="cvct-ticker-'.$post_id.'" class="cvct-ticker cvct-ticker-'.$style.' '.esc_attr($container_cls).'" data-ticker-style="'.$style.'" data-ticker-position-cls="'.$body_cls.'">
            <div class="cvct-ticker-heading">'.$ticker_heading.'</div>             
            <ul data-tickerspeed="'.$t_speed.'" id="'.$id.'">'.$ticker_html.'</ul>                   
        </div>';
    }

    $dynamic_styles ='';
    $dynamic_styles .="
    #cvct-ticker-$post_id.cvct-ticker-style-1 {".$bg_color." ".$fnt_color."}
    #cvct-ticker-$post_id.cvct-ticker-style-1 .cvct-ticker-heading {".$text_bg_color."}
    .tooltipster-sidetip .tooltipster-box {".$bg_color."}
    .tooltipster-sidetip .tooltipster-box .tooltipster-content {".$fnt_color."}
    .tooltipster-sidetip.tooltipster-top .tooltipster-arrow-border {border-top-color: ".$background_color.";}
    .tooltipster-sidetip.tooltipster-bottom .tooltipster-arrow-border {border-bottom-color: ".$background_color.";}

    #cvct-ticker-$post_id.cvct-ticker-style-2 {".$bg_color." ".$fnt_color."}
    #cvct-ticker-$post_id.cvct-ticker-style-2 .cvct-ticker-heading {".$text_bg_color."}

    #cvct-ticker-$post_id.cvct-ticker-style-3 {".$bg_color." ".$fnt_color." ".$border_color."}
    ";
    $dynamic_styles .= $custom_css;
    
    $css='<style>'. esc_html($this->cvct_ticker_styles($dynamic_styles, $style)).'</style>';
    
   // wp_add_inline_style('cvct_ticker_styles', $dynamic_styles.$custom_css);

    $cvct='<!-- COVID-19 Tracker - Version:- '.CVCT_VERSION.' By Cool Plugins (CoolPlugins.net) -->';	
    
     return $cvct.$output.$css;

}

function cvct_ticker_styles($dynamic_styles, $style){
    $css='';
    if($style=="style-1") {
        $css .= "
        .cvct-ticker-style-1 {
            display: table;
            width: 100%;
            padding: 0;
            margin: 0;
            z-index: 9999999;
            min-height: 35px;
        }
        .cvct-ticker-style-1 .cvct-ticker-heading,
        .cvct-ticker-style-1 .cvct-ticker-container {
            display: table-cell;
            vertical-align: middle;
        }
        .cvct-ticker-style-1 .cvct-ticker-heading {
            width: 175px;
            font-size: 14px;
            line-height: 16px;
            font-weight: bold;
            padding: 3px;
            text-align: center;
            text-shadow: 1px 0px 3px #000;
            color: #fff;
            background: #121d38;
        }
        .cvct-ticker-style-1 ul {
            display: inline-block;
            padding: 0;
            margin: 0;
        }
        .cvct-ticker-style-1 ul li {
            display: inline-block;
            padding: 3px;
            margin: 0 25px 0 0;
            font-size: 14px;
            line-height: 18px;
            cursor: pointer;
            vertical-align: middle;
            width: auto;
        }
        .cvct-ticker-style-1 li img {
            display: inline-block;
            vertical-align: middle;
            width: 20px;
            padding: 0;
            margin: 0 5px 0 0;
        }
        .cvct-ticker-style-1 li .ticker-cases {
            font-weight: bold;
            margin: 0 0 0 8px;
            vertical-align: middle;
        }
        .cvct-ticker-style-1 li .ticker-country {
            vertical-align: middle;
        }
        .cvct-ticker-tooltip-hidden {
            display: none;
        }
        .tooltipster-box {
            background-color: #eee;
            border-color: #eee;
            text-align: center;
        }
        .tooltip-title {
            margin: 7px 0;
            font-size: 18px;
            line-height: 22px;
        }
        .tooltip-cases {
            font-size: 14px;
            line-height: 20px;
            text-align: left;
        }
        .tooltipster-top .tooltipster-box {
            margin-bottom: 8px;
        }
        .tooltipster-sidetip.tooltipster-top .tooltipster-arrow-border {
            border-top-color: #eee;
        }
        .tooltipster-bottom .tooltipster-box {
            margin-top: 10px;
        }
        .tooltipster-sidetip.tooltipster-bottom .tooltipster-arrow-border {
            border-bottom-color: #eee;
        }
        body.cvct-ticker-top {
            margin-top: 35px;
        }
        body.cvct-ticker-bottom {
            margin-bottom: 35px;
        }
        ";
    }
    if($style=="style-2") {
        $css .= "
        .cvct-ticker-style-2 {
            display:block;
            width: 100%;
            padding: 0;
            margin: 0;
            z-index: 9999999;
            min-height: 35px;
            background: #464e61;
            color: #fff;
        }
        .cvct-ticker-style-2 .cvct-ticker-slider {
            display: table;
            width: 96%;
            max-width: 1240px;
            margin: 0 auto;
            table-layout: fixed;
        }
        .cvct-ticker-style-2 .cvct-ticker-heading,
        .cvct-ticker-style-2 ul {
            display: table-cell;
            vertical-align: middle;
        }
        .cvct-ticker-style-2 .cvct-ticker-heading {
            width: 225px;
            padding: 8px 15px 8px 8px;
            box-sizing: border-box;
            font-size: 14px;
            line-height: 18px;
            font-weight: bold;
            text-align: center;
            text-shadow: 1px 0px 3px #000;
            color: #fff;
            background: #121d38;
        }
        .cvct-ticker-style-2 button.slick-arrow {
            position: absolute;
            width: 20px;
            height: 20px;
            padding: 0;
            margin: 0;
            background: rgba(0, 0, 0, 0.75);
            border: 0;
            color: #fff;
            top: calc(50% - 10px);
            z-index: 99;
            cursor: pointer;
        }
        .cvct-ticker-style-2 button.ctl-slick-next.slick-arrow {
            right: -10px;
        }
        .cvct-ticker-style-2 button.ctl-slick-prev.slick-arrow {
            left: -10px;
        }
        .cvct-ticker-style-2 ul,
        .cvct-ticker-style-2 ul li {
            padding:0;
            margin:0;
        }
        .cvct-ticker-style-2 .cvct-ticker-slider-data {
            display: table;
            width: 100%;
        }
        .cvct-ticker-style-2 .cvct-slider-column {
            display: table-cell;
            table-layout: fixed;
            vertical-align: middle;
            text-align: center;
            padding: 5px;
            background: rgba(0, 0, 0, 0.1);
            border-right: 1px solid rgba(0, 0, 0, 0.1);
        }
        .cvct-ticker-style-2 .cvct-slider-column:last-child {
            border-right:0;
        }
        .cvct-ticker-style-2 span.cvct-ticker-label,
        .cvct-ticker-style-2 span.cvct-ticker-value {
            width: 100%;
            display: inline-block;
        }
        .cvct-ticker-style-2 span.cvct-ticker-label {
            font-size: 14px;
            font-weight: bold;
            line-height: 18px;
        }
        .cvct-ticker-style-2 span.cvct-ticker-value {
            font-weight: lighter;
            font-size: 20px;
            line-height: 24px;
        }
        .cvct-ticker-style-2 span.cvct-ticker-label img {
            width:20px;
            display:inline-block;
            padding:0;
            margin:0;
        }
        .cvct-ticker-style-2 .cvct-ticker-notice {
            display: block;
            width: 96%;
            height: auto;
            max-width: 1240px;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.15);
            padding: 8px;
            box-sizing: border-box;
            font-size: 14px;
            line-height: 18px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        body.cvct-ticker-top {
            margin-top: 90px;
        }
        body.cvct-ticker-bottom {
            margin-bottom: 90px;
        }
        .cvct-close-button {
            position: absolute;
            top: -18px;
            right: 50px;
            font-size: 36px;
            cursor: pointer;
            text-shadow: 1px 0px 3px rgba(0, 0, 0, 0.3);
            transition: 1.0s ease all;
            -moz-transition: 1.0s ease all;
            -webkit-transition: 1.0s ease all;
        }
        .cvct-close-button:hover {
            transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -webkit-transform: rotate(360deg);
            transform-origin: center;
            -moz-transform-origin: center;
            -webkit-transform-origin: center;
            transition: transform 1.0s ease all;
            -moz-transition: -moz-transform 1.0s ease all;
            -webkit-transition: -webkit-transform 1.0s ease all;
        }
        .cvct-show-button {
            display: none;
            position: fixed;
            bottom: 0px;
            left: 50px;
            top: unset;
            cursor: pointer;
            z-index: 9999999;
        }
        .cvct-show-button i {
            font-size: 24px;
            color: #fff;
            background: rgba(0, 0, 0, 0.75);
            padding: 4px;
        }
        .cvct-ticker-header .cvct-close-button {
            bottom: -14px;
            top: unset;
        }
        ";
    }
    if($style=="style-3") {
        $css .= "
        .cvct-ticker-style-3 {
            display: block;
            width: calc(96% - 30px);
            max-width: 500px;
            padding: 0;
            margin: 15px;
            z-index: 9999999;
            min-height: 35px;
            background: #fff;
            box-sizing: border-box;
            border-top: 5px solid;
            right: 0 !IMPORTANT;
            left: unset !IMPORTANT;
        }
        .cvct-close-button {
            position: absolute;
            top: -14px;
            right: -14px;
            font-size: 24px;
            cursor: pointer;
            text-shadow: 1px 0px 3px rgba(0, 0, 0, 0.3);
        }
        .cvct-show-button {
            display: none;
            position: fixed;
            bottom: 0px;
            right: 50px;
            top: unset;
            cursor: pointer;
            z-index: 9999999;
        }
        .cvct-show-button i {
            font-size: 24px;
            color: #fff;
            background: rgba(0, 0, 0, 0.75);
            padding: 4px;
        }
        .cvct-ticker-header .cvct-close-button {
            bottom: -14px;
            top: unset;
        }
        .cvct-ticker-style-3 .cvct-ticker-heading {
            width: 100%;
            display: inline-block;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            line-height: 20px;
            box-sizing: border-box;
            font-weight: bold;
            text-shadow: 1px 0px 1px rgba(0, 0, 0, 0.35);
        }
        .cvct-ticker-style-3 ul,
        .cvct-ticker-style-3 ul li {
            padding:0;
            margin:0;
            width: 100%;
            height: auto;
        }
        .cvct-ticker-style-3 .cvct-ticker-slider-data {
            display: table;
            width: 100%;
        }
        .cvct-ticker-style-3 .cvct-slider-column {
            display: table-cell;
            table-layout: fixed;
            vertical-align: middle;
            text-align: center;
            padding: 5px;
        }
        .cvct-ticker-style-3 span.cvct-ticker-label,
        .cvct-ticker-style-3 span.cvct-ticker-value {
            width: 100%;
            display: inline-block;
        }
        .cvct-ticker-style-3 span.cvct-ticker-label {
            font-size: 14px;
            font-weight: bold;
            line-height: 18px;
        }
        .cvct-ticker-style-3 span.cvct-ticker-value {
            font-weight: lighter;
            font-size: 18px;
            line-height: 24px;
        }
        .cvct-ticker-style-3 span.cvct-ticker-label img {
            width:20px;
            display:inline-block;
            padding:0;
            margin:0;
        }
        ";
    }
    if(is_admin()){
        $css .= ".cvct-ticker-footer, .cvct-ticker-header{
            position: relative;
        }";
    }
    else{
        $css .= "
        .cvct-ticker-footer,
        .cvct-ticker-header {
            position: fixed;
            left: 0px;
        }
        .cvct-ticker-header {
            top: 0;
            bottom: unset;
        }
        .cvct-ticker-footer {
            bottom: 0;
            top: unset;
        }
        ";
    }
    
    return $css.$dynamic_styles;
}
}