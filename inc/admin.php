<?php
/**
 * @package PreferabliForWooCommerce
 */

function p4wc_register_settings() {
    register_setting( 'p4wc_plugin_options', 'p4wc_plugin_options', 'p4wc_plugin_options_validate' );
    add_settings_section( 'api_feed_settings', 'Feed Settings', 'p4wc_api_feed_section_text', 'p4wc_plugin' );
    add_settings_section( 'api_label_settings', 'Label Settings', 'p4wc_api_label_section_text', 'p4wc_plugin' );

    add_settings_field( 'p4wc_setting_api_feed_key', 'Data Feed Key:', 'p4wc_setting_api_feed_key', 'p4wc_plugin', 'api_feed_settings' );
    add_settings_field( 'p4wc_setting_api_feed_category_ids', 'Data Feed Categories:', 'p4wc_setting_api_feed_category_ids', 'p4wc_plugin', 'api_feed_settings' );
    add_settings_field( 'p4wc_setting_api_feed_custom_field_slugs', 'Data Feed: Product Custom Field Slugs', 'p4wc_setting_api_feed_custom_field_slugs', 'p4wc_plugin', 'api_feed_settings' );

    add_settings_field( 'p4wc_setting_api_token', 'API Token', 'p4wc_setting_api_token', 'p4wc_plugin', 'api_label_settings' );
    add_settings_field( 'p4wc_setting_channel_id', 'Channel ID', 'p4wc_setting_channel_id', 'p4wc_plugin', 'api_label_settings' );
    add_settings_field( 'p4wc_setting_unique_identifier_method', 'Product Unique Identifier', 'p4wc_setting_unique_identifier_method', 'p4wc_plugin', 'api_label_settings' );
    add_settings_field( 'p4wc_setting_unique_identifier_method_custom_key', 'Product Unique Identifier Custom Key', 'p4wc_setting_unique_identifier_method_custom_key', 'p4wc_plugin', 'api_label_settings' );
    add_settings_field( 'p4wc_setting_client_interface', 'Client Interface (URL)', 'p4wc_setting_client_interface', 'p4wc_plugin', 'api_label_settings' );
    add_settings_field( 'p4wc_setting_default_max_image_height', 'Default Max Height', 'p4wc_setting_default_max_image_height', 'p4wc_plugin', 'api_label_settings' );
    add_settings_field( 'p4wc_setting_default_max_image_width', 'Default Max Width', 'p4wc_setting_default_max_image_width', 'p4wc_plugin', 'api_label_settings' );
    add_settings_field( 'p4wc_setting_force_square_images', 'Force square images?', 'p4wc_setting_force_square_images', 'p4wc_plugin', 'api_label_settings');
    add_settings_field( 'p4wc_setting_crop_fill_hex_color', 'Fill hex color for forced square images', 'p4wc_setting_crop_fill_hex_color', 'p4wc_plugin', 'api_label_settings' );
    add_settings_field( 'p4wc_setting_label_whitelisted_category_ids', 'Label Categories', 'p4wc_setting_label_whitelisted_category_ids', 'p4wc_plugin', 'api_label_settings' );
    add_settings_field( 'p4wc_setting_last_updated_at', 'Options last updated', 'p4wc_setting_last_updated_at', 'p4wc_plugin', 'api_label_settings_hide' );
    add_settings_field( 'p4wc_setting_placeholder_ids', 'Placeholder IDs', 'p4wc_setting_placeholder_ids', 'p4wc_plugin', 'api_label_settings' );

}
add_action( 'admin_init', 'p4wc_register_settings' );

function p4wc_add_color_pick($hook){
    if(is_admin()){
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('p4wc_plugin_colorpicker', plugins_url('settings.js', __FILE__), array('wp-color-picker'), false, true);
    }
}

add_action('admin_enqueue_scripts', 'p4wc_add_color_pick');

