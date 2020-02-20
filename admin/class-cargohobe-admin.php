<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tareqmahmud.com
 * @since      1.0.0
 *
 * @package    Cargohobe
 * @subpackage Cargohobe/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cargohobe
 * @subpackage Cargohobe/admin
 * @author     MD. Tareq Mahmud <me@tareqmahmud.com>
 */
class Cargohobe_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;


		add_action( 'admin_init', array( $this, 'cargohobe_register_settings' ) );
		add_action( 'admin_menu', array( $this, 'cargohobe_register_options_page' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cargohobe_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cargohobe_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cargohobe-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cargohobe_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cargohobe_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cargohobe-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register settings for cargohobe
	 */
	public function cargohobe_register_settings() {
		add_option( 'cargohobe_option_name', 'CargoHobe Options' );
		register_setting( 'cargohobe_options_group', 'cargohobe_option_name', 'cargohobe_callback' );
	}

	/**
	 * Add page to the cargohobe settings menu
	 */
	public function cargohobe_register_options_page() {
		add_menu_page( 'CargoHobe Options', 'CargoHobe', 'manage_options', 'cargohobe', array(
			$this,
			'cargohobe_options_page'
		) );
	}

	/**
	 * Cargohobe settings html
	 */
	public function cargohobe_options_page() {
		require_once 'partials/cargohobe-admin-display.php';
	}

}
