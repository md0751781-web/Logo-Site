/* Inner pages: artwork dropzone, portfolio filter, quick view. */
( function () {
	// ---------- Artwork dropzone ----------
	var zone = document.querySelector( '[data-dropzone]' );
	if ( zone ) {
		var input = zone.querySelector( 'input[type="file"]' );
		var status = zone.querySelector( '.dropzone__status' );
		var max = parseInt( zone.getAttribute( 'data-max' ), 10 ) || 10485760;
		var allowed = /\.(png|jpe?g|svg|pdf)$/i;

		var show = function ( msg, isError ) {
			status.textContent = msg;
			status.classList.toggle( 'is-error', !! isError );
		};

		var check = function () {
			var file = input.files && input.files[ 0 ];
			zone.classList.remove( 'has-file' );
			input.setCustomValidity( '' );
			if ( ! file ) {
				show( '' );
				return;
			}
			var error = '';
			if ( ! allowed.test( file.name ) ) {
				error = 'Unsupported file type. Please use PNG, JPG, SVG or PDF.';
			} else if ( file.size > max ) {
				error = 'This file is larger than 10 MB.';
			}
			if ( error ) {
				input.setCustomValidity( error );
				show( error, true );
				return;
			}
			zone.classList.add( 'has-file' );
			show( '✓ ' + file.name + ' (' + ( file.size / 1048576 ).toFixed( 2 ) + ' MB)' );
		};

		input.addEventListener( 'change', check );

		[ 'dragenter', 'dragover' ].forEach( function ( type ) {
			zone.addEventListener( type, function ( e ) {
				e.preventDefault();
				zone.classList.add( 'is-dragover' );
			} );
		} );
		[ 'dragleave', 'dragend', 'drop' ].forEach( function ( type ) {
			zone.addEventListener( type, function ( e ) {
				if ( 'dragleave' === type && zone.contains( e.relatedTarget ) ) {
					return;
				}
				zone.classList.remove( 'is-dragover' );
			} );
		} );
		zone.addEventListener( 'drop', function ( e ) {
			e.preventDefault();
			if ( e.dataTransfer && e.dataTransfer.files.length ) {
				input.files = e.dataTransfer.files;
				check();
			}
		} );

		// Prevent double submits while the file uploads.
		var form = zone.closest( 'form' );
		form.addEventListener( 'submit', function () {
			var btn = form.querySelector( '[type="submit"]' );
			if ( btn ) {
				btn.disabled = true;
				btn.setAttribute( 'aria-busy', 'true' );
			}
		} );
	}

	// ---------- Portfolio filter ----------
	var portfolio = document.querySelector( '[data-portfolio]' );
	if ( portfolio ) {
		var buttons = portfolio.querySelectorAll( '.pf-filter' );
		var items = portfolio.querySelectorAll( '.pf-item' );
		var counter = portfolio.querySelector( '[data-count]' );

		buttons.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var filter = btn.getAttribute( 'data-filter' );
				var shown = 0;
				buttons.forEach( function ( b ) {
					b.setAttribute( 'aria-pressed', b === btn ? 'true' : 'false' );
				} );
				items.forEach( function ( item ) {
					var cats = ( item.getAttribute( 'data-cats' ) || '' ).split( ' ' );
					var match = '*' === filter || cats.indexOf( filter ) !== -1;
					item.hidden = ! match;
					item.classList.remove( 'is-entering' );
					if ( match ) {
						shown++;
						void item.offsetWidth;
						item.classList.add( 'is-entering' );
					}
				} );
				if ( counter ) {
					counter.textContent = shown;
				}
			} );
		} );

		// ---------- Quick view ----------
		var dialog = portfolio.querySelector( '.quick-view' );
		if ( dialog && 'function' === typeof dialog.showModal ) {
			var opener = null;
			portfolio.addEventListener( 'click', function ( e ) {
				var trigger = e.target.closest( '[data-quick-view]' );
				if ( ! trigger ) {
					return;
				}
				opener = trigger;
				var img = dialog.querySelector( '.quick-view__img' );
				img.src = trigger.getAttribute( 'data-img' );
				img.alt = trigger.getAttribute( 'data-title' );
				dialog.querySelector( '.quick-view__title' ).textContent = trigger.getAttribute( 'data-title' );
				dialog.querySelector( '.quick-view__sub' ).textContent = trigger.getAttribute( 'data-subtitle' );
				dialog.querySelector( '.quick-view__price' ).textContent = trigger.getAttribute( 'data-price' );
				dialog.querySelector( '.quick-view__order' ).href = trigger.getAttribute( 'data-order' );
				dialog.showModal();
			} );
			dialog.addEventListener( 'click', function ( e ) {
				if ( e.target === dialog || e.target.closest( '[data-close]' ) ) {
					dialog.close();
				}
			} );
			dialog.addEventListener( 'close', function () {
				if ( opener ) {
					opener.focus();
				}
			} );
		} else {
			// No <dialog> support: Quick View links to the item page instead.
			portfolio.querySelectorAll( '[data-quick-view]' ).forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					var link = btn.closest( '.pf-card' ).querySelector( '.pf-card__title a' );
					window.location.href = link.href;
				} );
			} );
		}
	}
} )();