function p4wc_plugin_options_validate( $input ) {
    $newinput['api_feed_key'] = trim( $input['api_feed_key'] );
    if ( strlen($newinput['api_feed_key'] ) > 6 ) {
        // $hash_options = get_option('p4wc_feed_hash');
        $newinput['api_feed_hash'] = hash("sha256", "2bf070a73ce72c6e43c381381851607a:" . $_SERVER['HTTP_HOST'] . $newinput['api_feed_key']);
        update_option('p4wc_feed_hash', $newinput['api_feed_hash']);

        $key = substr($newinput['api_feed_key'], 0, 2);
        $key .= "**";
        $key .= substr($newinput['api_feed_key'], -2);

        $newinput['api_feed_key'] = $key;
    } elseif ( strlen($newinput['api_feed_key'] ) == 0 ) {
        // empty string. Cancel hash.
        update_option('p4wc_feed_hash', "");
    } elseif ( strpos($newinput['api_feed_key'], "*") !== false ) {
        // assume it's something like "AB**YZ"...leave it be and do nothing.
    } else {
        // assume passing invalid small key string like "pass"
        update_option('p4wc_feed_hash', "");
        $newinput['api_feed_key'] = "";
    }

    $newinput['api_feed_custom_field_slugs'] = trim( $input['api_feed_custom_field_slugs'] );
    $newinput['api_token'] = trim( $input['api_token'] );
    /*
    $newinput['api_token_visual'] = trim( $input['api_token_visual'] );

    if ( strpos($newinput['api_token_visual'], "*") === false ) {
        $newinput['api_token'] = trim( $input['api_token_visual'] );
    } else {
        $newinput['api_token'] = trim( $input['api_token'] );
    }
    */

    $newinput['channel_id'] = p4wc_ensurePositiveInteger($input['channel_id']);
    $newinput['placeholder_ids'] = trim($input['placeholder_ids']);
    $newinput['client_interface'] = trim( $input['client_interface'] );
    $newinput['unique_identifier_method'] = trim( $input['unique_identifier_method'] );
    $newinput['unique_identifier_method_custom_key'] = trim( $input['unique_identifier_method_custom_key'] );
    $newinput['crop_fill_hex_color'] = p4wc_ensureHexidecimalColor( $input['crop_fill_hex_color'] ) ? $input['crop_fill_hex_color'] : '#FFFFFF';
    if ( array_key_exists('force_square_images', $input) ) {
        $newinput['force_square_images'] = 1;
    } else {
        $newinput['force_square_images'] = 0;
    }

    $newinput['default_max_image_height'] = p4wc_ensurePositiveInteger( $input['default_max_image_height'], 800,  1000 );
    $newinput['default_max_image_width'] = p4wc_ensurePositiveInteger( $input['default_max_image_width'],800, 1000 );

    $categories = get_terms( 'product_cat', array(
        'orderby'    => 'count',
        'hide_empty' => 0
    ) );

    $label_whitelisted_category_ids = array();
    foreach ( $categories AS $category ) {
        if ( array_key_exists("label_category_id_".$category->term_id, $input ) ) {
            $label_whitelisted_category_ids[] = $category->term_id;
        }
    }
    $newinput["label_whitelisted_category_ids"] = implode(",", $label_whitelisted_category_ids);

    $api_feed_category_ids = array();
    foreach ( $categories AS $category ) {
        if ( array_key_exists("api_feed_category_id_".$category->term_id, $input ) ) {
            $api_feed_category_ids[] = $category->term_id;
        }
    }
    $newinput["api_feed_category_ids"] = implode(",", $api_feed_category_ids);

    $newinput["last_updated_at"] = date('Y-m-d H:i:s');

    return $newinput;
}

function p4wc_ensureHexidecimalColor($candidate_hex) {
    $candidate_hex = trim($candidate_hex);
    return preg_match( '/^#[a-f0-9]{6}$/i', $candidate_hex);
}

function p4wc_ensurePositiveInteger($candidate_integer, $default_value = null, $max_value = null) {
    $candidate_integer = trim( $candidate_integer );
    if ( $candidate_integer <> "" ) {
        $candidate_integer = (int)$candidate_integer;
        if ( ! ($candidate_integer > 0)){
            $candidate_integer = "";
        }
        if ( $max_value && $candidate_integer > $max_value ) {
            $candidate_integer = (int)$max_value;
        }
    }
    if ( !is_integer($candidate_integer) ) {
        $candidate_integer = $default_value;
    }
    return $candidate_integer;
}

function p4wc_api_label_section_text() {
}

function p4wc_api_feed_section_text() {
    echo '<p>The following fields define how Preferabli Labels are displayed. Preferabli provides guidance for each of these fields as part of initial integration. If you need assistance, support is available at <a href="https://support.preferabli.com/">support.preferabli.com</a>.</p>';
}

