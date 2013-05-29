<?php 

class WidgetPress_Controller_Dropzones {


	/**
	 * 
	 */
	public function init(){
		self::action_hooks();
	}

	private function action_hooks(){

		do_action('widgetpress_init');

		add_action( 'init', 			array(__CLASS__, 'register'));
		//add_action( 'created_layout',	array(__CLASS__, 'create_layout'));
		add_action( 'created_dropzone',	array(__CLASS__, 'create_dropzone'));
		add_action( 'wp_ajax_widgetpress_dropzone_settings', array(__CLASS__, 'save_dropzone'));
	}

	/**
	 * 
	 */
	public function create_layout($term_id, $tt_id = null){
		$layout = new WidgetPress_Model_Dropzone($term_id, null, 'layout', 'Dropzone_Widget');

	}

	/**
	 * 
	 */
	public function create_dropzone($term_id, $tt_id = null, $type = 'dropzone'){
		$dropzone = new WidgetPress_Model_Dropzone($term_id, null, $type);
	}


	public function add_dropzone(){
		// $layout = get_term($_POST['dropzone_id'], $_POST['dropzone_type']);
		// $dropzone = (object)wp_insert_term('Dropzone: ' . $layout->name, 'dropzone');

		// if(!is_wp_error($dropzone)){
		// 	$post = self::get_dropzone_post($dropzone, 'dropzone');

		// 	wp_set_object_terms($post->ID, $layout->term_id, $_POST['dropzone_type']);

		// }
		echo 'testingjson';
		exit('fuck');
	}

	public function save_dropzone(){
		
		update_post_meta($_POST['id'], 'dropzone_span', $_POST['span']);
		update_post_meta($_POST['id'], 'dropzone_borderleft', $_POST['borderleft']);
		//echo json_encode($_POST);
		exit();
	}


	public function get_dropzone_post($term, $type){
		$term = (is_object($term)) ? $term : get_term($term, $type);

		$post = get_posts(
			array(
				'post_type' => array($type),
				'posts_per_page' => 1,
				'tax_query' => array(
					array(
						'taxonomy' => $type,
						'field' => 'id',
						'terms' => array($term->term_id)
		))));
		return $post;
	}
	/**
	 * 
	 */
	public function get_dropzones($type = 'dropzone', $post_id = null){

		$terms =  get_the_terms( self::post_id($post_id), $type );
		$terms = ($type == 'layout') ? array($terms[0]) : $terms;

		$dropzones = array();

		if(!empty($terms)){

			foreach((array)$terms as $term){
				$args = array(
						'post_type' => array( $type ),
						'posts_per_page' => 1,
						'tax_query' => array(
							array(
								'taxonomy' => $type,
								'field' => 'id',
								'terms' => array($term->term_id)
					)));

				$post = get_posts($args);


				//echo "<pre>";print_r($post);echo "</pre>";


				$dropzones[] = new WidgetPress_Model_Dropzone($term, $post[0], $type);
			} 
		} else {

			$dropzones = false;
		}
		//echo "<pre>";print_r($dropzones);echo "</pre>";
		

		return $dropzones;
	}

	/**
	 * 
	 */
	private function post_id($post_id = null){
		if(is_null($post_id)){
			global $post_id;

			if(empty($post_id)){
				$post_id = $_REQUEST['post_id'];
			}
		}
		return $post_id;
	}

	/**
	 * 
	 */
	public function register(){
		//self::register_layout_taxonomy();
		self::register_dropzone_taxonomy();
		//self::register_layout_post_type();
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
			'parent_item_colon'		=> __( 'Parent Layout: ' ),
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
			'dropzone',
			'widget',
			'category'
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
			'public' 				=> false,
			'publicly_queryable' 	=> false,
			'show_ui' 				=> true, 
			'show_in_menu' 			=> true, 
			'query_var' 			=> false,
			'rewrite' 				=> false,
			'capability_type' 		=> 'post',
			'has_archive' 			=> false, 
			'hierarchical' 			=> false,
			'menu_position'			=> null,
			'exclude_from_search'	=> true,
			'supports' => array( 'title', 'custom-fields' )
		)); 

		register_post_type('dropzone', $args);
	}


}