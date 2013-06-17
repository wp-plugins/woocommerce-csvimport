<?php
/*
Plugin Name: Woocommerce CSV Import
#Plugin URI: http://allaerd.org/woocommerce-csv-importer/
Description: Import CSV files in Woocommerce
Version: 1.0.4
Author: Allaerd Mensonides
License: GPLv2 or later
Author URI: http://allaerd.org
parent: woocommerce
*/

//include the fuctions
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-admin.php';
include dirname( __FILE__ ) . '/include/woocommerce-csvimport-product.php';

class woocsvImport
{
	public $options;
	
	public $message;

	public $options_default = array (
		'seperator'=>',',
		'skipfirstline'=>1,
		'upload_dir' => '/csvimport/',
		'blocksize' => 10,
		'language' => 'EN',
		);

	public $fields = array (
		0 =>'sku',
		2 =>'post_status',
		3 =>'post_title',
		4 =>'post_content',
		5 =>'post_excerpt',
		6 =>'category',
		7 =>'tags',
		8 =>'stock',
		9 =>'price',
		10 =>'regular_price',
		11 =>'sales_price',
		12 =>'weight' ,
		13 =>'length',
		14 =>'width' ,
		15 =>'height' ,
		16 =>'images',
		17 =>'tax_status',
		18 =>'tax_class' ,
		19 => 'stock_status',
		20 => 'visibility',
		21 => 'backorders',
		22 => 'featured',
		23 => 'manage_stock',
	);
	
	public $language = array (
		"AA"=> "Afar"
		,"AB"=> "Abkhazian"
		,"AF"=> "Afrikaans"
		,"AM"=> "Amharic"
		,"AR"=> "Arabic"
		,"AS"=> "Assamese"
		,"AY"=> "Aymara"
		,"AZ"=> "Azerbaijani"
		,"BA"=> "Bashkir"
		,"BE"=> "Byelorussian"
		,"BG"=> "Bulgarian"
		,"BH"=> "Bihari"
		,"BI"=> "Bislama"
		,"BN"=> "Bengali"
		,"BO"=> "Tibetan"
		,"BR"=> "Breton"
		,"CA"=> "Catalan"
		,"CO"=> "Corsican"
		,"CS"=> "Czech"
		,"CY"=> "Welsh"
		,"DA"=> "Danish"
		,"DE"=> "German"
		,"DZ"=> "Bhutani"
		,"EL"=> "Greek"
		,"EN"=> "English"
		,"EO"=> "Esperanto"
		,"ES"=> "Spanish"
		,"ET"=> "Estonian"
		,"EU"=> "Basque"
		,"FA"=> "Persian"
		,"FI"=> "Finnish"
		,"FJ"=> "Fiji"
		,"FO"=> "Faeroese"
		,"FR"=> "French"
		,"FY"=> "Frisian"
		,"GA"=> "Irish"
		,"GD"=> "Gaelic"
		,"GL"=> "Galician"
		,"GN"=> "Guarani"
		,"GU"=> "Gujarati"
		,"HA"=> "Hausa"
		,"HI"=> "Hindi"
		,"HR"=> "Croatian"
		,"HU"=> "Hungarian"
		,"HY"=> "Armenian"
		,"IA"=> "Interlingua"
		,"IE"=> "Interlingue"
		,"IK"=> "Inupiak"
		,"IN"=> "Indonesian"
		,"IS"=> "Icelandic"
		,"IT"=> "Italian"
		,"IW"=> "Hebrew"
		,"JA"=> "Japanese"
		,"JI"=> "Yiddish"
		,"JW"=> "Javanese"
		,"KA"=> "Georgian"
		,"KK"=> "Kazakh"
		,"KL"=> "Greenlandic"
		,"KM"=> "Cambodian"
		,"KN"=> "Kannada"
		,"KO"=> "Korean"
		,"KS"=> "Kashmiri"
		,"KU"=> "Kurdish"
		,"KY"=> "Kirghiz"
		,"LA"=> "Latin"
		,"LN"=> "Lingala"
		,"LO"=> "Laothian"
		,"LT"=> "Lithuanian"
		,"LV"=> "Latvian"
		,"MG"=> "Malagasy"
		,"MI"=> "Maori"
		,"MK"=> "Macedonian"
		,"ML"=> "Malayalam"
		,"MN"=> "Mongolian"
		,"MO"=> "Moldavian"
		,"MR"=> "Marathi"
		,"MS"=> "Malay"
		,"MT"=> "Maltese"
		,"MY"=> "Burmese"
		,"NA"=> "Nauru"
		,"NE"=> "Nepali"
		,"NL"=> "Dutch"
		,"NO"=> "Norwegian"
		,"OC"=> "Occitan"
		,"OM"=> "Oromo"
		,"OR"=> "Oriya"
		,"PA"=> "Punjabi"
		,"PL"=> "Polish"
		,"PS"=> "Pashto"
		,"PT"=> "Portuguese"
		,"QU"=> "Quechua"
		,"RM"=> "Rhaeto-Romance"
		,"RN"=> "Kirundi"
		,"RO"=> "Romanian"
		,"RU"=> "Russian"
		,"RW"=> "Kinyarwanda"
		,"SA"=> "Sanskrit"
		,"SD"=> "Sindhi"
		,"SG"=> "Sangro"
		,"SH"=> "Serbo-Croatian"
		,"SI"=> "Singhalese"
		,"SK"=> "Slovak"
		,"SL"=> "Slovenian"
		,"SM"=> "Samoan"
		,"SN"=> "Shona"
		,"SO"=> "Somali"
		,"SQ"=> "Albanian"
		,"SR"=> "Serbian"
		,"SS"=> "Siswati"
		,"ST"=> "Sesotho"
		,"SU"=> "Sudanese"
		,"SV"=> "Swedish"
		,"SW"=> "Swahili"
		,"TA"=> "Tamil"
		,"TE"=> "Tegulu"
		,"TG"=> "Tajik"
		,"TH"=> "Thai"
		,"TI"=> "Tigrinya"
		,"TK"=> "Turkmen"
		,"TL"=> "Tagalog"
		,"TN"=> "Setswana"
		,"TO"=> "Tonga"
		,"TR"=> "Turkish"
		,"TS"=> "Tsonga"
		,"TT"=> "Tatar"
		,"TW"=> "Twi"
		,"UK"=> "Ukrainian"
		,"UR"=> "Urdu"
		,"UZ"=> "Uzbek"
		,"VI"=> "Vietnamese"
		,"VO"=> "Volapuk"
		,"WO"=> "Wolof"
		,"XH"=> "Xhosa"
		,"YO"=> "Yoruba"
		,"ZH"=> "Chinese"
		,"ZU"=> "Zulu"

	); 

