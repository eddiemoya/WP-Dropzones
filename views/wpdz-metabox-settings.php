
<p>
    <input type="checkbox" id="widgetpress_enabled" <?php checked($this->get_option_value($option_key), 'on' ); ?> name="<?php echo $option_key; ?>" />
    <label for="<?php echo $option_key; ?>" class="selectit">
        <?php echo $option_labels['label']; ?>
    </label>
</p>
<p class="howto">
    <?php echo $option_labels['label_help']; ?>
</p>