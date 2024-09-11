<?php
/*
Plugin Name: Disable Image Sizes
Description: Disable specific image sizes in WordPress and regenerate thumbnails.
Version: 3.0.0
Author: Dr. Sabbir H
Author URI: http://sabbirh.com/
Text Domain: disable-image-sizes
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-disable-image-sizes.php';