function p4wc_setting_label_whitelisted_category_ids() {
    echo "Filter labels to specific categories. If none checked, labels show for all products.<br/>";
    echo "Selecting a parent category includes all child categories automatically.<br/>";

    $options = get_option( 'p4wc_plugin_options' );

    $whitelisted_categories_string = $options["label_whitelisted_category_ids"];
    $whitelisted_category_ids = explode(",", $whitelisted_categories_string);
    $whitelisted_category_ids = array_map('trim',$whitelisted_category_ids);
    $whitelisted_category_ids = array_map('intval', $whitelisted_category_ids);

    $categories = get_terms( 'product_cat', array(
        'orderby'    => 'count',
        'hide_empty' => 0
    ) );

    foreach ( $categories AS $category ) {
        if( $category->parent == 0 ) {
            p4wc_display_parent_category($categories, $category, $whitelisted_category_ids);
        }
    }

}


function p4wc_display_parent_category($all_categories, $category, $whitelisted_category_ids, $level = 0) {

    echo '<p style="text-indent: '. esc_attr($level * 2) .'em">';
    echo "<input id='p4wc_setting_label_category_id_".esc_html($category->term_id)."' name='p4wc_plugin_options[label_category_id_".esc_html($category->term_id)."]'  type='checkbox'  ";
    if ( in_array($category->term_id, $whitelisted_category_ids ) ) {
        echo 'checked="checked"';
    }
    echo ' /> ' . esc_attr($category->name) . ' <br/>';
    echo '</p>';

    //Sub category information
    foreach( $all_categories as $subcategory ) {
        if($subcategory->parent == $category->term_id) {
            p4wc_display_parent_category($all_categories, $subcategory, $whitelisted_category_ids, $level + 1);
        }
    }
}


function p4wc_setting_api_feed_category_ids() {
    echo "Filter data feed to specific categories. If none checked, data feed includes all products.<br/>";
    echo "Selecting a parent category includes all child categories automatically.<br/>";

    $options = get_option( 'p4wc_plugin_options' );

    $categories_string = $options["api_feed_category_ids"];
    $category_ids = explode(",", $categories_string);
    $category_ids = array_map('trim',$category_ids);
    $category_ids = array_map('intval', $category_ids);

    $categories = get_terms( 'product_cat', array(
        'orderby'    => 'count',
        'hide_empty' => 0
    ) );

    foreach ( $categories AS $category ) {
        if( $category->parent == 0 ) {
            p4wc_display_api_feed_parent_category($categories, $category, $category_ids);
        }
    }

}


function p4wc_display_api_feed_parent_category($all_categories, $category, $whitelisted_category_ids, $level = 0) {

    echo '<p style="text-indent: '. esc_html($level * 2) .'em">';
    echo "<input id='p4wc_setting_api_feed_category_id_".esc_html($category->term_id)."' name='p4wc_plugin_options[api_feed_category_id_".esc_html($category->term_id)."]'  type='checkbox'  ";
    if ( in_array($category->term_id, $whitelisted_category_ids ) ) {
        echo 'checked="checked"';
    }
    echo ' /> ' . esc_html($category->name) . ' <br/>';
    echo '</p>';

    //Sub category information
    foreach( $all_categories as $subcategory ) {
        if($subcategory->parent == $category->term_id) {
            p4wc_display_api_feed_parent_category($all_categories, $subcategory, $whitelisted_category_ids, $level + 1);
        }
    }
}

