<?php
/*
|--------------------------------------------------------------------------
| fetches covid-19 global stats data
|--------------------------------------------------------------------------
*/ 
function cvct_get_global_data(){
  $cache_name='cvct_global_data';
  $cache=get_transient($cache_name);
  $gstats_data='';
  $save_arr=array();
  if($cache==false){
      $api_url = CVCT_API_ENDPOINT.'/v2/all';
       $request = wp_remote_get($api_url, array('timeout' => 120));
       if (is_wp_error($request)) {
           return false; // Bail early
       }
       $body = wp_remote_retrieve_body($request);
       $gt_data = json_decode($body);
        if(isset($gt_data) && !empty($gt_data)){
          $save_arr['total_cases']= !empty($gt_data->cases)?$gt_data->cases:0;
          $save_arr['total_recovered']=!empty($gt_data->recovered)?$gt_data->recovered:0;
          $save_arr['total_deaths']=!empty($gt_data->deaths)?$gt_data->deaths:0;
          $save_arr['today_cases'] = !empty($gt_data->todayCases)?$gt_data->todayCases:0;
          $save_arr['today_deaths'] = !empty($gt_data->todayDeaths)?$gt_data->todayDeaths:0;
          $save_arr['critical'] = !empty($gt_data->critical)?$gt_data->critical:0;
          set_transient($cache_name,
          $save_arr, CVCT_Cache_Timing);
           set_transient('api-source',
           'corona.lmao.ninja',CVCT_Cache_Timing);
          update_option("cvct_gs_updated",date('Y-m-d h:i:s') );   
          $gstats_data=$save_arr;
          return $gstats_data;
       }
   }else{
   return $gstats_data=get_transient($cache_name);
  }
 }


 
function cvct_get_global_data_alternative(){
  $cache_name='cvct_gs';
  $cache=get_transient($cache_name);
  $gstats_data='';
  $save_arr=array();
if($cache==false){
       $api_url = 'https://coronavirus-19-api.herokuapp.com/all';
       $request = wp_remote_get($api_url, array('timeout' => 120));
       if (is_wp_error($request)) {
           return false; // Bail early
       }
       $body = wp_remote_retrieve_body($request);
       $gt_data = json_decode($body);
     
       if(isset($gt_data) && !empty($gt_data)){
          $save_arr['total_cases']= !empty($gt_data->cases)?$gt_data->cases:0;
          $save_arr['total_recovered']=!empty($gt_data->recovered)?$gt_data->recovered:0;
          $save_arr['total_deaths']=!empty($gt_data->deaths)?$gt_data->deaths:0;
          set_transient($cache_name,
          $save_arr,
          CVCT_Cache_Timing);
           set_transient('api-source',
           'coronavirus-19-api',
           CVCT_Cache_Timing);
          update_option("cvct_gs_updated",date('Y-m-d h:i:s') );   
          $gstats_data=$save_arr;
          return $gstats_data;
       }
   }else{
   return $gstats_data=get_transient($cache_name);
   
   }
}

 
/*
|--------------------------------------------------------------------------
| get country based data
|--------------------------------------------------------------------------
*/ 
function cvct_country_stats_data($country_code){
  
  $cache_name='cvct_cs_'.$country_code;
  $cache=get_transient($cache_name);
// $cache=false;
  $cstats_data='';
  $save_arr=[];
 if($cache==false){
       $api_url = CVCT_API_ENDPOINT.'/V2/countries/'.$country_code;
       $request = wp_remote_get($api_url, array('timeout' => 120));
    
       if (is_wp_error($request)) {
           return false; // Bail early
       }
       $body = wp_remote_retrieve_body($request);
       $cs_data = json_decode($body);
      //  Var_dump($api_url);
       if(isset($cs_data)&& !empty($cs_data)){
              $save_arr['allData']= $cs_data;
              $save_arr['total_cases']= isset($cs_data->cases)?$cs_data->cases:0;
             $save_arr['total_recovered']=isset($cs_data->recovered)?$cs_data->recovered:0;
             $save_arr['total_deaths']= isset($cs_data->deaths)?$cs_data->deaths:0;
             $save_arr['today_cases']= isset($cs_data->todayCases)?$cs_data->todayCases:0;
             $save_arr['critical']=isset($cs_data->critical)?$cs_data->critical:0;
             $save_arr['today_deaths']= isset($cs_data->todayDeaths)?$cs_data->todayDeaths:0;
         set_transient($cache_name,
         $save_arr,
         CVCT_Cache_Timing);
          set_transient('api-source',
          'cool-paid-api',
          CVCT_Cache_Timing);
           update_option("cvct_cs_updated",date('Y-m-d h:i:s') );   
           $cstats_data= $save_arr;
          return $cstats_data;
       }else{
           return false;
       }
   }else{
     return $cstats_data=get_transient($cache_name);
   }
 }


