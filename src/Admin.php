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

    public function __construct()
    {
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
        if (! current_user_can('manage_options')) {
            wp_die($this->messages['unauthorized']);
        }

        $this->handleFormSubmission();
        $this->handleDeletion();

        echo View::render('admin', ['redirects' => $this->getRedirects()]);
    }

    private function handleFormSubmission()
    {
        // permission checking
        if (! isset($_POST['submit_redirect'])) {
            return;
        }

        if (! $this->verifyNonce('redirect_nonce', 'add_redirect')) {
            wp_die($this->messages['security_check']);
        }

        // validate source_url
        if (! isset($_POST['source_url'])) {
            return;
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

        $source_url = UrlValidator::normalizeUrl($source_url);
        if (empty($source_url)) {
            return;
        }

        // validate target_url
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

        $target_url = UrlValidator::normalizeUrl($target_url);
        if (empty($target_url)) {
            return;
        }

        // validate redirect_type
        $type = isset($_POST['redirect_type']) ? intval($_POST['redirect_type']) : 301;
        $redirect_type = in_array($type, [301, 302]) ? $type : 301;

        // save redirection
        if (Redirection::add($source_url, $target_url, $redirect_type)) {
            add_settings_error(
                'qrm_messages',
                'qrm_redirect_added',
                $this->messages['redirect_added'],
                'updated'
            );
        }

    }

    private function handleDeletion()
    {
        // check is delete request
        if (! isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['source'])) {
            return;
        }

        if (! $this->verifyNonce('delete_nonce', 'delete_redirect')) {
            wp_die($this->messages['security_check']);
        }

        // validate source_url
        if (! isset($_GET['source'])) {
            return null;
        }
        $source = sanitize_text_field(urldecode($_GET['source']));

        // delete redirection
        if ($source) {
            if (Redirection::delete($source)) {
                add_settings_error(
                    'qrm_messages',
                    'qrm_redirect_deleted',
                    $this->messages['redirect_deleted'],
                    'updated'
                );
            }
        }
    }

    private function verifyNonce(string $field, string $action): bool
    {
        return isset($_REQUEST[$field]) && wp_verify_nonce($_REQUEST[$field], $action);
    }

    private function getRedirects(): array
    {
        return get_option(Config::REDIRECTIONS_OPTION_KEY, []);
    }
}
