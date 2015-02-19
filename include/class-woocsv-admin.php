<?php
class woocsvAdmin
{

	public static function start()
	{
		add_action('admin_menu', 'woocsvAdmin::adminMenu');
		add_action('wp_ajax_runImport', 'woocsvAdminImport::runImport');
	}

	static function adminMenu()
	{
		global $woocsvImport;
		
		//======================================
		//role support 
		//======================================
		$user = wp_get_current_user();
		$current_role = $user->roles;
		$allowed_roles = $woocsvImport->options['roles'];
		
		
		if (is_admin() &&  ( count( array_intersect($current_role, $allowed_roles)) > 0 || current_user_can( 'manage_options' ) ) ) {
			$page = add_menu_page('CSV Import', 'CSV Import', current($current_role), 'woocsv_import', 'woocsvAdmin::mainPage', 'dashicons-randomize', '58.1501');			
		}

		add_action( 'admin_print_scripts-' .$page, 'woocsvAdmin::initJs');
		add_action( 'admin_print_styles-' . $page, 'woocsvAdmin::initCss' );
	}

	static function initJs()
	{
	
		//======================================
		// register the scripts
		//======================================
		wp_enqueue_script('jquery');
		wp_register_script( 'woocsv-script', plugins_url( '/woocommerce-csvimport/js/woocsv.js' ) );

		//======================================
		//! localize javascript
		//======================================
		$strings = array (
			'error' => __( 'Something went wrong. We could not make a connection with the server. Check your permissions and rights the do ajax requests!' ),
			'done' 	=> __( 'Done' ),
			'start' => __( 'Starting'),
		);
		
		wp_localize_script( 'woocsv-script', 'strings', $strings );
	
		//======================================
		// enqueue the javascript
		//======================================
		wp_enqueue_script( 'woocsv-script' );
	}

	static function initCss()
	{
		wp_register_style( 'woocsv-css', plugins_url('/woocommerce-csvimport/css/woocsv.css') );
		wp_enqueue_style( 'woocsv-css' );
	}

	static function mainPage()
	{
		echo '<div class="wrap">';
		echo '<div id="woocsv_warning" style="display:none" class="updated"></div>';
		self::mainPageContent();
		echo '</div>';
	}

	static function mainPageContent()
	{
		$tab = (isset($_REQUEST['tab']))?$_REQUEST['tab']:'main';
?>
		<div id="icon-themes" class="icon32"><br></div>
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo admin_url('admin.php?page=woocsv_import');?>"
				class="nav-tab <?php echo ($tab==='main')?'nav-tab-active':''; ?>"><?php echo __('Import','woocsv-import'); ?></a>
			<a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=header');?>"
				class="nav-tab <?php echo ($tab==='header')?'nav-tab-active':''; ?>"><?php echo __('Header','woocsv-import'); ?></a>
			<a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=settings');?>"
				class="nav-tab <?php echo ($tab==='settings')?'nav-tab-active':''; ?>"><?php echo __('Settings','woocsv-import'); ?></a>
			<a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=info');?>"
				class="nav-tab <?php echo ($tab==='info')?'nav-tab-active':''; ?>"><?php echo __('Documentation','woocsv-import'); ?></a>
			
			<a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=addons');?>"
				class="nav-tab <?php echo ($tab==='addons')?'nav-tab-active':''; ?>"><?php echo __('Add-ons','woocsv-import'); ?></a>
		</h2>
		<div>
		<?php
		switch ($tab) {
		case 'main':
			woocsvAdminImport::start();
			break;
		case 'header':
			woocsvAdminHeader::start();
			break;
		case 'settings':
			woocsvAdminSettings::start();
			break;
		case 'info':
			woocsvAdminInfo::info();
			break;
		case 'addons':
			woocsvAdminInfo::addons();
			break;
		default:
			woocsvAdminImport::start();
		}

?>
		</div>
		<?php
	}
}
