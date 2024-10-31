<div style="">
    <img style="max-width:200px; float: right; margin-top:10px; margin-right:10px; margin-bottom:10px; margin-left:10px" src="<? echo plugin_dir_url(__DIR__ . "../" ) ?>media/powered-by-preferabli.svg">
    <div style="display:inline-block; vertical-align:bottom;">
        <h1>Preferabli for WooCommerce</h1>
    </div>
</div>
<div style="clear:both"></div>
<?php
if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
 echo "<hr/>";
 echo "<h2 style='text-decoration: underline'><strong>Alert: This Plugin requires WooCommerce to function. It appears it is not active. Please activate it before continuing.</strong></h2>";
}
?>

<hr>
<form action="options.php" method="post">
    <?php
    settings_fields( 'p4wc_plugin_options' );
    do_settings_sections( 'p4wc_plugin' ); ?>
    <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
</form>
