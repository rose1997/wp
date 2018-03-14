<?php
/**
 * Jetpack Compatibility File
 * See: https://jetpack.me/
 *
 * @package Colinear
 */

function colinear_jetpack_setup() {
	/**
	 * Add theme support for Infinite Scroll.
	 * See: https://jetpack.me/support/infinite-scroll/
	 */
	add_theme_support( 'infinite-scroll', array(
		'container'      => 'main',
		'render'         => 'colinear_infinite_scroll_render',
		'footer'         => 'page',
		'footer_widgets' => array(
			'sidebar-5',
			'sidebar-6',
			'sidebar-7',
		),
	) );

	/**
	 * Add theme support for Responsive Videos.
	 */
	add_theme_support( 'jetpack-responsive-videos' );

	/**
	 * Add theme support for Logo upload.
	 */
	add_image_size( 'colinear-logo', 480, 108 );
	add_theme_support( 'site-logo', array( 'size' => 'colinear-logo' ) );
}
add_action( 'after_setup_theme', 'colinear_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function colinear_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content', get_post_format() );
	}
}

/**
 * Return early if Site Logo is not available.
 */
function colinear_the_site_logo() {
	if ( ! function_exists( 'jetpack_the_site_logo' ) ) {
		return;
	} else {
		jetpack_the_site_logo();
	}
}

/**
 * Overwritte default gallery widget content width.
 */
function colinear_gallery_widget_content_width( $width ) {
	return 863;
}
add_filter( 'gallery_widget_content_width', 'colinear_gallery_widget_content_width');