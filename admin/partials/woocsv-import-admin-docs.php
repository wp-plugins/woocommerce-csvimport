<?php
/**
 	** Documentation page
**/
 
?>
<div class="wrap">
	<h2><?php echo __('Documentation','woocsv'); ?></h2>
	<hr>
	<h2><?php echo __('Woocommerce CSV importer','woocsv'); ?></h2>
	<?php echo sprintf(__('Documentation is available in the %s knowledgebase %s. If you still have problems, feel free to drop me a mail at %s','woocsv'),'<a href="https://allaerd.org/knowledgebase">','</a>','<a href-"mailto:support@allaerd.org">support@allaerd.org</a>'); ?>	
	<h3><?php echo __('Common questions','woocsv'); ?></h3>
	<h4><?php echo __('Text is is cut during import or when i use special characters.','woocsv'); ?></h4>
 	<p class="description">
	 	<?php echo __('Make sure you encode you file in UTF-8.','woocsv'); ?>
	 </p>
 	<h4>My images imported double</h4>
 	<p class="description">
	 	<?php echo __('When you import with images with url\'s the images are imported everytime','woocsv'); ?>
	 </p>
	
	<h3><?php echo __('What fields are available','woocsv'); ?></h3>
		<h4>SKU</h4>
			<p class="description"><?php echo __('This is the unique identifier of your product. If a SKU is present it will be used to update!','woocsv'); ?></p>
		<h4><?php echo __('ID','woocsv'); ?></h4>
			<p class="description"><?php echo __('This is the unique identifier of your product in the database. If a ID is present it will be used to update! Only use this if you know are really sure, best use the sku!','woocsv'); ?></p>
		<h4><?php echo __('post_status','woocsv'); ?></h4> 
	 		<p class="description"><?php echo sprintf(__('%s<code>%s</code>','woocsv'),
		 			'The status of you product, values:',
		 			'publish, pending, draft, private, trash
		 			'); ?></p>
	 	<h4><?php echo __('post_title (mandatory)','woocsv'); ?></h4>
	 		<p class="description"><?php echo __('The title of your product','woocsv'); ?></p>
	 	<h4><?php echo __('post_content','woocsv'); ?></h4>
	 		<p class="description"><?php echo __('The description of your product','woocsv'); ?></p>
	 	<h4><?php echo __('post_excerpt','woocsv'); ?></h4>
	 		<p class="description"><?php echo __('The short description of your product','woocsv'); ?></p>
		<h4><?php echo __('category','woocsv'); ?></h4>
			<p class="description"><?php echo sprintf(__('The category of your product (or multiple). You can have multiple categories by separating them with a pipe %s. You can make subcategories with -> <code>cat1->subcat1</code> and you can mix them all <code>cat1->subcat1|cat2|cat3->subcat2->subsubcat1</code>.','woocsv'),
				'<code>cat1|cat2</code>',
				''
				); ?>
				</p>
		<h4><?php echo __('tags','woocsv'); ?></h4>
			<p class="description"><?php echo __('You can add tags or multiple with the pipe separator.','woocsv'); ?><code>tag1|tag2|tag3</code></p>
		<h4><?php echo __('manage_stock','woocsv'); ?></h4>
			<p class="description"><?php echo __('Enable or disable management of stock. Values:','woocsv'); ?><code>yes, no</code></p>
		<h4><?php echo __('stock_status','woocsv'); ?></h4>
			<p class="description"><?php echo __('The stock status of your product. You have to set it yourself if you want. Values:','woocsv'); ?><code>instock, outofstock</code></p>
		<h4><?php echo __('backorders','woocsv'); ?></h4>
			<p class="description"><?php echo __('If you want tot allow backorders for your product. Values:','woocsv'); ?><code>yes, no, notify</code></p>
		<h4><?php echo __('stock','woocsv'); ?></h4>
			<p class="description"><?php echo __('The actual stock of your product.','woocsv'); ?></p>
		<h4><?php echo __('regular_price, sale_price','woocsv'); ?></h4>
			<p class="description"><?php echo __('If you have a normal price. You can fill in regular price. If your product is on sale, you should fill in regular price and sale price. The sale price should be lower than the regular price.','woocsv'); ?></p>
		<h4><?php echo __('weight, length, width, height','woocsv'); ?></h4>
			<p class="description"><?php echo __('You can fill in the dimensions and weight of your product using these fields.','woocsv'); ?></p>
		<h4><?php echo __('tax_status','woocsv'); ?></h4>
			<p class="description"><?php echo __('The status of your product tax. Values:','woocsv'); ?><code>taxable, shipping, none</code></p>
		<h4><?php echo __('tax_class','woocsv'); ?></h4>
			<p class="description"><?php echo __('If you made any additional tax classes you can use this fields.','woocsv'); ?></p>
		<h4><?php echo __('visibility','woocsv'); ?></h4>
			<p class="description"><?php echo __('Determines if your product is visible or not and where. Values:','woocsv'); ?><code>visible, catalog, search, hidden</code></p>
		<h4><?php echo __('featured','woocsv'); ?></h4>
			<p class="description"><?php echo __('Determines if your product should be visible in any features lists or widgets. Values:','woocsv'); ?><code>yes, no</code></p>
		<h4><?php echo __('featured_image','woocsv'); ?></h4>
		<p class="description"><?php echo __('You can add the featured image by adding the URL to it or the filename. If you enter the filename, you MUST upload it in advance with the media manager of wordpress','woocsv'); ?></p>
		<h4><?php echo __('product_gallery','woocsv'); ?></h4>
		<p class="description"><?php echo __('You can add multiple images to your product gallery. Add them in a pipe separated list. <code>image1.jpg|image2.jpg</code> You can put in valid URL\'s or filenames.','woocsv'); ?></p>
		<h4><?php echo __('shipping_class','woocsv'); ?></h4>
		<p class="description"><?php echo __('You can add your custom shipping class. If the class does not exists it will be added.','woocsv'); ?></p>
		<h4><?php echo __('comment_status','woocsv'); ?></h4>
		<p class="description"><?php echo __('You can set the enable/disable review with the following values:','woocsv'); ?><code>open, closed</code></p>
		<h4><?php echo __('ping_status','woocsv'); ?></h4>
		<p class="description"><?php echo __('You can enable the ping status the following values:','woocsv'); ?><code>open, closed</code></p>
		<h4><?php echo __('menu_order','woocsv'); ?></h4>
		<p class="description"><?php echo __('you can change the menu order with an given number. the default is','woocsv'); ?><code>0</code></p>
		<h4><?php echo __('change_stock','woocsv'); ?></h4>
		<p class="description">
		<?php echo __('Here you can enter the stock adjustment. It will be used to calculate the stock of an existing product. It does not work for new products. If you want to decrease the stock by 2 you enter','woocsv'); ?><code>-2</code>.
		</p>
		<h4><?php echo __('post_author','woocsv'); ?></h4>
		<p class="description">
			<?php echo sprintf(__('%s <code> % s</code> %s','woocsv'),
				'You can attach an author to a product by matching him by',
				'id, slug, email, login.',
				'ID is the actual id in the users table, slug is the nice name of the user, email is the email adres of the user and login is the login name.'
				
				
				); ?> 
		</p>
		<?php do_action ('woocsv_documentation')?>
</div>
