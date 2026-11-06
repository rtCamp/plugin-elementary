/** Keep the skeleton-only files out of every install route. */
const fs = require( 'fs' );
const path = require( 'path' );

const root = path.resolve( __dirname, '../..' );
// A plugin made from the skeleton has run init, which removes .gitattributes and
// this test; skip there in case it was kept.
const inSkeleton = ! fs.existsSync( path.join( root, '.wp-scaffold.json' ) );

( inSkeleton ? describe : describe.skip )( 'skeleton-only files', () => {
	test( 'every export-ignore path is also an init cleanup target', () => {
		const ignored = fs
			.readFileSync( path.join( root, '.gitattributes' ), 'utf8' )
			.split( '\n' )
			.map( ( line ) => line.trim() )
			.filter( ( line ) => /\sexport-ignore$/.test( line ) )
			.map( ( line ) => line.split( /\s+/ )[ 0 ].replace( /^\//, '' ) );
		const { cleanup } = require( '../../bin/scaffold.config.js' );

		expect( ignored.length ).toBeGreaterThan( 0 );
		expect( cleanup.targets ).toEqual( expect.arrayContaining( ignored ) );
	} );
} );
