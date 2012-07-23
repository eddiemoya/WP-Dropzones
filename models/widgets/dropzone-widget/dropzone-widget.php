<?php /*
Plugin Name: Dropzone Widget
Description: Widget that spawns new sidebars.
Version: 0.8
Author: Eddie Moya
 */


class Dropzone_Widget extends WP_Widget {
      
    /**
     * Name for this widget type, should be human-readable - the actual title it will go by.
     * 
     * @var string [REQUIRED]
     */
    var $widget_name = 'Dropzone Widget';
   
    /**
     * Root id for all widgets of this type. Will be automatically generate if not set.
     * 
     * @var string [OPTIONAL]. FALSE by default.
     */
    var $id_base = 'dropzone_widget';
    
    /**
     * Shows up under the widget in the admin interface
     * 
     * @var string [OPTIONAL]
     */
    private $description = 'Spawn new dropzones/sidebars.';

    /**
     * CSS class used in the wrapping container for each instance of the widget on the front end.
     * 
     * @var string [OPTIONAL]
     */
    private $classname = 'dropzone-widget';
    
    /**
     *
     * @var type 
     */
    private $width = '250';
    
    
    /**
     * Never used - does nothing.
     * @var type 
     */
    private $height = '200';
    
    
    /**
     *
     * @var type 
     */
    public static $dropzones;
    
    /**
     * Be careful to consider PHP versions. If running PHP4 class name as the contructor instead.
     * 
     * @author Eddie Moya
     * @return void
     */
    public function Dropzone_Widget(){
        $widget_ops = array(
            'description' => $this->description,
            'classname' => $this->classname
        );
        
        $control_options = array(
            'height' => $this->height,
            'width' => $this->width
        );
        
        self::$dropzones = apply_filters('wpdz_dropzones', array());

        parent::WP_Widget($this->id_base, $this->widget_name, $this->widget_ops, $control_options);
    }
    
    /**
     * Self-registering widget method.
     * 
     * This can be called statically.
     * 
     * @author Eddie Moya
     * @return void
     */
    public function register_widget() {
        add_action('widgets_init', create_function( '', 'register_widget("' . __CLASS__ . '");' ));
    }
    
    /**
     * The front end of the widget. 
     * 
     * Do not call directly, this is called internally to render the widget.
     * 
     * @author [Widget Author Name]
     * 
     * @param array $args       [Required] Automatically passed by WordPress - Settings defined when registering the sidebar of a theme
     * @param array $instance   [Required] Automatically passed by WordPress - Current saved data for the widget options.
     * @return void 
     */
    public function widget( $args, $instance ){
        extract($args);
        extract($instance);
        
        //register_sidebar($args);
        $sidebar_uid = WPDZ_Sidebar::get_sidebar_id($instance['wpdz_dropzone_type'] . _ . $this->number);
        
        
        
        // if($instance['wpdz_dropzone_type'] == 'custom') {
        //     $classes[] = $instance['span'];
        //     if(isset($instance['border-left']))
        //         $classes[] = 'border-left';
            
        //     if(isset($instance['border-right']))
        //         $classes[] = "border-left";
        // }


        
        //$before_widget = $this->add_class($before_widget, implode(' ',$classes));

        echo $before_widget;
        dynamic_sidebar($sidebar_uid);
        echo $after_widget;
        
    }
    
    /**
     *
     * @param type $tag
     * @param type $class
     * @return type 
     */
    function add_class($string, $class) {
        return str_replace('$span', $class, $string);
        
    }
    
    /**
     * Data validation. 
     * 
     * Do not call directly, this is called internally to render the widget
     * 
     * @author [Widget Author Name]
     * 
     * @uses esc_attr() http://codex.wordpress.org/Function_Reference/esc_attr
     * 
     * @param array $new_instance   [Required] Automatically passed by WordPress
     * @param array $old_instance   [Required] Automatically passed by WordPress
     * @return array|bool Final result of newly input data. False if update is rejected.
     */
    public function update($new_instance, $old_instance){
        
        /* Lets inherit the existing settings */
        $instance = $old_instance;
   
        
        /**
         * Sanitize each option - be careful, if not all simple text fields,
         * then make use of other WordPress sanitization functions, but also
         * make use of PHP functions, and use logic to return false to reject
         * the entire update. 
         * 
         * @see http://codex.wordpress.org/Function_Reference/esc_attr
         */
        foreach($new_instance as $key => $value){
            $instance[$key] = esc_attr($value);
            
        }
        
        
        foreach($instance as $key => $value){
            if($value == 'on' && !isset($new_instance[$key])){
                unset($instance[$key]);
            }

        }
        // $instance['args'] = self::$dropzones[$new_instance['wpdz_dropzone_type']];
        // $instance['title'] = $instance['args']['name'];
        // $instance['wpdz_dropzone_type'] = $new_instance['wpdz_dropzone_type'];

        $instance['args'] = self::$dropzones['custom'];
        $instance['title'] = $new_instance['title'];
        $instance['wpdz_dropzone_type'] = 'custom';
        
        
       // $instance['dropzone'] = self::$dropzones[$instance['wpdz_dropzone_type']];
        return $instance;
    }
    
