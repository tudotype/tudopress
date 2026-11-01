/**
 * Hides the listed blocks from the inserter. They still load and render wherever they
 * are already used: only the offer goes. Loaded before the editor registers its blocks.
 */
( function ( hooks ) {
	var hidden = window.hwhHiddenBlocks || [];
	hooks.addFilter( 'blocks.registerBlockType', 'tudopress/hide-from-inserter', function ( settings, name ) {
		if ( hidden.indexOf( name ) === -1 ) {
			return settings;
		}
		return Object.assign( {}, settings, {
			supports: Object.assign( {}, settings.supports, { inserter: false } ),
		} );
	} );
} )( window.wp.hooks );
