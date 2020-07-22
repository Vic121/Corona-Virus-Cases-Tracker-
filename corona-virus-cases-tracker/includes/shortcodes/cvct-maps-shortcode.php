<?php

class CVCT_Maps_Shortcode
{
function __construct() {
    //main plugin shortcode for list widget
    require_once CVCT_DIR . 'includes/get_country_name.php';
    include_once CVCT_DIR . 'includes/cvct-functions.php';

    add_shortcode( 'cvct-maps', array($this, 'cvct_maps_shortcode' ));
    add_action('wp_ajax_basic_map_data', array($this, 'get_basic_map_data'));
    add_action('wp_ajax_nopriv_basic_map_data', array($this, 'get_basic_map_data'));
}

/**
 * Global COVID-19 spread data map
 */
public function  cvct_maps_shortcode( $atts, $content = null ) {
  $atts = shortcode_atts( array(
      'title'=>'Coronavirus (COVID-19) map',
      'show-list'=>'yes',
      'layout'=>'',
      'high-color'=>'',
      'medium-color'=>'',
      'low-color'=>'',
      'verylow-color'=>'',
      'verymuchlow-color'=>'',
      'default-color'=>'',
      'label-confirmed'=>"Confirmed cases",
        'label-deaths'=>"Death cases",
         'label-recovered'=>"Recovered cases",
         'label-active'=>'Active cases',
         'label-country'=>'Country',
  ), $atts, 'cvct-maps' );

  
   $show_list=!empty($atts['show-list'])?$atts['show-list']:"yes";
    $output='';
    $layout=!empty($atts['layout'])?$atts['layout']:"style-1";
    $title=!empty($atts['title'])?$atts['title']:"COVID-19 Spread Data";
    $label_country = !empty($atts['label-country'])?$atts['label-country']:'Country';
    $label_confirmed = !empty($atts['label-confirmed'])?$atts['label-confirmed']:'Confirmed';
    $label_deaths = !empty($atts['label-deaths'])?$atts['label-deaths']:'Death';
    $label_recovered  = !empty($atts['label-recovered'])?$atts['label-recovered']:'Recovered';
    $label_active = !empty($atts['label-active'])?$atts['label-active']:'Active';
     if(!is_admin()){
        $this->cvct_map_load_assets($layout);
     }
   if($layout=="style-2"){
     
    // delete cache
      if(get_option('delete_contries_data_cache')==false){
        delete_transient('cvct_countries_data');
        update_option('delete_contries_data_cache',true);
      }
          $colorsArr['high']=!empty($atts['high-color'])?$atts['high-color']:"#C33333";
          $colorsArr['medium']=!empty($atts['medium-color'])?$atts['medium-color']:"#FB5D5C";
          $colorsArr['low']=!empty($atts['low-color'])?$atts['low-color']:"#FB8382";
          $colorsArr['verylow']=!empty($atts['verylow-color'])?$atts['verylow-color']:"#FBC4C3";
          $colorsArr['verymuchlow']=!empty($atts['verymuchlow-color'])?$atts['verymuchlow-color']:"#EEEEEE";
          $colorsArr['defaultFill']=!empty($atts['default-color'])?$atts['default-color']:"#EEEEEE";
          $colorsArr['none']=!empty($atts['default-color'])?$atts['default-color']:"#EEEEEE";
          $settingsJson=json_encode($colorsArr);
          $output.='<div id="cvct_basic_map_wrp">
           <div class="cvct_global_stats"></div>
          <div id="cvct_basic_map"><div class="cvct_preloader">'.__("Loading...","cvct").'</div></div>
          <table class="indicator" aria-hidden="true">
          <tbody><tr>
               <td class="verymuchlow">1-100</td>
                <td class="verylow">100-1000</td>
                <td class="low">1000-10000 </td>
                <td class="medium">10000-50,000</td>
                <td class="high">50,000+</td>
             </tr></tbody></table>';
      
        if($show_list == 'yes') {
              $output.='<table class="cvct-basic-map-table" aria-hidden="true">
              <thead> 
              <th class="cvct-th">'.__($label_country,'cvct').'</th>
              <th class="cvct-th">'.__($label_confirmed,'cvct').'</th>
              <th class="cvct-th">'.__($label_recovered,'cvct').'</th>
             <th class="cvct-th">'.__($label_deaths,'cvct').'</th>
              </thead><tbody></tbody></table>';
             }
            $output .='<script type="application/json" id="cvct-color-settings">'.$settingsJson.'</script>';
          $output.='</div><style>
          #cvct_basic_map_wrp {
            width: 100%;
            display: block;
            overflow: visible;
            padding: 0 0 6px;
            margin: 10px auto 16px;
            text-align:center;
            position:relative;
          }
          img.cvct-flag {
            
            margin-right: 6px;
        }
          #cvct_basic_map_wrp .cvct_basic_map_card {
            display: inline-block;
            width: 100%;
            max-width: 750px;
            padding: 10px;
            border-radius: 8px;
          }
          #cvct_basic_map_wrp .cvct_basic_map_card h2 {
            margin: 5px 0 10px 0;
            padding: 0;
            font-size: 20px;
            line-height: 22px;
            font-weight: bold;
            display: inline-block;
            width: 100%;
            text-align:center;
          }
          #cvct_basic_map_wrp .cvct_basic_map_card .cvct-number {
              width: 33.33%;
              display: inline-block;
            float: none;
            padding:3px;
              text-align: center;
              vertical-align: top;
          }
          #cvct_basic_map_wrp .cvct_basic_map_card .cvct-number span {
              width: 100%;
              display: inline-block;
              font-size: 14px;
              line-height: 16px;
              margin-bottom: 2px;
              word-break: keep-all;
              vertical-align: middle;
          }
          #cvct_basic_map_wrp .cvct_basic_map_card .cvct-number span.large-num {
              font-size: 18px;
              line-height: 21px;
              font-weight: bold;
              margin-bottom: 3px;
          }
          @media only screen and (max-width: 480px) {
            #cvct_basic_map_wrp .cvct_basic_map_card .cvct-number {
              width: 49.98%;
            }
          }
          table.indicator, table.indicator tr, table.indicator tr td {
            border: 0;
            border-collapse: collapse;
            color: rgba(0, 0, 0, 0.8);
            font-weight: bold;
            font-size: 14px;
        }
        #cvct_basic_map_wrp svg {left:0;}
          .indicator tr td{border:1px solid #000;} #cvct_basic_map {  width:100%; height:100%; position: relative; }
         
          #cvct_basic_map_wrp table.dataTable {
            table-layout: fixed;
            border-collapse: collapse;
            border-radius: 5px;
            overflow: hidden;
            margin: 0;
            padding: 0;
          }
          #cvct_basic_map_wrp table.dataTable tr th,
          #cvct_basic_map_wrp table.dataTable tr td {
            text-align:left;
            vertical-align: middle;
            font-size:14px;
            line-height:16px;
            text-transform:capitalize;
            border: 1px solid rgba(0, 0, 0, 0.15);
            width: 110px;
            padding: 12px 8px;
          }
        
          #cvct_basic_map_wrp .dataTables_filter input,
          #cvct_basic_map_wrp .dataTables_length select {
            display: inline-block !IMPORTANT;
            margin: 0 2px !IMPORTANT;
            width: auto !IMPORTANT;
            min-width: 60px;
            padding: 8px;
            min-height: 44px;
            box-sizing: border-box;
            vertical-align: middle;
          }
          #cvct_basic_map_wrp .dataTables_length label,
          #cvct_basic_map_wrp .dataTables_filter label {
            margin-bottom: 12px;
            display: inline-block;
            vertical-align: middle;
          }
          #cvct_basic_map_wrp .dataTables_paginate .paginate_button {
            padding: 0.3em 0.8em;
            border-color: transparent;
          }
          #cvct_basic_map_wrp .dataTables_paginate .paginate_button:hover {
            border-color: transparent;
          }
          
          </style>';
              return $output;
   }else{
        
            $map_json_data= $this->generate_map_data();
              $output='<div  data-title="'.$title.'" class="flexbox" id="cvct_wrapper">
            <div id="cvct_map" ><p>'.__('Loading','cvct').'....</p></div>';
            if($show_list == 'yes') {
              $output.='
              <div id="list">
              <table id="areas" class="compact hover order-column row-border">
                <thead>
                  <tr>
                    <th class="cvct-th">'.__($label_country,'cvct').'</th>
                    <th class="cvct-th">'.__($label_confirmed,'cvct').'</th>
                    <th class="cvct-th">'.__($label_deaths,'cvct').'</th>
                    <th class="cvct-th">'.__($label_recovered,'cvct').'</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
              </div>';
            }
        $output.='</div><script type="application/json" id="cvct-map-data">'.$map_json_data.'</script>';
        $output.='<style>.flexbox {
          color:#fff;
          }</style>';
            return $output;
      }
} 


