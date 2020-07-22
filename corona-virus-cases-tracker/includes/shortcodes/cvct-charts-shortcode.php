<?php

class CVCT_Charts_Shortcode
{

    
function __construct() {
    //main plugin shortcode for list widget
    require_once CVCT_DIR . 'includes/get_country_name.php';
    include_once CVCT_DIR . 'includes/cvct-functions.php';
    add_shortcode( 'cvct-charts', array($this, 'cvct_charts_shortcode' ));
    add_shortcode( 'cvct-rate-distribution-charts', array($this, 'cvct_gauge_charts_shortcode' ));
    
 
}

/*
|--------------------------------------------------------------------------
| Countries data shortcode
|--------------------------------------------------------------------------
*/ 
public function  cvct_charts_shortcode( $atts, $content = null ) {
  $atts = shortcode_atts( array(
      'title'=>'Global Cases',
      'width'=>'700', 
      'height'=>'450',
      'type'=>'bar',
      'country'=>'all',
      'show-data'=>'cases',
      'bar-color'=>'',
      
      
  ), $atts, 'cvct-charts' );
      wp_enqueue_script('cvct_apexcharts');
      wp_enqueue_script('cvct_charts_main_js');
     $title= !empty($atts['title'])?$atts['title']:"Worldwide Cases";
     $country= !empty($atts['country'])?$atts['country']:"all";
     $width= !empty($atts['width'])?$atts['width']:700;
     $height=!empty( $atts['height'])?$atts['height']:450;
     $type=!empty($atts['type'])?$atts['type']:"bar";
     
     $show_case=!empty($atts['show-data'])?$atts['show-data']:"cases";
     $show_data=($show_case=="recovered")?"recoverd":$show_case;
     if($show_data=="cases"){
      $bar_color=!empty($atts['bar-color'])?$atts['bar-color']:"#FF4500";
      
     }
     elseif($show_data=="active"){
      $bar_color=!empty($atts['bar-color'])?$atts['bar-color']:"#1E90FF";
      
     }
     elseif($show_data=="recoverd"){
      $bar_color=!empty($atts['bar-color'])?$atts['bar-color']:"#32CD32";
      
     }
     else{
      $bar_color=!empty($atts['bar-color'])?$atts['bar-color']:"#FF6347";
      
     }

    // $bar_color=!empty($show_data=="cases")?$atts['bar-color']:"#fff";
    
      $styles='max-width:'.$width.'px;
      max-height:'.$height.'px;
      margin: 35px auto;';
      $jsonData='';
      $jsonData=$this->get_us_states_data($type,$show_data);
    
        if($country=="US"){
          $jsonData=$this->get_us_states_data($type,$show_data);
        }else{
          $jsonData=$this->get_all_countries_data($type,$show_data);
        }
     $chartId='cvct_chart_'.$country.'_'.$type.'_'.$show_data;
     return $output='<div class="cvct_chart_wrp">
     <div  data-title="'.$title.'" data-type="'.$type.'" bar-color="'.$bar_color.'"  id="'.$chartId.'"></div>
     <script type="application/json" id="'.$chartId.'_data">'.$jsonData.'</script>
     </div>
    <style>#'.$chartId.'{'.$styles.'} .apexcharts-menu-icon {
      display: none;
  }</style>';

}  

