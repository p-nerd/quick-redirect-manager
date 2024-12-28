<?php

namespace PNerd\QuickRedirectManager;

use PNerd\QuickRedirectManager\Config;

class Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'addMenuPage']);
    }

    public function addMenuPage() {
        add_options_page(
            Config::SETTINGS_PAGE_TITLE ,
            Config::SETTINGS_PAGE_TITLE ,
            'manage_options',
            Config::SETTINGS_PAGE_SLUG,
            [$this, 'renderPage']
        );
    }

    public function renderPage() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        $this->handleFormSubmission();
        $this->handleDeletion();

        $redirects = get_option(Config::REDIRECTIONS_OPTION_KEY, []);
        echo View::render('admin-page', ['redirects' => $redirects]);
    }

    private function handleFormSubmission() {
        if (!isset($_POST['submit_redirect'])) {
            return;
        }

        if (!isset($_POST['redirect_nonce']) ||
            !wp_verify_nonce($_POST['redirect_nonce'], 'add_redirect')) {
            wp_die('Security check failed');
        }

        $redirects = get_option(Config::REDIRECTIONS_OPTION_KEY, []);
        $source_url = sanitize_text_field($_POST['source_url']);

        $redirects[$source_url] = [
            'target_url' => sanitize_text_field($_POST['target_url']),
            'redirect_type' => intval($_POST['redirect_type']),
            'hits' => 0,
            'created_at' => current_time('mysql')
        ];

        update_option(Config::REDIRECTIONS_OPTION_KEY, $redirects);
        add_settings_error(
            'qrm_messages',
            'qrm_redirect_added',
            'Redirection added successfully!',
            'updated'
        );
    }

    private function handleDeletion() {
        if (!isset($_GET['action']) || $_GET['action'] !== 'delete' || !isset($_GET['source'])) {
            return;
        }

        if (!isset($_GET['delete_nonce']) ||
            !wp_verify_nonce($_GET['delete_nonce'], 'delete_redirect')) {
            wp_die('Security check failed');
        }

        $redirects = get_option(Config::REDIRECTIONS_OPTION_KEY, []);
        $source = sanitize_text_field(urldecode($_GET['source']));

        if (isset($redirects[$source])) {
            unset($redirects[$source]);
            update_option(Config::REDIRECTIONS_OPTION_KEY, $redirects);
            add_settings_error(
                'qrm_messages',
                'qrm_redirect_deleted',
                'Redirection deleted successfully!',
                'updated'
            );
        }
    }
}
