<?php
/**
 * Default dropzone. For now its here, but should probably
 * be set in the theme functions.php instead.
 */
function add_dropzones($dropzones) {
    $dropzones['custom'] = array(
        'name' => 'Custom Dropzone',
        'id' => 'custom',
        'description' => 'Use this area to control this pages layout',
        'before_widget' => '<article class="content-container dropzone-widget widget %2$s" id="%1s">',
        'after_widget' => '</article>',
        'before_title' => '<header class="content-header"><h3>',
        'after_title' => '</h3></header>'
    );
   return $dropzones;
}
add_filter('wpdz_dropzones', 'add_dropzones');

/**
 * Template Function to display a Dropzones container
 */
function display_dropzones(){
    WidgetPress_Controller_Widgets::display_dropzones();
}