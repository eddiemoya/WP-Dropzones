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
        'widgetpress_skcategory_archive' => array(
            'label' => 'SKCategory Archive',
            'label_help' => '/skcategory/{term}<hr>'
        ),
        'widgetpress_post_type_none' => array(
            'label' => 'Category Archive',
            'label_help' => '/category/{term}<hr>'
        ),
         'widgetpress_post_archive' => array(
            'label' => 'Posts Archive',
            'label_help' => '/posts'
        ),
        'widgetpress_question_archive' => array(
            'label' => 'Questions Archive',
            'label_help' => '/questions'
        ),
        'widgetpress_guide_archive' => array(
            'label' => 'Guides Archive',
            'label_help' => '/guides'
        ),
        'widgetpress_format_archive' => array(
            'label' => 'Format: Video Archive',
            'label_help' => '/videos<hr>'
        ),
        'widgetpress_post_type_post' => array(
            'label' => 'Categorized Posts',
            'label_help' => '/category/{term}/post'
        ),
        'widgetpress_post_type_question' => array(
            'label' => 'Categorized Questions',
            'label_help' => '/category/{term}/question'
        ),
        'widgetpress_post_type_guide' => array(
            'label' => 'Categorized Guides',
            'label_help' => '/category/{term}/guide'
        ),
        'widgetpress_category_format' => array(
            'label' => 'Categorized Videos',
            'label_help' => '/category/{term}/video'
        ),
    );

}