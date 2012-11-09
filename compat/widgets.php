<?php
/**
 * This section attemps to make other widgets compatible with WidgetPress. 
 * Most widgets work with WidgetPress with no problem, some however do not. 
 * 
 * This area attemps to make those widgets compatible with WidgetPress
 */


include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (is_plugin_active("wp-polls/wp-polls.php")){

	add_action('widgets_init', 'wp_unpolls', 11);
	function wp_unpolls(){
		unregister_widget('WP_Widget_Polls');
		register_widget('WP_Widget_Polls_WidgetPress_Compat');
	}

}

/**
 * Nearly complete copy of Polls widget from WP-Polls v2.63.
 * One line in the Update method is removed for compatibility.
 */
class WP_Widget_Polls_WidgetPress_Compat extends WP_Widget {
	// Constructor
	function WP_Widget_Polls_WidgetPress_Compat() {
		$widget_ops = array('description' => __('WP-Polls polls', 'wp-polls'));
		$this->WP_Widget('polls-widget', __('Polls', 'wp-polls'), $widget_ops);
	}

	// Display Widget
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		$poll_id = intval($instance['poll_id']);
		$display_pollarchive = intval($instance['display_pollarchive']);
		echo $before_widget;
		if(!empty($title)) {
			echo $before_title.$title.$after_title;
		}
		get_poll($poll_id);	
		if($display_pollarchive) {
			display_polls_archive_link();
		}
		echo $after_widget;
	}

	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance) {
		if (!isset($new_instance['submit'])) {
			//return false; //Commented out because I dont know why this is here, and it breaks the widget on WidgetPress
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['poll_id'] = intval($new_instance['poll_id']);
		$instance['display_pollarchive'] = intval($new_instance['display_pollarchive']);
		return $instance;
	}

	// DIsplay Widget Control Form
	function form($instance) {
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('title' => __('Polls', 'wp-polls'), 'poll_id' => 0, 'display_pollarchive' => 1));
		$title = esc_attr($instance['title']);
		$poll_id = intval($instance['poll_id']);
		$display_pollarchive = intval($instance['display_pollarchive']);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-polls'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('display_pollarchive'); ?>"><?php _e('Display Polls Archive Link Below Poll?', 'wp-polls'); ?>
				<select name="<?php echo $this->get_field_name('display_pollarchive'); ?>" id="<?php echo $this->get_field_id('display_pollarchive'); ?>" class="widefat">
					<option value="0"<?php selected(0, $display_pollarchive); ?>><?php _e('No', 'wp-polls'); ?></option>
					<option value="1"<?php selected(1, $display_pollarchive); ?>><?php _e('Yes', 'wp-polls'); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('poll_id'); ?>"><?php _e('Poll To Display:', 'wp-polls'); ?>
				<select name="<?php echo $this->get_field_name('poll_id'); ?>" id="<?php echo $this->get_field_id('poll_id'); ?>" class="widefat">
					<option value="-1"<?php selected(-1, $poll_id); ?>><?php _e('Do NOT Display Poll (Disable)', 'wp-polls'); ?></option>
					<option value="-2"<?php selected(-2, $poll_id); ?>><?php _e('Display Random Poll', 'wp-polls'); ?></option>
					<option value="0"<?php selected(0, $poll_id); ?>><?php _e('Display Latest Poll', 'wp-polls'); ?></option>
					<optgroup>&nbsp;</optgroup>
					<?php
					$polls = $wpdb->get_results("SELECT pollq_id, pollq_question FROM $wpdb->pollsq ORDER BY pollq_id DESC");
					if($polls) {
						foreach($polls as $poll) {
							$pollq_question = stripslashes($poll->pollq_question);
							$pollq_id = intval($poll->pollq_id);
							if($pollq_id == $poll_id) {
								echo "<option value=\"$pollq_id\" selected=\"selected\">$pollq_question</option>\n";
							} else {
								echo "<option value=\"$pollq_id\">$pollq_question</option>\n";
							}
						}
					}
					?>
				</select>
			</label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php
	}
}


