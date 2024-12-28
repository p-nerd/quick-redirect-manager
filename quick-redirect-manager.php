<?php

/**
 * Plugin Name: Quick Redirect Manager
 * Plugin URI: https://github.com/p-nerd/quick-redirect-manager
 * Description: A lightweight, user-friendly plugin to manage URL redirections. No database tables, just simple WordPress-native storage.
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 8.0
 * Author: Shihab Mahamud
 * Author URI: https://github.com/p-nerd
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: quick-redirect-manager
 * Domain Path: /languages
 */

// Prevent direct access to this file
if (! defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('QRM_VERSION', '1.0.0');
define('QRM_FILE', __FILE__);
define('QRM_PATH', dirname(QRM_FILE));
define('QRM_URL', plugins_url('', QRM_FILE));

// Check if Composer autoloader exists
if (file_exists(QRM_PATH.'/vendor/autoload.php')) {
    require_once QRM_PATH.'/vendor/autoload.php';
}

// Initialize the plugin
if (class_exists(\PNerd\QuickRedirectManager\Plugin::class)) {
    new \PNerd\QuickRedirectManager\Plugin;
}
