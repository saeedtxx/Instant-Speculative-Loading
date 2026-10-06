# Instant Speculative Loading ⚡

> Accelerate WordPress page navigation to near-zero latency using the native browser Speculation Rules API — with zero external JavaScript.

[![WordPress Tested](https://img.shields.io/badge/WordPress-5.7+-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net/)
[![License: GPL v2+](https://img.shields.io/badge/License-GPLv2+-green.svg)](LICENSE)

---

## 🚀 Overview

**Instant Speculative Loading** is an ultra-lightweight, zero-overhead WordPress plugin that brings the modern W3C [Speculation Rules API](https://developer.mozilla.org/en-US/docs/Web/API/Speculation_Rules_API) to your website.

Unlike legacy preloading libraries (such as `instant.page` or `InstantClick`) that inject heavy JavaScript bundles and trigger synthetic fetches, this plugin communicates directly with Chromium's native rendering engine. When a visitor hovers over an internal link, the browser prerenders the target page in the background, making navigation feel instantaneous (0ms perceived latency).

## ✨ Key Features

- **⚡ Zero JS Overhead:** Pure native browser prerendering with zero external JavaScript libraries or CSS injected.
- **🎯 Smart Intent (Moderate Eagerness):** Prerenders pages precisely when the user demonstrates intent (hover/pointerdown), saving server resources and bandwidth.
- **🛡️ Built-in Safety & Exclusions:** Automatically ignores sensitive routes:
  - Admin panel (`/wp-admin/*`)
  - Login/Logout endpoints (`wp-login.php`, logout actions)
  - Draft and preview URLs (`?preview=true`)
  - File downloads (`.pdf`, `.zip`, `.mp4`, `.exe`, etc.)
  - Links with `rel="nofollow"` or `.no-prerender` class
- **🛒 E-commerce Compatible:** Automatically detects and excludes WooCommerce Cart, Checkout, and My Account pages.
- **📈 Core Web Vitals Friendly:** Drastically improves **LCP** (Largest Contentful Paint) and perceived navigation speed across internal visits.

## 🛠️ How It Works

The plugin injects a native `<script type="speculationrules">` configuration in the document footer:

```html
<script type="speculationrules" id="instant-speculative-loading-rules">
{
  "prerender": [
    {
      "source": "document",
      "where": { ... },
      "eagerness": "moderate"
    }
  ]
}
</script>
