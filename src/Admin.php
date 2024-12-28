<?php

namespace PNerd\QuickRedirectManager;

class Admin
{
    private $messages = [
        'invalid_source_url' => 'Invalid source URL format.',
        'invalid_target_url' => 'Invalid target URL format.',
        'duplicate_source' => 'A redirect for this source URL already exists.',
        'unauthorized' => 'Unauthorized user',
        'security_check' => 'Security check failed',
        'redirect_added' => 'Redirection added successfully!',
        'redirect_deleted' => 'Redirection deleted successfully!',
    ];

    private $redirection;

    public function __construct()
    {
        $this->redirection = new Redirection;
        add_action('admin_menu', [$this, 'addMenuPage']);
    }

    public function addMenuPage()
    {
        add_options_page(
            Config::SETTINGS_PAGE_TITLE,
            Config::SETTINGS_PAGE_TITLE,
            'manage_options',
            Config::SETTINGS_PAGE_SLUG,
            [$this, 'renderPage']
        );
    }

    public function renderPage()
    {
        if (! $this->canUserManage()) {
            wp_die($this->messages['unauthorized']);
        }

        $this->handleFormSubmission();
        $this->handleDeletion();

        echo View::render('admin', [
            'redirects' => $this->getRedirects(),
        ]);
    }

    private function handleFormSubmission()
    {
        if (! isset($_POST['submit_redirect'])) {
            return;
        }

        if (! $this->verifyNonce('redirect_nonce', 'add_redirect')) {
            wp_die($this->messages['security_check']);
        }

        $source_url = $this->getSourceUrl();
        if (empty($source_url)) {
            return;
        }

        $target_url = $this->getTargetUrl();
        if (empty($target_url)) {
            return;
        }

        $redirect_type = $this->getRedirectType();
        $result = $this->redirection->add($source_url, $target_url, $redirect_type);

        add_settings_error(
            'qrm_messages',
            $result['success'] ? 'qrm_redirect_added' : 'qrm_duplicate_source',
            $result['message'],
            $result['success'] ? 'updated' : 'error'
        );
    }

    private function handleDeletion()
    {
        if (! $this->isDeletionRequest()) {
            return;
        }

        if (! $this->verifyNonce('delete_nonce', 'delete_redirect')) {
            wp_die($this->messages['security_check']);
        }

        $source = $this->getSourceFromRequest();
        if ($source) {
            $result = $this->redirection->delete($source);
            add_settings_error(
                'qrm_messages',
                $result['success'] ? 'qrm_redirect_deleted' : 'qrm_redirect_error',
                $result['message'],
                $result['success'] ? 'updated' : 'error'
            );
        }
    }

    private function canUserManage(): bool
    {
        return current_user_can('manage_options');
    }

    private function verifyNonce(string $field, string $action): bool
    {
        return isset($_REQUEST[$field]) && wp_verify_nonce($_REQUEST[$field], $action);
    }

    private function getSourceUrl(): ?string
    {
        if (! isset($_POST['source_url'])) {
            return null;
        }

        $source_url = sanitize_text_field($_POST['source_url']);

        if (! UrlValidator::isValid($source_url)) {
            add_settings_error(
                'qrm_messages',
                'qrm_invalid_source',
                $this->messages['invalid_source_url'],
                'error'
            );

            return null;
        }

        return UrlValidator::normalizeUrl($source_url);
    }

    private function getTargetUrl(): ?string
    {
        if (! isset($_POST['target_url'])) {
            return null;
        }

        $target_url = sanitize_text_field($_POST['target_url']);

        if (! UrlValidator::isValid($target_url)) {
            add_settings_error(
                'qrm_messages',
                'qrm_invalid_target',
                $this->messages['invalid_target_url'],
                'error'
            );

            return null;
        }

        return UrlValidator::normalizeUrl($target_url);
    }

    private function getRedirectType(): int
    {
        $type = isset($_POST['redirect_type']) ? intval($_POST['redirect_type']) : 301;

        return in_array($type, [301, 302]) ? $type : 301;
    }

    private function getRedirects(): array
    {
        return $this->redirection->getAll();
    }

    private function isDeletionRequest(): bool
    {
        return isset($_GET['action'])
            && $_GET['action'] === 'delete'
            && isset($_GET['source']);
    }

    private function getSourceFromRequest(): ?string
    {
        if (! isset($_GET['source'])) {
            return null;
        }

        return sanitize_text_field(urldecode($_GET['source']));
    }
}
