<?php

class CVCT_CountryStats
{
function __construct() {
    //main plugin shortcode for list widget
    require_once CVCT_DIR . 'includes/get_country_name.php';
    include_once CVCT_DIR . 'includes/cvct-functions.php';
    add_shortcode( 'cvct-country-stats', array($this, 'cvct_country_shortcode' ));
}
/*
|--------------------------------------------------------------------------
| Corona Stats - USA/Country Tabel/map Shortcodes
|--------------------------------------------------------------------------
*/ 
public function  cvct_country_shortcode( $atts, $content = null ) {
    $atts = shortcode_atts( array(
        'title'=>'US COVID-19 Stats',
        'layout'=>'style-2', 
        'country-code'=>'US',
        'label-confirmed'=>"Confirmed cases",
        'label-deaths'=>"Death cases",
         'label-recovered'=>"Recovered cases",
         'label-active'=>'Active cases',
         'label-states'=>'States cases',
        'bg-color'=>'#222222',
        'font-color'=>'#f9f9f9',
        'show'=>'10',
        'width'=>'', 
        'height'=>'',
    ), $atts, 'cvct_country_map' );
    $css ='';
    $layout=!empty($atts['layout'])?$atts['layout']:"style-1";
    $title = !empty($atts['title'])?$atts['title']:'US COVID-19 Stats';
    $country_code = !empty($atts['country-code'])?$atts['country-code']:'US';
    $label_states = !empty($atts['label-states'])?$atts['label-states']:'States';
    $label_confirmed = !empty($atts['label-confirmed'])?$atts['label-confirmed']:'Confirmed';
    $label_deaths = !empty($atts['label-deaths'])?$atts['label-deaths']:'Death';
    $label_recovered  = !empty($atts['label-recovered'])?$atts['label-recovered']:'Recovered';
    $label_active = !empty($atts['label-active'])?$atts['label-active']:'Active';
    $bgColors=!empty($atts['bg-color'])?$atts['bg-color']:"#222222";
    $fontColors=!empty($atts['font-color'])?$atts['font-color']:"#f9f9f9";
    $show_entry = !empty($atts['show'])?$atts['show']:'10';
    $output='';
  

    if($layout=="style-2"){
    
      if($country_code=="US"){
        wp_enqueue_script("cvct_d3_us",'https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.3/d3.min.js',null,null,true);
          wp_enqueue_script("cvct_topojson",'https://cdnjs.cloudflare.com/ajax/libs/topojson/1.6.9/topojson.min.js',null,null,true);
          wp_enqueue_script("cvct_datamaps_us",CVCT_URL.'assets/js/datamaps.usa.min.js',null,null,true);
        }else{
        /*  wp_enqueue_script("cvct_d3_in",'http://d3js.org/d3.v3.min.js',null,null,true);
          wp_enqueue_script("cvct_topojson",'https://d3js.org/topojson.v1.min.js',null,null,true);  
          wp_enqueue_script("cvct_datamaps_in",'https://rawgit.com/Anujarya300/bubble_maps/master/data/geography-data/datamaps.none.js',null,null,true);
          */
        }
        wp_enqueue_script("cvct_country_stats",CVCT_URL.'assets/js/country_stats.min.js',array('jquery'),null,null,true);
    }
    else{
      wp_enqueue_script('cvct_jquery_dt');
      wp_enqueue_script('cvct_states_tabl_lay_script');
      wp_enqueue_style('cvct_data_tbl_css');
  }
    if($layout=="style-2" && !in_array($country_code,array("US"))){
     return  $output.='<div>'.__('Map does not available for this country').'</div>';
    }   
    if($country_code=="IN"){
      $stats_data= cvct_india_global_stats($country_code);
    }else{
      $stats_data= cvct_country_stats_data("USA");
    }
    if($stats_data==false){
        $stats_data=cvct_get_global_data_alternative("USA");
    }
    $last_updated=date("d M Y, G:i A",strtotime(get_option('cvct_cs_updated'))).' (GMT)';

    if(is_array($stats_data) && count($stats_data)>0){
        $total=$stats_data['total_cases'];
        $recovered=$stats_data['total_recovered'];
        $deaths=$stats_data['total_deaths'];
        $total_cases=!empty($total)?number_format($total):"0";
        $total_recovered=!empty($recovered)?number_format($recovered):"0";
        $total_deaths=!empty($deaths)?number_format($deaths):"0";
        $active_cases=$total-($recovered+$deaths);
    }

    

  if($layout=="style-1"){
	$output.= '
	<div id="cvct-country-table-wrapper">
	<table id="cvct_states_table_id" class="table table-striped table-bordered cvct-states-table" data-pagination="'.$show_entry.'">
    <thead>
          <tr>
          <th class="cvct-th">'.__($label_states,'cvct').'</th>
          <th class="cvct-th">'.__($label_confirmed,'cvct').'</th>
          <th class="cvct-th">'.__($label_recovered,'cvct').'</th>
         <th class="cvct-th">'.__($label_deaths,'cvct').'</th>
         </tr>
         </thead>';
  }
    $jsonData='';
   $countryStats= cvct_get_states_data($country_code);
    $states_data=[];
    $sum_cases = array();
    $recoverd_sum = array();
    $death_sum = array();
    $total_case = 0;
    $condition='';
    if($country_code=="IN"){
      $states_data['ML']['all']=0;
      $states_data['ML']['fillKey']='none';
    }
   if(is_array($countryStats)&& count($countryStats)>0){
    foreach($countryStats as $countrydata){
       $cases= isset($countrydata["cases"])?$countrydata["cases"]:0;
       $state = isset($countrydata['state'])?$countrydata['state']:null;
       $active = isset($countrydata['active'])?$countrydata['active']:0;
       $death = isset($countrydata['deaths'])?$countrydata['deaths']:0;
       if($country_code=="IN"){
        $recovered = isset($countrydata['discharged'])?$countrydata['discharged']:0;
       }
       else{
        $recovered = $cases-($active+$death);
       }
      switch($layout){
          case 'style-2':
            if($country_code=="US"){
              if( $cases>20000){
                $condition='20000+';
              }else if($cases>5000){
                $condition='5000+';
              }else if($cases>1000){
                $condition='1000+';
              }else if($cases>100){
                $condition='100+';
              }else if($cases>1){
                $condition='1+';
              }else{
                $condition='none';
              }
            if($countrydata["state"]){
              $state_code=$this->get_us_state_code($countrydata["state"]);
              $countrydata['recovered']= $recovered;
              $states_data[$state_code]['all']= $countrydata;
              $states_data[$state_code]['fillKey']=$condition;
            }
            if(isset($states_data[0])){
            unset( $states_data[0]);
            }
          }else if($country_code=="IN"){
              if( $cases>1000){
                $condition='high';
              }else if($cases>500){
                $condition='medium';
              }else if($cases>100){
                $condition='minor';
              }else if($cases>20){
                $condition='low';
              } else if($cases>0){
                $condition='verylow';
              }else{
                $condition='none';
              }

             if($countrydata["state"]){
               $state_code=$this->get_in_state_code($countrydata["state"]);
               $countrydata['recovered']= $recovered;
               $states_data[$state_code]['all']= $countrydata;
               $states_data[$state_code]['fillKey']=$condition;
             }
             if(isset($states_data[0])){
              unset( $states_data[0]);
              }
          } 
        break;
        default: 
        $output .= '<tr class="cvct-style2-stats">
        <td>'.$state .'</td>
        <td>'.$cases.'</td>
        <td>'.$recovered.'</td>
        <td>'.$death.'</td>
        </tr>';
    break;
    }
  }



  switch($layout){
    case 'style-2':
      $width= !empty($atts['width'])?$atts['width']:100;
      $height=!empty( $atts['height'])?$atts['height']:900;
      $jsonData=json_encode($states_data);
      $customStyles="#cvct-country-map{height:".$height."px;overflow:visible;}
      #cvct-country-map-outer{
      padding-bottom:80%;text-align:left;
      }";
      $map_id='cvct_country_map_'.$country_code;
        $output .='
        <div class="cvct-country-map-wrapper" >';
        $output.='<script type="application/json" id="'.$map_id.'_data">'.$jsonData.'</script>';
        $output.='<div id="cvct-country-map-outer"><div data-country="'.$country_code.'" class="cvct-country-map" id="'.$map_id.'"></div></div>
        </div>';
  
 $output .='<style>
  .cvct-country-map-wrapper {
		width: 100%;
		display: block;
		overflow: visible;
		padding: 0 0 6px;
		margin: 10px auto 16px;
		text-align:center;
	}
    .cvct-country-map-wrapper .cvct-country-card {
      display: inline-block;
      width: 100%;
      max-width: 750px;
      padding: 10px;
      border-radius: 8px;
  	}
  	.cvct-country-map-wrapper .cvct-country-card h2 {
      margin: 5px 0 10px 0;
      padding: 0;
      font-size: 20px;
      line-height: 22px;
      font-weight: bold;
      display: inline-block;
      width: 100%;
      text-align:center;
	}
	.cvct-country-map-wrapper .cvct-country-card .cvct-number {
      width: 33.33%;
      display: inline-block;
	  float: none;
	  padding:3px;
      text-align: center;
      vertical-align: top;
	}
.cvct-country-map-wrapper .cvct-country-card .cvct-number span {
      width: 100%;
      display: inline-block;
      font-size: 14px;
      line-height: 16px;
      margin-bottom: 2px;
      word-break: keep-all;
      vertical-align: middle;
	}
	.cvct-country-map-wrapper .cvct-country-card .cvct-number span.large-num {
      font-size: 18px;
      line-height: 21px;
      font-weight: bold;
      margin-bottom: 3px;
	}
	@media only screen and (max-width: 480px) {
		.cvct-country-map-wrapper .cvct-country-card .cvct-number {
		  width: 49.98%;
		}
	}

	#cvct-country-map-outer {
		position: relative;
		height: 0;
  padding-bottom: 55%;
	}
   .cvct-country-map {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		width:100%;
		height:auto;
	}
    </style>';
  $output.='<style>'. $customStyles.'</style>';
  break;
  default:
  $output.=  '</tbody>
  <tfoot>
  <tr>
     <th>total</th>
     <th>'.$total_cases.'</th>
     <th>'.$total_recovered.'</th>
     <th>'.$total_deaths.'</th>
  </tr>
  </tfoot>
  </table>
  </div>';
  $css .=$this->customCSS();
  break;
  }
  return $output.$css;
  }
  
}

