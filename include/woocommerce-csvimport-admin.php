<?php
class woocsv_import_admin {

	public function __construct() {
		add_action('admin_menu', array($this,'admin_menu'));
	}

	public function admin_menu(){
		//add main menu page
		add_menu_page('CSV Import', 'CSV Import', 'manage_options', 'woocsv_import', array($this,'main_page'),'',58);
		//add settings page
		add_submenu_page( 'woocsv_import', 'Settings', 'Settings', 'manage_options', 'woocsv_settings', array($this,'settings'));
	}

	public function main_page(){
	//some bassic checks
	$upload_dir = wp_upload_dir();
	if (!is_writable($upload_dir['basedir'].'/csvimport/'))
		woocsv_admin_notice ('Import directory niet gevonden of hij is niet schrijfbaar. check of /uploads/csvimport bestaat');
		
	//handle zip uploads
	if ( isset( $_REQUEST['handle_csv_import_zip']) && check_admin_referer('handle_csv_import_zip')) 
		woocsv_handle_zip_import();
	//handle manual uploads
	if ( isset( $_REQUEST['handle_csv_import_random']) && check_admin_referer('handle_csv_import_random')) 
		wppcsv_handle_csv_import_random();
	//handle fixed uploads
	if ( isset( $_REQUEST['handle_csv_import_fixed']) && check_admin_referer('handle_csv_import_fixed')) 
		woocsv_handle_fixed_import();

	
	//main page
		echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>Import</h2></div>';
     ?>
	<script>
	jQuery(document).ready(function() {
		    jQuery( "#tabs" ).tabs();
		  });

	</script>
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php echo __('Select a zip file'); ?></a></li>
			<li><a href="#tabs-2"><?php echo __('Select youre own files'); ?></a></li>
			<li><a href="#tabs-3"><?php echo __('You already uploaded the files');?></a></li>
		</ul>
		<div id="tabs-1">
			<?php
			echo '<h3>'.__('Upload a zip file').'</h3>';
			echo '<form id="handle_csv_import_zip" name="handle_csv_import_zip" action="" method="POST" enctype="multipart/form-data">';
			echo '<input id="zip_file" name="zip_file" type="file" accept="application/zip"> <br />';
			echo '<input name="handle_csv_import_zip" type="submit" value="start">';
			echo wp_nonce_field('handle_csv_import_zip');
			echo '</form>';
			?>
		</div>
		<div id="tabs-2">
			<?php
			echo '<h3>'.__('Upload selected files from').'</h3>';
			echo '<p>'.__('We will only proccess csv and jpg files').'</p>';
			echo '<form id="handle_csv_import_random" name="handle_csv_import_random" method="POST" enctype="multipart/form-data">';
			echo __('jpg en csv:').'<input id="all_files" name="all_files[]" type="file" multiple> <br />';
			echo '<input name="handle_csv_import_random" type="submit" value="start">';
			echo wp_nonce_field('handle_csv_import_random');
			echo '</form>';
			?>
		</div>
		<div id="tabs-3">
			<?php
			echo '<h3>'.__('You already uploaded the files').'</h3>';
			echo '<p>'.__('We expect it to be the in uploads/csvimport/fixed').'</p>';
			echo '<form id="handle_csv_import_fixed" name="handle_csv_import_fixed" method="POST">';
			echo __('Override fixed directory with uploads').'<input type="text" name="fixed_dir" value="/csvimport/fixed"><br />';
			echo '<input name="handle_csv_import_fixed" type="submit" value="start">';
			echo wp_nonce_field('handle_csv_import_fixed');
			echo '</form>';
			?>
		</div>
	</div>
	<?php
	}

public function settings(){
	
	
	$upload_dir = wp_upload_dir();
	if ( isset( $_REQUEST['create_import_directory']) && check_admin_referer('create_import_directory')) {
		mkdir($upload_dir['basedir'] .'/csvimport/');
		mkdir($upload_dir['basedir'] .'/csvimport/fixed/');
	}

	if (!is_writable($upload_dir['basedir'].'/csvimport/'))
		woocsv_admin_notice ('Import directory niet gevonden of hij is niet schrijfbaar. check of /uploads/csvimport bestaat');
		
		
		echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>Settings</h2></div>';
        if (!is_dir($upload_dir['basedir'] .'/csvimport/')) {
        	echo '<h3>Create import directory</h3>';
			echo '<form id="create_import_directory" name="create_import_directory" method="POST">';
			echo '<input name="create_import_directory" type="submit" value="create">';
			echo wp_nonce_field('create_import_directory');
			echo '</form>';
	        
        }
    echo '<p>No settings available</p>';
    }
    



}

$csv_import_admin = new woocsv_import_admin();