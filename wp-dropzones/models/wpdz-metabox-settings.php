<?php
/**
 * Description of wpdz-settings-metabox
 *
 * @author Eddie Moya
 * @uses WPDZ_Helper::include_view();
 */
class WPDZ_Metabox_Settings extends WPDZ_Metabox {
    
    /**
     * @var string
     */
    var $id = 'wpdz-metabox-settigns';
    
    /**
     * @var string 
     */
    var $title = 'Dropzone Settings';
  
    /**
     * @var string|array 
     */
    var $post_types = array('page');
    
    /**
     * @var string 
     */
    var $context = 'side';
    
    /**
     * @var string
     */
    var $priority = 'low';
    
    /**
     * @var array 
     */
    var $callback_args = null;
    
    /**
     * @var string
     */
    var $view = 'wpdz-metabox-settings';
    
    /**
     *
     * @var type 
     */
    var $option_keys = array('wpdz_widgets_enabled', 'wpdz_dropzone_manager_enabled');
}
