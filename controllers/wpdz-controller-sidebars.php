<?php

/**
 * Handles all the dirty work of figuring out whats going on with sidebars.
 *
 * @author Eddie Moya
 */
class WPDZ_Controller_Sidebars {
  
   /**
    * @var array Arguments for the 'layout manager' sidebar.
    */
   public static $layout_manager_settings;
   
   /**
    * @var object Object of WPDZ_Sidebar(); 
    */
   public static $layout_manager;
   
   /**
    * This is determined (via abracadabra) by the widgets in the layout manager sidebar
    * 
    * @var array Arguments for the all the sidebars of the current post.
    */
   public static $sidebar_settings;
   
   /**
    * @var object Object of WPDZ_Sidebar(); 
    */
   public static $sidebars;

   /**
    * The name is.. whatever. This is a giant list of all potential widgets. It needed for
    * a lookup process that takes place in WPDZ_Controller_Sidebars::abracadabra();
    * @var array 
    */
   public static $registered_dropzones;

   
   
    /**
     * Start your engines. 
     */
    public function init() {
        global $post;
        add_action('init', array(__CLASS__, 'add_actions'));
        
        if(is_admin()){
            $post_id = $_REQUEST['post'];
        } else {
            $post_id = $post->ID;
        }
        
        self::$registered_dropzones = get_post_meta($post_id, 'wpdz_widgets-registered' . $sidebar->id, true);
        
        
        /**
         * The order of these is critical. The layout manager needs to be instantiated before
         * we can get all its widgets and determine what dropzones should be available and what
         * their settings should be.
         */
        self::get_sidebar_settings('layout-manager');
        self::register_sidebars('layout-manager');
        
        self::get_sidebar_settings('sidebars');
        self::register_sidebars('sidebars');
    }

    
    /**
     * Add actions for sidebars.
     * 
     * Ajax callback is static, but the register_sidebar must have the context of
     * each sidebar object.
     * 
     * @return void. 
     */
    public function add_actions() {
        add_action('wp_ajax_wpdz-save-widget', array(WPDZ_Sidebar, 'ajax_save_widget'));
        add_action('wp_ajax_refresh-metabox',  array(WPDZ_Controller_Metaboxes, 'refresh_metabox'));
        
        foreach((array)self::$sidebars as $sidebar){
            add_action('widgets_init', array($sidebar, 'register_sidebar'));
        }
    }

    /**
     * Instantiate an object of WPDZ_Sidebar for each of the settings. 
     * 
     * @return void
     */
    private function register_sidebars($metabox){
        foreach((array)self::$sidebar_settings[$metabox] as $settings){
            self::$sidebars[$metabox][] = new WPDZ_Sidebar($settings);
        }
    }
    
    /**
     * Sets up the paramaters for sidebars. 
     * Special condition for the layout-manager sidebar.
     * 
     * @param string $metabox 
     * @return void
     */
    private function get_sidebar_settings($metabox) {
        if ($metabox == 'layout-manager') {
            self::$sidebar_settings['layout-manager'][] = array(
                'name' => 'Dropzones',
                'id' => 'layout-manager',
                'description' => 'Use this area to control this pages layout',
                'before_widget' => '<ul>',
                'after_widget' => '</ul>',
                'before_title' => '<li>',
                'after_title' => '<li>'
            );
        } else {
            foreach ((array) self::abracadabra() as $key => $dropzone) {
                self::$sidebar_settings[$metabox][] = $dropzone;
            }
        }
    }

    /**
     * Calls the view method of each WPDZ_Sidebar assigned to whatever
     * metabox was passed. The metabox string is the associative key of the
     * self::$sidebars array.
     * 
     * @param type $type 
     */
    public function view($metabox) {
        foreach ((array)self::$sidebars[$metabox] as $sidebar) {
            $sidebar->view();
        }
    }
    
    /**
     * Fucking magical lookup function to grab the values of all the widgets
     * in the dropzones layout manager.
     * 
     * @return array. List of all the dropzone widgets in the layout manager along with all the values for that widget (arguments and settings for the dropzone)
     */
    private function abracadabra() {
        $layout_manager_widget_ids = self::$sidebars['layout-manager'][0]->widgets;
        $all_widgets = self::$registered_dropzones;
        $dropzones = array();
        $widgets = array();
        
        foreach((array)$layout_manager_widget_ids as $widget_id){
            $widgets[$widget_id] = $all_widgets[$widget_id];
        }
        
        foreach ((array) $widgets as $slug => $widget) {
            $widget_obj = $widget['callback'][0];

            if (is_object($widget_obj)) {
                $widget_number = $widget['params'][0]['number'];
                $widget_settings = $widget_obj->get_settings();

                foreach ((array)$widget_number as $number) {
                    $dropzones[$slug] = $widget_settings[$number]['args'];
                    $dropzones[$slug]['id'] .= '_' . $number;
                }
            }
        }
        return $dropzones;
    }
    
    /**
     * 
     */
    public function display_dropzones(){
        self::init();
        foreach((array)self::$sidebars['layout-manager'] as $layout_manager){
            dynamic_sidebar($layout_manager->id);
        }
    }
}

/**
 * 
 */
function display_dropzones(){
    WPDZ_Controller_Sidebars::display_dropzones();
}