/**
 * get data for map shortcode
 */
public function get_basic_map_data(){
  $jsonData='';
 $data_arr=[];

 $data_arr['TJ']=array();
 $data_arr['TM']=array();
 $data_arr['DJ']=array();
 $data_arr['KP']=array();
 $restData='';
 $globalCount['deaths']=0;
 $globalCount['confirmed']=0;
 $globalCount['recovered']=0;
   $allData=cvct_get_all_country_data();
    if($allData==false){
       $allData=cvct_get_all_country_alternative();
     }
         if(is_array($allData)&& count($allData)>0){
             foreach($allData as $countrydata){
               if(isset($countrydata['all_data'])&& !empty($countrydata['all_data']->countryInfo))
               $countryInfo= $countrydata['all_data']->countryInfo;
                $iso3_code=$countryInfo->iso3;
                $flag = isset($countryInfo->flag)?$countryInfo->flag:'';
                $country_name=$countrydata["country"];
               $deaths= isset($countrydata["deaths"])?$countrydata["deaths"]:0;
               $confirmed=isset($countrydata["cases"])?$countrydata["cases"]:0;
              $recovered =isset($countrydata["recoverd"])?$countrydata["recoverd"]:0;
               $globalCount['deaths']+=$deaths;
               $globalCount['confirmed']+=$confirmed;
               $globalCount['recovered']+=$recovered;
               $countries[]=$countrydata["country"];
          
           if( $confirmed>50000){
                $condition='high';
              }else if($confirmed>10000){
                $condition='medium';
              }else if($confirmed>1000){
                $condition='low';
              }else if($confirmed>100){
                $condition='verylow';
              }else if($confirmed>1){
                $condition='verymuchlow';
              }
              else{
                $condition='none';
              }
              if($flag!=''){
                $country_img = '<img class="cvct-flag" src="'.$flag.'" alt="Smiley face"width="20px">';
              }
              $data_arr[$iso3_code]=array(
              "deaths"=>$deaths,
              "country_name"=>$country_name,
              "flag"=>$country_img,
              "confirmed"=>$confirmed,
              "recovered"=>$recovered,
              "fillKey"=>$condition,
                );
             }
         }
    echo $jsonData= json_encode($data_arr); 
         wp_die();
}

