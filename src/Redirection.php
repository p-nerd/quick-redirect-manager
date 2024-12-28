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
    public static function delete(string $source_url): array
    {
        $redirects = self::all();

        unset($redirects[$source_url]);
        update_option(Config::REDIRECTIONS_OPTION_KEY, $redirects);

        return [
            'success' => true,
            'message' => 'Redirection deleted successfully!',
        ];
    }

    /**
     * Increments hit count for a redirection
     */
    public function incrementHits(string $source_url): void
    {
        $redirects = $this->getAll();
        $normalized_source = UrlValidator::normalizeUrl($source_url);

        if (isset($redirects[$normalized_source])) {
            $redirects[$normalized_source]['hits']++;
            update_option(Config::REDIRECTIONS_OPTION_KEY, $redirects);
        }
    }

    /**
     * Gets a specific redirection by source URL
     */
    public function get(string $source_url): ?array
    {
        $redirects = $this->getAll();
        $normalized_source = UrlValidator::normalizeUrl($source_url);

        return $redirects[$normalized_source] ?? null;
    }

    /**
     * Validates both source and target URLs
     */
    private static function validateUrls(string $source_url, string $target_url): bool
    {
        return UrlValidator::isValid($source_url) && UrlValidator::isValid($target_url);
    }

    /**
     * Validates and normalizes redirect type
     */
    private function validateRedirectType(int $type): int
    {
        return in_array($type, [301, 302]) ? $type : 301;
    }
}