    /**
     * Generates the form for this widget, in the WordPress admin area.
     * 
     * The use of the helper functions form_field() and form_fields() is not
     * neccessary, and may sometimes be inhibitive or restrictive.
     * 
     * @author Eddie Moya
     * 
     * @uses wp_parse_args() http://codex.wordpress.org/Function_Reference/wp_parse_args
     * @uses self::form_field()
     * @uses self::form_fields()
     * 
     * @param array $instance [Required] Automatically passed by WordPress
     * @return void 
     */
    public function form($instance){
        
        /* Setup default values for form fields - associtive array, keys are the field_id's */
        $defaults = array();
        
        /* Merge saved input values with default values */
        $instance = wp_parse_args((array) $instance, $defaults);

        // foreach(self::$dropzones as $dropzone){
        //     $options[$dropzone['id']] = $dropzone['name'];
        // }

        /* Examples of input fields one at a time. */
        $this->form_field('title', 'text', 'Widget Label (shown on admin side only)', $instance);
        //$this->form_field('wpdz_dropzone_type', 'select', 'Select a dropzone', $instance, $options);
        
        // if('custom' == $instance['wpdz_dropzone_type']){
        //     $show_options[] = array(
        //             'field_id' => 'span',
        //             'type'      => 'select',
        //             'label' =>  'Widget Width',
        //             'options' => array(
        //                 'span3' =>  '25%',
        //                 'span4'  => '33%',
        //                 'span6' => '50%',
        //                 'span8' => '66%',
        //                 'span9' => '75%',
        //                 'span12' => '100%'
        //             )
        //     );
            
        //     $show_options[] = array(
        //             'field_id' => 'border-left',
        //             'type'      => 'checkbox',
        //             'label' =>  'Left Border',
        //     );
            
        //     $show_options[] = array(
        //             'field_id' => 'border-right',
        //             'type'      => 'checkbox',
        //             'label' =>  'Right Border',
        //     );
            
        //     $this->form_fields($show_options, $instance);
        //}
        

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
    public function form_fields($fields, $instance, $group = false){
        
        if($group) {
            echo "<p>";
        }

        foreach($fields as &$field){
            extract($field);
            
            $this->form_field($field_id, $type, $label, $instance, $options);
        }

        if($group){
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
    public function form_field($field_id, $type, $label, $instance, $options = array(), $group = false){
  
        if(!$group){
             echo "<p>";
        }
        
        if(!empty($label) && 'checkbox' != $type){ ?>
            <label for="<?php echo $this->get_field_id( $field_id ); ?>"><?php echo $label; ?>: </label> <?php
        }
        switch ($type){
            
            case 'text': ?>
                    <input type="text" id="<?php echo $this->get_field_id( $field_id ); ?>" style="<?php echo $style; ?>" class="widefat" name="<?php echo $this->get_field_name( $field_id ); ?>" value="<?php echo $instance[$field_id]; ?>" />
                <?php break;
            
            
            case 'hidden': ?>
                    <input id="<?php echo $this->get_field_id( $field_id ); ?>" type="hidden" style="<?php echo $style; ?>" class="widefat" name="<?php echo $this->get_field_name( $field_id ); ?>" value="<?php echo $instance[$field_id]; ?>" />
                <?php break;
            
            case 'select': ?>
                    <select id="<?php echo $this->get_field_id( $field_id ); ?>" class="widefat" name="<?php echo $this->get_field_name($field_id); ?>">
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
                
                ?>                    <textarea class="widefat" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>" id="<?php echo $this->get_field_id($field_id); ?>" name="<?php echo $this->get_field_name($field_id); ?>"><?php echo $instance[$field_id]; ?></textarea>
                <?php break;
            
            case 'radio' :
                /**
                 * Need to figure out how to automatically group radio button settings with this structure.
                 */
                ?>
                    
                <?php break;
            
            case 'checkbox' : ?>
                    <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id($field_id); ?>" name="<?php echo $this->get_field_name($field_id); ?>"<?php checked( (!empty($instance[$field_id]))); ?> />
                	<label for="<?php echo $this->get_field_id( $field_id ); ?>"><?php echo $label; ?></label>
                <?php
        }
        
        if(!$group){
             echo "</p>";
        }
    }
}