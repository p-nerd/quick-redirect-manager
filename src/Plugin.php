<?php

namespace PNerd\QuickRedirectManager;

use PNerd\QuickRedirectManager\Lib\Url;

class Plugin
{
    public function __construct()
    {
        new Admin;
        // Register activation hook
        register_activation_hook(
            dirname(__DIR__).'/quick-redirect-manager.php',
            [$this, 'activate']
        );

        // Add settings link
        add_filter(
            'plugin_action_links_'.plugin_basename(dirname(__DIR__).'/quick-redirect-manager.php'),
            [$this, 'addSettingsLink']
        );

        // Register redirect template - set to a higher priority (lower number)
        add_action(
            'template_redirect',
            [$this, 'handleRedirection'],
            1
        );
    }

    public function activate()
    {
        if (! get_option(Config::REDIRECTIONS_OPTION_KEY)) {
            add_option(Config::REDIRECTIONS_OPTION_KEY, []);
        }
    }

    public function addSettingsLink($links)
    {
        $query = Config::SETTINGS_PAGE_SLUG;
        $settings_link = "<a href='options-general.php?page=$query'>Settings</a>";
        array_unshift($links, $settings_link);

        return $links;
    }

    public function handleRedirection(): void
    {
        $serverPath = $_SERVER['REQUEST_URI'];

        // Add error handling
        try {
            $redirect = Url::getRedirect($serverPath);

            if (! $redirect) {
                return;
            }
            // Ensure headers haven't been sent yet
            if (! headers_sent()) {
                error_log('Redirecting to '.$redirect->url);
                wp_redirect($redirect->url, $redirect->status);
                exit;
            }
        } catch (\Exception $e) {
            // Log error if needed
            error_log('Quick Redirect Manager: '.$e->getMessage());

            return;
        }
    }
}
