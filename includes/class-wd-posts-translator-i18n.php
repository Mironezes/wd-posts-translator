<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/Mironezes
 *
 * @package    wd_posts_translator
 * @subpackage wd_posts_translator/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package    wd_posts_translator
 * @subpackage wd_posts_translator/includes
 * @author     Alexey Suprun <mironezes@gmail.com>
 */
class Wd_Posts_Translator_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 *
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wd-posts-translator',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
