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
	<div id="wpdz-sidebar-layout-manager-337" class="widgets-sortables ui-sortable" style="min-height: 50px; ">

		<div class="sidebar-description">
			<p class="description"><?php echo $description; ?></p>
		</div>
	
	</div>
    	<?php 
    	if($this->get('type') == 'dropzone') {
    		$widgets = WidgetPress_Controller_Widgets::get_widgets($this->get('term'));
    	} else {
    		$widgets = WidgetPress_Controller_Dropzones::get_dropzones('dropzone');
    	}
    	
    	
    	if(!empty($widgets)){

	        foreach((array)$widgets as $widget) {
	        	//echo "<pre>";print_r($widget);echo "</pre>";
	            include(WPDZ_VIEWS . 'view-widget.php');
	        }
    	}
    ?>
</div> 

