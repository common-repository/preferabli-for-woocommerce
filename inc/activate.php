<?php
/**
 * @package PreferabliForWooCommerce
 */

class PreferabliForWooCommerceActivate
{
    public static function activate($p4wc_db_version) {
        // Set default option key/values if not already set.
        $required_keys_with_default_values = self::getRequiredKeysWithDefaultValues();
        // Get pre-existing values from WR4WC plugin if possible and overwrite as default value.
        if ( $wr4wc_options = get_option( 'wr4wc_plugin_options', array() ) ) {
            foreach ( $required_keys_with_default_values AS $required_key=>$required_value ) {
                if ( array_key_exists($required_key, $wr4wc_options) ) {
                    $required_keys_with_default_values[$required_key] = $wr4wc_options[$required_key];
                }
            }
        }

        $p4wc_options = get_option( 'p4wc_plugin_options', array() );
        foreach ( $required_keys_with_default_values AS $required_key=>$required_value ) {
            if ( !array_key_exists($required_key, $p4wc_options) ) {
                $p4wc_options[$required_key] = $required_value;
            }
        }

        $p4wc_options["last_updated_at"] = date('Y-m-d H:i:s');
        update_option('p4wc_plugin_options', $p4wc_options);

        update_option( "p4wc_db_version", $p4wc_db_version );


        // Update wp_options: `wr4wc_db_version` -- this can be ignored.
        // Update wp_options: `wr4wc_feed_hash`
        if ( !$p4wc_feed_hash = get_option( 'p4wc_feed_hash' ) ) {
            if ( $wr4wc_feed_hash = get_option( 'wr4wc_feed_hash' ) ) {
                update_option('p4wc_feed_hash', $wr4wc_feed_hash);
            }
        }

        $p4wc_options = get_option( 'p4wc_plugin_options' );
        // Update wp_options: `wr4wc_plugin_options`
        if ( !$p4wc_feed_hash = get_option( 'p4wc_plugin_options' ) ) {
            if ( $wr4wc_plugin_options = get_option( 'wr4wc_plugin_options' ) ) {
                update_option('p4wc_plugin_options', $wr4wc_plugin_options);
            }
        }

        global $wpdb;
        // Move WR4WC post type "wine-ring-label" to "preferabli-label" post type.
        $sql = "UPDATE {$wpdb->prefix}posts SET  `post_type` = 'preferabli-label' WHERE  `post_type` = 'wine-ring-label' ";
        $wpdb->query($sql);

        // Update postmeta: `_wine_ring_label_expires_at`
        $sql = "UPDATE {$wpdb->prefix}postmeta SET  `meta_key` = '_wine_ring_label_expires_at' WHERE  `meta_key` = '_preferabli_label_expires_at' ";
        $wpdb->query($sql);

        // Update postmeta: `_wine_ring_label_checked_at`
        $sql = "UPDATE {$wpdb->prefix}postmeta SET  `meta_key` = '_wine_ring_label_checked_at' WHERE  `meta_key` = '_preferabli_label_checked_at' ";
        $wpdb->query($sql);

        // Update postmeta: `_wine_ring_label_url`
        $sql = "UPDATE {$wpdb->prefix}postmeta SET  `meta_key` = '_wine_ring_label_url' WHERE  `meta_key` = '_preferabli_label_url' ";
        $wpdb->query($sql);

        flush_rewrite_rules();
    }

    public static function updateDB($p4wc_db_version) {
        self::activate($p4wc_db_version);
    }

    private static function getRequiredKeysWithDefaultValues() {
        $required_keys_with_default_values = array(
            "api_feed_key" => "",
            "api_feed_custom_field_slugs" => "",
            "api_feed_category_ids" => "",
            "api_token" => "",
            'channel_id' => "SET_CHANNEL_#_HERE",
            'placeholder_ids' => "",
            'client_interface' => get_site_url(),
            'crop_fill_hex_color' => '#FFFFFF',
            'force_square_images' => "0",
            'default_max_image_height' => 600,
            'default_max_image_width' => 600,
            'label_whitelisted_category_ids' => "",
            'unique_identifier_method' => "product_id",
            'unique_identifier_method_custom_key' => ""
        );

        return $required_keys_with_default_values;
    }
}