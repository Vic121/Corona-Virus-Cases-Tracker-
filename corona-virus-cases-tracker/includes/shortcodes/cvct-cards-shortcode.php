<?php
class CVCT_Shortcode
{
function __construct() {
    //shortcodes for card & text widgets
    require_once CVCT_DIR . 'includes/get_country_name.php';
    add_shortcode('cvct',array($this, 'cvct_shortcode'));
    add_shortcode('cvct-text',array($this,'cvct_ultimate_text'));
}


/*
|--------------------------------------------------------------------------
| Corona Stats - Cards Shortcodes
|--------------------------------------------------------------------------
*/ 
public function  cvct_shortcode( $atts, $content = null ) {
    wp_enqueue_style('cvct_cards_css');
    wp_enqueue_script('cvct_resizer_sensor');
    wp_enqueue_script('cvct_resizer_queries');
    $atts = shortcode_atts( array(
        'country-code'=>'all',
        'style'=>'style-2',
        'title'=>'Global Stats',
        'label-total'=>'Total Cases',
        'label-deaths'=>'Deaths',
        'label-recovered'=>'Recovered',
        'label-active'=>'Active Cases',
        'label-new'=>'New Cases',
        'label-death-per'=>"Death %",
        'label-recovered-per'=>"Recovered %",
        'bg-color'=>'#DDDDDD',
        'font-color'=>'#000'
    ), $atts, 'cvct' );
    $layout = !empty($atts['layout'])?$atts['layout']:'card-layout';
    $country=!empty($atts['country-code'])?$atts['country-code']:"all";
    $style=!empty($atts['style'])?$atts['style']:"style-2";
    $title=$atts['title'];
    if($style=="style-4"){
        $title =get_country_info($country) ." ".esc_html($title);
    }
    $label_total=$atts['label-total'];
    $label_deaths=$atts['label-deaths'];
    $label_recovered=$atts['label-recovered'];
    $label_active=$atts['label-active'];
    $label_deathp=$atts['label-death-per'];
    $label_recoveredp=$atts['label-recovered-per'];
    $bgColors=!empty($atts['bg-color'])?$atts['bg-color']:"#DDDDDD";
    $fontColors=!empty($atts['font-color'])?$atts['font-color']:"#000";
    $custom_style='';
    $custom_style .='background-color:'.$bgColors.';';
    $custom_style .='color:'.$fontColors.';';

    $total_cases='';
    $total_recovered='';
    $total_deaths='';
    
    $output='';
    $tp_html='';
    
    if( $country=="all"){
        $stats_data=cvct_get_global_data();
        if($stats_data==false){
            $stats_data=cvct_get_global_data_alternative();
        }
        $last_updated=date("d M Y, G:i A",strtotime(get_option('cvct_gs_updated'))).' (GMT)';
    }else{
        $c_name=get_country_name($country);
        if($c_name=='India'){
            $stats_data= cvct_india_global_stats($country);
        }
        else{
            $stats_data= cvct_country_stats_data($country);
        }
        if($stats_data==false){
            $stats_data=cvct_get_global_data_alternative($c_name);
        }
        $last_updated=date("d M Y, G:i A",strtotime(get_option('cvct_cs_updated'))).' (GMT)';
    }
    if(is_array($stats_data) && count($stats_data)>0){
        $total=$stats_data['total_cases'];
        $recovered=$stats_data['total_recovered'];
        $deaths=$stats_data['total_deaths'];
        $total_cases=!empty($total)?number_format($total):"0";
        $total_recovered=!empty($recovered)?number_format($recovered):"0";
        $total_deaths=!empty($deaths)?number_format($deaths):"0";
        $active_cases=$total-($recovered+$deaths);
        if($total>=1){
            $rp=($recovered/$total)*100;
            $dp=($deaths/$total)*100;
        }
        $recoveredPerstange=!empty($rp)? number_format($rp,1)."%":"?";
        $deathPerstange=!empty($dp)? number_format($dp,1)."%":"?";
        $total_active_cases=!empty($active_cases)? number_format($active_cases):"?";
    }

    switch($style){
        case "style-1":
        $tp_html.='
        <div class="card-update-time cvct-card-1"><i>'.esc_html($last_updated).'</i></div>
        <div class="coronatracker-card cvct-card-1" style="'.esc_attr($custom_style).'"><!-- 
            --><h2 style="color:'.esc_attr($fontColors).'">'.html_entity_decode($title).'</h2><!-- 
            --><div class="cvct-number">
                <span class="large-num">'.esc_html($total_cases).'</span>
                <span>'.esc_html($label_total).'</span>
                </div><!-- 
            --><div class="cvct-number">
                <span class="large-num">'.esc_html($total_deaths).'</span>
                <span>'.esc_html($label_deaths).'</span>
                </div><!-- 
            --><div class="cvct-number">
                <span class="large-num">'.esc_html($total_recovered).'</span>
                <span>'.esc_html($label_recovered).'</span>
                </div><!-- 
        --></div>
        ';
        break;
        case "style-2":
        $tp_html.='
        <div class="coronatracker-card cvct-card-2" style="'.esc_attr($custom_style).'"><!-- 
            --><h2 style="color:'.esc_attr($fontColors).'">😷 '.html_entity_decode($title).'</h2><!-- 
            --><div class="cvct-number">
                <span class="large-num">'.esc_html($total_cases).'</span>
                <span>'.esc_html($label_total).'</span>
                </div><!-- 
            --><div class="cvct-number">
                <span class="large-num">'.esc_html($total_deaths).'</span>
                <span>'.esc_html($label_deaths).'</span>
                </div><!-- 
            --><div class="cvct-number">
                <span class="large-num">'.esc_html($total_recovered).'</span>
                <span>'.esc_html($label_recovered).'</span>
                </div><!-- 
            --><div class="cvct-number">
                <span class="large-num">'.esc_html($total_active_cases).'</span>
                <span>'.esc_html($label_active).'</span>
                </div><!-- 
            --><div class="cvct-number">
                <span class="large-num">'.esc_html($deathPerstange).'</span>
                <span>'.esc_html($label_deathp).'</span>
                </div><!-- 
            --><div class="cvct-number">
                <span class="large-num">'.esc_html($recoveredPerstange).'</span>
                <span>'.esc_html($label_recoveredp).'</span>
                </div><!-- 
        --></div>
        ';
        break;
        case "style-4":
        $tp_html.='
        <div class="coronatracker-card cvct-card-4" style="'.esc_attr($custom_style).'"><!-- 
            --><h2 style="color:'.esc_attr($fontColors).'">'.html_entity_decode($title).'</h2><!-- 
            --><div class="cvct-number">
                <span class="large-num" style="color:#1877F2">😷 '.esc_html($total_cases).'</span>
                <span>'.esc_html($label_total).'</span>
                </div><!-- 
            --><div class="cvct-number">
                <span class="large-num" style="color:#FB3938">😥 '.esc_html($total_deaths).'</span>
                <span>'.esc_html($label_deaths).'</span>
                </div><!-- 
            --><div class="cvct-number">
                <span class="large-num" style="color:#3AA969">😇 '.esc_html($total_recovered).'</span>
                <span>'.esc_html($label_recovered).'</span>
                </div><!-- 
        --></div>
        ';
        break;
        case "style-5":
        $tp_html.='
        <div class="coronatracker-card cvct-card-5" style="'.esc_attr($custom_style).'"><!-- 
            --><div class="cvct-left">
                <h2 style="color:'.esc_attr($fontColors).'">'.html_entity_decode($title).'</h5>
                </div><!-- 
            --><div class="cvct-right"><!-- 
                --><div class="cvct-number">
                    <span><tipp class="blue"></tipp>'.esc_html($label_total).'</span>
                    <span class="large-num">'.esc_html($total_cases).'</span> 
                    </div><!-- 
                --><div class="cvct-number">
                    <span><tipp class="red"></tipp>'.esc_html($label_deaths).'</span>
                    <span class="large-num" style="color: #ff4141;">'.esc_html($total_deaths).'</span> 
                    </div><!-- 
                --><div class="cvct-number">
                    <span><tipp class="green"></tipp>'.esc_html($label_recovered).'</span>
                    <span class="large-num">'.esc_html($total_recovered).'</span> 
                    </div><!-- 
            --></div><!-- 
        --></div>
        ';
        break;
        case "style-6":
        $tp_html.='
        <div class="coronatracker-card cvct-card-6" style="'.esc_attr($custom_style).'"><div class="cvct-card-6-bg"></div>
            <h2 style="color:'.esc_attr($fontColors).'">'.html_entity_decode($title).'</h5><!-- 
            --><div class="cvct-number total_case">
                <span class="large-num">'.esc_html($total_cases).'</span> 
                <span>'.esc_html($label_total).'</span>
                </div><!-- 
            --><div class="cvct-number death_case">
                <span class="large-num">'.esc_html($total_deaths).' <small>('.esc_html($deathPerstange).')</small></span> 
                <span>'.esc_html($label_deaths).'</span>
                </div><!-- 
            --><div class="cvct-number recovered_case">
                <span class="large-num">'.esc_html($total_recovered).' <small>('.esc_html($recoveredPerstange).')</small></span> 
                <span>'.esc_html($label_recovered).'</span>
                </div><!-- 
        --></div><div class="card-update-time cvct-card-6"><i>'.esc_html($last_updated).'</i></div>
        ';
        break;
        default:
        $tp_html.='
        <div class="coronatracker-card cvct-card-3" style="'.esc_attr($custom_style).'"><!-- 
            --><div class="cvct-left">
                <h2  style="color:'.esc_attr($fontColors).'">'.html_entity_decode($title).'</h2>
                <i>'.esc_html($last_updated).'</i>
            </div><!-- 
            --><div class="cvct-right"><!-- 
                --><div class="cvct-number">
                <span class="large-num">'.esc_html($total_cases).'</span>
                <span>'.esc_html($label_total).'</span>
                </div><!-- 
                --><div class="cvct-number">
                <span class="large-num">'.esc_html($total_deaths).'</span>
                <span>'.esc_html($label_deaths).'</span>
                </div><!-- 
                --><div class="cvct-number">
                <span class="large-num">'.esc_html($total_recovered).'</span>
                <span>'.esc_html($label_recovered).'</span>
                </div><!-- 
            --></div><!-- 
        --></div>
        ';
        break;
    }
    $source= get_transient('api-source');
    $css='<style data-s="'.esc_attr($source).'">'. esc_html($this->cvct_load_styles($style)).'</style>';
    $output.='<div id="cvct-card-wrapper">'.$tp_html.'</div>';
    $cvctv='<!-- Corona Virus Cases Tracker Pro - Version:- '.CVCT_VERSION.' By Cool Plugins (CoolPlugins.net) -->';	
    return  $cvctv.$output.$css;	
}


/*
|--------------------------------------------------------------------------
| Corona Stats - Text Shortcodes
|--------------------------------------------------------------------------
*/ 
function cvct_ultimate_text($atts,$content=null){
    $atts = shortcode_atts( array(
        'id'  => '',
        'field'=> 'confirmed',
        'country-code'=> 'all',
    ), $atts, 'cvct' );
    $fields = isset($atts['field'])?$atts['field']:'confirmed';
    $country_code = isset($atts['country-code'])?$atts['country-code']:'all';
    
    if($country_code!='all'){
        $get_data = cvct_country_stats_data($country_code);
    }
    else{
        $get_data=cvct_get_global_data();
    }
    
    if($get_data == false){
        if($country_code!='all'){
           $get_data = cvct_country_stats_data_alternate($country_code);
        }
        else{
            $get_data=cvct_get_global_data_alternative();
        }
    }
    if($get_data==''){
        return false;
     }
    $confirmed = isset($get_data['total_cases'])?(int) $get_data['total_cases']:'';
    $total_recover = isset($get_data['total_recovered'])?(int) $get_data['total_recovered']:'';
    $total_death = isset($get_data['total_deaths'])?(int) $get_data['total_deaths']:'';
    $today_cases = isset($get_data['today_cases'])?(int) $get_data['today_cases']:'';
    $today_deaths = isset($get_data['today_deaths'])?(int) $get_data['today_deaths']:'';
    $critical = isset($get_data['critical'])?(int) $get_data['critical']:'';
    $total_active_cases=$confirmed-($total_recover+$total_death);
    $ap = ($total_active_cases/$confirmed)*100;
    $rp = ($total_recover/$confirmed)*100;
    $dp = ($total_death/$confirmed)*100;
    $activePercentage=!empty($ap)? number_format($ap,1)."%":"0%";
    $recoverdPerctange = !empty($rp)?number_format($rp,1)."%":"0%";
    $deathPerctange = !empty($dp)?number_format($dp,1)."%":"0%";
    switch($fields){
        case 'active':
            return isset($total_active_cases)?number_format($total_active_cases):0;
        break; 
        case 'recovered':
            return isset($total_recover)?number_format($total_recover):0;
        break;  
        case 'death':
            return isset($total_death)?number_format($total_death):0;
        break; 
        case 'active-per':
            return $activePercentage;    
        break;
        case 'recovered-per':
            return $recoverdPerctange;
        break;
        case 'death-per':
            return $deathPerctange;
        break;
        case 'today-cases':
            return $today_cases;
        break;
        case 'today-deaths':
            return $today_deaths;
        break;
        case 'critical':
            return $critical;
        break;
        default:
            return isset($confirmed)?number_format($confirmed):0;
        break;
    }
}


/*
|--------------------------------------------------------------------------
| loading required assets according to the card widget type
|--------------------------------------------------------------------------
*/  
function cvct_load_styles($style){
    $css = "";
    $css="
    #cvct-card-wrapper {
        width: 100%;
        display: block;
        overflow-x: auto;
        padding: 0;
        margin: 8px auto 16px;
        text-align: center;
    }
    #cvct-card-wrapper * {
        box-sizing: border-box;
    }
    #cvct-card-wrapper .coronatracker-card {
        display: inline-block;
        width: 100%;
        max-width: 750px;
        border: 1px solid rgba(0, 0, 0, 0.14);
        padding: 10px;
        border-radius: 8px;
        background: #ddd url(".CVCT_URL."/assets/corona-virus.png);
        background-size: 68px;
        background-position: right -20px top -18px;
        background-repeat: no-repeat;
        transition: background-position 1s;
    }
    #cvct-card-wrapper .coronatracker-card:hover {
        background-position: right -7px top -5px;
        transition: background-position 1s;
    }
    #cvct-card-wrapper h2 {
        margin: 5px 0 10px 0;
        padding: 0;
        font-size: 20px;
        line-height: 22px;
        font-weight: bold;
        display: inline-block;
        width: 100%;
        text-align:center;
    }
    #cvct-card-wrapper h2 img {
        display: inline-block;
        margin: 0 4px 0 0;
        padding: 0;
    }
    #cvct-card-wrapper .cvct-number {
        width: 33.33%;
        display: inline-block;
        float: none;
        padding: 8px 4px 15px;
        text-align: center;
        vertical-align: top;
    }
    #cvct-card-wrapper .cvct-number span {
        width: 100%;
        display: inline-block;
        font-size: 14px;
        line-height: 16px;
        margin-bottom: 2px;
        word-break: keep-all;
        vertical-align: middle;
    }
    #cvct-card-wrapper .cvct-number span.large-num {
        font-size: 18px;
        line-height: 21px;
        font-weight: bold;
        margin-bottom: 3px;
    }
    #cvct-card-wrapper .cvct-number span.large-num small {
        font-size: 0.7em !IMPORTANT;
    }
    tipp {
        width: 14px;
        height: 12px;
        border-radius: 3px;
        display: inline-block;
        background: #666;
        vertical-align: middle;
        margin-right: 3px;
    }
    tipp.red {
        background: #ff4141;
    }
    tipp.green {
        background: #3aa969;
    }
    tipp.orange {
        background: #f17822;
    }
    tipp.blue {
        background: #1877f2;
    }
    #cvct-card-wrapper i {
        display: inline-block;
        margin: 0;
        padding: 5px;
        font-size: 12.5px;
        line-height: 1.3em;
        font-style: italic;
    }
    ";
    if($style=="style-1") {
        $css.="
        .card-update-time.cvct-card-1 {
            max-width: 750px;
            display: inline-block;
            width: 100%;
            text-align: right;
        }
        ";
    }
    elseif($style=="style-2"){
        $css.="
        #cvct-card-wrapper .cvct-card-2 .cvct-number span:nth-child(2) {
            background: rgba(0, 0, 0, 0.65);
            width: auto;
            padding: 5px;
            border-radius: 3px;
            color: #fff;
            text-shadow: 0px 0px 2px #222;
        }
        ";
    }
    elseif($style=="style-3"){
        $css.="
        #cvct-card-wrapper .coronatracker-card.cvct-card-3 {
            background-position: left -20px top -18px;
            max-width: unset;
            animation-name: corona-move;
            animation-duration: 1s;
            animation-iteration-count: infinite;
            animation-direction: alternate;
        }
        #cvct-card-wrapper .coronatracker-card.cvct-card-3:hover {
            background-position: left -7px top -5px;
            transition: background-position 1s;
        }
        .cvct-left, .cvct-right {
            width: 49.98%;
            float: none;
            display: inline-block;
            vertical-align: middle;
        }
        .cvct-left {
            padding:5px;
        }
        .cvct-right {
            background: rgba(41, 41, 41, 0.14);
            border-radius: 8px;
        }
        @keyframes corona-move {
            from {background-position: left -20px top -18px;}
            to {background-position: left -7px top -5px;}
        }
        ";
    }
    elseif($style=="style-5"){
        $css.="
        #cvct-card-wrapper .coronatracker-card.cvct-card-5 {
            max-width: unset;
            border-radius: 20px;
            box-shadow: inset 0px 1px 8px -4px rgba(0, 0, 0, 0.5);
            background-position: left -20px top -18px;
            animation-name: corona-move;
            animation-duration: 1s;
            animation-iteration-count: infinite;
            animation-direction: alternate;
        }
        .cvct-left, .cvct-right {
            width: 49.98%;
            display: inline-block;
            vertical-align: middle;
        }
        .cvct-left {
            padding:5px;
        }
        .coronatracker-card.cvct-card-5 .cvct-right {
            background: none;
        }
        @keyframes corona-move {
            from {background-position: left -20px top -18px;}
            to {background-position: left -7px top -5px;}
        }
        ";
    }
    elseif($style=="style-6"){
        $css.= "
        #cvct-card-wrapper .coronatracker-card.cvct-card-6 {
            text-align: center;
            padding: 3%;
            position:relative;
            border-radius:0;
            max-width:unset;
        }
        .cvct-card-6-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            background:url(".CVCT_URL."assets/images/corona-bg.jpg);
            background-size:cover;
            opacity:0.4;
            top: 0;
            left: 0;
            z-index: 1;
        }
        #cvct-card-wrapper .cvct-card-6 .cvct-number span:nth-child(2) {
            background: rgba(0, 0, 0, 0.35);
            width: auto;
            padding: 5px;
            border-radius: 3px;
            color: #fff;
            text-shadow: 0px 0px 2px #222;
        }
        #cvct-card-wrapper .cvct-card-6 .cvct-number.death_case span:nth-child(2) {
            background: #c50909;
        }
        #cvct-card-wrapper .cvct-card-6 .cvct-number.recovered_case span:nth-child(2) {
            background: #0c9c0c;
        }
        #cvct-card-wrapper .coronatracker-card.cvct-card-6 h2,
        #cvct-card-wrapper .coronatracker-card.cvct-card-6 .cvct-number {
            text-align:center;
            z-index:2;
            position: relative;
        }
        .card-update-time.cvct-card-6 {
            display: inline-block;
            width: 100%;
            text-align: right;
        }
        ";   
    }
    return $css;
}
}


