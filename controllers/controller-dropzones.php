<?php 

class WidgetPress_Controller_Dropzones {

	//pubic

	/**
	 * 
	 */
	public function init(){

		do_action('widgetpress_init');

		//add_action( 'save_post',		array(__CLASS__, 'publish_layout') );
		add_action( 'created_layout',	array(__CLASS__, 'insert_layout_post'));
		add_action('init', 				array(__CLASS__, 'register'));

	}


	public function insert_layout_post($term_id, $tax_id = null){

		$layout_term = get_term($term_id, 'layout');

		$layout_id = wp_insert_post(array(
			'post_type' => 'layout',
			'post_title' => 'Layout: '.$layout_term->name,
			'post_name'	=> $layout_term->slug,
			'post_status' => 'publish',
			'tax_input'		=> array( 'layout' , $term_id )
		));
	}


	/**
	 * 
	 */
	public function register(){
		self::register_layout_taxonomy();
		self::register_dropzone_taxonomy();
		self::register_layout_post_type();
		self::register_dropzone_post_type();
	}


	/**
	 * 
	 */
	private function register_layout_taxonomy(){

		//Labels for layout taxonomy
		$labels = apply_filters('widgetpress_layout_taxonomy_labels', array(
			'name'					=> _x( 'Layouts', 'taxonomy general name' ),
			'singular_name' 		=> _x( 'Layout', 'taxonomy singular name' ),
			'search_items' 			=> __( 'Search Layouts' ),
			'all_items' 			=> __( 'All Layouts' ),
			'parent_item' 			=> __( 'Parent Layout' ),
			'parent_item_colon'		=> __( 'Parent Layout:' ),
			'edit_item' 			=> __( 'Edit Layout' ), 
			'update_item' 			=> __( 'Update Layout' ),
			'add_new_item'			=> __( 'Add New Layout' ),
			'new_item_name' 		=> __( 'New Layout Name' ),
			'menu_name' 			=> __( 'Layout' ),
		)); 	

		//Complete arguments for layout taxonomy
		$args = apply_filters('widgetpress_layout_taxonomy_args', array(
			'hierarchical' 			=> true,
			'labels' 				=> $labels,
			'show_ui' 				=> true,
			'query_var' 			=> false,
			'rewrite' 				=> false
		));

		$post_types = apply_filters('widgetpress_layout_taxonomy_post_types', array(
			'page',
			'section',
			'layout', 
			'dropzone',
		));

		register_taxonomy('layout',$post_types, $args);
	}

	/**
	 * 
	 */
	private function register_dropzone_taxonomy(){

		//Labels for layout taxonomy
		$labels = apply_filters('widgetpress_dropzone_taxonomy_labels', array(
			'name'					=> _x( 'Dropzones', 'taxonomy general name' ),
			'singular_name' 		=> _x( 'Dropzone', 'taxonomy singular name' ),
			'search_items' 			=> __( 'Search Dropzons' ),
			'all_items' 			=> __( 'All Drozones' ),
			'parent_item' 			=> __( 'Parent Dropzone' ),
			'parent_item_colon'		=> __( 'Parent Dropzone:' ),
			'edit_item' 			=> __( 'Edit Dropzone' ), 
			'update_item' 			=> __( 'Update Dropzone' ),
			'add_new_item'			=> __( 'Add New Dropone' ),
			'new_item_name' 		=> __( 'New Dropzone Name' ),
			'menu_name' 			=> __( 'Dropzone' ),
		)); 	

		//Complete arguments for layout taxonomy
		$args = apply_filters('widgetpress_layout_taxonomy_args', array(
			'hierarchical' 			=> true,
			'labels' 				=> $labels,
			'show_ui' 				=> true,
			'query_var' 			=> false,
			'rewrite' 				=> false
		));

		$post_types = apply_filters('widgetpress_layout_taxonomy_post_types', array(
			'page',
			'section',
			'layout', 
			'dropzone',
			'widget'
		));

		register_taxonomy('dropzone',$post_types, $args);
	}


	/**
	 * 
	 */
	private function register_layout_post_type(){

		$labels = apply_filters('widgetpress_layout_post_type_lables', array(
			'name' 					=> _x('Layout', 'post type general name'),
			'singular_name' 		=> _x('Layout', 'post type singular name'),
			'add_new' 				=> _x('Create New', 'book'),
			'add_new_item' 			=> __('Create New Layout'),
			'edit_item' 			=> __('Edit Layout'),
			'new_item' 				=> __('New Layout'),
			'all_items' 			=> __('All Layouts'),
			'view_item' 			=> __('View Layout'),
			'search_items' 			=> __('Search Layouts'),
			'not_found' 			=> __('No layouts found'),
			'not_found_in_trash' 	=> __('No layouts found in Trash'), 
			'parent_item_colon' 	=> '',
			'menu_name' 			=> __('Layouts')
		));


		$args = apply_filters('widgetpress_layout_post_type_args', array(
			'label'					=> 'layouts',
			'labels'				=> $labels,
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'show_ui' 				=> true, 
			'show_in_menu' 			=> true, 
			'query_var' 			=> false,
			'rewrite' 				=> false,
			'capability_type' 		=> 'post',
			'has_archive' 			=> true, 
			'hierarchical' 			=> false,
			'menu_position' 		=> null,
			'supports' 				=> array( 'title', 'custom-fields' )
		)); 

		register_post_type('layout', $args);
	}

	/**
	 * 
	 */
	private function register_dropzone_post_type(){

		$labels = apply_filters('widgetpress_dropzone_post_type_lables', array(
			'name' 					=> _x('Dropzones', 'post type general name'),
			'singular_name' 		=> _x('Dropzone', 'post type singular name'),
			'add_new' 				=> _x('Create New', 'book'),
			'add_new_item' 			=> __('Create New Dropzone'),
			'edit_item' 			=> __('Edit Dropzone'),
			'new_item' 				=> __('New Dropzone'),
			'all_items' 			=> __('All Dropzones'),
			'view_item' 			=> __('View Dropzone'),
			'search_items' 			=> __('Search Dropzones'),
			'not_found' 			=> __('No dropzones found'),
			'not_found_in_trash' 	=> __('No dropzones found in Trash'), 
			'parent_item_colon' 	=> '',
			'menu_name' 			=> __('Dropzones')
		));


		$args = apply_filters('widgetpress_dropzone_post_type_args', array(
			'label'					=> 'dropzones',
			'labels' 				=> $labels,
			'public' 				=> true,
			'publicly_queryable' 	=> false,
			'show_ui' 				=> true, 
			'show_in_menu' 			=> true, 
			'query_var' 			=> false,
			'rewrite' 				=> false,
			'capability_type' 		=> 'post',
			'has_archive' 			=> false, 
			'hierarchical' 			=> false,
			'menu_position'			=> null,
			'supports' => array( 'title', 'custom-fields' )
		)); 

		register_post_type('dropzone', $args);
	}




	public function set_post_dropzone(){

	}


}