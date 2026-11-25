( function () {
	'use strict';

	const FOCUSABLE =
		'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), iframe, [tabindex]:not([tabindex="-1"])';

	let current = null;
	let previousFocus = null;

	function open( popup ) {
		if ( current ) {
			return;
		}
		current = popup;
		previousFocus = document.activeElement;
		popup.hidden = false;
		document.documentElement.classList.add( 'mrzpop-open' );
		popup.querySelector( '.mrzpop__dialog' ).focus();
	}

	function close() {
		if ( ! current ) {
			return;
		}
		current.hidden = true;
		current = null;
		document.documentElement.classList.remove( 'mrzpop-open' );
		if ( previousFocus && previousFocus.isConnected ) {
			previousFocus.focus( { preventScroll: true } );
		}
	}

	function trapFocus( event ) {
		const dialog = current.querySelector( '.mrzpop__dialog' );
		// Le focus peut être ailleurs, sur le bandeau de cookies par exemple.
		if ( ! dialog.contains( document.activeElement ) ) {
			return;
		}
		const items = Array.from( dialog.querySelectorAll( FOCUSABLE ) ).filter( function ( item ) {
			return item.getClientRects().length > 0;
		} );
		const first = items[ 0 ];
		const last = items[ items.length - 1 ];
		const active = document.activeElement;

		if ( event.shiftKey && ( active === first || active === dialog ) ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && active === last ) {
			event.preventDefault();
			first.focus();
		}
	}

	document.addEventListener( 'keydown', function ( event ) {
		if ( ! current ) {
			return;
		}
		if ( 'Escape' === event.key ) {
			close();
		} else if ( 'Tab' === event.key ) {
			trapFocus( event );
		}
	} );

	document.querySelectorAll( '.mrzpop' ).forEach( function ( popup ) {
		let pressedOnOverlay = false;

		// Hors de tout conteneur du thème, qui pourrait décaler un élément fixe.
		document.body.appendChild( popup );

		// Une sélection de texte relâchée sur l'overlay ne ferme pas la pop-up.
		popup.addEventListener( 'mousedown', function ( event ) {
			pressedOnOverlay = event.target === popup;
		} );
		popup.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '.mrzpop__close' ) || ( event.target === popup && pressedOnOverlay ) ) {
				close();
			}
		} );
	} );

	const preview = document.querySelector( '[data-mrzpop-preview]' );
	const popups = Array.from( document.querySelectorAll( '.mrzpop' ) );
	const DAY = 86400000;
	const REOPEN_KEY = 'mrzpop_reopen';
	let autoOpened = false;
	let submittedAt = 0;

	// Le stockage peut être bloqué : la pop-up fonctionne alors sans mémoire.
	function read( type, key ) {
		try {
			return window[ type ].getItem( key );
		} catch ( error ) {
			return null;
		}
	}

	function write( type, key, value ) {
		if ( preview ) {
			return;
		}
		try {
			window[ type ].setItem( key, value );
		} catch ( error ) {}
	}

	function key( name, popup ) {
		return 'mrzpop_' + name + '_' + popup.id.replace( 'mrzpop-', '' );
	}

	function show( popup ) {
		open( popup );
		write( 'localStorage', key( 'seen', popup ), Date.now() );
		write( 'sessionStorage', key( 'seen', popup ), 1 );
	}

	// Dates en secondes, 0 sans limite.
	function isInDates( popup ) {
		const now = Date.now() / 1000;
		const end = Number( popup.dataset.mrzpopEnd );

		return now >= Number( popup.dataset.mrzpopStart ) && ( ! end || now < end );
	}

	function isHiddenOnMobile( popup ) {
		return '1' === popup.dataset.mrzpopHideMobile && window.matchMedia( '(max-width: 767.98px)' ).matches;
	}

	function isFrequencyOk( popup ) {
		const frequency = popup.dataset.mrzpopFrequency;

		if ( 'always' === frequency ) {
			return true;
		}
		if ( read( 'localStorage', key( 'clicked', popup ) ) ) {
			return false;
		}
		if ( 'session' === frequency ) {
			return ! read( 'sessionStorage', key( 'seen', popup ) );
		}
		const seen = Number( read( 'localStorage', key( 'seen', popup ) ) );
		return ! seen || ( 'once' !== frequency && Date.now() - seen >= Number( frequency ) * DAY );
	}

	// Une seule ouverture automatique par page.
	function autoOpen( popup ) {
		if ( autoOpened || current || ! isInDates( popup ) || isHiddenOnMobile( popup ) || ! isFrequencyOk( popup ) ) {
			return;
		}
		autoOpened = true;
		show( popup );
	}

	// Vérifie la condition au chargement complet (hauteur connue), puis au défilement.
	function onScroll( isReached, callback ) {
		function check() {
			if ( isReached() ) {
				window.removeEventListener( 'scroll', check );
				callback();
			}
		}
		window.addEventListener( 'scroll', check, { passive: true } );
		if ( 'complete' === document.readyState ) {
			check();
		} else {
			window.addEventListener( 'load', check, { once: true } );
		}
	}

	function scrollable() {
		return Math.max( 0, document.documentElement.scrollHeight - window.innerHeight );
	}

	function arm( popup ) {
		const value = Number( popup.dataset.mrzpopValue );
		const counter = window.mrzpopCounter;

		function fire() {
			autoOpen( popup );
		}

		switch ( popup.dataset.mrzpopTrigger ) {
			case 'immediate':
				fire();
				break;
			case 'delay':
				setTimeout( fire, value * 1000 );
				break;
			case 'scroll':
				onScroll( function () {
					return window.scrollY >= value;
				}, fire );
				break;
			case 'half':
				onScroll( function () {
					return window.scrollY >= scrollable() / 2;
				}, fire );
				break;
			case 'bottom':
				// Marge pour les positions non entières.
				onScroll( function () {
					return window.scrollY >= scrollable() - 2;
				}, fire );
				break;
			case 'minutes':
				if ( counter ) {
					const timer = setInterval( function () {
						if ( counter.seconds() >= value * 60 ) {
							clearInterval( timer );
							fire();
						}
					}, 1000 );
				}
				break;
			case 'pageviews':
				if ( counter && counter.views() >= value ) {
					fire();
				}
				break;
			case 'exit':
				if ( window.matchMedia( '(hover: hover)' ).matches ) {
					document.documentElement.addEventListener( 'mouseleave', function ( event ) {
						if ( event.clientY <= 0 ) {
							fire();
						}
					} );
				}
				break;
		}
	}

	// Formulaire envoyé avec rechargement : la pop-up revient avec sa confirmation
	// ou ses erreurs. Sans rechargement, le délai écoulé annule le retour.
	function reopenAfterSubmit() {
		const saved = read( 'sessionStorage', REOPEN_KEY );
		if ( ! saved ) {
			return;
		}
		write( 'sessionStorage', REOPEN_KEY, '' );
		const popup = popups.find( function ( item ) {
			return saved === item.id + location.pathname;
		} );
		if ( popup ) {
			autoOpened = true;
			show( popup );
		}
	}

	function onSubmit( event ) {
		const form = event.target.closest( 'form' );
		// Un formulaire AJAX vise un iframe et ne recharge pas la page.
		if ( current && current.contains( form ) && ( ! form.target || '_self' === form.target ) ) {
			submittedAt = Date.now();
		}
	}

	document.addEventListener( 'submit', onSubmit, true );

	window.addEventListener( 'pagehide', function () {
		if ( current && Date.now() - submittedAt < 30000 ) {
			write( 'sessionStorage', REOPEN_KEY, current.id + location.pathname );
		}
	} );

	// En capture, pour passer avant un thème qui arrêterait l'événement.
	document.addEventListener( 'click', function ( event ) {
		if ( event.target.closest( '[type="submit"]' ) ) {
			onSubmit( event );
		}
		const link = event.target.closest( 'a[href]' );
		if ( ! link ) {
			return;
		}

		if ( current && current.contains( link ) ) {
			submittedAt = 0;
			write( 'localStorage', key( 'clicked', current ), 1 );
		}

		const popup = popups.find( function ( item ) {
			return link.getAttribute( 'href' ) === '#' + item.id;
		} );
		if ( popup ) {
			event.preventDefault();
			if ( popup !== current && ( preview || isInDates( popup ) ) ) {
				close();
				show( popup );
			}
		}
	}, true );

	function start() {
		if ( preview ) {
			open( preview );
			return;
		}
		reopenAfterSubmit();
		popups.forEach( arm );
	}

	// Une page préchargée en arrière-plan n'a encore été vue par personne.
	if ( document.prerendering ) {
		document.addEventListener( 'prerenderingchange', start, { once: true } );
	} else {
		start();
	}
} )();
