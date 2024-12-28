<?php

namespace PNerd\QuickRedirectManager\DTO;

class Redirect
{
    public string $url;

    public int $status;

    public function __construct(
        string $url,
        int $status,
    ) {
        $this->url = $url;
        $this->status = $status;
    }
}
