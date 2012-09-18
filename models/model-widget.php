<?php 

class WidgetPress_Model_Widget {

	/**
	 * 		
	 */
	private $class;


	/**
	 * 
	 */
	private $post;

	/**
	 * 
	 */
	private $meta;

	/**
	 * 
	 */
	public function __construct($post = null, $term = null, $class = null, $type = 'dropzone'){

		if(!is_null($term))
			$term = (is_object($term)) ? $term : get_term($term, $type);

		if(is_null($post) && !is_null($term)){
			$this->post = $this->create_widget($term, $class);
		} else {
			$gpost = $GLOBALS['post'];
			unset($GLOBALS['post']);
			$this->post = (is_object($post)) ? $post : get_post($post);
			$GLOBALS['post'] = $gpost;
		}

		if(!empty($this->post)){
			$this->set_widget_meta();
		}

		$this->set_widget_class($class);


		if(!is_null($term) && !is_null($post) && !has_term($term->term_id, $term->taxonomy, $post)){
			//$this->add_to_dropzone($term->term_id, $term->taxonomy);
		}



	
	}

	/**
	 * Simple getter for dropzone properties.
	 */
	public function get($property){
		return $this->$property;
	}

	/**
	 * Setter
	 */
	protected function set($property, $value){
			$this->$property = $value;
	}

	/**
	 * 
	 */
	public function create_widget($term_id, $class = null){
		if(!empty($term_id)){
			$widget_post = $this->insert_post($term_id, $class);
		}

		return $widget_post;
	}

	/**
	 * 
	 */
	public function update($instance = null){
		if(empty($instance)){
			//$instance = apply_filters('widgetpress_update_filter', $this->meta, $instance);
			$this->set_widget_meta();
			$instance = $this->meta;
		}

		//$instance = apply_filters('widgetpress_update_filter', $instance, $this->meta);

		$this->update_options($instance);



		if(is_object($this->class)){
			$instance = $this->class->update($instance, $this->meta);
			$this->class->form($instance);
		}

		

		//$instance = apply_filters('widgetpress_after_form_fields', $instance, $this->class);
	}

	/**
	 * 
	 */
	private function set_widget_class($default_class = null){
		$class = $this->meta['widgetpress_widget_classname'];
		//$this->class = new $class();
		if(!empty($class) && class_exists($class)){
			$this->class = new $class();
		} else {
			$this->class = (!empty($default_class)) ? $default_class : $class;
		}

		if(!is_object($this->class) && is_string($this->class)){
			$class = $this->class;
			if(class_exists($class)){
				$this->class = new $class();
			}
		}
		//if(!is_null($default_class))
			

	}
	/**
	 * 
	 */
	private function insert_post($term, $widget_class = 'WP_Widget_Archives'){

		$tax = get_taxonomy('dropzone');

		$post_id = wp_insert_post(array(
			'post_status' 	=> 'publish',
			'post_type' 	=> 'widget',
			'post_name'		=> $term->slug,
			'post_title' 	=> $term->name.': '.$widget_class,
			'tax_input'		=> array( $term->taxonomy => array($term->term_id) )
		));

		


		add_post_meta($post_id, 'widgetpress_widget_classname', $widget_class);
		add_post_meta($post_id, "widgetpress_order_{$term->taxonomy}_{$term->term_id}", 0);

		
		


		return get_post($post_id);

	}

	public function view($template, $args = array()){
		extract($args);
		include (WPDZ_VIEWS . $template);
	}

	/**
	 * 
	 */
	private function set_widget_meta(){
		$meta = get_post_custom($this->post->ID);
		unset($meta['_edit_lock']);
		unset($meta['_edit_last']);

		//get post custom sometiems returns oddly structured arrays. This cleans it up. most of the time.
		if(!empty($meta)){
			foreach($meta as &$m){
				if(is_array($m)){
					$m = $m[0];
				}
			}
		}
		//return $meta;

		$this->meta = $meta;
	}

	private function get_widget_meta(){

		$meta = get_post_custom($this->post->ID);
		unset($meta['_edit_lock']);
		unset($meta['_edit_last']);
		unset($meta['widgetpress_widget_classname']);
		unset($meta['widgetpress_order_dropzone_856']);
		unset($meta['widetpress_span']);
		unset($meta['widget_name']);

		//get post custom sometiems returns oddly structured arrays. This cleans it up. most of the time.
		if(!empty($meta)){
			foreach($meta as &$m){
				if(is_array($m)){
					$m = $m[0];
				}
			}
		}
		return $meta;
	}

	/**
	 * 
	 */
	public function add_to_dropzone($dropzone_id, $dropzone_type){
		wp_set_post_terms($this->post_id, $dropzone_id, $dropzone_type );
	}

	public function remove_from_drozone($dropzone_id){

	}

