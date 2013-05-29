<?php
/**
 * Description of wpdz-settings-metabox
 *
 * @author Eddie Moya
 * @uses WPDZ_Helper::include_view();
 */
class WPDZ_Metabox_Sidebars extends WPDZ_Metabox {
    
    /**
     * @var string
     */
    var $id = 'wpdz-metabox-sidebars';
    
    /**
     * @var string 
     */
    var $title = 'Dropzones';
  
    /**
     * @var string|array 
     */
    var $post_types = array('page', 'section', 'category', 'skcategory');
    
    /**
     * @var string 
     */
    var $context = 'normal';
    
    /**
     * @var string
     */
    var $priority = 'low';
    
    /**
     * @var array 
     */
    var $callback_args = array('type' => 'dropzone', 'description' => 'Drop widgets into this area and edit their settings.');
    
    /**
     * @var string
     */
    var $view = 'wpdz-metabox-sidebars';
    
    var $enabled_option_key = 'wpdz-metabox-sidebars_enabled';
}
