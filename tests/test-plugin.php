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

	public function test_file_readable() {
		add_filter('wp_http_blocklist_file', function() {
			@unlink( __DIR__.'/blocklist_readable.txt' );
			file_put_contents( __DIR__.'/blocklist_readable.txt', 
				'google.fr  
				beapi.fr
				' 
			);
			return __DIR__.'/blocklist_readable.txt';
		});

		// New entry with space ok
		$result = wp_remote_get('https://google.fr');
		$this->assertTrue( is_wp_error( $result ) );
		$this->assertEquals( $result->get_error_code(), 'http_request_blocked' );

		// New entry with space ok
		$result = wp_remote_get('https://beapi.fr');
		$this->assertTrue( is_wp_error( $result ) );
		$this->assertEquals( $result->get_error_code(), 'http_request_blocked' );


		// Old entry ok
		$result = wp_remote_get('https://connect.advancedcustomfields.com');
		$this->assertFalse( is_wp_error( $result ) );
	}

	public function test_file_unreadable() {
		add_filter('wp_http_blocklist_file', function() {
			@unlink( __DIR__.'/blocklist_unreadable.txt' );
			file_put_contents( __DIR__.'/blocklist_unreadable.txt', 
				'google.fr  
				beapi.fr
				' 
			);
			chmod(  __DIR__.'/blocklist_unreadable.txt', 0377 );
			return __DIR__.'/blocklist_unreadable.txt';
		});

		// New entry with space ok
		$result = wp_remote_get('https://google.fr');
		$this->assertFalse( is_wp_error( $result ) );

	}

}
