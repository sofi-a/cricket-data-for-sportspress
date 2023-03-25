<?php

/**
 * Plugin Name: Cricket Data for SportsPress
 * Plugin URI: http://github.com/cricket-data-for-sportspress
 * Description: Cricket Data integration [https://cricketdata.org/how-to-use-cricket-data-api.aspx] for SportsPress
 * Version: 1.0.0
 * Author: Sofonias Abathun
 * Author URI: http://github.com/sofi-a
 * Requires at least: 3.8
 * Tested up to: 6.1
 * Text Domain: cricket-data-for-sportspress
 * License: GPL3
 * 
 * Cricket Data for SportsPress is free software. You can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 2 of the license, or
 * any later version.
 * 
 * Cricket Data for SportsPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU GENERAL PUBLIC LICENSE for more details.
 * You should have received a copy of the GNU Public License
 * along with Cricket Data for SportsPress. If not see https://www.gnu.org/licenses/gpl.html.
 * 
 * @package Cricket_Data_For_SportsPress
 * @category API Integration
 * @author Sofonias Abathun
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Check if SportsPress is installed and activated
 */
add_action('admin_init', 'check_sportspress_pro_installation');

function check_sportspress_pro_installation()
{
    if (is_admin() && current_user_can('activate_plugins') && !is_plugin_active('sportspress-pro/sportspress-pro.php')) {
        add_action('admin_notices', 'sportspress_pro_not_installed_or_active_notice');

        deactivate_plugins(plugin_basename(__FILE__));

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
}


/**
 * Display notice if SportsPress is not installed or activated
 */
function sportspress_pro_not_installed_or_active_notice()
{
?>
    <div class="error">
        <p>Sorry, but Cricket Data for SportsPress requires that the SportsPress Pro plugin is installed and active.</p>
    </div>
    <?php
}

/**
 * Register plugin settings, settings section, and settings fields
 */
add_action('admin_init', 'register_plugin_options');

function register_plugin_options()
{
    register_settings();
    register_settings_section();
    register_settings_fields();
}

/**
 * Register plugin settings
 */
function register_settings()
{
    register_setting(
        'cricket_data_for_sportspress_settings',
        'cricket_data_for_sportspress_settings',
        'validate_cricket_data_api_settings'
    );

    /**
     * Validates API settings before they are saved to the database
     */
    function validate_cricket_data_api_settings($input)
    {
        return $input;
    }
}

/**
 * Register plugin settings section
 */
function register_settings_section()
{
    add_settings_section(
        'cricket_data_for_sportspress_api_settings',
        'Cricket Data API Settings',
        'echo_api_settings_section_help_text',
        __FILE__
    );

    /**
     * Echos out any content at the top of the section displayed before the first option (between heading and fields)
     */
    function echo_api_settings_section_help_text()
    {
        echo '<p>Enter your Cricket Data API key below.</p>';
    }
}

/**
 * Register plugin settings fields
 */
function register_settings_fields()
{
    add_settings_field(
        'api_key',
        'API Key',
        'echo_api_key_field',
        __FILE__,
        'cricket_data_for_sportspress_api_settings'
    );

    /**
     * Echos out the HTML for the field
     */
    function echo_api_key_field()
    {
        $options = get_option('cricket_data_for_sportspress_settings');
        $api_key = $options['api_key'];
    ?>
        <input id="api_key" name="cricket_data_for_sportspress_settings[api_key]" size="40" type="text" value="<?= $api_key ?>" />
<?php
    }
}

/**
 * Register plugin menu pages
 */
add_action('admin_menu', 'register_submenu_pages');

function register_submenu_pages()
{
    register_settings_submenu_pages();
}

/**
 * Register plugin settings menu page
 */
function register_settings_submenu_pages()
{
    add_options_page(
        __('Cricket Data Settings', 'cricket-data-for-sportspress'),
        __('Cricket Data', 'cricket-data-for-sportspress'),
        'manage_options',
        'cricket-data-settings',
        'register_cricket_data_settings_submenu_page'
    );

    function register_cricket_data_settings_submenu_page()
    {
        require_once plugin_dir_path(__FILE__) . 'includes/cricket-data-settings.php';
    }
}

/**
 * Cleanup on deactivation
 */
register_deactivation_hook(__FILE__, 'cleanup');

function cleanup()
{
    unregister_setting(
        'cricket_data_for_sportspress_settings',
        'cricket_data_for_sportspress_settings',
    );
}
