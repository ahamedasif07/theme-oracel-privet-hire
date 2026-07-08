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
