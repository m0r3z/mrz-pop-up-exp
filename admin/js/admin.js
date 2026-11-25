( function ( $ ) {
	'use strict';

	// Choix des contenus précis, par la recherche REST de WordPress.
	function initPages() {
		const $ids = $( '#mrzpop_page_ids' );
		const $selected = $( '.mrzpop-pages-selected' );
		const $search = $( '#mrzpop_page_search' );
		const $results = $( '.mrzpop-pages-results' );
		let timer;
		let request = 0;

		function updateIds() {
			const ids = $selected.children().map( function () {
				return $( this ).data( 'id' );
			} );
			$ids.val( ids.get().join( ',' ) );
		}

		function add( item, title ) {
			if ( ! $selected.children( '[data-id="' + item.id + '"]' ).length ) {
				$( '<li>', { 'data-id': item.id } )
					.append( $( '<span>' ).text( title ) )
					.append( $( '<button>', { type: 'button', class: 'button-link button-link-delete' } ).text( mrzpopAdmin.remove ) )
					.appendTo( $selected );
				updateIds();
			}
			$search.val( '' ).trigger( 'focus' );
			$results.empty();
		}

		function search() {
			const term = $search.val().trim();
			const currentRequest = ++request;
			if ( term.length < 2 ) {
				$results.empty();
				return;
			}
			wp.apiFetch( {
				path: '/wp/v2/search?type=post&per_page=10&search=' + encodeURIComponent( term ),
			} )
				.catch( function () {
					return [];
				} )
				.then( function ( items ) {
					// Une recherche plus récente est déjà partie.
					if ( currentRequest !== request ) {
						return;
					}
					$results.empty();
					if ( ! items.length ) {
						$results.append( $( '<li>' ).text( mrzpopAdmin.noResult ) );
					}
					items.forEach( function ( item ) {
						const title = wp.htmlEntities.decodeEntities( item.title );
						$( '<button>', { type: 'button', class: 'button-link' } )
							.text( title + ' (' + item.subtype + ')' )
							.on( 'click', function () {
								add( item, title );
							} )
							.appendTo( $( '<li>' ).appendTo( $results ) );
					} );
				} );
		}

		$( '#mrzpop_display' ).on( 'change', function () {
			$( '.mrzpop-pages' ).prop( 'hidden', 'specific' !== this.value );
		} );
		$selected.on( 'click', 'button', function () {
			$( this ).closest( 'li' ).remove();
			updateIds();
		} );
		$search.on( 'input', function () {
			clearTimeout( timer );
			timer = setTimeout( search, 300 );
		} );
		// Entrée dans la recherche n'enregistre pas la pop-up.
		$search.on( 'keydown', function ( event ) {
			if ( 'Enter' === event.key ) {
				event.preventDefault();
			}
		} );
	}

	$( function () {
		const $imageId = $( '#mrzpop_image_id' );
		const $preview = $( '.mrzpop-image-preview' );
		const $remove = $( '.mrzpop-image-remove' );
		let frame;

		if ( mrzpopAdmin.codeEditor ) {
			wp.codeEditor.initialize( 'mrzpop_html', mrzpopAdmin.codeEditor );
		}

		$( '#mrzpop_overlay_color' ).wpColorPicker();

		$( '.mrzpop-image-choose' ).on( 'click', function () {
			if ( ! frame ) {
				frame = wp.media( {
					title: mrzpopAdmin.mediaTitle,
					library: { type: 'image' },
					multiple: false,
				} );
				frame.on( 'select', function () {
					const image = frame.state().get( 'selection' ).first().toJSON();
					const size = image.sizes && image.sizes.medium ? image.sizes.medium : image;
					$imageId.val( image.id );
					$preview.empty().append( $( '<img>', { src: size.url, alt: '' } ) );
					$remove.prop( 'hidden', false );
				} );
			}
			frame.open();
		} );

		$remove.on( 'click', function () {
			$imageId.val( 0 );
			$preview.empty();
			$remove.prop( 'hidden', true );
		} );

		$( '#mrzpop_trigger' ).on( 'change', function () {
			const trigger = this.value;
			const $units = $( '.mrzpop-trigger-value [data-trigger]' );
			$units.each( function () {
				this.hidden = this.dataset.trigger !== trigger;
			} );
			$( '.mrzpop-trigger-value' ).prop( 'hidden', ! $units.filter( '[data-trigger="' + trigger + '"]' ).length );
		} );

		initPages();
	} );
} )( jQuery );
