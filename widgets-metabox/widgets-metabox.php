<?php /*
Plugin Name: Widgets Metabox
Plugin URI: http://wordpress.org/extend/plugins/media-categories-2
Description:  Allows users to assign categories to media with a clean and simplified, filterable category meta box and use shortcodes to display category galleries
Version: 0.3 (Auto-erotic Asphyxiation)
Author: Eddie Moya
Author URL: http://eddiemoya.com
*/

    
define(WMB_PATH, WP_PLUGIN_DIR . '/widgets-metabox/');
  //print_r($_REQUEST);
class Widgets_Metabox {
    
    /**
     *
     * @var type 
     */
    public static $is_enabled;
    
    public static $on_loop_end;
    public static $screen;
    
    /**
     *
     * @global type $post 
     */
    public function __construct() {
        global $post;

        if (strstr($_SERVER['REQUEST_URI'], 'wp-admin/post')) {
            $this->is_enabled = $this->is_enabled();
            $this->on_loop_end = $this->on_loop_end();
            //print_pre(get_post_meta($post->ID, 'wmb_widgets', true));
            //print_r($screen);

            add_action('admin_head', array($this, 'get_screen'));

            add_action('admin_head', array($this, 'register_sidebars'));
            add_action('add_meta_boxes', array($this, 'add_meta_boxes'), 11);
            add_action('save_post', array($this, 'save'));
            add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue'));
            //add_action('wp_default_scripts', array(__CLASS__, 'enqueue_default_version'));
        }
        add_action('init', array(__CLASS__, 'ajax_actions'));

        //if (self::on_loop_end()) {
           add_action('loop_start',array($this, 'loop_end'), 1);
        //} else {
            add_action('sidebars_widgets', array($this, 'dynamic_sidebar'));
       // }
    }
    
    function loop_end(){
       global $wp_registered_sidebars;
       echo 'TEST';
       $sidebar = array_shift(array_values($wp_registered_sidebars));
 
       dynamic_sidebar($sidebar['id']);
       //exit();
    }
    function dynamic_sidebar($sidebars) {
        global $post, $wp_registered_sidebars, $wp_registered_widgets;

        $sidebar_widgets = get_post_meta($post->ID, 'wmb_widgets', true);

        if (self::is_enabled() && !empty($sidebar_widgets) && !is_admin()) {
            
            $sidebar_widgets = array_shift(array_values($sidebar_widgets));
            foreach($sidebars as $sidebar => $widgets){
                $sidebars[$sidebar] = $sidebar_widgets;
            }
        }
       
        //print_pre($wp_registered_sidebars);
        return $sidebars;
    }
    function ajax_actions(){
        if(is_admin()){
            add_action('wp_ajax_wmb-save-widget', array(__CLASS__, 'save_widget'));
            add_action('wp_ajax_wmb_save_order', array(__CLASS__, 'save_widget_order'));  
        }
    }
    function save_widget_order(){

    }
    
    function save_widget() {
        //delete_post_meta($_POST['post_id'], 'wmb_widgets');
        update_post_meta($_POST['post_id'], 'wmb_widgets', wp_get_sidebars_widgets());

        die('x');
    }

    function get_screen(){
        self::$screen = get_current_screen();
        //print_pre($);
    }
    
    function enqueue_default_version($wp_scripts) {
        //print_r($wp_scripts);
    }
    /**
     *
     * @global type $post 
     */
    public function add_meta_boxes(){
        global $post;
     
        //Only turn on te metabox if its been enabled for this post object.
        if($this->is_enabled()){
            add_meta_box('widgets-metabox', 'Widgets', array($this, 'widgets_metabox'), 'page', 'normal', 'low', $post);
        }
        
        add_meta_box('widgets-metabox-enabled', 'Enable Widgets', array(&$this, 'enable_widgets_metabox_option'), 'page', 'side', 'low', $post);
    }
    
    public function enqueue() {

  
        
        wp_register_style('widgets_metabox_styles', plugins_url('assets/css/widgets-metabox.css', __FILE__));
        wp_enqueue_style('widgets_metabox_styles');

        wp_register_script('save_widgets_metabox', plugins_url('assets/js/save-widgets.js', __FILE__));
        wp_enqueue_script('save_widgets_metabox');

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-droppable');
        
       // wp_localize_script('save_widgets_metabox', 'wmb_ajaxurl', $ajaxurl);
    }

    /**
     * 
     */
    public function enable_widgets_metabox_option(){ 
        $this->include_file('views/enable-widgets');
    }
    
    /**
     *
     * @param type $post
     * @param type $widgets_settings 
     */
    public function widgets_metabox($post, $widgets_settings){
        $this->include_file('classes/widget-metabox');
        $mb = new WMB_Widgets();
        $mb->widget_metabox();
       // $this->include_file('views/widgets');
    }

    /**
     * 
     */
    public function register_sidebars(){
        if($this->is_enabled()){
            $this->create_page_sidebar();
        }
    }
    
    /**
     *
     * @global type $post 
     */
    public function create_page_sidebar(){
        global $post, $wp_registered_sidebars;
        //print_r($post);
        register_sidebar( array(
            'name'          => $post->post_title . ' Widgets',
            'id'            => $this->get_sidebar_id(),
            'description'   => sprintf(__('Widget Area specifically for "%s"'), $post->post_title),
            'before_widget' => '<li id="%1$s" class="widget %2$s">',
            'after_widget'  => '</li>',
            'before_title'  => '<h2 class="widgettitle">',
            'after_title'   => '</h2>' 
            
        ));
        
        //print_pre($wp_registered_sidebars);
        
    }
    
    /**
     *
     * @global type $post_id
     * @return type 
     */
    function is_enabled(){
        global $post;
        
        //$c = get_post_custom($post->ID);
        //print_r($c);
        $meta = get_post_meta($post->ID, 'widgets-metabox-enabled', true);
        return !empty( $meta );
    }
    
    function on_loop_end(){
        global $post;
        $meta = get_post_meta($post->ID, 'widgets-metabox-on-loop-end', true);
        return !empty( $meta ); 
    }
    
    /**
     *
     * @global type $post_id
     * @return type 
     */
    function get_sidebar_id(){
        global $post;
        return 'widget-metabox-'. $post->ID;
    }
    
    function get_sidebar() {
        global $wp_registered_sidebars;
        $id = $this->get_sidebar_id();
        $sidebar = $wp_registered_sidebars[$id];
        
        $wrap_class = 'widgets-holder-wrap';
        
        if (!empty($sidebar['class']))
            $wrap_class .= ' sidebar-' . $sidebar['class'];
        
        return (object) array('id' => $id, 'sidebar' => $sidebar, 'class' => $wrap_class);
    }
    /**
     *
     * @param type $post_id
     * @return type 
     */
    function save($post_id) {

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        update_post_meta($post_id, 'widgets-metabox-enabled', $_POST['widgets-metabox-enable']);
        update_post_meta($post_id, 'widgets-metabox-on-loop-end', $_POST['widgets-metabox-on-loop-end']);
        //print_pre($_POST);
        
        $temp_widgets = get_post_meta($post_id, '');
    }

    public function include_file($path) {
        include (WMB_PATH . $path . '.php');
    }

}

$widgets = new Widgets_Metabox();
function print_pre($r){
    echo '<pre>';
    print_r($r);
    echo '</pre>';
}