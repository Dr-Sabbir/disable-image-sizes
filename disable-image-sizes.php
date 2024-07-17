<?php
/*
Plugin Name: Disable Image Sizes
Description: Disable specific image sizes in WordPress.
Version: 1.9
Author: Dr. Sabbir H
Author URI: http://sabbirh.com/
Text Domain: disable-image-sizes
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('DIS_Disable_Image_Sizes')) {
    class DIS_Disable_Image_Sizes {
        public function __construct() {
            register_activation_hook(__FILE__, array($this, 'on_activation'));
            add_action('admin_menu', array($this, 'create_settings_page'));
            add_action('admin_init', array($this, 'register_settings'));
            add_action('admin_init', array($this, 'apply_filters'));
            add_action('admin_init', array($this, 'redirect_to_settings_page'));
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
            load_plugin_textdomain('disable-image-sizes', false, dirname(plugin_basename(__FILE__)) . '/languages');
        }

        public function on_activation() {
            set_transient('dis_plugin_redirect', true, 30);
        }

        public function redirect_to_settings_page() {
            if (get_transient('dis_plugin_redirect')) {
                delete_transient('dis_plugin_redirect');
                if (is_admin() && !isset($_GET['activate-multi'])) {
                    wp_safe_redirect(admin_url('admin.php?page=disable-image-sizes'));
                    exit;
                }
            }
        }

        public function apply_filters() {
            $options = get_option('dis_disable_image_sizes_options', array());
            if (!empty($options)) {
                add_filter('intermediate_image_sizes_advanced', array($this, 'disable_selected_image_sizes'));
                add_filter('big_image_size_threshold', array($this, 'disable_big_image_size_threshold'));
                add_filter('wp_calculate_image_srcset', array($this, 'disable_image_srcset'));
            }
        }

        public function create_settings_page() {
            add_menu_page(
                __('Disable Image Sizes', 'disable-image-sizes'),
                __('Disable Image Sizes', 'disable-image-sizes'),
                'manage_options',
                'disable-image-sizes',
                array($this, 'settings_page_content'),
                'dashicons-format-image'
            );
        }

        public function register_settings() {
            register_setting('disable_image_sizes', 'dis_disable_image_sizes_options', array($this, 'validate_options'));
        }

        public function settings_page_content() {
            if (!current_user_can('manage_options')) {
                return;
            }
            ?>
            <div class="wrap">
                <h1><?php _e('Disable Image Sizes', 'disable-image-sizes'); ?></h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('disable_image_sizes');
                    $options = get_option('dis_disable_image_sizes_options', array());
                    $sizes = $this->get_all_image_sizes();
                    ?>
                    <table class="form-table">
                        <tbody>
                            <?php foreach ($sizes as $size => $details): ?>
                                <tr>
                                    <th scope="row"><?php echo esc_html($size); ?></th>
                                    <td>
                                        <input type="checkbox" name="dis_disable_image_sizes_options[<?php echo esc_attr($size); ?>]" value="1" <?php checked(1, isset($options[$size]) ? $options[$size] : 0); ?>>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th scope="row"><?php _e('Disable Big Image Size Threshold', 'disable-image-sizes'); ?></th>
                                <td>
                                    <input type="checkbox" name="dis_disable_image_sizes_options[disable_big_image_size_threshold]" value="1" <?php checked(1, isset($options['disable_big_image_size_threshold']) ? $options['disable_big_image_size_threshold'] : 0); ?>>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Disable Image Srcset', 'disable-image-sizes'); ?></th>
                                <td>
                                    <input type="checkbox" name="dis_disable_image_sizes_options[disable_image_srcset]" value="1" <?php checked(1, isset($options['disable_image_srcset']) ? $options['disable_image_srcset'] : 0); ?>>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }

        public function validate_options($input) {
            $valid = array();
            $sizes = $this->get_all_image_sizes();

            foreach ($sizes as $size => $details) {
                if (isset($input[$size]) && $input[$size] == 1) {
                    $valid[$size] = 1;
                }
            }

            if (isset($input['disable_big_image_size_threshold']) && $input['disable_big_image_size_threshold'] == 1) {
                $valid['disable_big_image_size_threshold'] = 1;
            }

            if (isset($input['disable_image_srcset']) && $input['disable_image_srcset'] == 1) {
                $valid['disable_image_srcset'] = 1;
            }

            return $valid;
        }

        public function get_all_image_sizes() {
            global $_wp_additional_image_sizes;
            $default_sizes = array('thumbnail', 'medium', 'large', 'medium_large', '1536x1536', '2048x2048');
            $sizes = array();

            foreach ($default_sizes as $size) {
                $sizes[$size] = array(
                    'width' => get_option("{$size}_size_w"),
                    'height' => get_option("{$size}_size_h"),
                    'crop' => (bool) get_option("{$size}_crop")
                );
            }

            if (isset($_wp_additional_image_sizes) && count($_wp_additional_image_sizes)) {
                $sizes = array_merge($sizes, $_wp_additional_image_sizes);
            }

            return $sizes;
        }

        public function disable_selected_image_sizes($sizes) {
            $options = get_option('dis_disable_image_sizes_options', array());
            foreach ($sizes as $key => $size) {
                if (isset($options[$key]) && $options[$key] == 1) {
                    unset($sizes[$key]);
                }
            }
            return $sizes;
        }

        public function disable_big_image_size_threshold($threshold) {
            $options = get_option('dis_disable_image_sizes_options');
            if (isset($options['disable_big_image_size_threshold']) && $options['disable_big_image_size_threshold'] == 1) {
                return false;
            }
            return $threshold;
        }

        public function disable_image_srcset($sources) {
            $options = get_option('dis_disable_image_sizes_options');
            if (isset($options['disable_image_srcset']) && $options['disable_image_srcset'] == 1) {
                return false;
            }
            return $sources;
        }

        public function add_settings_link($links) {
            $settings_link = '<a href="admin.php?page=disable-image-sizes">' . __('Settings', 'disable-image-sizes') . '</a>';
            array_unshift($links, $settings_link);
            return $links;
        }
    }

    new DIS_Disable_Image_Sizes();
}
