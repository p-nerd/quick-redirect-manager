<?php

namespace PNerd\QuickRedirectManager;

class Redirector {
    private $option_name = 'qrm_redirections';

    public function __construct() {
        add_action('template_redirect', [$this, 'handleRedirection']);
    }

    public function handleRedirection() {
        if (is_admin()) {
            return;
        }

        $current_url = $_SERVER['REQUEST_URI'];
        $redirects = get_option($this->option_name, []);

        if (!isset($redirects[$current_url])) {
            return;
        }

        $redirect = $redirects[$current_url];

        // Update hit counter
        $redirects[$current_url]['hits']++;
        update_option($this->option_name, $redirects);

        wp_redirect($redirect['target_url'], $redirect['redirect_type']);
        exit;
    }
}