function p4wc_setting_api_feed_key() {
    $options = get_option( 'p4wc_plugin_options' );
    echo "Enter pre-shared data feed key (different than \"token\"). Leaving this blank disables the data feed.<br/>";
    echo "<input id='p4wc_setting_api_feed_key' name='p4wc_plugin_options[api_feed_key]' type='text' value='". esc_attr( $options['api_feed_key'] ) . "' />";
    if ( strlen($options['api_feed_key']) == 0 ) {
        echo " (Currently Disabled)";
    }
}
function p4wc_setting_api_feed_custom_field_slugs() {
    $options = get_option( 'p4wc_plugin_options' );
    echo "(optional) Enter comma separated custom field names if they exist for a given product.<br/>";

    echo "<input id='p4wc_setting_api_feed_custom_field_slugs' name='p4wc_plugin_options[api_feed_custom_field_slugs]' type='text' value='". esc_attr( $options['api_feed_custom_field_slugs'] ) . "' />";
}
function p4wc_setting_api_token() {
    $options = get_option( 'p4wc_plugin_options' );
    echo "Enter pre-shared API token to enable labels (different than \"data feed key\"). Leaving this blank disables label usage.<br/>";
    echo "<input id='p4wc_setting_api_token' name='p4wc_plugin_options[api_token]' type='password' value='". esc_attr( $options['api_token'] ) . "' />";
}
function p4wc_setting_channel_id() {
    $options = get_option( 'p4wc_plugin_options' );
    echo "<input id='p4wc_setting_channel_id' name='p4wc_plugin_options[channel_id]' type='text' value='". esc_attr( $options['channel_id'] ) . "' />";
}
function p4wc_setting_placeholder_ids() {
    echo "(Optional) Enter the IDs of placeholder images that may be overwritten. Once overwritten, if plugin is removed, placeholders must be re-added manually. Syntax: 3,5,6,234<br/>";

    $options = get_option( 'p4wc_plugin_options' );
    echo "<input id='p4wc_setting_placeholder_ids' name='p4wc_plugin_options[placeholder_ids]' type='text' value='". esc_attr( $options['placeholder_ids'] ) . "' />";
}
function p4wc_setting_client_interface() {
    $options = get_option( 'p4wc_plugin_options' );
    echo "<input id='p4wc_setting_client_interface' name='p4wc_plugin_options[client_interface]' type='text' value='". esc_attr( $options['client_interface'] ) . "' />";
}
function p4wc_setting_crop_fill_hex_color() {
    $options = get_option( 'p4wc_plugin_options' );
    echo "<input id='p4wc_setting_crop_fill_hex_color' name='p4wc_plugin_options[crop_fill_hex_color]' type='text' value='". esc_attr( $options['crop_fill_hex_color'] ) . "' />";
}
function p4wc_setting_force_square_images() {
    $options = get_option( 'p4wc_plugin_options' );
    echo "<input id='p4wc_setting_force_square_images' name='p4wc_plugin_options[force_square_images]'  type='checkbox'  ";
    if ( array_key_exists("force_square_images",$options) && $options['force_square_images'] == 1 ) {
        echo 'checked="checked"';
    }
    echo " />";
}

function p4wc_setting_default_max_image_height() {
    $options = get_option( 'p4wc_plugin_options' );
    echo "<input id='p4wc_setting_default_max_image_height' name='p4wc_plugin_options[default_max_image_height]' type='number' value='". esc_attr( $options['default_max_image_height'] ) . "' />";
}
function p4wc_setting_default_max_image_width() {
    $options = get_option( 'p4wc_plugin_options' );
    echo "<input id='p4wc_setting_default_max_image_width' name='p4wc_plugin_options[default_max_image_width]' type='number' value='". esc_attr( $options['default_max_image_width'] ) . "' />";
}

function p4wc_setting_unique_identifier_method() {
    $options = get_option( 'p4wc_plugin_options' );
    echo '<select id="p4wc_setting_unique_identifier_method" name="p4wc_plugin_options[unique_identifier_method]">';
    if ( $options['unique_identifier_method'] == "product_id") {
        echo '<option selected="selected" value="product_id">WooCommerce Product ID</option>';
    } else {
        echo '<option value="product_id">WooCommerce Product ID</option>';
    }

    if ( $options['unique_identifier_method'] == "product_sku") {
        echo '<option selected="selected" value="product_sku">WooCommerce Product SKU</option>';
    } else {
        echo '<option value="product_sku">WooCommerce Product SKU</option>';
    }

    if ( $options['unique_identifier_method'] == "post_meta") {
        echo '<option selected="selected" value="post_meta">WordPress post_meta Field</option>';
    } else {
        echo '<option value="post_meta">WordPress post_meta Field</option>';
    }

    echo '</select>';
}

function p4wc_setting_unique_identifier_method_custom_key() {

    $options = get_option( 'p4wc_plugin_options' );
    echo "Use when selecting \"WordPress Post Meta\" above. Enter the post_meta.meta_key to utilize.<br/>";
    echo "<input id='p4wc_setting_unique_identifier_method_custom_key' name='p4wc_plugin_options[unique_identifier_method_custom_key]' type='text' value='". esc_attr( $options['unique_identifier_method_custom_key'] ) . "' />";

}
