<?php
/**
 * @package PreferabliForWooCommerce
 */


class PreferabliForWooCommerceDeactivate
{
    public static function deactivate() {
        flush_rewrite_rules();
    }
}