=== Woocommerce CSV importer ===
Contributors: allaerd
Tags: woocommerce, commerce,e-commerce, ecommerce, inventory, stock, products, import, csv, zip, multiple images, upload
Requires at least: 3.0.1
Tested up to: 3.4.2
Stable tag: 0.5.1
Donate link: http://allaerd.org
License: GPLv2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import products into woocommerce.

== Description ==

Import products into woocommerce. You can upload a zip, select you're own files or put them at a fixed place. An example csv is included.

The import handles most common fields, but also images. You can even upload multiple images for one product in youre csv file!

With the new schedule option it can handle REALLY BIG files!

== Installation ==

1. Upload to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. create a directoy in uploads called csvimport/fixed

== Frequently Asked Questions ==
1. What fields can i import?
title, description, short_description, category, stock,price, regular_price, sales_prices, weight, length, width, height, sku, picture, tags, tax status and tax class

2. Can i upload multiple pictures?
In the picture fields you can put image1.jpg|image2.jpg|image3.jpg to handle multiple files

3. Do you have an example csv?
In the plugin folder there is and example csv. And on the settings page there is also an example as well!

4. The most important is the SKU! The SKU is used to make a product UNIQUE! Make sure you always have one!. It is also used to update the products when you import multiple times.

== Upgrade Notice ==

nothing special so far :D

== Changelog ==

= 0.6.1 =
* fixed header already sent bug when activating

= 0.6.0 =
* added experimental schedule function. Can be enabled for fixed imports in the settngs page.
* solved some bugs related to empy fields in import
* solved some bugs related to check if a file is there and if it is valid

= 0.5.5 =
* added setting to convert a comma to a dot in the prices

= 0.5.4 =
* added a setting to use auto_detect_line_endings to solve the problem that the import is seen as ONE big file :D

= 0.5.3 =
* added tax functionality. You can now import tax related stuff. You can now import tax status ( taxable, shipping, none ) and the tax class. The tax class is somehow a but strange. If you have a tax class called 10 RATE it is stored like 10-rate. 

= 0.5.2 =
* Sub categories did not apear when importing, while they where there! This is related to http://wordpress.stackexchange.com/questions/24498/wp-insert-term-parent-child-problem. Problem fixed!

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
* add the possibiliy to have multiple images. In the column you can do image1.jpg|image2.jpg|image3.jpg
* solved some minor bugs added some additional checks

= 0.2 =
* added a setting page where you can create the csvimport directory
* fixed a css bug

= 0.1 =

first version.