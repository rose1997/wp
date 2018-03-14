/**
 * sidebar.js
 *
 * Handles resizing the content area.
 */
( function() {

	var content, sidebar;
	content = document.getElementById( 'content' );
	sidebar = document.getElementById( 'secondary' );

	if ( ! sidebar ) {
		return;
	}

	function contentMinHeight() {

		if ( window.innerWidth >= 1272 ) {
			content.style.minHeight = sidebar.offsetHeight + 24 + 'px';
		} else {
			content.removeAttribute( 'style' );
		}

	}

	contentMinHeight();

	window.addEventListener( 'resize', contentMinHeight );

} )();