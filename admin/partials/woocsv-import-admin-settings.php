<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2>Settings</h2>
	<hr>
	<form action="options.php" method="post">
		<?php settings_fields('woocsv-settings'); ?>
		<?php do_settings_sections('woocsv-settings'); ?>
		<?php submit_button(__('save','woocsv')); ?>
	</form>
</div>
