<?php 
/**
 * 
 */
class WPDZ_Model_Widget {

	/**
	 * The id of this widget instance.
	 */
	private $post_id;

	/**
	 * An object instance of the widget's class 
	 *
	 * - not to be confused with the $instance variable used within widget development
	 * used to contain a widgets values
	 */
	private $instance;

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
	public function __construct($widget_classname){
		$this->instance = new $widget_classname();
	}

	/**
	 * 
	 */
	public function update_options($new_options){

		//Run these options through the widgets update() method.
		$new_options = $this->instance->update($new_options, $this->options);


		if(!empty($new_options)) {
			$this->option = $new_options;
			foreach($new_options as $option => $value){
				$this->update_option($option, $value);
			}
		}

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
	public function get_post_id(){
		return $this->post_id;
	}

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
	private function insert_post(){

		$post_args = array(
			'post_type' => 'widget'
			//'post_parent' => [ <post ID> ] //Sets the parent of the new post.
			//'tax_input' => [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies. 
		);  

		$this->post_id = wp_insert_post($post_args);

	}






}