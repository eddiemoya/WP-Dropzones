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

function display_dropzone($slug = null){
    WidgetPress_Controller_Widgets::display_dropzone($slug);
}

function display_dropzone_by_terms($slug = null, $categories){

     //$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, implode( "', '", $slugs) ) );


    WidgetPress_Controller_Widgets::display_dropzone($slug_found);
}


/**
 * This is bad, please dont use this for anything else - it WILL be removed, and you will be shamed!
 * Do not look at this as an example of how to do anything - this is bad, and you are bad for even looking at it. Bad developer! Bad! Shoo, go away now.
 *
 * Added for single pages to use.
 *
 * @author Eddie Moya
 */
function get_first_available_slug_from_list_of_terms_dont_use_this_horrible_function($slug, $categories, $bool = false){
    global $wpdb;

    $slugs = array();
    $parents = array();

    //Go through each category
    foreach($categories as $category){

        //string together the bits for this category's section slug
        $slugs[] = $slug . '_' . $category->slug;

        //If it has a parent, get its section slug too, but store it seperately so we can add it to the end
        if($category->parent != 0){

            $slugs[] = $slug . '_' . get_term_by('id', $category->parent, 'category' )->slug;
        }

    }

    //$slugs = array_merge($slugs, $parent_slugs);
    $slugs[] = $slug;
    print_pre($slugs);

    //Stolen from the wp_unique_post_slug() function in wp-includes/post.php
    //Couldnt use that function because it requires an actual post object id to be passed, which we dont have or may not even exist in this case
    $check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name IN ( '".implode( "', '", esc_sql($slugs))."' ) AND post_type = %s ORDER BY field(post_name, '".implode( "', '", esc_sql($slugs))."' ) LIMIT 1";
    $slug_found = $wpdb->get_var($wpdb->prepare($check_sql, "section"));
    
    if($bool){
        return ($slug_found);
    } else {
        return $slug_found;
    }
}