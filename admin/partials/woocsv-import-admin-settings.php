<?php

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://allaerd.org
 * @since      1.0.0
 *
 * @package    woocsv_extensions
 * @subpackage woocsv_extensions/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2>Settings</h2>
	<hr>
	<form action="options.php" method="post">
		<?php settings_fields('woocsv-settings'); ?>
		<?php do_settings_sections('woocsv-settings'); ?>
		<?php submit_button('save'); ?>
	</form>
</div>