/**
 * get date for map shortcode
 */
 public function generate_map_data(){
     $jsonData='';
    $data_arr=[];
    $restData='';
    $globalCount['deaths']=0;
    $globalCount['confirmed']=0;
    $globalCount['recovered']=0;
      $allData=cvct_get_all_country_data();
        if($allData==false){
          $allData=cvct_get_all_country_alternative();
        }
            if(is_array($allData)&& count($allData)>0){
                foreach($allData as $countrydata){
                  $deaths= isset($countrydata["deaths"])?$countrydata["deaths"]:0;
                  $confirmed=isset($countrydata["cases"])?$countrydata["cases"]:0;
                 $recovered =isset($countrydata["recoverd"])?$countrydata["recoverd"]:0;
                  $globalCount['deaths']+=$deaths;
                  $globalCount['confirmed']+=$confirmed;
                  $globalCount['recovered']+=$recovered;
                  $countries[]=$countrydata["country"];
                 $c_code= cvct_interchange_name($countrydata["country"]);
                 if($c_code!=null){
                 $data_arr[]=array(
                    "deaths"=>$deaths,
                    "confirmed"=> $confirmed,
                    "recovered"=>$recovered,
                    "id"=>$c_code
                );
              }else{
                $notC[]=$countrydata["country"];
              }
                }
            }
      $countries_data['date']=date("Y-m-d"); 
      $countries_data['list']=$data_arr ;  
      $globalCount['date']=date("Y-m-d"); 
      $map_data['covid_world_timeline']=$countries_data;
      $map_data['covid_total_timeline']=$globalCount;
  return  $jsonData= json_encode($map_data);  
 }

 
 /**
 * Global COVID-19 spread data map
 */

