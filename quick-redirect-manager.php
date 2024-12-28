<?php
/*
Plugin Name: Quick Redirect Manager
Description: A lightweight, user-friendly plugin to manage URL redirections. No database tables, just simple WordPress-native storage.
Version: 1.0
Author: Shihab Mahamud
License: GPL v2 or later
*/

if (!defined('ABSPATH')) {
    exit;
}

// Check if autoload exists
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

// Initialize the plugin
if (class_exists('PNerd\\QuickRedirectManager\\Plugin')) {
    new PNerd\QuickRedirectManager\Plugin();
}
