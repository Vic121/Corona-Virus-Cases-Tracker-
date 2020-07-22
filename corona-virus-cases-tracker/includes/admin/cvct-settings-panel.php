<?php
function cvct_countries_array(){
    $k =cvct_get_all_country_data();
    $c_array=[];
    $c_array["World"]="World";
    if(is_array($k) && !empty($k)){
        foreach($k as $value){
            $country = $value['country'];
           $c_array[$country]=$value['country'];
        }
    }
    return $c_array;
}
$cvct_preview_cmb2 = new_cmb2_box( array(
    'id'            => 'cvct_live_preview',
    'title'         => __( 'Corona Tracker Live Preview', 'cmb2' ),
    'object_types'  => array( 'cvct'), // Post type
    'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true, // Show field names on the left
    // 'cmb_styles' => false, // false to disable the CMB stylesheet
    // 'closed'     => true, // Keep the metabox closed by default
) );
$cvct_preview_cmb2->add_field( array(
    'name' => '',
    'desc' =>cvct_display_live_preview(),
    'type' => 'title',
    'id'   => 'cvct_live_preview'
) );
   $cvct_cmb2 = new_cmb2_box( array(
            'id'            => 'cvct_general_settings',
            'title'         => __( 'Corona Virus Cases Tracker', 'cmb2' ),
            'object_types'  => array( 'cvct'), // Post type OR option-page
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true, // Show field names on the left
            'horizontal_tabs' => true, // Set vertical tabs, default false            
            
    ) );

    

    $cvct_cmb2->add_field(array(
        'id' => 'cvct_select_countries',
        'name' => __('Select Countries', 'cvct2'),
        'desc' => __('Select Countries', 'cvct2'),
        'type' => 'pw_multiselect',
        'options' => cvct_countries_array(),
        'attributes' => array(
            'required' =>true,   
        )
    ));
   
    $cvct_cmb2->add_field(array(
        'id' => 'cvct_style',
        'name' => __('Style', 'cvct2'),
        'desc' => __('Select Style', 'cvct2'),
        'type' => 'select',
        'default' => 'style-1',
        'options' => array(
            'style-1' => 'Style 1',
            'style-2' => 'Style 2',
            'style-3' => 'Style 3'
        ),
    ));

    $cvct_cmb2->add_field(array(
        'id' => 'cvct_ticker_position',
        'name' => __('Ticker Position', 'cvct2'),
        'desc' => '</br>'.__('Where do you want to show ticker (No need to add shortcode on pages for Header/Footer)', 'cvct2'),
        'type' => 'radio',
        'default' => 'anywhere',
        /* 'attributes' => array(
            'data-conditional-id' => 'cvct_style',
            'data-conditional-value' => json_encode(array('style-1','style-2')),
        ),  */
        'options' => array(
            'header' => 'Header',
            'footer' => 'footer',
            'anywhere' => 'Anywhere'
        ),
    ));
    
    $cvct_cmb2->add_field(array(
        'name' => 'Heading for Ticker',
        'desc' => 'Default is "Live Updates COVID-19 CASES"',
        'id' => 'cvct_ticker_heading',
        'type' => 'text',
        'default' => ''               
    ));

    $cvct_cmb2->add_field(array(
        'name' => 'Disable ticker from page/post',
        'desc' => 'Enter page/post id from where you want to disable ticker',
        'id' => 'cvct_ticker_disable',
        'type' => 'text',
        'default' => '',
        'attributes' => array(
            'data-conditional-id' => 'cvct_ticker_position',
            'data-conditional-value' => json_encode(array('footer','header')),
        ),              
    ));

    $cvct_cmb2->add_field(array(
        'name' => 'Confirmed Cases Label',
        'desc' => 'Add text for Confirmed Cases (default is "Confirmed")',
        'id' => 'cvct_confirmed_label',
        'type' => 'text'
    ));

    $cvct_cmb2->add_field(array(
        'name' => 'Active Cases Label',
        'desc' => 'Add text for Active Cases (default is "Active")',
        'id' => 'cvct_active_label',
        'type' => 'text'
    ));

    $cvct_cmb2->add_field(array(
        'name' => 'Recovered Cases Label',
        'desc' => 'Add text for Recovered Cases (default is "Recovered")',
        'id' => 'cvct_recovered_label',
        'type' => 'text'
    ));

    $cvct_cmb2->add_field(array(
        'name' => 'Death Cases Label',
        'desc' => 'Add text for Death Cases (default is "Death")',
        'id' => 'cvct_death_label',
        'type' => 'text'           
    ));

    $cvct_cmb2->add_field(array(
        'name' => 'Speed of Ticker',
        'desc' => 'Low value = high speed. (Best between 10 - 60) e.g 10*1000 = 10000 miliseconds (for Ticker style-1)',
        'id' => 'cvct_ticker_speed',
        'type' => 'text',
        'default' => '35',
        'attributes' => array(
            'data-conditional-id' => 'cvct_style',
            'data-conditional-value' => json_encode(array('style-1')),
        ),              
    ));

    $cvct_cmb2->add_field(array(
        'id' => 'cvct_bg_color',
        'name' => __('Background Color', 'cvct2'),
        'desc' => __('Background Color', 'cvct2'),
        'type' => 'colorpicker',
        'default' => ''
    ));
    $cvct_cmb2->add_field(array(
        'id' => 'cvct_text_color',
        'name' => __('Text Color', 'cvct2'),
        'desc' => __('Text Color', 'cvct2'),
        'type' => 'colorpicker',
        'default' => ''
    ));
    $cvct_cmb2->add_field(array(
        'id' => 'cvct_text_bg_color',
        'name' => __('Ticker Heading Background Color', 'cvct2'),
        'desc' => __('Background Color', 'cvct2'),
        'type' => 'colorpicker',
        'default' => ''
    ));
    /*
    $cvct_cmb2->add_field(array(
        'id' => 'cvct_font_size',
        'name' => __('Title font Size', 'cvct2'),
        'desc' => __('Title font Size', 'cvct2'),
        //'placeholder' => __('Text Input placeholder', 'cvct2'),
        'type' => 'text',
        'default' => '24px'
    ));
    */
    $cvct_cmb2->add_field(array(
        'id' => 'cvct_ticker_desc',
        'name' => __('Ticker Description', 'cvct2'),
        'desc' => __('Add Some Description to show on Ticker Style 2', 'cvct2'),
        'type' => 'textarea',
        'default' => '',
        'attributes' => array(
            'data-conditional-id' => 'cvct_style',
            'data-conditional-value' => json_encode(array('style-2')),
        ),
    ));
    
    $cvct_cmb2->add_field( array(
        'name'    => 'Custom CSS',
        'desc'    => 'Enter custom CSS',
        'id'      => 'cvct_custom_css',
        'type'    => 'textarea'
    ) );
        
    function cvct_display_live_preview(){
        $output='';
        if( isset($_REQUEST['post']) && !is_array($_REQUEST['post'])){
          $id = $_REQUEST['post'];
          $type = 'ticker';
          $output='<p><strong class="micon-info-circled"></strong>Backend preview may be a little bit different from frontend / actual view. Add this shortcode on any page for frontend view - <code>[cvct-advance id='.$id.']</code></p>'.do_shortcode("[cvct-advance id='".$id."']");
         
          $output.='<script type="text/javascript">
            jQuery(document).ready(function($){
                $(".cvct-ticker-cont").fadeIn();     
            });
            </script>
            <style type="text/css">
            .cvct-footer-ticker-fixedbar, .cvct-header-ticker-fixedbar{
                position:relative!important;
            }
          </style>';
          return $output;
         
           }else{
          return  $output='<h4><strong class="micon-info-circled"></strong> Publish to preview the widget.</h4>';
      
           }
      }