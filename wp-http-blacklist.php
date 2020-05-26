<?php
/**
 * Plugin Name:       WP HTTP Blacklist
 * Plugin URI:        https://github.com/BeAPI/wp-http-blacklist
 * Description:       Block unwanted HTTP requests with a blacklist
 * Version:           1.0.0
 * Requires at least: 4.4
 * Requires PHP:      5.6
 * Author:            Be API
 * Author URI:        https://beapi.fr
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-http-blacklist
 * Domain Path:
 */

namespace BEAPI\WPHTTPBlacklist;

if ( ! defined( 'WP_HTTP_BLACKLIST' ) ) {
	define( 'WP_HTTP_BLACKLIST', __DIR__ . '/blacklist.txt' );
}

// Standard plugin security, keep this line in place.
defined( 'ABSPATH' ) || die();

add_filter( 'pre_http_request', __NAMESPACE__ . '\\pre_http_request', 100, 3 );

/**
 * @return false|mixed|\WP_Error
 *
 * @param array $parsed_args
 * @param string $url
 * @param false|mixed $flag
 */
function pre_http_request( $flag, $parsed_args, $url ) {
	$request_host = wp_parse_url( $url, PHP_URL_HOST );
	if ( empty( $request_host ) ) {
		return $flag;
	}

	$blacklist_file = apply_filters( 'wp_http_blacklist_file', WP_HTTP_BLACKLIST );

	$blacklist = [];
	if ( is_file( $blacklist_file ) ) {
		$blacklist = file( $blacklist_file );
	}

	$blacklist = apply_filters( 'wp_http_blacklist', $blacklist );
	$blacklist = array_map( 'trim', $blacklist );
	$blacklist = array_filter( $blacklist );
	$blacklist = array_unique( $blacklist );

	if ( empty( $blacklist ) ) {
		return $flag;
	}

	foreach ( $blacklist as $blacklist_domain ) {
		if ( $request_host === $blacklist_domain ) {
			$response = new \WP_Error( 'http_request_blocked', __( 'This URL is blacklisted.', 'wp-http-blacklist' ) );
			/** This action is documented in wp-includes/class-http.php */
			do_action( 'http_api_debug', $response, 'response', 'Requests', $parsed_args, $url );

			return $response;
		}
	}

	return $flag;
}
