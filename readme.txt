=== Woocommerce CSV importer ===
Contributors: Allaerd
Tags: Woocommerce, commerce, e-commerce, ecommerce, inventory, stock, products, import, csv, multiple images, upload
Requires at least: 3.4.0
Tested up to: 3.5.0
Stable tag: 1.2.8
Donate link: http://allaerd.org
License: GPLv2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import products into woocommerce.

== Description ==

Import and manage products in Woocommerce. Upload your csv file, create your custom header and import. The import plugin uses AJAX to import the products. No more timeouts on big files. This plugin is tested to 10.000+ products.

First step is to look at your settings, select the right field separator and take a look at the other settings as well. 

When you have done that it's time to link the right columns to the right fields. Upload your CSV file in the header section and link them. If the plugin can map the fields it will do so automatically.

When you have mapped the right fields, it is time to import!

= There are a few nice add-ons: =

1. Import custom fields
2. Import attributes
3. Import variable products

You can find them at [allaerd.org](http://allaerd.org/shop)

If you have questions about the add-ons, please fill in my [contact form](http://allaerd.org/contact/). If you have questions about the free part, you can post them in the forum.


== Frequently Asked Questions ==

= How do i use it =

[youtube http://www.youtube.com/watch?v=RBLyoGCqa0Y]

= I get the error "something went wrong" =

Change the number of rows to process at the same time. You might have run into timeouts!

= What fields can i import? =

* sku
* post_name (permalink)
* post_status
* post_title
* post_content
* post_excerpt
* category
* tags
* stock
* price
* regular_price
* sales_price
* weight
* length
* width
* height
* images (depreciated)
* featured_image
* product_gallery
* tax_status
* tax_class
* stock_status
* visibility
* backorder
* featured
* manage_stock
* shipping_class
* comment_status


= Max number of files in a directory = 

If you have a FAT32 filesystem you can run into troubles when you are trying to put more than 15.000+ files in a directory!

== Screenshots ==

1. Upload a CSV and create a header
2. Link the fields to the right column
3. Upload the CSV, look at the preview and run your import!

== Installation ==

1. Upload to the plugins directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Upgrade Notice ==

Version 1.+  is very different. Please make a backup first before you upgrade! You can find older version at [allaerd.org] (http://allaerd.org/shop “the shop”)
== Changelog ==

= 1.2.8 =
* stock to '' as default instead of 0
* solved bug with featured image not working if you use names. ( thanks testcase Daniel Zenklusen )

= 1.2.7 =
* typo's
* changed the way the filename is used. Windows users experienced problems.
* lot's of improvements

= 1.2.6 = 
* if you use globals.....make them global 

= 1.2.5 =
* delete transients during import

= 1.2.4 =
* Solved a bug with new products (thanks Niall Walsh)

= 1.2.3 =
* solved another image by name problem! Now use get_posts to retreive image instead of query.

= 1.2.2 =
* Solve image problem by using cURL to get the images with an URL, file_get_contents is sometimes disabled on certain servers
* Start chaging some file names to be more in the wordpress standard
* Added some hooks and filters
* Some minor improvments
* added comment_status

= 1.2.1 =
* added some info about the max file size
* warning for ini_set resolved
* solved bug that product gallery was not shown when images field was used.
* solved bug that did not find images from the media manager that contain spaces

= 1.2.0 =
* Change position for menu, sometimes it confilicts with other plugins
* Added setting to merge products
* Added new image fields for featured image and product gallery. They used to be both in the images field.
* Added more info, descriptions and help texts. 
* Mayor code, speed and memory improvements
* Added post_name support
* Added shipping_class support
* Added options for category handling
* Added support for multi-site

= 1.1.2 =
* fixed a bug with the multiple categories. Now all categories are linked to the product instead of the last

= 1.1.1 =
* fixed a bug with the sales price. Was a typo in the fields. Now sales prices should be imported correct!
* If you have no SKU or leave it empty, a new post is created. but remember no SKU no updating!!!! 

= 1.1.0 =
This is a mayor revision of the plugin. A lot of small and larger bugfixes are done! Thanks to some help from the forum and code suggestions. 

* ajaxurl in javascript instead of hard link to ajax page (solves the problem that a header or the settings could not be saved!)
* added option to enable/disable adding images to gallery
* bug fixes for IE
* header and settings are not saved anymore through ajax.

= 1.0.8 =
* fixed a possible bug when saving settings

= 1.0.7 =
* add the product by default to simple term

= 1.0.6 =
* add ob_get_clean() to prevent errors from stopping the script

= 1.0.5 =
* solved bug with upload not working on windows machine!
* better handling images 

= 1.0.4 =
* added support for product gallery. the last image is set as thumbnail! (thanks to Fahad Mahmood) 

= 1.0.3 =
* add more options for number of rows to process in the same run to prevent timeouts

= 1.0.2 =
* add manage stock to field list
 
= 1.0.1 =
* Admin warnings only show on plugin page 
* get rid of some old functions 

= 1.0.0 =
* Complete new importer. With AJAX calls, custom headers and add-ons to import custom fields and attributes. BAckup your old stuff before trying this one!

= 0.7.3 =
* add option to import custom fields. In your CSV file add extra columns with the prefix cf_ . For example if you want the custom field colour, you add cf_colour in your header.

= 0.7.2 =
* fix a small bug. Menu was not visible because other plugin used the same menu order.

= 0.7.1 =
* fix some bugs (thanks to fransberns)

= 0.7.0 =
* fix for wordpress 3.5 wp_update_post does not insert anymore :-( 

= 0.6.1 =
* fixed header already sent bug when activating

= 0.6.0 =
* added experimental schedule function. Can be enabled for fixed imports in the settings page.
* solved some bugs related to empty fields in import
* solved some bugs related to check if a file is there and if it is valid

= 0.5.5 =
* added setting to convert a comma to a dot in the prices

= 0.5.4 =
* added a setting to use auto_detect_line_endings to solve the problem that the import is seen as ONE big file :D

= 0.5.3 =
* added tax functionality. You can now import tax related stuff. You can now import tax status ( taxable, shipping, none ) and the tax class. The tax class is somehow a but strange. If you have a tax class called 10 RATE it is stored like 10-rate. 

= 0.5.2 =
* Sub categories did not appear when importing, while they where there! This is related to http://wordpress.stackexchange.com/questions/24498/wp-insert-term-parent-child-problem. Problem fixed!

= 0.5.1 =
* small bug fixes because of header already sent when updating plugin

= 0.5 =
* categories can now have children. In the category column you can now do like this: cat1->subcat1->subsubcat1|cat2->subcat2|cat3
* added an option in the settings page to select a field seperator
* made a function to check the options on init

= 0.4.1 =
* tags can be imported now as well. You can add one or multiple. In the tag column of the import do like this: tag1|tag2|tag3. Now the product will have 3 tags.
* added the example CSV to the settings page

= 0.4 =
* images can now also be from an URL. In the image fields you can add http://mydomain.com/image1.jpg. You can also mix them!

= 0.3 =
* add setting to handle image imports. You can now choose to delete current images before importing new ones or append the image to the already existing ones
* add the possibility to have multiple images. In the column you can do image1.jpg|image2.jpg|image3.jpg
* solved some minor bugs added some additional checks

= 0.2 =
* added a setting page where you can create the csvimport directory
* fixed a css bug

= 0.1 =

first version.
