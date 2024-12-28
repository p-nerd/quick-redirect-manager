<?php

namespace PNerd\QuickRedirectManager;

class Redirection
{
    /**
     * Gets all redirections from the options table
     */
    public function getAll(): array
    {
        return get_option(Config::REDIRECTIONS_OPTION_KEY, []);
    }

    /**
     * Adds a new redirection
     */
    public function add(string $source_url, string $target_url, int $redirect_type): array
    {
        if (! $this->validateUrls($source_url, $target_url)) {
            return [
                'success' => false,
                'message' => 'Invalid URL format provided.',
            ];
        }

        $redirects = $this->getAll();
        $normalized_source = UrlValidator::normalizeUrl($source_url);

        if (isset($redirects[$normalized_source])) {
            return [
                'success' => false,
                'message' => 'A redirect for this source URL already exists.',
            ];
        }

        $redirects[$normalized_source] = [
            'target_url' => UrlValidator::normalizeUrl($target_url),
            'redirect_type' => $this->validateRedirectType($redirect_type),
            'hits' => 0,
            'created_at' => current_time('mysql'),
        ];

        update_option(Config::REDIRECTIONS_OPTION_KEY, $redirects);

        return [
            'success' => true,
            'message' => 'Redirection added successfully!',
        ];
    }

    /**
     * Deletes a redirection
     */
    public function delete(string $source_url): array
    {
        $redirects = $this->getAll();
        $normalized_source = UrlValidator::normalizeUrl($source_url);

        if (! isset($redirects[$normalized_source])) {
            return [
                'success' => false,
                'message' => 'Redirect not found.',
            ];
        }

        unset($redirects[$normalized_source]);
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
    private function validateUrls(string $source_url, string $target_url): bool
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