	public function __construct()
	{
		$this->init();
	}
	
	public function init() {
	register_activation_hook( __FILE__, array($this,'install' ));
	if (!get_option('woocsv-options')) {
			update_option('woocsv-options',$this->options_default);	
		} 
		$this->options = get_option('woocsv-options');
		
		do_action ('woocsv_main_init');
		$this->checkInstall();
	}
	
	public function install() {
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'] .'/csvimport/';
		@mkdir ($dir);
	}
	
	public function checkInstall() {
		$message = '';
		
		if (!get_option('woocsv-header')) 
			$message = '<p>You have not created a header yet, please create one before importing!</p>';
		
		if (!get_option('woocsv-options')) 
			$message = '<p>Please save your settings!</p>';


		$upload_dir = wp_upload_dir();
		if (!is_writable($upload_dir['basedir'] .'/csvimport/'))
			$message .= '<p>Upload directory is not writable, please check you permissions</p>';
	
		$this->message = $message;
		if ($message) 
			add_action( 'admin_notices', array($this,'showWarning'));
		
	}
	
	public function showWarning() {
	global $current_screen;
	 if ($current_screen->parent_base == 'woocsv_import' )
		echo '<div class="error"><p>'.$this->message.'</p></div>';
	}


}

$woocsvImport = new woocsvImport();
$woocsvImportAdmin = new woocsvImportAdmin();

//add-ons
if (class_exists('woocsvCustomFields')) {
    $woocsvCustomfields = new woocsvCustomFields();
}

if (class_exists('woocsvAttributes')) {
    $woocsvAttributes = new woocsvAttributes();
}

if (class_exists('woocsvVariableProducts')) {
    $woocsvVariableProducts = new woocsvVariableProducts();
}

?>