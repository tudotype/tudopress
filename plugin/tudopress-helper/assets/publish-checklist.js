/**
 * "Before you publish": a short, friendly list in the pre-publish panel. It advises and
 * never blocks. Each item has a "Show me" button that selects the block in question.
 * No build step: plain wp.* globals.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var sprintf = wp.i18n.sprintf;
	var useSelect = wp.data.useSelect;
	var Panel = ( wp.editor && wp.editor.PluginPrePublishPanel ) || ( wp.editPost && wp.editPost.PluginPrePublishPanel );
	var Button = wp.components.Button;

	// Link text that says nothing out of context, in English and Portuguese.
	var VAGUE = [ 'click here', 'here', 'read more', 'more', 'link', 'this', 'clique aqui', 'aqui', 'ler mais', 'saber mais', 'mais', 'ver mais' ];

	function flatten( blocks, out ) {
		blocks.forEach( function ( block ) {
			out.push( block );
			if ( block.innerBlocks && block.innerBlocks.length ) {
				flatten( block.innerBlocks, out );
			}
		} );
		return out;
	}

	function text( value ) {
		return value === undefined || value === null ? '' : String( value );
	}

	function linkTexts( html ) {
		var found = [];
		var re = /<a\b[^>]*>([\s\S]*?)<\/a>/gi;
		var match;
		while ( ( match = re.exec( html ) ) ) {
			found.push( match[ 1 ].replace( /<[^>]+>/g, '' ).trim().toLowerCase().replace( /[.…:!]+$/, '' ) );
		}
		return found;
	}

	function check( blocks, post ) {
		var issues = [];
		var all = flatten( blocks, [] );
		var titleShown = ! /no-title/.test( post.template || '' );
		var previous = titleShown ? 1 : 0;
		var mainHeadings = titleShown ? 1 : 0;
		var emptyParagraphs = [];

		all.forEach( function ( block ) {
			var a = block.attributes;

			if ( block.name === 'core/image' && a.url && ! a.isDecorative && ! text( a.alt ).trim() ) {
				issues.push( { id: block.clientId, text: __( 'An image has no description (alt text). Describe what it shows, for people who can\'t see it. Purely decorative? Switch on "Mark as decorative".', 'tudopress-helper' ) } );
			}
			if ( block.name === 'core/media-text' && a.mediaUrl && a.mediaType === 'image' && ! text( a.mediaAlt ).trim() ) {
				issues.push( { id: block.clientId, text: __( 'The image beside the text has no description (alt text).', 'tudopress-helper' ) } );
			}

			if ( block.name === 'core/heading' ) {
				var level = a.level || 2;
				if ( level === 1 ) {
					mainHeadings++;
					if ( mainHeadings > 1 ) {
						issues.push( { id: block.clientId, text: __( 'There is more than one main heading (H1): the page title is one already. Use H2 for sections.', 'tudopress-helper' ) } );
					}
				} else if ( level > previous + 1 ) {
					issues.push( { id: block.clientId, text: sprintf( __( 'A heading jumps from H%1$d to H%2$d. Screen readers use headings as a table of contents: use H%3$d here, and change the size in the block\'s Typography settings if it looks too big.', 'tudopress-helper' ), previous, level, previous + 1 ) } );
				}
				previous = level;
			}

			var html = text( a.content ) + text( a.text ) + text( a.value );
			linkTexts( html ).forEach( function ( label ) {
				if ( VAGUE.indexOf( label ) !== -1 ) {
					issues.push( { id: block.clientId, text: sprintf( __( 'A link says only "%s". Say where it goes ("our opening hours"), so it makes sense on its own.', 'tudopress-helper' ), label ) } );
				} else if ( /^(https?:\/\/|www\.)/.test( label ) ) {
					issues.push( { id: block.clientId, text: __( 'A link shows a web address. Put the link on words that say where it goes; addresses are read out letter by letter.', 'tudopress-helper' ) } );
				}
			} );

			// The last block is often an empty paragraph left by pressing Enter: not spacing.
			if ( block.name === 'core/paragraph' && block !== blocks[ blocks.length - 1 ] && ! text( a.content ).replace( /<br\s*\/?>|&nbsp;|\s/g, '' ) ) {
				emptyParagraphs.push( block.clientId );
			}
		} );

		if ( emptyParagraphs.length ) {
			issues.push( { id: emptyParagraphs[ 0 ], text: sprintf( __( '%d empty paragraph(s), probably used as spacing. Delete them: spacing comes from the design, and on phones the gaps grow.', 'tudopress-helper' ), emptyParagraphs.length ) } );
		}

		if ( post.type === 'post' && ! post.featuredMedia ) {
			issues.push( { id: null, text: __( 'No featured image: the news list and link previews will show none.', 'tudopress-helper' ) } );
		}
		if ( post.type === 'post' && ! text( post.excerpt ).trim() ) {
			issues.push( { id: null, text: __( 'No excerpt: search results and link previews will use the first lines of the post.', 'tudopress-helper' ) } );
		}
		return issues;
	}

	function Checklist() {
		var data = useSelect( function ( select ) {
			var editor = select( 'core/editor' );
			return {
				blocks: select( 'core/block-editor' ).getBlocks(),
				post: {
					type: editor.getCurrentPostType(),
					template: editor.getEditedPostAttribute( 'template' ),
					featuredMedia: editor.getEditedPostAttribute( 'featured_media' ),
					excerpt: editor.getEditedPostAttribute( 'excerpt' ),
				},
			};
		}, [] );
		var issues = check( data.blocks, data.post );

		function show( id ) {
			wp.data.dispatch( 'core/editor' ).closePublishSidebar();
			wp.data.dispatch( 'core/block-editor' ).selectBlock( id );
		}

		return el(
			Panel,
			{
				title: issues.length
					? sprintf( __( 'Before you publish (%d)', 'tudopress-helper' ), issues.length )
					: __( 'Before you publish', 'tudopress-helper' ),
				initialOpen: issues.length > 0,
				className: 'hwh-checklist',
			},
			issues.length
				? el(
					'ul',
					null,
					issues.map( function ( issue, i ) {
						return el(
							'li',
							{ key: i },
							issue.text,
							issue.id
								? el( Button, { variant: 'link', onClick: function () { show( issue.id ); } }, __( 'Show me', 'tudopress-helper' ) )
								: null
						);
					} )
				)
				: el( 'p', null, __( 'All good: images described, headings in order, links that say where they go.', 'tudopress-helper' ) )
		);
	}

	if ( Panel ) {
		wp.plugins.registerPlugin( 'tudopress-checklist', { render: Checklist } );
	}
} )( window.wp );
