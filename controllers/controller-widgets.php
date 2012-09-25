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
		add_filter('widgetpress_update_filter', array(__CLASS__, 'widget_update_filter'), 10, 2);
		add_action('wp_ajax_widgetpress_save_widget', array(__CLASS__, 'save_widget_meta'));
		add_action('wp_ajax_widgetpress_delete_widget', array(__CLASS__, 'delete_widget'));
		add_action('wp_ajax_widgetpress_remove_widget', array(__CLASS__, 'remove_widget'));
		add_action('wp_ajax_widgetpress_add_widget', array(__CLASS__, 'add_widget'));
		add_action('wp_ajax_widgetpress_sort_widget', array(__CLASS__, 'sort_widget'));
		add_action('wp_ajax_fucking_instanity', array(__CLASS__, 'fucking_instanity'));
		

	}

	/**
	 * Seriously,... fuck this.This
	 * This is a script that allows for the migration of widgets - taxonomy term id's get out of sync
	 * if widgets are improted through WXR
	 */
	public function fucking_instanity(){
		$terms = Section_Front::get_terms_by_post_type('dropzone', 'widget');
		$term_ids = wp_list_pluck($terms, 'term_id');
		$posts = array();
		 
			$posts = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => array('widget'),
				'tax_query' => array(
					array(
						'taxonomy' => 'dropzone',
						'terms' => $term_ids,
						'field' => 'id',
			))));
			foreach($posts as $post){
			
				$post->meta = get_post_custom($post->ID, true);

				foreach($post->meta as $key => $meta){
					if(is_array($meta)){
						$post->meta[$key] = $meta[0];
						$meta = $meta[0];
					}

					if(strstr($key, 'widgetpress_order_dropzone_')){
						$widget_dropzones = wp_get_object_terms($post->ID, 'dropzone');

						$old_key = $key;
						$new_key = array();
						foreach($widget_dropzones as $dz){

							 $new_key = 'widgetpress_order_dropzone_'.$dz->term_id;

							update_post_meta($post->ID, $new_key, $meta);
							delete_post_meta($post->ID, $old_meta, $meta);
						}

					}
				}
			}

		echo json_encode(array($new_key, $old_key, $meta));
		exit();


	}
	public function sort_widget(){
		$widget_ID = $_POST['widget_ID'];
		$id = $_POST['dropzone_id'];
		$type = $_POST['dropzone_type'];
		$order = $_POST['order'];

		update_post_meta($widget_ID, 'widgetpress_order_'.$type.'_'.$id, (int)$order);
		echo json_encode(array($order, $widget_ID));
		exit();

	}
	public function add_widget(){
		$widget_ID = (!empty($_POST['widget_ID'])) ? $_POST['widget_ID'] : null ;
		$widget_class = $_POST['widget_class'];
		$dropzone_id = $_POST['dropzone_id'];
		$dropzone_type = $_POST['dropzone_type'];

		$term = get_term($dropzone_id, $dropzone_type);

		$widget = new WidgetPress_Model_Widget($widget_ID, $term, $widget_class);
		echo json_encode($widget->get('post')->ID);
		exit();
	}

	public function delete_widget(){
		wp_delete_post($_POST['widget_ID']);
		exit();
	}

	public function remove_widget(){
		$widget_id = $_POST['widget_ID'];
		$dropzone_id = $_POST['dropzone_id'];
		$dropzone_type = $_POST['dropzone_type'];

		$widget = get_post($widget_id);

		$widget_dropzones = wp_get_object_terms($widget_id, $dropzone_type, array('fields' => 'ids'));

		foreach((array)$widget_dropzones as $index => $dropzone){
			$widget_dropzones[$index] = (int)$dropzone;
			if($dropzone == $dropzone_id){
				unset($widget_dropzones[$index]);
			}
		}
		$terms = wp_set_object_terms($widget_id, $widget_dropzones, $dropzone_type);
		exit();
	}

	public function save_widget_meta(){
		
		$widget_ID = $_POST['widget_ID'];
		$widget_class = $_POST['widget_class'];
		$widget_span = $_POST['widget_span'];

		$params = array();
		parse_str($_POST['meta'], $params);
		$meta = array_values($params);
		$meta = $meta[0];
		$metadata = array();
		foreach($meta as $index => $kvpair){

			foreach($kvpair as $key => $value){
				$metadata[$key] = stripslashes(stripslashes($value));
			}

		}
		//$metadata['widgetpress_span'] = $_POST['widgetpress_span'];
		update_post_meta($widget_ID, 'widgetpress_span', $widget_span);
		$widget = new WidgetPress_Model_Widget($widget_ID, null, $widget_class);
		
		//$metadata = apply_filters('widgetpress_update_filter', $widget->get('meta'), $meta);
		
		$widget->update($metadata);
		 //echo "<pre>";print_r($metadata);echo "</pre>";;
		exit();
	}
	
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
					'posts_per_page' => -1,
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
			'exclude_from_search'	=> true,
			'supports' => array( 'title', 'thumbnail', 'custom-fields' )
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

    /**
     * Holy filters batman!
     */
	public function display_dropzones($post_id = null){

		global $wp_the_query, $paged, $wp_query;

		//Magical fucking resetting of a query that _never_ took place FUCKING MAGIC BITCH!
		// $wp_the_query->query['post_type'] = array($wp_the_query->query_vars['old_post_type']);
		// $wp_the_query->query['category'] = $wp_the_query->query_vars['old_category'];
		// $wp_the_query->query['paged'] = $wp_the_query->query_vars['old_paged'];
		// $wp_the_query->query['orderby'] = 'date';
		// unset($wp_the_query->query['meta_key']);
		// $wp_the_query->query($wp_the_query->query);
		// echo "<pre>";print_r($wp_the_query);echo "</pre>";


		$query['post_type'] = $wp_the_query->query_vars['old_post_type'];
		$query['category_name'] = $wp_the_query->query_vars['old_category'];
		$query['paged'] = ($wp_the_query->query_vars['old_paged'] > 0) ? $wp_the_query->query_vars['old_paged'] : 1 ;
		$query['orderby'] = 'date';
	


		// unset($wp_the_query->query['meta_key']);
		//query_posts($query);
		$wp_the_query = new WP_Query($query);
		//echo "<pre>";print_r($wp_the_query);echo "</pre>";


		


		//if(empty($post_id)){
			$dropzones = WidgetPress_Controller_Dropzones::get_dropzones('dropzone');
		//} else {
			//$dropzones = WidgetPress_Cont
		//}
		echo apply_filters('widgetpress_before_dropzones_container', '<section class="dropzones span12">', $dropzones);

		if(!empty($dropzones)){
            foreach($dropzones as $dropzone){
           
           		//echo "<pre>";print_r($dropzone);echo "</pre>";
      			$dzmeta = $dropzone->get('meta');
				$dzspan = $dzmeta['dropzone_span'];
				$dzborderl = ($dzmeta['dropzone_borderleft'] === "true") ? 'border-left' : '';
            	
            	$before_dropzone = "<section class='dropzone dropzone_{$dropzone->get('term')->slug} {$dzspan}'>";

            	$inner_wrapper_before = "<section class='dropzone-inner-wrapper {$dzborderl}'>";

            	echo apply_filters('widgetpress_before_dropzone', $before_dropzone, $dropzone);
            	echo apply_filters('widgetpress_inner_wrapper_before', $inner_wrapper_before, $dropzone);

 				$widgets = WidgetPress_Controller_Widgets::get_widgets($dropzone->get('term'));

 				foreach($widgets as $widget){
 					//echo "rrr|";
 					$meta = $widget->get('meta');
 					$span = $meta['widgetpress_span'];
 					$classname = $widget->get('class')->widget_options['classname'];

	 			// 	echo "<pre>";print_r($span);echo "</pre>";
	             	$before_widget = apply_filters('widgetpress_before_widget', "<article class='widget content-container {$span} {$classname}'>", $dropzone, $widget);
					$after_widget = apply_filters('widgetpress_after_widget', "</article>", $dropzone, $widget);

	    			$before_title = apply_filters('widgetpress_before_title', "<header class='content-header'><h3>", $dropzone, $widget);
					$after_title = apply_filters('widgetpress_after_title', "</h3></header>", $dropzone, $widget);
					//echo "<pre>";print_r(array($meta, $span, $classname));echo "</pre>";
					$args = array(
						'before_widget' => $before_widget,
						'after_widget' => $after_widget,
						'before_title' => $before_title,
						'after_title' => $after_title
					);
					//echo "<pre>";print_r($args);echo "</pre>";

					if(is_object($widget->get('class'))){
	    				$widget->get('class')->widget($args, $meta);
	    			}
	    //         	//echo "<pre>";print_r($widget);echo "</pre>";

 				}
 				echo apply_filters('widgetpress_inner_wrapper_after', "<div class='clearfix'></div></section>", $dropzone);
                echo  apply_filters('widgetpress_after_dropzone', "</section>", $dropzone);
            }
        } 
        echo apply_filters('widgetpress_after_dropzones_container', "</section>", $dropzone);
	}


	/**
	* Duplicate of above, needs to be merged. The above needs to not try to get 'old_' query data on single page loads - but sections are single page loads.
	*/
	public function display_dropzone($slug  = null){

		
		$post = new WP_Query(array('name'=>$slug, 'post_type'=>'section'));
		//$post = get_dropzone_post($term, 'section');
		//echo "<pre>";print_r($post);echo "</pre>";
		
		$dropzones = WidgetPress_Controller_Dropzones::get_dropzones('dropzone', $post->post->ID);

			
		
		//$dropzones = new WP_Query(array('pagename'=>$slug));
		//echo "<pre>";print_r($dropzones);echo "</pre>";
		//echo apply_filters('widgetpress_before_dropzones_container', '<section class="dropzones span12">', $dropzone);


		foreach($dropzones as $dropzone){
			//echo apply_filters('widgetpress_before_dropzone', $before_dropzone, $dropzone);
            //echo apply_filters('widgetpress_inner_wrapper_before', $inner_wrapper_before, $dropzone);

            $widgets = WidgetPress_Controller_Widgets::get_widgets($dropzone->get('term'));

            foreach($widgets as $widget){
				$meta = $widget->get('meta');
				$span = $meta['widgetpress_span'];
				$classname = $widget->get('class')->widget_options['classname'];

             	$before_widget = apply_filters('widgetpress_before_widget', "<article class='widget content-container {$span} {$classname}'>", $dropzone, $widget);
				$after_widget = apply_filters('widgetpress_after_widget', "</article>", $dropzone, $widget);

    			$before_title = apply_filters('widgetpress_before_title', "<header class='content-header'><h3>", $dropzone, $widget);
				$after_title = apply_filters('widgetpress_after_title', "</h3></header>", $dropzone, $widget);
				//echo "<pre>";print_r(array($meta, $span, $classname));echo "</pre>";
				$args = array(
					'before_widget' => $before_widget,
					'after_widget' => $after_widget,
					'before_title' => $before_title,
					'after_title' => $after_title
				);
				if(is_object($widget->get('class'))){
    				$widget->get('class')->widget($args, $meta);
    			}
            	//echo "<pre>";print_r($widget);echo "</pre>";
            }
			

			//echo apply_filters('widgetpress_inner_wrapper_after', "</section>", $dropzone);
            //echo  apply_filters('widgetpress_after_dropzone', "</section>", $dropzone);
		}
		
		//echo apply_filters('widgetpress_after_dropzones_container', "</section>", $dropzone);
	}

}


