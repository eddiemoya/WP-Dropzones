
<?php 
    $spans = array(
        'span12' => '100%',
        'span9' => '75%',
        'span8' => '66%',
        'span6' => '50%',
        'span4' => '33%',
        'span3' => '25%'
    );

    $meta = $this->get('meta');
    $span = $meta['dropzone_span'];
    $borderleft = ($meta['dropzone_borderleft'] === "true") ? 'checked="checked"' : '';
?>
<div class="widgets-holder-wrap">
    
	<div class="sidebar-name">

    	<div class="sidebar-name-arrow"><br /></div>
    	<h3>
        	<?php echo ucfirst($this->get('type')) . ': ' .$this->get('term')->name; ?>
        	<span>
            	<img src="<?php echo esc_url(admin_url('images/wpspin_dark.gif')); ?>" class="ajax-feedback" title="" alt="" />
        	</span>
    	</h3>

	</div>
	<div class="widgets-sortables ui-sortable" style="min-height: 50px; " data-id="<?php echo $this->get('term')->term_id ?>" data-type="<?php echo $this->get('term')->taxonomy;?>">


		<div class="sidebar-description">
            <p>
                <form action="" class="dropzone-settings">

                    <div>
                        <label for="widgetpress_dropzone_span">Widget Width: </label>   
                        <select id="widgetpress_dropzone_span" class="widgetpress_dropzone_span" name="widgetpress_dropzone_span">
                            <?php
                                foreach ($spans as $value => $label ) :  ?>
                            
                                    <option value="<?php echo $value; ?>" <?php selected($value, $span); ?>>
                                        <?php echo $label ?>
                                    </option><?php
                                    
                                endforeach; 
                            ?>
                        </select>

                    <div class="alignright">
                        <img src="<?php echo admin_url('images/wpspin_light.gif'); ?>" class="ajax-feedback" title="" alt="" style="visibility: hidden; ">
                        <input type="submit" name="savedropzone" class="savedropzone" value="Save">
                    </div>
                    </div>

                    <div style="clear:left;">
                        <label for="dropzone_borderleft">Border Left: </label>   
                        <input type="checkbox" name="dropzone_borderleft" class="dropzone_borderleft" <?php echo $borderleft; ?> />
                        <input type="hidden" name="dropzone_id" class="dropzone_id" value="<?php echo $this->get('post')->ID; ?>" />
                        <input type="hidden" name="dropzone_type" class="dropzone_type" value="<?php echo $this->get('post')->post_type; ?>" />
                    </div>
                </form>
            </p>
			<p class="description" style="clear:left;"><?php echo $description; ?></p>
		</div>
	   
        <?php 
            if($this->get('type') == 'dropzone') {

                $widgets = WidgetPress_Controller_Widgets::get_widgets($this->get('term'));
                foreach ((array)$widgets as $widget){
                    include(WPDZ_VIEWS . 'view-admin-widget.php');
                }
            
            } else {
                $dropzones = WidgetPress_Controller_Dropzones::get_dropzones('dropzone');
                foreach ((array)$dropzones as $dropzone){
                    if(is_object($dropzone)){
                        if(class_exists($dropzone->get('class'))){
                            $widget = $dropzone->get('class');
                            include(WPDZ_VIEWS . 'view-admin-widget.php');
                        }
                    }
                }


            }
            ?>

	</div>


</div> 

