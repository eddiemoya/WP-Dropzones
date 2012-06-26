
<p>
    <input type="checkbox" id="wpdz_enabled" <?php checked($this->get_option_value($option_key), 'on' ); ?> name="<?php echo $option_key; ?>" />
    <label for="<?php echo $option_key; ?>" class="selectit">
        <?php echo $option_labels['enable']; ?>
    </label>
</p>
<p class="howto">
    <?php echo $option_labels['enable_help']; ?>
</p>