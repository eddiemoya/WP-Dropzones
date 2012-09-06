<?php 

class WidgetPress_Controller_Widgets {
	static public $widgets;
	/**
	 * 
	 */
	public function init(){
		self::add_actions();
	}

	/**
	 * 
	 */
	public function add_actions(){
		add_action('init', array(__CLASS__,'register_post_type') );
		add_action('widgets_init', array(__CLASS__, 'get_all_widgets'), 100);
		add_filter('widgetpress_before_form_fields', array(__CLASS__, 'widgets_before_form_fields'), 10);
		//add_filter('widgetpress_update_filter', array(__CLASS__, 'widget_update_filter'), 10, 2);
		add_action('wp_ajax_widgetpress_save_widget', array(__CLASS__, 'save_widget_meta'));
		add_action('wp_ajax_widgetpress_add_widget', array(__CLASS__, 'add_widget'));
		add_action('wp_ajax_widgetpress_sort_widget', array(__CLASS__, 'sort_widget'));
	}

	public function sort_widget(){
		$widget_ID = $_POST['widget_ID'];
		$id = $_POST['dropzone_id'];
		$type = $_POST['dropzone_type'];
		$order = $_POST['order'];

		update_post_meta($widget_ID, 'widgetpress_order_'.$type.'_'.$id, (int)$order);
		//echo json_encode($order+1);
		exit();


	}
	public function add_widget(){
		$widget_ID = (!empty($_POST['widget_ID'])) ? $_POST['widget_ID'] : null ;
		$widget_class = $_POST['widget_class'];
		$dropzone_id = $_POST['dropzone_id'];
		$dropzone_type = $_POST['dropzone_type'];

		$term = get_term($dropzone_id, $dropzone_type);

		$widget = new WidgetPress_Model_Widget(null, $term, $widget_class);
		echo json_encode($widget->get('post')->ID);
		exit();
	}

	public function save_widget_meta(){
		
		$widget_ID = $_POST['widget_ID'];
		$widget_class = $_POST['widget_class'];
		$widget_span = $_POST['widget_span'];
		$meta = array_values(wp_parse_args(urldecode($_POST['meta'])));
		$meta = $meta[0];
		$metadata = array();
		foreach($meta as $index => $kvpair){

			foreach($kvpair as $key => $value){
				$metadata[$key] = $value;
			}

		}
		//$metadata['widgetpress_span'] = $_POST['widgetpress_span'];
		update_post_meta($widget_ID, 'widgetpress_span', $widget_span);
		$widget = new WidgetPress_Model_Widget($widget_ID, null, $widget_class);
		
		//$metadata = apply_filters('widgetpress_update_filter', $widget->get('meta'), $meta);
		
		$widget->update($metadata);
		//echo json_encode($_POST);
		exit();
	}
	
	/**
	 * 
	 */
	public function get_all_widgets(){

		global $wp_widget_factory;
		$widgets = $wp_widget_factory->widgets;

		usort( $widgets, create_function( '$a, $b', 'return strnatcasecmp( $a->name, $b->name );' ) );

		$all_widgets = array();
		foreach($widgets as &$widget){
			$all_widgets[] = new WidgetPress_Model_Widget(null, null, $widget);
		}

		self::$widgets = $all_widgets;
	}

	public function get_widgets($dropzone_term, $tax = 'dropzone'){

		$term = (is_object($dropzone_term)) ? $dropzone_term : get_term($dropzone_term, $tax);
		$widgets = array();
		if(!empty($term) && !is_wp_error($term)){

			$widgets_posts = get_posts(
				array(
					'post_type' => array('widget'),
					'meta_key'	=> "widgetpress_order_".$tax."_".$term->term_id,
					'orderby' 	=> 'meta_value_num',
					'order'		=> 'ASC',
					'tax_query' => 
					array(
						array(
							'taxonomy' => $tax,
							'field' => 'id',
							'terms' => array($term->term_id)
						)
					)
				)
			);
			foreach($widgets_posts as $post){
				$widgets[] = new WidgetPress_Model_Widget($post);
			}

		} else {
			$widgets = false;
		}
		return $widgets;
	}

    // function widgets_before_form_fields($widget) {
    //     //$instance['widget_classname'] = $widget->widget_options['classname'];
    //     //print_pre($widget);
    //     $spans = array(
    //         'span12' => '100%',
    //         'span9' => '75%',
    //         'span8' => '66%',
    //         'span6' => '50%',
    //         'span4' => '33%',
    //         'span3' => '25%'
    //     );

    //     $borders[] = array(
    //         'field_id' => 'border-left',
    //         'type' => 'checkbox',
    //         'label' => 'Left Border',
    //     );

    //     $borders[] = array(
    //         'field_id' => 'border-right',
    //         'type' => 'checkbox',
    //         'label' => 'Right Border',
    //     );


    //     //self::form_fields($widget, $spans, $instance);
    //     // self::form_fields($widget, $borders, $instance, true);
    //     //echo "<pre>";print_r($widget);echo "</pre>";