function cvct_country_stats_data_alternate($country_code){
     $cache_name='cvct_cs_'.$country_code;
     $cache=get_transient($cache_name);
     $cstats_data='';
     $save_arr=[];
    if($cache==false){
          $api_url = 'https://coronavirus-19-api.herokuapp.com/countries/'.$country_code;
          $request = wp_remote_get($api_url, array('timeout' => 120));
          if (is_wp_error($request)) {
              return false; // Bail early
          }
          $body = wp_remote_retrieve_body($request);
          $cs_data = json_decode($body);
         if(isset($cs_data)&& !empty($cs_data)){
                 $save_arr['total_cases']=$cs_data->cases;
                $save_arr['total_recovered']=$cs_data->recovered;
                $save_arr['total_deaths']=$cs_data->deaths;
                $save_arr['today_deaths']=$cs_data->todayDeaths;
                $save_arr['today_cases']=$cs_data->todayCases;
                $save_arr['critical']=$cs_data->critical;
            set_transient($cache_name,
            $save_arr,
            CVCT_Cache_Timing);
             set_transient('api-source',
             'coronavirus-19-api',
             CVCT_Cache_Timing);
              update_option("cvct_cs_updated",date('Y-m-d h:i:s') );   
              $cstats_data= $save_arr;
                  return $cstats_data;
          }else{
              return false;
          }
      }else{
        return $cstats_data=get_transient($cache_name);
      }
    }
/*
|--------------------------------------------------------------------------
| fetches covid-19 all countries stats data
|--------------------------------------------------------------------------
*/ 
function cvct_get_all_country_data(){
  
  $cache_name='cvct_countries_data';
   $cache=get_transient($cache_name);
    if(get_option('delete_countries_data_cache')==false){
        delete_transient($cache_name);
        update_option('delete_countries_data_cache',true);
    }
    $country_stats_data = array();
    $data_arr = array();
      if($cache==false){
       $api_url =CVCT_API_ENDPOINT. '/v2/countries?sort=cases';
       $api_req = wp_remote_get($api_url,array('timeout' => 120));
       if (is_wp_error($api_req)) {
        return false; // Bail early
    }
    $body = wp_remote_retrieve_body($api_req);
    $cs_data = json_decode($body);
    
     if(isset($cs_data)&& !empty($cs_data)){

       if(isset($cs_data[0]->country) && $cs_data[0]->country=="World"){
         unset($cs_data[0]);
      }
     
    foreach($cs_data as  $all_country_data){
      $data_arr['all_data'] = $all_country_data;
          if($all_country_data->country=="India"){
                $inData=cvct_india_global_stats("India");
                
              $flag=CVCT_URL.'/assets/images/india.png';
              $data_arr['cases'] =$inData['total_cases'];
              $data_arr['active'] =$inData['active'];
              $data_arr['country'] =$all_country_data->country;
              $data_arr['confirmed'] =$inData['confirmed'];
              $data_arr['recoverd'] =$inData['total_recovered'];
              $data_arr['deaths'] =$inData['total_deaths'];
              $data_arr['flag']=$flag;
          }else{
            $country_info = $all_country_data->countryInfo;
              $data_arr['cases'] = $all_country_data->cases;
              $data_arr['active'] = $all_country_data->active;
              $data_arr['country'] =  $all_country_data->country;
              $data_arr['confirmed'] = $all_country_data->cases;
              $data_arr['recoverd'] = $all_country_data->recovered;
              $data_arr['deaths'] = $all_country_data->deaths;
              $data_arr['flag'] = $country_info->flag;
      }
        $country_stats_data[] = $data_arr;
      }
    set_transient($cache_name,
    $country_stats_data,
    CVCT_Cache_Timing);
   return $country_stats_data;
  }
 else{
     return false;
 }
  }
  else{
    return $country_stats_data =get_transient($cache_name);
  }
}
 

