=== Instant Speculative Loading ===
Contributors: saeedtx
Tags: speculation rules, prerender, prefetch, performance, speed
Requires at least: 5.7
Tested up to: 7.1
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Boost your WordPress site speed to instant navigation using the native Speculation Rules API with zero JavaScript overhead.

== Description ==
Instant Speculative Loading enables the modern **Speculation Rules API** across your website. Unlike legacy libraries (such as instant.page or InstantClick) that load heavy JavaScript bundles, this plugin relies entirely on the browser's native engine to prerender same-origin links right before users click them.

Features:
* **Near-Zero Latency:** Pages open almost instantaneously when users click internal links.
* **Pure Native Performance:** Exactly zero third-party JS scripts or CSS files loaded.
* **Safe by Design:** Automatically skips `/wp-admin/`, logout endpoints, previews, file downloads (.pdf, .zip), and WooCommerce checkout/cart pages.
* **Core Web Vitals Optimized:** Helps improve LCP (Largest Contentful Paint) and INP (Interaction to Next Paint) for seamless navigation.

== Installation ==
1. Upload the `instant-speculative-loading` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. The native speculation rules script will automatically activate on all public pages.

== Frequently Asked Questions ==
= Which browsers support Speculation Rules? =
All Chromium-based browsers (Google Chrome, Microsoft Edge, Brave, Opera) natively support Speculation Rules. Browsers without support will simply ignore the script tag and load pages normally without errors.

= Does it preload admin or cart pages? =
No. Admin routes, logout endpoints, downloads, and sensitive pages like the WooCommerce cart and checkout are strictly excluded.

== Changelog ==
= 1.0.0 =
* Initial public release with native prerender support, hover eagerness, and automated exclusions.
