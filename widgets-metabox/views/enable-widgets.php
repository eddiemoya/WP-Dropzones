
<p>
    <input type="checkbox" id="widgets-metabox-enable"  <?php checked(self::is_enabled(), true ); ?>name="widgets-metabox-enable" />
    
    <label for="widgets-metabox-enable" class="selectit">Enable Widget Metabox</label>
</p>
<p class="howto">
    Post must be updated for changes to take effect.
</p>

<?php if (self::is_enabled()) :  ?>
<p>
    <input type="checkbox" id="widgets-metabox-on-loop-end"  <?php checked(self::on_loop_end(), true ); ?>name="widgets-metabox-on-loop-end" />
    <label for="widgets-metabox-on-loop-end" class="selectit">Place widgets after "The Loop".</label>
</p>
<p class="howto">
    Without this on, these widgets will simply populate all sidebars on the page.
</p>
<?php endif; ?>