function cvct_get_all_country_alternative(){
  $cache_name='cvct_countries_data';
  $cache=get_transient($cache_name);
  $country_stats_data = array();
  $data_arr = array();
    if($cache==false){
     $api_url = 'https://coronavirus-19-api.herokuapp.com/countries';
     $api_req = wp_remote_get($api_url,array('timeout' => 120));
     if (is_wp_error($api_req)) {
      return false; // Bail early
  }
  $body = wp_remote_retrieve_body($api_req);
  $cs_data = json_decode($body);

   if(isset($cs_data)&& !empty($cs_data)){
  foreach($cs_data as  $all_country_data){
      $data_arr['cases'] = $all_country_data->cases;
      $data_arr['active'] = $all_country_data->active;
      $data_arr['country'] =  $all_country_data->country;
      $data_arr['confirmed'] = $all_country_data->cases;
      $data_arr['recoverd'] = $all_country_data->recovered;
      $data_arr['deaths'] = $all_country_data->deaths;
     $country_stats_data[] = $data_arr;
    }
  set_transient($cache_name,
  $country_stats_data,
  CVCT_Cache_Timing);
 return $country_stats_data;
}
else{
   return false;
}
}
else{
  return $country_stats_data =get_transient($cache_name);
}
}
/*
|--------------------------------------------------------------------------
| fetches US states data
|--------------------------------------------------------------------------
*/ 

   function cvct_get_states_data($country_name){
    $cache_name='cvct_state_data_'.$country_name.'';
    $cache=get_transient($cache_name);
    if($cache==false){
      switch($country_name){
        case 'IN':
          $api_url = 'https://api.rootnet.in/covid19-in/stats/latest';
        break;
        default:
        $api_url = CVCT_API_ENDPOINT.'/v2/states';
      break;
      return $api_url;
      }
      $request = wp_remote_get($api_url);
     if(is_wp_error($request)){
       return false;
     }
     $stats_arr = array();
     $result_arr = array();
     $body = wp_remote_retrieve_body($request);
     $get_stats = json_decode($body);
     switch($country_name){
      case 'IN':
        $regional_data = isset($get_stats->data->regional)?$get_stats->data->regional:0;
        if(is_array($regional_data) && count($regional_data)>0){
      foreach($regional_data as  $loc){
      $confirmedCasesIndian = isset($loc->confirmedCasesIndian)?$loc->confirmedCasesIndian:0;
      $confirmedCasesForeign = isset($loc->confirmedCasesForeign)?$loc->confirmedCasesForeign:0;
      $total = $confirmedCasesIndian+$confirmedCasesForeign;
      $result_arr['state'] = isset($loc->loc)?$loc->loc:'';
     $result_arr['cases'] = $total;
     $result_arr['deaths'] = isset($loc->deaths)?$loc->deaths:0;
     $result_arr['discharged'] = isset($loc->discharged)?$loc->discharged:0;
     $stats_arr[] = $result_arr;
    }
  }
  break;
      default:
    foreach($get_stats as $get_stats_value){
      $result_arr['state'] = $get_stats_value->state;
       $result_arr['cases'] = $get_stats_value->cases;
       $result_arr['deaths'] = $get_stats_value->deaths;
       $result_arr['active'] = $get_stats_value->active;
       $stats_arr[] = $result_arr;
     }
    break;
  }
     set_transient($cache_name,
    $stats_arr,
    CVCT_Cache_Timing);
    return $stats_arr;
    }else{
      return $stats_arr=get_transient($cache_name);
    }
   }
