<?php

namespace PNerd\QuickRedirectManager;

class Redirection
{
    /**
     * Gets all redirections from the options table
     */
    public static function all(): array
    {
        return get_option(Config::REDIRECTIONS_OPTION_KEY, []);
    }

    /**
     * Gets a specific redirection by source URL
     */
    public static function get(string $source_url): ?array
    {
        $redirects = self::all();

        return $redirects[$source_url] ?? null;
    }

    /**
     * Adds a new redirection
     */
    public static function add(string $source_url, string $target_url, int $redirect_type): bool
    {
        $redirects = self::all();

        $redirects[$source_url] = [
            'target_url' => $target_url,
            'redirect_type' => $redirect_type,
            'created_at' => current_time('mysql'),
        ];

        update_option(Config::REDIRECTIONS_OPTION_KEY, $redirects);

        return true;
    }

    /**
     * Deletes a redirection
     */
    public static function delete(string $source_url): bool
    {
        $redirects = self::all();

        unset($redirects[$source_url]);
        update_option(Config::REDIRECTIONS_OPTION_KEY, $redirects);

        return true;
    }

    public static function targetUrl(array $redirection)
    {
        return $redirection['target_url'];
    }

    public static function redirectType(array $redirection)
    {
        return $redirection['redirect_type'];
    }
}