/*
|--------------------------------------------------------------------------
| get US states list
|--------------------------------------------------------------------------
*/ 
function get_us_states_data($charttype,$show_data){
  $data_arr=[];
  $restData=0;
  $restData_death=0;
  $restData_recover=0;
  $death_arr=[];
  $recover_arr=[];
  $label_arr=[];
  $name_arr=[];
  $cases=[];
  $clr=[];
  $usStates= cvct_get_states_data("USA");
  //var_dump($usStates);
          if(is_array($usStates)&& count($usStates)>0){
              $i=0;
              foreach($usStates as $state_info){
                  $i++;
                  if($i>10){
                      $restData +=($show_data=="recoverd")?$state_info["cases"]-($state_info["active"]+$state_info["deaths"]):$state_info[$show_data];
                      $restData_death +=is_numeric($state_info["deaths"])?$state_info["deaths"]:0;
                      $restData_recover +=is_numeric($state_info["cases"]-($state_info["active"]+$state_info["deaths"]))?$state_info["cases"]-($state_info["active"]+$state_info["deaths"]):0;
                  }else{
                        $data_arr[]=array("x"=>$state_info["state"],"y"=>($show_data=="recoverd")?$state_info["cases"]-($state_info["active"]+$state_info["deaths"]):$state_info[$show_data]);
                       $death_arr[]=$state_info["deaths"];
                       $recover_arr[]=$state_info["cases"]-($state_info["active"]+$state_info["deaths"]);
                     //  $criticl_arr[]=$state_info["critical"];
                       $label_arr[]=$state_info["cases"];
                       $name_arr[]=$state_info["state"];
                       $cases[]=($show_data=="recoverd")?$state_info["cases"]-($state_info["active"]+$state_info["deaths"]):$state_info[$show_data];
                  }
              }
               $data_arr[]=array("x"=>"Rest Of States","y"=>$restData);
              $label_arr[]=$restData;
              $name_arr[]="Rest Of States";
              $death_arr[]=$restData_death ;
              $recover_arr[]=$restData_recover;
              $cases[]=$restData;
              if($show_data=="recoverd"){
              $clr=["#008000","#008000","#008000","#008000","#66CDAA","#66CDAA","#66CDAA","#66CDAA","#90EE90","#90EE90","#90EE90",];
              }
              elseif($show_data=="deaths"){
                $clr=["#DC143C","#DC143C","#DC143C","#DC143C","#FF6347","#FF6347","#FF6347","#FF6347","#FFA07A","#FFA07A","#FFA07A",];
               
              }
              elseif($show_data=="cases"){
                $clr=["#1E90FF","#1E90FF","#1E90FF","#1E90FF","#00BFFF","#00BFFF","#00BFFF","#00BFFF","#87CEEB","#87CEEB","#87CEEB",];
               
              }
              else {
                $clr=["#EE82EE","#EE82EE","#EE82EE","#EE82EE","#FF69B4","#FF69B4","#FF69B4","#FF69B4","#FFC0CB","#FFC0CB","#FFC0CB",];
               
              }
         
         
         
            }
          if($charttype=="bar"){
          return  $chartData=json_encode(array(
            "data"=>$data_arr,
            "name"=>($show_data=="recoverd")?"Recovered":ucfirst($show_data),
          //  "cname"=>$name_arr,
          //  "case"=>$label_arr,
          //  "death"=>$death_arr,
           // "recover"=>$recover_arr,
          ));
      }
      else if($charttype=="stack"){

        return  $chartData=json_encode(array(        
          "cname"=>$name_arr,
          "case"=>$label_arr,
          "death"=>$death_arr,
          "recover"=>$recover_arr,
         // "active"=>$active_arr,
        ));
      }
      
      
      else{
        return  $chartData=json_encode(array(
          "lbl"=>$cases,
          "nm"=>$name_arr,
          "clr"=>$clr
        ));
      }
}
/*
|--------------------------------------------------------------------------
| get countires list
|--------------------------------------------------------------------------
*/ 
function get_all_countries_data($charttype,$show_data){
  $data_arr=[];
  $restData=0;
  $restData_death=0;
  $restData_recover=0;
  $death_arr=[];
  $recover_arr=[];
  $label_arr=[];
  $name_arr=[];
  $cases=[];
  $allData=cvct_get_all_country_data();
  $clr="";
 //echo"<pre>";
  //var_dump($allData);
  if($allData==false){
    $allData=cvct_get_all_country_alternative();
  }
          if(is_array($allData)&& count($allData)>0){
              $i=0;
              foreach($allData as $countrydata){
                  $i++;
                  if($i>10){
                      $restData +=is_numeric($countrydata[$show_data])?$countrydata[$show_data]:0;
                      $restData_death +=is_numeric($countrydata["deaths"])?$countrydata["deaths"]:0;
                      $restData_recover +=is_numeric($countrydata["recoverd"])?$countrydata["recoverd"]:0;
                  }else{
                        $data_arr[]=array("x"=>$countrydata["country"],"y"=>$countrydata[$show_data]);
                        $death_arr[]=$countrydata["deaths"];
                        $recover_arr[]=$countrydata["recoverd"];
                       // $active_arr[]=$countrydata["active"];                       
                        $label_arr[]=$countrydata["cases"];
                        $name_arr[]=$countrydata["country"];
                        $cases[]=$countrydata[$show_data];
                  }
              }
                $data_arr[]=array("x"=>"Rest Of World","y"=>$restData);
                $label_arr[]=$restData;
                $name_arr[]="Rest Of World";
                $death_arr[]=$restData_death ;
                $recover_arr[]=$restData_recover;
                $cases[]=$restData;
              //  $active_arr[]=$restData_activ;
              if($show_data=="recoverd"){
                $clr=["#008000","#008000","#008000","#008000","#66CDAA","#66CDAA","#66CDAA","#66CDAA","#90EE90","#90EE90","#90EE90",];
                }
                elseif($show_data=="deaths"){
                  $clr=["#DC143C","#DC143C","#DC143C","#DC143C","#FF6347","#FF6347","#FF6347","#FF6347","#FFA07A","#FFA07A","#FFA07A",];
                 
                }
                elseif($show_data=="cases"){
                  $clr=["#1E90FF","#1E90FF","#1E90FF","#1E90FF","#00BFFF","#00BFFF","#00BFFF","#00BFFF","#87CEEB","#87CEEB","#87CEEB",];
                 
                }
                else {
                  $clr=["#EE82EE","#EE82EE","#EE82EE","#EE82EE","#FF69B4","#FF69B4","#FF69B4","#FF69B4","#DDA0DD","#DDA0DD","#DDA0DD",];
                 
                }
           
          }
          if($charttype=="bar"){
          return  $chartData=json_encode(array(
            "data"=> $data_arr,
            "name"=>ucfirst($show_data),
           // "cname"=>$name_arr,
           // "case"=>$label_arr,
          //  "death"=>$death_arr,
          //  "recover"=>$recover_arr,
           // "active"=>$active_arr,
          ));
      }
      else if($charttype=="stack"){

        return  $chartData=json_encode(array(        
          "cname"=>$name_arr,
          "case"=>$label_arr,
          "death"=>$death_arr,
          "recover"=>$recover_arr,
         // "active"=>$active_arr,
        ));
      }
      else{
        return  $chartData=json_encode(array(
          "lbl"=>$cases,
          "nm"=>$name_arr,
          "clr"=>$clr,
        ));
      }
}



