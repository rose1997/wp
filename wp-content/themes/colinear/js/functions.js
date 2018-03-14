( function( $ ) {

	// Move Related Posts after Entry Footer.
	var relatedPosts = $( '#jp-relatedposts' );

	if ( relatedPosts.length ) {
		relatedPosts.insertAfter( '.entry-footer' )
		            .addClass( 'entry-related' );
	}

	// Make sure tables don't overflow in Entry Content.
	function tableStyle() {
		$( '.entry-content' ).find( 'table' ).each( function() {
			if ( $( this ).width() > $( this ).parent().width() ) {
				$( this ).css( 'table-layout', 'fixed' );
			}
		} );
	}

	$( window ).load( tableStyle );

	$( document ).on( 'post-load', tableStyle );

} )( jQuery );