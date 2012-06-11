<?php
/**
 * Widgets administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */
/** WordPress Administration Bootstrap */
global $wp_registered_sidebars, $wp_registered_widgets;
/** WordPress Administration Widgets API */
require_once(ABSPATH . 'wp-admin/includes/widgets.php');

if (!current_user_can('edit_theme_options'))
    wp_die(__('Cheatin&#8217; uh?'));

$widgets_access = get_user_setting('widgets_access');
if (isset($_GET['widgets-access'])) {
    $widgets_access = 'on' == $_GET['widgets-access'] ? 'on' : 'off';
    set_user_setting('widgets_access', $widgets_access);
}

function wp_widgets_access_body_class($classes) {
    return "$classes widgets_access ";
}

if ('on' == $widgets_access) {
    add_filter('admin_body_class', 'wp_widgets_access_body_class');
} else {
    //wp_enqueue_script('admin-widgets');
}

do_action('sidebar_admin_setup');

$title = __('Widgets');
$parent_file = 'themes.php';

get_current_screen()->add_help_tab(array(
    'id' => 'overview',
    'title' => __('Overview'),
    'content' =>
    '<p>' . __('Widgets are independent sections of content that can be placed into any widgetized area provided by your theme (commonly called sidebars). To populate your sidebars/widget areas with individual widgets, drag and drop the title bars into the desired area. By default, only the first widget area is expanded. To populate additional widget areas, click on their title bars to expand them.') . '</p>
	<p>' . __('The Available Widgets section contains all the widgets you can choose from. Once you drag a widget into a sidebar, it will open to allow you to configure its settings. When you are happy with the widget settings, click the Save button and the widget will go live on your site. If you click Delete, it will remove the widget.') . '</p>'
));
get_current_screen()->add_help_tab(array(
    'id' => 'removing-reusing',
    'title' => __('Removing and Reusing'),
    'content' =>
    '<p>' . __('If you want to remove the widget but save its setting for possible future use, just drag it into the Inactive Widgets area. You can add them back anytime from there. This is especially helpful when you switch to a theme with fewer or different widget areas.') . '</p>
	<p>' . __('Widgets may be used multiple times. You can give each widget a title, to display on your site, but it&#8217;s not required.') . '</p>
	<p>' . __('Enabling Accessibility Mode, via Screen Options, allows you to use Add and Edit buttons instead of using drag and drop.') . '</p>'
));
get_current_screen()->add_help_tab(array(
    'id' => 'missing-widgets',
    'title' => __('Missing Widgets'),
    'content' =>
    '<p>' . __('Many themes show some sidebar widgets by default until you edit your sidebars, but they are not automatically displayed in your sidebar management tool. After you make your first widget change, you can re-add the default widgets by adding them from the Available Widgets area.') . '</p>' .
    '<p>' . __('When changing themes, there is often some variation in the number and setup of widget areas/sidebars and sometimes these conflicts make the transition a bit less smooth. If you changed themes and seem to be missing widgets, scroll down on this screen to the Inactive area, where all your widgets and their settings will have been saved.') . '</p>'
));

