<?php
/**
 * Query
 *
 * @package AchttienVijftien\Tile
 */

namespace AchttienVijftien\Tile;

use AchttienVijftien\Tile\Wrapper;

/**
 * Class Query.
 */
class Query {
	/**
	 * Gets posts with get_posts() and wraps them into Twig post objects.
	 *
	 * @param array $args Query arguments for get_posts().
	 *
	 * @return array
	 */
	public static function get_posts( array $args ) {
		return Wrapper::wrap_multiple( get_posts( $args ), 'post' );
	}
}
