<?php 
/*
Plugin Name: WP Dropzones
Plugin URI: http://wordpress.org/extend/plugins/wp-dropzones
Description: Magical things that sound like fun, but might end badly.
Version: 0.7 Alpha 1
Author: Eddie Moya
Author URI: http://eddiemoya.com
*/

define('WPDZ_PATH', 		plugin_dir_path(__FILE__));
define('WPDZ_ASSETS', 		WPDZ_PATH . 'assets/');
define('WPDZ_CONTROLLERS', 	WPDZ_PATH . 'controllers/');
define('WPDZ_VIEWS', 		WPDZ_PATH . 'views/');
define('WPDZ_MODELS', 		WPDZ_PATH . 'models/');
define('WPDZ_WIDGETS', 		WPDZ_MODELS . 'widgets/');

include (WPDZ_WIDGETS 		. 'dropzone-widget/dropzone-widget.php');

include (WPDZ_CONTROLLERS 	. 'controller-metaboxes.php');
include (WPDZ_CONTROLLERS 	. 'controller-dropzones.php');
include (WPDZ_CONTROLLERS 	. 'controller-widgets.php');

include (WPDZ_MODELS 		. 'model-dropzone.php');
include (WPDZ_MODELS 		. 'model-metabox.php');
include (WPDZ_MODELS 		. 'model-metabox-settings.php');
include (WPDZ_MODELS 		. 'model-metabox-layout.php');
include (WPDZ_MODELS 		. 'model-metabox-posttypes.php');
include (WPDZ_MODELS 		. 'model-metabox-dropzone.php');

include (WPDZ_MODELS 		. 'model-widget.php');
include (WPDZ_PATH   		. 'functions.php');


WPDZ_Controller_Metaboxes::init();
WidgetPress_Controller_Dropzones::init();
WidgetPress_Controller_Widgets::init();
//Dropzone_Widget::register_widget();