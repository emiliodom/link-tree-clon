<?php
/*
Plugin Name: Simple Linktree
Description: Una plugin simple estilo Linktree
Version: 1.0
Author: Emilio Dominguez
*/

// Salir si se accede directamente
if (!defined('ABSPATH')) {
    exit;
}

// Añadir menu de administración
add_action('admin_menu', 'simple_linktree_add_admin_menu');

// Registro de ajustes
add_action('admin_init', 'simple_linktree_settings_init');

// Añadir el shortcode
add_shortcode('simple_linktree', 'simple_linktree_shortcode');

function my_theme_enqueue_styles() {
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'my-theme-style', $plugin_url . 'style.css' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

// Menu de administración
function simple_linktree_add_admin_menu() {
    add_menu_page(
        'Simple Linktree',
        'Simple Linktree',
        'manage_options',
        'simple_linktree',
        'simple_linktree_options_page',
        'dashicons-list-view',
        30
    );
}

// inicializar ajustes
function simple_linktree_settings_init() {
    register_setting('simple_linktree', 'simple_linktree_settings');

    add_settings_section(
        'simple_linktree_section',
        'Linktree Settings',
        'simple_linktree_section_callback',
        'simple_linktree'
    );

    $fields = array(
        'facebook' => 'URL Facebook ',
        'twitter' => 'URL Twitter (X) ',
        'linkedin' => 'URL LinkedIn ',
        'github' => 'URL GitHub ',
        'website' => 'URL Sitio Personal',
        'promo_code' => 'Código Promocional Promo',
        'phone' => 'Número de Teléfono'
    );

    foreach ($fields as $field_id => $field_title) {
        add_settings_field(
            'simple_linktree_' . $field_id,
            $field_title,
            'simple_linktree_field_callback',
            'simple_linktree',
            'simple_linktree_section',
            array('field' => $field_id)
        );
    }
}

// Llamar a la sección
function simple_linktree_section_callback() {
    echo '<h2>Para utilizar esta plugin debes incluir el shortcode [simple_linktree] en tu página o post.</h2>';
    echo 'Ingresa tu información abajo:';
}

// Llamar a los campos
function simple_linktree_field_callback($args) {
    $options = get_option('simple_linktree_settings');
    $field = $args['field'];
    $value = isset($options[$field]) ? $options[$field] : '';
    echo "<input type='text' name='simple_linktree_settings[$field]' value='$value' class='regular-text'>";
}

// Página opciones
function simple_linktree_options_page() {
    ?>
    <div class="wrap">
        <h1>Ajustes Simple Linktree</h1>
        <form action='options.php' method='post'>
            <?php
            settings_fields('simple_linktree');
            do_settings_sections('simple_linktree');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Función del Shortcode
function simple_linktree_shortcode() {
    $options = get_option('simple_linktree_settings');
    $output = '<div class="simple-linktree">';
    
    $fields = array(
        'facebook' => 'Facebook',
        'twitter' => 'Twitter (X)',
        'linkedin' => 'LinkedIn',
        'github' => 'GitHub',
        'website' => 'Sitio Personal',
        'promo_code' => 'Promo Code',
        'phone' => 'Número de Teléfono'
    );

    foreach ($fields as $field => $label) {
        if (!empty($options[$field])) {
            if (in_array($field, array('facebook', 'twitter', 'linkedin', 'github', 'website'))) {
                $output .= "<a class='boton-link' href='{$options[$field]}' target='_blank'>$label</a><br>";
            } else {
                $output .= "<p><strong>$label:</strong> {$options[$field]}</p>";
            }
        }
    }

    $output .= '</div>';
    return $output;
}
