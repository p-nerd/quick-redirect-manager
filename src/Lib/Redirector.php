<?php

namespace PNerd\QuickRedirectManager\Lib;

use PNerd\QuickRedirectManager\DTO\Redirect;
use PNerd\QuickRedirectManager\Redirection;

class Redirector
{
    public function getRedirect(string $serverPath): ?Redirect
    {
        $serverPath = $_SERVER['REQUEST_URI'];

        $queries = Url::extractQueries($serverPath);
        $currentPath = Url::normalizeUrl($serverPath);

        $redirection = Redirection::get($currentPath);

        if (! $redirection) {
            return null;
        }

        $targetUrl = Redirection::targetUrl($redirection);
        $redirectType = Redirection::redirectType($redirection);

        $redirectUrl = Url::concatQueries($targetUrl, $queries);

        return new Redirect($redirectUrl, $redirectType);
    }
}
