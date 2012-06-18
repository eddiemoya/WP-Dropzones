<?php

/**
 * Static class containing methods pertaining directly to metaboxes
 *
 * @author Eddie Moya
 */
class WPDZ_Controller_Sidebars {
    
   public static $sidebar_args;
   public static $sidebars;
   public static $dropzone_args;
   public static $dropzones;
   
   

    public function init(){
        add_action('init', array(WPDZ_Sidebar, 'add_actions'));
        self::set_sidebar_args();
        self::set_dropzone_args();
        self::register_sidebars();
        self::register_dropzones();
        
        
        
        
    }
    
    private function register_sidebars(){
        foreach(self::$sidebar_args as $args){
            self::$sidebars[] = new WPDZ_Sidebar($args);
        }
    }
    
    private function register_dropzones(){
        foreach(self::$dropzone_args as $args){
            self::$dropzones[] = new WPDZ_Sidebar($args);
        }
    }

    private function set_sidebar_args(){
        self::$sidebar_args = array(
            array(
                'name' => 'Right Rail',
                'description' => 'Right Rail, full page height.',
                'before_widget' => '<ul>',
                'after_widget' => '</ul>',
                'before_title' => '<li>',
                'after_title' => '<li>'
            ),
            array(
                'name' => 'Left Rail',
                'description' => 'Left Rail, full page height.',
                'before_widget' => '<ul>',
                'after_widget' => '</ul>',
                'before_title' => '<li>',
                'after_title' => '<li>'
            )
        );
    }
    
    private function set_dropzone_args() {
        self::$dropzone_args = array(
            array(
                'name' => 'Dropzones',
                'description' => 'Use this area to control this pages layout',
                'before_widget' => '<ul>',
                'after_widget' => '</ul>',
                'before_title' => '<li>',
                'after_title' => '<li>'
            )
        );
    }

    public function sidebars(){
        foreach(self::$sidebars as $sidebar){
            include(WPDZ_Helper::include_view('wpdz-sidebar', '', true));
        }
    }
    
    public function dropzones(){
        foreach(self::$dropzones as $sidebar){
            include(WPDZ_Helper::include_view('wpdz-sidebar', '', true));
        }
    }
}