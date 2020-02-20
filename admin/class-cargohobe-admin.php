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
	 * Define remote server url where the data will be send
	 *
	 * @var string
	 */
	private $remote_url;
	/**
	 * @var true|void
	 */
	private $interval;
	/**
	 * @var int
	 */
	private $single_shipment_time;

	private $options;

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

		/**
		 * Default value For CargoHobe Settings
		 */
		$defaultOptionsValue = array(
			"remote_url"           => "https://darling-butcherbird-p5iy-7777.nt.run",
			"interval"             => 60,
			"single_shipment_time" => 60,
			"data_split"           => 10
		);

		$this->options = wp_parse_args( get_option( "cargohobe_option_name" ), $defaultOptionsValue );


		// Trigger all the hooks
		add_action( 'admin_init', array( $this, 'cargohobe_register_settings' ) );
		add_action( 'admin_menu', array( $this, 'cargohobe_register_options_page' ) );
		add_action( "wp_ajax_cargohobe_remote_send_all_data", array( $this, 'cargohobe_cache_cron_job' ) );
		add_action( 'cargohobe_send_all_data_event', array( $this, 'cargohobe_send_remote_data' ) );
		add_filter( 'cron_schedules', array( $this, 'cargohobe_add_custom_schedule' ) );
		add_action( 'save_post', array( $this, 'send_single_cargo_data_to_the_remote' ), 90, 3 );
		add_action( 'cargohobe_send_single_data', array( $this, 'cargohobe_send_single_data_to_remote' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cargohobe-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cargohobe-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register settings for cargohobe
	 */
	public function cargohobe_register_settings() {
		add_option( 'cargohobe_option_name', 'CargoHobe Options' );
		register_setting( 'cargohobe_options_group', 'cargohobe_option_name', 'cargohobe_callback' );
		add_settings_section(
			'cargohobe_main_section', // ID
			'CargoHobe', // Title
			array(), // Callback
			'cargohobe-settings-admin' // Page
		);
		add_settings_field(
			'remote_url', // ID
			'Remote URL', // Title
			array( $this, 'cargohobe_remote_url_callback' ), // Callback
			'cargohobe-settings-admin', // Page
			'cargohobe_main_section' // Section
		);
		add_settings_field(
			'interval', // ID
			'Interval Time', // Title
			array( $this, 'cargohobe_interval_callback' ), // Callback
			'cargohobe-settings-admin', // Page
			'cargohobe_main_section' // Section
		);
		add_settings_field(
			'data_split', // ID
			'Data Split', // Title
			array( $this, 'cargohobe_data_split_callback' ), // Callback
			'cargohobe-settings-admin', // Page
			'cargohobe_main_section' // Section
		);
		add_settings_field(
			'single_shipment_time', // ID
			'New Shipment Time', // Title
			array( $this, 'cargohobe_single_shipment_time_callback' ), // Callback
			'cargohobe-settings-admin', // Page
			'cargohobe_main_section' // Section
		);
	}

	/**
	 * Add page to the cargohobe settings menu
	 */
	public function cargohobe_register_options_page() {
		add_menu_page( 'CargoHobe Option', 'CargoHobe', 'manage_options', 'cargohobe', array(
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

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		$new_input = array();

		if ( isset( $input['interval'] ) ) {
			$new_input['interval'] = absint( $input['interval'] );
		}

		if ( isset( $input['data_split'] ) ) {
			$new_input['data_split'] = absint( $input['data_split'] );
		}

		if ( isset( $input['single_shipment_time'] ) ) {
			$new_input['single_shipment_time'] = absint( $input['single_shipment_time'] );
		}


		if ( isset( $input['remote_url'] ) ) {
			$new_input['remote_url'] = sanitize_text_field( $input['remote_url'] );
		}

		return $new_input;
	}

	// Show Remote HOST url field
	public function cargohobe_remote_url_callback() {
		printf(
			'<input type="text" class="regular-text" id="remote_url" name="cargohobe_option_name[remote_url]" value="%s" />
					<p class="description" id="tagline-description">Where the data will be sent.</p>',
			isset( $this->options['remote_url'] ) ? esc_attr( $this->options['remote_url'] ) : ''
		);
	}

	// Show Interval field
	public function cargohobe_interval_callback() {
		printf(
			'<input type="text" class="regular-text" id="interval" name="cargohobe_option_name[interval]" value="%s" />
					<p class="description" id="tagline-description">Interval time between per request. (In seconds)</p>',
			isset( $this->options['interval'] ) ? esc_attr( $this->options['interval'] ) : ''
		);
	}

	// Show data_split field
	public function cargohobe_data_split_callback() {
		printf(
			'<input type="text" class="regular-text" id="interval" name="cargohobe_option_name[data_split]" value="%s" />
					<p class="description" id="tagline-description">How much data will be sent per request</p>',
			isset( $this->options['data_split'] ) ? esc_attr( $this->options['data_split'] ) : ''
		);
	}

	// Show single shipment delay field
	public function cargohobe_single_shipment_time_callback() {
		printf(
			'<input type="text" class="regular-text" id="single_shipment_time" name="cargohobe_option_name[single_shipment_time]" value="%s" />
					<p class="description" id="tagline-description">Delay sending data after create a new shipment. (In Seconds)</p>',
			isset( $this->options['single_shipment_time'] ) ? esc_attr( $this->options['single_shipment_time'] ) : ''
		);
	}

	/**
	 * Add meta value to a WP_Query Query Object
	 *
	 * @param string $wp_query
	 *
	 * @return string
	 */
	public function cargohobe_add_query_meta( $wp_query = "" ) {
		//return In case if wp_query is empty or postmeta already exist
		if ( ( empty( $wp_query ) ) || ( ! empty( $wp_query ) && ! empty( $wp_query->posts ) && isset( $wp_query->posts[0]->postmeta ) ) ) {
			return $wp_query;
		}

		$sql      = $postmeta = '';
		$post_ids = array();
		$post_ids = wp_list_pluck( $wp_query->posts, 'ID' );
		if ( ! empty( $post_ids ) ) {
			global $wpdb;
			$post_ids = implode( ',', $post_ids );
			$sql      = "SELECT meta_key, meta_value, post_id FROM $wpdb->postmeta WHERE post_id IN ($post_ids)";
			$postmeta = $wpdb->get_results( $sql, OBJECT );
			if ( ! empty( $postmeta ) ) {
				foreach ( $wp_query->posts as $pKey => $pVal ) {
					$wp_query->posts[ $pKey ]->postmeta = new StdClass();
					foreach ( $postmeta as $mKey => $mVal ) {
						if ( $postmeta[ $mKey ]->post_id == $wp_query->posts[ $pKey ]->ID ) {
							$newmeta[ $mKey ]                   = new stdClass();
							$newmeta[ $mKey ]->meta_key         = $postmeta[ $mKey ]->meta_key;
							$newmeta[ $mKey ]->meta_value       = maybe_unserialize( $postmeta[ $mKey ]->meta_value );
							$wp_query->posts[ $pKey ]->postmeta = (object) array_merge( (array) $wp_query->posts[ $pKey ]->postmeta, (array) $newmeta );
							unset( $newmeta );
						}
					}
				}
			}
			unset( $post_ids );
			unset( $sql );
			unset( $postmeta );
		}

		return $wp_query;
	}

	/**
	 * After click send data by user,
	 * Start cache all the wpcargo_shipment data using transient api and
	 * Start a cron job for send that data to the remote server
	 *
	 */
	public function cargohobe_cache_cron_job() {
		$args     = array(
			'post_type' => 'wpcargo_shipment',
		);
		$wp_query = new WP_Query( $args );
		if ( $wp_query->have_posts() ) {
			// Add meta data to the wp_query objects
			$wp_query = $this->cargohobe_add_query_meta( $wp_query );

			// Check is wpcargo_shipment data save on the cache
			$cargo_data = get_transient( "cargohobe_data" );
			if ( ! $cargo_data ) {
				// If not then save it to the cache
				set_transient( "cargohobe_data", $wp_query->posts );
			}

			// Enable WPCron
			//Use wp_next_scheduled to check if the event is already scheduled
			$timestamp = wp_next_scheduled( 'cargohobe_send_all_data_event' );

			//If $timestamp == false schedule daily backups since it hasn't been done previously
			if ( $timestamp == false ) {
				//Schedule the event for right now, then to repeat daily using the hook 'cargohobe_send_all_data_event'
				wp_schedule_event( time(), 'minute', 'cargohobe_send_all_data_event' );
			}

		}
	}

	/**
	 * Send all data to the remote server
	 */
	public function cargohobe_send_remote_data() {
		// Check is all the data save on cache or not
		$cargo_data = get_transient( "cargohobe_data" );
		if ( $cargo_data ) {
			$limit        = $this->options["data_split"];
			$chunk_offset = array( "offset" => 0 );

			// Check is there any previous chunk offset available or not
			$prev_chuck_offset = get_transient( "prev_chuck_offset" );
			if ( ! $prev_chuck_offset ) {
				// If not then then add
				set_transient( "prev_chuck_offset", $chunk_offset );
			} else {
				// Otherwise delete transient offset
				// and re add offset + limit for next chunk
				delete_transient( "prev_chuck_offset" );
				$chunk_offset = array( "offset" => intval( $prev_chuck_offset["offset"] ) + $limit );
				set_transient( "prev_chuck_offset", $chunk_offset );
			}

			// Slice the data with offset and limit
			$chunk_data = array_slice( $cargo_data, $chunk_offset["offset"], $limit );

			// If chunk is complete then delete transients and cron jobs
			if ( count( $chunk_data ) === 0 ) {
				wp_clear_scheduled_hook( 'cargohobe_send_all_data_event' );
				delete_transient( "prev_chuck_offset" );
				delete_transient( "cargohobe_data" );
			} else {
				// Otherwise send that chunk data to the remote server
				$this->cargohobe_send_data( $chunk_data );
			}
		}
	}


	/**
	 * Add custom time schedule events
	 *
	 * @param $schedules
	 *
	 * @return mixed
	 */
	public function cargohobe_add_custom_schedule( $schedules ) {
		$schedules['minute'] = array(
			'interval' => $this->options["interval"], //7 days * 24 hours * 60 minutes * 60 seconds
			'display'  => __( 'Once Per Two Minute', 'cargohobe' )
		);

		return $schedules;
	}


	/**
	 * Helper method to send data to the remote server
	 *
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function cargohobe_send_data( $data ) {
		// Data argument
		$args = array(
			'method'    => 'POST',
			'sslverify' => false,
			'headers'   => array(
				'Content-Type' => 'application/json',
			),
			'body'      => wp_json_encode( $data ),
		);

		// Send data to the remote server
		$request = wp_remote_post( $this->options["remote_url"], $args );

		// If there are any errors then log it
		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			log_it( $request );

			return false;
		}

		// Otherwise return true
		return true;
	}

	function send_single_cargo_data_to_the_remote( $post_id, $post, $update ) {
		if ( $post->post_type == "wpcargo_shipment" ) {
			if ( $post->post_status && $post->post_status == 'publish' ) {
				// Refetch the post for maintain specific data structure
				$args     = array(
					'post_type' => 'wpcargo_shipment',
					'p'         => $post_id
				);
				$wp_query = new WP_Query( $args );
				if ( $wp_query->have_posts() ) {
					// Add meta data to the wp_query objects
					$wp_query = $this->cargohobe_add_query_meta( $wp_query );

					// Check is wpcargo_shipment data save on the cache
					$cargo_data = get_transient( "cargohobe_single_data" );
					if ( ! $cargo_data ) {
						// If not then save it to the cache
						set_transient( "cargohobe_single_data", $wp_query->posts );
					} else {
						// If yes then add new shipment with old shipment
						array_push( $cargo_data, $wp_query->posts );
						delete_transient( "cargohobe_single_data" );
						set_transient( "cargohobe_single_data", $cargo_data );
					}

					// Delete Previous Schedule Event
					wp_clear_scheduled_hook( 'cargohobe_send_single_data' );

					// Register Schedule Event For Single Data
					wp_schedule_single_event( time() + $this->options["single_shipment_time"], 'cargohobe_send_single_data' );
				}
			}
		}

		return;
	}

	/**
	 * Send Single Data To The Remote Server
	 */
	function cargohobe_send_single_data_to_remote() {
		// Fetch existing data
		// Check is all the data save on cache or not
		$cargo_data = get_transient( "cargohobe_single_data" );
		if ( $cargo_data ) {
			$success = $this->cargohobe_send_data( $cargo_data );
			if ( $success ) {
				// If Successfully send data to the remote server then clear event and transient
				wp_clear_scheduled_hook( 'cargohobe_send_single_data' );
				delete_transient( "cargohobe_single_data" );
			}
		}
	}
}
