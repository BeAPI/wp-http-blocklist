<?php
/**
 * Class WP_HTTP_BLOCKLIST
 *
 * @package WP HTTP Blocklist
 */

/**
 * Sample test case.
 */
class WP_HTTP_BLOCKLIST extends WP_UnitTestCase {

	public static function tearDownAfterClass(): void {
		chmod( TEST_DIR . '/blocklist_unreadable.txt', 0755 );
	}

	/**
	 * A single example test.
	 */
	public function test_default() {
		// Replace this with some actual testing code.
		$result = wp_remote_get('https://connect.advancedcustomfields.com');

		$this->assertTrue( is_wp_error( $result ) );
	}

	public function test_filter() {
		add_filter('wp_http_blocklist', function( $blocklist ) {
			$blocklist[] = 'google.fr';
			return $blocklist;
		});

		$result = wp_remote_get('https://google.fr');

		$this->assertTrue( is_wp_error( $result ) );

	}

	public function test_remove_entry_filter() {
		add_filter('wp_http_blocklist', function( array $blocklist ) {
			unset( $blocklist[ array_search( 'api.wordpress.org', $blocklist ) ] );
			return $blocklist;
		});

		$result = wp_remote_get('https://api.wordpress.org');

		$this->assertTrue( ! is_wp_error( $result ) );

	}


	public function test_file_readable() {
		add_filter('wp_http_blocklist_file', function() {
			return TEST_DIR . '/blocklist_readable.txt';
		});

		// New entry with space ok
		$result = wp_remote_get('https://be-beau.fr');
		$this->assertTrue( is_wp_error( $result ) );
		$this->assertEquals( $result->get_error_code(), 'http_request_blocked' );

		// New entry with space ok
		$result = wp_remote_get('https://beapi.fr');
		$this->assertTrue( is_wp_error( $result ) );
		$this->assertEquals( $result->get_error_code(), 'http_request_blocked' );

		// Old entry ok
		$result = wp_remote_get('https://api.wordpress.org');
		$this->assertFalse( is_wp_error( $result ) );
	}

	public function test_file_unreadable() {
		add_filter('wp_http_blocklist_file', function() {
			chmod( TEST_DIR . '/blocklist_unreadable.txt', 0377 );
			return TEST_DIR.'/blocklist_unreadable.txt';
		});

		// New entry with space ok
		$result = wp_remote_get('https://google.fr');
		$this->assertFalse( is_wp_error( $result ) );

	}

}
