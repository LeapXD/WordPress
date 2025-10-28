/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
const name = 'xd/buttons'

const transforms = {
	from: [
		{
			type: 'block',
			isMultiBlock: true,
			blocks: [ 'xd/button' ],
			transform: ( buttons ) =>
				// Creates the buttons block
				createBlock(
					name,
					{},
					// Loop the selected buttons
					buttons.map( ( attributes ) =>
						// Create singular button in the buttons block
						createBlock( 'xd/button', attributes )
					)
				),
		},
		{
			type: 'block',
			isMultiBlock: true,
			blocks: [ 'core/paragraph' ],
			transform: ( buttons ) =>
				// Creates the buttons block
				createBlock(
					name,
					{},
					// Loop the selected buttons
					buttons.map( ( attributes ) => {
						// Remove any HTML tags
						const div = document.createElement( 'div' );
						div.innerHTML = attributes.content;
						const text = div.innerText || '';
						// Get first url
						const link = div.querySelector( 'a' );
						const url = link?.getAttribute( 'href' );
						// Create singular button in the buttons block
						return createBlock( 'xd/button', {
							text,
							url,
						} );
					} )
				),
			isMatch: ( paragraphs ) => {
				return paragraphs.every( ( attributes ) => {
					const div = document.createElement( 'div' );
					div.innerHTML = attributes.content;
					const text = div.innerText || '';
					const links = div.querySelectorAll( 'a' );
					return text.length <= 30 && links.length <= 1;
				} );
			},
		},
	],
};

export default transforms;
