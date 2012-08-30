<?php 
/**
 * 
 */
class WidgetPress_Model_Widget {

	/**
	 * The post object of this widget instance.
	 */
	private $post;

	/**
	 * An object instance of the widget's class 
	 *
	 * - not to be confused with the $instance variable used within widget development
	 * used to contain a widgets values
	 */
	private $widget;

	/**
	 * For convenience - an array of the meta value names.
	 */
	private $meta_keys;

	/**
	 * An array of all the available options for this widget, and theit values.
	 */
	private $options = array();

	/**
	 * 
	 */
	private $widget_class;


	/**
	 * 
	 */
	public function __construct($post = null, $term = null, $widget_class = null){


		if(is_null($post) && !is_null($term)){
			$this->insert_post($term);
		}

		//$widget_classname = $this->get_widget_class();
		$this->post = (is_object($post)) ? $post : get_post($post);

		if(!is_null($widget_class)){
			$this->widget_class = $widget_class;
		}

		if(!is_null($this->widget_class)){
			$this->widget = new $widget_class();
		}
		//
	}

	/**
	 * 
	 */
	public function update_options($new_options){

		//Run these options through the widgets update() method.
		$new_options = $this->widget->update($new_options, $this->options);


		if(!empty($new_options)) {
			$this->option = $new_options;
			foreach($new_options as $option => $value){
				$this->update_option($option, $value);
			}
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
	public function get_option($key, $value){
		return $this->options[$key];
	}

	/**
	 * 
	 */


	public function add_to_sidebar($sidebar_id){
		wp_set_post_terms($this->post_id, $sidebr_id, 'sidedebar' );
	}

	/**
	 * 
	 */
	private function set_widget_classname(){

		$classname = get_class($this->instance);
		$this->update_option('_widget_classname', $classname);
	}	
	/**
	 * 
	 */
	private function update_option($key, $value, $prefix = 'widget_'){

		$previous_value = (isset($this->options[$key])) ? $this->options[$key] : null ;

		update_post_meta($this->post_id, $prefix.$key, $value, $previous_value);
		$this->options[$key] = $value;
	}

	/**
	 * 
	 */
	private function insert_post($term){

		$tax = get_taxonomy('dropzone');

		$post_id = wp_insert_post(array(
			'post_status' 	=> 'publish',
			'post_type' 	=> 'widget',
			'tax_input'		=> array( 'dropzone' => array($term_id) )
		));

		return wp_insert_post($post_args);

	}






}