    //     return $instance;
    // }

    function widget_update_filter( $instance, $new_instance ) {
        $instance['span'] = $new_instance['span'];
        $instance['border-left'] = $new_instance['border-left'];
        $instance['border-right'] = $new_instance['border-right'];
        
        return $instance;
    }

	/**
	 * 
	 */
	public function register_post_type(){
		$labels = array(
			'name' => _x('Widgets', 'post type general name'),
			'singular_name' => _x('Widget', 'post type singular name'),
			'add_new' => _x('Create New', 'book'),
			'add_new_item' => __('Create New Widget'),
			'edit_item' => __('Edit Widget'),
			'new_item' => __('New Widget'),
			'all_items' => __('All Widgets'),
			'view_item' => __('View Widget'),
			'search_items' => __('Search Widgets'),
			'not_found' =>  __('No widgets found'),
			'not_found_in_trash' => __('No widgets found in Trash'), 
			'parent_item_colon' => '',
			'menu_name' => __('Widgets')
		);


		$args = array(
			'label'	=> 'widgets',
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => false,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => false,
			'rewrite' => false,
			'capability_type' => 'post',
			'has_archive' => false, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'custom-fields' )
		); 

		register_post_type('widget', $args);
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
            $label = (!isset($label)) ? null : $label;
            $options = (!isset($options)) ? null : $options;
            
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
            <label for="<?php echo $field_id; ?> "><?php echo $label; ?>: </label> <?php
        }
        switch ($type){
            
            case 'text': ?>
                    <input type="text" id="<?php echo $widget->get_field_id( $field_id ); ?>" style="<?php echo $style; ?>" class="widefat" name="<?php echo $widget->get_field_name( $field_id ); ?>" value="<?php echo $instance[$field_id]; ?>" />
                <?php break;
            
            
            case 'hidden': ?>
                    <input id="<?php echo $widget->get_field_id( $field_id ); ?>" type="hidden" style="<?php echo (isset($style)) ? $style : ''; ?>" class="widefat" name="<?php echo $widget->get_field_name( $field_id ); ?>" value="<?php echo $instance[$field_id]; ?>" />
                <?php break;
            
            case 'select': ?>
                    <select id="<?php echo $field_id; ?>" class="widefat <?php echo $field_id; ?>" name="<?php echo $field_id; ?>">
                        <?php
                            foreach ( $options as $value => $label ) :  ?>
                        
                                <option value="<?php echo $value; ?>" <?php if(isset($instance[$field_id])) selected($value, $instance[$field_id]) ?>>
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


	public function display_dropzones($template = 'dropzones'){

		ob_start();?>
        	<section class="dropzones span12">
        <?php $before_dropzones = apply_filters('widgetpress_before_dropzones', ob_get_clean(), $dropzone);

        echo $before_dropzones;


		$dropzones = WidgetPress_Controller_Dropzones::get_dropzones('dropzone');
		if(!empty($dropzones)){
            foreach($dropzones as $dropzone){

            	//echo "<pre>";print_r($dropzone);echo "</pre>";
           
      			$dzmeta = $dropzone->get('meta');
				$dzspan = $dzmeta['dropzone_span'];
			

            	ob_start();?>
            		<section class="dropzone <?php echo $dropzone->get('term')->slug; ?> <?php echo $dzspan; ?>">
            	<?php $before_dropzone = apply_filters('widgetpress_before_dropzone', ob_get_clean(), $dropzone);

            	echo $before_dropzone;

 				$widgets = WidgetPress_Controller_Widgets::get_widgets($dropzone->get('term'));
 				foreach($widgets as $widget){
 					$meta = $widget->get('meta');
 					$span = $meta['widgetpress_span'];
 					$classname = $meta['widgetpress_widget_classname'];

	 
	            	$before_widget = apply_filters('widgetpress_before_widget', "<article class='widget content-container {$span} {$classname}'>", $dropzone, $widget);
					$after_widget = apply_filters('widgetpress_after_widget', "</article>", $dropzone, $widget);

	     
	            	$before_title = apply_filters('widgetpress_before_title', "<header class='content-header'><h3>", $dropzone, $widget);
					$after_title = apply_filters('widgetpress_after_title', "</h3></header>", $dropzone, $widget);

					$args = array(
						'before_widget' => $before_widget,
						'after_widget' => $after_widget,
						'before_title' => $before_title,
						'after_title' => $after_title
					);


	       			$widget->get('class')->widget($args, $meta);
	            	//echo "<pre>";print_r($widget);echo "</pre>";




	




 				}

                ob_start();?>
            		</section>
            	<?php $after_dropzone = apply_filters('widgetpress_after_dropzone', ob_get_clean(), $dropzone);

            	echo $after_dropzone;
            }
        } 

        ob_start();?>
    		</section>
    	<?php $after_dropzones = apply_filters('widgetpress_after_dropzones', ob_get_clean(), $dropzone);

    	echo $after_dropzones; 

	}

}