<?php
/**
 * Plugin Name:       WP HTTP Blocklist
 * Plugin URI:        https://github.com/BeAPI/wp-http-blocklist
 * Description:       Block unwanted HTTP requests with a deny list
 * Version:           1.0.5
 * Requires at least: 4.4
 * Requires PHP:      5.6
 * Author:            Be API
 * Author URI:        https://beapi.fr
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-http-blocklist
 * Domain Path:
 */

namespace BEAPI\WPHTTPBlocklist;

if ( ! defined( 'WP_HTTP_BLOCKLIST' ) ) {
	define( 'WP_HTTP_BLOCKLIST', __DIR__ . '/denylist.txt' );
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

	$blocklist_file = apply_filters( 'wp_http_blocklist_file', WP_HTTP_BLOCKLIST );

	$blocklist = [];
	if ( is_file( $blocklist_file ) && is_readable( $blocklist_file ) ) {
		$blocklist = file( $blocklist_file );
	}

	/**
	 * Here we get the file values and they have a \n at the end.
	 * Remove all useless caracters.
	 */
	$blocklist = array_map( 'trim', $blocklist );
	$blocklist = array_filter( $blocklist );

	$blocklist = apply_filters( 'wp_http_blocklist', $blocklist );
	$blocklist = array_unique( $blocklist );

	if ( empty( $blocklist ) ) {
		return $flag;
	}

	foreach ( $blocklist as $blocklist_domain ) {
		if ( $request_host === $blocklist_domain ) {
			// translators: First is the host blocked, second is the full url called
			$response = new \WP_Error( 'http_request_blocked', sprintf( __( 'Host %1$s is blocked from a deny list.', 'wp-http-blocklist' ), $request_host ) );
			/** This action is documented in wp-includes/class-http.php */
			do_action( 'http_api_debug', $response, 'response', 'Requests', $parsed_args, $url );

			return $response;
		}
	}

	return $flag;
}
