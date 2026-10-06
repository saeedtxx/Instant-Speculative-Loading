=== Instant Speculative Loading ===
Contributors: saeedtx
Tags: speculation rules, prerender, prefetch, performance, speed
Requires at least: 5.7
Tested up to: 7.1
Stable tag: 1.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Complete control suite for the native W3C Speculation Rules API with granular configuration, Save-Data support, and zero JavaScript overhead.

== Description ==
Instant Speculative Loading provides an advanced, user-friendly control suite for the browser-native **W3C Speculation Rules API**. Rather than using generic hardcoded rules, it gives administrators full visual control over how pages are pre-loaded without writing any code.

Unlike simple experimental plugins, Instant Speculative Loading includes:
* **Custom Delivery Modes:** Switch effortlessly between full Prerender (near 0ms load) and lightweight Prefetch (HTML cache only).
* **Eagerness Tuning:** Adjust trigger sensitivity (Moderate for hover intent, Conservative for click initiation, or Eager).
* **Network & Save-Data Awareness:** Detects client `Save-Data` headers automatically to avoid wasting mobile bandwidth on metered networks.
* **Granular Exclusions:** Visual exclusion managers for custom URL wildcard paths and custom CSS selectors.
* **Role Safety:** Option to disable speculative loading for logged-in administrators and authors.
* **E-Commerce Safe:** Automatically detects and excludes WooCommerce Cart, Checkout, and Account endpoints.
* **Zero Runtime JS:** Relies 100% on native browser rendering engines without injecting heavy third-party scripts.

== Installation ==
1. Upload the `instant-speculative-loading` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings > Speculative Loading** to configure your delivery preferences.

== Frequently Asked Questions ==
= How does this differ from basic speculation rules plugins? =
Basic plugins inject rigid, non-configurable scripts into the footer. Instant Speculative Loading offers a complete WordPress dashboard suite to customize delivery modes (Prerender vs. Prefetch), set eagerness thresholds, respect mobile Save-Data preferences, and manage custom URL/selector exclusions visually.

= Does it preload admin or cart pages? =
No. Sensitive admin routes and WooCommerce checkout/cart pages are strictly excluded out of the box.

== Changelog ==
= 1.1.0 =
* Major release: Added dedicated admin settings dashboard under Settings > Speculative Loading.
* Added support for toggling Prerender vs. Prefetch delivery modes.
* Added Eagerness selector (Moderate, Conservative, Eager).
* Added Save-Data client network header detection.
* Added user exclusion controls for logged-in sessions.
* Added visual input fields for custom URL paths and CSS selector exclusions.