/*
|--------------------------------------------------------------------------
| Load required JS
|--------------------------------------------------------------------------
*/
   function cvct_load_assets(){	
	    	wp_register_script("cvct_jquery_dt",CVCT_URL.'assets/js/cvct-jquery-dt.js',array('jquery'),CVCT_VERSION,true);
        wp_register_style("cvct_data_tbl_css",CVCT_URL.'assets/css/cvct-datatable.css');
        wp_register_script("cvct_tabl_lay_script",CVCT_URL.'assets/js/cvct-tbl-layout2.min.js',array('jquery'),CVCT_VERSION,true);
        wp_register_script("cvct_states_tabl_lay_script",CVCT_URL.'assets/js/cvct-states-tbl.min.js',array('jquery'),CVCT_VERSION,true);

        wp_register_script("cvct_resizer_sensor",CVCT_URL.'assets/js/css-resizer/ResizeSensor.min.js',array('jquery'),CVCT_VERSION,true);
        wp_register_script("cvct_resizer_queries",CVCT_URL.'assets/js/css-resizer/ElementQueries.min.js',array('jquery'),CVCT_VERSION,true);

        wp_register_style("cvct_cards_css",CVCT_URL.'assets/css/cvct-cards.min.css');
        wp_register_style("cvct_tables_css",CVCT_URL.'assets/css/cvct-tables.css');
        
        wp_register_script("cvct_core_js", CVCT_URL. 'assets/js/cvct-amchart-core.js',array('jquery'),CVCT_VERSION,true);
        wp_register_script("cvct_amcharts_js", CVCT_URL. 'assets/js/cvct-amchart.js',array('jquery'),CVCT_VERSION,true);
        wp_register_script("cvct_animated_js", CVCT_URL. 'assets/js/cvct-amchart-theme-animation.js',array('jquery'),CVCT_VERSION,true);
     
     
    //Chart assets
    wp_register_script("cvct_apexcharts",'https://cdn.jsdelivr.net/npm/apexcharts',array('jquery'),CVCT_VERSION,true);
    wp_register_script("cvct_charts_main_js",CVCT_URL.'assets/js/cvct-charts.js',array('jquery'),CVCT_VERSION,true);  
	
    //End Chart assets      
    /**
     * Historical Charts Assets
     */
    wp_register_script("cvct_historical_charts_js",CVCT_URL.'assets/js/cvct-historical-chart.min.js',array('jquery'),CVCT_VERSION,true);
  }
  



   
  
/*
|--------------------------------------------------------------------------
| get country name and flag image
|--------------------------------------------------------------------------
*/ 

  function get_country_info($c_code){
    $info='';
    if($c_code=="all"){
        $info='<img  width="20px" src="'.esc_url(CVCT_URL.'/assets/logos/global.svg').'"> ';
    }else{
        // Get the contents of the JSON file 
        $strJsonFileContents = file_get_contents(CVCT_DIR.'/assets/countries.json');
        // Convert to array 
        $countries_arr = json_decode($strJsonFileContents, true);
        // print array
        if(is_array($countries_arr) && isset($countries_arr[$c_code]))
        {
        $img_url=CVCT_URL.'/assets/logos/'.strtolower($c_code).'.svg';
        $info='<img  width="20px" src="'.esc_url($img_url).'"> '.esc_html($countries_arr[$c_code]);
        }
    }
    return $info;
}
/**
 * Get Historical Data
 */
function cvct_get_historical_data($country,$days_data){
  $historical_data='';
  $cache_name='historical_data_'.$country.'-'.$days_data.'';
  $cache=get_transient($cache_name);
  if($cache==false){
    $api_url = CVCT_API_ENDPOINT.'/v2/historical/'.$country.'?lastdays='.$days_data;
    //$api_url = 'https://corona.lmao.ninja/v2/historical/india?lastdays=10';
    $request = wp_remote_get($api_url, array('timeout' => 120));
    if (is_wp_error($request)) {
        return false; // Bail early
    }
    $body = wp_remote_retrieve_body($request);
    $response = json_decode($body);
    if($country=="all"){
      $historical_data=(array)$response;
    }else{
      if($response->timeline){
      $historical_data=(array)$response->timeline;
      }
    }
   
      set_transient($cache_name,
      $historical_data,
      6*HOUR_IN_SECONDS);
      return $historical_data;
    }else{
      return $historical_data=get_transient($cache_name);
    }
}

