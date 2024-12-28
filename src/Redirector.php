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

        $serverPath = $_SERVER['REQUEST_URI'];

        $queries = Url::extractQueries($serverPath);
        $currentPath = Url::normalizeUrl($serverPath);

        $redirection = Redirection::get($currentPath);

        if (! $redirection) {
            return;
        }

        $targetUrl = Redirection::targetUrl($redirection);
        $redirectType = Redirection::redirectType($redirection);

        $redirectUrl = Url::concatQueries($targetUrl, $queries);

        $this->performRedirect($redirectUrl, $redirectType);
    }

    private function performRedirect(string $url, int $status): void
    {
        wp_redirect($url, $status);
        exit;
    }
}
