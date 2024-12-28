<?php
use PNerd\QuickRedirectManager\Config;

if (! defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(Config::SETTINGS_PAGE_TITLE); ?></h1>

    <div class="notice notice-info">
        <p><strong>Supported Redirect Types:</strong></p>
        <ul style="list-style-type: disc; padding-left: 20px;">
            <li>Internal path to internal path (e.g., <code>/old-page</code> → <code>/new-page</code>)</li>
            <li>Internal path to external URL (e.g., <code>/external-link</code> → <code>https://example.com</code>)</li>
            <li>Full URL to internal path (e.g., <code>https://yourdomain.com/old-page</code> → <code>/new-page</code>)</li>
            <li>Full URL to external URL (e.g., <code>https://yourdomain.com/old-page</code> → <code>https://example.com</code>)</li>
        </ul>
    </div>

    <?php settings_errors('qrm_messages'); ?>

    <!-- Add new redirect form -->
    <h2>Add New Redirection</h2>
    <form method="post" action="">
        <?php wp_nonce_field('add_redirect', 'redirect_nonce'); ?>
        <table class="form-table">
            <tr>
                <th><label for="source_url">Source URL</label></th>
                <td>
                    <input type="text" name="source_url" id="source_url" class="regular-text" required>
                    <p class="description">Enter the URL you want to redirect from. You can use either:</p>
                    <ul class="description" style="list-style-type: disc; margin-left: 20px; margin-top: 5px;">
                        <li>Internal path (e.g., <code>/old-page</code>)</li>
                        <li>Full URL (e.g., <code>https://yourdomain.com/old-page</code>)</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <th><label for="target_url">Target URL</label></th>
                <td>
                    <input type="text" name="target_url" id="target_url" class="regular-text" required>
                    <p class="description">Enter the URL you want to redirect to. You can use either:</p>
                    <ul class="description" style="list-style-type: disc; margin-left: 20px; margin-top: 5px;">
                        <li>Internal path (e.g., <code>/new-page</code>)</li>
                        <li>External URL (e.g., <code>https://example.com</code>)</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <th><label for="redirect_type">Redirect Type</label></th>
                <td>
                    <select name="redirect_type" id="redirect_type">
                        <option value="301">301 - Permanent</option>
                        <option value="302">302 - Temporary</option>
                    </select>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit_redirect" class="button button-primary" value="Add Redirection">
        </p>
    </form>

    <!-- Display existing redirects -->
    <h2>Existing Redirections</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Source URL</th>
                <th>Target URL</th>
                <th>Type</th>
                <th>Hits</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($redirects as $source => $redirect) { ?>
            <tr>
                <td><?php echo esc_html($source); ?></td>
                <td><?php echo esc_html($redirect['target_url']); ?></td>
                <td><?php echo esc_html($redirect['redirect_type']); ?></td>
                <td><?php echo esc_html($redirect['hits']); ?></td>
                <td><?php echo esc_html($redirect['created_at']); ?></td>
                <td>
                    <a href="<?php echo wp_nonce_url(
                        admin_url('options-general.php?page='.Config::SETTINGS_PAGE_SLUG.'&action=delete&source='.urlencode($source)),
                        'delete_redirect',
                        'delete_nonce'
                    ); ?>"
                       onclick="return confirm('Are you sure you want to delete this redirect?')"
                       class="button button-small">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