/*
			check admin side post type page
		*/
		function cvct_get_post_type_page() {
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
function cvct_india_global_stats($country){
  $cache_name='cvct_india_states_data';
   $cache=get_transient($cache_name);
  //$cache = false;
  $india_stats_data = array();
  $cstats_data = '';
    if($cache==false){
     $api_url = 'https://api.rootnet.in/covid19-in/stats/latest';
     $api_req = wp_remote_get($api_url,array('timeout' => 120));
     if (is_wp_error($api_req)) {
      return false; // Bail early
  }
  $body = wp_remote_retrieve_body($api_req);
  $cs_data = json_decode($body);
  $summary = isset($cs_data->data->summary)?$cs_data->data->summary:'';
  $india_stats_data['total_cases'] =isset($summary->total)?$summary->total:0;
  $india_stats_data['confirmed'] = isset($summary->confirmedCasesIndian)?$summary->confirmedCasesIndian:0;
  $india_stats_data['total_recovered'] = isset($summary->discharged)?$summary->discharged:0;
  $india_stats_data['total_deaths'] = isset($summary->deaths)?$summary->deaths:0;
  $india_stats_data['active'] =$india_stats_data['total_cases']-($india_stats_data['total_recovered']+$india_stats_data['total_deaths']);
  set_transient($cache_name,
  $india_stats_data,
  2*HOUR_IN_SECONDS);
  set_transient('api-source',
  'api.rootnet.in',
  2*HOUR_IN_SECONDS);
   update_option("cvct_cs_updated",date('Y-m-d h:i:s') );
 $cstats_data= $india_stats_data;
        return $cstats_data;
}
else{
  return $cstats_data=get_transient($cache_name);
 }

}

function get_country_arr(){
  $countries = array
  (
  "AF"=>"Afghanistan",
  "AL"=>"Albania",
  "DZ"=>"Algeria",
  "AO"=>"Angola",
  "AR"=>"Argentina",
  "AM"=>"Armenia",
  "AU"=>"Australia",
  "AT"=>"Austria",
  "AZ"=>"Azerbaijan",
  "BS"=>"Bahamas",
  "BD"=>"Bangladesh",
  "BY"=>"Belarus",
  "BE"=>"Belgium",
  "BZ"=>"Belize",
  "BJ"=>"Benin",
  "BT"=>"Bhutan",
  "BO"=>"Bolivia",
  "BA"=>"Bosnia and Herzegovina",
  "BW"=>"Botswana",
  "BR"=>"Brazil",
  "BN"=>"Brunei Darussalam",
  "BG"=>"Bulgaria",
  "BF"=>"Burkina Faso",
  "BI"=>"Burundi",
  "KH"=>"Cambodia",
  "CM"=>"Cameroon",
  "CA"=>"Canada",
  "CF"=>"Central African Republic",
  "TD"=>"Chad",
  "CL"=>"Chile",
  "CN"=>"China",
  "CO"=>"Colombia",
  "CG"=>"Congo",
  "CR"=>"Costa Rica",
  "HR"=>"Croatia",
  "CU"=>"Cuba",
  "CY"=>"Cyprus",
  "CZ"=>"Czech Republic",
  "CD"=>"Democratic Republic of Congo",
  "DK"=>"Denmark",
  "DP"=>"Diamond Princess",
  "DJ"=>"Djibouti",
  "DO"=>"Dominican Republic",
  "CD"=>"DR Congo",
  "EC"=>"Ecuador",
  "EG"=>"Egypt",
  "SV"=>"El Salvador",
  "GQ"=>"Equatorial Guinea",
  "ER"=>"Eritrea",
  "EE"=>"Estonia",
  "ET"=>"Ethiopia",
  "FK"=>"Falkland Islands",
  "FJ"=>"Fiji",
  "FI"=>"Finland",
  "FR"=>"France",
  "GF"=>"French Guiana",
  "TF"=>"French Southern Territories",
  "GA"=>"Gabon",
  "GM"=>"Gambia",
  "GE"=>"Georgia",
  "DE"=>"Germany",
  "GH"=>"Ghana",
  "GR"=>"Greece",
  "GL"=>"Greenland",
  "GT"=>"Guatemala",
  "GN"=>"Guinea",
  "GW"=>"Guinea-Bissau",
  "GY"=>"Guyana",
  "HT"=>"Haiti",
  "HN"=>"Honduras",
  "HK"=>"Hong Kong",
  "HU"=>"Hungary",
  "IS"=>"Iceland",
  "IN"=>"India",
  "ID"=>"Indonesia",
  "IR"=>"Iran",
  "IQ"=>"Iraq",
  "IE"=>"Ireland",
  "IL"=>"Israel",
  "IT"=>"Italy",
  "CI"=>"Ivory Coast",
  "JM"=>"Jamaica",
  "JP"=>"Japan",
  "JO"=>"Jordan",
  "KZ"=>"Kazakhstan",
  "KE"=>"Kenya",
  "KP"=>"Korea",
  "XK"=>"Kosovo",
  "KW"=>"Kuwait",
  "KG"=>"Kyrgyzstan",
  "LA"=>"Lao",
  "LV"=>"Latvia",
  "LB"=>"Lebanon",
  "LS"=>"Lesotho",
  "LR"=>"Liberia",
  "LY"=>"Libya",
  "LT"=>"Lithuania",
  "LU"=>"Luxembourg",
  "MK"=>"Macedonia",
  "MG"=>"Madagascar",
  "MW"=>"Malawi",
  "MY"=>"Malaysia",
  "ML"=>"Mali",
  "MR"=>"Mauritania",
  "MX"=>"Mexico",
  "MD"=>"Moldova",
  "MN"=>"Mongolia",
  "ME"=>"Montenegro",
  "MA"=>"Morocco",
  "MZ"=>"Mozambique",
  "MM"=>"Myanmar",
  "NA"=>"Namibia",
  "NP"=>"Nepal",
  "NL"=>"Netherlands",
  "NC"=>"New Caledonia",//
  "NZ"=>"New Zealand",
  "NI"=>"Nicaragua",
  "NE"=>"Niger",
  "NG"=>"Nigeria",
  "KP"=>"North Korea",
  "NO"=>"Norway",
  "OM"=>"Oman",
  "PK"=>"Pakistan",
  "PS"=>"Palestine",
  
  "PA"=>"Panama",
  "PG"=>"Papua New Guinea",
  "PY"=>"Paraguay",
  "PE"=>"Peru",
  "PH"=>"Philippines",
  "PL"=>"Poland",
  "PT"=>"Portugal",
  "PR"=>"Puerto Rico",
  "QA"=>"Qatar",
  "XK"=>"Republic of Kosovo",
  "RO"=>"Romania",
  "RU"=>"Russia",
  "RW"=>"Rwanda",
  "SA"=>"Saudi Arabia",
  "SN"=>"Senegal",
  "RS"=>"Serbia",
  "SL"=>"Sierra Leone",
  "SG"=>"Singapore",
  "SK"=>"Slovakia",
  "SI"=>"Slovenia",
  "SB"=>"Solomon Islands",
  "SO"=>"Somalia",
  "ZA"=>"South Africa",
  "KR"=>"South Korea",
  "SS"=>"South Sudan",
  "ES"=>"Spain",
  "LK"=>"Sri Lanka",
  "SD"=>"Sudan",
  "SR"=>"Suriname",
  "SJ"=>"Svalbard and Jan Mayen",
  "SZ"=>"Swaziland",
  "SE"=>"Sweden",
  "CH"=>"Switzerland",
  "SY"=>"Syrian Arab Republic",
  "TW"=>"Taiwan",
  "TJ"=>"Tajikistan",
  "TZ"=>"Tanzania",
  "TH"=>"Thailand",
  "TL"=>"Timor-Leste",
  "TG"=>"Togo",
  "TT"=>"Trinidad and Tobago",
  "TN"=>"Tunisia",
  "TR"=>"Turkey",
  "TM"=>"Turkmenistan",
  "AE"=>"UAE",
  "UG"=>"Uganda",
  "UA"=>"Ukraine",
  "GB"=>"United Kingdom",
  "UY"=>"Uruguay",
  "US"=>"USA",
  "UZ"=>"Uzbekistan",
  "VU"=>"Vanuatu",
  "VE"=>"Venezuela",
  "VN"=>"Vietnam",
  "EH"=>"Western Sahara",
  "YE"=>"Yemen",
  "ZM"=>"Zambia",
  "ZW"=>"Zimbabwe"
  );
  return $countries;
}
function cvct_get_multiple_country_hist_data($country,$days_data){
$data_arr = array();
  $result_arr = array();
  $historical_data='';
   $cache_name='cvct_compare_data_'.$country.'-'.$days_data.'';
   $cache=get_transient($cache_name);
  if($cache==false){
  $api_url = CVCT_API_ENDPOINT.'/v2/historical/'.$country.'?lastdays='.$days_data;
    //$api_url = 'https://corona.lmao.ninja/v2/historical/india?lastdays=10';
    $request = wp_remote_get($api_url, array('timeout' => 120));
    if (is_wp_error($request)) {
        return false; // Bail early
    }
    $body = wp_remote_retrieve_body($request);
    $response = json_decode($body);
    if(is_array($response)){
      foreach($response as $values){
        $data_arr['country'] = $values->country;
        $data_arr['timeline'] = $values->timeline;
        $result_arr[] = $data_arr;
      }
    }
    else{
      $data_arr['country'] = $response->country;
      $data_arr['timeline'] = $response->timeline;
      $result_arr[] = $data_arr;
    }
    set_transient($cache_name,
    $result_arr,
    6*HOUR_IN_SECONDS);
    return $result_arr;
  }
  else{
    return $result_arr=get_transient($cache_name);
  }
}
