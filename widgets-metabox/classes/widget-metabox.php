<?php
/**
 * Widgets administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */
/** WordPress Administration Bootstrap */


class WMB_Widgets {
    
    /**
     * @var type 
     */
    public $wp_registered_sidebars;
    public $wp_registered_widgets;
    
    /**
     * @var type  ???
     */
    public $widget_access;
    
    /**
     *
     * @var type 
     */
    public $sidebars_widgets;
    

    /**
     * 
     */
    public function __construct(){
        global $wp_registered_sidebars, $wp_registered_widgets;
        
        $this->wp_registered_sidebars = $wp_registered_sidebars;
        $this->wp_registered_widgets = $wp_registered_widgets;
        $this->widget_access = get_user_setting('widgets_access');
        
        //if (empty($sidebars_widgets))
        $this->sidebars_widgets = $this->get_sidebar_widgets();
        
        require_once(ABSPATH . 'wp-admin/includes/widgets.php');
        
        $this->current_user_can();
        $this->set_widget_access();
        
        do_action('sidebar_admin_setup');
        //add_action('save_post', array($this, 'save')); 
        
        //print_pre($this->wp_registered_sidebars);
        //print_pre($this->wp_registered_widgets);
        //print_pre($this->sidebars_widgets);
        
        
    }
    
    
    
    public function widget_metabox() {
        //do_action( 'widgets_admin_page' );
       $this->include_file('views/sidebar-metabox'); 


    }

    public function wp_widgets_access_body_class($classes) {
        return "$classes widgets_access ";
    }
    
    
    private function current_user_can() {
        if (!current_user_can('edit_theme_options'))
            wp_die(__('Cheatin&#8217; uh?'));
    }
    
    function get_sidebar_widgets(){
        global $post;
        $widgets = wp_get_sidebars_widgets();
        $saved_widgets = get_post_meta($post->ID, 'wmb_widgets', true);
  
        if(empty($widgets[$this->get_sidebar_id()]) && !empty($saved_widgets)){
            $widgets = $saved_widgets;
        }
        wp_set_sidebars_widgets($widgets);
       // print_pre($widgets);
        return (!empty($widgets)) ? $widgets : wp_get_widget_defaults();
    }
    
    
    function get_sidebar() {
        $id = $this->get_sidebar_id();
        $sidebar = $this->wp_registered_sidebars[$id];
        
        $wrap_class = 'widgets-holder-wrap';
        
        if (!empty($sidebar['class']))
            $wrap_class .= ' sidebar-' . $sidebar['class'];
        
        return (object) array('id' => $id, 'sidebar' => $sidebar, 'class' => $wrap_class);
    }
    
    function inactive_widgets(){
        
    }
    /**
     * ???
     */
    private function set_widget_access() {
        
        if (isset($_GET['widgets-access'])) {
            $this->widgets_access = 'on' == $_GET['widgets-access'] ? 'on' : 'off';
            set_user_setting('widgets_access', $this->widgets_access);
        } 
         

        if ('on' == $this->widgets_access) {
            add_filter('admin_body_class', 'wp_widgets_access_body_class');
        } else {
            //wp_enqueue_script('admin-widgets');
        }
    }
    
    public function include_file($path) {
        include (WMB_PATH . $path . '.php');
    }
    
    /**
     * 
     */
 
    function get_sidebar_id(){
        global $post;
        return 'widget-metabox-'. $post->ID;
    }
    
    function save($post_id){
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        //print_pre($_POST);
        //update_post_meta($post_id, 'wmb_widgets', $_POST['widgets-metabox-enable']);
    }
}


  
?>









<?php
