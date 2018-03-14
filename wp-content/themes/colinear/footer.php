<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Colinear
 */

?>

	</div><!-- #content -->

	<?php get_sidebar( 'footer' ); ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
			<!-- <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'colinear' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'colinear' ), 'WordPress' ); ?></a>  -->
			<!-- <span class="sep genericon genericon-wordpress"></span>  -->
			<!-- <?php printf( esc_html__( 'Theme: %1$s by %2$s.', 'colinear' ), 'Colinear', '<a href="https://wordpress.com/themes/" rel="designer">Automattic</a>' ); ?>  -->
			<p><a href="http://www.cc.ncu.edu.tw/">&#169;  2018 - 國立中央大學電算中心Service Desk服務平台</a></p>	
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
