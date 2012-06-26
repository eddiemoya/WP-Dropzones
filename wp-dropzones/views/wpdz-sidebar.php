<div class="widgets-holder-wrap <?php echo esc_attr($this->get_wrap_class()); ?>">
    <div class="sidebar-name">
        <div class="sidebar-name-arrow"><br /></div>
        <h3>
            <?php echo esc_html($this->name); ?>
            <span>
                <img src="<?php echo esc_url(admin_url('images/wpspin_dark.gif')); ?>" class="ajax-feedback" title="" alt="" />
            </span>
        </h3>
    </div>
    <?php wp_list_widget_controls($this->id); // Show the control forms for each of the widgets in this sidebar ?>
</div> 