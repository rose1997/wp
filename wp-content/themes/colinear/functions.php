<?php
/**
 * Colinear functions and definitions
 *
 * @package Colinear
 */

if ( ! function_exists( 'colinear_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function colinear_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Colinear, use a find and replace
	 * to change 'colinear' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'colinear', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 863, 0, true );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'colinear' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'colinear_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // colinear_setup
add_action( 'after_setup_theme', 'colinear_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function colinear_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'colinear_content_width', 863 );
}
add_action( 'after_setup_theme', 'colinear_content_width', 0 );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function colinear_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 1', 'colinear' ),
		'id'            => 'sidebar-5',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 2', 'colinear' ),
		'id'            => 'sidebar-6',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 3', 'colinear' ),
		'id'            => 'sidebar-7',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	if ( 'full-width' === get_theme_mod( 'colinear_sidebars' ) ) {
		return;
	}
	if ( 'left' !== get_theme_mod( 'colinear_sidebars' ) && 'left-left' !== get_theme_mod( 'colinear_sidebars' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Sidebar Right', 'colinear' ),
			'id'            => 'sidebar-1',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
	if ( 'right' !== get_theme_mod( 'colinear_sidebars' ) && 'right-right' !== get_theme_mod( 'colinear_sidebars' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Sidebar Left', 'colinear' ),
			'id'            => 'sidebar-2',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
	if ( 'right-right' === get_theme_mod( 'colinear_sidebars' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Sidebar Right 2', 'colinear' ),
			'id'            => 'sidebar-3',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
	if ( 'left-left' === get_theme_mod( 'colinear_sidebars' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Sidebar Left 2', 'colinear' ),
			'id'            => 'sidebar-3',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
	if ( 'right-right' === get_theme_mod( 'colinear_sidebars' ) || 'left-left' === get_theme_mod( 'colinear_sidebars' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Featured Sidebar', 'colinear' ),
			'id'            => 'sidebar-4',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
}
add_action( 'widgets_init', 'colinear_widgets_init' );

/**
 * Register Open Sans and Lora fonts.
 *
 * @return string
 */
function colinear_open_sans_lora_fonts_url() {
	$fonts_url = '';

	/* translators: If there are characters in your language that are not supported
	 * by Open Sans, translate this to 'off'. Do not translate into your own language.
	 */
	$open_sans = esc_html_x( 'on', 'Open Sans font: on or off', 'colinear' );

	/* translators: If there are characters in your language that are not supported
	 * by Lora, translate this to 'off'. Do not translate into your own language.
	 */
	$lora = esc_html_x( 'on', 'Lora font: on or off', 'colinear' );

	if ( 'off' !== $open_sans || 'off' !== $lora ) {
		$font_families = array();

		if ( 'off' !== $open_sans ) {
			$font_families[] = 'Open Sans:400,700,400italic,700italic';
		}

		if ( 'off' !== $lora ) {
			$font_families[] = 'Lora:400,700,400italic,700italic';
		}

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "https://fonts.googleapis.com/css" );
	}

	return $fonts_url;
}

/**
 * Register Inconsolata font.
 *
 * @return string
 */
function colinear_inconsolata_fonts_url() {
	$fonts_url = '';

	/* translators: If there are characters in your language that are not supported
	 * by Inconsolata, translate this to 'off'. Do not translate into your own language.
	 */
	$inconsolata = esc_html_x( 'on', 'Inconsolata font: on or off', 'colinear' );

	if ( 'off' !== $inconsolata ) {
		$query_args = array(
			'family' => urlencode( 'Inconsolata:400, 700' ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "https://fonts.googleapis.com/css" );
	}

	return $fonts_url;
}

/**
 * Enqueue scripts and styles.
 */
function colinear_scripts() {
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.3.1' );

	wp_enqueue_style( 'colinear-open-sans-lora', colinear_open_sans_lora_fonts_url() );

	wp_enqueue_style( 'colinear-inconsolata', colinear_inconsolata_fonts_url() );

	wp_enqueue_style( 'colinear-style', get_stylesheet_uri() );

	wp_enqueue_script( 'colinear-navigation', get_template_directory_uri() . '/js/navigation.js', array( 'jquery' ), '20150729', true );

	wp_enqueue_script( 'colinear-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	wp_enqueue_script( 'colinear-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20150723', true );

	if ( 'right-left' === get_theme_mod( 'colinear_sidebars' ) ) {
		wp_enqueue_script( 'colinear-sidebar', get_template_directory_uri() . '/js/sidebar.js', array(), '20150806', true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_localize_script( 'colinear-navigation', 'screenReaderText', array(
		'expand'   => '<span class="screen-reader-text">' . esc_html__( 'expand child menu', 'colinear' ) . '</span>',
		'collapse' => '<span class="screen-reader-text">' . esc_html__( 'collapse child menu', 'colinear' ) . '</span>',
	) );
        global $wp_scripts;
        wp_enqueue_style( 'bootstrap_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css' );
        wp_enqueue_script( 'bootstrap_js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
	wp_enqueue_script( 'my_custom_js', get_template_directory_uri() . '/js/scripts.js');
}
add_action( 'wp_enqueue_scripts', 'colinear_scripts' );
add_action('wp_enqueue_scripts','theme_js');
/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';


/**
 * Load plugin enhancement file to display admin notices.
 */
require get_template_directory() . '/inc/plugin-enhancements.php';
