<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * @package Colinear
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses colinear_header_style()
 * @uses colinear_admin_header_style()
 * @uses colinear_admin_header_image()
 */
function colinear_custom_header_setup() {
	/*
	 * Default custom headers packaged with the theme.
	 * %s is a placeholder for the theme template directory URI.
	 */
	register_default_headers( array(
		'header-image-1' => array(
			'url'           => '%s/images/header-image-1.jpg',
			'thumbnail_url' => '%s/images/header-image-1-thumb.jpg',
		),
		'header-image-2' => array(
			'url'           => '%s/images/header-image-2.jpg',
			'thumbnail_url' => '%s/images/header-image-2-thumb.jpg',
		),
		'header-image-3' => array(
			'url'           => '%s/images/header-image-3.jpg',
			'thumbnail_url' => '%s/images/header-image-3.jpg',
		),
	) );

	add_theme_support( 'custom-header', apply_filters( 'colinear_custom_header_args', array(
		'default-image'          => '%s/images/header-image-1.jpg',
		'default-text-color'     => '000000',
		'width'                  => 1188,
		'height'                 => 240,
		'flex-height'            => true,
		'wp-head-callback'       => 'colinear_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'colinear_custom_header_setup' );

if ( ! function_exists( 'colinear_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see colinear_custom_header_setup().
 */
function colinear_header_style() {
	$header_text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value.
	if ( HEADER_TEXTCOLOR == $header_text_color ) {
		return;
	}

	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == $header_text_color ) :
	?>
		.site-title,
		.site-description {
			position: absolute;
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that.
		else :
	?>
		.site-title a,
		.site-description {
			color: #<?php echo esc_attr( $header_text_color ); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // colinear_header_style