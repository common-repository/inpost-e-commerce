=== InPost eCommerce ===
Contributors: darthurinpost.co.uk
Donate link: http://inpost.co.uk
Tags: e-commerce, InPost, shop, parcel, lockers, shipping, delivery
Requires at least: 3.7
Tested up to: 3.9
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

InPost E-Commerce is a free plugin that allows parcel creation and label printing for delivery to an InPost locker.

== Description ==

This shipping plugin was developed by InPost UK Ltd. for WordPress e-Commerce.
Once installed the plugin allows customers to opt for delivery of their
purchased items to an InPost locker terminal. 

When checking out, customers can specify a destination locker terminal where
their purchased items will be delivered on the next day after dispatch. Once
the item(s) have been delivered to a locker terminal, the customer will
receive an email containing a QR code as well as a mobile SMS notification
containing a PIN code. They can then go to their selected locker, scan the QR
code or enter the PIN code manually using the onscreen keyboard on the
terminal, in order to collect the parcel. At present, InPost's UK network 
comprises over 1000 locker terminals across the country.

== Installation ==

= Minimum Requirements =

* WordPress 3.8 or greater
* WP eCommerce 3.8 or greater
* PHP 5.0 or greater
* PHP cURL

1. Upload the folder 'inpost-wp-e-commerce' to the '/wp-content/plugins/' directory
1. You *must* also create the directory 'inpost-wp-e-commerce-pdf'. This must exist in the plugins directory
1. Make sure that the inpost-wp-e-commerce-pdf folder has 777 permisions, or
that the user Apache has write permisions
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Set up the InPost Shipping method within WP eCommerce

== Frequently Asked Questions ==

= Is this a stand alone plugin? =

No, the plugin requires WP eCommerce.

= What is InPost =

InPost is a company that provides customers a means of delivering a parcel to 
one of our lockers for later collection. This removes the need for the 
customer to stay in and wait for a delivery person.

= What Else Is Required =

You will need to talk to a sales representitive to get an InPost Account. This
will allow you to connect to our servers for parcel and label creation.

Please call 033 033 52024 (UK only) or contact our sales team on
ecommerce.team@inpost.co.uk

== Screenshots ==

1. This shows the new fields that the customer is asked to fill in for an
InPost parcel delivery.

== Changelog ==

= 1.4 - 05/10/2015 =

* Fix - The filename extension changes for the Epl2 format files.
* Fix - The number of parcels shown in the list is increased to ten.

= 1.3 - 02/10/2015 =

* New Feature - Add the ability to select the type of printer that the
customer needs to print their labels on.
* Fix - The plugin was using the wrong subdirectory for the PDF files.

= 1.2 =

* Fix - The installer was not working.
* Fix - Added the cURL option which allows us to send / receive REST API calls
using https.

= 1.0 =

* Created the plugin

== Upgrade Notice ==

= 1.3 =

The plugin was using the wrong subdirectory for the PDF files.
The maximum weight and size for parcels was adjusted.

= 1.2 =

The installer is not working. I have tried to resolve this by moving the PDF
folder.

= 1.0 =

Created this initial version.

