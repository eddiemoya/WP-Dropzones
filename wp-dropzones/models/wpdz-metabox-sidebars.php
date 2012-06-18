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
    var $title = 'Widgets';
  
    /**
     * @var string|array 
     */
    var $post_types = 'page';
    
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
    var $callback_args = array('callback' => "sidebars");
    
    /**
     * @var string
     */
    var $view = 'wpdz-metabox-sidebars';
    
}
