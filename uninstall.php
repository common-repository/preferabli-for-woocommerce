<?php
/**
 * Auto-triggered during uninstall.
 *
 * @package PreferabliForWooCommerce
 */

if ( !defined( "WP_UNINSTALL_PLUGIN") ) {
    die();
}

// Purge all the references to the now deleted preferabli-label post type entries.
global $wpdb;
$sql = "DELETE FROM {$wpdb->prefix}postmeta WHERE `meta_key` = '_thumbnail_id' AND `meta_value` IN (SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_type`='preferabli-label') ";
$wpdb->query($sql);


$preferabli_labels = get_posts( array('post_type'=>'preferabli-label', 'post_status' => 'any, trash, auto-draft', 'numberposts'=> -1 ) );
foreach ( $preferabli_labels AS $preferabli_label ) {
    wp_delete_post($preferabli_label->ID, true);
}

/**
 * Delete Post Meta within WooCommerce Products.
 */
$post_meta_keys = array(
    "_preferabli_label_checked_at",
    "_preferabli_label_expires_at",
    "_preferabli_label_url"
);
foreach ($post_meta_keys as $post_meta_key) {
    delete_post_meta_by_key($post_meta_key);
}

/**
 * Delete general options
 */
delete_option("p4wc_plugin_options");
delete_site_option("p4wc_plugin_options");





