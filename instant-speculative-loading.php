<?php
/**
 * Plugin Name: Instant Speculative Loading
 * Description: Accelerates page navigation to near-zero latency using the native browser Speculation Rules API with zero external JavaScript.
 * Version: 1.0.0
 * Author: Saeed Tosifyan
 * Author URI: https://linkedin.com/in/saeedtx
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: instant-speculative-loading
 * Requires at least: 5.7
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * تزریق بومی قوانین پیش‌بارگذاری (Speculation Rules) در فوتر
 */
function isl_inject_speculation_rules() {
    // عدم اجرا در پنل مدیریت، فیدها، درخواست‌های REST یا کرون
    if (is_admin() || is_feed() || (function_exists('wp_is_json_request') && wp_is_json_request()) || (defined('DOING_CRON') && DOING_CRON)) {
        return;
    }

    // مسیرهای حساس وردپرس که نباید پیش‌بارگذاری شوند
    $exclude_paths = [
        '/wp-admin/*',
        '/wp-login.php*',
        '/*\\?*preview=true*',
        '/*\\?*action=logout*',
    ];

    // در صورت فعال بودن ووکامرس، صفحات حساس فروشگاه پیش‌بارگذاری نشوند
    if (class_exists('WooCommerce')) {
        $cart_id     = wc_get_page_id('cart');
        $checkout_id = wc_get_page_id('checkout');
        $account_id  = wc_get_page_id('myaccount');

        if ($cart_id > 0 && ($path = wp_parse_url(get_permalink($cart_id), PHP_URL_PATH))) {
            $exclude_paths[] = rtrim($path, '/') . '/*';
        }
        if ($checkout_id > 0 && ($path = wp_parse_url(get_permalink($checkout_id), PHP_URL_PATH))) {
            $exclude_paths[] = rtrim($path, '/') . '/*';
        }
        if ($account_id > 0 && ($path = wp_parse_url(get_permalink($account_id), PHP_URL_PATH))) {
            $exclude_paths[] = rtrim($path, '/') . '/*';
        }
    }

    /**
     * امکان شخصی‌سازی مسیرهای استثنا توسط فیلتر وردپرس
     */
    $exclude_paths = apply_filters('isl_speculation_rules_exclude_paths', $exclude_paths);

    // ساختار استاندارد قوانین بر اساس مستندات W3C
    $rules = [
        'prerender' => [
            [
                'source' => 'document',
                'where'  => [
                    'and' => [
                        [
                            'href_matches' => '/*'
                        ],
                        [
                            'not' => [
                                'href_matches' => array_values(array_unique($exclude_paths))
                            ]
                        ],
                        [
                            'not' => [
                                'selector_matches' => '.no-prerender, [rel~="nofollow"], a[href$=".pdf"], a[href$=".zip"], a[href$=".rar"], a[href$=".mp4"], a[href$=".mp3"], a[href$=".tar.gz"], a[href$=".exe"]'
                            ]
                        ]
                    ]
                ],
                'eagerness' => 'moderate'
            ]
        ]
    ];

    $rules_json = wp_json_encode($rules, JSON_UNESCAPED_SLASHES);

    if (!empty($rules_json)) {
        wp_print_inline_script_tag(
            $rules_json,
            [
                'type' => 'speculationrules',
                'id'   => 'instant-speculative-loading-rules',
            ]
        );
    }
}

add_action('wp_footer', 'isl_inject_speculation_rules', 9999);
