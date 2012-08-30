<?php 

class WidgetPress_Controller_Widgets {

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

	/**
	 * 
	 */
	public function add_widget($class){
		$new_widget = WPDZ_Model_Widgets($class);
	}

	/**
	 * 
	 */
	public function loop($dropzone_term_id, $view = 'view-widgets', $tax = 'dropzone'){

		$widgets = $this->get_widgets();

		foreach($widgets as $widget){
			include(WPDZ_VIEWS . $view);
		}

	}

	/**
	 * Horrible. I am shame - copied and stripped down from the depths of WordPress core.
	 */
	public function get_widget_classes(){
		global $wp_registered_widgets;//, $sidebars_widgets, $wp_registered_widget_controls;

		$sort = $wp_registered_widgets;
		usort( $sort, create_function( '$a, $b', 'return strnatcasecmp( $a["name"], $b["name"] );' ) );
		$done = array();

		foreach ( $sort as $widget ) {
			if ( in_array( $widget['callback'], $done, true ) ) // We already showed this multi-widget
				continue;

			$done[] = new $widget['callback'][0];
		}

		return $done;
	}

	public function get_widgets($dropzone_term, $tax = 'dropzone'){

		$term = (is_object($dropzone_term)) ? $dropzone_term : get_term($dropzone_term, $tax);
		$widgets = array();
		if(!empty($term) && !is_wp_error($term)){

			$widgets_posts = get_posts(
				array(
					'post_type' => 'widget',
					'tax_query' => array(
						array(
							'taxonomy' => $tax,
							'field' => 'id',
							'terms' => array($term->term_id)
				))));
			foreach($widgets_posts as $post){
				$widgets[] = new WidgetPress_Model_Widget($post);
			}

		} else {
			$widgets = false;
		}
		return $widgets;
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