
# Instant Speculative Loading

Accelerates WordPress page navigation to near-zero latency using the native browser Speculation Rules API with zero external JavaScript overhead.

[![WordPress Tested](https://img.shields.io/badge/WordPress-5.7+-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net/)
[![License: GPL v2+](https://img.shields.io/badge/License-GPLv2+-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Overview

**Instant Speculative Loading** is an ultra-lightweight, zero-dependency WordPress plugin that implements the modern W3C [Speculation Rules API](https://developer.mozilla.org/en-US/docs/Web/API/Speculation_Rules_API).

Unlike legacy preloading scripts (such as `instant.page` or `InstantClick`) that inject JavaScript bundles and trigger synthetic background fetches, this plugin communicates directly with Chromium's native rendering engine. When a visitor hovers over an internal link, the browser prerenders the target page in the background, making navigation feel instantaneous.

## Features

- **Zero JavaScript Overhead:** Pure native browser prerendering with no external JavaScript or CSS assets loaded.
- **Moderate Eagerness:** Initiates prerendering when a user hovers over or starts interacting with an internal link, preserving server resources and client bandwidth.
- **Automated Exclusions:** Automatically skips non-HTML resources and administrative endpoints:
  - Admin dashboard (`/wp-admin/*`)
  - Login and authentication endpoints (`wp-login.php`, `?action=logout`)
  - Post preview URLs (`?preview=true`)
  - Binary and media downloads (`.pdf`, `.zip`, `.rar`, `.mp4`, `.mp3`, `.exe`, `.tar.gz`)
  - Links containing `rel="nofollow"` or `.no-prerender` CSS class
- **WooCommerce Ready:** Detects active WooCommerce installations and automatically excludes the Cart, Checkout, and My Account pages from prerendering.
- **Core Web Vitals Optimization:** Improves Largest Contentful Paint (LCP) and Interaction to Next Paint (INP) across internal navigation.

## How It Works

The plugin registers a script tag inside the document footer using WordPress core standards:

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

Supporting browsers (Chrome, Edge, Brave, Opera) parse the specification directly. Non-supporting browsers cleanly ignore the script tag without generating console warnings or errors.

## Developer Hooks

Extend or modify excluded paths through the `isl_speculation_rules_exclude_paths` filter:

```php
add_filter('isl_speculation_rules_exclude_paths', function( array $exclude_paths ): array {
    $exclude_paths[] = '/my-custom-portal/*';
    $exclude_paths[] = '/private-downloads/*';
    return $exclude_paths;
});

```

To prevent a specific link on your site from being prerendered, append `rel="nofollow"` or the `.no-prerender` class:

```html
<a href="/special-page" class="no-prerender">Link Title</a>

```

## Installation

### Manual Installation

1. Clone or download this repository.
2. Place the `instant-speculative-loading` folder inside your `/wp-content/plugins/` directory.
3. Activate the plugin via **Plugins > Installed Plugins** in the WordPress admin panel.

### Git Submodule / Clone

```bash
git clone [https://github.com/saeedtx/instant-speculative-loading.git](https://github.com/saeedtx/instant-speculative-loading.git) wp-content/plugins/instant-speculative-loading

```

## Author

**Saeed Tosifyan**

* Website: [medseo.ir](https://medseo.ir)
* LinkedIn: [linkedin.com/in/saeedtx](https://linkedin.com/in/saeedtx)

## License

This plugin is open-source software licensed under the GNU General Public License v2.0 or later.

```

```
