<?php
/**
 * Plugin Name: Instant Speculative Loading
 * Description: High-performance control suite for the native W3C Speculation Rules API with granular administrative controls, data-saver detection, and zero external JS.
 * Version: 1.1.0
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

final class Instant_Speculative_Loading {

    private const OPTION_KEY = 'isl_settings';

    public static function init(): void {
        add_action('admin_menu', [__CLASS__, 'register_settings_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('wp_footer', [__CLASS__, 'render_speculation_rules'], 9999);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [__CLASS__, 'add_settings_action_link']);
    }

    /**
     * تنظیمات پیش‌فرض
     */
    public static function get_default_options(): array {
        return [
            'mode'                 => 'prerender', // prerender | prefetch
            'eagerness'            => 'moderate',  // moderate | conservative | eager
            'disable_logged_in'    => '1',
            'respect_save_data'    => '1',
            'custom_exclude_paths' => "/wp-login.php*\n/*\\?*preview=true*\n/*\\?*action=logout*",
            'custom_exclude_css'   => '.no-prerender, [rel~="nofollow"]',
        ];
    }

    public static function get_options(): array {
        $options = get_option(self::OPTION_KEY, []);
        return wp_parse_args(is_array($options) ? $options : [], self::get_default_options());
    }

    /**
     * افزودن صفحه تنظیمات به پیشخوان
     */
    public static function register_settings_menu(): void {
        add_options_page(
            __('Speculative Loading', 'instant-speculative-loading'),
            __('Speculative Loading', 'instant-speculative-loading'),
            'manage_options',
            'instant-speculative-loading',
            [__CLASS__, 'render_settings_page']
        );
    }

    /**
     * ثبت و اعتبارسنجی تنظیمات
     */
    public static function register_settings(): void {
        register_setting(
            'isl_settings_group',
            self::OPTION_KEY,
            [
                'type'              => 'array',
                'sanitize_callback' => [__CLASS__, 'sanitize_settings'],
                'default'           => self::get_default_options(),
            ]
        );
    }

    public static function sanitize_settings($input): array {
        $clean = self::get_default_options();

        if (is_array($input)) {
            if (isset($input['mode']) && in_array($input['mode'], ['prerender', 'prefetch'], true)) {
                $clean['mode'] = $input['mode'];
            }
            if (isset($input['eagerness']) && in_array($input['eagerness'], ['moderate', 'conservative', 'eager'], true)) {
                $clean['eagerness'] = $input['eagerness'];
            }
            $clean['disable_logged_in'] = !empty($input['disable_logged_in']) ? '1' : '0';
            $clean['respect_save_data'] = !empty($input['respect_save_data']) ? '1' : '0';

            if (isset($input['custom_exclude_paths'])) {
                $clean['custom_exclude_paths'] = sanitize_textarea_field($input['custom_exclude_paths']);
            }
            if (isset($input['custom_exclude_css'])) {
                $clean['custom_exclude_css'] = sanitize_text_field($input['custom_exclude_css']);
            }
        }

        return $clean;
    }

    public static function add_settings_action_link(array $links): array {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            esc_url(admin_url('options-general.php?page=instant-speculative-loading')),
            esc_html__('Settings', 'instant-speculative-loading')
        );
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * رندر صفحه تنظیمات
     */
    public static function render_settings_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $opts = self::get_options();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Instant Speculative Loading Settings', 'instant-speculative-loading'); ?></h1>
            <p><?php esc_html_e('Fine-tune how modern browsers prerender and prefetch your pages using native W3C Speculation Rules.', 'instant-speculative-loading'); ?></p>

            <form method="post" action="options.php">
                <?php
                settings_fields('isl_settings_group');
                ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e('Delivery Mode', 'instant-speculative-loading'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="radio" name="<?php echo esc_attr(self::OPTION_KEY); ?>[mode]" value="prerender" <?php checked($opts['mode'], 'prerender'); ?> />
                                    <strong>Prerender</strong> (<?php esc_html_e('Instant navigation, renders full HTML & DOM in background', 'instant-speculative-loading'); ?>)
                                </label><br />
                                <label>
                                    <input type="radio" name="<?php echo esc_attr(self::OPTION_KEY); ?>[mode]" value="prefetch" <?php checked($opts['mode'], 'prefetch'); ?> />
                                    <strong>Prefetch</strong> (<?php esc_html_e('Bandwidth-safe, pre-downloads HTML only without rendering', 'instant-speculative-loading'); ?>)
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Eagerness Level', 'instant-speculative-loading'); ?></th>
                        <td>
                            <select name="<?php echo esc_attr(self::OPTION_KEY); ?>[eagerness]">
                                <option value="moderate" <?php selected($opts['eagerness'], 'moderate'); ?>>Moderate (Triggers on hover intent / 200ms hold - Recommended)</option>
                                <option value="conservative" <?php selected($opts['eagerness'], 'conservative'); ?>>Conservative (Triggers strictly on pointer down / mouse click start)</option>
                                <option value="eager" <?php selected($opts['eagerness'], 'eager'); ?>>Eager (Triggers as soon as link enters viewport or loads)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Performance & Safety', 'instant-speculative-loading'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[disable_logged_in]" value="1" <?php checked($opts['disable_logged_in'], '1'); ?> />
                                    <?php esc_html_e('Disable speculation rules for logged-in administrators and users', 'instant-speculative-loading'); ?>
                                </label><br />
                                <label>
                                    <input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[respect_save_data]" value="1" <?php checked($opts['respect_save_data'], '1'); ?> />
                                    <?php esc_html_e('Respect Save-Data header (Automatically disables if mobile user enabled data saving)', 'instant-speculative-loading'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Exclude URL Paths', 'instant-speculative-loading'); ?></th>
                        <td>
                            <textarea name="<?php echo esc_attr(self::OPTION_KEY); ?>[custom_exclude_paths]" rows="4" cols="50" class="large-text code"><?php echo esc_textarea($opts['custom_exclude_paths']); ?></textarea>
                            <p class="description"><?php esc_html_e('One rule per line. Wildcard (*) is supported. Default admin routes are protected automatically.', 'instant-speculative-loading'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Exclude CSS Selectors', 'instant-speculative-loading'); ?></th>
                        <td>
                            <input type="text" name="<?php echo esc_attr(self::OPTION_KEY); ?>[custom_exclude_css]" value="<?php echo esc_attr($opts['custom_exclude_css']); ?>" class="large-text" />
                            <p class="description"><?php esc_html_e('Comma-separated CSS selectors that will never trigger speculative loading.', 'instant-speculative-loading'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * تزریق تگ نهایی قوانین به فوتر
     */
    public static function render_speculation_rules(): void {
        if (is_admin() || is_feed() || (function_exists('wp_is_json_request') && wp_is_json_request()) || (defined('DOING_CRON') && DOING_CRON)) {
            return;
        }

        $opts = self::get_options();

        // غیرفعال‌سازی برای کاربران لاگین‌شده
        if ($opts['disable_logged_in'] === '1' && is_user_logged_in()) {
            return;
        }

        // احترام به مود مصرف بهینه دیتا
        if ($opts['respect_save_data'] === '1' && isset($_SERVER['HTTP_SAVE_DATA']) && strtolower((string)$_SERVER['HTTP_SAVE_DATA']) === 'on') {
            return;
        }

        $exclude_paths = [
            '/wp-admin/*',
        ];

        // افزودن مسیرهای استثنای شخصی‌سازی‌شده
        if (!empty($opts['custom_exclude_paths'])) {
            $lines = explode("\n", str_replace("\r", "", $opts['custom_exclude_paths']));
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $exclude_paths[] = $line;
                }
            }
        }

        // سازگاری با ووکامرس
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

        $exclude_paths = apply_filters('isl_speculation_rules_exclude_paths', array_values(array_unique($exclude_paths)));

        $default_binary_selectors = 'a[href$=".pdf"], a[href$=".zip"], a[href$=".rar"], a[href$=".mp4"], a[href$=".mp3"], a[href$=".tar.gz"], a[href$=".exe"]';
        $css_selectors = trim($opts['custom_exclude_css']);
        $combined_selectors = !empty($css_selectors) ? $css_selectors . ', ' . $default_binary_selectors : $default_binary_selectors;

        $target_key = in_array($opts['mode'], ['prerender', 'prefetch'], true) ? $opts['mode'] : 'prerender';

        $rules = [
            $target_key => [
                [
                    'source' => 'document',
                    'where'  => [
                        'and' => [
                            [
                                'href_matches' => '/*'
                            ],
                            [
                                'not' => [
                                    'href_matches' => $exclude_paths
                                ]
                            ],
                            [
                                'not' => [
                                    'selector_matches' => $combined_selectors
                                ]
                            ]
                        ]
                    ],
                    'eagerness' => $opts['eagerness']
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
}

add_action('plugins_loaded', ['Instant_Speculative_Loading', 'init']);
