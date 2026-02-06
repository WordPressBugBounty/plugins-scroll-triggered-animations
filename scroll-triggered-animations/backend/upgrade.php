<?php add_action('admin_init', function() {
    if (isset($_GET['sta_install_animation_builder'])) {
        if (!current_user_can('install_plugins')) return;

        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        if (!class_exists('Silent_Installer_Skin')) {
            class Silent_Installer_Skin extends WP_Upgrader_Skin {
                public $feedback = array();
                public function feedback($string, ...$args) {
                    $this->feedback[] = sprintf($string, ...$args);
                }
            }
        }

        $slug = 'animation-builder';
        $api = plugins_api('plugin_information', array(
            'slug' => $slug,
            'fields' => array('sections' => false),
        ));

        if (!is_wp_error($api)) {
            $upgrader = new Plugin_Upgrader(new Silent_Installer_Skin());
            $result = $upgrader->install($api->download_link);

            if (!is_wp_error($result)) {
                // Activate dynamically
                $plugins = get_plugins("/$slug");
                if (!empty($plugins)) {
                    $main_file = key($plugins);
                    activate_plugin("$slug/$main_file");

                    add_action('admin_notices', function() {
                        echo '<div class="updated"><p>Animation Builder installed and activated successfully!</p></div>';
                    });
                } else {
                    add_action('admin_notices', function() {
                        echo '<div class="error"><p>Plugin installed but main file not found.</p></div>';
                    });
                }
            } else {
                $error_message = is_wp_error($result) ? $result->get_error_message() : 'Unknown error';
                add_action('admin_notices', function() use ($error_message) {
                    echo '<div class="error"><p>Installation failed: ' . esc_html($error_message) . '</p></div>';
                });
            }
        } else {
            add_action('admin_notices', function() {
                echo '<div class="error"><p>Failed to get plugin info.</p></div>';
            });
        }

        // Redirect after silent install
        wp_safe_redirect(admin_url('plugins.php'));
        exit;
    }
});

/**
 * Display admin notice with install button
 */
function sta_advertise_animation_builder() {
    $plugin_to_deactivate = 'animation-builder/animationbuilder.php';

    if (!is_plugin_active( $plugin_to_deactivate ) ) {

        add_action('admin_notices', function() {
            echo '<div class="error">';
            echo '<p><strong>Attention!</strong> Development of Scroll Triggered Animations is continuing under the new name of <a href="https://wordpress.org/plugins/animation-builder/" target="_blank">Animation Builder</a>. Migrate to the new plugin to stay secure and up to date. Your existing animations will carry over.</p>';
            echo '<p><a href="admin.php?sta_install_animation_builder=1" class="button">Update now</a></p>';
            echo '</div>';
        });
    }
}
add_action( 'admin_init', 'sta_advertise_animation_builder' );


function my_plugin_add_simple_notice( ) {
    $colspan = 4;
    if ( is_multisite() ) {
        $colspan = 5;
    }

    ?>
    <tr class="plugin-update-tr active"> 
        <td colspan="<?php echo esc_attr( $colspan ); ?>" class="plugin-update colspanchange">
            <div class="update-message notice inline notice-warning notice-alt">
                <p>
                    <?php
                    $notice_message = '<strong>MAJOR UPDATE:</strong> This plugin has been renamed is continuing under the new name of Animation Builder. Please <a href="admin.php?sta_install_animation_builder=1">migrate now</a>. <br><br>Your existing animations will be automatically transferred, but a manual check will be required after migration.';
                    echo wp_kses_post( $notice_message );
                    ?>
                </p>
            </div>
        </td>
    </tr>
    <?php
}

// Ensure the hook name is correct (using the period '.')
add_action( "after_plugin_row_scroll-triggered-animations/toaststa.php", 'my_plugin_add_simple_notice', 10, 3 );