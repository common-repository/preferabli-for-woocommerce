<?php

if ( !class_exists('PreferabliCustomFields') ) {

    class PreferabliCustomFields {
        var $prefix = '_preferabli_';
        var $postTypes = array( "preferabli-label", "product" );
        var $customFields = array(
            array(
                "name"          => "label_url",
                "title"         => "Label Path",
                "description"   => "",
                "type"          => "text",
                "scope"         => array( "preferabli-label" ),
                "capability"    => "edit_posts"
            ),
            array(
                "name"          => "label_expires_at",
                "title"         => "Expires at",
                "description"   => "",
                "type"          => "text",
                "scope"         => array( "preferabli-label" ),
                "capability"    => "edit_posts"
            ),
            array(
                "name"          => "label_checked_at",
                "title"         => "Label last checked at (delete timestamp and save to force a re-check)",
                "description"   => "",
                "type"          => "text",
                "scope"         => array( "product" ),
                "capability"    => "edit_posts"
            )
        );
        function myCustomFields() { $this->__construct(); }
        function __construct() {
            add_action( 'admin_menu', array( $this, 'createCustomFields' ) );
            add_action( 'save_post', array( $this, 'saveCustomFields' ), 1, 2 );
            // Comment this line out if you want to keep default custom fields meta box
            add_action( 'do_meta_boxes', array( $this, 'removeDefaultCustomFields' ), 10, 3 );
        }
        function removeDefaultCustomFields( $type, $context, $post ) {
            foreach ( array( 'normal', 'advanced', 'side' ) as $context ) {
                foreach ( $this->postTypes as $postType ) {
                    remove_meta_box( 'postcustom', $postType, $context );
                }
            }
        }

        function createCustomFields() {
            if ( function_exists( 'add_meta_box' ) ) {
                foreach ( $this->postTypes as $postType ) {
                    add_meta_box( 'preferabli-custom-fields', 'Preferabli Label Meta Data', array( &$this, 'displayCustomFields' ), $postType, 'normal', 'default' );
                }
            }
        }

        function displayCustomFields() {
            global $post;
            ?>
            <div class="form-wrap">
                <?php
                wp_nonce_field( 'preferabli-custom-fields', 'preferabli-custom-fields_wpnonce', false, true );
                foreach ( $this->customFields as $customField ) {
                    // Check scope
                    $scope = $customField[ 'scope' ];
                    $output = false;
                    foreach ( $scope as $scopeItem ) {
                        switch ( $scopeItem ) {
                            default: {
                                if ( $post->post_type == $scopeItem )
                                    $output = true;
                                break;
                            }
                        }
                        if ( $output ) break;
                    }
                    // Check capability
                    if ( !current_user_can( $customField['capability'], $post->ID ) )
                        $output = false;
                    // Output if allowed
                    if ( $output ) { ?>
                        <div class="form-field form-required">
                            <?php
                            switch ( $customField[ 'type' ] ) {
                                case "checkbox": {
                                    // Checkbox
                                    echo '<label for="' . esc_attr($this->prefix) . esc_attr($customField[ 'name' ]) .'" style="display:inline;"><b>' . esc_attr($customField[ 'title' ]) . '</b></label>';
                                    echo '<input type="checkbox" name="' . esc_attr($this->prefix) . esc_attr($customField['name']) . '" id="' . esc_attr($this->prefix) . esc_attr($customField['name']) . '" value="yes"';
                                    if ( get_post_meta( $post->ID, $this->prefix . $customField['name'], true ) == "yes" )
                                        echo ' checked="checked"';
                                    echo '" style="width: auto;" />';
                                    break;
                                }
                                case "textarea":
                                case "wysiwyg": {
                                    // Text area
                                    echo '<label for="' . esc_html($this->prefix) . esc_html($customField[ 'name' ]) .'"><b>' . esc_html($customField[ 'title' ]) . '</b></label>';
                                    echo '<textarea name="' . esc_html($this->prefix) . esc_html($customField[ 'name' ]) . '" id="' . esc_html($this->prefix) . esc_html($customField[ 'name' ]) . '" columns="30" rows="3">' . htmlspecialchars( get_post_meta( $post->ID, esc_html($this->prefix) . esc_html($customField[ 'name' ]), true ) ) . '</textarea>';
                                    // WYSIWYG
                                    if ( $customField[ 'type' ] == "wysiwyg" ) { ?>
                                        <script type="text/javascript">
                                            jQuery( document ).ready( function() {
                                                jQuery( "<?php echo esc_html($this->prefix) . esc_html($customField[ 'name' ]); ?>" ).addClass( "mceEditor" );
                                                if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
                                                    tinyMCE.execCommand( "mceAddControl", false, "<?php echo esc_html($this->prefix) . esc_html($customField[ 'name' ]); ?>" );
                                                }
                                            });
                                        </script>
                                    <?php }
                                    break;
                                }
                                default: {
                                    // Plain text field
                                    echo '<label for="' . esc_html($this->prefix) . esc_html($customField[ 'name' ]) .'"><b>' . esc_html($customField[ 'title' ]) . '</b></label>';
                                    echo '<input type="text" name="' . esc_html($this->prefix) . esc_html($customField[ 'name' ]) . '" id="' . esc_html($this->prefix) . esc_html($customField[ 'name' ]) . '" value="' . htmlspecialchars( get_post_meta( $post->ID, esc_html($this->prefix) . esc_html($customField[ 'name' ]), true ) ) . '" />';
                                    break;
                                }
                            }
                            ?>
                            <?php if ( $customField[ 'description' ] ) echo '<p>' . esc_html($customField[ 'description' ]) . '</p>'; ?>
                        </div>
                        <?php
                    }
                } ?>
            </div>
            <?php
        }

        function saveCustomFields( $post_id, $post ) {
            if ( !isset( $_POST[ 'preferabli-custom-fields_wpnonce' ] ) || !wp_verify_nonce( sanitize_text_field($_POST[ 'preferabli-custom-fields_wpnonce' ]), 'preferabli-custom-fields' ) )
                return;
            if ( !current_user_can( 'edit_post', $post_id ) )
                return;
            if ( ! in_array( $post->post_type, $this->postTypes ) )
                return;
            foreach ( $this->customFields as $customField ) {
                if ( current_user_can( $customField['capability'], $post_id ) ) {
                    if ( isset( $_POST[ $this->prefix . $customField['name'] ] ) && trim( sanitize_text_field($_POST[ $this->prefix . $customField['name'] ] ) ) ) {
                        $value = sanitize_text_field($_POST[ $this->prefix . $customField['name'] ]);
                        // Auto-paragraphs for any WYSIWYG
                        if ( $customField['type'] == "wysiwyg" ) $value = wpautop( $value );
                        update_post_meta( $post_id, $this->prefix . $customField[ 'name' ], $value );
                    } else {
                        delete_post_meta( $post_id, $this->prefix . $customField[ 'name' ] );
                    }
                }
            }
        }
    }
}

if ( class_exists('PreferabliCustomFields') ) {
    $PreferabliCustomFields_var = new PreferabliCustomFields();
}