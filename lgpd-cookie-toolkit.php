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




function lgpd_cookie_toolkit_scripts() {
    // Enfileira Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
     // Seu CSS personalizado
    wp_enqueue_style('lgpd-cookie-toolkit-style', plugins_url('/assets/css/styles.css', __FILE__));
    // Enfileira jQuery (vem com o WordPress)
    wp_enqueue_script('jquery');

    // Enfileira Bootstrap JS
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js', array('jquery'), null, true);

    wp_enqueue_script('lgpd-cookie-toolkit-script', plugin_dir_url(__FILE__) . 'assets/js/scripts.js', array('jquery'), '1.0', true);
    wp_localize_script('lgpd-cookie-toolkit-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ajax_cookie_nonce')
    ));

}
add_action('wp_enqueue_scripts', 'lgpd_cookie_toolkit_scripts');



function lgpd_cookie_toolkit_settings_page() {
    // Verificar permissões
    if (!current_user_can('manage_options')) {
        return;
    }

    // Verificar se o usuário está tentando salvar as configurações
    if (isset($_POST['update_settings'])) {
        check_admin_referer('lgpd_update_settings');

        // Salvar as novas configurações
        update_option('lgpd_cookie_toolkit_primary_color', sanitize_hex_color($_POST['lgpd_cookie_toolkit_primary_color']));
        update_option('lgpd_cookie_toolkit_privacy_policy_url', esc_url_raw($_POST['lgpd_cookie_toolkit_privacy_policy_url']));
        update_option('lgpd_cookie_toolkit_hide_reject_button', isset($_POST['lgpd_cookie_toolkit_hide_reject_button']) ? '1' : '0');
        update_option('lgpd_cookie_toolkit_hide_customize_button', isset($_POST['lgpd_cookie_toolkit_hide_customize_button']) ? '1' : '0');
        
        echo '<div id="message" class="updated fade"><p>Settings saved.</p></div>';
    }

    // HTML do formulário de configurações
    ?>
    <div class="wrap">
        <h2><?php echo __('LGPD Cookie Toolkit Settings', 'lgpd-cookie-toolkit'); ?></h2>
        <form method="post" action="">
            <?php
            wp_nonce_field('lgpd_update_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Cor Primária:</th>
                    <td><input type="text" name="lgpd_cookie_toolkit_primary_color" value="<?php echo get_option('lgpd_cookie_toolkit_primary_color'); ?>" class="my-color-field" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">URL Politica de Privacidade:</th>
                    <td><input type="text" name="lgpd_cookie_toolkit_privacy_policy_url" value="<?php echo esc_attr(get_option('lgpd_cookie_toolkit_privacy_policy_url')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Ocultar botão Rejeitar:</th>
                    <td><input type="checkbox" name="lgpd_cookie_toolkit_hide_reject_button" value="1" <?php checked(get_option('lgpd_cookie_toolkit_hide_reject_button'), '1'); ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Ocultar botão Personalizar:</th>
                    <td><input type="checkbox" name="lgpd_cookie_toolkit_hide_customize_button" value="1" <?php checked(get_option('lgpd_cookie_toolkit_hide_customize_button'), '1'); ?> /></td>
                </tr>
            </table>
            <?php submit_button('Salvar Alterações', 'primary', 'update_settings'); ?>
        </form>
    </div>
    <?php
    // Adicione o seletor de cores ao campo de cor primária
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style('wp-color-picker');
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.my-color-field').wpColorPicker();
        });
    </script>
    <?php
}

function lgpd_cookie_toolkit_include_consent_modal() {
    // Verifica se os botões devem ser ocultados
    $hide_reject_button = get_option('lgpd_cookie_toolkit_hide_reject_button') == '1';
    $hide_customize_button = get_option('lgpd_cookie_toolkit_hide_customize_button') == '1';

    // Passa as variáveis para a view, ou inclui o CSS diretamente para ocultar os botões
    echo $hide_reject_button ? '<style>#reject-cookies{display:none;}</style>' : '';
    echo $hide_customize_button ? '<style>#customize-cookies{display:none;}</style>' : '';

    include plugin_dir_path(__FILE__) . 'views/consent-modal.php';
}

add_action('wp_footer', 'lgpd_cookie_toolkit_include_consent_modal');