/**
 * get state name or Code
 */
public function get_in_state_code($name){
  $indian_all_states  = array (
    'AP' => 'Andhra Pradesh',
    'AR' => 'Arunachal Pradesh',
    'AS' => 'Assam',
    'BR' => 'Bihar',
    'CT' => 'Chhattisgarh',
    'GA' => 'Goa',
    'GJ' => 'Gujarat',
    'HR' => 'Haryana',
    'HP' => 'Himachal Pradesh',
    'JK' => 'Jammu and Kashmir',
    'JH' => 'Jharkhand',
    'KA' => 'Karnataka',
    'KL' => 'Kerala',
    'MP' => 'Madhya Pradesh',
    'MH' => 'Maharashtra',
    'MN' => 'Manipur',
    'ML' => 'Meghalaya',
    'MZ' => 'Mizoram',
    'NL' => 'Nagaland',
    'OD' => 'Odisha',
    'PB' => 'Punjab',
    'RJ' => 'Rajasthan',
    'SK' => 'Sikkim',
    'TN' => 'Tamil Nadu',
    'TR' => 'Tripura',
    'UK' => 'Uttarakhand',
    'UP' => 'Uttar Pradesh',
    'WB' => 'West Bengal',
    'AN' => 'Andaman and Nicobar Islands',
    'CH' => 'Chandigarh',
    'DN' => 'Dadra and Nagar Haveli',
    'DD' => 'Daman & Diu',
    'DL' => 'Delhi',
    'LD' => 'Lakshadweep',
    'TS' =>'Telengana',
    'PY' => 'Puducherry',
    'LD'  =>'Ladakh'
  );
  $code=array_search($name,$indian_all_states,true);
  if($code){
    return $code;
  }else{
    return false;
  }
}
public function get_us_state_code($name){
  $state_list = array('AL'=>"Alabama",  
    'AK'=>"Alaska",  
    'AZ'=>"Arizona",  
    'AR'=>"Arkansas",  
    'CA'=>"California",  
    'CO'=>"Colorado",  
    'CT'=>"Connecticut",  
    'DE'=>"Delaware",  
    'DC'=>"District Of Columbia",  
    'FL'=>"Florida",  
    'GA'=>"Georgia",  
    'HI'=>"Hawaii",  
    'ID'=>"Idaho",  
    'IL'=>"Illinois",  
    'IN'=>"Indiana",  
    'IA'=>"Iowa",  
    'KS'=>"Kansas",  
    'KY'=>"Kentucky",  
    'LA'=>"Louisiana",  
    'ME'=>"Maine",  
    'MD'=>"Maryland",  
    'MA'=>"Massachusetts",  
    'MI'=>"Michigan",  
    'MN'=>"Minnesota",  
    'MS'=>"Mississippi",  
    'MO'=>"Missouri",  
    'MT'=>"Montana",
    'NE'=>"Nebraska",
    'NV'=>"Nevada",
    'NH'=>"New Hampshire",
    'NJ'=>"New Jersey",
    'NM'=>"New Mexico",
    'NY'=>"New York",
    'NC'=>"North Carolina",
    'ND'=>"North Dakota",
    'OH'=>"Ohio",  
    'OK'=>"Oklahoma",  
    'OR'=>"Oregon",  
    'PA'=>"Pennsylvania",  
    'RI'=>"Rhode Island",  
    'SC'=>"South Carolina",  
    'SD'=>"South Dakota",
    'TN'=>"Tennessee",  
    'TX'=>"Texas",  
    'UT'=>"Utah",  
    'VT'=>"Vermont",  
    'VA'=>"Virginia",  
    'WA'=>"Washington",  
    'WV'=>"West Virginia",  
    'WI'=>"Wisconsin",  
    'WY'=>"Wyoming");
    $code=array_search($name,$state_list,true);
    if($code){
      return $code;
    }else{
      return false;
    }
}

