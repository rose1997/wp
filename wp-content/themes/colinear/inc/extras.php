<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Colinear
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function colinear_body_classes( $classes ) {
	// Adds a class to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class to blogs with a menu.
	if ( has_nav_menu( 'primary' ) ) {
		$classes[] = 'has-menu';
	}

	// Adds a class to blogs depending on the sidebar.
	if ( 'right' === get_theme_mod( 'colinear_sidebars' ) || 'right-right' === get_theme_mod( 'colinear_sidebars' ) ) {
		$classes[] = 'has-right-sidebar';
	}
	if ( 'left' === get_theme_mod( 'colinear_sidebars' ) || 'left-left' === get_theme_mod( 'colinear_sidebars' ) ) {
		$classes[] = 'has-left-sidebar';
	}
	if ( 'right-right' === get_theme_mod( 'colinear_sidebars' ) || 'left-left' === get_theme_mod( 'colinear_sidebars' ) || 'right-left' === get_theme_mod( 'colinear_sidebars' ) ) {
		$classes[] = 'has-double-sidebar';
	}
	if ( 'right-left' === get_theme_mod( 'colinear_sidebars' ) ) {
		$classes[] = 'has-right-left-sidebar';
	}
	if ( 'full-width' === get_theme_mod( 'colinear_sidebars' ) ) {
		$classes[] = 'has-no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'colinear_body_classes' );