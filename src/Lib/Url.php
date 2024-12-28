<?php

namespace PNerd\QuickRedirectManager\Lib;

use PNerd\QuickRedirectManager\DTO\Redirect;
use PNerd\QuickRedirectManager\Redirection;

class Url
{
    /**
     * List of allowed URL schemes
     *
     * @var list<string>
     */
    private static $allowedSchemes = ['http', 'https'];

    /**
     * Validates a URL or path
     */
    public static function isValid(string $url): bool
    {
        // Check if it's a relative path
        if (self::isRelativePath($url)) {
            return true;
        }

        // Check if it's a valid URL
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Validate URL scheme
        $scheme = parse_url($url, PHP_URL_SCHEME);

        return in_array(strtolower($scheme), self::$allowedSchemes);
    }

    /**
     * Checks if the given string is a relative path
     */
    private static function isRelativePath(string $path): bool
    {
        // Path must start with / and not contain protocol or domain
        return str_starts_with($path, '/') &&
               ! str_contains($path, '://') &&
               ! preg_match('/^\/\//', $path);
    }

    /**
     * Normalizes a URL or path
     */
    public static function normalizeUrl(string $url): string
    {
        $url = strtok($url, '?');

        // If it's already a full URL, validate and return
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return self::normalizeFullUrl($url);
        }

        return self::normalizePath($url);
    }

    /**
     * Normalizes a full URL
     */
    private static function normalizeFullUrl(string $url): string
    {
        $parsedUrl = parse_url($url);

        // Ensure scheme is present and valid
        $scheme = isset($parsedUrl['scheme']) ? strtolower($parsedUrl['scheme']) : 'https';
        if (! in_array($scheme, self::$allowedSchemes)) {
            $scheme = 'https';
        }

        // Build normalized URL
        $normalizedUrl = $scheme.'://'.$parsedUrl['host'];

        // Add port if non-standard
        if (isset($parsedUrl['port'])) {
            $normalizedUrl .= ':'.$parsedUrl['port'];
        }

        // Add path
        if (isset($parsedUrl['path'])) {
            $normalizedUrl .= self::normalizePath($parsedUrl['path']);
        }

        // Add query string
        if (isset($parsedUrl['query'])) {
            $normalizedUrl .= '?'.$parsedUrl['query'];
        }

        // Add fragment
        if (isset($parsedUrl['fragment'])) {
            $normalizedUrl .= '#'.$parsedUrl['fragment'];
        }

        return $normalizedUrl;
    }

    /**
     * Normalizes a path
     */
    private static function normalizePath(string $path): string
    {
        // Ensure path starts with /
        if (! str_starts_with($path, '/')) {
            $path = '/'.$path;
        }

        // Remove multiple consecutive slashes
        $path = preg_replace('/\/+/', '/', $path);

        // Remove trailing slash except for root path
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        return $path;
    }

    public static function getRedirect(string $serverPath): ?Redirect
    {
        $queries = self::extractQueries($serverPath);
        $currentPath = self::normalizeUrl($serverPath);

        $redirection = Redirection::get($currentPath);

        if (! $redirection) {
            return null;
        }

        $targetUrl = Redirection::targetUrl($redirection);
        $redirectType = Redirection::redirectType($redirection);

        $redirectUrl = self::concatQueries($targetUrl, $queries);

        return new Redirect($redirectUrl, $redirectType);
    }

    /**
     * Extracts and returns query parameters from a URL as an array
     *
     * @return array<string, string>
     */
    private static function extractQueries(string $url): array
    {
        // If URL is invalid, return empty array
        if (! self::isValid($url)) {
            return [];
        }

        // Parse the URL and extract query string
        $parsedUrl = parse_url($url);
        if (! isset($parsedUrl['query'])) {
            return [];
        }

        // Parse query string into array
        $queryParams = [];
        parse_str($parsedUrl['query'], $queryParams);

        return $queryParams;
    }

    /**
     * Concatenates a URL with query parameters
     *
     * @param  array<string, string>  $queries
     */
    private static function concatQueries(string $url, array $queries): string
    {
        // If no queries provided, return the original URL
        if (empty($queries)) {
            return $url;
        }

        // Check if URL already has query parameters
        $hasQueries = str_contains($url, '?');

        // Build query string from new queries
        $queryString = http_build_query($queries);

        // Concatenate properly based on existing query parameters
        if ($hasQueries) {
            return $url.'&'.$queryString;
        }

        return $url.'?'.$queryString;
    }
}
