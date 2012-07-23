<?php 
/*
Plugin Name: WP Dropzones
Plugin URI: http://wordpress.org/extend/plugins/wp-dropzones
Description: Magical things that sound like fun, but might end badly.
Version: 0.4.1 (Docking)
Author: Eddie Moya
Author URI: http://eddiemoya.com
*/

define(WPDZ_PATH, plugin_dir_path(__FILE__));
define(WPDZ_ASSETS, WPDZ_PATH . 'assets/');
define(WPDZ_CONTROLLERS, WPDZ_PATH . 'controllers/');
define(WPDZ_VIEWS, WPDZ_PATH . 'views/');
define(WPDZ_MODELS, WPDZ_PATH . 'models/');
define(WPDZ_WIDGETS, WPDZ_MODELS . 'widgets/');


include (WPDZ_WIDGETS . 'dropzone-widget/dropzone-widget.php');
include (WPDZ_CONTROLLERS . 'wpdz-controller-core.php');
include (WPDZ_CONTROLLERS . 'wpdz-controller-metaboxes.php');
include (WPDZ_CONTROLLERS . 'wpdz-controller-sidebars.php');
include (WPDZ_MODELS . 'wpdz-metabox.php');
include (WPDZ_MODELS . 'wpdz-metabox-settings.php');
include (WPDZ_MODELS . 'wpdz-metabox-sidebars.php');
include (WPDZ_MODELS . 'wpdz-metabox-dropzones.php');
include (WPDZ_MODELS . 'wpdz-sidebar.php');

Dropzone_Widget::register_widget();

if(is_admin()){
	//WPDZ_Controller_Core::init();
	WPDZ_Controller_Sidebars::init();
	WPDZ_Controller_Metaboxes::init();
}