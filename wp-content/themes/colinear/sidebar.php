<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Colinear
 */

if ( 'full-width' === get_theme_mod( 'colinear_sidebars' ) || ( ! is_active_sidebar( 'sidebar-1' ) ) && ( ! is_active_sidebar( 'sidebar-2' ) ) && ( ! is_active_sidebar( 'sidebar-3' ) ) && ( ! is_active_sidebar( 'sidebar-4' ) ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">

	<?php if ( ( 'right-right' === get_theme_mod( 'colinear_sidebars' ) || 'left-left' === get_theme_mod( 'colinear_sidebars' ) ) && is_active_sidebar( 'sidebar-4' ) ) : ?>
	<div class="featured-widget-area">
		<?php dynamic_sidebar( 'sidebar-4' ); ?>
	</div><!-- .featured-widget-area -->
	<?php endif; ?>

	<?php if ( 'left-left' === get_theme_mod( 'colinear_sidebars' ) && is_active_sidebar( 'sidebar-3' ) ) : ?>
	<div class="left-sidebar-2">
		<?php dynamic_sidebar( 'sidebar-3' ); ?>
	</div><!-- .left-sidebar-2 -->
	<?php endif; ?>

	<?php if ( ( 'right' !== get_theme_mod( 'colinear_sidebars' ) && 'right-right' !== get_theme_mod( 'colinear_sidebars' ) ) && is_active_sidebar( 'sidebar-2' ) ) : ?>
	<div class="left-sidebar">
		<?php dynamic_sidebar( 'sidebar-2' ); ?>
	</div><!-- .left-sidebar -->
	<?php endif; ?>

	<?php if ( ( 'left' !== get_theme_mod( 'colinear_sidebars' ) && 'left-left' !== get_theme_mod( 'colinear_sidebars' ) ) && is_active_sidebar( 'sidebar-1' ) ) : ?>
	<div class="right-sidebar">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div><!-- .right-sidebar -->
	<?php endif; ?>

	<?php if ( 'right-right' === get_theme_mod( 'colinear_sidebars' ) && is_active_sidebar( 'sidebar-3' ) ) : ?>
	<div class="right-sidebar-2">
		<?php dynamic_sidebar( 'sidebar-3' ); ?>
	</div><!-- .right-sidebar-2 -->
	<?php endif; ?>

</div><!-- #secondary -->