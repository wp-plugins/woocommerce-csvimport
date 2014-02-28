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
		$page = add_menu_page('CSV Import', 'CSV Import', 'manage_options', 'woocsv_import', 'woocsvAdmin::mainPage', '', '58.1501');
		add_action( 'admin_print_scripts-' .$page, 'woocsvAdmin::initJs');
		add_action( 'admin_print_styles-' . $page, 'woocsvAdmin::initCss' );
	}

	static function initJs()
	{
		wp_enqueue_script('jquery');
		wp_register_script( 'woocsv-script', plugins_url( '/woocommerce-csvimport/js/woocsv.js' ) );
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
				class="nav-tab <?php echo ($tab==='main')?'nav-tab-active':''; ?>">Import</a>
			<a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=header');?>"
				class="nav-tab <?php echo ($tab==='header')?'nav-tab-active':''; ?>">Header</a>
			<a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=settings');?>"
				class="nav-tab <?php echo ($tab==='settings')?'nav-tab-active':''; ?>">Settings</a>
			<a href="<?php echo admin_url('admin.php?page=woocsv_import&amp;tab=info');?>"
				class="nav-tab <?php echo ($tab==='info')?'nav-tab-active':''; ?>">Info</a>
		</h2>
		<div id="woocsvSidebar" class="welcome-panel" style="width:20%;float:right;">
			<h2>Add-ons</h2>
			<p>If you want to import custom fields, attributes, variable products, grouped products and all other types of products and fields check out 			<a href="http://allaerd.org/shop">Allaerd.org</a></p>
			<p>Also check out the tutorials on my site!</p>
		</div>
		<div style="width:70%;float:left;">
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
		default:
			woocsvAdminImport::start();
		}

?>
		</div>
		<?php
	}
}