get_current_screen()->set_help_sidebar(
        '<p><strong>' . __('For more information:') . '</strong></p>' .
        '<p>' . __('<a href="http://codex.wordpress.org/Appearance_Widgets_Screen" target="_blank">Documentation on Widgets</a>') . '</p>' .
        '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

if (empty($sidebars_widgets))
    $sidebars_widgets = wp_get_widget_defaults();

foreach ($sidebars_widgets as $sidebar_id => $widgets) {
    if ('wp_inactive_widgets' == $sidebar_id)
        continue;

    if (!isset($wp_registered_sidebars[$sidebar_id])) {
        if (!empty($widgets)) { // register the inactive_widgets area as sidebar
            register_sidebar(array(
                'name' => __('Inactive Sidebar (not used)'),
                'id' => $sidebar_id,
                'class' => 'inactive-sidebar orphan-sidebar',
                'description' => __('This sidebar is no longer available and does not show anywhere on your site. Remove each of the widgets below to fully remove this inactive sidebar.'),
                'before_widget' => '',
                'after_widget' => '',
                'before_title' => '',
                'after_title' => '',
            ));
        } else {
            unset($sidebars_widgets[$sidebar_id]);
            print_r($sidebar_id);
        }
    }
}

// register the inactive_widgets area as sidebar
register_sidebar(array(
    'name' => __('Inactive Widgets'),
    'id' => 'wp_inactive_widgets',
    'class' => 'inactive-sidebar',
    'description' => __('Drag widgets here to remove them from the sidebar but keep their settings.'),
    'before_widget' => '',
    'after_widget' => '',
    'before_title' => '',
    'after_title' => '',
));

retrieve_widgets();


//require_once( './admin-header.php' );
?>

<div class="wrap">

<?php do_action('widgets_admin_page'); ?>

    <div class="widget-liquid-left">
        <div id="widgets-left">
            <div id="available-widgets" class="widgets-holder-wrap">
                <div class="sidebar-name">
                    <div class="sidebar-name-arrow"><br /></div>
                    <h3><?php _e('Available Widgets'); ?> <span id="removing-widget"><?php _ex('Deactivate', 'removing-widget'); ?> <span></span></span></h3></div>
                <div class="widget-holder">
                    <p class="description"><?php _e('Drag widgets from here to a sidebar on the right to activate them. Drag widgets back here to deactivate them and delete their settings.'); ?></p>
                    <div id="widget-list">
<?php wp_list_widgets(); ?>
                    </div>
                    <br class='clear' />
                </div>
                <br class="clear" />
            </div>

<?php
foreach ($wp_registered_sidebars as $sidebar => $registered_sidebar) {
    if (false !== strpos($registered_sidebar['class'], 'inactive-sidebar') || 'orphaned_widgets' == substr($sidebar, 0, 16)) {
        $wrap_class = 'widgets-holder-wrap';
        if (!empty($registered_sidebar['class']))
            $wrap_class .= ' ' . $registered_sidebar['class'];
        ?>

                    <div class="<?php echo esc_attr($wrap_class); ?>">
                        <div class="sidebar-name">
                            <div class="sidebar-name-arrow"><br /></div>
                            <h3><?php echo esc_html($registered_sidebar['name']); ?>
                                <span><img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback" title="" alt="" /></span>
                            </h3>
                        </div>
                        <div class="widget-holder inactive">
        <?php wp_list_widget_controls($registered_sidebar['id']); ?>
                            <br class="clear" />
                        </div>
                    </div>
        <?php
    }
}
?>

        </div>
    </div>

    <div class="widget-liquid-right">
        <div id="widgets-right">
<?php
$registered_sidebar = $wp_registered_sidebars[$this->get_sidebar_id()];
$sidebar = $this->get_sidebar_id();


if (false !== strpos($registered_sidebar['class'], 'inactive-sidebar') || 'orphaned_widgets' == substr($sidebar, 0, 16))
    continue;

$wrap_class = 'widgets-holder-wrap';
if (!empty($registered_sidebar['class']))
    $wrap_class .= ' sidebar-' . $registered_sidebar['class'];

if ($i)
    $wrap_class .= ' closed';
?>

            <div class="<?php echo esc_attr($wrap_class); ?>">
                <div class="sidebar-name">
                    <div class="sidebar-name-arrow"><br /></div>
                    <h3><?php echo esc_html($registered_sidebar['name']); ?>
                        <span><img src="<?php echo esc_url(admin_url('images/wpspin_dark.gif')); ?>" class="ajax-feedback" title="" alt="" /></span></h3></div>
<?php wp_list_widget_controls($sidebar); // Show the control forms for each of the widgets in this sidebar  ?>
            </div>



        </div>
    </div>
    <form action="" method="post">
<?php wp_nonce_field('save-sidebar-widgets', '_wpnonce_widgets', false); ?>
    </form>
    <br class="clear" />
</div>

<?php