//country based piechart shortcode

 public function  cvct_gauge_charts_shortcode( $atts, $content = null ) {
  $atts = shortcode_atts( array(
    'title'=>'Covid 19 Stats',
    'country_code'=>'all',
    'label-confirmed'	=> 'Confirmed',
		'label-deaths'		=> 'Death',
		'label-recovered'	=> 'Recovered',
    'label-active'		=> 'Active',
    'label-critical'		=> 'Critical',
      
  ), $atts, 'cvct-pie-charts' );
    //  wp_enqueue_script('cvct_apexcharts');
      wp_enqueue_script('cvct_core_js');
      wp_enqueue_script('cvct_amcharts_js');
      wp_enqueue_script('cvct_animated_js');
      wp_enqueue_script('cvct_charts_main_js');
     $data_lbl=[];
     $data_series=[];
     $country_code= !empty($atts['country_code'])?$atts['country_code']:'all';
     $chartId='cvct_pie_chart_'.$country_code;
     $allData=($country_code=="all")?cvct_get_global_data():cvct_country_stats_data($country_code);
     
     $country_name='';
     
      
     if(isset($allData)&& !empty($allData))
      {
        $act=($allData['total_cases']-$allData['total_recovered']-$allData['total_deaths']);
        $all_info=($country_code=="all")?cvct_get_global_data():$allData['allData'];
        $data_series[]=$allData['total_cases'];
        $data_series[]=$allData['total_recovered'];
        $data_series[]=($country_code=="all")?$all_info['critical']:$all_info->critical;
        $data_series[]=$allData['total_deaths'];
        $data_series[]=($country_code=="all")?$act:$all_info->active;
        $country_name=($country_code=="all")?"Global":$all_info->country;
        //echo $this->get_percentage($allData['total_cases'],$allData['total_cases']);
      
      }
      $title= !empty($atts['title'])?$atts['title']:$country_name.' Updates';
      $data_lbl[]= !empty($atts['label-confirmed'])?$atts['label-confirmed']:'Confirmed';
      $data_lbl[]= !empty($atts['label-recovered'])?$atts['label-recovered']:'Recovered';
      $data_lbl[]= !empty($atts['label-critical'])?$atts['label-critical']:'Critical';
      $data_lbl[]= !empty($atts['label-deaths'])?$atts['label-deaths']:'Death';
      $data_lbl[]= !empty($atts['label-active'])?$atts['label-active']:'Active';

     
      $chartData=json_encode(array("label"=>$data_lbl,"series"=>$data_series));    
         
      $output='<div class="cvct_pie_chart_wrp"><script type="application/json" id="'.$chartId.'-data">'.$chartData.'</script>';
      $output.='<div  data-title="'.$title.'" data-type="'.$country_code.'"  id="'.$chartId.'"></div>
     </div>';
     $output.='<style>
     #'.$chartId.' {
       width: 100%;
     height: 500px;
   } </style>';

     return $output;

}  


/*public function get_percentage($total, $number)
{
  if ( $total > 0 ) {
   return round($number / ($total / 100),2);
  } else {
    return 0;
  }
}
*/
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


}
