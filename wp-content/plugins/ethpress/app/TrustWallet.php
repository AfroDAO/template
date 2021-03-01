<?php
/**
 * Has TrustWallet class. TrustWallet has some specific things.
 *
 * See https://github.com/trustwallet/developer/.
 *
 * @since 0.4.0
 * @package ethpress
 */

namespace losnappas\Ethpress;

defined( 'ABSPATH' ) || die;

/**
 * Contains functions for TrustWallet.
 *
 * @since 0.4.0
 */
class TrustWallet {

	/**
	 * The basis for trustwallet deeplinks.
	 *
	 * @since 0.4.0
	 *
	 * @var string $trustlink
	 */
	public static $trustlink = 'https://link.trustwallet.com/open_url';

	/**
	 * Generates deeplink to blog login.
	 *
	 * @since 0.4.0
	 *
	 * @param string $to Optional. Deeplink will point to. Defaults to current url.
	 * @return string Deeplink.
	 */
	public static function get_deeplink( $to = '' ) {
		if ( empty( $to ) ) {
			global $wp;
			// Full current URL.
			$to = home_url(
				add_query_arg(
					$wp->query_vars,
					$wp->request
				)
			);
		}
		// TrustWallet does not work with cleartext (http).
		$to          = \preg_replace( '/^http:/i', 'https:', $to );
		$target_link = \rawurlencode( $to );
		$deeplink    = add_query_arg(
			[
				'coin_id' => 60, // Ethereum.
				'url'     => $target_link,
			],
			self::$trustlink
		);
		$deeplink    = esc_url( $deeplink );
		return $deeplink;
	}
}
