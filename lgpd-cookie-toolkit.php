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
    $script_url = 'http://lgpd.desenvolvahub.com/script.js'; // Endereço do script externo.

    wp_enqueue_script('lgpd-cookie-toolkit-external-script', $script_url, array('jquery'), null, true);

    $settings = array(
        'privacyPolicyUrl' => get_option('lgpd_cookie_toolkit_privacy_policy_url', ''),
        'primaryColor' => get_option('lgpd_cookie_toolkit_primary_color', '#000'),
        'hideRejectButton' => get_option('lgpd_cookie_toolkit_hide_reject_button', false) ? true : false,
        'hideCustomizeButton' => get_option('lgpd_cookie_toolkit_hide_customize_button', false) ? true : false,
    );

    wp_localize_script('lgpd-cookie-toolkit-external-script', 'lgpdSettings', $settings);
}
add_action('wp_enqueue_scripts', 'lgpd_cookie_toolkit_scripts');



function lgpd_cookie_toolkit_settings_page() {
    // Verifica se o usuário tem permissões adequadas
    if (!current_user_can('manage_options')) {
        return;
    }

    // Verifica a submissão do formulário e atualiza as configurações
    if (isset($_POST['submit'])) {
        check_admin_referer('lgpd_cookie_toolkit_update_settings');

        // Atualiza as opções no banco de dados
        update_option('lgpd_cookie_toolkit_primary_color', sanitize_text_field($_POST['primary_color']));
        update_option('lgpd_cookie_toolkit_privacy_policy_url', esc_url_raw($_POST['privacy_policy_url']));
        update_option('lgpd_cookie_toolkit_hide_reject_button', isset($_POST['hide_reject_button']) ? '1' : '0');
        update_option('lgpd_cookie_toolkit_hide_customize_button', isset($_POST['hide_customize_button']) ? '1' : '0');

        // Mensagem de confirmação
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    // Formulário de configurações
    ?>
    <div class="wrap">
        <h1>LGPD Cookie Toolkit Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('lgpd_cookie_toolkit_update_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="primary_color">Primary Color</label></th>
                    <td><input type="text" id="primary_color" name="primary_color" value="<?php echo esc_attr(get_option('lgpd_cookie_toolkit_primary_color')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="privacy_policy_url">Privacy Policy URL</label></th>
                    <td><input type="url" id="privacy_policy_url" name="privacy_policy_url" value="<?php echo esc_url(get_option('lgpd_cookie_toolkit_privacy_policy_url')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Hide Reject Button</th>
                    <td><input type="checkbox" id="hide_reject_button" name="hide_reject_button" value="1" <?php checked(get_option('lgpd_cookie_toolkit_hide_reject_button'), '1'); ?> /></td>
                </tr>
                <tr>
                    <th scope="row">Hide Customize Button</th>
                    <td><input type="checkbox" id="hide_customize_button" name="hide_customize_button" value="1" <?php checked(get_option('lgpd_cookie_toolkit_hide_customize_button'), '1'); ?> /></td>
                </tr>
            </table>
            <?php submit_button('Save Changes'); ?>
        </form>
    </div>
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
    $primary_color = get_option('lgpd_cookie_toolkit_primary_color', '#13a1b5');
    // Calcula uma versão mais escura da cor primária para o hover
    $darker_primary_color = lgpd_cookie_toolkit_adjust_brightness($primary_color, -50);

    echo "<style>
    :root {
        --primary-color: $primary_color;
        --darker-primary-color: $darker_primary_color;
    }
        .btn-primary, .slider:checked, #lgpd-cookie-consent .text a {
            background-color: $primary_color;
            border-color: $primary_color;
            color: #fff; /* Cor do texto dentro do botão primário */
        }
        .btn-outline {
            border-color: $primary_color;
            color: $primary_color;
        }
        .btn-outline:hover, .btn-primary:hover {
            background-color: $darker_primary_color;
            border-color: $darker_primary_color;
            color: #fff;
        }
        .slider.round:before {
            background-color: $primary_color;
        }
        #cookieCustomizationModal .modal-header .modal-title, #cookieCustomizationModal .modal-cookies .modal-title-category {
            color: $primary_color;
        }
        #lgpd-cookie-consent .text a {
            background: none;
            border: none;
            color: var(--primary-color);
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
