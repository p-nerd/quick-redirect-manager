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

        // Register redirect template
        add_action(
            'template_redirect',
            [$this, 'handleRedirection']
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
        if (is_admin()) {
            return;
        }

        $serverPath = $_SERVER['REQUEST_URI'];

        $redirect = Url::getRedirect($serverPath);

        if (! $redirect) {
            return;
        }
        wp_redirect($redirect->url, $redirect->status);
        exit;
    }
}
