<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://tareqmahmud.com
 * @since      1.0.0
 *
 * @package    Cargohobe
 * @subpackage Cargohobe/admin/partials
 */
?>
<div class="wrap">
    <h1>CargoHobe Options</h1>
    <form method="post" action="options.php">
		<?php
		// This prints out all hidden setting fields
		settings_fields( 'cargohobe_options_group' );
		do_settings_sections( 'cargohobe-settings-admin' );
		submit_button();
		?>
    </form>
    <div class="wrap cargohobe-options">
        <h2>Send data to Server</h2>
		<?php settings_errors(); ?>
        <button id="send-data" class="button button-secondary cargohobe-send-button">Send Data</button>
    </div>
</div>