<?php
if (!class_exists('DIS_Disable_Image_Sizes')) {
    class DIS_Disable_Image_Sizes {
        public function __construct() {
            add_action('admin_menu', array($this, 'create_settings_page'));
            add_action('admin_init', array($this, 'register_settings'));
            add_action('init', array($this, 'unregister_disabled_image_sizes')); // Unregister sizes on init
            load_plugin_textdomain('disable-image-sizes', false, dirname(plugin_basename(__FILE__)) . '/languages');
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

        // Unregister the disabled image sizes and set them to 0x0 pixels
        public function unregister_disabled_image_sizes() {
            $options = get_option('dis_disable_image_sizes_options', array());
            foreach ($options as $size => $enabled) {
                if ($enabled) {
                    remove_image_size($size); // Unregister the image size

                    // Set the size dimensions to zero
                    update_option("{$size}_size_w", 0);
                    update_option("{$size}_size_h", 0);
                }
            }

            if (isset($options['disable_big_image_size_threshold']) && $options['disable_big_image_size_threshold'] == 1) {
                add_filter('big_image_size_threshold', '__return_false');
            }

            if (isset($options['disable_image_srcset']) && $options['disable_image_srcset'] == 1) {
                add_filter('wp_calculate_image_srcset', '__return_false');
            }
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
    }
}
