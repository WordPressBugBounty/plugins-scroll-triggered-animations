<?php $option = get_option( 'toast_sta_settings' ); ?>
<?php if($option['disable_all'] !== 'on'): ?>
<?php include dirname(__FILE__). '/js.php'; ?>
<?php function sta_enqueue_frontend_styles(){
	wp_enqueue_script('jquery');
	wp_enqueue_style('sta_css', plugins_url('animations.css', __FILE__ ), array(), null, false);
}
add_action('wp_enqueue_scripts', 'sta_enqueue_frontend_styles');

function sta_enqueue_custom_css(){
    $option = get_option('toast_sta_settings');
    $css = isset($option['toast_sta_animations_css']) ? $option['toast_sta_animations_css'] : '';
    $sanitized_css = wp_strip_all_tags($css); // Remove any HTML/JS
    echo "<style>" . esc_html($sanitized_css) . "</style>";
}
add_action('wp_head', 'sta_enqueue_custom_css');
endif; ?>