	/**
	 * 
	 */
	public function update_options($new_options){

		//Run these options through the widgets update() method.
		if(is_object($this->class)){
			$new_options = $this->class->update($new_options, $this->meta);

			$old_options = $this->get_widget_meta();

			foreach((array)$old_options as $key => $value){
				delete_post_meta($this->post->ID, $key);
			}

			if(!empty($new_options)) {
				$this->meta = $new_options;
				foreach($new_options as $option => $value){
					$this->update_option($option, $value);
					update_post_meta($this->post->ID, $option, $value);
				}
			}
		}

	}
	/**
	 * 
	 */
	private function update_option($key, $value, $prefix = 'widgetpress_'){

		$previous_value = (isset($this->meta[$key])) ? $this->meta[$key] : null ;

		update_post_meta($this->post->ID, $key, $value, $previous_value);
		$this->meta[$key] = $value;
	}

}

/**
 * 
 */
// class WidgetPress_Model_Widget {

// 	/**
// 	 * The post object of this widget instance.
// 	 */
// 	private $post;

// 	/**
// 	 * An object instance of the widget's class 
// 	 *
// 	 * - not to be confused with the $instance variable used within widget development
// 	 * used to contain a widgets values
// 	 */
// 	private $widget;

// 	/**
// 	 * For convenience - an array of the meta value names.
// 	 */
// 	private $meta_keys;

// 	/**
// 	 * An array of all the available options for this widget, and theit values.
// 	 */
// 	private $options = array();

// 	/**
// 	 * 
// 	 */
// 	private $widget_class;


// 	/**
// 	 * 
// 	 */
// 	public function __construct($post = null, $term = null, $widget_class = null){


// 		if(is_null($post) && !is_null($term)){
// 			$this->insert_post($term);
// 		}

// 		$this->options = $this->get_options();

// 		//$widget_classname = $this->get_widget_class();
// 		$this->post = (is_object($post)) ? $post : get_post($post);


// 		//$this->set_widget($widget_class);
// 		//
// 	}

// 	private function get_options(){
// 		if(!empty($this->post)){
// 			$this->options = get_post_custom($this->post->ID);
// 		}
// 	}
// 	/**
// 	 * 
// 	 */
// 	public function update_options($new_options){

// 		//Run these options through the widgets update() method.
// 		$new_options = $this->widget->update($new_options, $this->options);


// 		if(!empty($new_options)) {
// 			$this->option = $new_options;
// 			foreach($new_options as $option => $value){
// 				$this->update_option($option, $value);
// 				update_post_meta($this->post->ID, $option, $value);
// 			}
// 		}

// 	}

// 	/**
// 	 * Simple getter for dropzone properties.
// 	 */
// 	public function get($property){
// 		return $this->$property;
// 	}

// 	/**
// 	 * Setter
// 	 */
// 	protected function set($property, $value){
// 			$this->$property = $value;
// 	}

// 	/**
// 	 * 
// 	 */
// 	public function get_option($key, $value){
// 		return $this->options[$key];
// 	}

// 	/**
// 	 * 
// 	 */
// 	public function add_to_sidebar($sidebar_id){
// 		wp_set_post_terms($this->post_id, $sidebr_id, 'sidedebar' );
// 	}

// 	/**
// 	 * 
// 	 */
// 	private function set_widget($widget_class = null){

// 		// If object was passed, get the objects class name.
// 		$widget_class = (is_object($widget_class)) ? get_class($widget_class) : $widget_class;

// 		//if we nothing was passed, try to find the classname elsewhere
// 		if(empty($widget_class)) {
// 			$widget_class = (isset($this->options['widgetpress_widget_classname'])) ? $this->options['widgetpress_widget_classname'] : $widget_class;
// 		}

// 		if(empty($widget_class)) {
// 			$widget_class = (isset($this->post)) ? get_class($this->post) : $widget_class;
// 		}

// 		if(!empty($widget_class)){
// 			$this->update_option('widgetpress_widget_classname', $this->widget_class);
// 		}
// 	}	
// 	/**
// 	 * 
// 	 */
// 	private function update_option($key, $value, $prefix = 'widget_'){

// 		$previous_value = (isset($this->options[$key])) ? $this->options[$key] : null ;

// 		update_post_meta($this->post_id, $prefix.$key, $value, $previous_value);
// 		$this->options[$key] = $value;
// 	}

// 	/**
// 	 * 
// 	 */
// 	private function insert_post($term){

// 		$tax = get_taxonomy('dropzone');

// 		$post_id = wp_insert_post(array(
// 			'post_status' 	=> 'publish',
// 			'post_type' 	=> 'widget',
// 			'tax_input'		=> array( 'dropzone' => array($term_id) )
// 		));

// 		return wp_insert_post($post_args);

// 	}






// }