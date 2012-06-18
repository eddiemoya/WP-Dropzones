<p>
    <input type="checkbox" id="wpdz_enabled" <?php checked($this->get_option_value('wpdz_widgets_enabled'), 'on' ); ?> name="wpdz_widgets_enabled" />
    <label for="wpdz_widgets_enabled" class="selectit">
        Enable Widget Metabox
    </label>
</p>
<p class="howto">
    Post must be updated for changes to take effect.
</p>

<?php if ( $this->get_option_value('wpdz_widgets_enabled') ) {  ?>
<p>
    <input type="checkbox" id="wpdz_enabled"  <?php checked($this->get_option_value('wpdz_dropzone_manager_enabled'), 'on' ); ?> name="wpdz_dropzone_manager_enabled" />
    <label for="wpdz_dropzone_manager_enabled" class="selectit">
        Enabled Layout Manager
    </label>
</p>
<p class="howto">
    Choose what kind of dropzones this page needs, and the order in which they should appear.
</p>
<?php }