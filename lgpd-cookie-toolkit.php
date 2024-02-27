<?php
/**
 * Plugin Name: LGPD Cookie Toolkit by Desenvolva
 * Description: Gerenciamento de cookies e conformidade com a LGPD.
 * Version: 1.0
 * Author: Desenvolva
 */

if (!defined('ABSPATH')) {
    exit;
}


function lgpd_cookie_toolkit_init() {
    load_plugin_textdomain('lgpd-cookie-toolkit', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('init', 'lgpd_cookie_toolkit_init');

function lgpd_cookie_toolkit_add_admin_menu() {
    add_options_page(
        'LGPD Cookie Toolkit Settings',
        'LGPD Cookie Toolkit',
        'manage_options',
        'lgpd_cookie_toolkit',
        'lgpd_cookie_toolkit_settings_page'
    );
}

add_action('admin_menu', 'lgpd_cookie_toolkit_add_admin_menu');

function lgpd_cookie_toolkit_set_cookie($name, $value, $expire) {
    setcookie($name, $value, $expire, '/');
}

function lgpd_cookie_toolkit_get_cookie($name) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
}

function lgpd_cookie_toolkit_delete_cookie($name) {
    setcookie($name, '', time() - 3600, '/');
}

function lgpd_cookie_toolkit_ajax_handler() {
    // Verifique o nonce e outros dados da requisição
    // Defina ou atualize os cookies conforme necessário
    wp_send_json_success('Preferências salvas com sucesso.');
}

add_action('wp_ajax_lgpd_cookie_toolkit_save_preferences', 'lgpd_cookie_toolkit_ajax_handler');
add_action('wp_ajax_nopriv_lgpd_cookie_toolkit_save_preferences', 'lgpd_cookie_toolkit_ajax_handler');




function lgpd_cookie_toolkit_enqueue_scripts() {
    wp_enqueue_style('lgpd-cookie-toolkit-style', plugins_url('/assets/css/styles.css', __FILE__));
    wp_enqueue_script('lgpd-cookie-toolkit-script', plugins_url('/assets/js/scripts.js', __FILE__), array('jquery'), null, true);

    // Mova wp_localize_script para dentro desta função
    wp_localize_script('lgpd-cookie-toolkit-script', 'lgpdCookieToolkitAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        // Garanta que wp_create_nonce seja chamado aqui, dentro do hook adequado
        'nonce' => wp_create_nonce('lgpd_cookie_toolkit_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'lgpd_cookie_toolkit_enqueue_scripts');


function lgpd_cookie_toolkit_settings_page() {
    ?>
    <div class="wrap">
        <h2><?php _e('LGPD Cookie Toolkit Settings', 'lgpd-cookie-toolkit'); ?></h2>
        <!-- Formulário de configurações aqui -->
    </div>
    <?php
}



