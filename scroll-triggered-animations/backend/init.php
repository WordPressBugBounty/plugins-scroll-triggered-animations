<?php 
include dirname(__FILE__) . '/page.php';
include dirname(__FILE__) . '/hooks-free.php';

function toast_sta_menu(){ 
	add_menu_page('Scroll Triggered Animations', 'Scroll Triggered Animations', 'manage_options', 'toast_sta_items', 'toast_sta_options_page', 'dashicons-lightbulb');
}
add_action( 'admin_menu', 'toast_sta_menu' );

function toast_sta_backend_scripts($hook){
	if($hook == 'toplevel_page_toast_sta_items') {
		wp_enqueue_style( 'toast_sta_backend_css', plugin_dir_url( __FILE__ ) . 'style.css', array(), 'null', false );
		wp_enqueue_script( 'toast_sta_backend_js', plugin_dir_url( __FILE__ ) . 'scripts.js', array(), 'null', false );
		wp_enqueue_style( 'sta_animations_css', plugin_dir_url( __FILE__ ) . '../frontend/animations.css');

        wp_localize_script('toast_sta_backend_js', 'sta_ajax_data', array(
        'nonce'    => wp_create_nonce('update_sta_options_nonce')
    ));
	}
}
add_action('admin_enqueue_scripts', 'toast_sta_backend_scripts');

/*
 * AJAX function which runs when an option is updated
 */
function sta_update_options() {
    // ✅ Nonce verification
    check_ajax_referer('update_sta_options_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized access');
    }

    if (!isset($_POST['option'], $_POST['value'])) {
        wp_send_json_error('Missing parameters');
    }

    $option = sanitize_text_field($_POST['option']);
    $value = sanitize_text_field($_POST['value']);

    if ($value === 'off' || $value === 'false') {
        $value = 'off';
    } elseif ($value === 'checked' || $value === 'true') {
        $value = 'on';
    }

    $sta_options = get_option('toast_sta_settings', array());
    if (!is_array($sta_options)) {
        $sta_options = array();
    }

    $sta_options[$option] = sanitize_text_field($value);
    update_option('toast_sta_settings', $sta_options);

    wp_send_json_success('Option updated');
}
add_action('wp_ajax_sta_update_options', 'sta_update_options');