function lgpd_cookie_toolkit_register_settings() {
    register_setting('lgpd_cookie_toolkit_options_group', 'lgpd_cookie_toolkit_primary_color', 'sanitize_hex_color');
    register_setting('lgpd_cookie_toolkit_options_group', 'lgpd_cookie_toolkit_privacy_policy_url', 'esc_url_raw');
    register_setting('lgpd_cookie_toolkit_options_group', 'lgpd_cookie_toolkit_show_reject_button');
    register_setting('lgpd_cookie_toolkit_options_group', 'lgpd_cookie_toolkit_show_customize_button');
}
add_action('admin_init', 'lgpd_cookie_toolkit_register_settings');

function lgpd_cookie_toolkit_custom_styles() {
    // Obtem a cor primária definida pelo usuário ou usa o padrão #13a1b5 se não estiver definida
    $primary_color = get_option('lgpd_cookie_toolkit_primary_color', '#13a1b5');

    // Calcula uma versão mais escura da cor primária para o hover
    // Certifique-se de que a função lgpd_cookie_toolkit_adjust_brightness() esteja definida e capaz de ajustar a cor corretamente
    $darker_primary_color = lgpd_cookie_toolkit_adjust_brightness($primary_color, -50);

    echo "<style>
    :root {
        --primary-color: $primary_color;
        --darker-primary-color: $darker_primary_color;
    }
        .btn-primary, .slider:checked, #lgpd-cookie-consent .text a {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff; /* Cor do texto dentro do botão primário */
        }
        .btn-outline {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        .btn-outline:hover, .btn-primary:hover {
            background-color: var(--darker-primary-color);
            border-color: var(--darker-primary-color);
            color: #fff;
        }
        .slider.round:before {
            background-color: var(--primary-color);
        }
        #cookieCustomizationModal .modal-header .modal-title, #cookieCustomizationModal .modal-cookies .modal-title-category {
            color: var(--primary-color);
        }
        #lgpd-cookie-consent .text a {
            text-decoration: underline;
        }
        .btn-primary:focus, .btn-outline:focus, .btn-secondary:focus {
            outline: none;
        }
    </style>";
}
add_action('wp_head', 'lgpd_cookie_toolkit_custom_styles');

add_action('rest_api_init', function () {
    register_rest_route('lgpd/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => 'get_lgpd_settings',
    ));
});

function get_lgpd_settings() {
    return new WP_REST_Response(array(
        'privacy_policy_url' => get_option('lgpd_cookie_toolkit_privacy_policy_url'),
        'show_reject_button' => get_option('lgpd_show_reject_button', true),
        'show_customize_button' => get_option('lgpd_show_customize_button', true),
    ));
}

// Função para ajustar o brilho da cor
function lgpd_cookie_toolkit_adjust_brightness($hex, $steps) {
    // Converte hex para RGB
    $steps = max(-255, min(255, $steps));
    $hex = str_replace('#', '', $hex);
    $r = hexdec(strlen($hex) == 3 ? str_repeat(substr($hex, 0, 1), 2) : substr($hex, 0, 2));
    $g = hexdec(strlen($hex) == 3 ? str_repeat(substr($hex, 1, 1), 2) : substr($hex, 2, 2));
    $b = hexdec(strlen($hex) == 3 ? str_repeat(substr($hex, 2, 1), 2) : substr($hex, 4, 2));

    // Ajusta os valores de RGB
    $r = max(0,min(255,$r + $steps));
    $g = max(0,min(255,$g + $steps));
    $b = max(0,min(255,$b + $steps));

    $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
    $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
    $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

    return '#'.$r_hex.$g_hex.$b_hex;
}


function handle_save_cookie_preferences() {
    // Verifica se o nonce é válido para segurança
    if (!check_ajax_referer('ajax_cookie_nonce', 'security', false)) {
        wp_send_json_error('Nonce inválido.');
        return; // Encerra a execução da função
    }

    // Verifica se as preferências foram enviadas
    if (!isset($_POST['preferences'])) {
        wp_send_json_error('Preferências não fornecidas.');
        return; // Encerra a execução da função
    }

    // Processa as preferências
    $preferences = json_decode(stripslashes($_POST['preferences']), true);
    if (is_array($preferences)) {
        // Aqui você pode salvar as preferências como desejar
        update_option('user_cookie_preferences', $preferences);
        wp_send_json_success('Preferências atualizadas com sucesso.');
    } else {
        wp_send_json_error('Erro ao decodificar preferências.');
    }
}
add_action('wp_ajax_save_cookie_preferences', 'handle_save_cookie_preferences');
add_action('wp_ajax_nopriv_save_cookie_preferences', 'handle_save_cookie_preferences');

