<?php

/**
 * Extend for the creation of specific metaboxes.
 * 
 * @author Eddie Moya 
 * @todo Detailed documentation of these properties, mostly just off the add_meta_box function in core.
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
    var $options = array('');

    /**
     * @var string
     */
    var $view;
    
    var $enabled_option_key = '';
    /**
     * Start you fucking engines!
     * 
     * @param array $args optional. Array of properties to be set.
     * @return void
     */
    public function __construct($args = null) {

        //Get all properties of this object
        $properties = get_object_vars($this);

        // Allow only pre-existing properties to be set by arguments to the constructor
        foreach ($properties as $key => $value) {
            if (isset($args[$key])) {
                $this->$key = $args[$key];
            }
        }
        
    }

    /**
     * Save any options set in the $option_keys property.
     * 
     * Gets called on the 'save_post' action. 
     * 
     * The add_action for this is in WPDZ_Controller_Metaboxes::add_actions();
     * 
     * @param int $post_id
     * @return void 
     */
    public function save($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        foreach ((array) $this->options as $key => $option) {
            if(isset($_POST[$key])){
                update_post_meta($post_id, $key, $_POST[$key]);
            }
        }
    }

    /**
     * Includes the view for this metabox, the filename of which should
     * be set to the $view property.
     * 
     * @return void
     */
    public function view() {
        foreach ($this->options as $option_key => $option_labels) {
            include(WPDZ_VIEWS . $this->view . '.php');
        }
    }

    /**
     * Checks if the options for this metabox to be enabled, have been set.
     * 
     * @return bool
     */
    public function is_enabled(){
        $is_enabled = true;
        if(!empty($this->enabled_option_key)){
            $is_enabled = ($this->get_option_value($this->enabled_option_key));
        }
        return $is_enabled;
    }
    
    /**
     * Right now is just a wrapper for get_post_meta, but may later have use
     * to handle $option_key values more robustly. 
     * 
     * @global object $post
     * @param string $key The meta_key for the desidered post_meta.
     * @return mixed Contents of the post meta of the desired option. 
     */
    public function get_option_value($key){
        global $post;
        
        if(is_admin()){ $post_id = $_REQUEST['post']; } 
        else { $post_id = $post->ID;}
        
        return get_post_meta($post_id, $key, true);
    }

}