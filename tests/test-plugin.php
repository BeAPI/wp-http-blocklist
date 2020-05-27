<?php
/**
 * Class WP_HTTP_BLACKLIST
 *
 * @package WP HTTP Blacklist
 */

/**
 * Sample test case.
 */
class WP_HTTP_BLACKLIST extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function test_default() {
		// Replace this with some actual testing code.
		$result = wp_remote_get('https://connect.advancedcustomfields.com');

		$this->assertTrue( is_wp_error( $result ) );
	}

	public function test_filter() {
		add_filter('wp_http_blacklist', function( $blacklist ) {
			$blacklist[] = 'google.fr';
			return $blacklist;
		});

		$result = wp_remote_get('https://google.fr');

		$this->assertTrue( is_wp_error( $result ) );

	}

	public function test_file_readable() {
		add_filter('wp_http_blacklist_file', function() {
			@unlink( __DIR__.'/blacklist_readable.txt' );
			file_put_contents( __DIR__.'/blacklist_readable.txt', 
				'google.fr  
				beapi.fr
				' 
			);
			return __DIR__.'/blacklist_readable.txt';
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
		add_filter('wp_http_blacklist_file', function() {
			@unlink( __DIR__.'/blacklist_unreadable.txt' );
			file_put_contents( __DIR__.'/blacklist_unreadable.txt', 
				'google.fr  
				beapi.fr
				' 
			);
			chmod(  __DIR__.'/blacklist_unreadable.txt', 0377 );
			return __DIR__.'/blacklist_unreadable.txt';
		});

		// New entry with space ok
		$result = wp_remote_get('https://google.fr');
		$this->assertFalse( is_wp_error( $result ) );

	}

}
