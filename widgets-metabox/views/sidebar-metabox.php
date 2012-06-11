<div class="wrap"><?php //do_action('widgets_admin_page');     ?>

    <div class="widget-liquid-left">
        <div id="widgets-left">
            <div id="available-widgets" class="widgets-holder-wrap">
                <div class="sidebar-name">
                    <div class="sidebar-name-arrow"><br /></div>
                    <h3>
                        <?php _e('Available Widgets'); ?> 
                        <span id="removing-widget">
                            <?php _ex('Deactivate', 'removing-widget'); ?> 
                            <span></span>  
                        </span>
                    </h3>
                </div>

                <div class="widget-holder">
                    <p class="description">
                        <?php _e('Drag widgets from here to a sidebar on the right to activate them. Drag widgets back here to deactivate them and delete their settings.'); ?>
                    </p>

                    <div id="widget-list">
                        <?php wp_list_widgets(); ?>
                    </div>
                    <br class="clear" />
                </div>

            </div>
        </div>   
    </div>
    <div class="widget-liquid-right">
        <div id="widgets-right">
            <div class="widgets-holder-wrap <?php echo esc_attr($this->get_sidebar()->wrap_class); ?>">
                <div class="sidebar-name">
                    <div class="sidebar-name-arrow"><br /></div>
                    <h3>
                        <?php echo esc_html($this->get_sidebar()->sidebar['name']); ?>
                        <span>
                            <img src="<?php echo esc_url(admin_url('images/wpspin_dark.gif')); ?>" class="ajax-feedback" title="" alt="" />
                        </span>
                    </h3>
                </div>
                <?php wp_list_widget_controls($this->get_sidebar()->id); // Show the control forms for each of the widgets in this sidebar ?>
            </div> 
        </div>
    </div>

    <form action="" method="post">
        <?php wp_nonce_field('save-sidebar-widgets', '_wpnonce_widgets', false); ?>
    </form>
    <br class="clear" />
</div>
