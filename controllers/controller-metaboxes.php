<?php

/**
 * Static class containing methods pertaining directly to metaboxes
 *
 * @author Eddie Moya
 */
class WPDZ_Controller_Metaboxes {

    public static $metaboxes;

    /**
     * Start your engines.
     * 
     * @author Eddie Moya
     * @return void 
     */
    public function init() {

        require_once(ABSPATH . 'wp-admin/includes/widgets.php');
        
        self::add_actions();
        
        self::create_metaboxes();
        
    }

    /**
     * Instantiate objects for each of the metabox classes.
     * 
     * This function was previously a factory-style method
     * which concatinated strings to get the class. I scrapped
     * that because there turned out to only be a few classes
     * and this is easier to read.
     * 
     * @author Eddie Moya
     * @return void.
     */
    private function create_metaboxes() {
        
        self::$metaboxes['settings']    = new WPDZ_Metabox_Settings();
        self::$metaboxes['layout']      = new WPDZ_Metabox_Sidebars();
        self::$metaboxes['dropzones']   = new WPDZ_Metabox_Dropzones();
    }

    /**
     * Setup metabox related actions.
     * 
     * @author Eddie Moya
     * @uses add_action();
     * 
     * @return void
     */
    private function add_actions() {
        add_action('admin_print_scripts-post.php',  array(__CLASS__, 'enqueue'));
        add_action('add_meta_boxes',                array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post',                     array(__CLASS__, 'save_metaboxes'));
        add_action('wp_ajax_refresh-metabox',       array(__CLASS__, 'refresh_metabox'));
    }

    /**
     * Enqueue all necessary styles and scripts. 
     * 
     * I only called on post editor paged, due to the dynamically generated hook used.
     * 
     * @author Eddie Moya
     * @return void
     */
    public function enqueue() {
        wp_register_style('widgets_metabox_styles', plugins_url('assets/css/widgets-metabox.css', dirname(__FILE__)));
        wp_enqueue_style('widgets_metabox_styles');

        wp_register_script('save_widgets_metabox', plugins_url('assets/js/save-widgets.js', dirname(__FILE__)));
        wp_enqueue_script('save_widgets_metabox');

        //Better if I moved this to the sidebar controller, but meh.
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
            
            // If callback args were not passed as an array, then make it one.
            if (!is_array($metabox->callback_args)) {
                $metabox->callback_args = array($metabox->callback_args);
            }
            foreach ((array) $metabox->post_types as $index => $post_type) {
                if($metabox->is_enabled()){
                    if(is_array($metabox->priority)){
                        $priority = $metabox->priority[$index];
                    } else {
                        $priority = $metabox->priority;
                    }
                    add_meta_box($metabox->id, $metabox->title, array($metabox, 'view'), $post_type, $metabox->context, $priority, $metabox->callback_args);
                }
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
    
    public function refresh_metabox(){
        return json_encode(self::$metaboxes['sidebars']->view());
    }
}