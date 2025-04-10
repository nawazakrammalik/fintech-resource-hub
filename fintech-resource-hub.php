<?php
/**
 * Plugin Name: FinTech Resource Hub
 * Plugin URI: https://example.com/fintech-resource-hub
 * Description: A filterable content hub for sharing videos, guides, tools, and articles for accountants.
 * Version: 1.0.0
 * Author: Nawaz Akram
 * Author URI: https://example.com
 * Text Domain: fintech-resource-hub
 * Domain Path: /languages
 */


if (!defined('ABSPATH')) {
    exit;
}


define('FINTECH_RESOURCE_HUB_VERSION', '1.0.0');
define('FINTECH_RESOURCE_HUB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FINTECH_RESOURCE_HUB_PLUGIN_URL', plugin_dir_url(__FILE__));


require_once FINTECH_RESOURCE_HUB_PLUGIN_DIR . 'includes/class-fintech-resource-hub.php';


function fintech_resource_hub_init() {
    $plugin = new FinTech_Resource_Hub();
    $plugin->init();
}
add_action('plugins_loaded', 'fintech_resource_hub_init');


register_activation_hook(__FILE__, 'fintech_resource_hub_activate');
function fintech_resource_hub_activate() {
    flush_rewrite_rules();
}


register_deactivation_hook(__FILE__, 'fintech_resource_hub_deactivate');
function fintech_resource_hub_deactivate() {
    flush_rewrite_rules();
} 