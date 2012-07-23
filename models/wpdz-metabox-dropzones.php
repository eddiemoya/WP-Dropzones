<?php
/**
 * Description of wpdz-settings-metabox
 *
 * @author Eddie Moya
 * @uses WPDZ_Helper::include_view();
 */
class WPDZ_Metabox_Dropzones extends WPDZ_Metabox {
    
    /**
     * @var string
     */
    var $id = 'wpdz-metabox-dropzones';
    
    /**
     * @var string 
     */
    var $title = 'Dropzones Layout Manager';
  
    /**
     * @var string|array 
     */
    var $post_types = array('page', 'section');
    
    /**
     * @var string 
     */
    var $context = 'side';
    
    /**
     * @var string
     */
    var $priority = array('low', 'high');
    
    /**
     * @var array 
     */
    var $callback_args = array('metabox' => "layout-manager");
    
    /**
     * @var type 
     */
    var $view = 'wpdz-metabox-sidebars';

    var $enabled_option_key = 'wpdz-metabox-dropzones_enabled';
    
}
