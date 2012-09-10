<?php
/**
 * Description of wpdz-settings-metabox
 *
 * @author Eddie Moya
 * @uses WPDZ_Helper::include_view();
 */
class WPDZ_Metabox_Post_Types extends WPDZ_Metabox {
    
    /**
     * @var string
     */
    var $id = 'wpdz-metabox-post-types';
    
    /**
     * @var string 
     */
    var $title = 'Section Post Types';
  
    /**
     * @var string|array 
     */
    var $post_types = array('section');
    
    /**
     * @var string 
     */
    var $context = 'side';
    
    /**
     * @var string
     */
    var $priority = 'core';
    
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
        'widgetpress_post_type_none' => array(
            'label' => 'Category Archive',
            'label_help' => 'Category Archives with no post type filter: "/category/{term}"'
        ),
        'widgetpress_post_type_post' => array(
            'label' => 'Posts',
        ),
        'widgetpress_post_type_question' => array(
            'label' => 'Questions',
        ),
        'widgetpress_post_type_guides' => array(
            'label' => 'Guides',
            'label_help' => 'Select a post type to have this show up on: "category/{term}/{post_type}"'
        ),
    );

}