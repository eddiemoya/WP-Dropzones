<?php 

class WPDZ_Controller_Widgets {

	/**
	 * 
	 */
	public function init(){
		self::add_actions();
	}

	/**
	 * 
	 */
	public function add_actions(){
		add_action('init', array(__CLASS__,'register_post_type') );
	}

	public function add_widget($class){
		$new_widget = WPDZ_Model_Widgets($class);
	}
	/**
	 * 
	 */
	public function register_post_type(){
		$labels = array(
			'name' => _x('Widgets', 'post type general name'),
			'singular_name' => _x('Widget', 'post type singular name'),
			'add_new' => _x('Create New', 'book'),
			'add_new_item' => __('Create New Widget'),
			'edit_item' => __('Edit Widget'),
			'new_item' => __('New Widget'),
			'all_items' => __('All Widgets'),
			'view_item' => __('View Widget'),
			'search_items' => __('Search Widgets'),
			'not_found' =>  __('No widgets found'),
			'not_found_in_trash' => __('No widgets found in Trash'), 
			'parent_item_colon' => '',
			'menu_name' => __('Widgets')
		);


		$args = array(
			'label'	=> 'widgets',
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => false,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => false,
			'rewrite' => false,
			'capability_type' => 'post',
			'has_archive' => false, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'custom-fields' )
		); 

		register_post_type('widget', $args);
	}


}