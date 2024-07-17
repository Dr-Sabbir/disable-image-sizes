=== Disable Image Sizes ===
Contributors: drsabbirh
Donate link: http://sabbirh.com/
Tags: image sizes, disable image sizes, WordPress image sizes, image management
Requires at least: 5.0
Tested up to: 6.2
Requires PHP: 7.0
Stable tag: 1.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Disable specific image sizes in WordPress.

== Description ==

Disable Image Sizes is a simple plugin that allows you to disable specific image sizes in WordPress. With this plugin, you can easily manage which image sizes are generated when you upload an image.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/disable-image-sizes` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->Disable Image Sizes screen to configure the plugin.

== Frequently Asked Questions ==

= How do I disable an image size? =

Go to Settings->Disable Image Sizes, and check the boxes for the image sizes you want to disable. Save your changes, and the selected image sizes will no longer be generated for new uploads.

= Can I re-enable an image size? =

Yes, simply uncheck the box for the image size you want to re-enable and save your changes.

= How do I disable the big image size threshold? =

Go to Settings->Disable Image Sizes, and check the box labeled "Disable Big Image Size Threshold." Save your changes, and the big image size threshold will be disabled.

= How do I disable the image srcset? =

Go to Settings->Disable Image Sizes, and check the box labeled "Disable Image Srcset." Save your changes, and the image srcset will be disabled.

== Changelog ==

= 1.9 =
* Added handling for additional image sizes.
* Finalized for WordPress.org submission.

= 1.8 =
* Added debug logging to trace issues.
* Improved settings validation.

= 1.7 =
* Improved filter application timing.

= 1.6 =
* Added option to disable image srcset.
* Added option to disable big image size threshold.

= 1.5 =
* Improved handling of disabling image sizes.

= 1.4 =
* Added redirect to settings page upon activation.

= 1.3 =
* Added settings link on the plugin page.
* Created a top-level menu for the settings page.

= 1.2 =
* Added text domain for translations.
* Added security checks.

= 1.1 =
* Initial release.

== Upgrade Notice ==

= 1.9 =
* Ensure handling for additional image sizes.

== Screenshots ==

1. Settings page with image sizes list.
2. Example of disabled image sizes in action.

== License ==

This plugin is licensed under the GPLv2 or later.
