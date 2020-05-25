<?php
/**
 * Plugin Name:       WP HTTP Blacklist
 * Plugin URI:        https://github.com/BeAPI/wp-http-blacklist
 * Description:       Block unwanted HTTP requests with a blacklist
 * Version:           1.0
 * Requires at least: 4.6
 * Requires PHP:      5.6
 * Author:            Be API
 * Author URI:        https://beapi.fr
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:
 * Domain Path:
 */

namespace BEAPI\WP_HTTP_Blacklist;

// Standard plugin security, keep this line in place.
defined( 'ABSPATH' ) || die();

add_filter( 'pre_http_request', __NAMESPACE__ . '\\pre_http_request', 100, 3 );
function pre_http_request( $flag, $parsed_args, $url ) {
	$request_host = wp_parse_url( $url, PHP_URL_HOST );
	if ( empty( $request_host ) ) {
		return $flag;
	}

	$blacklist = array();
	if ( is_file( __DIR__ . '/blacklist.txt' ) ) {
		$blacklist = file( __DIR__ . '/blacklist.txt' );
	}

	$blacklist = apply_filters( 'wp_http_blacklist', $blacklist );
	$blacklist = array_unique( $blacklist );
	$blacklist = array_filter( $blacklist );
	$blacklist = array_map( 'trim', $blacklist );

	if ( empty( $blacklist ) ) {
		return $flag;
	}

	foreach ( $blacklist as $blacklist_domain ) {
		if ( $request_host === $blacklist_domain ) {
			$response = new \WP_Error( 'http_request_blocked', __( 'This URL is blacklisted.' ) );
			/** This action is documented in wp-includes/class-http.php */
			do_action( 'http_api_debug', $response, 'response', 'Requests', $parsed_args, $url );

			return $response;
		}
	}

	return $flag;
}
