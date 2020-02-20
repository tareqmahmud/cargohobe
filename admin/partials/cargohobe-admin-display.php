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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap cargohobe-options">
    <h2>CargoHobe Options</h2>
	<?php settings_errors(); ?>
    <button id="send-data" class="button button-primary cargohobe-send-button">Send Data</button>
</div>