<?php 
class woocsvAdminInfo {
	
	public static function addons () {
	global $woocsvImport;
	?>
	<style>
		
		.addons {
			overflow: hidden;
		}
				
		.addons li {
			float: left;
			margin: 0 1em 1em 0!important;
			padding: 0;
			vertical-align: top;
			width: 250px;
			
		}
		
		.addons li a {
			text-decoration: none;
			color: inherit;
			border: 1px solid #ddd;
			display: block;
			overflow: hidden;
			background: #f5f5f5;
			-webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,.2),inset 0 -1px 0 rgba(0,0,0,.1);
			box-shadow: inset 0 1px 0 rgba(255,255,255,.2),inset 0 -1px 0 rgba(0,0,0,.1);
		}
		
		.addons li a h3 {
			margin: 0!important;
			padding: 20px!important;
			background: #fff;
			min-height: 45px;
		}
		
		.addons li a p {
			padding: 20px!important;
			margin: 0!important;
			border-top: 1px solid #f1f1f1;
			min-height: 175px;
		}
		
		.addons li a h3 span {
			float: right;
			font-size: 12px;
			vertical-align: super;
		}
		
		.addons li a h3 span.installed {
			color : #12B34F;
		}
		
		.addons li a h3 span.update {
			color : #F2975E;
		}
		
	</style>
	<?php
/*

		 if ( isset ( $woocsvImport->addons ) ) {
			 echo "<h1>Installed Add-on's</h1>";
			 echo '<table class="widefat"><thead><tr><th class="row-title">add-on</th><th>Version</th><th>Status</th></tr></thead><tbody>';
			 foreach ( $woocsvImport->addons as $addon  ) {
				 ?>
				 <tr>
				 	<td><?php echo $addon->name;?></td>
				 	<td><?php echo $addon->version;?></td>
				 	
				 <?php 				 
				 $url = 'http://localhost/api/wc-api/check_for_updates';
				 $response = wp_remote_post( $woocsvImport->api_url, 
				 	array('body' => array( 'remote_slug' => $addon->remote_slug, 'version'=> $addon->version ))
				 );
				 
				 //if error stop
				 if ( is_wp_error($response) ) {
					 echo '<td&nbsp;</td></tr>';
				 } else {
 					 $response_body = json_decode( $response['body'] );
					 if ( isset ( $response_body->version ) && version_compare($response_body->version,$addon->version) === 1) {
						?>
						<td>version <?php echo $response_body->version;?> available. Please login into your account on <a href="http://allaerd.org/my-account">allaerd.org</a> to download the latest version</td>
						<?php	
					 }
					 echo '</tr>';
				 }
				 

				 
				 
			 }
			 echo '<tbody></table>';
		 }
		
*/
	?>
	<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h1>Available add-on's</h1>
		<ul class="addons">
			<li class="addon">
				<a href="http://allaerd.org/shop/get-them-all">
					<h3>Get Them all</h3>
					<p>
						You get a discount for the add-ons's in the bundle and updates are included for 360 days! 
					</p>
				</a>
			</li>
			<li class="addon">
				<a href="https://allaerd.org/shop/import-taxonomies/">
					<h3>Taxonomies</h3>
					<p>
						With this add-on you can import additinal taxonomies into Woocommerce. Custom taxonomies are used a lot in themes or extensions to add functionality to woocommerce. Brands is a custom taxonomy that might sound familiar.
 
					</p>
				</a>
			</li>
			<li class="addon">
				<a href="http://allaerd.org/shop/woocommerce-import-variable-products">
					<h3>Variable products</h3>
					<p>
						Import and manage your variable products and set up attributes used for variations. Variable products are products like t-shirts, you have the min different colours and sizes.
					</p>
				</a>
			</li>
			<li class="addon">
				<a href="http://allaerd.org/shop/import-downloadable-external-grouped-products">
					<h3>Downloadable, external, grouped products</h3>
					<p>
						With this add-on you can import Downloadable products, Grouped Products, External/Affiliate products and some additional fields like cross-sells and up-sells.
					</p>
				</a>
			</li>
			<li class="addon">
				<a href="http://allaerd.org/shop/woocommerce-import-attributes">
					<h3>Attributes</h3>
					<p>
						Import global attributes into Woocommerce. Import one or multiple. Control there visibility and attach multiple at once to your product.
					</p>
				</a>
			</li>
			<li class="addon">
				<a href="http://allaerd.org/shop/woocommerce-import-custom-fields">
					<h3>Custom Fields</h3>
					<p>
						Custom fields are used to store all kind of information. Lots of other extensions and other plugins use it to store there data. Use this add-on to fill all that data using your CSV.
					</p>
				</a>
			</li>
		</ul>
	
