<?php

/**
 * Oracle Private Hire theme functions.
 */

if (! defined('ABSPATH')) {
    exit; // No direct access.
}

define('ORACLE_THEME_VERSION', '1.0.0');

/**
 * Theme setup.
 */
function oracle_theme_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo');
    add_theme_support('responsive-embeds');

    register_nav_menus([
        'primary' => __('Primary Menu', 'oracle'),
        'footer'  => __('Footer Menu', 'oracle'),
    ]);
}
add_action('after_setup_theme', 'oracle_theme_setup');

/**
 * Enqueue styles and scripts.
 * The design is built with the Tailwind CDN build (loaded in header.php)
 * plus a small custom stylesheet at assets/css/style.css, and a small
 * script at assets/js/main.js for the mobile menu / booking wizard.
 */
function oracle_theme_assets()
{
    // Root style.css - required by WordPress, kept enqueued for good practice.
    wp_enqueue_style('oracle-theme-style', get_stylesheet_uri(), [], ORACLE_THEME_VERSION);

    // Custom design stylesheet.
    wp_enqueue_style(
        'oracle-custom-style',
        get_template_directory_uri() . '/assets/css/style.css',
        ['oracle-theme-style'],
        ORACLE_THEME_VERSION
    );

    // Site interactions (mobile menu, booking wizard, header scroll state).
    wp_enqueue_script(
        'oracle-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        ORACLE_THEME_VERSION,
        true
    );
    // Site interactions navigation.
    wp_enqueue_script(
        'oracle-nav',
        get_template_directory_uri() . '/assets/js/nav.js',
        [],
        ORACLE_THEME_VERSION,
        true
    );
    // Site interactions booking.
    wp_enqueue_script(
        'oracle-booking',
        get_template_directory_uri() . '/assets/js/booking.js',
        [],
        ORACLE_THEME_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'oracle_theme_assets');

/**
 * Make sure a static "Home" page routes to the theme's front-page.php
 * design even before the site owner has configured Settings > Reading.
 * This is a soft fallback only; for full control, set a static front
 * page in wp-admin under Settings > Reading.
 */
function oracle_theme_editor_style()
{
    add_editor_style();
}
add_action('admin_init', 'oracle_theme_editor_style');


// contact form post type 

function register_contact_messages_cpt()
{
    register_post_type('contact_message', [
        'labels' => [
            'name' => 'Contact Messages',
            'singular_name' => 'Message',
            'menu_name' => 'Contact Messages',
            'all_items' => 'All Messages',
            'edit_item' => 'View Message',
            'view_item' => 'View Message',
            'search_items' => 'Search Messages',
            'not_found' => 'No contact messages found',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-email-alt',
        'menu_position' => 25,
        'capability_type' => 'post',
        'supports' => ['title', 'editor', 'custom-fields'],
        'capabilities' => ['create_posts' => 'do_not_allow'],
        'map_meta_cap' => true,
    ]);
}
add_action('init', 'register_contact_messages_cpt');

function oracle_contact_message_columns($columns)
{
    return [
        'cb' => $columns['cb'],
        'title' => 'Message',
        'contact_phone' => 'Phone',
        'contact_email' => 'Email',
        'contact_subject' => 'Subject',
        'date' => $columns['date'],
    ];
}
add_filter('manage_contact_message_posts_columns', 'oracle_contact_message_columns');

function oracle_contact_message_column_content($column, $post_id)
{
    if (in_array($column, ['contact_phone', 'contact_email', 'contact_subject'], true)) {
        echo esc_html(get_post_meta($post_id, $column, true));
    }
}
add_action('manage_contact_message_posts_custom_column', 'oracle_contact_message_column_content', 10, 2);

function oracle_handle_contact_form_submit()
{
    if (! isset($_POST['oracle_contact_nonce']) || ! wp_verify_nonce($_POST['oracle_contact_nonce'], 'oracle_contact_form')) {
        wp_safe_redirect(add_query_arg('contact_error', 'security', wp_get_referer() ?: home_url('/contact/')));
        exit;
    }

    $name = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $subject = sanitize_text_field($_POST['subject'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    $subject = $subject ?: 'New Inquiry';

    $redirectUrl = remove_query_arg(['contact_sent', 'contact_error'], wp_get_referer() ?: home_url('/contact/'));

    $contactMessageId = wp_insert_post([
        'post_title' => 'Message from: ' . ($name ?: 'Website visitor'),
        'post_content' => "Name: $name\nPhone: $phone\nEmail: $email\nSubject: $subject\n\nMessage:\n$message",
        'post_status' => 'publish',
        'post_type' => 'contact_message',
        'meta_input' => [
            'contact_name' => $name,
            'contact_phone' => $phone,
            'contact_email' => $email,
            'contact_subject' => $subject,
        ],
    ], true);

    if (is_wp_error($contactMessageId)) {
        wp_safe_redirect(add_query_arg('contact_error', 'save', $redirectUrl));
        exit;
    }

    wp_mail(
        get_option('admin_email'),
        '[Contact Form] ' . $subject,
        "Name: $name\nPhone: $phone\nEmail: $email\nMessage: $message"
    );

    wp_safe_redirect(add_query_arg('contact_sent', '1', $redirectUrl));
    exit;
}
add_action('admin_post_oracle_contact_submit', 'oracle_handle_contact_form_submit');
add_action('admin_post_nopriv_oracle_contact_submit', 'oracle_handle_contact_form_submit');
