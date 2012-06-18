<?php

/**
 * Static class containing methods pertaining directly to metaboxes
 *
 * @author Eddie Moya
 */
class WPDZ_Controller_Metaboxes {

    public static $metaboxes;

    /**
     * Adds metaboxes. Some are conditional on options set by the user.
     * 
     * @author Eddie Moya
     * @global object $post
     * 
     * @return void 
     */
    public function init() {

        require_once(ABSPATH . 'wp-admin/includes/widgets.php');

        self::$metaboxes['settings'] = new WPDZ_Metabox_Settings();
        self::$metaboxes['sidebars'] = new WPDZ_Metabox_Sidebars();
        //self::$metaboxes['dropzones'] = new WPDZ_Metabox_Dropzones();
        self::add_actions();
    }

    /**
     * Setup actions for this plugin
     * 
     * @author Eddie Moya
     * @uses add_action();
     * 
     * @return void
     */
    protected function add_actions() {
        if (strstr($_SERVER['REQUEST_URI'], 'wp-admin/post')) {
            add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue'));
            add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
            add_action('save_post', array(__CLASS__, 'save_metaboxes'));
        }
        //add_action('init', array(WPDZ_Sidebar, 'ajax_actions'));
    }

    /**
     * Enqueue all necessary styles and scripts.
     * 
     * @author Eddie Moya
     * @return void
     */
    public function enqueue() {
        wp_register_style('widgets_metabox_styles', plugins_url('wp-dropzones/assets/css/widgets-metabox.css', WPDZ_PATH));
        wp_enqueue_style('widgets_metabox_styles');

        wp_register_script('save_widgets_metabox', plugins_url('wp-dropzones/assets/js/save-widgets.js', WPDZ_PATH));
        wp_enqueue_script('save_widgets_metabox');

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-droppable');
    }

    /**
     * Add All Metaboxes - is called during the 'init' action.
     * 
     * The view method of the metabox object should be in this controller instead of
     * in the model, but it would need some sort of autoloader, which I don't 
     * feel like building right now.
     * 
     * The name of the view template is stored as a property in each metabox object.
     * 
     * @uses add_meta_box(); 
     */
    public function add_meta_boxes() {
        
        foreach ((array) self::$metaboxes as $metabox) {
            
            // if callback args were not passed as an array, then make it one.
            if (!is_array($metabox->callback_args)) {
                $metabox->callback_args = array($metabox->callback_args);
            }
            foreach ((array) $metabox->post_types as $post_type) {
                add_meta_box($metabox->id, $metabox->title, array($metabox, 'view'), $post_type, $metabox->context, $metabox->priority, $metabox->callback_args);
            }
        }
    }
    
    /**
     * Call the save method for each metabox. The metaboxes each
     * should have a property containing an array for all the
     * option names it handles. 
     * 
     * The $metabox->save() method loops through each of those options
     * and saves it to post meta.
     * 
     * @uses WPDZ_Abstract_Metabox::save(); (or a save method thats been overwritten by a child class)
     */
    public function save_metaboxes($post_id){
        foreach ((array) self::$metaboxes as $metabox) {
            $metabox->save($post_id);
        }
    }
    
    /**
     *
     * @global object $post
     * @return type 
     */
    function is_enabled() {
        global $post;

        $meta = get_post_meta($post->ID, 'wpdz_dropzones_enabled', true);
        return !empty($meta);
    }

}