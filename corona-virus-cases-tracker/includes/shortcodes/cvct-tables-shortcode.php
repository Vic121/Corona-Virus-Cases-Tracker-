<?php
class CVCT_Table_Shortcode
{
function __construct() {
  require_once CVCT_DIR . 'includes/get_country_name.php';
  add_shortcode('cvct-tbl',array($this,'cvct_tbl_shortcode'));
}

    
/*
|--------------------------------------------------------------------------
| Corona Stats - table Shortcodes
|--------------------------------------------------------------------------
*/ 
public function cvct_tbl_shortcode($atts, $content = null ){
	$atts = shortcode_atts( array(
    	'id'				=> '',
		'layout'			=> 'layout-1',
		'show'				=> '10',
		'label-confirmed'	=> 'Confirmed',
		'label-deaths'		=> 'Death',
		'label-recovered'	=> 'Recovered',
		'label-active'		=> 'Active',
		'label-country'		=> 'Country',
		'bg-color'			=> '#222222',
		'font-color'		=> '#f9f9f9'
  	), $atts, 'cvct' );
	$style		= !empty($atts['layout'])?$atts['layout']:'layout-1';
	$country	= !empty($atts['label-country'])?$atts['label-country']:'Country';
	$confirmed	= !empty($atts['label-confirmed'])?$atts['label-confirmed']:'Confirmed';
	$deaths		= !empty($atts['label-deaths'])?$atts['label-deaths']:'Death';
	$recoverd	= !empty($atts['label-recovered'])?$atts['label-recovered']:'Recovered';
	$active		= !empty($atts['label-active'])?$atts['label-active']:'Active';
	$bgColors	= !empty($atts['bg-color'])?$atts['bg-color']:'#222222';
	$fontColors = !empty($atts['font-color'])?$atts['font-color']:'#f9f9f9';
	$show_entry = !empty($atts['show'])?$atts['show']:'10';
	$cvct_html	= '';
	$stack_arr	= array();
	$results	= array();

	$stats_data=cvct_get_global_data();
	if($stats_data==false){
		$stats_data=cvct_get_global_data_alternative();
	}
	if(is_array($stats_data) && count($stats_data)>0) {	
		$global_confirmed_sum = !empty($stats_data['total_cases'])?$stats_data['total_cases']:'0';
		$global_recovered_sum = !empty($stats_data['total_recovered'])?$stats_data['total_recovered']:'0';
		$global_death_sum = !empty($stats_data['total_deaths'])?$stats_data['total_deaths']:'0';
		$global_active_sum = $global_confirmed_sum-($global_recovered_sum+$global_death_sum);
		$global_recovered_sum_p = number_format(($global_recovered_sum/$global_confirmed_sum*100),1);
		$global_death_sum_p = number_format(($global_death_sum/$global_confirmed_sum*100),1);
		$global_active_sum_p = number_format(($global_active_sum/$global_confirmed_sum*100),1);
    }

	$last_updated=date("d M Y, G:i A",strtotime(get_option('cvct_gs_updated'))).' (GMT)';
	
	$cvct_get_data =  cvct_get_all_country_data();
	if(is_array($cvct_get_data)&& count($cvct_get_data)>0){
		$total_countries = 0;
		$confirmed_sum = 0;
		$recovered_sum = 0;
		$death_sum = 0;
		$active_sum = 0;
		$recovered_sum_p = 0;
		$death_sum_p = 0;
		$active_sum_p = 0;

		$country_name = array();
		$country_confirmed = array();
		$country_recovered = array();
		$country_death = array();
		$country_active = array();
		$country_recovered_p = array();
		$country_death_p = array();
		$country_active_p = array();

		$cun_confirmed = 0;
		$cun_recovered = 0;
		$cun_death = 0;
		$cun_active = 0;
		$cun_recovered_p = 0;
		$cun_death_p = 0;
		$cun_active_p = 0;
	
		foreach($cvct_get_data as $cvct_stats_data){
			$total_countries = $total_countries + 1;
			$cun_confirmed = $cvct_stats_data['confirmed'];
			$cun_recovered = $cvct_stats_data['recoverd'];
			$cun_death = $cvct_stats_data['deaths'];
			$cun_active = $cvct_stats_data['active'];
			if($cun_recovered > 0) {
				$cun_recovered_p = $cun_recovered/$cun_confirmed*100;
			} else { $cun_recovered_p=0; }
			if($cun_death > 0) {
				$cun_death_p = $cun_death/$cun_confirmed*100;
			} else { $cun_death_p=0; }
			if($cun_active > 0) {
				$cun_active_p = $cun_active/$cun_confirmed*100;
			} else { $cun_active_p=0; }
			
			$country_name[] = isset($cvct_stats_data['country'])?$cvct_stats_data['country']:'';
			$country_confirmed[] = !empty($cun_confirmed)?number_format($cun_confirmed):'0';
			$country_recovered[] = !empty($cun_recovered)?number_format($cun_recovered):'0';
			$country_death[] = !empty($cun_death)?number_format($cun_death):'0';
			$country_active[] = !empty($cun_active)?number_format($cun_active):'0';
			$country_recovered_p[] = !empty($cun_recovered_p)?number_format($cun_recovered_p,1):'0';
			$country_death_p[] = !empty($cun_death_p)?number_format($cun_death_p,1):'0';
			$country_active_p[] = !empty($cun_active_p)?number_format($cun_active_p,1):'0';

			$confirmed_sum = $confirmed_sum + $cun_confirmed;
			$recovered_sum = $recovered_sum + $cun_recovered;
			$death_sum = $death_sum + $cun_death;
			$active_sum = $active_sum + $cun_active;

		}
		$confirmed_sum = !empty($confirmed_sum)?$confirmed_sum:'0';
		$recovered_sum = !empty($recovered_sum)?$recovered_sum:'0';
		$death_sum = !empty($death_sum)?$death_sum:'0';
		$active_sum = !empty($active_sum)?$active_sum:'0';
		$recovered_sum_p = number_format(($recovered_sum/$confirmed_sum*100),1);
		$death_sum_p = number_format(($death_sum/$confirmed_sum*100),1);
		$active_sum_p = number_format(($active_sum/$confirmed_sum*100),1);
	}

	wp_enqueue_style('cvct_tables_css');
    wp_enqueue_script('cvct_resizer_sensor');
    wp_enqueue_script('cvct_resizer_queries');

	$total_rows = 0;
    switch($style){
	case 'layout-2':
		if(!is_admin()){
		wp_enqueue_style('cvct_data_tbl_css');
		wp_enqueue_script('cvct_jquery_dt');
		wp_enqueue_script('cvct_tabl_lay_script');
		}
		$cvct_html.= '
		<table id="cvct_table_id" class="table table-striped table-bordered cvct-table-2" data-pagination="'.$show_entry.'">
		<thead>
        <tr>
			<th class="cvct-th">'.__($country,'cvct').'</th>
			<th class="cvct-th">'.__($confirmed,'cvct').'</th>
			<th class="cvct-th">'.__($recoverd,'cvct').'</th>
			<th class="cvct-th">'.__($recoverd.' (%)','cvct').'</th>
			<th class="cvct-th">'.__($deaths,'cvct').'</th>
			<th class="cvct-th">'.__($deaths.' (%)','cvct').'</th>
			<th class="cvct-th">'.__($active,'cvct').'</th>
			<th class="cvct-th">'.__($active.' (%)','cvct').'</th>
        </tr>
		</thead>
		<tbody>
		';
		for($table_rows = 0; $table_rows < $total_countries; $table_rows++) {
		$cvct_html.= '
		<tr>	
			<td>'.$country_name[$table_rows].'</td>
			<td>'.$country_confirmed[$table_rows].'</td>
			<td>'.$country_recovered[$table_rows].'</td>
			<td>'.$country_recovered_p[$table_rows].'%</td>
			<td>'.$country_death[$table_rows].'</td>
			<td>'.$country_death_p[$table_rows].'%</td>
			<td>'.$country_active[$table_rows].'</td>
			<td>'.$country_active_p[$table_rows].'%</td>
		</tr>
		';
		}
		$cvct_html.= '
		</tbody>
		<tfoot>
		<tr>
			<th>'.__('Total','cvct').'</th>
			<th>'.number_format($global_confirmed_sum).'</th>
			<th>'.number_format($global_recovered_sum).'</th>
			<th>'.number_format($global_recovered_sum_p,1).'%</th>
			<th>'.number_format($global_death_sum).'</th>
			<th>'.number_format($global_death_sum_p,1).'%</th>
			<th>'.number_format($global_active_sum).'</th>
			<th>'.number_format($global_active_sum_p,1).'%</th>
		</tr>
		</tfoot>
		</table>
		';
	break;
	default:
		$img_url=CVCT_URL.'/assets/images/cvct-world.png';
		$cvct_html.= '
		<table class="cvct-table-1">
		<thead>
		<tr>
			<th>'.__($country,'cvct').'</th>
			<th>'.__($confirmed,'cvct').'</th>
			<th>'.__($recoverd,'cvct').'</th>
			<th>'.__($deaths,'cvct').'</th>	
		</tr>
		</thead>
		<tbody>
		';
		for($table_rows = 0; $table_rows < $show_entry; $table_rows++) {
			$c_name=cvct_interchange_name($country_name[$table_rows]);
			$title=get_country_info($c_name);
			if($title == '') { $title = $c_name; }
			$cvct_html.= '
			<tr>	
				<td>'.$title.'</td>
				<td>'.$country_confirmed[$table_rows].'</td>
				<td class="table-recovered">'.$country_recovered[$table_rows].'</td>
				<td class="table-death">'.$country_death[$table_rows].'</td>
			</tr>
			';
		}
		$cvct_html.= '
		</tbody>
		<tfoot>
		<tr>
			<th><img  width="20px" src="'.esc_url($img_url).'">'.__('Worldwide','cvct').'</th>
			<th>'.number_format($global_confirmed_sum).'</th>
			<th>'.number_format($global_recovered_sum).'</th>
			<th>'.number_format($global_death_sum).'</th>
		</tr>
		</tfoot>
		</table>
		<div class="table-update-time"><i>'.esc_html($last_updated).'</i></div>
		';
	break;
	}

	if($style=="layout-1") {
		if($bgColors == 'light' || $bgColors == '#fff' || $bgColors == '#ffffff'){
			$css="
			<style>
			". $this->cvct_load_table_styles($style)."
			#cvct-table-wrapper table.cvct-table-1 tr th,
			#cvct-table-wrapper table.cvct-table-1 tr td {background-color:#ffffff;color:".$fontColors.";}
			</style>
			";
		}
		elseif($bgColors == 'dark' || $bgColors == '#000' || $bgColors == '#000000'){
			$css="
			<style>
			". $this->cvct_load_table_styles($style)."
			#cvct-table-wrapper table.cvct-table-1 tr th,
			#cvct-table-wrapper table.cvct-table-1 tr td {background-color:#20202b;color:".$fontColors.";}
			</style>
			";
		}
		else{
			$css="
			<style>
			". $this->cvct_load_table_styles($style)."
			#cvct-table-wrapper table.cvct-table-1 tr th {background-color:".$bgColors.";color:".$fontColors.";}
			#cvct-table-wrapper table.cvct-table-1 tr td {background-color:".$fontColors.";color:".$bgColors.";}
			</style>
			";		
		}
	}
	else {
		if($bgColors == 'light' || $bgColors == '#fff' || $bgColors == '#ffffff'){
			$css="
			<style>
			". $this->cvct_load_table_styles($style)."
			#cvct-table-wrapper table.cvct-table-2 tr th,
			#cvct-table-wrapper table.cvct-table-2 tr td {background-color:#ffffff;color:".$fontColors.";}
			#cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.current {background:".$fontColors.";color:#ffffff !Important;}
			#cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button {background:#ffffff;color:".$fontColors." !Important;}
			#cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button:hover {background:".$fontColors.";color:#ffffff !Important;}
			</style>
			";
		}
		elseif($bgColors == 'dark' || $bgColors == '#000' || $bgColors == '#000000'){
			$css="
			<style>
			". $this->cvct_load_table_styles($style)."
			#cvct-table-wrapper table.cvct-table-2 tr th,
			#cvct-table-wrapper table.cvct-table-2 tr td {background-color:#000;color:".$fontColors.";}
			#cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.current {background:".$fontColors.";color:#20202b !Important;}
			#cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button {background:#20202b;color:".$fontColors." !Important;}
			#cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button:hover {background:".$fontColors.";color:#20202b !Important;}
			</style>
			";
		}
		else{
			$css="
			<style>
			". $this->cvct_load_table_styles($style)."
			#cvct-table-wrapper table.cvct-table-2 tr th {background-color:".$bgColors.";color:".$fontColors.";}
			#cvct-table-wrapper table.cvct-table-2 tr td {background-color:".$fontColors.";color:".$bgColors.";}
			#cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.current {background:".$bgColors.";color:".$fontColors." !Important;}
			#cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button {background:".$fontColors.";color:".$bgColors." !Important;}
			#cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button:hover {background:".$bgColors.";color:".$fontColors." !Important;}
			</style>
			";			
		}

	}
	
	$cvctv='<!-- Corona Virus Cases Tracker - Version:- '.CVCT_VERSION.' By Cool Plugins (CoolPlugins.net) -->';
	return $cvctv. '<div id="cvct-table-wrapper">' . $cvct_html . '</div>' .$css;
}


/*
|--------------------------------------------------------------------------
| loading required assets according to the widget type
|--------------------------------------------------------------------------
*/  
function cvct_load_table_styles($style){
  	$css = '';
	$css='
	#cvct-table-wrapper {
		width: 100%;
		display: block;
		overflow-x: auto;
		padding: 0;
		margin: 10px auto 16px;
	}
	/* width */
	#cvct-table-wrapper::-webkit-scrollbar {
		height: 10px;
		cursor: pointer;
	}
	/* Track */
	#cvct-table-wrapper::-webkit-scrollbar-track {
		background: #fff;
		border: 1px solid #ddd;
	}
	/* Handle */
	#cvct-table-wrapper::-webkit-scrollbar-thumb {
		background: #aaa;
	}
	/* Handle on hover */
	#cvct-table-wrapper::-webkit-scrollbar-thumb:hover {
		background: #ccc; 
	}
	#cvct-table-wrapper table {
		table-layout: fixed;
		border-collapse: collapse;
		border-radius: 5px;
		overflow: hidden;
		margin: 0;
    	padding: 0;
	}
	#cvct-table-wrapper table tr th,
	#cvct-table-wrapper table tr td {
		text-align: center;
		vertical-align: middle;
		font-size:14px;
		line-height:16px;
		text-transform:capitalize;
		border: 1px solid rgba(0, 0, 0, 0.15);
		width: 110px;
		padding: 12px 8px;
	}    
	';

	if($style=="layout-1") {
		$css.='
		#cvct-table-wrapper table.cvct-table-1 tr th:first-child,
        #cvct-table-wrapper table.cvct-table-1 tr td:first-child {
			text-align: left;
        }
		#cvct-table-wrapper table.cvct-table-1 tr th img,
		#cvct-table-wrapper table.cvct-table-1 tr td img {
			margin: 0 4px 2px 0;
			padding: 0;
			vertical-align: middle;
		}
		#cvct-table-wrapper table.cvct-table-1 tr td.table-recovered {
			color: #13af11;
			font-weight: bold;
		}
		#cvct-table-wrapper table.cvct-table-1 tr td.table-death {
			color: #da1313;
			font-weight: bold;
		}
		#cvct-table-wrapper .table-update-time {
            display: inline-block;
            width: 100%;
            text-align: right;
        }
		#cvct-table-wrapper i {
			display: inline-block;
			margin: 0;
			padding: 5px;
			font-size: 12.5px;
			line-height: 1.3em;
			font-style: italic;
		}
		';
	}
	else {
		$css.='
		#cvct-table-wrapper {
			padding-bottom: 6px;
		}
		#cvct-table-wrapper .dataTables_wrapper input,
		#cvct-table-wrapper .dataTables_wrapper select {
			display: inline-block !IMPORTANT;
			margin: 0 2px !IMPORTANT;
			width: auto !IMPORTANT;
			min-width: 60px;
			padding: 8px;
			min-height: 44px;
			box-sizing: border-box;
			vertical-align: middle;
		}
		#cvct-table-wrapper .dataTables_wrapper label {
			margin-bottom: 12px;
			display: inline-block;
			vertical-align: middle;
		}
        #cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button {
			padding: 0.3em 0.8em;
			border-color: transparent;
        }
        #cvct-table-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
			border-color: transparent;
		}
		';
	}
  	return $css;
}

}


