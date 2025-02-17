=== Preferabli for WooCommerce ===

Contributors: preferablitechadmin
Tags: WooCommerce, wine, spirits, beer
Requires at least: 5.2
Tested up to: 6.5
Requires PHP: 7.2
Stable tag: 2.10
License: The 3-Clause BSD License
License URI: https://opensource.org/licenses/BSD-3-Clause

Add Preferabli label images to your WooCommerce storefront. Data feeds and LTTT-JS coming soon.
 
== Description ==

Utilize industry-leading Preferabli functionality to optimize your customer experience and eliminate your time spent finding product label images to attach to your WooCommerce products. Preferabli for WooCommerce provides hosted product label images directly to your website.

Label images are hosted on a world-wide CDN and can be customized with settings such as maximum label sizes, background colors, forcing of square images and product category filters.

A subscription to Preferabli is required for this integration to function. Go to Preferabli to request a demo. Additional functionality such as automatic data feeds and LTTT-JS coming soon.

== Installation ==

1. Unzip and upload `preferabli-for-woocommerce.zip` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add your token and client_id provided by Preferabli (contact support if needed).
1. Update the max image sizes, background colors, etc if desired. 
1. The labels will begin showing, randomly, over a period of 30 minutes to spread out server loads and improve the user's experience.

Note: This plugin does *NOT* override existing images.
 
== Frequently Asked Questions ==
 
= Does this work for ecommerce platforms other than WooCommerce? =
 
Not currently, but Preferabli is committed to supporting additional platforms based on demand. Contact us at [support.preferabli.com](https://support.preferabli.com) for more information.
 
= Does this override existing labels? =
 
No, Preferabli labels have second priority to existing product labels. If you manually upload placeholder labels for every product (not recommended), they are considered a label and will need to be removed before Preferabli functionality will provide labels to impacted products.
 
== Changelog ==
 
= 1.0 =
* Initial build
 
= 1.1 =
* Bug fixes

= 1.2 =
* Bug fixes

= 1.3 =
* Add product SKU functionality
* Improve product category handling

= 1.4 =
* Add Data Feed support

= 1.5 =
* Update data feed hashing handling

= 1.6 / 1.7 =
* Minor patches

= 1.8 =
* Update feed.image - remove html formatting

= 1.9 =
* Add feed.customer data

= 2.0 =
* Bugs with timestamps

= 2.1 =
* Ensure updated timestamps on activation after deactivation

= 2.2 =
* Add ability to overwrite of one or more placeholders images/labels

= 2.3 =
* Bugfix for _wp_attachment_metadata

= 2.4 =
* Update Wordpress Version to 6.1

= 2.5 =
* Add ability to pull in prior "Wine Ring" branded configuration.

= 2.6 =
* Bugfix

= 2.7 =
* Added Product Unique Identifier Custom Key (post_meta)

= 2.8 =
* Confirmed compatibility through WordPress 6.4.1.

= 2.9 =
* Bugfix for placeholder image handling.

= 2.10 =
* Compatibility with 6.5
