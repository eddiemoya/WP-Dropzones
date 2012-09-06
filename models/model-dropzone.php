<?php 

class WidgetPress_Model_Dropzone {

	/**
	 * 		
	 */
	private $type;

	/**
	 * 
	 */
	private $term;

	/**
	 * 
	 */
	private $post;

	/**
	 * 
	 */
	private $meta;

	private $class;
	/**
	 * 
	 */
	public function __construct($term = null, $post = null, $type = 'dropzone', $default_widget = 'Dropzone_Widget'){

		$this->type 	= $type;
		$this->term 	= (is_object($term)) ? $term : get_term($term, $type);


		if(is_null($post)){
			$this->post = $this->create_dropzone($this->term->term_id, $type);
		} else {
			$gpost = $GLOBALS['post'];
			unset($GLOBALS['post']);
			$this->post = (is_object($post)) ? $post : get_post($post);
			$GLOBALS['post'] = $gpost;
		}

		//echo "<pre>";print_r($post);echo "</pre>";
		if(!empty($this->post)){
			$this->get_dropzone_meta();
		}

		if(!empty($default_widget)){
			//$this->class = new WidgetPress_Model_Widget(null, null, $default_widget);
		}

		//echo "<pre>";print_r($post);echo "</pre>";


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
	public function create_dropzone($term_id, $type = 'dropzone'){
		if(!empty($term_id)){
			$this->post = $this->insert_post($term_id, $type);
			$this->meta = $this->get_dropzone_meta();
		}

		return $this;
	}

	/**
	 * 
	 */
	private function insert_post($term_id, $type){
		$tax = get_taxonomy($type);

		$post_id = wp_insert_post(array(
			'post_status' 	=> 'publish',
			'post_type' 	=> $this->type,
			'post_name'		=> $this->term->slug,
			'post_title' 	=> $tax->labels->singular_name.': '.$this->term->name,
			'tax_input'		=> array( $this->type => array($term_id) )
		));

		return get_post($post_id);

	}

	public function view($template, $args = array()){
		extract($args);
		include (WPDZ_VIEWS . $template);
	}

	public function form(){

	}

	public function update($data = null){

	}

	/**
	 * 
	 */
	private function get_dropzone_meta(){
		$meta = get_post_meta($this->post->ID, '', false);
		unset($meta['_edit_lock']);
		unset($meta['_edit_last']);

		foreach($meta as &$m){
			if(is_array($m)){
				$m = $m[0];
			}
		}
		$this->meta = $meta;
	}


}