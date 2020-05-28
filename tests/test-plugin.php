<?php
/**
 * Class WP_HTTP_BLACKLIST
 *
 * @package WP HTTP Blacklist
 */

/**
 * Basic plugin tests.
 */
class WP_HTTP_BLACKLIST_TEST extends WP_UnitTestCase {

	const READABLE_FILE = __DIR__ . '/blacklist_readable.txt';
	const UNREADABLE_FILE = __DIR__ . '/blacklist_unreadable.txt';

	/**
	 * Create a readable file with domains to block
	 */
	private function filter_file_redable() {
		@unlink( self::READABLE_FILE );
		file_put_contents( self::READABLE_FILE,
			'google.fr  
				beapi.fr
				'
		);

		add_filter( 'wp_http_blacklist_file', function () {
			return self::READABLE_FILE;
		} );
	}

	/**
	 *
	 */
	private function filter_file_unredable() {
		@unlink( self::UNREADABLE_FILE );
		file_put_contents( self::UNREADABLE_FILE,
			'google.fr  
				beapi.fr
				'
		);
		chmod( self::UNREADABLE_FILE, 0377 );

		add_filter( 'wp_http_blacklist_file', function () {
			return self::UNREADABLE_FILE;
		} );
	}

	public function test_blacklisted() {
		// Replace this with some actual testing code.
		$result = BEAPI\WPHTTPBlacklist\pre_http_request( false, [], 'https://connect.advancedcustomfields.com' );

		$this->assertTrue( is_wp_error( $result ) );
	}

	public function test_not_blacklisted() {
		// Replace this with some actual testing code.
		$result = BEAPI\WPHTTPBlacklist\pre_http_request( false, [], 'https://google.fr' );

		$this->assertFalse( $result );
	}

	public function test_filter() {
		add_filter( 'wp_http_blacklist', function ( $blacklist ) {
			$blacklist[] = 'google.fr';

			return $blacklist;
		} );

		$result = BEAPI\WPHTTPBlacklist\pre_http_request( false, [], 'https://google.fr' );

		$this->assertTrue( is_wp_error( $result ) );

	}

	public function test_file_readable() {
		$this->filter_file_redable();

		// New entry with space ok
		$result = BEAPI\WPHTTPBlacklist\pre_http_request( false, [], 'https://google.fr' );
		$this->assertTrue( is_wp_error( $result ) );
		$this->assertEquals( $result->get_error_code(), 'http_request_blocked' );

		// New entry with space ok
		$result = BEAPI\WPHTTPBlacklist\pre_http_request( false, [], 'https://beapi.fr' );
		$this->assertTrue( is_wp_error( $result ) );
		$this->assertEquals( $result->get_error_code(), 'http_request_blocked' );

		@unlink( __DIR__ . '/blacklist_readable.txt' );
	}

	public function test_file_readable_removed_default_blocked() {
		$this->filter_file_redable();

		// Old entry ok
		$result = BEAPI\WPHTTPBlacklist\pre_http_request( false, [], 'https://connect.advancedcustomfields.com' );
		$this->assertFalse( $result );

		@unlink( __DIR__ . '/blacklist_readable.txt' );
	}

	public function test_file_unreadable() {
		$this->filter_file_unredable();

		// New entry with space ok
		$result = BEAPI\WPHTTPBlacklist\pre_http_request( false, [], 'https://google.fr' );
		$this->assertFalse( $result );
		@unlink( __DIR__ . '/blacklist_unreadable.txt' );
	}

}
