=== Woocommerce CSV importer ===
Contributors: allaerd
Tags: woocommerce, commerce,e-commerce, ecommerce, inventory, stock, products, import, csv, zip, multiple images, upload
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
Donate link: http://allaerd.org
License: GPLv2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import products into woocommerce.

== Description ==

Import products into woocommerce. You can upload a zip, select you're own files or put them at a fixed place. An example csv is included.

The import handles most common fields, but also images. You can even upload multiple images for one product in youre csv file!

== Installation ==

1. Upload to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. create a directoy in uploads called csvimport/fixed

== Frequently Asked Questions ==
1. What fields can i import?
title, description, short_description, category, stock,price, regular_price, sales_prices, weight, length, width, height, sku, picture

2. Can i upload multiple pictures?
In the picture fields you can put image1.jpg|image2.jpg|image3.jpg to handle multiple files

3. Do you have an example csv?
In the plugin folder there is and example csv.

== Upgrade Notice ==

nothing special so far :D

== Changelog ==

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