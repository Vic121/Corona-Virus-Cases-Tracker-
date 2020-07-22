<?php

if( !class_exists( 'CVCTPostType' ) ) 
{
	class CVCTPostType 
	{
		
		/* Initializes the plugin functions*/
		function __construct()  
		{
			
			$this-> cvct_posttype_includes();
			add_action( 'add_meta_boxes',array($this,'register_cvct_meta_box'));
			add_action('init', array($this,'cvct_register_post_type'));
			add_action( 'cmb2_admin_init', array( $this,'cmb2_cvct_metaboxes' ));      
			add_action( 'save_post', array( $this,'save_cvct_shortcode'),50, 3 );		
			add_action( 'cmb2_admin_init',  array( $this,'cvct_other_shortcodes_page' ));
			add_filter( 'manage_cvct_posts_columns',array($this,'set_custom_edit_cvct_columns'));
			add_action( 'manage_cvct_posts_custom_column' ,array($this,'custom_cvct_column'), 10, 2 );
			add_action( 'add_meta_boxes_cvct',array($this,'cvct_add_meta_boxes'));	
			add_action( 'admin_enqueue_scripts', array( $this, 'cvct_admin_enqueue_scripts') );

		}

		function cvct_admin_enqueue_scripts(){
			if(cvct_get_post_type_page()=='cvct'){
				wp_register_style("cvct_admin_styles", CVCT_URL.'assets/css/cvct-admin-style.css');    
				wp_enqueue_style('cvct_admin_styles');
        	}			
		}

		function cvct_other_shortcodes_page() {
			
			/**
			 * Registers options page menu item and form.
			 */
			$cmb_options = new_cmb2_box( array(
				'id'           => 'cvct_shortcodes_page',
				'title'        => esc_html__( 'Shortcodes', 'cvct1' ),
				'object_types' => array( 'options-page' ),
				'option_key'      => 'cvct_shortcodes', // The option key and admin menu page slug.
				'menu_title'      => esc_html__( 'Other Shortcodes', 'cvct1' ), // Falls back to 'title' (above).
				'parent_slug'     => 'edit.php?post_type=cvct', // Make options page a submenu item of the themes menu.
				'capability'      => 'manage_options', // Cap required to view options-page.
			) );
			$cmb_options->add_field( array(
				'name' =>'<b style="color:red;">'.__( 'Other Shortcodes', 'cvct1' ).'</b>',
		
				'id'   => 'cvct_other_shortcodes_documentation',
				'type' => 'title',
				) );
			$cmb_options->add_field( array(
				'name' => __( 'Card Layout Shortcode', 'cvct1' ),
				'id'   => 'cvct_card_shortcode',
				'type' => 'textarea_small',
				'default' => '[cvct country-code="all" style="style-1" title="Global Stats" label-total="Total Cases" label-deaths="Deaths" label-active="Active Cases"  label-recovered="Recovered" bg-color="#DDDDDD" font-color="#000"] ',
				'attributes'  => array(
							'readonly' => 'readonly',
					),
			) );
			$cmb_options->add_field( array(
				'name' => __( 'World Map Shortcode', 'cvct1' ),
				'id'   => 'cvct_map_shortcode',
				'type' => 'textarea_small',
				'default' => '[cvct-maps title="Global stats" show-list="yes"layout="style-2"]',
				'attributes'  => array(
							'readonly' => 'readonly',
					),
			) );
			$cmb_options->add_field( array(
				'name' => __( 'Table Layout Shortcode', 'cvct1' ),
				'id'   => 'cvct_table_shortcode',
				'type' => 'text',
				'default' => '[cvct-tbl layout="layout-1" show="10"]',
				'attributes'  => array(
							'readonly' => 'readonly',
					),
			) );
			$cmb_options->add_field( array(
				'name' => __( 'Chart Layout Shortcode', 'cvct1' ),
				'id'   => 'cvct_chart_shortcode',
				'type' => 'textarea_small',
				'default' => '[cvct-charts title="COVID-19 Total Cases Stats" width="700" height="450"]',
				'attributes'  => array(
							'readonly' => 'readonly',
					),
			) );
			$cmb_options->add_field( array(
				'name' => __( 'US States Shortcode', 'cvct1' ),
				'id'   => 'cvct_us_states_shortcode',
				'type' => 'textarea_small',
				'default' => '[cvct-country-stats layout="style-1" title="US COVID-19 Spread Map"]',
				'attributes'  => array(
							'readonly' => 'readonly',
						//'disabled' => 'disabled',
					),
			) );
			$cmb_options->add_field( array(
				'name' => __( 'Simple Text Shortcode', 'cvct1' ),
				'id'   => 'cvct_simple_text_shortcode',
				'type' => 'text',
				'default' => '[cvct-text field="confirmed"]',
				'attributes'  => array(
							'readonly' => 'readonly',
					),
			) );
			$cmb_options->add_field( array(
				'name' => __( 'Plugin Documentation', 'cvct1' ),
				'id'   => 'cvct_documentation_link',
				'type' => 'textarea_small',
				'desc' => 'Follow this link to know more about shortcodes.',
				'default' => 'https://docs.coolplugins.net/doc/corona-virus-cases-tracker/',
				'attributes'  => array(
							'readonly' => 'readonly',
					),
			) );
			
		
		}		
		
		

		/**
		 * Save shortcode when a post is saved.
		 *
		 * @param int $post_id The post ID.
		 * @param post $post The post object.
		 * @param bool $update Whether this is an existing post being updated or not.
		 */
		function save_cvct_shortcode( $post_id, $post, $update ) {
			// Autosave, do nothing
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
					return;
			// AJAX? Not used here
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
					return;
			// Check user permissions
			if ( ! current_user_can( 'edit_post', $post_id ) )
					return;
			// Return if it's a post revision
			if ( false !== wp_is_post_revision( $post_id ) )
					return;
						
			
			$post_type = get_post_type($post_id);
			
			// If this isn't a 'cvct' post, don't update it.
			if ( "cvct" != $post_type ) return;
				// - Update the post's metadata.
			$_POST['type']="ticker";
			if(isset($_POST['type']) && $_POST['type']=="ticker"){
				
				if(isset($_POST['cvct_ticker_position'])&& in_array($_POST['cvct_ticker_position'],array('header','footer'))){
				if($_POST['cvct_ticker_position'] =='footer'){
					update_option('cvct-fp-id',$post_id);
				}
				else{
					update_option('cvct-p-id',$post_id);
				}
					
				}
			}
			
        }


		/*
		|--------------------------------------------------------------------------
		| Register  meta boxes for shortcode
		|--------------------------------------------------------------------------
		*/ 
		function register_cvct_meta_box()
		{
			add_meta_box('cvct-shortcode', 'Corona Virus Cases Tracker', array($this,'cvct_shortcode_meta'), 'cvct', 'side', 'high');
		
		}

		/*
		Plugin Shortcode meta section
		*/
		function cvct_shortcode_meta()
		{
			$id = get_the_ID();
			$dynamic_attr = '';
			_e(' <p>Paste this shortcode anywhere in Page/Post.</p>', 'cvctx');

			$dynamic_attr .= "[cvct-advance id=\"{$id}\"";
			$dynamic_attr .= ']';
			?>
				<input style="width:100%" onClick="this.select();" type="text" class="regular-small" name="my_meta_box_text" id="my_meta_box_text" value="<?php echo htmlentities($dynamic_attr); ?>" readonly/>
			
				<?php
		}
		
		function cvct_register_post_type()
		{
			$labels = array(
				'name'                  => _x( 'Corona Virus Cases Tracker', 'Post Type General Name'),
				'singular_name'         => _x( 'Corona Virus Cases Tracker', 'Post Type Singular Name'),
				'menu_name'             => __( 'Corona Tracker'),
				'name_admin_bar'        => __( 'Corona Virus Cases Tracker'),
				'archives'              => __( 'Item Archives'),
				'attributes'            => __( 'Item Attributes'),
				'parent_item_colon'     => __( 'Parent Item:'),
				'all_items'             => __( 'All Ticker Shortcodes'),
				'add_new_item'          => __( 'Add New Shortcode'),
				'add_new'               => __( 'Add New'),
				'new_item'              => __( 'New Item'),
				'edit_item'             => __( 'Edit Item'),
				'update_item'           => __( 'Update Item'),
				'view_item'             => __( 'View Item'),
				'view_items'            => __( 'View Items'),
				'search_items'          => __( 'Search Item'),
				'not_found'             => __( 'Not found'),
				'not_found_in_trash'    => __( 'Not found in Trash'),
				'featured_image'        => __( 'Featured Image'),
				'set_featured_image'    => __( 'Set featured image'),
				'remove_featured_image' => __( 'Remove featured image'),
				'use_featured_image'    => __( 'Use as featured image'),
				'insert_into_item'      => __( 'Insert into item'),
				'uploaded_to_this_item' => __( 'Uploaded to this item'),
				'items_list'            => __( 'Items list'),
				'items_list_navigation' => __( 'Items list navigation'),
				'filter_items_list'     => __( 'Filter items list'),
				);
				$args = array(
					'label'                 => __('Corona Virus Cases Tracker', 'tecspb2'),
					'description'           => __('Post Type Description', 'tecspb2'),
					'labels'                => $labels,
					'supports' => array('title'),
					'taxonomies' => array(''),
					'hierarchical' => false,
					'public' => false, // it's not public, it shouldn't have it's own permalink, and so on
					'show_ui' => true,
					'show_in_nav_menus' => false, // you shouldn't be able to add it to menus
					'menu_position' => 5,
					'show_in_admin_bar' => true,
					'show_in_nav_menus' => true,
					'can_export' => true,
					'has_archive' => false, // it shouldn't have archive page
					'rewrite' => false, // it shouldn't have rewrite rules
					'exclude_from_search' => true,
					'publicly_queryable' => true,
					'capability_type' => 'page',
				);			
			
			register_post_type('cvct',$args);
		}

		public function cvct_posttype_includes(){
            
            if(is_admin() && cvct_get_post_type_page()=="cvct"){
                if ( file_exists( CVCT_DIR . 'includes/admin/cmb2/init.php' ) ) {
					require_once CVCT_DIR . 'includes/admin/cmb2/init.php';
					//require_once CVCT_DIR . 'includes/admin/cmb2/cmb2-tabs/cmb2-tabs.php'; 
					require_once CVCT_DIR . 'includes/admin/cmb2/cmb2-conditionals.php';
					require_once CVCT_DIR . 'includes/admin/cmb2/cmb-field-select2/cmb-field-select2.php';
                    //require_once CVCT_DIR . 'includes/CMB2/cmb2-fontawesome-picker.php';
                }
							
			}
            
		}

		/**
		 * Define the metabox and field configurations.
		 */
		function cmb2_cvct_metaboxes() {

			// Start with an underscore to hide fields from custom fields list
			$prefix = 'cvct_';
			
			require_once CVCT_DIR . '/includes/admin/cvct-settings-panel.php';
			
		}

		 /*
		|--------------------------------------------------------------------------
		| Handle All Widget Columns
		|--------------------------------------------------------------------------
		*/   
		function set_custom_edit_cvct_columns($columns) {
			$columns['cb']            =  '<input type="checkbox" />';
			$columns['style'] = __( 'Ticker Style', 'cvct2' );
			$columns['shortcode'] = __( 'Shortcode', 'cvct2' );
			$columns['date']    =  _x('Sort By Date', 'column name');
			return $columns;
		}

		/**
		 * cvct custom post type all shortcode types
		*/
		function custom_cvct_column( $column, $post_id ) {
			switch ( $column ) {
				case 'style' :
					$style=get_post_meta( $post_id , 'cvct_style' , true );
				switch ( $style ){
					case "style-1":
						_e('Style 1','cvct2');
					break;
					case "style-2":
						_e('Stye 2','cvct2');
					break;
					case "style-3":
						_e('Stye 3','cvct2');
					break;	
				}
					break;
				case 'shortcode' :
					echo '<code>[cvct-advance id="'.$post_id.'"]</code>';
				break;	
			}
		}    

		
		/*
		|--------------------------------------------------------------------------
		| Register meta boxes for Feedback
		|--------------------------------------------------------------------------
		*/
		function cvct_add_meta_boxes( $post){
			add_meta_box(
				'cvct-feedback-section',
				__( 'STAY HOME! STAY SAFE!','cvct2'),
				array($this,'cvct_right_section'),
				'cvct',
				'side',
				'low'
			);
		}

		/**
		*  admin notice for plugin review callback
		*/
		function cvct_right_section($post, $callback){
		global $post;
		$pro_add='';
		$pro_add .='
		We hope our plugin is helping your website visitors to view current worldwide covid-19 cases status.<br/>'.
		'Please give us a rating on codecanyon, it works as a boost for us to work continuously on this plugin in this hard time'.
		'
		<br/>';
		$pro_add .='<a target="_blank" href="https://codecanyon.net/item/corona-virus-cases-tracker-covid19-tracker/reviews/26055464">
		<img src="https://res.cloudinary.com/cooltimeline/image/upload/v1504097450/stars5_gtc1rg.png">
		</a>
		<div><a href="https://codecanyon.net/item/corona-virus-cases-tracker-covid19-tracker/reviews/26055464" class="button button-secondary" target="_blank">
		Submit Review</a>
		&nbsp;
		<a href="https://covid19.coolplugins.net/" class="button button-primary" target="_blank">
		View Demos</a> 
		</div>
		';
		echo $pro_add ;

		}



    }


}