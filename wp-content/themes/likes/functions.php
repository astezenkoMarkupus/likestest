<?php
/**
 * Theme functions.
 *
 * @package    WordPress
 * @subpackage critick
 */

const THEME_NAME = 'critick';
define( 'THEME_URI', get_template_directory_uri() );
define( 'THEME_DIR', get_template_directory() );
define( 'THEME_VERSION', mt_rand() );   // Change to static value when go live.

add_action( 'after_setup_theme', 'critick_load_theme_dependencies' );
/**
 * Theme dependencies.
 */
function critick_load_theme_dependencies(): void {
	// Register theme menus.
	register_nav_menus( [
		'header_menu' => __( 'Header Menu', 'critick' ),
		'footer_menu' => __( 'Footer Menu', 'critick' )
	] );

	// Please place all custom functions declarations in this file.
	require_once( 'theme-functions/theme-filters.php' );
	require_once( 'theme-functions/theme-functions.php' );
}

add_action( 'init', 'critick_init_theme' );
/**
 * Theme initialization.
 */
function critick_init_theme(): void {
	// Remove extra styles and default SVG tags.
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

	// Enable post thumbnails.
	add_theme_support( 'post-thumbnails' );

	// Custom images sizes.
	// add_image_size( 'full-hd', 1920, 0, 1 );
}

add_action( 'wp_enqueue_scripts', 'critick_inclusion_enqueue' );
/**
 * Enqueue styles and scripts.
 */
function critick_inclusion_enqueue(): void {
	// Remove Gutenberg styles on front-end except single posts where Gutenberg is used.
	if ( ! is_admin() && ! is_singular( 'post' ) ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'wc-blocks-style' );
	}

	// Main styles & scripts.
	wp_enqueue_style( 'main', THEME_URI . '/static/css/main.min.css', [], THEME_VERSION );
	wp_enqueue_script( 'main', THEME_URI . '/static/js/main.min.js', [], THEME_VERSION, true );

	/**
	 * Additional pages styles & scripts below:
	 */
}

