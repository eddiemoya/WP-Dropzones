<?php

/**
 * extend for the creation of specific metaboxes.
 * 
 * @author Eddie Moya 
 */
class WPDZ_Metabox {


    /**
     * @var string
     */
    var $id;

    /**
     * @var string 
     */
    var $title;

    /**
     * @var string|array 
     */
    var $post_types;

    /**
     * @var string 
     */
    var $context = null;

    /**
     * @var int
     */
    var $priority = 'default';

    /**
     * @var array 
     */
    var $callback_args = null;

    /**
     * @var type 
     */
    var $option_keys = array();

    /**
     * @var string
     */
    var $view;
    
    /**
     *
     * @param type $args 
     */
    public function __construct($args = null) {

        //Get all properties of this object
        $properties = get_object_vars($this);

        // Allow only pre-existing properties to be set by arguments to the cunstructor
        foreach ($properties as $key => $value) {
            if (isset($args[$key])) {
                $this->$key = $args[$key];
            }
        }
    }

    /**
     *
     * @param type $post_id
     * @return type 
     */
    public function save($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        foreach ((array) $this->option_keys as $option) {
            update_post_meta($post_id, $option, $_POST[$option]);
        }
    }

    /**
     * 
     */
    public function view() {
        WPDZ_Helper::include_view($this->view);
    }
    
    public function get_option_value($key){
        global $post;
        return get_post_meta($post->ID, $key, true);
    }

}