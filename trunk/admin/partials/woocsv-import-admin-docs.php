<?php
/**
 	** Documentation page
**/
 
?>
<div class="wrap">
	<h2>Documentation</h2>
	<hr>
	<h2>Woocommerce CSV importer</h2>
	Documentation is available in the <a href="https://allaerd.org/knowledgebase">knowledgebase</a>. If you still have problems, feel free to drop me a mail at <a href-"mailto:support@allaerd.org">support@allaerd.org</a>
	
	<h3>Common questions</h3>
	<h4>Text is is cut during import or when i use special characters.</h4>
 	<p class="description">
	 	Make sure you encode you file in UTF-8.
	 </p>
 	<h4>My images are not importing</h4>
 	<p class="description">
	 	Make sure you have cURL installed when you use use URL's. If you use filenames, make sure the images are imported in the media manager of wordpress.
	 </p>
	
	<h3>What fields are available</h3>
		<h4>SKU</h4>
			<p class="description">This is the unique identifier of your product. If a SKU is present it will be used to update!</p>
		<h4>ID</h4>
			<p class="description">This is the unique identifier of your product in the database. If a ID is present it will be used to update!</p>
		<h4>post_status</h4> 
	 		<p class="description">The status of you product, values: <code>publish, pending, draft, private, trash</code></p>
	 	<h4>post_title (mandatory)</h4>
	 		<p class="description">The title of your product</p>
	 	<h4>post_content</h4>
	 		<p class="description">The description of your product</p>
	 	<h4>post_excerpt</h4>
	 		<p class="description">The short description of your product</p>
		<h4>category</h4>
			<p class="description">The category of your product (or multiple). You can have multiple categories by separating them with a pipe <code>cat1|cat2</code>. You can make subcategories with -> <code>cat1->subcat1</code> and you can mix them all <code>cat1->subcat1|cat2|cat3->subcat2->subsubcat1</code>.</p>
		<h4>tags</h4>
			<p class="description">You can add tags or multiple with the pipe separator. <code>tag1|tag2|tag3</code></p>
		<h4>manage_stock</h4>
			<p class="description">Enable or disable management of stock. Values: <code>yes, no</code></p>
		<h4>stock_status</h4>
			<p class="description">the stock status of your product. You have to set it yourself if you want. Values: <code>instock, outofstock</code></p>
		<h4>backorders</h4>
			<p class="description">If you want tot allow backorders for your product. Values: <code>yes, no, notify</code></p>
		<h4>stock</h4>
			<p class="description">The actual stock of your product.</p>
		<h4>regular_price, sale_price</h4>
			<p class="description">If you have a normal price. You can fill in regular price. If your product is on sale, you should fill in regular price and sale price. The sale price should be lower than the regular price.</p>
		<h4>weight, length, width, height</h4>
			<p class="description">You can fill in the dimensions and weight of your product using these fields.</p>
		<h4>tax_status</h4>
			<p class="description">The status of your product tax. Values: <code>taxable, shipping, none</code></p>
		<h4>tax_class</h4>
			<p class="description">If you made any additional tax classes you can use this fields.</p>
		<h4>visibility</h4>
			<p class="description">Determines if your product is visible or not and where. Values: <code>visible, catalog, search, hidden</code></p>
		<h4>featured</h4>
			<p class="description">Determines if your product should be visible in any features lists or widgets. Values: <code>yes, no</code></p>
		<h4>featured_image</h4>
		<p class="description">You can add the featured image by adding the URL to it or the filename. If you enter the filename, you MUST upload it in advance with the media manager of wordpress</p>
		<h4>product_gallery</h4>
		<p class="description">You can add multiple images to your product gallery. Add them in a pipe separated list. <code>image1.jpg|image2.jpg</code> You can put in valid URL's or filenames.</p>
		<h4>shipping_class</h4>
		<p class="description">You can add your custom shipping class. If the class does not exists it will be added.</p>
		<h4>comment_status</h4>
		<p class="description">You can set the enable/disable review with the following values: <code>open, closed</code></p>
		<h4>ping_status</h4>
		<p class="description">You can enable the ping status the following values: <code>open, closed</code></p>
		<h4>menu_order</h4>
		<p class="description">you can change the menu order with an given number. the default is <code>0</code></p>
		<h4>change_stock</h4>
		<p class="description">
		Here you can enter the stock adjustment. It will be used to calculate the stock of an existing product. It does not work for new products. If you want to decrease the stock by 2 you enter <code>-2</code>.
		</p>
		<h4>post_author</h4>
		<p class="description">
			You can attach an author to a product by matching him by <code>id, slug, email, login</code>. ID is the actual id in the users table, slug is the nice name of the user, email is the email adres of the user and login is the login name.
		</p>
		<?php do_action ('woocsv_documentation')?>
</div>
