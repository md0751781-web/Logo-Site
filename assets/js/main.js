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

/* Hero slider: crossfade, auto-play (paused on hover/focus and for reduced-motion users), dots, swipe. */
( function () {
	var root = document.querySelector( '[data-hero-slider]' );
	if ( ! root ) {
		return;
	}
	var slides = root.querySelectorAll( '.hero-slide' );
	var dots = root.querySelectorAll( '.hero-slider__dot' );
	if ( slides.length < 2 ) {
		return;
	}

	var index = 0;
	var timer = null;
	var hovering = false;
	var keyboardFocus = false;
	var seconds = parseInt( root.getAttribute( 'data-interval' ), 10 );
	var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var autoplay = seconds > 0 && ! reduce;

	function show( n ) {
		index = ( n + slides.length ) % slides.length;
		slides.forEach( function ( slide, i ) {
			var active = i === index;
			slide.classList.toggle( 'is-active', active );
			slide.setAttribute( 'aria-hidden', active ? 'false' : 'true' );
		} );
		dots.forEach( function ( dot, i ) {
			if ( i === index ) {
				dot.setAttribute( 'aria-current', 'true' );
			} else {
				dot.removeAttribute( 'aria-current' );
			}
		} );
	}

	function stop() {
		if ( timer ) {
			window.clearInterval( timer );
			timer = null;
		}
	}

	// Auto-play only while the pointer is away, keyboard focus is elsewhere and the tab is visible.
	function start() {
		stop();
		if ( autoplay && ! hovering && ! keyboardFocus && ! document.hidden ) {
			timer = window.setInterval( function () {
				show( index + 1 );
			}, seconds * 1000 );
		}
	}

	dots.forEach( function ( dot ) {
		dot.addEventListener( 'click', function () {
			show( parseInt( dot.getAttribute( 'data-slide' ), 10 ) );
			start();
		} );
	} );

	root.addEventListener( 'mouseenter', function () {
		hovering = true;
		start();
	} );
	root.addEventListener( 'mouseleave', function () {
		hovering = false;
		start();
	} );
	root.addEventListener( 'focusin', function ( e ) {
		// A mouse click also focuses a button; only keyboard focus should pause the slider.
		keyboardFocus = !! ( e.target.matches && e.target.matches( ':focus-visible' ) );
		start();
	} );
	root.addEventListener( 'focusout', function () {
		keyboardFocus = false;
		start();
	} );
	document.addEventListener( 'visibilitychange', start );

	// Swipe on touch screens.
	var startX = null;
	root.addEventListener( 'touchstart', function ( e ) {
		startX = e.changedTouches[ 0 ].clientX;
	}, { passive: true } );
	root.addEventListener( 'touchend', function ( e ) {
		if ( null === startX ) {
			return;
		}
		var dx = e.changedTouches[ 0 ].clientX - startX;
		startX = null;
		if ( Math.abs( dx ) > 40 ) {
			show( index + ( dx < 0 ? 1 : -1 ) );
			start();
		}
	}, { passive: true } );

	start();
} )();
