<div class="widgets-holder-wrap <?php echo esc_attr($this->get_wrap_class()); ?>">
    <?php 
        $dropzones = new WidgetPress_Model_Dropzone(); 
        
        foreach((array)$dropzones->get_dropzones('layout') as $dropzone) {
            include(WPDZ_VIEWS . 'view-widget.php');
        }
    ?>
</div> 