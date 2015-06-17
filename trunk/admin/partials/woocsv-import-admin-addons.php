<?php
/**
 * Add-ons page
 *
 */
?>

<div class="wrap">
    <h2>Add-ons</h2>
    <hr>
<?php
	global $woocsv_import;
	
		 if ( isset ( $woocsv_import->addons ) ) {
			 echo "<h3>Installed Add-on's</h3>";
			 echo '<table class="widefat"><thead><tr><th class="row-title">add-on</th><th>Version</th><th>Status</th></tr></thead><tbody>';
			 foreach ( $woocsv_import->addons as $addon  ) {
				 ?>
				 <tr>
				 	<td><?php echo $addon->name;?></td>
				 	<td><?php echo $addon->version;?></td>
				 	
				 <?php 				 
				 $url = 'http://localhost/api/wc-api/check_for_updates';
				 $response = wp_remote_post( $woocsv_import->api_url, 
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
?>

<h3>Available add-ons</h3>
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
						Custom fields are used to store all kind of information. Lots of other extensions and other plugins use it to store their data. Use this add-on to fill all that data using your CSV.
					</p>
				</a>
			</li>
		</ul>
</div>