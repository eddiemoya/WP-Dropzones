<?php
/**
 * Description of wpdz-settings-metabox
 *
 * @author Eddie Moya
 * @todo save_post functionality, to allow for previews.
 */
class WPDZ_Sidebar {
    
   
    /**
     * @var type 
     */
    var $id;
    
    /**
     * @var string
     */
    var $class;
    /**
     * @var string
     */
    var $name;
    
    /**
     * @var string|array 
     */
    var $before_widget;
    
    /**
     * @var string 
     */
    var $after_widget;
    
    /**
     * @var string
     */
    var $before_title;
    
    /**
     * @var array 
     */
    var $after_title;
    
    /**
     * @var string
     */
    var $description;
    
    /**
     * @var type 
     */
    var $enabled = true;
    
    /**
     * @var type 
     */
    var $widgets;
    
    /**
     * Start you fucking engines!
     * 
     * @param array $args optional. Array of properties to be set.
     * @return void
     */
    public function __construct($args = array()){

        //Get all properties of this object
        $properties = get_class_vars(__CLASS__);

        // Allow only pre-existing properties to be set by arguments to the cunstructor
        foreach($properties as $key => $value){
            if(isset($args[$key])){
                //echo 'value: ' . $args[$key];
                $this->$key = $args[$key]; 
            }
        }
        
        //Determine this sidebars id.
        $this->id = $this->get_sidebar_id();
        
        //Register this sidebar
        $this->register_sidebar();
        
        //Get this sidebars widgets.
        $sidebars  = $this->get_sidebar_widgets(); 
        $this->widgets = $sidebars[$this->id];   
        
    }
 
    
    /**
     * Generates a slug for this sidebar, for this post specifically.
     * 
     * Uses the existing ID to differentiate between types of sidebars.
     * 
     * @global int $post_id
     * @return string The ID of this sidebar. 
     */
    public function get_sidebar_id($id = null) {
        global $post;
        
      if(is_admin()){ $post_id = $_REQUEST['post']; } 
        else { $post_id = $post->ID;}
        
        if(empty($id)){
           $id = $this->id; 
        }
        
        if(empty($id)){
            $id = str_replace(' ' , '-', strtolower($this->name));
            $this->id = $id;
        }
        
        return 'wpdz-sidebar-'. $id. '-' . $post_id;
    }

    /**
     * Retreives widget id's from post meta for the widgets added to this
     * sidebar.
     * 
     * @global object $post
     * @return array Array of slugs for each widget. 
     */
    public function get_sidebar_widgets() {
        global $post, $wp_registered_sidebars, $wp_registered_widgets;

        if(is_admin()){ $post_id = $_REQUEST['post']; } 
        else { $post_id = $post->ID;}

        $widgets_key = 'wpdz_widgets-temp';
        //$widgets_key .= (is_preview() || is_admin()) ? '-temp' : '';

        $widgets = get_post_meta($post_id, $widgets_key, true);

 

        if (empty($widgets)) {
            $widgets = $this->wp_get_sidebars_widgets();
        }
        wp_set_sidebars_widgets($widgets);

        return (!empty($widgets)) ? $widgets : wp_get_widget_defaults();
    }
    
    /**
     * Register this sidebar.
     * 
     * Keep in mind that each sidebar is not globally available,
     * but is instead only registered and only exists for the durration
     * of a particular page load.
     * 
     * @return void
     */
    private function register_sidebar() {
        register_sidebar(array(
            'name' => $this->name,
            'id' => $this->id,
            'description' => $this->description,
            'before_widget' => $this->before_widget,
            'after_widget' => $this->after_widget,
            'before_title' => $this->before_title,
            'after_title' => $this->after_title
        ));
    }
    
    /**
     * Used by the admin sidebar view to add CSS classes.
     * 
     * @return string CSS classnames to be wrapped for the backend. 
     */
    public function get_wrap_class() {
        
        $wrap_class = 'widgets-holder-wrap';

        if (!empty($this->class))
            $wrap_class .= ' sidebar-' . $this->class;

        return $wrap_class;
    }

    /**
     * Checks to see if the current setup has been saved or not.
     * 
     * @return bool
     */
    public function is_outofdate(){
        global $post;
        
        if(is_admin()){ $post_id = $_REQUEST['post']; } 
        else { $post_id = $post->ID;}
        
        $temp_widgets   = get_post_meta($post_id, 'wpdz_widgets-temp', true);
        $saved_widgets  = get_post_meta($post_id, 'wpdz_widgets', true);
        
        $outofdate = false;
        
        if( !empty($temp_widgets) && $temp_widgets != $saved_widgets ) {
            $outofdate = true;
        }

        return $outofdate;
    }
    
    /**
     * Ajax callback - saves newly added widgets via ajax.
     * 
     * @return void
     */
    public function ajax_save_widget() {
        global $post,  $wp_registered_widgets, $wp_registered_sidebars;
 
        update_post_meta($_POST['post_id'], 'wpdz_widgets-temp', wp_get_sidebars_widgets());
        update_post_meta($_POST['post_id'], 'wpdz_widgets-registered', $wp_registered_widgets);
        die('1 - widget saved');
    }
    
    
    /**
     * Save the widgets, move them from -temp, to the perminent option.
     * 
     * Gets called on the 'save_post' action. 
     * 
     * The add_action for this is in WPDZ_Controller_Sidebars::add_actions();
     * 
     * @param int $post_id
     * @return void 
     */
    public function save($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        $widgets = get_post_meta($post_id, 'wpdz_widgets-temp', true);
        
        if(!empty($widgets)){
            update_post_meta($post_id, 'wpdz_widgets', $widgets);
        }
        
    }
    
    
    /**
     * Include the sidebar view.
     * 
     * @return void 
     */
    public function view() {
        include(WPDZ_VIEWS . 'wpdz-sidebar.php');
    }

function wp_get_sidebars_widgets($deprecated = true) {
    if ( $deprecated !== true )
        _deprecated_argument( __FUNCTION__, '2.8.1' );

    global $wp_registered_widgets, $_wp_sidebars_widgets, $sidebars_widgets;
    //unset($_wp_sidebars_widgets);
    // If loading from front page, consult $_wp_sidebars_widgets rather than options
    // to see if wp_convert_widget_settings() has made manipulations in memory.
    if ( !is_admin() ) {
        //if ( empty($_wp_sidebars_widgets) )
            $_wp_sidebars_widgets = get_option('sidebars_widgets', array());

        $sidebars_widgets = $_wp_sidebars_widgets;
    } else {
        $sidebars_widgets = get_option('sidebars_widgets', array());
    }

    if ( is_array( $sidebars_widgets ) && isset($sidebars_widgets['array_version']) )
        unset($sidebars_widgets['array_version']);
    /// USE THE FILTER!!
    $sidebars_widgets = apply_filters('sidebars_widgets', $sidebars_widgets);
    return $sidebars_widgets;
}


    

}
