<?php
/**
 * The sidebar containing the footer widget area.
 *
 * @package Colinear
 */

if ( ! is_active_sidebar( 'sidebar-5' ) && ! is_active_sidebar( 'sidebar-6' ) && ! is_active_sidebar( 'sidebar-7' ) ) {
	return;
}
?>

<div id="tertiary" class="widget-area-footer" role="complementary">

	<?php if ( is_active_sidebar( 'sidebar-5' ) ) : ?>
	<div class="footer-widget">
		<?php dynamic_sidebar( 'sidebar-5' ); ?>
	</div><!-- .footer-widget -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-6' ) ) : ?>
	<div class="footer-widget">
		<?php dynamic_sidebar( 'sidebar-6' ); ?>
	</div><!-- .footer-widget -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-7' ) ) : ?>
	<div class="footer-widget">
		<?php dynamic_sidebar( 'sidebar-7' ); ?>
	</div><!-- .footer-widget -->
	<?php endif; ?>

</div><!-- #tertiary -->