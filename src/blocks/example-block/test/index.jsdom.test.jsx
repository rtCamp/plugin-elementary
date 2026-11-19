/**
 * External dependencies
 */
import { render, screen } from '@testing-library/react';
import { afterAll, beforeAll, describe, expect, test } from 'vitest';

/**
 * WordPress dependencies
 */
import {
	createBlock,
	registerBlockType,
	serialize,
	unregisterBlockType,
} from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from '../block.json';
import Edit from '../edit';
import save from '../save';

describe( 'Example Block', () => {
	beforeAll( () => {
		registerBlockType( metadata, { edit: Edit, save } );
	} );

	afterAll( () => {
		unregisterBlockType( metadata.name );
	} );

	// A change to this markup invalidates blocks already saved in posts.
	test( 'saves a paragraph with the block class', () => {
		expect( serialize( createBlock( metadata.name ) ) ).toBe(
			`<!-- wp:${ metadata.name } -->\n` +
				`<p class="wp-block-${ metadata.name.replace(
					'/',
					'-'
				) }">Example Block – hello from the saved content!</p>\n` +
				`<!-- /wp:${ metadata.name } -->`
		);
	} );

	test( 'shows its text in the editor', () => {
		render( <Edit /> );

		expect(
			screen.getByText( 'Example Block – hello from the editor!' )
		).toBeInTheDocument();
	} );
} );
