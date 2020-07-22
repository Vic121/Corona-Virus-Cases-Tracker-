<?php
class CVCT_SPREAD_TREND_Shortcode
{
function __construct() {
    add_shortcode( 'cvct-spread-trend', array($this, 'CVCT_SPREAD_TREND_Shortcode' ));
}
function CVCT_SPREAD_TREND_Shortcode($atts, $content = null){
    $atts = shortcode_atts( array(
         'style'=>'style-1',
         'country-code'=>'IT,US',
         'title'=>'Compare by Region',
         'days-data'=>'30',
         'show-data'=>'cases',
         'width'=>'100',
         'height'=>'350',
         'bg-color'=>'#ddd',
         'font-color'=>'#000000',
         'theme'=>'light'
    ), $atts, 'cvct-spread-trend' );
    $keys_arr = array();
    $output = '';
    wp_enqueue_script('cvct_apexcharts');
    wp_enqueue_script('cvct_historical_charts_js');
    $title = !empty($atts['title'])?$atts['title']:'Compare by Region';
    $days_data = isset($atts['days-data'])?$atts['days-data']:'30';
    $country = !empty($atts['country-code'])?$atts['country-code']:'IT,US';
    $show_data = isset($atts['show-data'])?$atts['show-data']:'cases';
    $bg_color = isset($atts['bg-color'])?$atts['bg-color']:'#ddd';
    $font_color = isset($atts['font-color'])?$atts['font-color']:'#000000';
    $theme = isset($atts['theme'])?$atts['theme']:'light';
    $width = isset($atts['width'])?$atts['width']:'100';
    $height = isset($atts['height'])?$atts['height']:'100';
    $get_data = cvct_get_multiple_country_hist_data($country,$days_data);
    
    $series_data=[];
    $i=0;
    if(isset($get_data) && is_array($get_data))
    {
        foreach($get_data as $type=>$value_arr)
        {
            $name = $value_arr['country'];
            $series_data[$i]['name']=ucfirst($name);
            $value_arr=(array)$value_arr['timeline'];
            $show_case = (array)$value_arr[$show_data];

            $indexObj=array_map(function ($date, $value) {
                $date_form = date_create($date);
               $date_format = date_format($date_form,"d F");
              return array(
                    'x' => $date_format,
                    'y'  =>$value
                );
            }, array_keys($show_case), $show_case);
      
        $final_arr=array_splice($indexObj,-$days_data,$days_data);
        $series_data[$i]['data']=$final_arr;
        $i++;
        }
    }
        $jsonData= json_encode($series_data);
        $result = preg_replace('/[ ,]+/', '_', trim($country));
        $show_query = preg_replace('/[ ,]+/', '_', trim($show_data));
        $id='cvct_historical_chart_'.$result.'_'.$show_query;
        $output .='<div class="historical_chart_wrp">
        <div data-days="'.$days_data.'"  id="'.$id.'" data-theme="'.$theme.'" data-title="'.$title.'" data-height="'.$height.'" data-width="'.$width.'" data-fontcolor="'.$font_color.'"></div>
        <script type="application/json" id="'.$id.'-data">'.$jsonData.'</script>';
        $output .='</div>';
    return $output;
}

}