( function () {
	var toggle = document.querySelector( '.menu-toggle' );
	var nav = document.getElementById( 'site-nav' );
	if ( ! toggle || ! nav ) {
		return;
	}

	function setOpen( open ) {
		nav.classList.toggle( 'is-open', open );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	}

	toggle.addEventListener( 'click', function () {
		setOpen( ! nav.classList.contains( 'is-open' ) );
	} );

	// Close after choosing a link (same-page anchors otherwise leave the panel open).
	nav.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( 'a' ) ) {
			setOpen( false );
		}
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key && nav.classList.contains( 'is-open' ) ) {
			setOpen( false );
			toggle.focus();
		}
	} );

	document.addEventListener( 'click', function ( e ) {
		if ( nav.classList.contains( 'is-open' ) && ! nav.contains( e.target ) && ! toggle.contains( e.target ) ) {
			setOpen( false );
		}
	} );

	// Reset when resizing back to the desktop layout.
	window.matchMedia( '(min-width: 1200px)' ).addEventListener( 'change', function ( mq ) {
		if ( mq.matches ) {
			setOpen( false );
		}
	} );
} )();

/* Shop: the category panel is always open on desktop and collapsed by default on phones. */
( function () {
	var cats = document.querySelector( '.shop-cats' );
	if ( ! cats || ! window.matchMedia ) {
		return;
	}
	var desktop = window.matchMedia( '(min-width: 900px)' );
	var sync = function () {
		cats.open = desktop.matches;
	};
	sync();
	desktop.addEventListener( 'change', sync );
	// A closed panel on desktop would hide the navigation, so the heading only toggles on small screens.
	cats.querySelector( 'summary' ).addEventListener( 'click', function ( e ) {
		if ( desktop.matches ) {
			e.preventDefault();
		}
	} );
} )();
