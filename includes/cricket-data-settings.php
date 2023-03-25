<div class="wrap">
    <h2>Cricket Data API Settings</h2>
    <p>Store API key and schedule refreshes</p>
    <form action="options.php" method="post">
        <?php
        if (function_exists('wp_nonce_field'))
            wp_nonce_field('cricket_data_for_sportspress_api_settings_action');
        ?>
        <?php settings_fields('cricket_data_for_sportspress_settings'); ?>
        <?php do_settings_sections(plugin_dir_path(__DIR__) . 'cricket-data-for-sportspress.php'); ?>
        <p class="submit">
            <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
        </p>
    </form>
</div>