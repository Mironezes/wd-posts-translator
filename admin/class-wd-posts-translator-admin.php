<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/Mironezes
 *
 * @package    wd_posts_translator
 * @subpackage wd_posts_translator/admin
 */

 

// Includes Admin Panel UI Template
require('templates/wd-posts-translator-admin-display.php');

// Includes Admin Panel Options List
require_once('options/wd-posts-translator-admin-options.php');

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wd_posts_translator
 * @subpackage wd_posts_translator/admin
 * @author     Alexey Suprun <mironezes@gmail.com>
 */
class Wd_Posts_Translator_Admin {

	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	// Admin Functionality Init
	public function wdpt_init() {

    add_action( 'admin_menu', array($this, 'wdpt_admin_menu'));

		if ( is_admin_bar_showing() ) {
			add_action( 'admin_bar_menu', array($this, 'wdpt_add_adminbar_link'), 100);
		}

    $options = new Wd_Posts_Translator_Admin_Options();
		$options->wdpt_options();

    add_filter( 'cron_schedules', 'cron_add_six_hours_one_min' );
    function cron_add_six_hours_one_min( $schedules ) {
      $schedules['6h1m'] = array(
        'interval' => (3600 * 6) + 60,
        'display' => 'Раз в 6 часов и 1 минуту'
      );
      return $schedules;
    }

    if(!wp_next_scheduled('translate_posts_event')) {
      wp_schedule_event(time(), '6h1m', 'translate_posts_event');
    }
	}


	// Add Admin Menu
	public function wdpt_admin_menu() {
		add_options_page('WD Posts Translator Settings', 'WD Posts Translator', 'manage_options', 'wdpt-posts-translator', 'wdpt_settings_template', null, 100 );
	}

	// Add Adminbar Quick Link
	public function wdpt_add_adminbar_link($admin_bar) {
		$admin_bar->add_menu([
			'id'    => 'wdpt',
			'title' => 'WD Posts Translator',
			'href'  =>  admin_url('options-general.php?page=wdpt-posts-translator'),
			'meta'  => array(
					'title' => 'WD Posts Translator Settings'
			),
		]);
	}


	// Register the stylesheets for the admin area
	public function wdpt_admin_enqueue_styles() {
    wp_enqueue_style('font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome/css/all.min.css', array(), $this->version, 'all');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wd-posts-translator-admin.css', array(), $this->version, 'all' );
	}


	//Register the JavaScript for the admin area
	public function wdpt_admin_enqueue_scripts() {

    wp_enqueue_script( 'translate-single-post', plugin_dir_url( __FILE__ ) . 'js/translate-single-post.js', array(), wp_rand() );
    $wdpt_localize_script = [
      'url' => admin_url( 'admin-ajax.php' ),
      'translate_nonce' => wp_create_nonce( 'post-single-translate' )
    ];
    wp_localize_script('translate-single-post', 'wdpt_single_translation', $wdpt_localize_script);


		if( get_current_screen()->id == 'settings_page_wdpt-posts-translator' ) {
			wp_enqueue_media();
		
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wdpt-admin.js', array(), $this->version, true );
			
			$wdpt_settings_localize_script = [
				'wp_rand' => wp_rand(),
				'url' => admin_url( 'admin-ajax.php' ),
        'title_dictionary_nonce' => wp_create_nonce( 'title-dictionary-nonce' ),
				'update_nonce' => wp_create_nonce( 'posts-update-nonce' ),
        'translate_nonce' => wp_create_nonce( 'posts-translate-nonce' )
			];
			wp_localize_script($this->plugin_name, 'wdpt_localize', $wdpt_settings_localize_script);
		}
	}
}

