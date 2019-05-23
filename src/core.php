<?php
/**
 * Core setup, site hooks and filters.
 *
 * @package CPI\Core
 */
namespace CPI\Core;

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @return void
 */
function setup()
{
    $n = function ($function) {
        return __NAMESPACE__ . "\\$function";
    };
    add_action('after_setup_theme', $n('theme_setup'));
    add_action('wp_enqueue_scripts', $n('scripts'));
    add_action('wp_enqueue_scripts', $n('styles'));
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @return void
 */
function themeSetup()
{
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');


    // This theme uses wp_nav_menu() in three locations.
    register_nav_menus(
        array(
            'primary' => 'Primary Menu'
        )
    );
}


/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function scripts()
{
    wp_enqueue_script(
        'app',
        CPI_THEME_URL . '/dist/app.js',
        [],
        null,
        true
    );
}
/**
 * Enqueue styles for front-end.
 *
 * @return void
 */
function styles()
{
    wp_enqueue_style(
        'styles',
        CPI_THEME_URL . '/dist/css/app.css',
        [],
        trie
    );
}
