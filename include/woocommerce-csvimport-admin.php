<?php
class woocsvImportAdmin extends woocsvImport
{
	public $options = array();

	public function __construct()
	{
		global $woocsvHandleImport;
		add_action('admin_menu', array($this, 'adminMenu'));
		ini_set('auto_detect_line_endings', true);
		add_action('wp_ajax_runImport', array($woocsvHandleImport, 'runImport'));
		$this->options = get_option('woocsv-options');
	}

	public function adminMenu()
	{
		$page=add_menu_page('CSV Import', 'CSV Import', 'manage_options', 'woocsv_import', array($this, 'mainPage'), '', 58);
		add_action('admin_print_scripts-' .$page, array(&$this, 'initJsCss'));
		$this->handleRequest();
	}

	public function initJsCss()
	{
		wp_enqueue_script('jquery');
		wp_register_script( 'woocsv-script', plugins_url( '/woocommerce-csvimport/js/woocsv.js' ) );
		wp_enqueue_script( 'woocsv-script' );
	}

	public function handleRequest()
	{
		add_action('woocsv_admin_menu' , array(&$this, 'mainPageContent'));
	}

	public function mainPage()
	{
		echo '<div class="wrap">';
		echo '<div id="woocsv_warning" style="display:none" class="updated"></div>';
		$this->mainPageContent();
		echo '</div>';
	}

	public function mainPageContent()
	{
	global $woocsvHandleImport;
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
			<p>If you want to import custom fields, attributes, variable products, grouped products and all other types of products and fields check out <a href="http://allaerd.org">allaerd.org</a></p><p>There are add-ons for every type!</p>
		</div>
		<div style="width:70%;float:left;">
		<?php
		switch ($tab) {
		case 'main':	
			$woocsvHandleImport->import();
		break;
		case 'header':
			$header = new woocsvImportAdminHeader();
			break;
		case 'settings':
			$settings = new woocsvImportAdminSettings();
			break;
		case 'info':
			$this->info();
			break;
		default:
			$this->import();
		}

?>
		</div>
		<?php
	}

	public function addons()
	{
		do_action('woocsv_add_addons_to_menu');
	}

	
	public function info()
	{
?>		<h2>How to use this plugin?</h2>
		<ul>
			<li>Step 1. Goto the settings page and set the appropriate settings</li>
			<li>Step 2. Goto the header page and import a CSV file</li>
			<li>Step 3. Link the right fields to the right columns and press save</li>
			<li>Step 4. Goto the import section and upload the same CSV file</li>
			<li>Step 5. Check out the import preview and check if it OK!</li>
			<li>Step 6. Press go and wait until the import is finished!</li>
		</ul>
			
		<h2>Support the free plugin</h2>
		Want to support the free version. Please consider a donation :-)
<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick" />
<input type="hidden" name="hosted_button_id" value="PGEBD4BHNH6W4" />
<input type="image" alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/NL/i/btn/btn_donateCC_LG.gif" />
<img alt="" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1" border="0" /></form>

		<?php
	}


}