function cvct_map_load_assets($layout){
      
    if($layout=="style-1"){
          wp_enqueue_script("cvct_core_js");
          wp_enqueue_script("cvct_amcharts_js");
          wp_enqueue_script("cvct_maps",'https://www.amcharts.com/lib/4/maps.js',null,null,true);
          
          wp_enqueue_script("cvct_animated_js");
          wp_enqueue_script("cvct_dark_theme",CVCT_URL.'assets/maps/dark.js',null,null,true);

          wp_enqueue_script("cvct_worldLow",'https://www.amcharts.com/lib/4/geodata/worldLow.js',null,null,true);
          wp_enqueue_script("cvct_countries2",CVCT_URL.'assets/maps/countries2.min.js',null,null,true);
          wp_enqueue_style("cvct_dark_theme",CVCT_URL.'assets/maps/dark.min.css');
          wp_enqueue_script("jquery.dataTables",CVCT_URL.'assets/maps/datatables/js/jquery.dataTables.min.js',array('jquery'),null,true);
          wp_enqueue_script("dataTables.select",CVCT_URL.'assets/maps/datatables/js/dataTables.select.min.js',array('jquery'),null,true);
          wp_enqueue_style("jquery.dataTables",CVCT_URL.'assets/maps/datatables/css/jquery.dataTables.min.css');
          wp_enqueue_style("select.dataTables",CVCT_URL.'assets/maps/datatables/css/select.dataTables.min.css');
          wp_enqueue_script("cvct_app",CVCT_URL.'assets/maps/app.min.js',array('jquery'),null,true);

          wp_enqueue_script('cvct_resizer_sensor');
          wp_enqueue_script('cvct_resizer_queries');

          wp_localize_script(
              'cvct_app','cvct_map_data',array( 'ajax_url' => admin_url('admin-ajax.php')) );
    }else{
          wp_enqueue_script("cvct_d3",'https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.3/d3.min.js',null,null,true);
          wp_enqueue_script("cvct_topojson",'https://cdnjs.cloudflare.com/ajax/libs/topojson/1.6.9/topojson.min.js',null,null,true);
          wp_enqueue_script("cvct_datamaps",'https://cdnjs.cloudflare.com/ajax/libs/datamaps/0.5.8/datamaps.all.js',null,null,true);
          wp_enqueue_script('cvct_jquery_dt');
          wp_enqueue_style('cvct_data_tbl_css');
          wp_enqueue_script("cvct_basic_map",CVCT_URL.'assets/maps/basic_map.min.js',null,null,true);
          wp_localize_script(
            'cvct_basic_map','cvct_map_data',array( 'ajax_url' => admin_url('admin-ajax.php')) );
    }
}


   
}


