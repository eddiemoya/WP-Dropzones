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

	/**
	 * 
	 */
	public function __construct($term = null, $post = null, $type = 'dropzone'){

		$this->type 	= $type;
		$this->term 	= $term;

		if(is_null($post)){
			$this->post = $this->create_dropzone($this->term->term_id, $type);
		} else {
			$this->post = (is_object($post)) ? $post : get_post($post);
		}

		if(!empty($this->post)){
			$this->get_dropzone_meta();
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
	public function create_dropzone($term_id, $type = 'dropzone'){
		if(!empty($term_id)){
			$this->post = $this->insert_post($term_id, $type);
			$this->meta = $this->set_dropzone_meta();
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

	/**
	 * 
	 */
	private function get_dropzone_meta(){
		$meta = get_post_custom($this->post->ID);
		unset($meta['_edit_lock']);
		$this->meta = $meta;
	}


}