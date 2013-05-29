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
    var $post_types = array('page', 'section', 'category');
    
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
    var $options = array(
        'wpdz-metabox-sidebars_enabled' => array(
            'label' => 'Enable Widget Editor for this post.',
            'label_help' => 'Post bust be saved for changes to take effect'
        ),
        'wpdz-metabox-dropzones_enabled' => array(
            'label' => 'Enable Layout Manager for this post',
            'label_help' => 'Requires that the Widget Editor be enabled.'
        ),
    );

}
