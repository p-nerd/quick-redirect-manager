<?php

namespace PNerd\QuickRedirectManager;

class Redirector
{
    public function __construct()
    {
        add_action('template_redirect', [$this, 'handleRedirection']);
    }

    public function handleRedirection(): void
    {
        if (is_admin()) {
            return;
        }

        $current_path = UrlValidator::normalizeUrl($_SERVER['REQUEST_URI']);

        $redirection = Redirection::get($current_path);

        if (! $redirection) {
            return;
        }

        $targetUrl = Redirection::targetUrl($redirection);
        $redirectType = Redirection::redirectType($redirection);

        $this->performRedirect($targetUrl, $redirectType);
    }

    private function performRedirect(string $url, int $status): void
    {
        wp_redirect($url, $status);
        exit;
    }
}
