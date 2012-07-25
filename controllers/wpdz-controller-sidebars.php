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
        add_filter('dynamic_sidebar_params',    array(__CLASS__, 'add_classes') );

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
        add_action('wp_ajax_wpdz-save-widget',  array(WPDZ_Sidebar, 'ajax_save_widget'));
        add_action('wp_ajax_refresh-metabox',   array(WPDZ_Controller_Metaboxes, 'refresh_metabox'));
        add_action('widgets_admin_page',        array(__CLASS__, 'hide_sidebars'));
        add_filter('widget_form_callback',      array(__CLASS__, 'widgets_form_extend'), 10, 2);
        add_filter('widget_update_callback',    array(__CLASS__, 'widget_update'), 10, 2 );
        

        foreach ((array) self::$sidebars as $sidebar) {
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
                'before_widget' => '<ul class="dropzone">',
                'after_widget' => '</ul>',
                'before_title' => '<h4>',
                'after_title' => '</h4>'
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
    * Hides dropzone sidebars when not on single pages in the admin (the real widgets place especially)
    * 
    * @global type $wp_registered_sidebars 
    */
    public function hide_sidebars(){
        global $wp_registered_sidebars;

        //print_r($wp_registered_sidebars); 

        //To test as admin, just put junk text for the cap.
        if(is_admin() && !is_single()){

            //This sidebar name is from the twenty-ten theme.
            unset($wp_registered_sidebars['wpdz-sidebar-layout-manager-']);

            foreach($wp_registered_sidebars as $key => $sb){
                if(strstr($key, 'wpdz-')){
                    unset($wp_registered_sidebars[$key]);
                }
            }

        }
    }

    /**
     * 
     */
    public function display_dropzones(){
        self::init();
        foreach ((array) self::$sidebars['layout-manager'] as $layout_manager) {
            dynamic_sidebar($layout_manager->id);
        }
    }

    function widgets_form_extend($instance, $widget) {
        $instance['widget_classname'] = $widget->widget_options['classname'];
        //print_pre($widget);
        $spans[] = array(
            'field_id' => 'span',
            'type' => 'select',
            'label' => 'Widget Width',
            'options' => array(
                'span3' => '25%',
                'span4' => '33%',
                'span6' => '50%',
                'span8' => '66%',
                'span9' => '75%',
                'span12' => '100%'
            )
        );

        $borders[] = array(
            'field_id' => 'border-left',
            'type' => 'checkbox',
            'label' => 'Left Border',
        );

        $borders[] = array(
            'field_id' => 'border-right',
            'type' => 'checkbox',
            'label' => 'Right Border',
        );

        $borders[] = array(
            'field_id' => 'widget_classname',
            'type' => 'hidden',
            'label' => '',
        );

        self::form_fields($widget, $spans, $instance);
        self::form_fields($widget, $borders, $instance, true);

        return $instance;
    }

    function widget_update( $instance, $new_instance ) {
        $instance['span'] = $new_instance['span'];
        $instance['border-left'] = $new_instance['border-left'];
        $instance['border-right'] = $new_instance['border-right'];
        
        return $instance;
    }

    public function add_classes( $params ) {
        global $wp_registered_widgets;
        $widget_id  = $params[0]['widget_id'];
        $widget_obj = $wp_registered_widgets[$widget_id];
        $widget_opt = get_option($widget_obj['callback'][0]->option_name);
        $widget_num = $widget_obj['params'][0]['number'];

        $classes[] = $widget_opt[$widget_num]['span'];
        $classes[] = $widget_opt[$widget_num]['widget_classname'];
        
        if(isset($widget_opt[$widget_num]['border-left']))
            $classes[] = 'border-left';
        
        if(isset($widget_opt[$widget_num]['border-right']))
            $classes[] = "border-left";

        if ( isset($classes) && !empty($classes) )
            $classes = implode(' ',$classes);
            $params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$classes} ", $params[0]['before_widget'], 1 );

        return $params;
    }


    
    /**
     * Helper function - does not need to be part of widgets, this is custom, but 
     * is helpful in generating multiple input fields for the admin form at once. 
     * 
     * This is a wrapper for the singular form_field() function.
     * 
     * @author Eddie Moya
     * 
     * @uses self::form_fields()
     * 
     * @param array $fields     [Required] Nested array of field settings
     * @param array $instance   [Required] Current instance of widget option values.
     * @return void
     */
    public function form_fields($widget, $fields, $instance, $group = false){
        
        if($group) {
            echo "<p>";
        }

        foreach($fields as &$field){
            extract($field);
            
            self::form_field($widget, $field_id, $type, $label, $instance, $options, $group);
        }

        if($group) {
            echo "</p>";
        }
    }
    
    /**
     * Helper function - does not need to be part of widgets, this is custom, but 
     * is helpful in generating single input fields for the admin form at once. 
     *
     * @author Eddie Moya
     * 
     * @uses get_field_id() (No Codex Documentation)
     * @uses get_field_name() http://codex.wordpress.org/Function_Reference/get_field_name
     * 
     * @param string $field_id  [Required] This will be the CSS id for the input, but also will be used internally by wordpress to identify it. Use these in the form() function to set detaults.
     * @param string $type      [Required] The type of input to generate (text, textarea, select, checkbox]
     * @param string $label     [Required] Text to show next to input as its label.
     * @param array $instance   [Required] Current instance of widget option values. 
     * @param array $options    [Optional] Associative array of values and labels for html Option elements.
     * 
     * @return void
     */
    public function form_field($widget, $field_id, $type, $label, $instance, $options = array(), $group = false){
  
        if(!$group) {
            echo "<p>";
        }
        
        if(!empty($label) && 'checkbox' != $type){ ?>
            <label for="<?php echo $widget->get_field_id( $field_id ); ?>"><?php echo $label; ?>: </label> <?php
        }
        switch ($type){
            
            case 'text': ?>
                    <input type="text" id="<?php echo $widget->get_field_id( $field_id ); ?>" style="<?php echo $style; ?>" class="widefat" name="<?php echo $widget->get_field_name( $field_id ); ?>" value="<?php echo $instance[$field_id]; ?>" />
                <?php break;
            
            
            case 'hidden': ?>
                    <input id="<?php echo $widget->get_field_id( $field_id ); ?>" type="hidden" style="<?php echo $style; ?>" class="widefat" name="<?php echo $widget->get_field_name( $field_id ); ?>" value="<?php echo $instance[$field_id]; ?>" />
                <?php break;
            
            case 'select': ?>
                    <select id="<?php echo $widget->get_field_id( $field_id ); ?>" class="widefat" name="<?php echo $widget->get_field_name($field_id); ?>">
                        <?php
                            foreach ( $options as $value => $label ) :  ?>
                        
                                <option value="<?php echo $value; ?>" <?php selected($value, $instance[$field_id]) ?>>
                                    <?php echo $label ?>
                                </option><?php
                                
                            endforeach; 
                        ?>
                    </select>
                    
				<?php break;
                
            case 'textarea':
                
                $rows = (isset($options['rows'])) ? $options['rows'] : '16';
                $cols = (isset($options['cols'])) ? $options['cols'] : '20';
                
                ?>                    <textarea class="widefat" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>" id="<?php echo $widget->get_field_id($field_id); ?>" name="<?php echo $widget->get_field_name($field_id); ?>"><?php echo $instance[$field_id]; ?></textarea>
                <?php break;
            
            case 'radio' :
                /**
                 * Need to figure out how to automatically group radio button settings with this structure.
                 */
                ?>
                    
                <?php break;
            
            case 'checkbox' : ?>
                    <input type="checkbox" class="checkbox" id="<?php echo $widget->get_field_id($field_id); ?>" name="<?php echo $widget->get_field_name($field_id); ?>"<?php checked( (!empty($instance[$field_id]))); ?> />
                	<label for="<?php echo $widget->get_field_id( $field_id ); ?>"><?php echo $label; ?></label>
                <?php
        }
        
        if(!$group) {
            echo "</p>";
        }
    }

}

