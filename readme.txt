=== Quick Redirect Manager ===
Contributors: phyphennerd
Tags: redirect, redirection, 301 redirect, 302 redirect, URL management
Requires at least: 5.0
Tested up to: 6.4.3
Stable tag: 1.0.0
Requires PHP: 8.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight WordPress URL redirection manager using native options table storage. Manage redirects without additional database tables.

== Description ==
Quick Redirect Manager provides an efficient way to manage URL redirections using WordPress's native options table for storage.

Features:
* Simple and intuitive WordPress admin interface
* Support for both 301 (permanent) and 302 (temporary) redirects
* Multiple redirection types:
 - Internal path to internal path (/old-page → /new-page)
 - Internal path to external URL (/external-link → https://example.com)
* Lightweight - uses WordPress options table (no additional tables)
* Built with performance in mind

== Installation ==
1. Upload `quick-redirect-manager` folder to `/wp-content/plugins/`
2. Activate through WordPress admin panel
3. Go to Settings → Redirections in WordPress admin
4. Start adding redirections

== Frequently Asked Questions ==
= Does this create new database tables? =
No, it uses WordPress native options table for storage.

= What types of redirects are supported? =
Both 301 (permanent) and 302 (temporary) redirects are supported.

== Screenshots ==
1. Main dashboard interface for managing redirections

== Changelog ==
= 1.0.0 =
* Initial release with core redirection features
* Support for 301 and 302 redirects
* Internal and external URL redirection support

== Upgrade Notice ==
= 1.0.0 =
First stable release with core redirection functionality