	<?php
	}
		
	public static function info() {
	?>		
	<h2>How to use this plugin?</h2>
		<ul>
			<li>Step 1. Read the documentation at <a target="_blank" href="http://allaerd.org/documentation">allaerd.org</a></li>
			<li>Step 2. Watch the instruction movie <a target="_blank" href="http://www.youtube.com/watch?v=RBLyoGCqa0Y">here</a></li>
			<li>Step 3. Goto the settings page and set the appropriate settings</li>
			<li>Step 4. Goto the header page and import a CSV file and link the right fields to the right columns</li>
			<li>Step 5. Goto the import section and upload the same CSV file</li>
			<li>Step 6. Check out the import preview and check if it OK!</li>
			<li>Step 7. Press go and wait until the import is finished!</li>
		</ul>
		
		<h2>Add-ons</h2>
		<p>
			You can get a lot of add-ons that import additional product type, attributes or custom fields.
			<ol>
				<li>Import variable products</li>
				<li>Import Attributes</li>
				<li>Import custom fields</li>
				<li>Import downloadable, grouped, affiliate/ external products and all kind of extra fields</li>
			</ol>
			Check out <a href="http://allaerd.org/shop/">Allaerd.org</a> to find all the add-ons.		
		</p>
	
		<h2>Example CSV</h2>
		<p>Check out the example CSV file <a href="<?php echo plugin_dir_url(__file__);?>example.csv">here</a></p>
		
		<h2>What fields are available</h2>
		<dl>
			<dt>SKU</dt>
				<dl>This is the unique identifier of your product. If a SKU is present it will be used to update!</dl>
			<dt>ID</dt>
				<dl>This is the unique identifier of your product in the database. If a ID is present it will be used to update!</dl>
			<dt>post_status</dt> 
		 		<dl>The status of you product, values: <code>publish, pending, draft, private, trash</code></dl>
		 	<dt>post_title (mandatory)</dt>
		 		<dl>The title of your product</dl>
		 	<dt>post_content</dt>
		 		<dl>The description of your product</dl>
		 	<dt>post_excerpt</dt>
		 		<dl>The short description of your product</dl>
			<dt>category</dt>
				<dl>The category of your product (or multiple). You can have multiple categories by separating them with a pipe <code>cat1|cat2</code>. You can make subcategories with -> <code>cat1->subcat1</code> and you can mix them all <code>cat1->subcat1|cat2|cat3->subcat2->subsubcat1</code>.</dl>
			<dt>tags</dt>
				<dl>You can add tags or multiple with the pipe separator. <code>tag1|tag2|tag3</code></dl>
			<dt>manage_stock</dt>
				<dl>Enable or disable management of stock. Values: <code>yes, no</code></dl>
			<dt>stock_status</dt>
				<dl>the stock status of your product. You have to set it yourself if you want. Values: <code>instock, outofstock</code></dl>
			<dt>backorders</dt>
				<dl>If you want tot allow backorders for your product. Values: <code>yes, no, notify</code></dl>
			<dt>stock</dt>
				<dl>The actual stock of your product.</dl>
			<dt>price, regular_price, sale_price</dt>
				<dl>If you have a normal price. You can fill in price and regular price. They should both be equal. If your products is on sale, you should fill in price, regular price and sale price. The sale price should be equal to the price and should be lower than the regular price.</dl>
			<dt>weight, length, width, height</dt>
				<dl>You can fill in the dimensions and weight of your product using these fields.</dl>
			<dt>tax_status</dt>
				<dl>The status of your product tax. Values: <code>taxable, shipping, none</code></dl>
			<dt>tax_class</dt>
				<dl>If you made any additional tax classes you can use this fields.</dl>
			<dt>visibility</dt>
				<dl>Determines if your product is visible or not and where. Values: <code>visible, catalog, search, hidden</code></dl>
			<dt>featured</dt>
				<dl>Determines if your product should be visible in any features lists or widgets. Values: <code>yes, no</code></dl>
			<dt>images <b>(deprecated)</b></dt>
			<dl>You can add images by name or URL. If you enter the filename, the plugin will search the images to match it and use the latest. If you enter a valid URL it will try to upload it with the media manager. You can have multiple images seperated by pipe. The last image is taken as featured image. Values: <code>http://www.example.com/image1.jpg|image2.jpg|image3.jpg|http://www.example.com/image4.jpg</code>. Image 4 will be the featured image in this case.</dl>
			<dt>featured_image</dt>
			<dl>You can add the featured image by adding the URL to it or the filename. If you enter the filename, you MUST upload it in advance with the media manager of wordpress</dl>
			<dt>product_gallery</dt>
			<dl>You can add multiple images to your product gallery. Add them in a pipe separated list. <code>image1.jpg|image2.jpg</code> You can put in valid URL's or filenames.</dl>
			<dt>shipping_class</dt>
			<dl>You can add your custom shipping class. If the class does not exists it will be added.</dl>
			<dt>comment_status</dt>
			<dl>You can set the enable/disable review with the following values: <code>open, closed</code></dl>
			<dt>ping_status</dt>
			<dl>You can enable the ping status the following values: <code>open, closed</code></dl>
			<dt>menu_order</dt>
			<dl>you can change the menu order with an given number. the default is <code>0</code></dl>
			<dt>change_stock</dt>
			<dl>
			Here you can enter the stock adjustment. It will be used to calculate the stock of an existing product. It does not work for new products. If you want to decrease the stock by 2 you enter <code>-2</code>.
			</dl>
			<dt>post_author</dt>
			<dl>
				You can attach an author to a product by matching him by <code>id, slug, email, login</code>. ID is the actual id in the users table, slug is the nice name of the user, email is the email adres of the user and login is the login name.
			</dl>
		</dl>	
		<?php
	}
}