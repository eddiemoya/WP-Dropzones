<?php 

/**
 * Note to self. Whenever using ->form(), it needs to be through the models ->update() so that $instance can be passed, be it from ajax or from post custom.
 */

//$widget = $widget->get('class');
$options = (object)$widget->get('class')->widget_options; 
//$ID = $widget->widget_ID;

//echo "<pre>";print_r($widget);echo "</pre>"; 
$meta = $widget->get('meta');
$span = $meta['widgetpress_span'];

$layout_span = null;

if(is_object($dropzone) && empty($span)){
	global $post_id; 
	$id = (int)$dropzone->get('term')->term_id - 1;
	$span = get_post_meta($post_id, 'widgetpress_dropzone_span_'.$id);
	$span = $span[0];
}

	?>

	<div id="widget-" class="widget ui-draggable"> 
	    <div class="widget-top">
	        <div class="widget-title-action">
	            <a class="widget-action hide-if-no-js" href="#available-widgets"></a>
	            <a class="widget-control-edit hide-if-js" href="/wp-admin/widgets.php?editwidget_class=<?php echo $widget_class; ?>">
	                <span class="edit">Edit</span>
	                <span class="add">Add</span>
	            </a>
	        </div>
	        <div class="widget-title">
	            <h4>
	                <?php echo $widget->get('class')->name; ?>
	                <span class="in-widget-title"></span>
	            </h4>
	        </div>
	    </div>

	    <div class="widget-inside">
	        <form action="" method="post">
	            <div class="widget-content">

	                <?php $widget->update(); ?>
	                <?php //do_action('widgetpress_after_form_fields'); ?>
	                <?php //$m = $widget->get('meta'); echo $m['widgetpress_order_dropzone_87']; //echo "<pre>";print_r($widget);echo "</pre>";
					        $spans = array(
					            'span12' => '100%',
					            'span9' => '75%',
					            'span8' => '66%',
					            'span6' => '50%',
					            'span4' => '33%',
					            'span3' => '25%'
					        );
	                ?>
	            </div>
	      
					<label for="widgetpress_span">Widget Width: </label>   
                    <select id="widgetpress_span" class="widefat widgetpress_span" name="widgetpress_span">
                        <?php
                            foreach ($spans as $value => $label ) :  ?>
                        
                                <option value="<?php echo $value; ?>" <?php selected($value, $span); ?>>
                                    <?php echo $label ?>
                                </option><?php
                                
                            endforeach; 
                        ?>
                    </select>


	            <input type="hidden" name="id_base" class="id_base" value="archives">
	            <input type="hidden" name="classname" class="classname" value="<?php echo $options->classname; ?>">
	            <input type="hidden" name="widget-class" class="widget-class" value="<?php echo get_class($widget->get('class')); ?>">
	            <input type="hidden" name="widget_ID" class="widget_ID" value="<?php echo $widget->get('post')->ID; ?>">

	            <div class="widget-control-actions">
	                <div class="alignleft">

	                	<a class="widget-control-remove" href="#remove">Remove</a> |<a class="widget-control-delete" href="#remove">Delete</a> |<?php edit_post_link('Edit', '','',$widget->get('post')->ID); ?> |<a class="widget-control-close" href="#close">Close</a>
	                    

	                </div>
	                <div class="alignright">
	                    <img src="http://com.loc/wp-admin/images/wpspin_light.gif" class="ajax-feedback" title="" alt="" style="visibility: hidden; ">
	                    <input type="submit" name="savewidget" id="widget-archives-__i__-savewidget" class="button-primary widget-control-save" value="Save">
	                </div>
	                <br class="clear">
	            </div>
	        </form>
	    </div>

	    <div class="widget-description">
	        <?php echo $options->description; ?>
	    </div>
	</div>
