<?php 


$options = (object)$widget->widget_options; 
$widget_class = get_class($widget); 

$widget = new $widget_class();
//echo "<pre>";print_r($widget);echo "</pre>"; 

if(is_subclass_of($widget_class, 'WP_Widget')){ 
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
	                <?php echo $widget->name; ?>
	                <span class="in-widget-title"></span>
	            </h4>
	        </div>
	    </div>

	    <div class="widget-inside">
	        <form action="" method="post">
	            <div class="widget-content">

	                <?php $widget->form(array()); ?>
	            </div>
	            
	            <input type="hidden" name="id_base" class="id_base" value="archives">
	            <input type="hidden" name="widget-width" class="widget-width" value="250">
	            <input type="hidden" name="widget-height" class="widget-height" value="200">
	            <input type="hidden" name="add_new" class="add_new" value="multi">
	            <input type="hidden" name="classname" class="classname" value="<?php echo $options->classname; ?>">
	            <input type="hidden" name="widget-class" class="widget-class" value="<?php echo $widget_class; ?>">

	            <div class="widget-control-actions">
	                <div class="alignleft">
	                    <a class="widget-control-remove" href="#remove">Delete</a> |
	                    <a class="widget-control-close" href="#close">Close</a>
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
<?php }