public function customCSS(){
    $css='<style>
    #cvct_states_table_id_wrapper table#cvct_states_table_id tr th {
      background-color: black;
      color: white;
  }
  #cvct_states_table_id_wrapper table#cvct_states_table_id tr td {
    background-color: #f9f9f9;
    color: #222222;
  }
  #cvct-country-table-wrapper {
	width: 100%;
	display: block;
	overflow-x: auto;
	padding: 0 0 6px;
	margin: 10px auto 16px;
	}
	/* width */
	#cvct-country-table-wrapper::-webkit-scrollbar {
		height: 10px;
		cursor: pointer;
	}
	/* Track */
	#cvct-country-table-wrapper::-webkit-scrollbar-track {
		background: #fff;
		border: 1px solid #ddd;
	}
	/* Handle */
	#cvct-country-table-wrapper::-webkit-scrollbar-thumb {
		background: #aaa;
	}
	/* Handle on hover */
	#cvct-country-table-wrapper::-webkit-scrollbar-thumb:hover {
		background: #ccc; 
	}
	#cvct-country-table-wrapper table {
		table-layout: fixed;
		border-collapse: collapse;
		border-radius: 5px;
		overflow: hidden;
		margin: 0;
		padding: 0;
	}
	#cvct-country-table-wrapper table tr th,
	#cvct-country-table-wrapper table tr td {
		text-align: center;
		vertical-align: middle;
		font-size:14px;
		line-height:16px;
		text-transform:capitalize;
		border: 1px solid rgba(0, 0, 0, 0.15);
		width: 110px;
		padding: 12px 8px;
	}

	#cvct-country-table-wrapper .dataTables_wrapper input,
	#cvct-country-table-wrapper .dataTables_wrapper select {
		display: inline-block !IMPORTANT;
		margin: 0 2px !IMPORTANT;
		width: auto !IMPORTANT;
		min-width: 60px;
		padding: 8px;
		min-height: 44px;
		box-sizing: border-box;
		vertical-align: middle;
	}
	#cvct-country-table-wrapper .dataTables_wrapper label {
		margin-bottom: 12px;
		display: inline-block;
		vertical-align: middle;
	}
	#cvct-country-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button {
		padding: 0.3em 0.8em;
		border-color: transparent;
	}
	#cvct-country-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
		border-color: transparent;
	}

	#cvct-country-table-wrapper table.cvct-states-table tr th {background-color:".$bgColors.";color:".$fontColors.";}
	#cvct-country-table-wrapper table.cvct-states-table tr td {background-color:".$fontColors.";color:".$bgColors.";}
	#cvct-country-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.current {background:".$bgColors.";color:".$fontColors." !Important;}
	#cvct-country-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button {background:".$fontColors.";color:".$bgColors." !Important;}
	#cvct-country-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button:hover {background:".$bgColors.";color:".$fontColors." !Important;}
  </style>';
  return $css;
}
}


