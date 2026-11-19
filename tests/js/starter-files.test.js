/** Keep the skeleton-only files out of every install route. */
import fs from 'node:fs';
import path from 'node:path';
import { describe, expect, test } from 'vitest';
import scaffoldConfig from '../../bin/scaffold.config.js';

const root = path.resolve( import.meta.dirname, '../..' );
// A plugin made from the skeleton has run init, which removes .gitattributes and
// this test; skip there in case it was kept.
const inSkeleton = ! fs.existsSync( path.join( root, '.wp-scaffold.json' ) );

describe.runIf( inSkeleton )( 'skeleton-only files', () => {
	test( 'every export-ignore path is also an init cleanup target', () => {
		const ignored = fs
			.readFileSync( path.join( root, '.gitattributes' ), 'utf8' )
			.split( '\n' )
			.map( ( line ) => line.trim() )
			.filter( ( line ) => /\sexport-ignore$/.test( line ) )
			.map( ( line ) => line.split( /\s+/ )[ 0 ].replace( /^\//, '' ) );

		expect( ignored.length ).toBeGreaterThan( 0 );
		expect( scaffoldConfig.cleanup.targets ).toEqual(
			expect.arrayContaining( ignored )
		);
	} );
} );
