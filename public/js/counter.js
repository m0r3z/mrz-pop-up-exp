// Compteurs de navigation, gardés dans le navigateur uniquement.
( function () {
	'use strict';

	const VIEWS_KEY = 'mrzpop_views';
	const SECONDS_KEY = 'mrzpop_seconds';
	let views = 0;
	let seconds = 0;

	// Pages vues par jour, sur 90 jours.
	function countView() {
		const today = Math.floor( Date.now() / 86400000 );
		const kept = {};
		let saved = null;

		try {
			saved = JSON.parse( localStorage.getItem( VIEWS_KEY ) );
		} catch ( error ) {}

		Object.entries( saved || {} ).forEach( function ( entry ) {
			if ( Number( entry[ 0 ] ) > today - 90 && Number( entry[ 1 ] ) > 0 ) {
				kept[ entry[ 0 ] ] = Number( entry[ 1 ] );
			}
		} );
		kept[ today ] = ( kept[ today ] || 0 ) + 1;
		views = Object.values( kept ).reduce( function ( total, count ) {
			return total + count;
		}, 0 );

		try {
			localStorage.setItem( VIEWS_KEY, JSON.stringify( kept ) );
		} catch ( error ) {}
	}

	// Temps passé dans l'onglet, quand il est visible.
	function countSeconds() {
		try {
			seconds = Number( sessionStorage.getItem( SECONDS_KEY ) ) || 0;
		} catch ( error ) {}

		setInterval( function () {
			if ( document.hidden ) {
				return;
			}
			seconds++;
			try {
				sessionStorage.setItem( SECONDS_KEY, seconds );
			} catch ( error ) {}
		}, 1000 );
	}

	window.mrzpopCounter = {
		views: function () {
			return views;
		},
		seconds: function () {
			return seconds;
		},
	};

	function start() {
		if ( mrzpopCounters.includes( 'pageviews' ) ) {
			countView();
		}
		if ( mrzpopCounters.includes( 'minutes' ) ) {
			countSeconds();
		}
	}

	if ( document.prerendering ) {
		document.addEventListener( 'prerenderingchange', start, { once: true } );
	} else {
		start();
	}
} )();
