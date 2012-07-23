<div class="wrap">
    <?php $metabox = $this->callback_args['metabox']; ?>
    <?php if($metabox != 'layout-manager'){ ?>
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
    <?php } ?>
    <div class="widget-liquid-right">
        <div id="widgets-right">
            <?php WPDZ_Controller_Sidebars::view($metabox); ?>
        </div>
    </div>

    <form action="" method="post">
        <?php wp_nonce_field('save-sidebar-widgets', '_wpnonce_widgets', false); ?>
    </form>
    <br class="clear" />
</div>