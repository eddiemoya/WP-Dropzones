<?php
/**
 * Description of wpdz-settings-metabox
 *
 * @author Eddie Moya
 * @uses WPDZ_Helper::include_view();
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
     * @param type $args 
     */
    public function __construct($args = array()){
        global $post;
        //print_r(get_post_meta($post->ID, 'wpdz_widgets-temp', true));
        //Get all properties of this object
        $properties = get_class_vars(__CLASS__);

        // Allow only pre-existing properties to be set by arguments to the cunstructor
        foreach($properties as $key => $value){
            if(isset($args[$key])){
                //echo 'value: ' . $args[$key];
                $this->$key = $args[$key]; 
            }
        }
        
        $this->id = $this->get_sidebar_id();
        
        $this->register_sidebar();
        
        $this->widgets  = $this->get_sidebar_widgets();    
        
    }
 
    
    /**
     * @global type $post_id
     * @return type 
     */
    public function get_sidebar_id() {
        
        if(empty($this->id)){
            $this->id = str_replace(' ' , '-', strtolower($this->name));
        }
        
        return 'wpdz-sidebar-'. $this->id. '-' . $_GET['post'];
    }

    /**
     * Retreives widgets from post meta
     * 
     * @global type $post
     * @return type 
     */
     function get_sidebar_widgets() {
        global $post, $wp_registered_sidebars, $wp_registered_widgets;
        
        $widgets = get_post_meta($_GET['post'], 'wpdz_widgets-temp', true);
        
        if(empty($widgets)){
            $widgets = wp_get_sidebars_widgets();
        }
        
        wp_set_sidebars_widgets($widgets);
        
        return (!empty($widgets)) ? $widgets : wp_get_widget_defaults();
    }
    
    /**
     * Registers a 'ghost' sidebar for this post, which will only exist
     * globally for the duration of this page view.
     */
    public function register_sidebar() {
//         global $post, $wp_registered_sidebars;
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
     * @return type 
     */
    public function get_wrap_class() {
        
        $wrap_class = 'widgets-holder-wrap';

        if (!empty($this->class))
            $wrap_class .= ' sidebar-' . $this->class;

        return $wrap_class;
    }

    /**
     * 
     */
    public function is_outofdate(){
        global $post;
        $temp_widgets   = get_post_meta($post->ID, 'wpdz_widgets-temp', true);
        $saved_widgets  = get_post_meta($post->ID, 'wpdz_widgets', true);
        
        $outofdate = false;
        
        if( !empty($temp_widgets) && $temp_widgets != $saved_widgets ) {
            $outofdate = true;
        }

        return $outofdate;
    }
    
    public function add_actions(){
            add_action('wp_ajax_wpdz-save-widget', array(__CLASS__, 'save_widget'));
            add_action('wp_ajax_wpdz-save-order', array(__CLASS__, 'save_widget_order'));  
            add_action('widgets_init', array($this, 'register_sidebar'));  


    }
    /**
     * 
     */
    public function save_widget() {
         global $post,  $wp_registered_widgets, $wp_registered_sidebars;
        update_post_meta($_POST['post_id'], 'wpdz_widgets-temp', wp_get_sidebars_widgets());
        die(json_encode(get_post_meta($_POST['post_id'], 'wpdz_widgets-temp', true)));
    }

}
