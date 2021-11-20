<?php
/**
 * Plugin Name: WD Posts Translator 
 * Plugin URI: https://github.com/Mironezes
 * Description: Based on Google Translator library for automatic post content translation on different languages.
 * Version: 0.1.0
 * Author: Alexey Suprun
 * Author URI: https://github.com/Mironezes
 * Requires at least: 5.5
 * Requires PHP: 7.0
 * Tested up to: 5.8
 * License: GPL-2.0+
 * Text Domain: wd-posts-translator
 * Domain Path: /languages
 * 
 * @link https://github.com/Mironezes
 * @package wd_posts_translator
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Includes Google Translate Lib
 */ 
require_once('libs/GoogleTranslate.php');

/**
 * Currently plugin version.
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WD_POSTS_TRANSLATOR_VERSION', '0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wd-posts-translator-activator.php
 */
function activate_wd_posts_translator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wd-posts-translator-activator.php';
	Wd_Posts_Translator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wd-posts-translator-deactivator.php
 */
function deactivate_wd_posts_translator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wd-posts-translator-deactivator.php';
	Wd_Posts_Translator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wd_posts_translator' );
register_deactivation_hook( __FILE__, 'deactivate_wd_posts_translator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wd-posts-translator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 *
 */
function run_wd_posts_translator() {

	$plugin = new Wd_Posts_Translator();
	$plugin->run();

}
run_wd_posts_translator();
