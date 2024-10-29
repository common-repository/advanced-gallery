<?php
/**
 * Plugin Name:     	Pixieshot Gallery - Widgets for Elementor
 * Plugin URI:      	https://kraftplugins.com/advanced-gallery/
 * Description:     	Pixieshot Gallery is a powerful gallery addon widget of Elementor plugin built for creating beautiful and mobile-responsive galleries in minutes.
 * Author:          	kraftplugins
 * Author URI:      	https://kraftplugins.com/
 * License:         	GPL v3 or later
 * License URI:     	https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:     	advanced-gallery
 * Domain Path:     	/languages
 * Requires at least: 	5.1
 * Tested up to: 		6.6
 * Requires PHP:		7.4
 * Version:         	1.0.4
 *
 * @package         Advanced_Gallery
 */

use Advanced_Gallery\Init;

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ADVANCED_GALLERY_PLUGIN_FILE' ) ) {
	define( 'ADVANCED_GALLERY_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'ADVANCED_GALLERY_PLUGIN_PATH' ) ) {
	define( 'ADVANCED_GALLERY_PLUGIN_PATH', plugin_dir_path( ADVANCED_GALLERY_PLUGIN_FILE ) );
}
if ( ! defined( 'ADVANCED_GALLERY_PLUGIN_VERSION' ) ) {
	define( 'ADVANCED_GALLERY_PLUGIN_VERSION', '1.0.4' );
}
if ( ! defined( 'ADVANCED_GALLERY_MINIMUM_PHP_VERSION' ) ) {
	define('ADVANCED_GALLERY_MINIMUM_PHP_VERSION', '5.6');
}
if ( ! defined( 'ADVANCED_GALLERY_ELEMENTOR_MINIMUM_ELEMENTOR_VERSION' ) ) {
	define('ADVANCED_GALLERY_ELEMENTOR_MINIMUM_ELEMENTOR_VERSION', '2.5.0');
}

// Include the autoloader.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Return the main instance of Advanced_Gal_Init.
 *
 * @since 1.0.0
 * @return Advanced_Gal_Init
 */
function Advanced_Gal_Init() {
	require ADVANCED_GALLERY_PLUGIN_PATH . 'src/Advanced_Gallery.php';
	return Init::instance();
}

// Changed load order.
add_action('plugins_loaded', 'Advanced_Gal_Init');
