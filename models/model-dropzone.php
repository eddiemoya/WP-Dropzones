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
	public function __construct($post_id = null, $type = 'dropzone'){

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
	 * Simple getter for dropzone properties.
	 */
	public function get($property){
		return $this->$property;
	}

	/**
	 * Setter
	 */
	private function set($property, $value){
			$this->$property = $value;
	}

	/**
	 * 
	 */
	public function get_dropzones($type = 'dropzone', $post_id = null){
		$terms =  get_the_terms( $this->post_id($post_id), $type );
		$terms = ($type == 'layout') ? array($terms[0]) : $terms;
		foreach($terms as &$term){
			$post = get_posts(
				array(
					'post_type' => $type,
					'posts_per_page' => 1,
					'tax_query' => array(
						array(
							'operator' => 'IN',
							'taxonomy' => $type,
							'field' => 'id',
							'terms' => $term->term_id
						)
					)
				)
			);
			$term->post = $post;
		}

		return $terms;
	}

	/**
	 * 
	 */
	public function create_dropzone($term_id, $tax_id = null, $type = 'dropzone'){
		$this->type = $type;
		$this->term = get_term($term_id, $type);
		$this->post = $this->insert_post($term_id, $tax_id, $type);
	}

	/**
	 * 
	 */
	private function insert_post($term_id, $tax_id = null){
		$tax = get_taxonomy($this->type);

		$post_id = wp_insert_post(array(
			'post_status' 	=> 'publish',
			'post_type' 	=> $this->type,
			'post_name'		=> $this->term->slug,
			'post_title' 	=> $tax->labels->singular_name.': '.$this->term->name,
			'tax_input'		=> array( $this->type => array($term_id) )
		));

		return get_post($post_id);

	}


}