<?php
class CVCT_HISTORICAL_Shortcode
{
function __construct() {
    add_shortcode( 'cvct-historical-charts', array($this, 'cvct_historical_shortcode' ));
}
function cvct_historical_shortcode($atts, $content = null){
    $atts = shortcode_atts( array(
         'style'=>'style-1',
         'country-code'=>'all',
         'title'=>'Global Cases',
         'label-cases'	=> 'Cases',
		'label-deaths'		=> 'Death',
		'label-recovered'	=> 'Recovered',
         'days-data'=>'30',
         'width'=>'100',
         'height'=>'350',
         'bg-color'=>'#ddd',
         'font-color'=>'#000000',
         'theme'=>'light'
    ), $atts, 'cvct-historical-charts' );
    $keys_arr = array();
    $output = '';
    wp_enqueue_script('cvct_apexcharts');
    wp_enqueue_script('cvct_historical_charts_js');
    $title = !empty($atts['title'])?$atts['title']:'Spread over time';
    $days_data = isset($atts['days-data'])?$atts['days-data']:'30';
    $country = isset($atts['country-code'])?$atts['country-code']:'all';
    $show_data = isset($atts['show-data'])?$atts['show-data']:'confirmed';
    $bg_color = isset($atts['bg-color'])?$atts['bg-color']:'#ddd';
    $font_color = isset($atts['font-color'])?$atts['font-color']:'#000000';
    $theme = isset($atts['theme'])?$atts['theme']:'light';
    $width = isset($atts['width'])?$atts['width']:'100';
    $height = isset($atts['height'])?$atts['height']:'100';
    $get_data = cvct_get_historical_data($country,$days_data);
    $confirmed	= !empty($atts['label-cases'])?$atts['label-cases']:'Cases';
	$deaths		= !empty($atts['label-deaths'])?$atts['label-deaths']:'Death';
	$recoverd	= !empty($atts['label-recovered'])?$atts['label-recovered']:'Recovered';
    $series_data=[];
    $i=0;
    if(isset($get_data) && is_array($get_data))
    {
        foreach($get_data as $type=>$value_arr)
        {
            switch($type){
                case 'recovered':
                    $type = $recoverd;
                break;
                case 'deaths':
                    $type = $deaths;
                break;
                default:
                $type= $confirmed;
            break;
            return $type;
            }
            $value_arr=(array)$value_arr;
            $series_data[$i]['name']=ucfirst($type);
                $indexObj=array_map(function ($date, $value) {
                    // var_dump($date);
                    $date_form = date_create($date);
                   $date_format = date_format($date_form,"d F");
                  return array(
                        'x' => $date_format,
                        'y'  =>$value
                    );
                }, array_keys($value_arr), $value_arr);
          
            $final_arr=array_splice($indexObj,-$days_data,$days_data);
            $series_data[$i]['data']=$final_arr;
            $i++;
        }
    }
        $jsonData= json_encode($series_data);
        $id='cvct_historical_chart_'.$country;
        $output .='<div class="historical_chart_wrp">
        <div data-days="'.$days_data.'"  id="'.$id.'" data-theme="'.$theme.'" data-title="'.$title.'" data-height="'.$height.'" data-width="'.$width.'" data-fontcolor="'.$font_color.'"></div>
        <script type="application/json" id="'.$id.'-data">'.$jsonData.'</script>';
        $output .='</div>';
    return $output;
}

}