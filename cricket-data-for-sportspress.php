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

add_action('admin_init', 'register_ajax_actions');

function register_ajax_actions()
{
    add_action('wp_ajax_cricket_series_list', 'get_cricket_series_list');

    function get_cricket_series_list()
    {
        $search = $_POST['q'];
        $api_key = get_option('cricket_data_for_sportspress_settings')['api_key'];
        $url = 'https://api.cricapi.com/v1/series?apikey=' . $api_key;

        if ($search) {
            $url .= '&search=' . $search;
        }

        $response = wp_remote_get($url);
        $body = wp_remote_retrieve_body($response);

        if ($body->status == 'success') {
            $result = json_decode($body);
            $series_list = array();

            foreach ($result->data as $series) {
                $series_list[] = array(
                    'id' => $series->id,
                    'name' => $series->name,
                    "startDate" => $series->startDate,
                    "endDate" => $series->endDate,
                    "odi" => $series->odi,
                    "t20" => $series->t20,
                    "test" => $series->test,
                    "squads" => $series->squads,
                    "matches" => $series->matches
                );
            }

            wp_send_json($series_list);
            wp_die();
        } else {
            wp_send_json(array('reason' => $body->reason));
            wp_die();
        }
    }

    add_action('wp_ajax_add_cricket_series', 'add_cricket_series');

    function add_cricket_series()
    {
        // TODO: Add league to SportsPress
        // TODO: Add seasons to SportsPress
        // TODO: Add matches to SportsPress
        // TODO: Add players to SportsPress
        // TODO: Add teams to SportsPress
        // TODO: Add venues to SportsPress
    }
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
    register_managment_submenu_pages();
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
 * Register plugin managment menu page
 */
function register_managment_submenu_pages()
{
    add_management_page(
        __('Import Cricket Data', 'cricket-data-for-sportspress'),
        __('Cricket Data', 'cricket-data-for-sportspress'),
        'import',
        'import-cricket-data',
        'register_import_cricket_data_submenu_page'
    );

    function register_import_cricket_data_submenu_page()
    {
        wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), '4.1.0', true);
        wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0', 'all');

        require_once plugin_dir_path(__FILE__) . 'includes/import-cricket-data.php';
    }

    if (!get_option('cricket_data_for_sportspress_settings')['api_key']) {
        add_action('admin_notices', 'api_key_not_set_notice');

        function api_key_not_set_notice()
        {
        ?>
            <div class="error">
                <p>
                    <strong>Cricket Data for SportsPress</strong> requires an API key to be set in order to import data.
                    <a href="<?= admin_url('options-general.php?page=cricket-data-settings') ?>">Set API key</a>
                </p>
            </div>
<?php
        }
    } else {
        add_action('admin_footer', 'fetch_cricket_series');

        function fetch_cricket_series()
        {
            // External JS file
            // wp_enqueue_script('fetch-cricket-series', plugin_dir_url(__FILE__) . 'js/fetch-cricket-series.js', array('jquery'), '1.0.0', true);
            // wp_localize_script(
            //     'fetch-cricket-series',
            //     'ajax_object',
            //     array('ajax_url' => admin_url('admin-ajax.php'))
            // );

            require_once plugin_dir_path(__FILE__) . 'includes/fetch-cricket-series.php';
        }
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
