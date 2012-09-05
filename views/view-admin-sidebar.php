<div class="widgets-holder-wrap">
    <?php //echo "<pre>";print_r($this);echo "</pre>";?>
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
			<p class="description"><?php echo $description; ?></p>
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
                        $widget = $dropzone->get('class');
                        include(WPDZ_VIEWS . 'view-admin-widget.php');
                    }
                }


            }
            ?>

	</div>


</div> 

