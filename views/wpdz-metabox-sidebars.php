<div class="wrap">
    <?php $dropzone_type = $this->callback_args['type']; ?>
    <?php if($dropzone_type != 'layout'){ ?>
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
                        <?php
                            $widgets = WidgetPress_Controller_Widgets::$widgets; 

                            foreach ((array)$widgets as $widget){
                                include(WPDZ_VIEWS . 'view-admin-widget.php');
                            } 
                         ?>
                    </div>
                    <br class="clear" />
                </div>
            </div>
        </div>   
    </div>
    <?php } ?>
    <div class="widget-liquid-right">
        <div id="widgets-right">
            <?php 
                $dropzones = WidgetPress_Controller_Dropzones::get_dropzones($dropzone_type);
                if($dropzone_type == 'layout'){
                   //echo "<pre>";print_r($dropzones);echo "</pre>";
                }
                
                if(!empty($dropzones) && !is_wp_error($dropzones[0]->get('term'))){

                    foreach($dropzones as $dropzone){
                        //$widget = $dropzone->get('class');
                        $dropzone->view('view-admin-sidebar.php', $this->callback_args);
                    }
                }    
             ?>
        </div>
    </div>

    <form action="" method="post">
        <?php wp_nonce_field('save-sidebar-widgets', '_wpnonce_widgets', false); ?>
    </form>
    <br class="clear" />
</div>