/**
 * measure-page.js — measures a rendered page's typography, colour and basic accessibility.
 * Paste into the browser pane (javascript_tool) or a DevTools console on any page, at each
 * width you care about (e.g. 1440 and 390). It returns a JSON report; it judges nothing.
 *
 * Counts: typefaces, font files, text sizes, style combinations, text colours; contrast of
 * each text colour on its nearest solid background (WCAG 2); long centred or right-aligned
 * paragraphs; characters per line; line spacing; empty paragraphs and <br>; headings outline;
 * headings ending on one word; straight quotes; images without alt; small tap targets;
 * fixed elements; title and meta description. Read-only.
 */
( () => {
	const visible = ( el ) => {
		if ( el.closest( "#wpadminbar" ) ) {
			return false; // WordPress's toolbar is not part of the page.
		}
		const r = el.getBoundingClientRect();
		const s = getComputedStyle( el );
		return r.width > 1 && r.height > 1 && s.visibility !== 'hidden' && s.display !== 'none' && s.opacity !== '0';
	};
	const rgb = ( c ) => ( c.match( /[\d.]+/g ) || [] ).map( Number );
	const hex = ( c ) => '#' + rgb( c ).slice( 0, 3 ).map( ( n ) => Math.round( n ).toString( 16 ).padStart( 2, '0' ) ).join( '' );
	const lum = ( c ) => {
		const v = rgb( c ).slice( 0, 3 ).map( ( n ) => {
			n /= 255;
			return n <= 0.04045 ? n / 12.92 : ( ( n + 0.055 ) / 1.055 ) ** 2.4;
		} );
		return 0.2126 * v[ 0 ] + 0.7152 * v[ 1 ] + 0.0722 * v[ 2 ];
	};
	const ratio = ( a, b ) => {
		const [ x, y ] = [ lum( a ), lum( b ) ].sort( ( p, q ) => q - p );
		return Math.round( ( ( x + 0.05 ) / ( y + 0.05 ) ) * 100 ) / 100;
	};
	// The nearest ancestor with an opaque colour; null when an image or gradient is in the way.
	const background = ( el ) => {
		for ( let n = el; n; n = n.parentElement ) {
			const s = getComputedStyle( n );
			if ( s.backgroundImage !== 'none' ) {
				return null;
			}
			const c = rgb( s.backgroundColor );
			if ( c.length && ( c[ 3 ] === undefined || c[ 3 ] === 1 ) ) {
				return s.backgroundColor;
			}
		}
		return 'rgb(255, 255, 255)';
	};
	const lines = ( el ) => {
		const range = document.createRange();
		range.selectNodeContents( el );
		const tops = new Set( [ ...range.getClientRects() ].map( ( r ) => Math.round( r.top ) ) );
		return tops.size;
	};

	// Elements that hold text directly.
	const textEls = [ ...document.body.querySelectorAll( '*' ) ].filter(
		( el ) => visible( el ) && [ ...el.childNodes ].some( ( n ) => n.nodeType === 3 && n.textContent.trim() )
	);

	const families = {};
	const sizes = {};
	const combos = new Set();
	const colours = {};
	const contrast = {};
	for ( const el of textEls ) {
		const s = getComputedStyle( el );
		const family = s.fontFamily.split( ',' )[ 0 ].replace( /["']/g, '' ).trim();
		const chars = [ ...el.childNodes ].filter( ( n ) => n.nodeType === 3 ).map( ( n ) => n.textContent.trim() ).join( ' ' ).length;
		families[ family ] = ( families[ family ] || 0 ) + chars;
		sizes[ s.fontSize ] = ( sizes[ s.fontSize ] || 0 ) + 1;
		colours[ hex( s.color ) ] = ( colours[ hex( s.color ) ] || 0 ) + 1;
		combos.add( [ family, s.fontSize, s.fontWeight, s.fontStyle, hex( s.color ) ].join( ' · ' ) );
		const bg = background( el );
		if ( bg ) {
			const px = parseFloat( s.fontSize );
			const large = px >= 24 || ( px >= 18.66 && parseInt( s.fontWeight, 10 ) >= 700 );
			const r = ratio( s.color, bg );
			const need = large ? 3 : 4.5;
			const key = `${ hex( s.color ) } on ${ hex( bg ) } (${ large ? 'large' : 'text' })`;
			if ( r < need ) {
				contrast[ key ] = contrast[ key ] || { ratio: r, needs: need, example: el.textContent.trim().slice( 0, 50 ) };
			}
		}
	}

	const paragraphs = [ ...document.querySelectorAll( 'p, li' ) ].filter( visible );
	const longAligned = paragraphs
		.filter( ( p ) => [ 'center', 'right', 'end' ].includes( getComputedStyle( p ).textAlign ) && lines( p ) > 2 )
		.map( ( p ) => ( { align: getComputedStyle( p ).textAlign, lines: lines( p ), text: p.textContent.trim().slice( 0, 50 ) } ) );
	const measure = paragraphs
		.filter( ( p ) => p.tagName === 'P' && lines( p ) >= 3 )
		.map( ( p ) => Math.round( p.textContent.trim().length / lines( p ) ) );
	const spacing = [ ...new Set( paragraphs.filter( ( p ) => p.tagName === 'P' ).map( ( p ) => {
		const s = getComputedStyle( p );
		return s.lineHeight === 'normal' ? 'normal' : ( parseFloat( s.lineHeight ) / parseFloat( s.fontSize ) ).toFixed( 2 );
	} ) ) ];

	const headings = [ ...document.querySelectorAll( 'h1, h2, h3, h4, h5, h6' ) ].filter( visible );
	const skips = [];
	headings.reduce( ( prev, h ) => {
		const level = Number( h.tagName[ 1 ] );
		if ( level > prev + 1 ) {
			skips.push( `${ 'H' + prev } → ${ h.tagName }: ${ h.textContent.trim().slice( 0, 40 ) }` );
		}
		return level;
	}, 0 );
	const oneWordEndings = headings.filter( ( h ) => {
		if ( lines( h ) < 2 ) {
			return false;
		}
		const words = h.textContent.trim().split( /\s+/ );
		const last = document.createRange();
		const walker = document.createTreeWalker( h, NodeFilter.SHOW_TEXT );
		let node;
		let lastNode;
		while ( ( node = walker.nextNode() ) ) {
			if ( node.textContent.trim() ) {
				lastNode = node;
			}
		}
		const word = words[ words.length - 1 ];
		const at = lastNode.textContent.lastIndexOf( word );
		if ( at < 0 ) {
			return false;
		}
		last.setStart( lastNode, Math.max( 0, at - 1 ) );
		last.setEnd( lastNode, at + word.length );
		const rects = [ ...last.getClientRects() ];
		const before = document.createRange();
		before.selectNodeContents( h );
		before.setEnd( lastNode, Math.max( 0, at - 1 ) );
		const prevRects = [ ...before.getClientRects() ];
		return rects.length && prevRects.length && Math.round( rects[ rects.length - 1 ].top ) > Math.round( prevRects[ prevRects.length - 1 ].top );
	} ).map( ( h ) => h.textContent.trim().slice( 0, 60 ) );

	const bodyText = document.body.innerText;
	const fonts = performance.getEntriesByType( 'resource' ).filter( ( r ) => /\.(woff2?|ttf|otf)(\?|$)/i.test( r.name ) || r.initiatorType === 'css' && /font/i.test( r.name ) );

	return {
		url: location.href,
		viewport: `${ innerWidth } × ${ innerHeight }`,
		title: document.title,
		metaDescription: document.querySelector( 'meta[name="description"]' )?.content || null,
		typefaces: Object.fromEntries( Object.entries( families ).sort( ( a, b ) => b[ 1 ] - a[ 1 ] ).map( ( [ f, c ] ) => [ f, `${ c } characters` ] ) ),
		fontFiles: { count: fonts.length, kb: Math.round( fonts.reduce( ( t, f ) => t + ( f.transferSize || f.encodedBodySize || 0 ), 0 ) / 1024 ), files: fonts.map( ( f ) => f.name.split( '/' ).pop().slice( 0, 60 ) ) },
		textSizes: { count: Object.keys( sizes ).length, sizes: Object.keys( sizes ).sort( ( a, b ) => parseFloat( b ) - parseFloat( a ) ) },
		styleCombinations: combos.size,
		textColours: colours,
		contrastFailures: contrast,
		longCentredOrRightAligned: longAligned,
		charactersPerLine: measure.length ? { min: Math.min( ...measure ), median: measure.sort( ( a, b ) => a - b )[ Math.floor( measure.length / 2 ) ], max: Math.max( ...measure ) } : null,
		paragraphLineSpacing: spacing,
		emptyParagraphs: [ ...document.querySelectorAll( 'p' ) ].filter( ( p ) => ! p.textContent.trim() && ! p.querySelector( 'img, svg, video, iframe' ) ).length,
		lineBreaksInParagraphs: document.querySelectorAll( 'p br' ).length,
		headings: { h1: headings.filter( ( h ) => h.tagName === 'H1' ).length, skips, outline: headings.map( ( h ) => `${ h.tagName } ${ h.textContent.trim().slice( 0, 50 ) }` ) },
		headingsEndingOnOneWord: oneWordEndings,
		straightQuotes: { double: ( bodyText.match( /"/g ) || [] ).length, apostrophes: ( bodyText.match( /\b'\b|'/g ) || [] ).length },
		imagesWithoutAlt: [ ...document.images ].filter( ( i ) => visible( i ) && ! i.hasAttribute( 'alt' ) ).map( ( i ) => i.src.split( '/' ).pop() ),
		imagesWithEmptyAlt: [ ...document.images ].filter( ( i ) => visible( i ) && i.getAttribute( 'alt' ) === '' ).length,
		linksShowingAddress: [ ...document.querySelectorAll( 'a' ) ].filter( ( a ) => /^(https?:\/\/|www\.)/i.test( a.textContent.trim() ) ).map( ( a ) => a.textContent.trim().slice( 0, 60 ) ),
		smallTapTargets: [ ...document.querySelectorAll( 'a, button, input, select, [role="button"]' ) ].filter( visible ).filter( ( el ) => {
			const r = el.getBoundingClientRect();
			if ( el.tagName === "A" && el.closest( "p" ) ) {
				return false; // Links inside a sentence are exempt (WCAG 2.5.8).
			}
			return r.width < 24 || r.height < 24;
		} ).map( ( el ) => `${ el.tagName.toLowerCase() } ${ Math.round( el.getBoundingClientRect().width ) }×${ Math.round( el.getBoundingClientRect().height ) } ${ ( el.textContent || el.getAttribute( 'aria-label' ) || '' ).trim().slice( 0, 30 ) }` ),
		unnamedControls: [ ...document.querySelectorAll( 'a, button' ) ].filter( visible ).filter( ( el ) => ! ( el.textContent.trim() || el.getAttribute( 'aria-label' ) || el.getAttribute( 'title' ) || el.querySelector( 'img[alt]:not([alt=""])' ) ) ).length,
		fixedElements: [ ...document.body.querySelectorAll( '*' ) ].filter( ( el ) => visible( el ) && [ 'fixed', 'sticky' ].includes( getComputedStyle( el ).position ) ).map( ( el ) => `${ el.tagName.toLowerCase() }.${ [ ...el.classList ].slice( 0, 2 ).join( '.' ) } ${ Math.round( el.getBoundingClientRect().width ) }×${ Math.round( el.getBoundingClientRect().height ) }` ),
		motion: { animations: document.getAnimations().length, reducedMotionPreferred: matchMedia( '(prefers-reduced-motion: reduce)' ).matches },
	};
} )();
