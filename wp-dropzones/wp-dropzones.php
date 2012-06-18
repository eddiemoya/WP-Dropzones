<?php 
/*
Plugin Name: WP Dropzones
Plugin URI: http://wordpress.org/extend/plugins/wp-dropzones
Description: Magical things that sound like fun, but might end badly.
Version: 0.4 (Bear Fucking)
Author: Eddie Moya
Author URL: http://eddiemoya.com
*/

define(WPDZ_PATH, WP_PLUGIN_DIR . '/wp-dropzones/');

/* A static class with helpful methods */
include WPDZ_PATH . 'singletons/wpdz-helper.php';

WPDZ_Helper::include_controller('wpdz-controller-core');
WPDZ_Helper::include_controller('wpdz-controller-metaboxes');
WPDZ_Helper::include_controller('wpdz-controller-sidebars');
WPDZ_Helper::include_model('wpdz-metabox');
WPDZ_Helper::include_model('wpdz-metabox-settings');
WPDZ_Helper::include_model('wpdz-metabox-sidebars');
WPDZ_Helper::include_model('wpdz-metabox-dropzones');
WPDZ_Helper::include_model('wpdz-sidebar');


/**
 * @see /classes/wp-dropzone.php
 */
WPDZ_Controller_Core::init();
WPDZ_Controller_Sidebars::init();
WPDZ_Controller_Metaboxes::init();
