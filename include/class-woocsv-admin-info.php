<?php 
class woocsvAdminInfo {
	public static function info() {
	?>		
	<h2>How to use this plugin?</h2>
		<ul>
			<li>Step 1. Goto the settings page and set the appropriate settings</li>
			<li>Step 2. Goto the header page and import a CSV file</li>
			<li>Step 3. Link the right fields to the right columns and press save</li>
			<li>Step 4. Goto the import section and upload the same CSV file</li>
			<li>Step 5. Check out the import preview and check if it OK!</li>
			<li>Step 6. Press go and wait until the import is finished!</li>
		</ul>
		<h2>What fields are available</h2>
		<dl>
			<dt>SKU</dt>
				<dl>This is the unique identifier of your product. If a SKU is present it will be used to update!</dl>
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
			<dt>images <b>(DEPRECIATED)</b></dt>
			<dl>You can add images by name or URL. If you enter the filename, the plugin will search the images to match it and use the latest. If you enter a valid URL it will try to upload it with the media manager. You can have multiple images seperated by pipe. The last image is taken as featured image. Values: <code>http://www.example.com/image1.jpg|image2.jpg|image3.jpg|http://www.example.com/image4.jpg</code>. Image 4 will be the featured image in this case.</dl>
			<dt>featured_image</dt>
			<dl>You can add the featured image by adding the URL to it or the filename. If you enter the filename, you MUST upload it in advance with the media manager of wordpress</dl>
			<dt>product_gallery</dt>
			<dl>You can add multiple images to your product gallery. Add them in a pipe separated list. <code>image1.jpg|image2.jpg</code> You can put in valid URL's or filenames.</dl>
			<dt>shipping_class</dt>
			<dl>You can add your custom shipping class. If the class does not exists it will be added.</dl>
			<dt>comment_status</dt>
			<dl>You can set the enable/disable review with the following values: <code>open, closed</code></dl>
		</dl>	

		<h2>Support the free plugin</h2>
		Want to support the free version. Please consider a donation :-)
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick" />
		<input type="hidden" name="hosted_button_id" value="PGEBD4BHNH6W4" />
		<input type="image" alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/NL/i/btn/btn_donateCC_LG.gif" />
		<img alt="" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1" border="0" /></form>

		<?php
	}
}