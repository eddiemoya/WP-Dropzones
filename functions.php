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
        'before_widget' => '<ul class="dropzone-widget widget %2$s $span">',
        'after_widget' => '</ul>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    );
   return $dropzones;
}
add_filter('wpdz_dropzones', 'add_dropzones');

/**
 * Template Function to display a Dropzones container
 */
function display_dropzones(){
    WPDZ_Controller_Sidebars::display_dropzones();
}