<?php
/**
 * Colinear Theme Customizer
 *
 * @package Colinear
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function colinear_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	$wp_customize->add_section( 'colinear_theme_options', array(
		'title'    => esc_html__( 'Theme Options', 'colinear' ),
		'priority' => 130,
	) );

	/* Sidebars */
	$wp_customize->add_setting( 'colinear_sidebars', array(
		'default'           => 'right',
		'sanitize_callback' => 'colinear_sanitize_sidebars',
	) );
	$wp_customize->add_control( 'colinear_sidebars', array(
		'label'             => esc_html__( 'Sidebars', 'colinear' ),
		'section'           => 'colinear_theme_options',
		'priority'          => 1,
		'type'              => 'radio',
		'choices'           => array(
			'right'       => esc_html__( '1 Sidebar Right', 'colinear' ),
			'left'        => esc_html__( '1 Sidebar Left', 'colinear' ),
			'right-right' => esc_html__( '2 Sidebars Right', 'colinear' ),
			'left-left'   => esc_html__( '2 Sidebars Left', 'colinear' ),
			'right-left'  => esc_html__( '1 Sidebar Right & 1 Sidebar Left', 'colinear' ),
			'full-width'  => esc_html__( 'No Sidebar', 'colinear' ),
		),
	) );
}
add_action( 'customize_register', 'colinear_customize_register' );

/**
 * Sanitize the Sidebars value.
 *
 * @param string $sidebars.
 * @return string.
 */
function colinear_sanitize_sidebars( $sidebars ) {
	if ( ! in_array( $sidebars, array( 'right', 'left', 'right-right', 'left-left', 'right-left', 'full-width' ) ) ) {
		$sidebars = 'right';
	}
	return $sidebars;
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function colinear_customize_preview_js() {
	wp_enqueue_script( 'colinear_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'colinear_customize_preview_js' );