<?php
function class26_enqueue_styles() {
    wp_enqueue_style('class26-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'class26_enqueue_styles');
