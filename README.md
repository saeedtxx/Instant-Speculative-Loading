
![Instant Speculative Loading](assets/banner-772x250.png)

# Instant Speculative Loading ⚡

> Advanced control suite for the native W3C Speculation Rules API — featuring granular admin settings, network awareness, and zero external JavaScript overhead.

[![WordPress Tested](https://img.shields.io/badge/WordPress-5.7+-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net/)
[![License: GPL v2+](https://img.shields.io/badge/License-GPLv2+-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

---

## 🚀 Overview

**Instant Speculative Loading** is an ultra-lightweight, zero-dependency WordPress performance suite that implements the modern W3C [Speculation Rules API](https://developer.mozilla.org/en-US/docs/Web/API/Speculation_Rules_API).

Unlike legacy preloading libraries (such as `instant.page` or `InstantClick`) that inject heavy JavaScript bundles and trigger synthetic background fetches, this plugin coordinates directly with Chromium's native rendering engine. When a visitor hovers over or intends to click an internal link, the browser prerenders the target page in the background, making navigation feel instantaneous.

## ✨ Key Features

- **⚙️ Dedicated Admin Dashboard:** Visual settings panel located under **Settings > Speculative Loading** — configure behavior without editing theme files.
- **🔄 Dual Delivery Modes:**
  - **Prerender:** Background DOM rendering for near-zero latency page transitions.
  - **Prefetch:** Bandwidth-conscious mode that pre-downloads HTML only.
- **🎯 Granular Eagerness Controls:** Select between `Moderate` (hover intent), `Conservative` (pointer down / click start), or `Eager` (viewport detection).
- **📶 Network & Save-Data Awareness:** Automatically inspects the client `Save-Data` HTTP header and suspends background requests on metered mobile connections.
- **🛡️ Logged-In User Protection:** Toggle to disable speculative loading for administrators and editors to prevent unnecessary server strain.
- **🚫 Visual Exclusions Manager:** Define custom URL wildcard paths (`*`) and CSS selectors directly from the WordPress dashboard.
- **🛒 E-commerce Compatible:** Automatically detects and excludes WooCommerce Cart, Checkout, and My Account endpoints.
- **⚡ Zero JavaScript Overhead:** 100% native browser prerendering with no external JS or CSS assets loaded.

## 🛠️ How It Works

The plugin registers an inline configuration tag inside the document footer:

```html
<script type="speculationrules" id="instant-speculative-loading-rules">
{
  "prerender": [
    {
      "source": "document",
      "where": {
        "and": [
          { "href_matches": "/*" },
          { "not": { "href_matches": [...] } },
          { "not": { "selector_matches": "..." } }
        ]
      },
      "eagerness": "moderate"
    }
  ]
}
</script>

```

Supporting browsers (Chrome, Edge, Brave, Opera) parse the specification directly. Non-supporting browsers cleanly ignore the script tag without generating console warnings.

## 🔌 Developer Hooks

Extend or modify excluded paths programmatically through the `instant_speculative_loading_exclude_paths` filter:

```php
add_filter('instant_speculative_loading_exclude_paths', function( array $exclude_paths ): array {
    $exclude_paths[] = '/my-custom-portal/*';
    $exclude_paths[] = '/private-downloads/*';
    return $exclude_paths;
});

```

To prevent a specific link on your site from being prerendered via markup, append `rel="nofollow"` or the `.no-prerender` class:

```html
<a href="/special-page" class="no-prerender">Link Title</a>

```

## 📦 Installation

### From WordPress Dashboard:

1. Download `instant-speculative-loading.zip` from the latest [Releases](https://www.google.com/search?q=https://github.com/saeedtx/Instant-Speculative-Loading/releases).
2. Navigate to **Plugins > Add New > Upload Plugin**.
3. Upload the `.zip` archive and click **Activate**.
4. Configure options under **Settings > Speculative Loading**.

### Via Git:

```bash
git clone [https://github.com/saeedtx/instant-speculative-loading.git](https://github.com/saeedtx/instant-speculative-loading.git) wp-content/plugins/instant-speculative-loading

```

## 👤 Author

**Saeed Tosifyan**

* Website: [medseo.ir](https://medseo.ir)
* LinkedIn: [linkedin.com/in/saeedtx](https://linkedin.com/in/saeedtx)

## 📄 License

This project is open-source software licensed under the GNU General Public License v2.0 or later.

```

```
